<?php 
/* 
 * fetch all the post in drafts
 */
function LP_fetch_drafts($type = 'draft', $Where_ids = "")
{
    global $wpdb;
    $user_ID = get_current_user_id(); 
    $user_blogs = get_blogs_of_user( $user_ID );
       
    $sql = "SELECT * FROM (";
    $sep = "";
	$prefix = $wpdb->base_prefix;
	
    foreach($user_blogs as $blog){
        if($blog->userblog_id == 1){
                continue;
        }else{
			$db_name = LP_get_blog_db_name($blog->userblog_id);
            $wp_posts = "`$db_name`.`".$prefix.$blog->userblog_id."_posts`";
        }
        
        switch_to_blog($blog->userblog_id);
        if($type        == 'draft' || $type == ""){
            $extra = " AND `post_status`='draft' ";
        }elseif($type   == 'future'){
            $extra = " AND (`post_status`='future' AND `post_date_gmt` < DATE_FORMAT('".get_gmt_from_date(DATE("Y-m-d H:i:s"))."')) ";
        }elseif($type   == 'all'){
            $extra = " AND (`post_status`='draft' OR (`post_status`='future' AND `post_date_gmt` > '".get_gmt_from_date(DATE("Y-m-d H:i:s"))."')) ";
        }else{
            $extra = " AND `post_status`='draft' ";
        }
        $W_extra = "";
        if($Where_ids!=""){
            $W_extra = " AND `ID` in($Where_ids) ";
        }
        
        
        $sql.= $sep." SELECT *,".$blog->userblog_id." as blog_id FROM $wp_posts WHERE `post_type`='post' $extra $W_extra AND `post_author` = $user_ID";
        $sep = " UNION ALL ";
        restore_current_blog();
    }
    $sql.= ") as from_all ORDER BY from_all.`ID` ASC";
    $drafts = $wpdb->get_results($sql,ARRAY_A);
    return $drafts;
}

/* 
 * fetch all the post in drips from AJAX
 */
function LP_adjust_fetch_drips()
{
    global $switched;
    $to_ret["drips"] = LP_adjust_drips();
    $to_drip = array();
    foreach($to_ret["drips"] as $post){
        switch_to_blog($post["blog_id"]);
        $t_img = wp_get_attachment_image_src( get_post_thumbnail_id( $post["post_id"] ));
        $post["img"] = $t_img[0];
        if(date("Ymd",strtotime($post["post_date"]))== date("Ymd")){
            $post["day_group"] = "Today ".date("d",strtotime($post["post_date"]));
        }else{
            $post["day_group"] = date("l d",strtotime($post["post_date"]));
        }
        $post["time_string"] = date("H:i",strtotime($post["post_date"]));
        $to_drip[] = $post;
        restore_current_blog();
    }
    $to_ret["drips"] = $to_drip;
    $to_ret["post"]  = $_POST;
    echo json_encode($to_ret);
    die();
}
add_action('wp_ajax_LP_adjust_fetch_drips', 'LP_adjust_fetch_drips');

/* 
 * fetch all drips enqueued
 */
function LP_adjust_drips()
{
    global $wpdb;
    
    $user_ID = get_current_user_id();
    $user_blogs = get_blogs_of_user( $user_ID );
    // print_r($user_blogs);
    $sql = "";
    $a = 0;
    $to_return = array();
    $prefix = $wpdb->base_prefix;
    foreach($user_blogs as $blog){
        if($blog->userblog_id == 1){
            continue;
        }else{
			$db_name = LP_get_blog_db_name($blog->userblog_id);
            $wp_lp_drip_queue = "`".$db_name."`.`" . $prefix.$blog->userblog_id."_lp_drip_queue`";
            $wp_posts = "`$db_name`.`" . $prefix.$blog->userblog_id."_posts`";
        }
        $sql= "SELECT * FROM $wp_lp_drip_queue d
            LEFT JOIN $wp_posts p on p.`ID` = d.`post_id`
            WHERE d.`is_published`=0 AND d.`blog_id`=".$blog->userblog_id."  AND p.`post_author` = $user_ID AND p.`post_type`='post' AND (p.`post_status`='future' OR p.`post_status`='draft') ORDER BY d.`order` ASC";
        if(count($to_return)>0){
            $to_return = merge_these($to_return, $wpdb->get_results($sql,ARRAY_A));
        }else{
            $to_return = $wpdb->get_results($sql,ARRAY_A);
        }
        $a++;
    }
    usort($to_return, "sort_drips");
    return $to_return;
}


function sort_drips( $b, $a ) {
    return $b["order"] - $a["order"];
}

/* 
 * 
 */
function merge_these($arr1,$arr2){
    $arr = array();
    $a = 0;
    foreach($arr1 as $k => $v){
        $arr[$a] = $v;
        $a++;
    }
    $b = count($arr1);
    foreach($arr2 as $k1 => $v1){
        $arr[$b] = $v1;
        $b++;
    }
    return $arr;
}

/* 
 * add to drip schedules
 */
function LP_adjust_update_draft($post_id, $blog_id)
{
    global $wpdb;
    global $LP_user_drip_settings;
    global $switched;
    
    $DEBUG = false;
    
    if($DEBUG) echo "<br />post ID : ".$post_id." blog ID : ".$blog_id."</br>";
    $user_ID    = get_current_user_id(); 
    $user_meta  = get_user_meta($user_ID);
    LP_drip_settings($user_meta);
	
	$prefix = $wpdb->base_prefix; 
	$db_name = LP_get_blog_db_name($blog_id);
	
    if($blog_id == 1){
        $lp_drip_queue = "`$db_name`.`" . $prefix . "lp_drip_queue`";
    }else{
        $lp_drip_queue = "`$db_name`.`" .$prefix .$blog_id ."_lp_drip_queue`";
    }
	
    $qry = "SELECT * FROM $lp_drip_queue WHERE `blog_id`=$blog_id  AND `post_id`=$post_id";
    $the_dripx = $wpdb->get_results($qry,ARRAY_A);
    if(count($the_dripx)<=0){

        $drip_setting = $LP_user_drip_settings["drip_time"];
        $drip_t_keys = array_keys($LP_user_drip_settings["drip_time"]);

        $indexx = "NO";
        
        $qry = "SELECT MAX(`order`)max_order,DATE_FORMAT(MAX(`post_date`),'%Y-%m-%d')max_date FROM $lp_drip_queue";        
        
        $max_s = $wpdb->get_results($qry,ARRAY_A);

        if(isset($max_s[0]["max_order"]) && $max_s[0]["max_order"]!=""){            
            $new_order = $max_s[0]["max_order"] +1;
            $last_date = $max_s[0]["max_date"];
            if($DEBUG) echo "last_date : ". $last_date."<br />";
            if(strtotime($last_date) < strtotime(date("Y-m-d"))){
                if($DEBUG) echo "Setting the Date today<br />";
                $last_date = date("Y-m-d");
                $indexx = $drip_t_keys[0];
            }
        }else{
            if($DEBUG) echo "Setting First schedule<br />";
            $new_order = 0;
            $last_date = date("Y-m-d");
            $indexx = $drip_t_keys[0];
        }
        
        if(is_int($indexx) || ($indexx!="" && $indexx != "NO")){
            $to_date    = $last_date ." ". $drip_setting[$indexx];
            if($DEBUG) echo "Indexx is already set so we take the time from settings : $to_date<br />";
            
            // format the date
            $post_date  = date("Y-m-d H:i:s",strtotime($to_date));
        }else{
            $qry = "SELECT count(*)c FROM $lp_drip_queue 
                    WHERE 
                    DATE_FORMAT(`post_date`,'%Y-%m-%d') = DATE_FORMAT('".$last_date."','%Y-%m-%d') 
                    GROUP BY DATE_FORMAT(`post_date`,'%Y-%m-%d') 
                    LIMIT 0,1";
            $num_date_schedule = $wpdb->get_results($qry,ARRAY_A);
            if($DEBUG) echo "We have the date but indexx is not set wo we need to get it first : ".$num_date_schedule[0]['c'].".<br />";

            // check if all meter time has post in schedule for the today and proceed if true to use the first meter time.
            if($num_date_schedule[0]['c'] >= count($drip_setting)){
                // use the first meter time
                $indexx = $drip_t_keys[0];
                $meter_time =$drip_setting[$indexx];
                $to_date = strtotime(date("Y-m-d",strtotime($last_date)+86400)." ".$meter_time);
                 if($DEBUG) echo "Enough drips are scheduled for this day so we use the first mete time from settings for the next day of $last_date : $to_date<br />";
            }else{
                // use the correct index for the meter time.
                $indexx      = $drip_t_keys[$num_date_schedule[0]['c']];
                
                $meter_time =$drip_setting[$indexx];
                $to_date = strtotime($last_date." ".$meter_time);
                
                if($DEBUG) echo "We will proceed with using the last_date as the date : ".$last_date." ".$meter_time."<br />";
            }
            
            
            
            // format the date
            $post_date  = date("Y-m-d H:i:s",$to_date);
                
        }
        
        if($DEBUG) echo "Post Date : $post_date<br/>";
        $day = strtolower(date("l",strtotime($post_date)));
        if($LP_user_drip_settings["drip_day"][$day]=="0"){
            $aday = 0;
            $s    = false;
            $have_scheuled = false;
            foreach($LP_user_drip_settings["drip_day"] as $key => $sday){
                if($key == $day || $s == true){
                    if($sday == 1){
                        $have_scheuled = true;
                        break;
                    }
                    $aday+=86400;
                    $s = true;
                }
            }
            
            if($have_scheuled == false){
                foreach($LP_user_drip_settings["drip_day"] as $k => $sd){
                        if($sd == 1){
                            break;
                        }
                        $aday+=86400;
                }
            }
            $post_date = date("Y-m-d H:i:s",strtotime($post_date) + $aday);
        }
       if($DEBUG)  echo "Final Post Date : $post_date<br/>";
        $sql = "INSERT INTO $lp_drip_queue SET `post_id`=$post_id, `blog_id`=$blog_id, `is_published`=0, `order`=$new_order, `post_date`='$post_date'";

        $wpdb->query($sql);
        switch_to_blog($blog_id);
        $my_args = Array(
            "ID"            => $post_id,
            "post_status"   => "future",
            "post_date"     => $post_date,
            "post_date_gmt" => get_gmt_from_date($post_date),
            "edit_date"     => true
        );
        remove_action('save_post', 'LP_adjust_save_posts');
            wp_update_post( $my_args );
        restore_current_blog();
    }
    return $post_id;
}

/* 
 * Save Post
 */
function LP_adjust_save_posts($post_id)
{
    $post_type = get_post_type($post_id);
    $post_status = get_post_status($post_id);
    if (!wp_is_post_revision( $post_id ) && ($post_status=="future" || $post_status=="draft"  || $post_status=="auto-draft") && $post_type=="post"){
        $blog_id = get_current_blog_id();
        LP_adjust_update_draft($post_id, $blog_id);
    }  
    return $post_id;
}
// add_action('save_post', 'LP_adjust_save_posts');


function LP_adjust_assign_schedules()
{
    LP_adjust_create_table();
}

add_action('wp_login', 'LP_adjust_assign_schedules');

function LP_adjust_create_table()
{
    if(is_user_logged_in()){
        global $wpdb;      
        require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
        $user_ID = get_current_user_id(); 
        $user_blogs = get_blogs_of_user( $user_ID );
		$prefix = $wpdb->base_prefix;
		
		foreach($user_blogs as $blog){
			$db_name = LP_get_blog_db_name($blog->userblog_id);
			if($blog->userblog_id == 1){
					$lp_drip_queue = "`$db_name`.`".$prefix."lp_drip_queue`";
			}else{
				$lp_drip_queue = "`$db_name`.`".$prefix.$blog->userblog_id."_lp_drip_queue`";
			}
			if ($wpdb->get_var("SHOW TABLES LIKE '" . $lp_drip_queue . "'") != $lp_drip_queue) {            
				$create_table = "CREATE TABLE IF NOT EXISTS ".$lp_drip_queue." (
					`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
					`post_id` bigint(20) NOT NULL,
					`blog_id` bigint(20) NOT NULL,
					`post_date` datetime NOT NULL,
					`is_published` int(11) NOT NULL,
					`order` bigint(20) NOT NULL,
					PRIMARY KEY (`id`),
					KEY `post_id` (`post_id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
				dbDelta($create_table);
				$posts = LP_fetch_drafts("all");
				foreach($posts as $post){
					LP_adjust_update_draft($post["ID"], $blog->userblog_id);
				}
			}
		}
    }
}

function LP_udpate_sort_drips()
{
    global $wpdb;
    global $LP_user_drip_settings;
    
    $drip_times = count($LP_user_drip_settings["drip_time"]);
    $drip_t_keys = array_keys($LP_user_drip_settings["drip_time"]);
    
    $new_drip_order = explode(",",$_POST["new_drip_order"]);

    $user_ID = get_current_user_id(); 
    $user_blogs = get_blogs_of_user( $user_ID );
	$prefix = $wpdb->base_prefix;
    foreach($user_blogs as $blog){
		$db_name = LP_get_blog_db_name($blog->userblog_id);
        if($blog->userblog_id != 1){
            $lp_drip_queue = "`$db_name`.`".$prefix.$blog->userblog_id."_lp_drip_queue`";
        }else{
            continue;
        }
        $sql = "SELECT min(`order`)minorder FROM `$lp_drip_queue` WHERE `is_published`=0";
        $min_order = $wpdb->get_results($sql,ARRAY_A);
        $minorder = $min_order[0]["minorder"];
        
        
        $qry = "SELECT count(*)c ,`post_date` 
                FROM `$lp_drip_queue` 
                WHERE DATE_FORMAT(`post_date`,'%Y-%m-%d') = (
                        SELECT DATE_FORMAT( MAX(  `post_date` ) ,  '%Y-%m-%d' ) 
                        FROM  `$lp_drip_queue` 
                        WHERE  `is_published` =1
                    )";
        $last_schedule = $wpdb->get_results($sql,ARRAY_A);
        $future_drips = LP_fetch_drafts('future');
        $last_day_submitted = $last_schedule[0]["c"];
       
        if($last_day_submitted>0){
            if($last_day_submitted < $drip_times){
                $t_start = $drip_times + 1;
                $schedule_last_date = date("U",strtotime($last_schedule[0]["post_date"]));
            }else{
                $schedule_last_date = date("U",strtotime("Yesterday"));
                $t_start = 0;
            }
        }else{
            $schedule_last_date = date("U",strtotime("Yesterday"));
            $t_start = 0;
        }
        $new_future_date = $schedule_last_date;   
        $first_loop = true;
        foreach($new_drip_order as $key => $drip){
            $t_start++;

            if($first_loop){
                if($t_start == 1){
                    $new_future_date_day = date("Y-m-d",$new_future_date + 86400);
                }else{
                    $new_future_date_day = date("Y-m-d");
                }
                $first_loop = false;
            }else{
                if($t_start == 1){
                    $new_future_date_day = date("Y-m-d",strtotime($new_future_date) + 86400);
                }
            }
            
            $schedule = $LP_user_drip_settings["drip_time"][$drip_t_keys[$t_start-1]];
            $t = str_replace(" am",":00 AM", $schedule);
            $t = str_replace(" pm",":00 PM", $t);
            $new_future_date = Date("Y-m-d H:i:s",strtotime($new_future_date_day." ".$t));
            

            $qry = "UPDATE `$lp_drip_queue` SET `order`=$minorder,`post_date`= '$new_future_date' WHERE `id`=$drip";
            $wpdb->query($qry);
            $minorder++;
            
            
            $qry = "SELECT * FROM `$lp_drip_queue` WHERE `id`=$drip";
            $post_s = $wpdb->get_results($qry,ARRAY_A);
            
            global $switched;
            switch_to_blog($post_s[0]["blog_id"]);
            $my_args = Array(
                "ID"            => $post_s[0]["post_id"],
                "post_status"   => "future",
                "post_date"     => $new_future_date,
                "post_date_gmt" => get_gmt_from_date($new_future_date),
                "edit_date"     => true
            );
            
            wp_update_post( $my_args );
            restore_current_blog();
            
            if($t_start >= $drip_times){
                $t_start = 0;
            }
        }
    }
    // LP_adjust_fetch_drips();
}
add_action('wp_ajax_LP_udpate_sort_drips', 'LP_udpate_sort_drips');

/* 
 * Updates post from AJAX
 */
function LP_adjust_update_drip_post(){
    global $wpdb;
    global $switched;
    $user_ID = get_current_user_id(); 
    $user_blogs = get_blogs_of_user( $user_ID );
    foreach($user_blogs as $blog){
		$db_name = LP_get_blog_db_name($blog->userblog_id);
        if($blog->userblog_id != 1){
            $lp_drip_queue = "`$db_name`.`".$wpdb->prefix.$blog->userblog_id."_lp_drip_queue`";
        }else{
            continue;
        }
        $qry = "SELECT * FROM $lp_drip_queue WHERE `id`=".$_POST["drip_id"];
        $post_s = $wpdb->get_results($qry,ARRAY_A);
        if($_POST["post_id"] == $post_s[0]["post_id"]){
            $my_args = Array(
                "ID"            => $_POST["post_id"],
                "post_content"  => $_POST["content"],
                "post_title"    => $_POST["title"],
                "post_excerpt"  => $_POST["excerpt"]
            );
            
            switch_to_blog($post_s[0]["blog_id"]);
            wp_update_post( $my_args );
            restore_current_blog();
        }
   }
}
add_action('wp_ajax_LP_adjust_update_drip_post', 'LP_adjust_update_drip_post');

function LP_reschedule_drips()
{
    global $wpdb;      
    $user_ID = get_current_user_id(); 
    $user_blogs = get_blogs_of_user( $user_ID );
    $prefix = $wpdb->base_prefix;
    foreach($user_blogs as $blog){
        if($blog->userblog_id == 1){
                continue;
        }else{
			$db_name = LP_get_blog_db_name($blog->userblog_id);
            $lp_drip_queue = "`$db_name`.`".$prefix.$blog->userblog_id."_lp_drip_queue`";
        }
        $sql = "TRUNCATE $lp_drip_queue";
        // echo $sql;
        $wpdb->query($sql);
        $posts = LP_fetch_drafts("all");
        foreach($posts as $post){
            LP_adjust_update_draft($post["ID"], $blog->userblog_id);
        }
    }
    // LP_adjust_fetch_drips();
}


?>