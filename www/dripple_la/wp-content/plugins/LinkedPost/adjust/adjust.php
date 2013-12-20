<?php
/* 
 * This will reschedule all post with status as future based on the topics meter settings; 
 * $restart if set to false it will consider the number of drips published today.
 */
function LP_reschedule_all_drips($topic_id)
{
	global $wpdb;
	$blog_id = LP_get_user_blog_id();
	$db_name = LP_get_blog_db_name($blog_id);
	$prefix = $wpdb->base_prefix;
	$sql = "UPDATE `{$db_name}`.`{$prefix}{$blog_id}_posts` SET `post_status` = 'draft' WHERE `post_status` = 'future' AND `post_parent` = $topic_id";
	$wpdb->query($sql);
	
	LP_maintain_enough_schedules($topic_id);
}

/* 
 * Returns all the future drips from the specific topic 
 */
function LP_fetch_topics_future_drips($topic_id)
{
	global $wpdb;
	$blog_id = LP_get_user_blog_id();
	$db_name = LP_get_blog_db_name($blog_id);
	$prefix = $wpdb->base_prefix;
	$sql = "SELECT * FROM `{$db_name}`.`{$prefix}{$blog_id}_posts` WHERE `post_parent`={$topic_id} AND `post_type`='post' AND (`post_status` = 'future' || `post_status` = 'draft') ORDER BY `post_date` ASC";
	return $wpdb->get_results($sql,ARRAY_A);
}

function LP_fetch_blog_latest_drip()
{
    global $wpdb, $shardb_prefix;
    $blog_id = get_current_blog_id();
    $db_name = LP_get_blog_db_name($blog_id);
    $prefix = $wpdb->base_prefix;
    $sql = "SELECT * FROM `{$db_name}`.`{$prefix}{$blog_id}_posts` p WHERE DATE_FORMAT(p.`post_date`,'%Y-%m-%d')<= DATE_FORMAT('".date("Y-m-d")."','%Y-%m-%d')  AND p.`post_status`='publish' ORDER BY p.`post_date` DESC LIMIT 0,1";
    $post = $wpdb->get_results($sql, ARRAY_A);
    if(count($post)>0){
        return $post[0];
    }else{
        return false;
    }
}
/* 
 * Returns all the future drips from the user by topics 
 * $topics is CSV of topic_ids
 */
function LP_fetch_user_future_drips($topics,$history = false)
{
	global $wpdb, $shardb_prefix, $switched;
	$global_db = $shardb_prefix."global";
	$blog_id = LP_get_user_blog_id();
	$db_name = LP_get_blog_db_name($blog_id);
	$prefix = $wpdb->base_prefix;
	$ins = explode(",",$topics);
	$the_ins = "";
	$sep = "";
	
	// This will make sure that no MySql injections can pass through....
	foreach($ins as $in){
		$the_ins.=$sep.$wpdb->prepare("%d",$in);
		$sep = ", ";
	}
	$post_status = "(p.`post_status` = 'future' || p.`post_status` = 'draft')";
	$order = "CAST(lp_order as SIGNED) ASC, CAST(p.`post_date` as DATETIME) ASC";
	$AND_extra = "";
	if($history){
		switch_to_blog($blog_id);
			$curr_blog_time = current_time( 'timestamp', 0 );
		restore_current_blog();
	
		$post_status = "p.`post_status` = 'publish'";
		$order = "CAST(p.`post_date` as DATETIME) ASC";
		$AND_extra = "AND CAST(p.`post_date` as DATETIME) > CAST('".date('Y-m-d H:i:s',$curr_blog_time-(86400*10))."' as DATETIME)";
	}
	$lp_flip_dir = lp_flip_dir()."/";
	$sql = "SELECT
				p.*,
				t.`post_title` as topic_name, 
				c.`name` as channel_name,
				m.`meta_value` as lp_order,
				fm.`meta_value` as lp_flip_img,
				'".$lp_flip_dir."' flip_dir
			FROM `{$db_name}`.`{$prefix}{$blog_id}_posts` p 
			LEFT JOIN `{$db_name}`.`{$prefix}{$blog_id}_posts` t on t.`ID` = p.`post_parent`
			LEFT JOIN (SELECT * FROM `{$db_name}`.`{$prefix}{$blog_id}_postmeta` WHERE `meta_key` = 'LP_channel' ) pm on pm.`post_id` = t.`ID`
			LEFT JOIN `{$global_db}`.`{$prefix}channels` c on c.`id` = pm.`meta_value`
			LEFT JOIN (SELECT * FROM `{$db_name}`.`{$prefix}{$blog_id}_postmeta` WHERE `meta_key` = '_LP_drip_order' ) m on m.`post_id` = p.`ID`
			LEFT JOIN (SELECT * FROM `{$db_name}`.`{$prefix}{$blog_id}_postmeta` WHERE `meta_key` = '_LP_flip_img' ) fm on fm.`post_id` = p.`ID`
			WHERE 
				p.`post_parent` IN ({$the_ins}) 
				AND p.`post_type`='post' 
				AND $post_status
				$AND_extra
			ORDER BY $order";
	// echo $sql;
	$future_drips = $wpdb->get_results($sql,ARRAY_A);
	switch_to_blog($blog_id);
	$grouped_drips = array();
	foreach($future_drips as $drip){
		$grouped_drips[$drip["post_parent"]]["{$drip["ID"]} "] = $drip;
        
		$curr_blog_time = current_time( 'timestamp', 0 );
		
        if($drip["post_status"] == "future"){
            if(date("Y-m-d",strtotime($drip["post_date"])) == date("Y-m-d",$curr_blog_time)){
                $grouped_drips[$drip["post_parent"]]["{$drip["ID"]} "]["date_label"]= "Today";
            }elseif(date("Y-m-d",strtotime($drip["post_date"])) == date("Y-m-d",$curr_blog_time+86400)){
                $grouped_drips[$drip["post_parent"]]["{$drip["ID"]} "]["date_label"]= "Tomorrow";
            }else{
                $grouped_drips[$drip["post_parent"]]["{$drip["ID"]} "]["date_label"] = strtolower(date("l",strtotime($drip["post_date"])));
            }
        }elseif($drip["post_status"] == "draft"){
            $grouped_drips[$drip["post_parent"]]["{$drip["ID"]} "]["date_label"] = "drafts";
        }else{
			if(!$history)continue;
			else $grouped_drips[$drip["post_parent"]]["{$drip["ID"]} "]["date_label"] = strtolower(date("l",strtotime($drip["post_date"])));
        }
        
        $grouped_drips[$drip["post_parent"]]["{$drip["ID"]} "]["d_day"] = strtolower(date("l",strtotime($drip["post_date"])));
		$grouped_drips[$drip["post_parent"]]["{$drip["ID"]} "]["date_actual"] = date("Y-m-d",strtotime($drip["post_date"]));
		$grouped_drips[$drip["post_parent"]]["{$drip["ID"]} "]["date_actual_formatted"] = date("jS F",strtotime($drip["post_date"]));
		$grouped_drips[$drip["post_parent"]]["{$drip["ID"]} "]["post_time"] = date("h:i a",strtotime($drip["post_date"]));
		
		$grouped_drips[$drip["post_parent"]]["{$drip["ID"]} "]["LP_flip_img"] = lp_flip_dir()."/".$drip["lp_flip_img"];
		$grouped_drips[$drip["post_parent"]]["{$drip["ID"]} "]["lp_drip_img"] = LP_get_post_thumb_url($drip["ID"], $blog_id,"lp-drip");
	
		$grouped_drips[$drip["post_parent"]]["{$drip["ID"]} "]["tags"] = wp_get_post_tags($drip["ID"]);
		$story_URL =  LP_get_post_story_URL($drip["ID"], $blog_id);
		$grouped_drips[$drip["post_parent"]]["{$drip["ID"]} "]["story_URL"] = $story_URL["story_URL"];
	}
	restore_current_blog();
	if(count($grouped_drips)>0){
		return $grouped_drips;
	}else{
		return false;
	}
}

function LP_fetch_all_topic_future_drips()
{
	if(is_user_logged_in()){
		$future_drips = LP_fetch_user_future_drips($_POST["user_topic_ids"]);
		if($future_drips){
			// print_r($future_drips);
			echo json_encode($future_drips);
		}else{
			echo "0";
		}
	}
	die();
}
add_action('wp_ajax_LP_fetch_all_topic_future_drips', 'LP_fetch_all_topic_future_drips');

function LP_fetch_all_topic_history_drips()
{
	if(is_user_logged_in()){
		$is_history = true;
		$user_topic_ids = $_POST["user_topic_ids"];
			
		$history_drips = LP_fetch_user_future_drips($user_topic_ids, $is_history);
		if($history_drips){
			// print_r($history_drips);
			echo json_encode($history_drips);
		}else{
			echo "0";
		}
	}
	die();
}
add_action('wp_ajax_LP_fetch_all_topic_history_drips', 'LP_fetch_all_topic_history_drips');

/* 
 * Fetches the future drip 
 * $drip_id is the post_id of the drip
 */
function LP_fetch_future_drip($drip_id)
{
	global $wpdb, $shardb_prefix, $switched;
	$global_db = $shardb_prefix."global";
	$blog_id = LP_get_user_blog_id();
	$db_name = LP_get_blog_db_name($blog_id);
	$prefix = $wpdb->base_prefix;
	$sql = $wpdb->prepare("SELECT
				p.*,
				t.`post_title` as topic_name, 
				c.`name` as channel_name
			FROM `{$db_name}`.`{$prefix}{$blog_id}_posts` p 
			LEFT JOIN `{$db_name}`.`{$prefix}{$blog_id}_posts` t on t.`ID` = p.`post_parent`
			LEFT JOIN (SELECT * FROM `{$db_name}`.`{$prefix}{$blog_id}_postmeta` WHERE `meta_key` = 'LP_channel' ) pm on pm.`post_id` = t.`ID`
			LEFT JOIN `{$global_db}`.`{$prefix}channels` c on c.`id` = pm.`meta_value`
			WHERE 
				p.`ID` = %d
				AND p.`post_type`='post' 
				AND (p.`post_status` = 'future' || p.`post_status` = 'draft') 
			ORDER BY p.`post_date` ASC",$drip_id);
	// echo $sql;
	$future_drips = $wpdb->get_results($sql,ARRAY_A);
	switch_to_blog($blog_id);
	$grouped_drips = array();
	foreach($future_drips as $drip){
		$grouped_drips[$drip["post_parent"]]["{$drip["ID"]} "] = $drip;
        $curr_blog_time = current_time( 'timestamp', 0 );
        if($drip["post_status"] == "future"){
            if(date("Y-m-d",strtotime($drip["post_date"])) == date("Y-m-d",$curr_blog_time)){
                $grouped_drips[$drip["post_parent"]]["{$drip["ID"]} "]["date_label"]= "Today";
            }elseif(date("Y-m-d",strtotime($drip["post_date"])) == date("Y-m-d",$curr_blog_time+86400)){
                $grouped_drips[$drip["post_parent"]]["{$drip["ID"]} "]["date_label"]= "Tomorrow";
            }else{
                $grouped_drips[$drip["post_parent"]]["{$drip["ID"]} "]["date_label"] = strtolower(date("l",strtotime($drip["post_date"])));
            }
        }elseif($drip["post_status"] == "draft"){
            $grouped_drips[$drip["post_parent"]]["{$drip["ID"]} "]["date_label"] = "drafts";
        }else{
            continue;
        }
        
		$grouped_drips[$drip["post_parent"]]["{$drip["ID"]} "]["date_actual"] = date("Y-m-d",strtotime($drip["post_date"]));
		$grouped_drips[$drip["post_parent"]]["{$drip["ID"]} "]["post_time"] = date("h:i a",strtotime($drip["post_date"]));
		$grouped_drips[$drip["post_parent"]]["{$drip["ID"]} "]["date_actual_formatted"] = date("jS F",strtotime($drip["post_date"]));
		$grouped_drips[$drip["post_parent"]]["{$drip["ID"]} "]["thumb_medium"] = LP_get_post_thumb_url($drip["ID"], $blog_id,"lp-medium");
		$grouped_drips[$drip["post_parent"]]["{$drip["ID"]} "]["tags"] = wp_get_post_tags($drip["ID"]);
	}
	restore_current_blog();
	if(count($grouped_drips)>0){
		return $grouped_drips;
	}else{
		return false;
	}
}

/* 
 * This will return the next date schedule for a topic 
 */
function LP_get_next_date_schedule($topic_id)
{
	global $wpdb, $switched;
	if(is_user_logged_in()){
		$blog_id = LP_get_user_blog_id();
	}else{
		$blog_id = get_current_blog_id();
	}
	$db_name = LP_get_blog_db_name($blog_id);
	$prefix = $wpdb->base_prefix;
	
	$sql = "SELECT `post_date` FROM `{$db_name}`.`{$prefix}{$blog_id}_posts` WHERE `post_parent`={$topic_id} AND `post_type`='post' AND `post_status` = 'future' ORDER BY `post_date` DESC LIMIT 0,1";
	$l_date = $wpdb->get_results($sql,ARRAY_A);
	
	// IF not future drips are found, we check the date of the latest published post
	if(count($l_date)<=0){
		$sql = "SELECT `post_date` FROM `{$db_name}`.`{$prefix}{$blog_id}_posts` WHERE `post_parent`={$topic_id} AND `post_type`='post' AND `post_status` = 'publish' ORDER BY `post_date` DESC LIMIT 0,1";
		$l_date = $wpdb->get_results($sql,ARRAY_A);
	}
	// print_r($l_date);
	// Using the time of wordpress to get the correct timezone for every blogs
	switch_to_blog($blog_id);
		$curr_blog_time = current_time( 'timestamp', 0 );
	restore_current_blog();
	
	$topic_meter = LP_get_topic_meter($topic_id);
	// print_r($topic_meter);
	$drip_time = $topic_meter["drip_time"];
	$c_drip_time = count($drip_time);
	$today = date("Y-m-d",$curr_blog_time);
	$the_date = date("Y-m-d",strtotime($l_date[0]["post_date"]));
	// print_r($topic_meter);
	// echo $the_date." >= ".$today."\n\r";
	if(count($l_date)>0 && date("U",strtotime($the_date))>=date("U",strtotime($today))){
		$last_day = date("Y-m-d",strtotime($l_date[0]["post_date"]));
		$last_time = date("h:i a",strtotime($l_date[0]["post_date"]));
		// echo $last_day." ".$last_time." \n\r";
		$to_next_time = "";
		$to_next_day = "";		
		if(array_key_exists($last_time,$drip_time)){
			$loop = true;
			$a =0;			
			while($loop==true){
				$time = current($drip_time);
				if($time == $last_time){
					// echo $time.' == '.$last_time." yep we are a match!!!\n\r";
					// this means that the next schedule will be tomorrow
					if(end($topic_meter["drip_time"]) == $time){
						// echo "end \n\r";
						$to_next_time = reset($drip_time);
						$to_next_day = date("Y-m-d",strtotime($last_day) + 86400);
					}else{
						// echo "next \n\r";
						$to_next_time = next($drip_time);
						$to_next_day = $last_day;
					}
					$loop = false;
				}
				if($a>=$c_drip_time){
					$loop = false;
					die();
				}
				
				next($drip_time);
				$a++;
			}
		}else{
			$to_next_day = date("Y-m-d",$curr_blog_time);
			$to_next_time = reset($drip_time);
			//Resetting of all the schedules should be called here...
		}
	}else{
		$to_next_day = date("Y-m-d",$curr_blog_time);
		$to_next_time = reset($drip_time);
	}
	
	$day = strtolower(date("l",strtotime($to_next_day)));
	$day_toggled = $topic_meter["drip_day"][$day];
	// echo $to_next_day." : ".$day." = ".$day_toggled."\n\r";
	while($day_toggled == "false" || $day_toggled == "0" || $day_toggled == false){
		$to_next_day = date("Y-m-d",strtotime($to_next_day)+86400);
		$day = strtolower(date("l",strtotime($to_next_day)));
		$day_toggled = $topic_meter["drip_day"][$day];
	}
	
	// Makes sure that the time should always be greater than now.
	// echo "check ".date("Y-m-d H:i:s A",$curr_blog_time) ." >= ".$to_next_day." ".$to_next_time."\n\r";
	
	if($curr_blog_time >= date("U",strtotime($to_next_day." ".$to_next_time))){
		// echo "Got in \n\r";
		$to_next_day = date("Y-m-d",$curr_blog_time);
		foreach($drip_time as $key => $time){
			$to_next_time = $time;
			if($curr_blog_time < date("U",strtotime($to_next_day." ".$to_next_time))){
				break;
			}
		}
		
		if($curr_blog_time >= date("U",strtotime($to_next_day." ".$to_next_time))){
			$to_next_time = reset($drip_time);
			$to_next_day = date("Y-m-d",strtotime($to_next_day." ".$to_next_time)+86400);
		}
	}
	$day = strtolower(date("l",strtotime($to_next_day)));
	$d_days = array('monday','tuesday','wednesday','thursday','friday','saturday','sunday');
	
	// Day is toggled off from meter page. So we nee to check the next day
	// echo '$day : '.$day." -- ".$topic_meter["drip_day"][$day];
	
// 1378557518 - 1378556640 = 878

// $day : sunday -- true1378557518 - 1378643280 = -85762

// $day : sunday -- true

	$day_toggled = $topic_meter["drip_day"][$day];
	// echo $to_next_day." : ".$day." = ".$day_toggled."\n\r";
	while($day_toggled == "false" || $day_toggled == "0" || $day_toggled == false){
		$to_next_day = date("Y-m-d",strtotime($to_next_day)+86400);
		$day = strtolower(date("l",strtotime($to_next_day)));
		$day_toggled = $topic_meter["drip_day"][$day];
	}
							

	// if($topic_meter["drip_day"][$day] == "0" || $topic_meter["drip_day"][$day] == "false" || $topic_meter["drip_day"][$day] === false){
		// $nday = date("N",strtotime($to_next_day));
		// $days_to_add = 0;
		// $has_assigned = false;
		// for($a=($nday-1); $a<7; $a++){
			// if($topic_meter["drip_day"][$d_days[$a]] == 1 || $topic_meter["drip_day"][$d_days[$a]] == "true"){
				// $has_assigned = true;
				// break;
			// }
			// $days_to_add++;
		// }
		
		// if($has_assigned == false){
			// reset($topic_meter["drip_day"]);
			// foreach($topic_meter["drip_day"] as $key => $tday){
				// $days_to_add++;
				// if($tday == 1 || $tday == "true"){
					// $has_assigned = true;
					// break;
				// }
			// }
		// }
		
		// if($has_assigned == false){
			// return false;
		// }
		
		// $to_next_day = date("Y-m-d",(strtotime($to_next_day))+($days_to_add * 86400));
	// }
	
	$next_date_schedule = date("Y-m-d H:i:s",strtotime($to_next_day." ".$to_next_time));
	// echo "next_date_schedule : ".$next_date_schedule."\n\r";
	return $next_date_schedule;
}

/* 
 * Updates Drip From ADJUST page
 */
function LP_adjust_update_drip_post(){
	if(is_user_logged_in()){
		global $wpdb;
		global $switched;
		$blog_id = LP_get_user_blog_id();

		if($_POST["post_id"] != ""){
			$my_args = Array(
				"ID"            => $_POST["post_id"],
				"post_content"  => $_POST["content"],
				"post_title"    => $_POST["title"],
				"post_excerpt"  => $_POST["excerpt"],
				"post_type"		=> "post"
			);
			
			switch_to_blog($blog_id);
				remove_save_actions();
				wp_update_post( $my_args );
				attach_save_actions();
			restore_current_blog();
			$updated_drip = LP_fetch_future_drip($_POST["post_id"]);
			if($updated_drip){
				echo json_encode($updated_drip);
			}else{
				echo "0";
			}
		}
	}
	die();
}
add_action('wp_ajax_LP_adjust_update_drip_post', 'LP_adjust_update_drip_post');

/* 
 * Call this function to ensure there are enough drips in schedule 
 */
function LP_maintain_enough_schedules($topic_id, $num=0)
{
	if(LP_is_topic_private($topic_id)){
		return true;
	}
	
	global $wpdb, $switched;
	if(is_user_logged_in()){
		$blog_id = LP_get_user_blog_id();
	}else{
		$blog_id = get_current_blog_id();
	}
	$db_name = LP_get_blog_db_name($blog_id);
	$prefix = $wpdb->base_prefix;
	
	if($num == 0){
		$topic_meter = LP_get_topic_meter($topic_id);
		$drip_time = $topic_meter["drip_time"];
		$num = count($drip_time);
	}
	
	$sql = "SELECT * FROM `{$db_name}`.`{$prefix}{$blog_id}_posts` WHERE `post_parent`={$topic_id} AND `post_type`='post' AND `post_status` = 'future' LIMIT 0, $num";
	// echo $sql;
	$future = $wpdb->get_results($sql,ARRAY_A);
	// print_r($future);
	if(count($future) >= $num){
		return true;
	}else{
		// echo $lack ." = ". $num ." - ". count($future);
		$lack = $num - count($future);
		switch_to_blog($blog_id);
		remove_save_actions();
		remove_filter ( 'publish_post', 'LP_call_maintain_num_dirps' );
		remove_filter ( 'publish_future_post', 'LP_call_maintain_num_dirps' );
		for($a = 0; $a<$lack; $a++){
			$next_date_schedule = LP_get_next_date_schedule($topic_id);
			// echo "next_date_schedule : ".$next_date_schedule."\n\r";
			$next_drip = LP_get_next_drip_enqueue($topic_id);
			if($next_drip){
				
					$my_args = Array(
						"ID"            => $next_drip["ID"],
						"post_status"   => "future",
						"post_date"     => $next_date_schedule,
						"post_date_gmt" => get_gmt_from_date($next_date_schedule),
						"edit_date"     => true
					);
					wp_update_post( $my_args );

					// $db_name = LP_get_blog_db_name($blog_id);
					// $prefix = $wpdb->base_prefix;
					// $sql = "UPDATE `{$db_name}`.`{$prefix}{$blog_id}_posts` SET `post_status` = 'future', `post_date` = '$next_date_schedule', `post_date_gmt` = '".get_gmt_from_date($next_date_schedule)."' WHERE `ID` = ".$next_drip["ID"];
					// $wpdb->query($sql);
			}else{
				// No more drips to schedule...
				break;
			}
		}
		add_filter ( 'publish_post', 'LP_call_maintain_num_dirps' );
		add_filter ( 'publish_future_post', 'LP_call_maintain_num_dirps' );
		attach_save_actions();
		restore_current_blog();
	}
}

function LP_get_next_drip_enqueue($topic_id)
{
	global $wpdb;
	$blog_id = LP_get_user_blog_id();
	$db_name = LP_get_blog_db_name($blog_id);
	$prefix = $wpdb->base_prefix;
	$sql = "SELECT *
			FROM `{$db_name}`.`{$prefix}{$blog_id}_posts` p
			LEFT JOIN `{$db_name}`.`{$prefix}{$blog_id}_postmeta` m on m.`post_id` = p.`ID`
			WHERE p.`post_parent` = $topic_id
			AND p.`post_status` = 'draft'
			AND p.`post_type` = 'post'
			AND m.`meta_key` = '_LP_drip_order'
			ORDER BY CAST(m.`meta_value` as SIGNED) ASC
			LIMIT 0,1
			";
	
	$to_next = $wpdb->get_results($sql,ARRAY_A);
	if(count($to_next)>0)
		return $to_next[0];
	else return false;
}

/* 
 * Setting the schedule of a new drip 
 */
function LP_new_drip($post_id)
{
	$topic_id = $_POST["LP_topic"];
	// echo $topic_id."\n\r";
	if($parent_id = wp_is_post_revision($post_id) ){
		$post_id = $parent_id;
	}
	$post_type = get_post_type($post_id);
	$post_status = get_post_status($post_id);
	if (!wp_is_post_revision( $post_id ) && isset($_POST["LP_topic"]) && $topic_id!="" && $post_status=='draft' && $post_type=='post'){
		$lp_drip_order = LP_get_next_topic_order($topic_id,$post_id);
		update_post_meta($post_id, "_LP_drip_order", $lp_drip_order);
		LP_maintain_enough_schedules($topic_id);
		// die();
	}
	// echo "a \n\r";
	return $post_id;	
}
add_action('save_post', 'LP_new_drip');

/* 
 * Reorder the drips from jQuery sortable by updating each of the postmeta '_LP_drip_order'
 */
function LP_reorder_drips()
{
	if(is_user_logged_in()){
		global $switched ,$wpdb;
		$blog_id = LP_get_user_blog_id();
		$db_name = LP_get_blog_db_name($blog_id);
		$prefix = $wpdb->base_prefix;
		$before = $_POST["before"];
		$after  = $_POST["after"];
		$topic_id  = $_POST["topic_id"];

		if($before){
			$s_id = $before;
		}elseif($after){
			$s_id = $after;
		}
		$post_id = $_POST["post_id"];
		$blog_id = LP_get_user_blog_id();
		switch_to_blog($blog_id);
		
		$srange = get_post_meta( $s_id, "_LP_drip_order", true );
		$erange = get_post_meta( $post_id, "_LP_drip_order", true );	
		// echo $srange ." < ". $erange;
	
		// ***Moving up a drip***
		if($srange < $erange){
		// * Moving UP Drip 6 BELOW Drip 2
		// * Try  to get the drip ID of Drip 2
		// * Update and ADD 1 to all the “ORDER NUMBER” that are
		// less than the “ORDER NUMBER” of Drip 6 and 
		// Greater than the “ORDER NUMBER” of Drip 2
		// * Update the “ORDER NUMBER” of Dip 6 with the previous 
		// “ORDER NUMBER” of (Drip 2)+1
			if($before){
				$soperand = "<";
				$eoperand = ">";
				$minplus = "+";
				$post_order = $srange+1;
			}else{
		// Could not find anything before this item... so we are using the item after this.
				
		// * Moving UP Drip 6 ABOVE Drip 2
		// * Try  to get the drip ID of Drip 2
		// * Update and ADD 1 to all the “ORDER NUMBER” that are
		// less than the “ORDER NUMBER” of Drip 6 and 
		// Greater than and equal to the “ORDER NUMBER” of Drip 2
		// * Update the “ORDER NUMBER” of Dip 6 with the previous 
		// “ORDER NUMBER” of (Drip 2)
				$soperand = "<";
				$eoperand = ">=";
				$minplus = "+";
				$post_order = $srange;
			}
		}else{
		// ***Moving down a drip***
		
		// Moving Drip 2 BELOW Drip 5
		// * Try to get the Drip ID of Drip 5
		// * Update and MINUS 1 to all the 
		// “ORDER NUMBER” that are 
		
		// LESS THAN to the “ORDER NUMBER” of Drip 5 
		
		// AND Greater than the “ORDER NUMBER” of Drip 2
		
		// * Upadte the “ORDER NUMBER” of Drip 2
		// with the “ORDER NUMBER” of (Dirp 5)
			if($before){
				$soperand = ">";
				$eoperand = "<";
				$minplus = "-";
				$post_order = $srange;
			}
		}

		$sql = "UPDATE `{$db_name}`.`{$prefix}{$blog_id}_posts` p
			LEFT JOIN `{$db_name}`.`{$prefix}{$blog_id}_postmeta` m on m.`post_id` = p.`ID`
			SET m.`meta_value` = (m.`meta_value`){$minplus}1
			WHERE 
				p.`post_parent` = $topic_id
				AND m.`meta_value` $soperand $erange
				AND m.`meta_value` $eoperand $srange
				AND m.`meta_key` = '_LP_drip_order'
			";
		
		$wpdb->query($sql);
		
		update_post_meta($post_id, '_LP_drip_order', $post_order);
		restore_current_blog();
		LP_reschedule_all_drips($topic_id);
		$t_drips = LP_fetch_user_future_drips($topic_id);
		if($t_drips !== false){
			echo json_encode($t_drips);
		}else echo "0";
		die();
	}
}
add_action('wp_ajax_LP_reorder_drips', 'LP_reorder_drips');

/* 
 * This gets the next number of order for the newly added drip in draft 
 */
function LP_get_next_topic_order($topic_id,$post_id)
{
	global $wpdb;
	$blog_id = LP_get_user_blog_id();
	$db_name = LP_get_blog_db_name($blog_id);
	$prefix = $wpdb->base_prefix;
	$sql = "SELECT p.`post_parent`,p.`ID` 
				, max(m.`meta_value`)+1 as LP_drip_order
			FROM `{$db_name}`.`{$prefix}{$blog_id}_posts` p
			LEFT JOIN `{$db_name}`.`{$prefix}{$blog_id}_postmeta` m on m.`post_id` = p.`ID`
			WHERE 
				p.`post_parent` = $topic_id
				AND `post_type` = 'post'
				AND (`post_status` = 'draft' OR `post_status`='future')
				AND m.`meta_key` = '_LP_drip_order'
				AND m.`post_id`!=$post_id
				";
	$to_next = $wpdb->get_results($sql,ARRAY_A);
	if($to_next[0]["LP_drip_order"])
		return $to_next[0]["LP_drip_order"];
	else return 0;
}

function LP_call_maintain_num_dirps($post_id)
{
	$blog_id = get_current_blog_id();
	$topic = LP_get_post_topic($post_id, $blog_id);
	$topic_id = $topic["ID"];
	LP_maintain_enough_schedules($topic_id);
}
add_filter ( 'publish_post', 'LP_call_maintain_num_dirps' );
// add_filter ( 'publish_future_post', 'LP_call_maintain_num_dirps' );



function LP_delete_buffer(){
	global $switched;
	if(is_user_logged_in()){
		$blog_id = LP_get_user_blog_id();
		switch_to_blog($blog_id);
			$post_id = $_POST["post_id"];
			$topic = LP_get_post_topic($post_id, $blog_id);
			$topic_id = $topic["ID"];		
			$res = wp_delete_post($post_id,true);
			if($res === false){
				echo "0";
			}else{
				LP_reschedule_all_drips($topic_id);
				$t_drips = LP_fetch_user_future_drips($topic_id);
				if($t_drips !== false){
					echo json_encode($t_drips);
				}else{ 
					echo "0";
				}
			}
		restore_current_blog();
	}
	die();
}
add_action('wp_ajax_LP_delete_buffer', 'LP_delete_buffer');
?>