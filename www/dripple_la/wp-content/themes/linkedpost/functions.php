<?php
add_theme_support( 'post-thumbnails' );
// Topic Images
add_image_size( 'lp-large', 662, 332,true );
add_image_size( 'lp-topic-medium', 495, 248,true );

// add_image_size( 'lp-medium', 490, 272,true );


// Drip images
add_image_size( 'lp-drip', 495, 275,true );
add_image_size( 'lp-small', 302,168,true );

require_once("includes/options.php");
require_once("includes/industries.php");

function word_trim($string, $count, $ellipsis = FALSE){
  $words = explode(' ', $string);
  if (count($words) > $count){
    array_splice($words, $count);
    $string = implode(' ', $words);
    if (is_string($ellipsis)){
      $string .= $ellipsis;
    }
    elseif ($ellipsis){
      $string .= '&hellip;';
    }
  }
  return $string;
} 

if ( function_exists('register_sidebars') )
 register_sidebars(1,array("name"=>"UserOnline"));
 
function wpu_user_avatars($name, $user) {
	if ( !$user->user_id )
		return $name;

	$url = get_author_posts_url($user->user_id);
	$avatar = get_avatar($user->user_id, 57);

	return html("a href='$url' title='$user->user_name'", $avatar);
}
add_filter('useronline_display_user', 'wpu_user_avatars', 10, 2);

/**
* Creates sharethis shortcode
*/
if (function_exists('st_makeEntries')) :
add_shortcode('sharethis', 'st_makeEntries');
endif;

function sortPosts( $a, $b ) {
    return strtotime($a["post_date"]) - strtotime($b["post_date"]);
}

function sortRipples( $a, $b ) {
	if(is_object($a)){
		$the_a = $a->comment_date;
	}else{
		$the_a = $a["post_date"];
	}
	
	if(is_object($b)){
		$the_b = $b->comment_date;
	}else{
		$the_b = $b["post_date"];
	}
    return strtotime($the_a) - strtotime($the_b);
}

function get_blog_post_info($blogid, $post_id, $user_id,$user_meta = array(), $post_meta = array()){
    global $switched;
    global $wpdb, $db_servers, $shardb_prefix;
	
	$global_db = $shardb_prefix."global";
	
    switch_to_blog($blogid);
    // echo "blog id : ".get_current_blog_id()."<br />";
    // echo "post_id : ".$post_id."<br />";
    $the_meta = array();
    foreach($post_meta as $meta_key){
        if($meta_key == "the_excerpt"){
            $the_meta[$meta_key] = "";
            // apply_filters( 'get_the_excerpt', $post->post_excerpt )
        }elseif($meta_key == "cloaked_URL"){
            $to_story = get_post_meta($post_id,"story_URL",true);
            if($to_story){
                $res = $wpdb->get_results("SELECT * FROM `$global_db`.`wp_post_iframe` WHERE `post_id`=$post_id AND `blog_id`=$blogid",ARRAY_A);
                if(count($res)>0){
                    $cloakedURL = $res[0]["id"];
                    $strlen = strlen($cloakedURL);
                    if($strlen < 5){
                        $x="";
                        for($a=0;$a<(5-$strlen);$a++){
                            $x.="0";
                        }
                        $cloakedURL=$x.$cloakedURL;
                    }
                }else{
                    $cloakedURL = "";
                }
            }else{
                $cloakedURL = "";
            }
            $the_meta[$meta_key] = $cloakedURL;
        }else{
            $the_meta[$meta_key] = get_post_meta($post_id, $meta_key, true);
        }
    }
    
     $u_meta = array();
    foreach($user_meta as $meta_key){
        $u_meta[$meta_key] = get_the_author_meta($meta_key, $user_id);
        // echo $meta_key." : ".$u_meta[$meta_key]."<br />";
        // die();
    }
    
    $the_permalink      = get_permalink($post_id);
    $post_thumbnail     = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id),"lp-small");
	$post_thumbnail_medium = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id),"lp-medium");
    $has_post_thumbnail = has_post_thumbnail($post_id);
    $get_the_channel    = get_the_channel($post_id, $blogid);
    $the_tags           = get_the_term_list( $post_id, 'post_tag',"" ,',','');
    $post_view          = do_shortcode('[post_view]');
    $dot_recommends     = do_shortcode('[dot_recommends]');
    $back_flip_image    = get_post_meta($post_id, "_LP_flip_img", true);
    $count_user_posts   = count_user_blog_posts($user_id);
    $ret = array(
            "the_permalink"         => $the_permalink,
            "has_post_thumbnail"    => $has_post_thumbnail,
            "post_thumbnail"        => $post_thumbnail,
			"post_thumbnail_medium"	=> $post_thumbnail_medium,
            "count_user_posts"      => $count_user_posts,
            "the_channels"          => $get_the_channel,
            "post_view"             => $post_view,
            "dot_recommends"        => $dot_recommends,
            "the_tags"              => $the_tags,
            "user_meta"             => $u_meta,
            "back_flip_image"       => $back_flip_image,
            "meta"                  => $the_meta
            );
    // print_r($ret);
    restore_current_blog();
    return $ret;
}

function get_post_cloaked_URL($post_id)
{
    global $wpdb;
    $current_blog_id = get_current_blog_id();
    $res = $wpdb->get_results("SELECT * FROM wp_post_iframe WHERE `post_id`=$post_id AND `blog_id`=$current_blog_id",ARRAY_A);
    if(count($res)<=0){
        return "";
    }else{
        $cloakedURL = $res[0]["id"];
        $strlen = strlen($cloakedURL);
        if($strlen < 5){
            $x="";
            for($a=0;$a<(5-$strlen);$a++){
                $x.="0";
            }
            $cloakedURL=$x.$cloakedURL;
        }
        return $cloakedURL;
    }
}

function count_user_blog_posts($user_id){
	global $switched;
    $user_blogs = get_blogs_of_user($user_id);

    $count_user_posts = 0;

    foreach($user_blogs as $blog => $ublog){  
        switch_to_blog($ublog->userblog_id);
			$count_user_posts+= count_user_posts($user_id);
		restore_current_blog();
    }
    
    return $count_user_posts;
}


function LP_set_user_func( $arg ) {
    require_once(plugin_dir_path(__FILE__).'../../plugins/LinkedPost/linkedin/linkedin_3.2.0.class.php');
	  
	  // start the session
	  if(!session_start()) {
		throw new LinkedInException('This script requires session support, which appears to be disabled according to session_start().');
	  }
	  
	  // display constants
	  $API_CONFIG = array(
        'appKey'       => '782a3kg6eadv',
        'appSecret'    => 'd3vPgblSQ9aQmYrA',
        'callbackUrl'  => NULL 
	  );

    $blog_id = $arg["blog_id"];

    $LP_linkedin_info = get_blog_option($blog_id, "LP_linkedin_info");
    $access = $LP_linkedin_info["linkedin_TokenAccess"];
    if(!isset($access) || $access=="") return "false";

    $OBJ_linkedin = new LinkedIn($API_CONFIG);
    $OBJ_linkedin->setTokenAccess($access);
    $profile = $OBJ_linkedin->profile('~:(id,first-name,last-name,email-address,educations,picture-url,picture-urls::(original),public-profile-url,three-current-positions:(company:(name,id,industry),isCurrent,title),positions,summary,specialties,interests)?format=json');
    
    $profile["linkedin"] = json_decode($profile["linkedin"]);
    if(isset($profile["linkedin"]->errorCode)){return "false";}
    
    $com_id = $profile["linkedin"]->positions->values[0]->company->id;
    $company = $OBJ_linkedin->company($com_id.':(locations:(address:(city,country-code)))?format=json');

    $profile["linkedin"]->company_ext = json_decode($company["linkedin"]);
    return json_encode($profile["linkedin"]);
}
add_shortcode('LP_set_user', 'LP_set_user_func');


function LP_get_top_influencers(){
    global $switched;
	global $wpdb, $db_servers;
	
    $prefix = $wpdb->base_prefix;
    
    $all_blogs = get_blog_list( 0, 'all' );

    $sql = "SELECT  posts.`post_author`, SUM(posts.mpost)totposts,blog_id FROM(";
    $sep = "";
    foreach($all_blogs as $blog){
        if($blog["blog_id"] == 1){
                continue;
        }else{
            $wp_posts = $prefix.$blog["blog_id"]."_posts";
        }
		
		$db_name = LP_get_blog_db_name($blog["blog_id"]);
		
        $sql.= $sep."SELECT COUNT(*) mpost,`post_author`,".$blog["blog_id"]." blog_id FROM `$db_name`.`$wp_posts` WHERE  `post_type`='post' AND `post_status`='publish' GROUP BY `post_author`";
        $sep = " UNION ALL ";
    }
    $sql.= ") as posts GROUP BY posts .`post_author`
            ORDER BY totposts DESC LIMIT 0,12";

    $blog_posts = $wpdb->get_results($sql,ARRAY_A); 

    return $blog_posts;
}

function LP_get_blog_db_name($blog_id){
	global $wpdb, $db_servers;
    if(!$blog_id){
        $blog_id = LP_get_user_blog_id();
    }
    
    if(!$blog_id){
        $db_name = "";
    }else{
        if ( function_exists('shardb_get_ds_part_from_blog_id') ){
            $ds_part = shardb_get_ds_part_from_blog_id( $blog_id );
            $db_name = $db_servers[ $ds_part[ 'dataset' ] ][ $ds_part[ 'partition' ] ][ 0 ][ 'name' ];
        }else{
            $db_name = "";
        }
    }
    return $db_name;
}

function LP_get_user_blog_id($user_id = "")
{
    if(!$user_id){
        if(is_user_logged_in()){
			// echo "I M here\n\r";
            $user_id = get_current_user_id(); 
        }else{
			// echo "not logged in\n\r";
            return get_current_blog_id();
        }
    }
	// echo "user_id : $user_id\n\r";
	$user_blogs = get_blogs_of_user( $user_id );
	foreach($user_blogs as $blog){
		if($blog->userblog_id == 1){
			// echo "userblog_id : {$blog->userblog_id}\n\r";
			continue;
		}else{
			$blog_id = $blog->userblog_id;
			// echo "else not 1 : {$blog->userblog_id}\n\r";
		}
	}
	// echo "blog_id : {$blog_id}\n\r";
    return $blog_id;
}

function LP_get_latest_comments(){
    global $switched;
	global $wpdb;

    $prefix = $wpdb->base_prefix;
    
    $all_blogs = get_blog_list( 0, 'all' );
    $sql = "SELECT * FROM(";
    $sep = "";
    foreach($all_blogs as $blog){
        if($blog["blog_id"] == 1){
                $wp_comments = $prefix."comments";
        }else{
            $wp_comments = $prefix.$blog["blog_id"]."_comments";
        }
        
		$db_name = LP_get_blog_db_name($blog["blog_id"]);
		
        $sql.= $sep."SELECT *,".$blog["blog_id"]." blog_id FROM `$db_name`.`$wp_comments` WHERE `comment_approved`=1 AND `user_id` > 0";
        $sep = " UNION ALL ";
    }
    $sql.= ") as comments ORDER BY `comment_date_gmt` DESC LIMIT 0,5";
            // echo $sql;

    $comments = $wpdb->get_results($sql,ARRAY_A); 

    return $comments;
}
///////////////////////////////////////////////////
define('MY_WORDPRESS_FOLDER',$_SERVER['DOCUMENT_ROOT']);
define('MY_THEME_FOLDER',str_replace("\\",'/',dirname(__FILE__)));
define('MY_THEME_PATH','/' . substr(MY_THEME_FOLDER,stripos(MY_THEME_FOLDER,'wp-content')));
 
add_action('admin_init','my_meta_init');
 
function my_meta_init()
{
    // review the function reference for parameter details
    // http://codex.wordpress.org/Function_Reference/wp_enqueue_script
    // http://codex.wordpress.org/Function_Reference/wp_enqueue_style
 
    //wp_enqueue_script('my_meta_js', MY_THEME_PATH . '/custom/meta.js', array('jquery'));
    wp_enqueue_style('my_meta_css', MY_THEME_PATH . '/custom/meta.css');
 
    // review the function reference for parameter details
    // http://codex.wordpress.org/Function_Reference/add_meta_box
 
    // add a meta box for each of the wordpress page types: posts and pages
    foreach (array('post') as $type) 
    {
        add_meta_box('my_all_meta', 'DipDrip Fields', 'my_meta_setup', $type, 'normal', 'high');
    }
     
    // add a callback function to save any data a user enters in
    add_action('save_post','my_meta_save');
}
 
function my_meta_setup()
{
    global $post;   
    // instead of writing HTML here, lets do an include
    include(MY_THEME_FOLDER . '/custom/meta.php');
  
    // create a custom nonce for submit verification later
    echo '<input type="hidden" name="my_meta_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
}
  
function my_meta_save($post_id) 
{
    if ( !wp_is_post_revision( $post_id ) ) {
        if ( 'trash' != get_post_status( $post_id ) && 'auto-draft' != get_post_status( $post_id ) && 'topic' != get_post_type ($post_id) ) {
            global $wpdb, $shardb_prefix;
            
            $global_db = $shardb_prefix."global";
            $prefix = $wpdb->base_prefix;
            $current_blog_id = get_current_blog_id();

            $new_SURL = $_POST["story_URL"];
            // update_post_meta($post_id,'story_URL',$new_SURL]);        
            $my_args = Array(
                "ID"            => $post_id,
                "post_parent"   => $_POST["LP_topic"]
            );
            remove_action('save_post', 'my_meta_save');
			remove_filter ( 'publish_post', 'LP_call_maintain_num_dirps' );
			remove_filter ( 'publish_future_post', 'LP_call_maintain_num_dirps' );
            wp_update_post( $my_args );
			$res_t = add_post_meta($post_id, '_LP_topic', $_POST["LP_topic"]);
			add_filter ( 'publish_post', 'LP_call_maintain_num_dirps' );
			add_filter ( 'publish_future_post', 'LP_call_maintain_num_dirps' );
            add_action('save_post','my_meta_save');
			
			$channel = get_the_channel($_POST["LP_topic"], $current_blog_id);
            
            $res = $wpdb->get_results("SELECT * FROM `{$global_db}`.`{$prefix}lp_drips` WHERE `post_id`=$post_id AND `blog_id`=$current_blog_id",ARRAY_A);
            if(count($res)<=0){
                $wpdb->query("INSERT INTO `{$global_db}`.`{$prefix}lp_drips` SET `story_URL`='$new_SURL', `post_id`=$post_id, `blog_id`=$current_blog_id, `channel_id`=".$channel["id"].",`post_date`='".date("Y-m-d H:i:s"."'"));
            }else{
                $wpdb->query("UPDATE `{$global_db}`.`{$prefix}lp_drips` SET `story_URL`='$new_SURL',`channel_id`=".$channel["id"]." WHERE `post_id`=$post_id AND `blog_id`=$current_blog_id");
            }
        }
    }
    return $post_id;
}

/*** 
 *Set blog post tags 
 */
function LP_set_tags($post_id, $blog_id, $tags)
{
    global $switched;
    switch_to_blog($blog_id);
        wp_set_post_tags( $post_id, $tags, false );
    restore_current_blog();
}
 
// function my_meta_clean(&$arr)
// {
    // if (is_array($arr))
    // {
        // foreach ($arr as $i => $v)
        // {
            // if (is_array($arr[$i])) 
            // {
                // my_meta_clean($arr[$i]);
 
                // if (!count($arr[$i])) 
                // {
                    // unset($arr[$i]);
                // }
            // }
            // else
            // {
                // if (trim($arr[$i]) == '') 
                // {
                    // unset($arr[$i]);
                // }
            // }
        // }
 
        // if (!count($arr)) 
        // {
            // $arr = NULL;
        // }
    // }
// }

////////////////////////// latest posts from all sites //////////////////////////////////
function LP_latest_posts($channels = "", $date_stop = "", $page = 1, $numposts = 20)
{
	global $wpdb, $shardb_prefix, $current_site;
    $LP_siteurl = "http://".$current_site->domain;
	$global_db = $shardb_prefix."global";
    $prefix = $wpdb->base_prefix;
	
	$date_where = "";
	if($date_stop != ""){
		$date_limit = date("Y-m-d",strtotime($date_stop));
		$date_where = "AND DATE_FORMAT(`post_date`,'%Y-%m-%d') >= DATE_FORMAT('$date_limit','%Y-%m-%d')";
	}else{
		// $date_limit = date("Y-m-d",strtotime("2 days ago"));
		// $date_where = "WHERE DATE_FORMAT(`post_date`,'%Y-%m-%d') >= DATE_FORMAT('$date_limit','%Y-%m-%d')";
		// $AND = " AND ";
	}
	
	
	$chan_filter = "";
	if(is_array($channels)){
		$in_channels = implode(",",$channels);
		$chan_filter = " AND `channel_id` IN ($in_channels)";
	}
    
	$start = ($page-1) * $numposts;
	if($start > 0) $start = $start -1;
	$qry = "SELECT * FROM `$global_db`.`{$prefix}lp_drips` WHERE (`post_type`='' OR `post_type`='post') $date_where $chan_filter  ORDER BY `id` DESC LIMIT $start,$numposts";
	// echo $qry;
	// die();
	$latest_posts = $wpdb->get_results($qry,ARRAY_A);
	$grouped_blog = array();
	foreach($latest_posts as $lpost){
		$grouped_blog[$lpost["blog_id"]] = $grouped_blog[$lpost["blog_id"]].",".$lpost["post_id"];
	}
	$sql = "SELECT * FROM (";
	$sep = "";

	foreach($grouped_blog as $blog_id => $posts){
		if($blog_id == 1){
				$wp_posts    = "{$prefix}posts";
				$wp_postmeta = "{$prefix}postmeta";
		}else{
			$wp_posts = $prefix.$blog_id."_posts";
			$wp_postmeta = $prefix.$blog_id."_postmeta";
		}

		$db_name = LP_get_blog_db_name($blog_id);
		$check_table = $wpdb->get_results("SELECT 1 as dip FROM information_schema.tables WHERE table_name = '{$prefix}posts' limit 1",ARRAY_A);
		if (isset($check_table) && $check_table[0]["dip"] == 1 ) {
			$sql.= $sep."SELECT 
							dp.*,
							$blog_id as blog_id, 
							pt.`post_title` topic_name, pt.`ID` topic_id, 
							dc.`name` channel_name, dc.`id` channel_id,
							dd.`story_URL`, CONCAT('".$LP_siteurl."/lp/0000',dd.`id`) cloaked_URL,
                            '".lp_flip_dir($blog_id)."/' flip_dir,
							f.`meta_value` _LP_flip_img
						FROM `{$db_name}`.`{$wp_posts}` dp
						LEFT JOIN `{$db_name}`.`{$wp_posts}` pt ON pt.`ID` = dp.`post_parent`
						LEFT JOIN (SELECT * FROM `{$db_name}`.`{$wp_postmeta}` WHERE `meta_key`='_private')as t_private ON t_private.`post_id` = dp.`post_parent`
						LEFT JOIN (SELECT * FROM `{$db_name}`.`{$wp_postmeta}` WHERE `meta_key`='_LP_flip_img')as f ON f.`post_id` = dp.`ID`
						LEFT JOIN `{$global_db}`.`{$prefix}lp_drips` dd ON dd.`post_id` = dp.`id`
						LEFT JOIN `{$global_db}`.`{$prefix}channels` dc ON dc.`id` = dd.`channel_id`
						WHERE dp.`ID` in(". trim($posts,",") .") 
							AND dp.`post_type`='post' 
							AND (dp.`post_status`='publish' OR dp.`post_status`='private')
							AND (t_private.`meta_value` = 'false' OR t_private.`meta_value` = 0 OR t_private.`meta_value` = false OR t_private.`meta_value` IS NULL)
							AND dd.`blog_id` = $blog_id";
			$sep = " UNION ALL ";
		}
	}
	$sql.= ") as from_all ORDER BY post_date_gmt DESC";
	// echo $sql;
	return $wpdb->get_results($sql,ARRAY_A); 
}

function LP_sync_posts($pid)
{
	global $wpdb, $shardb_prefix;
	
	$global_db = $shardb_prefix."global";
	$curr_blog_id = get_current_blog_id();
								
	if ($wpdb->get_var($wpdb->prepare('SELECT post_id FROM `$global_db`.`wp_post_iframe` WHERE post_id = %d AND blog_id=%d', $pid, $curr_blog_id))) {
		
		return $wpdb->query($wpdb->prepare('DELETE wi, wcr FROM `$global_db`.`wp_post_iframe` wi 
											LEFT JOIN `$global_db`.`wp_channel_relationship` wcr on wcr.`post_id`= wi.`post_id` AND wcr.`blog_id` = wi.`blog_id`
											WHERE wi.`post_id`=%d AND wi.`blog_id=`%d', $pid, $curr_blog_id));
	}
	return true;
}
add_action('delete_post', 'LP_sync_posts', 10);

function LP_get_channels(){
    global $wpdb, $shardb_prefix;
	
	$global_db = $shardb_prefix."global";
    $prefix = $wpdb->base_prefix;
    $sql = "SELECT * FROM `$global_db`.`{$prefix}channels`";
    return $wpdb->get_results($sql,ARRAY_A);
}

function LP_is_topic_private($topic_id)
{
	$is_private = get_post_meta($topic_id,"_private",true);
	if($is_private == "true" || $is_private === true)
		return true;
	else return false;
}

/* 
 * Fetch all topics of the user 
 */
function LP_get_user_topics($post_id = 0, $deep = false)
{
    global $siwtched;
    if(is_user_logged_in()){
        $blog_id = LP_get_user_blog_id();
        switch_to_blog($blog_id);
        global $wpdb, $shardb_prefix;
        $extra = "";
        if($post_id > 0){
            $extra = "AND dp.`ID`= $post_id";
        }
        $global_db = $shardb_prefix."global";
        
        $db_name = LP_get_blog_db_name($blog_id);
        $prefix = $wpdb->base_prefix;
		
		$defaul_meter = DEFAULT_METER;
        $sql = "SELECT 
					{$blog_id} as blog_id,
					dp.*, 
					dm.*, 
					dc.*, 
					dc.`name` as channel_name,
					pm.`meta_value` as collection_setup,
					dm.`meta_value` as channel_id, 
					IF(lpd.`short_name`<>'',lpd.`short_name`,CONCAT('Topic ',lpd.`temp_name`)) as short_name, 
					IF(tm.`meta_value`<>'',tm.`meta_value`,'$defaul_meter') as LP_topic_meter
			FROM `{$db_name}`.`{$prefix}{$blog_id}_posts` dp
			LEFT JOIN (SELECT * FROM `{$db_name}`.`{$prefix}{$blog_id}_postmeta` WHERE `meta_key`='LP_channel') dm on dm.`post_id` = dp.`ID`
            LEFT JOIN `{$global_db}`.`{$prefix}channels` dc on dc.`id`= dm.`meta_value`
			LEFT JOIN (SELECT * FROM `{$global_db}`.`{$prefix}lp_drips` WHERE `blog_id` = {$blog_id}) lpd on lpd.`post_id` = dp.`ID`
            LEFT JOIN (SELECT * FROM `{$db_name}`.`{$prefix}{$blog_id}_postmeta` as pt WHERE pt.`meta_key`='_collection_setup') pm on pm.`post_id`= dp.`ID`
			LEFT JOIN (SELECT * FROM `{$db_name}`.`{$prefix}{$blog_id}_postmeta` WHERE `meta_key`='_LP_topic_meter') tm on tm.`post_id` = dp.`ID`
            WHERE 
				dp.`post_type`='topic'
				AND dp.`post_status`='publish'
				$extra 
				ORDER BY dp.`post_date` DESC";
        // echo $sql;
        $topics = $wpdb->get_results($sql,ARRAY_A);
		foreach($topics as $key=>$topic){
			$topics[$key]["drip_stats"] = LP_get_topic_stats($topic["ID"], $blog_id);
			$topics[$key]["images"]["lp-topic-medium"] = LP_get_post_thumb_url($topic["ID"], $blog_id, "lp-topic-medium");
			$topics[$key]["post_fields"] = get_post_meta($topic["ID"]);
			
			if($deep){
				$history_drips = LP_fetch_user_future_drips($post_id, true);
				if($history_drips){
					$topics[$key]["history_drips"] = $history_drips[$post_id];
				}else{
					$topics[$key]["history_drips"] = "";
				}
				
				$future_drips = LP_fetch_user_future_drips($post_id);
				if($future_drips){
					$topics[$key]["future_drips"] = $future_drips[$post_id];
				}else{
					$topics[$key]["future_drips"] = "";
				}
				
				$topics[$key]["filter_article_url"] = LP_user_topics_filter_URLS($topic_id);
			}
			
		}
		
		restore_current_blog();
        return $topics;
    }else{
        return false;
    }
}

function LP_user_topics_filter_URLS($topic_id)
{
	global $wpdb, $shardb_prefix;
            
	$global_db = $shardb_prefix."global";
	$prefix = $wpdb->base_prefix;
	$blog_id = LP_get_user_blog_id();
	$db_name = LP_get_blog_db_name($blog_id);
	$sql = "SELECT ld.`story_URL`  FROM `{$global_db}`.`{$prefix}lp_drips` ld 
			LEFT JOIN `{$db_name}`.`{$prefix}posts` wp on wp.`ID` = ld.`post_id`
			WHERE 
				ld.`blog_id`={$blog_id} 
				AND ld.`post_type`='post' 
				AND wp.`post_parent` = $topic_id
			ORDER BY `id` LIMIT 0,100";
	$filter = $wpdb->get_results($sql,ARRAY_A);
	return $filter;
}

/* 
 * Returns the topic of the post 
 */
function LP_get_post_topic($post_id, $blog_id)
{
    global $wpdb;
    $db_name = LP_get_blog_db_name($blog_id);
    $prefix = $wpdb->base_prefix;
    
    $sql = "SELECT topic.* FROM `{$db_name}`.`{$prefix}{$blog_id}_posts` drip
            LEFT JOIN `{$db_name}`.`{$prefix}{$blog_id}_posts` topic on topic.`ID` = drip.`post_parent`
            WHERE drip.`ID` = $post_id";
    $topic = $wpdb->get_results($sql,ARRAY_A);
    return $topic[0];
}

function LP_get_post_story_URL($post_id, $blog_id)
{
    global $wpdb, $shardb_prefix;
    
    $global_db = $shardb_prefix."global";
    $db_name = LP_get_blog_db_name($blog_id);
    $prefix = $wpdb->base_prefix;
    
    $sql = "SELECT url.* FROM `{$db_name}`.`{$prefix}{$blog_id}_posts` drip
            LEFT JOIN `{$global_db}`.`{$prefix}lp_drips` url on url.`post_id` = drip.`ID`
            WHERE drip.`ID` = $post_id AND url.`blog_id` = $blog_id";
    // echo $sql;
    // die();
    $url = $wpdb->get_results($sql,ARRAY_A);
    return $url[0];
}

function LP_get_topic_channel($topic_id, $blog_id)
{
	global $wpdb, $shardb_prefix;

    $global_db = $shardb_prefix."global";
	$db_name = LP_get_blog_db_name($blog_id);
    $prefix = $wpdb->base_prefix;
	
	$sql = "SELECT * FROM `{$db_name}`.`{$prefix}{$blog_id}_postmeta` pb
			LEFT JOIN `{$global_db}`.`{$prefix}channels` pc on pc.`id` = pb.`meta_value`
			WHERE pb.`post_id`=$topic_id AND pb.`meta_key`='LP_channel' LIMIT 0,1";
	$channel = $wpdb->get_results($sql,ARRAY_A);
	return $channel[0];
}

/* 
 * Get the statics of a topic 
 * Returns array("ID" => 3, "buffered" => 12, "days" => 4, "drips" => 245)
 */
function LP_get_topic_stats($topic_id, $blog_id)
{
	// return array("ID" => 0, "buffered" => 0, "days" => 0, "drips" => 0);
	global $wpdb, $switched;
	$db_name = LP_get_blog_db_name($blog_id);
	$prefix = $wpdb->base_prefix;
	
	$sql = "SELECT a.`ID`,IFNULL(b.buffered,0)buffered, IFNULL(d.dripped,0)dripped
	FROM `{$db_name}`.`{$prefix}{$blog_id}_posts` a
	LEFT JOIN 
	(SELECT count(`ID`) buffered,`post_parent`  FROM `{$db_name}`.`{$prefix}{$blog_id}_posts` WHERE (`post_status`='draft' OR `post_status`='future')
	AND `post_type` = 'post'
	AND `post_parent` = $topic_id) as b on b.`post_parent` = a.`ID`
	LEFT JOIN 
	(SELECT count(`ID`) dripped,`post_parent`  FROM `{$db_name}`.`{$prefix}{$blog_id}_posts` WHERE 
	`post_status` = 'publish'
	AND `post_type` = 'post'
	AND `post_parent` = $topic_id) as d on d.`post_parent` = a.`ID`
	WHERE `ID` = $topic_id";
	
	$res = $wpdb->get_results($sql,ARRAY_A);
	
	if(count($res)==0){
		$stats = array("ID" => $topic_id,"buffered" => 0, "days" => 0, "drips" => 0);
	}else{
		$topic_meter = LP_get_topic_meter($topic_id,$blog_id);
		$drip_time = $topic_meter["drip_time"];
		$num = count($drip_time);
		$stats = $res[0];
		$stats["ID"] = $topic_id;
		$stats["days"] = ceil($stats["buffered"]/$num);
	}
	return $stats;
}

function LP_get_channel_topics($channel_id)
{
	global $wpdb, $shardb_prefix;
	$global_db = $shardb_prefix."global";
    $prefix = $wpdb->base_prefix;
	
	$sql = $wpdb->prepare("SELECT * FROM `{$global_db}`.`{$prefix}lp_drips` WHERE `channel_id`= %d AND `post_type`='topic' LIMIT 0, 20",$channel_id);
	$topic_list = $wpdb->get_results($sql,ARRAY_A);
	
	
	$grouped_blog = array();
	foreach($topic_list as $lpost){
		$grouped_blog[$lpost["blog_id"]] = $grouped_blog[$lpost["blog_id"]].",".$lpost["post_id"];
	}
	
	$sql = "SELECT * FROM (";
	$sep = "";
	foreach($grouped_blog as $blog_id => $topic_ids){
		$channel_id = $topic["channel_id"];
		$post_ids = $topic_ids;
		$db_name = LP_get_blog_db_name($blog_id);
		$sql.= $sep."SELECT dc.`blog_id`, dch.id channel_id, dch.`name`, dp.* FROM `{$global_db}`.`{$prefix}lp_drips` dc
				LEFT JOIN `{$db_name}`.`{$prefix}{$blog_id}_posts` dp on dp.`ID` = dc.`post_id`
				LEFT JOIN `{$global_db}`.`{$prefix}channels` dch on dch.`id` = dc.`channel_id`
				WHERE dc.`post_id` in (".trim($post_ids,",").") AND dc.`blog_id` = $blog_id";
		$sep = " UNION ALL ";
	}
	$sql.= ") as tall";
	// echo $sql;
	$channel_topics = $wpdb->get_results($sql,ARRAY_A);
	return $channel_topics;
}

/**
 * Retrieves the Channel details of the post/driplet
 *
 * @param int $post_id
 * @param int $blog_id
 */
function get_the_channel($post_id, $blog_id)
{
	global $wpdb, $shardb_prefix;
	$global_db = $shardb_prefix."global";
    $db_name = LP_get_blog_db_name($blog_id);
    $prefix = $wpdb->base_prefix;
    
    $post_type = get_post_type($post_id);
    if($post_type == "topic"){ 
        $sql = "SELECT * FROM `{$db_name}`.`{$prefix}{$blog_id}_postmeta` pm
                LEFT JOIN `$global_db`.`{$prefix}channels` ch on ch.`id` = pm.`meta_value`
                WHERE pm.`meta_key` = 'LP_channel' AND pm.`post_id`=$post_id
                ";
        $post_channel = $wpdb->get_results($sql,ARRAY_A);
    }elseif($post_type == "post"){
        $sql = "SELECT * FROM `{$global_db}`.`{$prefix}lp_drips` lp
                LEFT JOIN `{$global_db}`.`{$prefix}channels` ch on ch.`id` = lp.`channel_id`
                WHERE lp.`post_id` = $post_id AND lp.`blog_id`=$blog_id
                ";
        $post_channel = $wpdb->get_results($sql,ARRAY_A);
    }
	return $post_channel[0];
}


/**
 * Retrieves all the channels of a user
 *
 */
function LP_get_user_channels()
{
    global $LP_channel_settings;
	$member_chans = array();
	foreach($LP_channel_settings as $chan){
		if($chan["active"]== 1)
			$member_chans[]= $chan["id"];
	}
    return $member_chans;
}

/**
 * Retrieves all the Linkedin connections that the user selected from the message page.
 *
 * @param int $user_id
 * @param boolean $group - set to true if you want to group the connections by industry
 * @param string $ex_industry_name - define the industry yo want to be excluded.
 */
function LP_get_user_linkedin_connections($user_id = "", $group = true, $ex_industry_name = "")
{
    global $switched, $wpdb, $db_servers;
    if($user_id == "")
        return false;
    $blog_id = LP_get_user_blog_id($user_id);
    switch_to_blog($blog_id);
    $db_name = LP_get_blog_db_name($blog_id);
    $prefix =  $wpdb->prefix;
    restore_current_blog();
    $ext = "";
    if($ex_industry_name!=""){
        $ext = "AND `industry` !='".$ex_industry_name."'";
    }
    $sql = "SELECT `linkedin_id` id,`firstName`,`lastName`, `industry`, `messaging`, `sent_count` FROM `{$db_name}`.`{$prefix}linkedin_connections` WHERE `messaging` = 1 AND `sent_count` < 2 $ext";
    $my_connections = $wpdb->get_results($sql,ARRAY_A);
    $lin_connections = array();
    if($group){
        foreach($my_connections as $conn){
            $lin_connections[$conn["industry"]]["contacts"][] = $conn;
        }
    }else{
        $lin_connections["all"]["contacts"] = $my_connections;
    }
    return $lin_connections;
}

/**
 * Retrieves all users linkedin connections
 *
 * @param int $user_id
 * @param boolean $group - set to true if you want to group the connections by industry
 * @param mixed $num - the number of connections to be returned; empty to return all.
 * @param boolean $linkedin - set to true to retrieve connections from linkedin API, false to retrieve from database.
 */
function LP_get_user_linkedin_connections_from_linkedin($user_id = "",$group = true, $num = "", $linkedin = false){
    if(is_user_logged_in()){
        global $switched;
        if($user_id == "")
            $user_id = get_current_user_id();
        $blog_id = LP_get_user_blog_id($user_id);
        switch_to_blog($blog_id);
        if($linkedin == true){
            $user_info = get_blog_option($blog_id,"LP_linkedin_info");

            $API_CONFIG = json_decode(LINKEDIN_API_CONFIG,true);
            $OBJ_linkedin = new LinkedIn($API_CONFIG);
            $OBJ_linkedin->setTokenAccess($user_info["linkedin_TokenAccess"]);
            if(is_int($num))$count = "&count=$num";

            $connections = $OBJ_linkedin->connections("~/connections:(id,first-name,last-name,picture-url,industry)?format=json".$count);
            $myconnections = json_decode($connections["linkedin"]);
//            print_r($myconnections);
            $lin_connections = array();
            foreach($myconnections->values as $contact){
                if($group){
                    if($contact->industry){
                        $the_ind = $contact->industry;
                    }else{
                        $the_ind = "unspecified";
                    }
                    $lin_connections[$the_ind]["contacts"][] = array("firstName" => $contact->firstName, "lastName" => $contact->lastName, "id" => $contact->id, "industry" => $contact->industry);
                }else{
                    $lin_connections["contacts"][] = array("firstName" => $contact->firstName, "lastName" => $contact->lastName, "id" => $contact->id, "industry" => $contact->industry);
                }
            }
            $lin_connections["total"] = $myconnections->_total;

        }else{
            // in here $num is being used as the page number...
            global $switched, $wpdb, $db_servers;
            $items = 100;
            $blog_id = LP_get_user_blog_id();
            switch_to_blog($blog_id);
            $db_name = LP_get_blog_db_name($blog_id);
            $prefix =  $wpdb->prefix;
            restore_current_blog();
            $sql = "SELECT `linkedin_id` id,`firstName`,`lastName`, `industry`, `messaging`, `sent_count` FROM `{$db_name}`.`{$prefix}linkedin_connections` LIMIT ".($items * $num).",{$items}";
            $my_connections = $wpdb->get_results($sql,ARRAY_A);
            $lin_connections = array();
            if($group){
                foreach($my_connections as $conn){
                        $lin_connections[$conn["industry"]]["contacts"][] = $conn;
                }
            }else{
                $lin_connections["contacts"] = $my_connections;
            }
        }
        restore_current_blog();
        return $lin_connections;
    }else{
        return false;
    }
}

/**
 * Retrieves post from another blog.
 *
 * @param int $post_id
 * @param int $blog_id
 */
function LP_get_drip($post_id, $blog_id)
{
	global $wpdb;
    $db_name = LP_get_blog_db_name($blog_id);
    $prefix = $wpdb->base_prefix;
	
	$sql = $wpdb->prepare("SELECT * FROM `{$db_name}`.`{$prefix}{$blog_id}_posts` WHERE `ID`= %d",$post_id);
	$drip = $wpdb->get_results($sql,ARRAY_A);
	return $drip[0];
}

/**
 * Retrieves post parent, or the original drip being redripped
 *
 * @param int $post_id
 * @param int $blog_id
 */
function LP_get_drip_parent($post_id, $blog_id)
{
	global $wpdb, $shardb_prefix;
	$global_db = $shardb_prefix."global";
    $db_name = LP_get_blog_db_name($blog_id);
    $prefix = $wpdb->base_prefix;
	$sql = $wpdb->prepare("SELECT lpd1.`id` drip_id, lpd1.`story_URL` URL, lpd2.* FROM `{$global_db}`.`{$prefix}lp_drips` lpd1
						LEFT JOIN `{$global_db}`.`{$prefix}lp_drips` lpd2 on lpd2.`id` = lpd1.`parent`
						WHERE lpd1.`post_id` = %d AND lpd1.`blog_id` = %d", $post_id, $blog_id);
	// echo $sql."<br /><br />";
	$drip = $wpdb->get_results($sql,ARRAY_A);
	return $drip[0];
}

/**
 * Retrieves all the ripples of the drip from all blogs
 *
 * @param int $post_id
 * @param int $blog_id
 */
function LP_get_drip_ripples($post_id, $blog_id)
{
	global $wpdb, $shardb_prefix;
	$global_db = $shardb_prefix."global";
    $prefix = $wpdb->base_prefix;
	
	$parent = LP_get_drip_parent($post_id, $blog_id);
	if($parent["id"]){
		$parent_id = $parent["id"];
	}else{
		$parent_id = $parent["drip_id"];
	}

	$sql = $wpdb->prepare("	SELECT * FROM `{$global_db}`.`{$prefix}lp_drips` lpparent WHERE lpparent.`id` = %d
							UNION ALL
							SELECT * FROM `{$global_db}`.`{$prefix}lp_drips` lpripples WHERE lpripples.`parent` = %d
							",$parent_id, $parent_id);
	$drips = $wpdb->get_results($sql,ARRAY_A);
	$ripples = LP_get_blogs_posts($drips);
    // print_r($ripples);
	return $ripples;
}

/**
 * Retrienves all the comments from all blogs as ripples
 * @param int $post_id
 * @param int $blog_id
 */
function LP_get_comment_ripples($post_id, $blog_id)
{
	global $switched;
    $parent = LP_get_drip_parent($post_id, $blog_id);
    
    if($parent["post_id"]){
        $parent_post_id = $parent["post_id"];
		$parent_blog_id = $parent["blog_id"];
    }else{
        $parent_post_id = $post_id;
		$parent_blog_id = $blog_id;
    }
    switch_to_blog($parent_blog_id);
		$comments = get_comments("post_id={$parent_post_id}");
	restore_current_blog();
    return $comments;
}

/**
 * fetch all posts from with blog id and post id
 *
 * @param array $args is an array of post_id blog_id pair
 */
function LP_get_blogs_posts($args){
	global $wpdb, $shardb_prefix;
	$global_db = $shardb_prefix."global";
    // $db_name = LP_get_blog_db_name($blog_id);
    $prefix = $wpdb->base_prefix;
	
	$grouped_blog = array();
	foreach($args as $lpost){
		$grouped_blog[$lpost["blog_id"]] = $grouped_blog[$lpost["blog_id"]].",".$lpost["post_id"];
	}
	$sql = "SELECT * FROM (";
	$sep = "";
	foreach($grouped_blog as $blog_id => $posts){
		if($blog_id == 1){
				$wp_posts    = "{$prefix}posts";
				$wp_postmeta = "{$prefix}postmeta";
		}else{
			$wp_posts = $prefix.$blog_id."_posts";
			$wp_postmeta = $prefix.$blog_id."_postmeta";
		}

		$db_name = LP_get_blog_db_name($blog_id);
		$check_table = $wpdb->get_results("SELECT 1 as dip FROM information_schema.tables WHERE table_name = '{$prefix}posts' limit 1",ARRAY_A);
		if (isset($check_table) && $check_table[0]["dip"] == 1 ) {
			$sql.= $sep."SELECT 
							dp.*,
							$blog_id as blog_id, 
							pt.`post_title` topic_name, pt.`ID` topic_id, 
							dc.`name` channel_name, dc.`id` channel_id,
							dd.`story_URL`, CONCAT('".$LP_siteurl."/lp/0000',dd.`id`) cloaked_URL
						FROM `{$db_name}`.`{$wp_posts}` dp
						LEFT JOIN `{$db_name}`.`{$wp_posts}` pt ON pt.`ID` = dp.`post_parent`
						LEFT JOIN `{$global_db}`.`{$prefix}lp_drips` dd ON dd.`post_id` = dp.`id`
						LEFT JOIN `{$global_db}`.`{$prefix}channels` dc ON dc.`id` = dd.`channel_id`
						WHERE dp.`ID` in(". trim($posts,",") .") 
							AND dp.`post_type`='post' 
							AND (dp.`post_status`='publish' OR dp.`post_status`='private')
							AND dd.`blog_id` = $blog_id";
			$sep = " UNION ALL ";
		}
	}
	$sql.= ") as from_all ORDER BY post_date DESC";
	$posts = $wpdb->get_results($sql,ARRAY_A);
	return $posts;
}

/**
 * Calculates the age of the post
 *
 * @param timestamp $the_date
 * @param int $blog_id
 */
function LP_get_post_age($the_date,$blog_id)
{
	switch_to_blog($blog_id);
			$dnow = current_time( 'timestamp', 0 );
	restore_current_blog();
    $age    = ($dnow - $the_date) / 60;
	$plural = "";
    if($age >= 60){
        $age = $age/60;
        if($age >= 24){
            $age = $age/24;
            if($age >= 30){
                $age = $age/30;
                if($age >= 12){
					$age = floor($age/12);
					if($age>1)$plural = "s";
                    $post_age = $age." year{$plural} ago";
                }else{
					if(floor($age)>1)$plural = "s";
                    $post_age = floor($age)." month{$plural} ago";
                }
            }else{
				if(floor($age)>1)$plural = "s";
                $post_age = floor($age)." day{$plural} ago";
            }
        }else{
			if(floor($age)>1)$plural = "s";
            $post_age = floor($age)." hour{$plural} ago";
        }
    }else{
        if($age >= 1){
			if(floor($age)>1)$plural = "s";
            $post_age = floor($age)." minute{$plural} ago";
        }else{
			if(floor($dnow - $the_date)>1)$plural = "s";
            $post_age = ($dnow - $the_date)." second{$plural} ago";
        }
    }
    return $post_age;
}

/**
 * Get the url of the thumbnail of a post from another blog
 *
 * @param int $post_id
 * @param int $blog_id
 * @param string $size - the size of the thubmbnail defined by wordpress
 */
function LP_get_post_thumb_url($post_id, $blog_id, $size = "lp-medium")
{
    global $switched;
    if($blog_id)switch_to_blog($blog_id);
        if(has_post_thumbnail($post_id)){
            $the_thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), $size);
            $posts_thumb = $the_thumb[0];
        }else{
            $posts_thumb = 0;
        }
    if($blog_id)restore_current_blog();
    return $posts_thumb;
}

/**
 * filter the size of the image being upload via wordpress upoload
 *
 * @param resource $file
 * @param int $width - minimum allowed width
 * @param int $height - minimum allowed height
 */
add_filter('wp_handle_upload_prefilter','tc_handle_upload_prefilter');
function tc_handle_upload_prefilter($file, $width = 490, $height = 262)
{
    $img=getimagesize($file['tmp_name']);
	// This is the minimum requirements for drip thumbnails
    $minimum = array('width' => $width, 'height' => $height);
    $width= $img[0];
    $height =$img[1];

    if ($width < $minimum['width'] )
        return array("error"=>"Image dimensions are too small. Minimum width is {$minimum['width']}px. Uploaded image width is $width px");

    elseif ($height <  $minimum['height'])
        return array("error"=>"Image dimensions are too small. Minimum height is {$minimum['height']}px. Uploaded image height is $height px");
    else
        return $file; 
}

/**
 * Generates our custom user profile URL
 *
 * @param int $user_id
 */
function LP_get_user_url($user_id)
{
    global $current_site;
    $LP_siteurl = "http://".$current_site->domain;
    $the_user = get_userdata($user_id);
    return  $LP_siteurl."/in/".$the_user->user_nicename."/";
}

/**
 * Get the user avatar
 *
 * @param int $user_id
 * @param int $size
 *
 * It will try to use the users linkedin profile picture
 * and will use the users gravatar if linkedin is not available.
 */
function LP_get_user_avatar($user_id, $size = 80)
{
	global $current_site;
	$LP_siteurl = "http://".$current_site->domain;
	$default_avatar = $LP_siteurl."/wp-content/themes/LinkedPOST/images/author.png";
	$blog_id = LP_get_user_blog_id($user_id);
	$user_info = get_blog_option($blog_id,"LP_linkedin_info");
	$profile_pic = ($user_info["linkedin_profile_pic"] ? $user_info["linkedin_profile_pic"] : $user_info["linkedin_profile_thumb"]);
	
	$display_name = get_the_author_meta("display_name", $user_id);
	
	if($profile_pic){
		$author_avatar = "<img src=\"".$profile_pic."\" width=\"$size\" height=\"$size\" alt=\"$display_name\"/>";
	}else{
		$author_avatar = get_avatar($user_email, $size, $default_avatar);
	}
	return $author_avatar;
}

/**
 * Retrieves the post's recomended tags
 *
 * @param int $post_id
 * @param int $blog_id
 * @param string $type eg: top, recent
 * @aram int $number - the number of tags to be returned
 */
function LP_get_recommended_tags($post_id, $blog_id, $type = "top", $number = 5)
{
    if(is_user_logged_in()){
		global $wpdb;
		$prefix = $wpdb->base_prefix;
        $the_post = get_blog_post($blog_id,$post_id);
        
        $bid = LP_get_user_blog_id();
        $db_name = LP_get_blog_db_name($bid);
        
        $ORDER_BY = "";
        if($type == "recent"){
			$sql = "SELECT t.* FROM `{$db_name}`.`{$prefix}{$bid}_terms` t
                        LEFT JOIN `{$db_name}`.`{$prefix}{$bid}_term_taxonomy` tx on tx.`term_id` = t.`term_id`
                        WHERE 
                        tx.`taxonomy` = 'post_tag'
						ORDER BY t.`term_id` DESC
                        LIMIT 0, $number";
        }elseif($type = "top" ){
            $sql = "SELECT t.*, tx.`count` FROM `{$db_name}`.`{$prefix}{$bid}_terms` t
                    LEFT JOIN `{$db_name}`.`{$prefix}{$bid}_term_taxonomy` tx on tx.`term_id` = t.`term_id`
                    WHERE 
                    MATCH (t.`name`) AGAINST('".strip_tags($the_post->post_title)." ".strip_tags($the_post->post_content)."') 
                    AND tx.`taxonomy` = 'post_tag'
                    ORDER BY tx.`count` DESC LIMIT 0, $number";
        }
       
        // echo $sql;
        $tags = $wpdb->get_results($sql,ARRAY_A);
        // print_r($tags);
        // die();
        return $tags;
    }
}

/**
 * Generates the URL of the channel
 *
 * @param string $channel - the title of the channel
 */
function LP_channel_url($channel)
{
	global $current_site;
    $LP_siteurl = "http://".$current_site->domain;
    return  $LP_siteurl."/channel/".str_replace(" ","_",$channel)."/";
}

/**
 * Checks if the post has been redripped already
 *
 * @param int $post_id
 * @param int $blog_id
 * *
 * *
 * @return boolean - true if the drip already been redripped and false if not.
 */
function LP_is_has_redripped($post_id, $blog_id)
{
	if(is_user_logged_in()){
		global $wpdb, $shardb_prefix;
		$user_blog_id = LP_get_user_blog_id();
		$global_db = $shardb_prefix."global";
		$prefix = $wpdb->base_prefix;
		
		$parent = LP_get_drip_parent($post_id, $blog_id);
		if($parent["id"]){
			$parent_id = $parent["id"];
		}else{
			$parent_id = $parent["drip_id"];
		}
		
		$sql = "SELECT * FROM `{$global_db}`.`{$prefix}lp_drips` WHERE (`parent` = $parent_id OR `id` = $parent_id) AND `blog_id` = $user_blog_id";
		// echo $sql;
		$drip = $wpdb->get_results($sql,ARRAY_A);
		if(count($drip)>0){
			return true;
		}else{
			return false;
		}
	}else{
		return true;
	}
}

/**
 * This is for registering our custom post type "topic" for our topic
 */
add_action( 'init', 'create_post_type' );
function create_post_type() {
	register_post_type( 'topic',
		array(
			'labels' => array(
				'name' => __( 'Topics' ),
				'singular_name' => __( 'Topic' )
			),
		'public' => true,
		'has_archive' => true,
        'rewrite' => array('slug' => 'topic','with_front'=> false),
		)
	);
}


/**
 * Used for removing stop words from the string to be used as a keyword for searching.
 *
 * @param string $string
 * @param string $stop
 */
function extractCommonWords($string, $stop=""){
    $stopWords = array('i','a','about','an','and','are','as','at','be','by','com','de','en','for','from','how','in','is','it','la','of','on','or','that','the','this','to','was','what','when','where','who','will','with','und','the','www','like');
    
    $stopWords = array_merge($stopWords, $stop);
    $string = preg_replace('/\s\s+/i', '', $string); // replace whitespace
    $string = trim($string); // trim the string
    $string = preg_replace('/[^a-zA-Z0-9 -]/', '', $string); // only take alphanumerical characters, but keep the spaces and dashes too…
    $string = strtolower($string); // make it lowercase

    preg_match_all('/\b.*?\b/i', $string, $matchWords);
    $matchWords = $matchWords[0];

    foreach ( $matchWords as $key=>$item ) {
      if ( $item == '' || in_array(strtolower($item), $stopWords) || strlen($item) <= 3 ) {
          unset($matchWords[$key]);
      }
    }   
    $wordCountArr = array();
    if ( is_array($matchWords) ) {
      foreach ( $matchWords as $key => $val ) {
          $val = strtolower($val);
          if ( isset($wordCountArr[$val]) ) {
              $wordCountArr[$val]++;
          } else {
              $wordCountArr[$val] = 1;
          }
      }
    }
    arsort($wordCountArr);
    $wordCountArr = array_slice($wordCountArr, 0, 10);
    return $wordCountArr;
}

/* GETTING OPTIONS */

/**
 * This will return the default custom blog options if the option is not found in database
 *
 * @param int $blog_id
 * @param string $option
 *
 * @return mixed - the blog option;
 */
function LP_get_blog_option($blog_id, $option){

    //Retrieves the default blog option;
    eval('$opt='.$option.';');
    return get_blog_option($blog_id, $option, $opt);
}

/**
 * Retrieves the blog option of the user
 *
 * @param string $option
 * @param mixed $user_id; set to false if you want to process with the current loggedin users' id
 */
function LP_get_user_blog_option($option, $user_id = false){
    $blog_id = LP_get_user_blog_id($user_id);
    if($blog_id){
        return LP_get_blog_option($blog_id, $option);
    }else{
        return false;
    }
}


function remove_save_actions(){
	remove_action('save_post', 'LP_new_drip');
	remove_action('save_post', 'my_meta_save');
}

function attach_save_actions(){
	add_action('save_post', 'LP_new_drip');
	add_action('save_post', 'my_meta_save');
}

/**
 * Generates the directory URL of the images on the flip
 *
 * @param int @blog_id
 */
function lp_flip_dir($blog_id = 0){
	global $current_site;
	if($blog_id == 0){
		$blog_id = LP_get_user_blog_id();
	}
	return "http://".$current_site->domain."/wp-content/plugins/LinkedPost/flip_imgs/u_".$blog_id;
}

/**
 * Generates the short ur for the article URL/ story URL of the drip
 *
 * @param int $storyURL_id
 */
function LP_generate_short_drip_url($storyURL_id)
{
    $short_url = base_convert($storyURL_id,10,36);
    if(strlen($short_url) < 6){
        $zeros = "000000";
        $short_url = substr($zeros,0,(6 - strlen($short_url))).$short_url;
    }
    return "http://www.drippost.com/".$short_url;
}

function LP_blog_has_messaging()
{
    if(is_user_logged_in()){
        global $switched, $wpdb, $db_servers;
        $blog_id = LP_get_user_blog_id();
        switch_to_blog($blog_id);
        $db_name = LP_get_blog_db_name($blog_id);
        $prefix =  $wpdb->prefix;
        restore_current_blog();
        $sql = "SELECT COUNT(*)as c FROM information_schema.tables WHERE table_schema = '{$db_name}' AND table_name = '{$prefix}linkedin_connections';";
        $res = $wpdb->get_var($sql);
        if($res == 0)return false;
        else return true;
    }else{return false;}
}
?>