<?php
/*
Plugin Name: Lenkpos
*/
global $LP_user_drip_settings;


add_filter('pre_get_posts','LP_searchfilter');
function LP_searchfilter($query) {

    if (is_search() && !is_admin() ) {
        $query->set('post_type',array('post'));
    }
    return $query;
}

add_filter( 'post_limits', 'LP_post_limits', 0);
function LP_post_limits( $limit ) {
    if (is_search() && !is_admin() ) {
        $temp 			= str_replace("LIMIT", "", $limit);
        $temp 			= explode(",", $temp);
        return "LIMIT {$temp[0]}, 25";
    }
    return $limit;
}


add_filter('the_posts','LP_search_dripplets',100000);
function LP_search_dripplets($results){
    if(is_search() && get_current_blog_id()==1){
        $res = array();
        $a = 0;
        foreach($results as $result){
            if($result->post_type == "post"){
                $result->post_thumbnail = LP_get_post_thumb_url($result->ID, $result->blog_id,"lp-large");
                $story_URL = LP_get_post_story_URL($result->ID, $result->blog_id);
                $result->story_URL = $story_URL["story_URL"];
                $res[$a] = $result;
                $a++;
            }
        }
        echo json_encode($res);
        die();
    }
}

add_action("init", "LinkedPost_js_init");
function LinkedPost_js_init()
{
    if(!session_start()) {
        throw new LinkedInException('This script requires session support, which appears to be disabled according to session_start().');
    }

    // wp_register_style( 'LP-style', plugins_url('/style.css', __FILE__) );
    // wp_enqueue_style( 'LP-style' );
    if(is_user_logged_in()){
        $user_ID    = get_current_user_id(); 
        $user_meta  = get_user_meta($user_ID);
        // LP_drip_settings($user_meta);
        // LP_adjust_assign_schedules();
        LP_channel_settings(true);
    }
}

add_action( 'wp_enqueue_scripts', 'LP_enqueue_all_scritps' );
function LP_enqueue_all_scritps() 
{
    global $current_site;
    global $LP_user_drip_settings, $switched;
    
    $user_info = get_userdata(get_current_user_id());
	switch_to_blog(1);
		$admin_url = admin_url( 'admin-ajax.php' );
	restore_current_blog();
	
	$js_global = array( 
            'ajaxurl'           => $admin_url, 
            'drip_settings'     => $LP_user_drip_settings, 
            "user_nicename"     => $user_info->user_nicename, 
            "user_display_name" => $user_info->display_name, 
            "site_url"          => trim(network_site_url(),"/")."/", 
            "is_forms"          => is_user_logged_in(), 
            "is_home"           => is_home(),
            "LP_blog_has_messaging" => LP_blog_has_messaging(),
            );
	
	if(is_user_logged_in()){
		$LP_siteurl = trim(network_site_url(),"/");
		$default_avatar = $LP_siteurl."/wp-content/themes/LinkedPOST/images/author.png";
		$blog_info = get_blog_option(LP_get_user_blog_id(),"LP_linkedin_info");
		$profile_pic = ($blog_info["linkedin_profile_pic"] ? $blog_info["linkedin_profile_pic"] : $blog_info["linkedin_profile_thumb"]);
		if($profile_pic){
			$author_avatar = "<img src=\"".$profile_pic."\" width=\"80\" height=\"80\"/>";
		}else{
			$author_avatar = get_avatar($user_email, 72, $default_avatar);
		}
		
		$js_global["user_avatar"] = $author_avatar;
		$js_global["current_user"] = get_current_user_id();
		$js_global["time_zone"] = get_blog_option(LP_get_user_blog_id(), "timezone_string");
	}
	// $minified = true;
	if($minified === true){
		wp_enqueue_script('LinkedPost_js', plugins_url('/LinkedPost/script.min.js'), array('jquery') );
	}else{
		wp_enqueue_script('LinkedPost_js', plugins_url('/LinkedPost/script.js'), array('jquery') );
		wp_enqueue_script('LinkedPost_js2', plugins_url('../themes/linkedpost/js/jquery-ui-1.10.3.custom.min.js'));
	}
    wp_localize_script('LinkedPost_js', 'linkedIn_AJAX', $js_global);
}

/**
 * This is where all the redirections take place
 */
add_action( 'template_redirect', 'LinkedPost_handle' );
function LinkedPost_handle($content){
    $uri = explode("/",trim($_SERVER["REQUEST_URI"],"/"));
	$LP_siteurl = trim(network_site_url(),"/");

    if(count($uri) == 1){
        //Checking Shortened URL to be redirected...
        global $wpdb, $shardb_prefix;
        $global_db = $shardb_prefix."global";
        $prefix = $wpdb->base_prefix;

        $pid = base_convert(ltrim($uri[0],"0"),36,10);
        $sql = $wpdb->prepare("SELECT * FROM `{$global_db}`.`{$prefix}lp_drips` WHERE `id`=%d", $pid);

        $drip = $wpdb->get_results($sql, ARRAY_A);
        if(count($drip) > 0){
            set_not_404();
            wp_redirect( $drip[0]["story_URL"], 301 );
            die();
        }
    }

    // $template_dir = get_template_directory();
    $template_dir = "wp-content/themes/linkedpost/";
    if(is_home() && get_current_blog_id()==1){
        // load_template(trim(get_template_directory(),"/")."/home-main.php");
        load_template($template_dir."home-main.php");
        die();
    }elseif(is_home() && get_current_blog_id()!=1){
        header ('HTTP/1.1 301 Moved Permanently');
        header ('Location: /in/'.end($uri));
        die();
    }
    
	if (!is_404()){
        return $content;
	}

	if(is_404() && (end($uri)=="linkedInP" || prev($uri)=="linkedInP")){
        set_not_404();
		do_linkedin_log();
		die();
	}elseif(is_404() && (end($uri)=="drip" || end($uri)=="tile" || end($uri)=="mash" || end($uri)=="list" || end($uri)=="iframe" || end($uri)=="icons")){
        set_not_404();
        if(end($uri)=="iframe"){
            load_template($template_dir."iframe.php");
            die();
        }
		
        global $test_view_type;
        $test_view_type = end($uri);
		load_template($template_dir."home-main.php");
        die();
	}elseif(is_404() && (end($uri)=="llogin" || prev($uri)=="llogin")){
        set_not_404();
        do_llogin();
		die();
    }elseif(is_404() && $uri[0]=="LP_drip"){
        set_not_404();
        LP_proccess_uploads();
        die();
    }elseif(is_404() && ($uri[0]=="in" || $uri[0]=="fb" || $uri[0]=="tw")){
        global $wpdb, $switched, $shardb_prefix, $user_id;

        $global_db = $shardb_prefix."global";
        set_not_404();
        $prefix = $wpdb->base_prefix;

        $user = $wpdb->get_results("SELECT * FROM `$global_db`.`{$prefix}users` WHERE `user_nicename`='".trim($uri[1])."'",ARRAY_A);
        $user_id = $user[0]["ID"];
        // echo "U : ".$user_id."<br />";
        $blog_id = LP_get_user_blog_id($user_id);

        switch_to_blog($blog_id);
            if(count($uri)==3){
                // echo "topics";
                load_template($template_dir."topics.php");
            }elseif(count($uri)==2){
                load_template($template_dir."in.php");
            }
        restore_current_blog();
        die();
    }elseif(is_404() && $uri[0]=="LP_remote_post"){
        set_not_404();
        LP_remote_post();
        die();   
    }elseif(is_404() && $uri[0]=="LP_update_topic_thumb"){
		set_not_404();
		$args = $_POST;
		$args["new_topic_image"] = $_FILES["new_topic_image"];
        LP_update_topic_thumb($args);
        die();   
    }elseif(is_404() && $uri[0]=="lp_add_topic_thumb"){
        set_not_404();
        LP_upload_img_to_temp();
        die();   
    }elseif(is_404() && $uri[0]=="lp_update_current_topic"){
		set_not_404();
		if(isset($_POST["topic"]) && isset($_POST["current_topic_title"]) && isset($_POST["current_topic_content"])){
			if(isset($_FILES["current_topic_image"]["tmp_name"]) && $_FILES["current_topic_image"]["tmp_name"] !=""){
				$args = $_POST;
				$args["new_topic_image"] = $_FILES["current_topic_image"];
				LP_update_topic_thumb($args);
			}
			LP_udpate_post($_POST["topic"], $_POST["current_topic_content"], $_POST["current_topic_title"]);
			echo "<script>parent.LP_refresh_topic(".$_POST["topic"].");</script>";
		}
		
		die();
    }elseif(is_404() && $uri[0]=="lp"){
        global $wpdb, $switched, $shardb_prefix, $story_URL,$the_post_id;
        $global_db = $shardb_prefix."global";
        $prefix = $wpdb->base_prefix;
        $cloaked = $uri[1];
        $res = $wpdb->get_results("SELECT * FROM `{$global_db}`.`{$prefix}lp_drips` WHERE `id`=".intval($cloaked),ARRAY_A);
        if(count($res)>0){
            set_not_404();
			$blog_id   = $res[0]["blog_id"];
			$the_post_id   = $res[0]["post_id"];
            $story_URL = $res[0]["story_URL"];
			switch_to_blog($blog_id);
				load_template($template_dir."iframe.php");
			restore_current_blog();
        }
        die();
    }elseif(is_404() && $uri[0]=="channel"){
		global $wpdb, $shardb_prefix, $channel_id, $post_channel;
		$global_db = $shardb_prefix."global";
        $prefix = $wpdb->base_prefix;
		$channel = str_replace("_"," ",$uri[1]);
		
		$res = $wpdb->get_results("SELECT * FROM `{$global_db}`.`{$prefix}channels` WHERE `name`='{$channel}'",ARRAY_A);
		if(count($res)>0){
            set_not_404();	
			$channel_id = $res[0]["id"];
			$post_channel = $res[0];
			load_template($template_dir."channels.php");
		}
        die(); 
    }elseif(is_404() && $uri[0]=="facebook"){
		set_not_404();
		if($uri[1]=="ripple_oauth"){
			global $current_site;
			$uri2 = reset(explode("?",$uri[2]));
			LP_facebook_aouth($LP_siteurl."/facebook/FB_CB/".$uri2);
		}elseif($uri[1]=="FB_CB"){
			$uri2 = reset(explode("?",$uri[2]));
			LP_ripple_FB_CB($uri2);
		}
		die();
    }elseif(is_404() && $uri[0]=="twitter"){
		set_not_404();
		if($uri[1]=="aouth"){
			LP_twitter_aouth();
		}elseif($uri[1]=="CB"){
			LP_twitter_CB();
		}elseif($uri[1]=="ripple_oauth"){
			global $current_site;
			$uri2 = reset(explode("?",$uri[2]));
			LP_twitter_aouth($LP_siteurl."/twitter/TW_CB/".$uri2);
        }elseif($uri[1]=="TW_CB"){
            $uri2 = reset(explode("?",$uri[2]));
            LP_ripple_TW_CB($uri2);
        }elseif($uri[1] == "LP_tw_login"){
            set_not_404();
            LP_tw_login();
        }elseif($uri[1]=="testpost"){
			global $siwtched;
			$blog_id = LP_get_user_blog_id();
			switch_to_blog($blog_id);
			LP_ripple_this_drip($_GET["pid"]);
			restore_current_blog();
		}
		die();
    }elseif(is_404() && $uri[0]=="lin_groups"){
		set_not_404();
        global $siwtched;
        switch_to_blog(49);
        LP_send_message_to_lin_industry(291, 52);
        restore_current_blog();
		die();
	}elseif(is_404() && $uri[0]=="LP_save_drip_iframe"){
		if(is_user_logged_in()){
			set_not_404();
			LP_save_fresh_drip();
		}
		die();
	}elseif(is_404() && $uri[0]=="crop"){
		set_not_404();
		$plugin_dir_path = plugin_dir_path(__FILE__);
		$npath = $plugin_dir_path."sample.png";
		echo "file : ".$npath;
		$image_p = LP_resize_crop_image($npath, $to_width = 400, $to_heigth = 500, $focal_point = "1/12");
		var_dump(imagepng($image_p, $plugin_dir_path.'/re-sample-1-12.png'));
		echo "1/12 \n\r";
		
		$image_p = LP_resize_crop_image($npath, $to_width = 300, $to_heigth = 400, $focal_point = "5/1");
		var_dump(imagepng($image_p, $plugin_dir_path.'/re-sample-5-1.png'));
		echo "5/1 \n\r";
		
		$image_p = LP_resize_crop_image($npath, $to_width = 200, $to_heigth = 300, $focal_point = "5/8");
		var_dump(imagepng($image_p, $plugin_dir_path.'/re-sample-5-8.png'));
		echo "5/8 \n\r";
		
		$image_p = LP_resize_crop_image($npath, $to_width = 50, $to_heigth = 100, $focal_point = "12/12");
		var_dump(imagepng($image_p, $plugin_dir_path.'/re-sample-12-12.png'));
		echo "12/12 \n\r";
		
		$image_p = LP_resize_crop_image($npath, $to_width = 400, $to_heigth = 100, $focal_point = "5/10");
		var_dump(imagepng($image_p, $plugin_dir_path.'/re-sample-l-5-8.png'));
		echo "5/8 \n\r";
		die();
    }elseif(is_404() && $uri[0]=="testing"){
        set_not_404();
        testing();
    }elseif(is_404() && $uri[0]=="LP_msg"){
        set_not_404();
        LP_daily_linkedin_messaging();
    }else{
		LP_process_url();
	}
	
}

/**
 * Set the global and SESSION variable for the blog Channels
 *
 * @param boolean $forece : set to true to overwrite global variable and seesion.
 */
function LP_channel_settings($force = false){
	if(is_user_logged_in()){
		global $LP_channel_settings;
		
        $blog_id = LP_get_user_blog_id();
        // echo $blog_id."\n\r";
		if(isset($_SESSION["LP_channel_settings"]) && $_SESSION["LP_channel_settings"]!="" && !$force){
			$LP_channel_settings = maybe_unserialize($_SESSION["LP_channel_settings"]);
            // echo "I am here<br />";
		}else{
            // echo "Else I am here<br />";
            $chan_settings = get_blog_option($blog_id, "LP_channel_settings");
            if(!$chan_settings){
                // echo "I am Forced here<br />";
				global $wpdb, $db_servers, $shardb_prefix;
				$global_db = $shardb_prefix."global";
				$prefix = $wpdb->base_prefix;
				$sql = "SELECT * FROM `{$global_db}`.`{$prefix}channels`";
				$channels = $wpdb->get_results($sql,ARRAY_A);
				$new_chan = array();
				foreach($channels as $channel){
					$new_chan[$channel["name"]] = array("active" => 0, "label" => $channel["label"],"id" => $channel["id"]);
				}
				$new_chan["buzz"]["active"] = 1;
				$new_chan["editor picks"]["active"] = 1;
				$_SESSION["LP_channel_settings"] = $new_chan;
                $LP_channel_settings = $_SESSION["LP_channel_settings"];
                // update_usermeta( $user_ID, "LP_channel_settings", $LP_channel_settings );
                // echo "Adding blog options<br />";
                update_blog_option($blog_id, "LP_channel_settings", $LP_channel_settings);
            }else{
                $_SESSION["LP_channel_settings"] = maybe_unserialize($chan_settings);
                $LP_channel_settings = $_SESSION["LP_channel_settings"];
            }
		}
	}
}

/**
 * Enables/Activates the Channel for this blog
 */
add_action('wp_ajax_LP_toggle_follow', 'LP_toggle_follow');
function LP_toggle_follow()
{
	if(is_user_logged_in()){
		global $LP_channel_settings;
		$user_ID = get_current_user_id(); 
		$the_active = ($LP_channel_settings[$_POST["channel"]]["active"] == 1 ? 0:1);
	
		// unset($LP_channel_settings[$POST["channel"]]);

		$LP_channel_settings[$_POST["channel"]]["active"] = $the_active;
		// print_r($_POST);
		LP_sort_channel_settings($_POST["channel"]);
		// update_usermeta( $user_ID, "LP_channel_settings", $LP_channel_settings );
        $blog_id = LP_get_user_blog_id();
        update_blog_option($blog_id, "LP_channel_settings", $LP_channel_settings);
        $_SESSION["LP_channel_settings"] = $LP_channel_settings;
        $toret["blog_id"] = $blog_id;
		$toret["LP_channel_settings"] = $LP_channel_settings;
		$toret["post"] = $_POST;
		echo json_encode($toret);
	}
	die();
}

/**
 * Updates the sorting of channels from the Channel page
 */

add_action('wp_ajax_LP_udpate_sort_channels', 'LP_udpate_sort_channels');
function LP_udpate_sort_channels()
{

    if(is_user_logged_in()){
        global $LP_channel_settings;
        $channels = explode(",",$_POST["channels"]);
        $new_channels = array();
        foreach($channels as $chan){
            $key = trim($chan);
            $new_channels[$key] = $LP_channel_settings[$key];
        }
		$not_chan = array_diff($new_channels,$LP_channel_settings);
		if(count($not_chan)>0){
			foreach($not_chan as $nkey => $nchan){
				unset($new_channels[$nkey]);
			}
		}
		
		$in_chan = array_diff($LP_channel_settings,$new_channels);
		if(count($in_chan)>0){
			foreach($in_chan as $ikey => $ichan){
				$new_channels[$ikey] = $ichan;
			}
		}
		
		$LP_channel_settings = $new_channels;
		$blog_id = LP_get_user_blog_id();
		update_blog_option($blog_id, "LP_channel_settings", $LP_channel_settings);
		$_SESSION["LP_channel_settings"] = $LP_channel_settings;
		echo json_encode($LP_channel_settings);
	}
    die();
}

/**
 * Sorts the Blog's Channels and moce the newly activated Channel to first
 *
 * @param string $channel - channel name
 */
function LP_sort_channel_settings($channel)
{
	global $LP_channel_settings;
	if($channel){
		$the_active = $LP_channel_settings[$channel]["active"];
		$the_channel = $LP_channel_settings[$channel];
		unset($LP_channel_settings[$channel]);
	}
	// echo "channel : ".$channel."\n\r";
	// echo "active : ".$the_active."\n\r";
	$to_settings = array();
	$has_added = false;
	foreach($LP_channel_settings as $key => $chan){
		if($channel && $the_active == 1){
			$to_settings[$channel] = $the_channel;
			// $to_settings[$channel]["id"] = $LP_channel_settings[$channel]["id"];
		}elseif($channel && $the_active == 0 && $has_added == false && $chan["active"] == 0){
			$to_settings[$channel] = $the_channel;
			// $to_settings[$channel]["id"] = $LP_channel_settings[$channel]["id"];
			$has_added = true;
		}
		$to_settings[$key] = $chan;
		// $to_settings[$key]["id"] = $LP_channel_settings[$key]["id"];
	}
	
	if($has_added === false){
		$to_settings[$channel] = $the_channel;
	}
	// print_r($to_settings);
	$LP_channel_settings = $to_settings;
	
}

function sort_drip_time( $b, $a ) {
    return strtotime($b) - strtotime($a);
}

function sort_LP_drip_time($drip_time){
    usort($drip_time, "sort_drip_time");
    $to_return = array();
    foreach($drip_time as $dtime){
        $to_return[$dtime] = $dtime;
    }
    return $to_return;
}

function oauth_session_exists() 
{
  if((is_array($_SESSION)) && (array_key_exists('oauth', $_SESSION))) {
    return TRUE;
  } else {
    return FALSE;
  }
}

function LP_process_url(){
	global $post;
	// drip URL : {blog_path}/c/{channel}/t/{topic}/d/{drip slug}/
	$uri = explode("/",trim($_SERVER["REQUEST_URI"],"/"));
	$blog_id = get_current_blog_id();
	$blog_details = get_blog_details(get_current_blog_id(),"path");
	$path = $blog_details->path;
	if(trim($path,"/") == $uri[0]){
		if($uri[1] == "c" && $uri[3] == "t" && $uri[5] == "d"){
			if(count($uri) == 7){
				$c = $uri[2];
				$t = $uri[4];
				$the_slug = $uri[6];
				$args=array(
				  'name' => $the_slug,
				  'post_type' => 'post',
				  'post_status' => 'publish',
				  'posts_per_page' => 1
				);
				query_posts($args);
				print_r($Query_posts);
				if( have_posts() ) {
				// while ( have_posts() ) : the_post();
					// the_title();
					// echo "<br />";
					
					// echo $post->post_name;
					// echo "<br /><br /><br />";
				// endwhile;
					$drip_id    = get_the_ID();
					$topic      = LP_get_post_topic($drip_id, $blog_id);
					$channel 	= LP_get_topic_channel($topic["ID"], $blog_id);
					$the_drip	= $post->post_name;
					$the_topic 	= sanitize_title($topic["post_title"]);
					$the_channel= sanitize_title($channel["name"]);
					// echo '$the_channel : '.$the_channel." == ".$c."<br />";
					// echo '$the_topic : '.$the_topic." == ".$t."<br />";
					// print_r($the_slug);
					// echo '$drip : '.$the_drip." == ".$the_slug."<br />";
					if($the_topic == $t && $the_channel == $c && $the_drip == $the_slug){
						set_not_404();
						$template_dir = get_template_directory();
						load_template($template_dir."/drip.php");
						die();
					}else{
						wp_reset_query();
						return;
					}
				}else{
					wp_reset_query();
					return;
				}
			}
		}
	}
}

function LP_get_drip_url($drip){
	global $current_site;
	$base_url = trim(network_site_url(),"/");
	$topic_name = sanitize_title($drip->topic_name);
	$channel_name = sanitize_title($drip->channel_name);
	$drip_name = sanitize_title($drip->post_title);
	$drip_url = $base_url."c/".$channel_name."/t/".$topic_name."/d/".$drip_name."/";
	return $drip_url;
}

function set_not_404()
{
    status_header(200);
    global $wp_query;
    // set status of 404 to false
    $wp_query->is_404 = false;
    $wp_query->is_archive = true;
}

function do_llogin()
{
    $rtype = $_GET["rtype"];
    if($rtype == "register" || $_POST['rtype'] == "register"){
        $userdata = array(
            "user_login"    =>  $_POST["email"],
            "user_pass "    =>  $_POST["upass"],
            "user_email"    =>  $_POST["email"],
            "first_name"    =>  $_POST["fname"],
            "last_name"     =>  $_POST["lname"],
            "role"          =>  "author",
        );
        $user_id = _insert_user($userdata);
        if($user_id){
            wp_update_user( array ( 'ID' => $user_id, 'user_pass' =>  $_POST["upass"] ) ) ;
            $meta_key = "account_type";
            $meta_value = $_POST["acctype"];
            add_user_meta( $user_id, $meta_key, $meta_value, true );
                        
            $creds = array();
            $creds['user_login'] = $_POST["email"];
            $creds['user_password'] = $_POST["upass"];
            $creds['remember'] = true;
            $user = wp_signon( $creds, false );
            if ( is_wp_error($user) ){
                echo $user->get_error_message();
            }else{
                if($_POST["is_ajax"]=="1"){
                    echo 1;
                    die();
                }else{
                    header('Location: ' . get_option('siteurl')."/thank-you/");
                }
            }
        }
    }elseif($rtype == "login"){
        $creds = array();
        $creds['user_login'] = $_POST["wp_ulogin"];
        $creds['user_password'] = $_POST["wp_upass"];
        $creds['remember'] = true;
        $user = wp_signon( $creds, false );
        if ( is_wp_error($user) ){
            echo $user->get_error_message();
        }else{
            // echo "good";
            header('Location: ' . get_option('siteurl')."/welcome/");
        }
    }elseif($rtype == "logout"){
        global $current_site;
        $user_info = get_userdata(get_current_user_id());
        $redirect =  trim(network_site_url(),"/")."/in/".$user_info->user_nicename."/";
        wp_logout();
        header('Location: ' . $redirect);
    }
}

function do_linkedin_log()
{
	try {
	  // include the LinkedIn class
	  require_once('linkedin/linkedin_3.2.0.class.php');
	  
	  // start the session
	  if(!session_start()) {
		throw new LinkedInException('This script requires session support, which appears to be disabled according to session_start().');
	  }
	  $API_CONFIG = json_decode(LINKEDIN_API_CONFIG,true);    

	  // set index
	  $_REQUEST[LINKEDIN::_GET_TYPE] = (isset($_REQUEST[LINKEDIN::_GET_TYPE])) ? $_REQUEST[LINKEDIN::_GET_TYPE] : '';
      // echo "ltype : ".LINKEDIN::_GET_TYPE .$_REQUEST[LINKEDIN::_GET_TYPE];
      // die();
	  switch($_REQUEST[LINKEDIN::_GET_TYPE]) {
		case 'initiate':

		  /**
		   * Handle user initiated LinkedIn connection, create the LinkedIn object.
		   */
			
		  // check for the correct http protocol (i.e. is this script being served via http or https)
		  if($_SERVER['HTTPS'] == 'on') {
			$protocol = 'https';
		  } else {
			$protocol = 'http';
		  }
		  
		  // set the callback url
		  $API_CONFIG['callbackUrl'] = get_option('siteurl')."/linkedInP/?". LINKEDIN::_GET_TYPE . '=initiate&' . LINKEDIN::_GET_RESPONSE . '=1&rtype='.$_GET['rtype'];
		  $OBJ_linkedin = new LinkedIn($API_CONFIG);
		  
		  // check for response from LinkedIn
		  $_GET[LINKEDIN::_GET_RESPONSE] = (isset($_GET[LINKEDIN::_GET_RESPONSE])) ? $_GET[LINKEDIN::_GET_RESPONSE] : '';
		  if(!$_GET[LINKEDIN::_GET_RESPONSE]) {
			// LinkedIn hasn't sent us a response, the user is initiating the connection
			
			// Set API SCOPE
			// $OBJ_linkedin->setScope("r_fullprofile%20r_emailaddress");
            $OBJ_linkedin->setScope("r_basicprofile%20r_emailaddress%20r_network%20rw_nus%20r_fullprofile%20w_messages%20rw_groups");
            // send a request for a LinkedIn access token
			$response = $OBJ_linkedin->retrieveTokenRequest();
			if($response['success'] === TRUE) {
			  // store the request token
			  $_SESSION['oauth']['linkedin']['request'] = $response['linkedin'];

			  // redirect the user to the LinkedIn authentication/authorisation page to initiate validation.
			  header('Location: ' . LINKEDIN::_URL_AUTH . $response['linkedin']['oauth_token']);
			} else {
			  // bad token request
			  echo "Request token retrieval failed:<br /><br />RESPONSE:<br /><br /><pre>" . print_r($response, TRUE) . "</pre><br /><br />LINKEDIN OBJ:<br /><br /><pre>" . print_r($OBJ_linkedin, TRUE) . "</pre>";
			}
		  } else {
			// LinkedIn has sent a response, user has granted permission, take the temp access token, the user's secret and the verifier to request the user's real secret key
			$response = $OBJ_linkedin->retrieveTokenAccess($_SESSION['oauth']['linkedin']['request']['oauth_token'], $_SESSION['oauth']['linkedin']['request']['oauth_token_secret'], $_GET['oauth_verifier']);
            if($response['success'] === TRUE) {

                // the request went through without an error, gather user's 'access' tokens
                $_SESSION['oauth']['linkedin']['access'] = $response['linkedin'];

                // set the user as authorized for future quick reference
                $_SESSION['oauth']['linkedin']['authorized'] = TRUE;
                $_SESSION["tempuname"]    = $_POST["uname"];
                $profile = $OBJ_linkedin->profile('~:(id,first-name,last-name,email-address,picture-url,picture-urls::(original))?format=json');
                $temp = json_decode($profile["linkedin"]);

                if($profile['success'] === TRUE) {
                     $args = array(
                        'meta_key'     => 'linkedin_uid',
                        'meta_value'   => $temp->id
                    );
                    $the_user = get_users($args);
                    // Check is linkedin profile is not yet connected to us.
                    if (count($the_user) == 0){
                        // echo "<html><head><script>opener.linkedin_callback_".$_GET["rtype"]."(".$profile["linkedin"].");</script></head><body></body></html>";
                        // Create an account to us with the users Linkedin Profile.
                        LP_create_account();
                        die();
                    }else{
                    // Else Linkedin account is alraedy connected to us
                        // Force update user Linkedin information to us
                        if($_GET["rtype"] == "force_update"){
                            header('Location: ' . get_option('siteurl')."/linkedInP/?lType=default&rtype=force_update&CB=1");
                            die();
                        }else{
                        // Else login the user.
                            // echo "<html><head><script>opener.linkedin_callback_login(\"login\");</script></head><body></body></html>";
                            LP_login_user_by_LinkedIn($temp->id,true);
                            die();
                        }
                    }
                }
            } else {
                // bad token access
                echo "Access token retrieval failed:<br /><br />RESPONSE:<br /><br /><pre>" . print_r($response, TRUE) . "</pre><br /><br />LINKEDIN OBJ:<br /><br /><pre>" . print_r($OBJ_linkedin, TRUE) . "</pre>";
            }
		  }
		  break;

		case 'revoke':
		  /**
		   * Handle authorization revocation.
		   */
						
		  // check the session
		  if(!oauth_session_exists()) {
			throw new LinkedInException('This script requires session support, which doesn\'t appear to be working correctly.');
		  }
		  
		  $OBJ_linkedin = new LinkedIn($API_CONFIG);
		  $OBJ_linkedin->setTokenAccess($_SESSION['oauth']['linkedin']['access']);
		  $response = $OBJ_linkedin->revoke();
		  if($response['success'] === TRUE) {
			// revocation successful, clear session
			session_unset();
			$_SESSION = array();
			if(session_destroy()) {
			  // session destroyed
			  header('Location: ' . $_SERVER['PHP_SELF']);
			} else {
			  // session not destroyed
			  echo "Error clearing user's session";
			}
		  } else {
			// revocation failed
			echo "Error revoking user's token:<br /><br />RESPONSE:<br /><br /><pre>" . print_r($response, TRUE) . "</pre><br /><br />LINKEDIN OBJ:<br /><br /><pre>" . print_r($OBJ_linkedin, TRUE) . "</pre>";
		  }
		  break;
        default:
            // LP_create_account();
            
            if($_GET["rtype"]=="force_update" || $_POST["rtype"]=="force_update"){
                 LP_update_linkedin_info();
				 die(1);
            }
        break;
       }
	} catch(LinkedInException $e) {
	  // exception raised by library call
	  echo $e->getMessage();
      die();
	}
}

function LP_update_linkedin_info(){

	if(is_user_logged_in()){
		$API_CONFIG = json_decode(LINKEDIN_API_CONFIG,true);
	
		$blog_id = LP_get_user_blog_id();
		$user_info = get_blog_option($blog_id,"LP_linkedin_info");
		
		if($_GET["CB"]==1){
			$utoken = $_SESSION['oauth']['linkedin']['access'];
		}else{
			$utoken = maybe_unserialize($user_info["linkedin_TokenAccess"]);
		}
		// print_r($utoken);
		$OBJ_linkedin = new LinkedIn($API_CONFIG);
		$OBJ_linkedin->setTokenAccess($utoken);
		// $profile = $OBJ_linkedin->profile('~:(id,first-name,last-name,email-address,picture-url,picture-urls::(original))?format=json');
		$profile = $OBJ_linkedin->profile('~:(id,first-name,last-name,email-address,educations,picture-url,picture-urls::(original),public-profile-url,three-current-positions:(company:(name,id,industry),isCurrent,title),positions,summary,specialties,interests)?format=json');
		$profile["linkedin"] = json_decode($profile["linkedin"]);
		$com_id = $profile["linkedin"]->positions->values[0]->company->id;
		$company = $OBJ_linkedin->company($com_id.':(locations:(address:(city,country-code)))?format=json');
		$profile["profile"]->company_ext = json_decode($company["linkedin"]);

		$connections = $OBJ_linkedin->connections("~/connections:(id,first-name,last-name,picture-url,industry)?format=json");
		$myconnections = json_decode($connections["linkedin"]);

		if(isset($profile["linkedin"]->errorCode)){
			header('Location: ' . get_option('siteurl')."/linkedInP/?lType=initiate&rtype=force_update");
		}

		$meta["LP_linkedin_info"] = array(
			"linkedin_TokenAccess"      =>  $_SESSION['oauth']['linkedin']['access'],
			"linkedin_profile_thumb"    =>  $profile["linkedin"]->pictureUrl,
			"linkedin_profile_pic"      =>  $profile["linkedin"]->pictureUrls->values[0],
			"linkedin_uid"              =>  $profile["linkedin"]->id,
			"LP_prof_title"             =>  $profile["linkedin"]->positions->values[0]->title,
			"LP_city"                   =>  $profile["linkedin"]->company_ext->locations->values[0]->address->city,
			"LP_current_position"       =>  $profile["linkedin"]->positions->values[0]->title,
			"LP_current_company"        =>  $profile["linkedin"]->positions->values[0]->company->name,
			"LP_company_city"           =>  $profile["linkedin"]->company_ext->locations->values[0]->address->city,
			"LP_company_country"        =>  $profile["linkedin"]->company_ext->locations->values[0]->address->countryCode,
			"LP_company_industry"       =>  $profile["linkedin"]->positions->values[0]->company->industry,
			"LP_previous_companies"     =>  $profile["linkedin"]->positions->values,
			"LP_email"                  =>  $profile["linkedin"]->emailAddress,
			"LP_firstname"              =>  $profile["linkedin"]->firstName,
			"LP_lastname"               =>  $profile["linkedin"]->lastName,
		);

		// print_r($myconnections);
		$industries = array();
		foreach($myconnections->values as $contact){
			if($contact->industry){
				$the_ind = $contact->industry;
			}else{
				$the_ind = "unspecified";
			}
				$industries["_LPin_connection_".$the_ind]["contacts"][] = array("firstName" => $contact->firstName, "lastName" => $contact->lastName, "id" => $contact->id, "industry" => $the_ind);
		}

		$sep = "";
		$myindustries = "";
		foreach($industries as $key => $industry){
			$industries[$key]["count"] = count($industry["contacts"]);
			$industries[$key]["name"] = $industry["contacts"][0]["industry"];;
			$myindustries.=$sep.$industry["contacts"][0]["industry"];
			$sep = ",";
		}
		// echo $myindustries;
		$meta = array_merge($meta,$industries);
		$meta["_LPin_industries"] = $myindustries;
		// print_r($meta);

		// LP_update_user_meta(get_current_user_id(), $meta);
		$blog_id = LP_get_user_blog_id();
		LP_update_blog_meta($blog_id, $meta);
	}

}

function LP_create_account(){
    $API_CONFIG = json_decode(LINKEDIN_API_CONFIG,true);
	
    if($_SESSION['oauth']['linkedin']['authorized'] === TRUE){
        $OBJ_linkedin = new LinkedIn($API_CONFIG);
        $OBJ_linkedin->setTokenAccess($_SESSION['oauth']['linkedin']['access']);
        $profile = $OBJ_linkedin->profile('~:(id,first-name,last-name,email-address,educations,picture-url,picture-urls::(original),public-profile-url,three-current-positions:(company:(name,id,industry),isCurrent,title),positions,summary,specialties,interests)?format=json');
        $profile["linkedin"] = json_decode($profile["linkedin"]);
        $com_id = $profile["linkedin"]->positions->values[0]->company->id;
        $company = $OBJ_linkedin->company($com_id.':(locations:(address:(city,country-code)))?format=json');
        $profile["linkedin"]->company_ext = json_decode($company["linkedin"]);
		$connections = $OBJ_linkedin->connections("~/connections:(id,first-name,last-name,picture-url,industry)?format=json");
		$myconnections = json_decode($connections["linkedin"]);
    }

    $slogin = true;
    $upass = wp_generate_password( 12, false );
    $user_email = $profile["linkedin"]->emailAddress;

    $user_name = $user_email;                    
    $user_fname = $profile["linkedin"]->firstName;
    $user_lname = $profile["linkedin"]->lastName;
    
    $args = array(
        'meta_key'     => 'linkedin_uid',
        'meta_value'   => $profile["linkedin"]->id
        );
    $userx = get_users( $args );
    if (count($userx) == 0) {
     
        $userdata = array(
            "user_login"    =>  $user_name,
            "user_pass "    =>  $upass,
            "user_email"    =>  $user_email,
            "first_name"    =>  $user_fname,
            "last_name"     =>  $user_lname,
            "role"          =>  "author"
        );
        $user_id = _insert_user($userdata);
        
        if($user_id){
            // wp_update_user( array ( 'ID' => $user_id, 'user_pass' => $upass ) ) ;
            
            $umeta = array("linkedin_uid" => $profile["linkedin"]->id);
            LP_add_user_meta($user_id, $umeta);
            
            $blogname = LP_generate_domain_name();
            $blog = array(
                "domain"    =>  $blogname,
                "title"     =>  $user_fname.$user_lname,
                "email"     =>  $user_email
            );
            $blog_id = LP_add_site($user_id, $blog);
            // wp_update_user( array ( 'ID' => $user_id, 'user_pass' => $upass ) ) ;
            wp_update_user( array ( 'ID' => $user_id, 'user_pass' => $upass, 'user_nicename' => $blogname ) ) ;
           
            $meta["LP_linkedin_info"] = array(
                "linkedin_TokenAccess"      =>  $_SESSION['oauth']['linkedin']['access'],
                "linkedin_profile_thumb"    =>  $profile["linkedin"]->pictureUrl,
                "linkedin_profile_pic"      =>  $profile["linkedin"]->pictureUrls->values[0],
                "linkedin_uid"              =>  $profile["linkedin"]->id,
                "LP_prof_title"             =>  $profile["linkedin"]->positions->values[0]->title,
                "LP_city"                   =>  $profile["linkedin"]->company_ext->locations->values[0]->address->city,
                "LP_current_position"       =>  $profile["linkedin"]->positions->values[0]->title,
                "LP_current_company"        =>  $profile["linkedin"]->positions->values[0]->company->name,
                "LP_company_city"           =>  $profile["linkedin"]->company_ext->locations->values[0]->address->city,
                "LP_company_country"        =>  $profile["linkedin"]->company_ext->locations->values[0]->address->countryCode,
                "LP_company_industry"       =>  $profile["linkedin"]->positions->values[0]->company->industry,
                "LP_previous_companies"     =>  $profile["linkedin"]->positions->values,
                "LP_email"                  =>  $profile["linkedin"]->emailAddress,
                "LP_firstname"              =>  $profile["linkedin"]->firstName,
                "LP_lastname"               =>  $profile["linkedin"]->lastName,
            );
			$industries = array();
			foreach($myconnections->values as $contact){
				if($contact->industry){
					$the_ind = $contact->industry;
				}else{
					$the_ind = "unspecified";
				}
				$industries["_LPin_connection_".$the_ind]["contacts"][] = array("firstName" => $contact->firstName, "lastName" => $contact->lastName, "id" => $contact->id, "industry" => $contact->industry);
			}
			
			$sep = "";
			$myindustries = "";
			foreach($industries as $key => $industry){
				$industries[$key]["count"] = count($industry["contacts"]);
				$myindustries.=$sep.$industry["contacts"][0]["industry"];
				$sep = ",";
			}
			

			$meta = array_merge($meta,$industries);
			$meta["_LPin_industries"] = $myindustries;
			LP_update_blog_meta($blog_id, $meta);
					
            // add_blog_option( $blog_id, "LP_linkedin_info", $meta );
            
            $creds = array();
            $creds['user_login']    = $user_name;
            $creds['user_password'] = $upass;
            $creds['remember']      = true;
            $user = wp_signon( $creds, false );
            if ( is_wp_error($user) ){
               echo "Error : ";
               echo $user->get_error_message();
               die();
            }else{
                LP_channel_settings(true);
                global $current_site;
                $user_info = get_userdata($user_id);
                // $res = array(
                    // "success"           =>  true,
                    // "member_page_url"   =>  "http://".$current_site->domain."/in/".$user_info->user_nicename."/"
                // );
                // echo json_encode($res);
                $redirect_to = trim(network_site_url(),"/")."/in/".$user_info->user_nicename."/";
                echo "<html><head><script>opener.linkedin_callback_redirect(\"".$redirect_to."\");</script></head><body></body></html>";
                die();
            }
        }
    }elseif($slogin == true){
        LP_login_user_by_LinkedIn($profile["linkedin"]->id);
    }else{
        if($_POST["isajax"]!= 1)
            header('Location: ' . get_option('siteurl')."/user-already-exists/");
         else
         die("A registered user is associated with the email address you provided.");
    }
}

function LP_tw_login()
{
    if($_REQUEST["oauth_token"]){

        session_start();
        require_once('twitter/twitteroauth.php');

        $oauth_verifier = $_REQUEST['oauth_verifier'];
        $oauth_token = $_SESSION['oauth_token'];
        $oauth_token_secret = $_SESSION['oauth_token_secret'];
        $twitter_api_config = json_decode(TWITTER_API_CONFIG,true);
        // print_r($twitter_api_config);
        define('CONSUMER_KEY', $twitter_api_config["CONSUMER_KEY"]);
        define('CONSUMER_SECRET',  $twitter_api_config["CONSUMER_SECRET"]);

        $twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $oauth_token, $oauth_token_secret);

        $access_token = $twitteroauth->getAccessToken($oauth_verifier);
        //linkedin_TokenAccess
        $tw_user = $twitteroauth->get("https://api.twitter.com/1.1/account/verify_credentials.json",array("skip_status" => "1"));

        $tw = json_encode($tw_user);
        $tw_user = json_decode($tw,true);
        $tw_id      = $tw_user["id"];
        $args = array(
            'meta_key'     => '_LP_tw_uid',
            'meta_value'   => $tw_id
        );
        $the_user = get_users($args);
        if (count($the_user) == 0){
            $tw_user["picture"] = str_replace("_normal","",$tw_user["profile_image_url"]);
            LP_create_account_tw($tw_user);
        }else{
            $res = LP_login_user_by_social("_LP_tw_uid",$tw_id, true);
            if($res !== false){
                echo $res["member_page_url"];
            }
        }
        die();
    }
    die();
}

function LP_create_account_tw($tw_user)
{
    $tw_user["email"] = "dripple".rand(100,100000)."@dummy_dripple".rand(100,100000).".com";
    $upass = wp_generate_password( 12, false );
    $user_name = $tw_user["email"];
    $userdata = array(
        "user_login"    =>  $user_name,
        "user_pass "    =>  $upass,
        "user_email"    =>  $tw_user["email"],
        "first_name"    =>  $tw_user["name"],
        "role"          =>  "author"
    );
    $user_id = wp_insert_user($userdata);

    if($user_id){
        $umeta = array("_LP_tw_uid" => $tw_user["id"]);
        LP_add_user_meta($user_id, $umeta);

        $blogname = LP_generate_domain_name();
        $blog = array(
            "domain"    =>  $blogname,
            "title"     =>  $tw_user["name"],
            "email"     =>  $tw_user["email"]
        );
        $blog_id = LP_add_site($user_id, $blog);
        wp_update_user( array ( 'ID' => $user_id, 'user_pass' => $upass, 'user_nicename' => $blogname ) ) ;
        $meta["LP_tw_info"] =  $tw_user;
        LP_update_blog_meta($blog_id, $meta);

        $creds = array();
        $creds['user_login']    = $user_name;
        $creds['user_password'] = $upass;
        $creds['remember']      = true;
        $user = wp_signon( $creds, false );
        if ( is_wp_error($user) ){
            echo "Error : ";
            echo $user->get_error_message();
            die();
        }else{
            LP_channel_settings(true);
            global $current_site;
            $user_info = get_userdata($user_id);
            $redirect_to = trim(network_site_url(),"/")."/tw/".$user_info->user_nicename."/";
            echo $redirect_to;
            echo "<html><head><script>opener.social_callback_redirect(\"".$redirect_to."\");</script></head><body></body></html>";
            die();
        }
    }
}

add_action('wp_ajax_LP_fb_login', 'LP_fb_login');
add_action('wp_ajax_nopriv_LP_fb_login', 'LP_fb_login');
function LP_fb_login()
{
    $fb_user    = $_POST["fb_user"];
    $fb_id      = $fb_user["id"];
    $args = array(
        'meta_key'     => '_LP_fb_uid',
        'meta_value'   => $fb_id
    );
    $the_user = get_users($args);
    if (count($the_user) == 0){
        LP_create_account_fb($fb_user);
    }else{
        $res = LP_login_user_by_social('_LP_fb_uid',$fb_id, false, false);
        if($res !== false){
            echo $res["member_page_url"];
        }
    }
    die();
}

function LP_login_user_by_social($social_meta,$scoial_id,$from_popup = false, $redirect = true)
{
    $args = array(
        'meta_key'     => $social_meta,
        'meta_value'   => $scoial_id
    );

    $the_user = get_users( $args );
    if(is_array($the_user) && isset($the_user[0]->ID)){
        $user_id = $the_user[0]->ID;
        $user = get_user_by( "id", $user_id );
        $user_name = $user->data->user_login;
        wp_set_current_user($user_id, $user_name);
        wp_set_auth_cookie($user_id);
        do_action('wp_login', $user_name);
        global $current_site;

        if($social_meta == "_LP_fb_uid") $P_prefix = "fb";
        elseif($social_meta == "_LP_tw_uid") $P_prefix = "tw";

        $user_info = get_userdata($user_id);
        $res = array(
            "success"           =>  true,
            "member_page_url"   =>  trim(network_site_url(),"/")."/$P_prefix/".$user_info->user_nicename."/"
        );
        if($redirect){
            if(!$from_popup){
                header('Location: ' .$res["member_page_url"]);
            }else{
                echo "<html><head><script>opener.social_callback_redirect(\"".$res["member_page_url"]."\");</script></head><body></body></html>";
                die();
            }
        }else{
            return $res;
        }
    }else{
        if($from_popup == true && $redirect == true){
            echo "<html><head><script>
                    window.opener = self;
                    window.close();
                 </script></head><body></body></html>";
            die();
        }elseif($redirect == false){
            return false;
        }
    }
}

function LP_create_account_fb($fb_user)
{
    if(isset($fb_user["email"]) && $fb_user["email"]!=''){
        $upass = wp_generate_password( 12, false );
        $user_name = $fb_user["email"];
        $userdata = array(
            "user_login"    =>  $user_name,
            "user_pass "    =>  $upass,
            "user_email"    =>  $fb_user["email"],
            "first_name"    =>  $fb_user["first_name"],
            "last_name"     =>  $fb_user["last_name"],
            "role"          =>  "author"
        );
        $user_id = wp_insert_user($userdata);

        if($user_id){
            $umeta = array("_LP_fb_uid" => $fb_user["id"]);
            LP_add_user_meta($user_id, $umeta);

            $blogname = LP_generate_domain_name();
            $blog = array(
                "domain"    =>  $blogname,
                "title"     =>  $fb_user["first_name"].$fb_user["last_name"],
                "email"     =>  $fb_user["email"]
            );
            $blog_id = LP_add_site($user_id, $blog);
            wp_update_user( array ( 'ID' => $user_id, 'user_pass' => $upass, 'user_nicename' => $blogname ) ) ;

            $fb_user["picture"] = "http://graph.facebook.com/".$fb_user["id"]."/picture/?type=large";
            $meta["LP_fb_info"] =  $fb_user;
            LP_update_blog_meta($blog_id, $meta);

            $creds = array();
            $creds['user_login']    = $user_name;
            $creds['user_password'] = $upass;
            $creds['remember']      = true;
            $user = wp_signon( $creds, false );
            if ( is_wp_error($user) ){
                echo "Error : ";
                echo $user->get_error_message();
                die();
            }else{
                LP_channel_settings(true);
                global $current_site;
                $user_info = get_userdata($user_id);
                $redirect_to = trim(network_site_url(),"/")."/fb/".$user_info->user_nicename."/";
                echo $redirect_to;
                die();
            }
        }
    }
}

function LP_generate_domain_name()
{
    global $wpdb;
    $sql = "SELECT max(`ID`)+100000 as blogname FROM `dip_users` WHERE 1";
    $to_blog_name = $wpdb->get_results($sql,ARRAY_A);
    return $to_blog_name[0]["blogname"];
}

function LP_add_user_meta($user_id, $meta = array())
{
    foreach($meta as $meta_key => $meta_value){
        add_user_meta( $user_id, $meta_key, $meta_value, true );
    }
}

function LP_update_user_meta($user_id, $meta = array())
{
    foreach($meta as $meta_key => $meta_value){
        update_user_meta( $user_id, $meta_key, $meta_value);
    }
}

function LP_update_blog_meta($blog_id, $meta = array())
{
    foreach($meta as $meta_key => $meta_value){
		update_blog_option( $blog_id, $meta_key, $meta_value );
    }
}

function LP_add_site($user_id, $blog = array()){
    global $current_site;
    global $wpdb;
	// $blog = $_POST['blog'];
	$domain = '';
	if ( preg_match( '|^([a-zA-Z0-9-])+$|', $blog['domain'] ) )
		$domain = strtolower( $blog['domain'] );

	// If not a subdomain install, make sure the domain isn't a reserved word
	if ( ! is_subdomain_install() ) {
		$subdirectory_reserved_names = apply_filters( 'subdirectory_reserved_names', array( 'page', 'comments', 'blog', 'files', 'feed' ) );
		if ( in_array( $domain, $subdirectory_reserved_names ) )
			return( sprintf( __('The following words are reserved for use by LinkedPost functions and cannot be used as blog names: <code>%s</code>' ), implode( '</code>, <code>', $subdirectory_reserved_names ) ) );
	}

	$email = sanitize_email( $blog['email'] );
	$title = $blog['title'];

	if ( empty( $domain ) )
		return( __( 'Missing or invalid site address.' ) );
	if ( empty( $email ) )
		return( __( 'Missing email address.' ) );
	if ( !is_email( $email ) )
		return( __( 'Invalid email address.' ) );

	if ( is_subdomain_install() ) {
		$newdomain = $domain . '.' . preg_replace( '|^www\.|', '', $current_site->domain );
		$path      = $current_site->path;
	} else {
		$newdomain = $current_site->domain;
		$path      = $current_site->path . $domain . '/';
	}
    $password = '{The password you provided during registration}';
	$id = wpmu_create_blog( $newdomain, $path, $title, $user_id , array( 'public' => 1 ), $current_site->id );
	$wpdb->show_errors();
	if ( !is_wp_error( $id ) ) {
		if ( !is_super_admin( $user_id ) && !get_user_option( 'primary_blog', $user_id ) )
			update_user_option( $user_id, 'primary_blog', $id, true );
		$content_mail = sprintf( __( 'New site created by %1$s

Address: %2$s
Name: %3$s' ), $current_user->user_login , get_site_url( $id ), stripslashes( $title ) );
		wp_mail( get_site_option('admin_email'), sprintf( __( '[%s] New Site Created' ), $current_site->site_name ), $content_mail, 'From: "Site Admin" <' . get_site_option( 'admin_email' ) . '>' );
		wpmu_welcome_notification( $id, $user_id, $password, $title, array( 'public' => 1 ) );
		// wp_redirect( add_query_arg( array( 'update' => 'added', 'id' => $id ), 'site-new.php' ) );
		return $id;
	} else {
		return( $id->get_error_message() );
	}
}

function LP_login_user_by_LinkedIn($linkedIn_ID ,$from_popup = false, $redirect = true)
{
    $args = array(
        'meta_key'     => 'linkedin_uid',
        'meta_value'   => $linkedIn_ID
    );
    
    $the_user = get_users( $args );
    if(is_array($the_user) && isset($the_user[0]->ID)){
        $user_id = $the_user[0]->ID;
        $user = get_user_by( "id", $user_id );
        $user_name = $user->data->user_login;
        wp_set_current_user($user_id, $user_name);
        wp_set_auth_cookie($user_id);
        do_action('wp_login', $user_name);
        LP_update_linkedin_info();
        global $current_site;
        $user_info = get_userdata($user_id);
        $res = array(
            "success"           =>  true,
            "member_page_url"   =>  trim(network_site_url(),"/")."/in/".$user_info->user_nicename."/"
        );
        if($redirect){
            if(!$from_popup){
                header('Location: ' .$res["member_page_url"]);
            }else{
                echo "<html><head><script>opener.linkedin_callback_redirect(\"".$res["member_page_url"]."\");</script></head><body></body></html>";
                die();
            }
        }else{
            return true;
        }
    }else{
        if($from_popup == true && $redirect == true){
            echo "<html><head><script>
                    window.opener = self;
                    window.close();
                 </script></head><body></body></html>";
            die();
        }elseif($redirect == false){
            return false;
        }
    }
}

add_action('wp_ajax_nopriv_LP_fast_linkedin_login', 'LP_fast_linkedin_login');
function LP_fast_linkedin_login()
{
    echo LP_login_user_by_LinkedIn($_POST["linkedIn_ID"], false, false);
    die();
}

function _insert_user($userdata){
    return wp_insert_user( $userdata );
}


// add_action('wp_ajax_linkedP_check_login', 'linkedP_check_login');
add_action('wp_ajax_nopriv_linkedP_check_login', 'linkedP_check_login');
add_action('wp_ajax_nopriv_lwp_login', 'lwp_login');
// add_action('wp_ajax_lwp_login', 'lwp_login');
add_action('wp_ajax_lwp_validate_user_form', 'lwp_validate_user_form');
add_action('wp_ajax_nopriv_lwp_validate_user_form', 'lwp_validate_user_form');
add_action('wp_ajax_nopriv_lwp_wpmu_user_signup', 'lwp_wpmu_user_signup');
add_action('wp_ajax_nopriv_linkedP_registe_new_site', 'do_linkedin_log');

function linkedP_check_login(){
    global $wpdb;
    $errs = array();
    if ( preg_match( '|^([a-zA-Z0-9-])+$|', $_POST['domain'] ) ){
        $res = $wpdb->get_results("(SELECT `path` FROM `wp_blogs` WHERE `path` = '/".$_POST['domain']."/') 
                UNION ALL 
                (SELECT `path` FROM `wp_signups` WHERE `path` = '/".$_POST['domain']."/')",ARRAY_A);
    }else{
        $errs[] = "Domain name is not valid.";
    }
    // $user = username_exists( $_POST["uname"] );
	$user = get_user_by("email",$_POST["email"]);
    if (!$user && count($res)==0 && $_POST["email"] !=""){
        echo "good";
    }else{
        if($user!=false){
            $errs["email"] = "A registered user is associated with the email address you provided.";
        }
        if(count($res)>0){
            $errs["domain"] = "Domain name is taken";
        }
        echo json_encode($errs);
    }
    die();
}

function lwp_login(){
    $creds = array();
    $creds['user_login'] = $_POST["wp_ulogin"];
    $creds['user_password'] = $_POST["wp_upass"];
    $creds['remember'] = true;
        
    $user = wp_signon( $creds, true );
    wp_set_auth_cookie($user->ID);
    if ( is_wp_error($user) ){
        $errs = array("error" => $user->get_error_message());
        echo json_encode($errs);
        die();
    }else{
        global $current_site;
        $user_info = get_userdata($user->ID);
        $res = array(
            "success"           =>  true,
            "member_page_url"   =>  trim(network_site_url(),"/")."/in/".$user_info->user_nicename."/"
        );
        echo json_encode($res);
        die();
    }
    die();
}


///// START MANUAL CREATION OF ACCOUNT /////

function lwp_validate_user_form(){
    // print_r($_POST);
    // $user_name = $_POST["email"];
    // $user_name = str_replace(".","",$user_name);
    // $user_name = str_replace("@","",$user_name);
    
    $result = LP_validate_user_form($_POST["email"],$_POST["email"]);
    extract($result);

    if ( $errmsg = $errors->get_error_message('user_email') ) {
        echo '<p class="error">'.$errmsg.'</p>';
        die();
    }

    echo 1;
    die();
}

function lwp_wpmu_user_signup(){
    $user_name  = strtolower($_POST["email"]);
    $user_name  = str_replace(".","",$user_name);
    $user_name  = str_replace("@","",$user_name);
    $user_email = $_POST["email"];
    $meta = array(
        "LP_account_type"       =>  $_POST["acc_type"],
        "LP_prof_title"         =>  $_POST["title"],
        "LP_city"               =>  $_POST["city"],
        "LP_current_position"   =>  $_POST["current_pos"],
        "LP_current_company"    =>  $_POST["current_com"],
        "LP_company_city"       =>  $_POST["com_city"],
        "LP_company_country"    =>  $_POST["com_country"],
        "LP_email"              =>  $_POST["email"],
        "LP_fname"              =>  $_POST["fname"],
        "LP_lname"              =>  $_POST["lname"],
        "LP_upass"              =>  $_POST["upass"]
    );
    
        
    // echo LP_validate_user_signup($user_name, $user_email, $meta);
    $blog = array(
        "blogname"   => $_POST["blogname"],
        "blog_title" => $_POST["blogname"]
        );
    echo LP_validate_blog_signup($user_name, $user_email, $blog, $meta);
    die();
}

function LP_validate_user_signup($user_name,$user_email, $meta = array()) {
	$result = LP_validate_user_form($user_name,$user_email);
	extract($result);
    if ( $errmsg = $errors->get_error_message('user_email') ) {
        return '<p class="error">'.$errmsg.'</p>';
    }

	wpmu_signup_user($user_email, $user_email, apply_filters( 'add_signup_meta', $meta ) );

	// confirm_user_signup($user_name, $user_email);
	return true;
}

function LP_validate_user_form($user_name,$user_email){
	return wpmu_validate_user_signup($user_name, $user_email);
}

function LP_validate_blog_signup($user_name, $user_email, $blog, $meta = array()){
	global $current_site;
    // Re-validate user info.
	$result = wpmu_validate_user_signup($user_name, $user_email);
	extract($result);

	if ( $errmsg = $errors->get_error_message('user_email') ) {
		 return '<p class="error">'.$errmsg.'</p>';
	}

	$result = wpmu_validate_blog_signup($blog['blogname'], $blog['blog_title']);
	extract($result);

	if ( $errmsg = $errors->get_error_message('blogname') ) {
		 return '<p class="error">'.$errmsg.'</p>';
	}
	$meta['lang_id']    = 1;
    $meta['public']     = 1;
	$meta = apply_filters( 'add_signup_meta', $meta );

    $newdomain = $current_site->domain;
    $path      = $current_site->path . $blog['blogname'] . '/';
        
	wpmu_signup_blog($newdomain, $path, $blog['blog_title'], $user_name, $user_email, $meta);
	return true;
}

///////////////////////////////////////////////

function LP_uploadify(){
// Define a destination
$targetFolder = plugin_dir_path(__FILE__).'temp_profile'; // Relative to the root
// echo $targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
// echo $targetFolder;
$verifyToken = md5('unique_salt' . $_POST['timestamp']);

    if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
        $tempFile = $_FILES['Filedata']['tmp_name'];
        $targetPath = $targetFolder;
        $targetFile = rtrim($targetPath,'/') . '/' . $_FILES['Filedata']['name'];
        
        // Validate the file type
        $fileTypes = array('jpg','jpeg','gif','png'); // File extensions
        $fileParts = pathinfo($_FILES['Filedata']['name']);
        
        if (in_array($fileParts['extension'],$fileTypes)) {
            move_uploaded_file($tempFile,$targetFile);
            echo '1';
        } else {
            echo 'Invalid file type.';
        }
    }
}
/////////////////////////////

function LP_proccess_uploads()
{
    if(is_user_logged_in()){
        $f_type = strtolower($_FILES["LP_file"]["type"]);
        if(($f_type == "image/png" || $f_type == "image/jpg" || $f_type == "image/jpeg" || $f_type == "image/gif") && $_POST["LP_drip_id"]!=""){
            global $wpdb;
            global $switched;
            
            global $current_site;
            $user_info = get_userdata(get_current_user_id());
            
            $drip_id   = $_POST["LP_drip_id"];
            $file      = $_FILES["LP_file"]["tmp_name"];
            
            
            $user_ID = get_current_user_id(); 
            $user_blogs = get_blogs_of_user( $user_ID );
            foreach($user_blogs as $blog){
                if($blog->userblog_id != 1){
                    $lp_drip_queue = $wpdb->prefix.$blog->userblog_id."_lp_drip_queue";
                }else{
                    continue;
                }
                $qry = "SELECT * FROM `$lp_drip_queue` WHERE `id`=".$drip_id;
                $post_s = $wpdb->get_results($qry,ARRAY_A);
                if($post_s[0]["blog_id"] == $blog->userblog_id){
                    switch_to_blog($post_s[0]["blog_id"]);
                     
                    $wp_upload_dir = wp_upload_dir();
                    // print_r($wp_upload_dir);
                    $filename = $_FILES["LP_file"]["name"];
                    $to_new_file = $wp_upload_dir["path"]."/".$filename;
                    $a = 0;
                    while(file_exists($to_new_file)){
                        // echo "File Exists : ".$to_new_file."<br />";
                        $a++;
                        $exp_name = explode(".",$_FILES["LP_file"]["name"]);
                        $c = count($exp_name);
                        $exp_name[$c-2].="_".$a;
                        $filename = implode(".",$exp_name);
                        $to_new_file = $wp_upload_dir["path"]."/".$filename;
                    }
                    
                   move_uploaded_file($_FILES["LP_file"]["tmp_name"],$to_new_file);
                    
                    $wp_filetype = wp_check_filetype(basename($filename), null );
                    //'guid' => $wp_upload_dir['url'] . '/' . $filename, 
                    //http://sanyahaitun.com/ronlinking/wp-content/uploads/sites/10/2013/06/nokia.jpg
                    //http://sanyahaitun.com/wp-content/uploads/sites/10/2013/06/IMG_5571.JPG 
                    $guid = $wp_upload_dir['url'] . '/' . $filename;
                    $guid = explode("wp-content",$guid);
                    $guid[0]=$guid[0]. $user_info->user_nicename."/";
                    $guid_ = implode("wp-content",$guid);
                    $attachment = array(
                        'guid' => $guid_ , 
                        'post_mime_type' => $wp_filetype['type'],
                        'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );
                    
                    // print_r($attachment);
                    $attach_id = wp_insert_attachment( $attachment , $wp_upload_dir['subdir']."/".$filename );
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    $attach_data = wp_generate_attachment_metadata( $attach_id, $wp_upload_dir['path'] . '/' . $filename );
                    // print_r($attach_data);
                    wp_update_attachment_metadata( $attach_id, $attach_data );
                    add_post_meta($post_s[0]["post_id"], '_thumbnail_id', $attach_id);
                    restore_current_blog();
                    echo "<script>parent.lp_update_image('".$guid_ ."','".$drip_id."');</script>";
                    break;
                }http://www.drippost.com/00016h/#!
            }
        }
    }
    die();
}

/* 
 * Deletes the topic and all of its drips/posts
 */
  add_action('wp_ajax_LP_delete_this_topic', 'LP_delete_this_topic');
function LP_delete_this_topic()
{
	if($_POST["topic"]!=""){
		global $switched, $wpdb, $shardb_prefix;
		$user_ID = get_current_user_id(); 
		$blog_id = LP_get_user_blog_id($user_ID);
		$global_db = $shardb_prefix."global";
		$db_name = LP_get_blog_db_name($blog_id);
		$prefix = $wpdb->base_prefix;
		switch_to_blog($blog_id);
			$sql = $wpdb->prepare("SELECT ID FROM `{$db_name}`.`{$prefix}{$blog_id}_posts` WHERE `post_type`='post' AND `post_parent`=%d",$_POST["topic"]);
			$drips = $wpdb->get_results($sql,ARRAY_A);
			$post_ids = "";
			$sep = "";
			foreach($drips as $drip){
				$post_ids.= $sep.$drip["ID"];
				$sep = ",";
				wp_delete_post( $drip["ID"], true );
			}
			wp_delete_post( $_POST["topic"], true );
			//$sql = "DELETE FROM {$global_db} WHERE `blog_id`={$blog_id} AND `post_id` IN ({$post_ids})";
		restore_current_blog();
        echo json_encode(LP_get_user_topics());
	}
	die();
}

/* 
 * Updates the thumbnail of the topic 
 */
function LP_update_topic_thumb($args)
{
	// print_r($args);
	if(isset($args["new_topic_image"]) && isset($args["topic"]) && isset($args["top"]) && isset($args["left"]) && isset($args["scaled_width"])){
		$left = ($args["left"] * -1 );
		$top = ($args["top"] * -1 );

		$plugin_dir = ABSPATH ."wp-content/plugins/LinkedPost/";
		$uid = get_current_user_id();
		// $target = $plugin_dir."temp_upload/temp_".$uid;
		$target = $plugin_dir."hd_upload/temp_".$uid;
		if(!file_exists($target))
			mkdir($target);
		$file = $target."/".$args["new_topic_image"]["name"];
		while(file_exists($file)){
			$tf = explode(".",$args["new_topic_image"]["name"]);
			$tf[count($tf)-2].= "_".rand(100,100000);
			$file = $target."/".implode(".",$tf);
		}
		// echo "\n\r file : ".$file."\n\r";
		if(move_uploaded_file($args["new_topic_image"]["tmp_name"],$file)){
			$thumb = get_post_meta( $args["topic"], "_LP_HD_image", true );
			if($thumb!=""){
				unlink($thumb);
			}
			update_post_meta($args["topic"], '_LP_HD_image', $file);
			// lets recompute so that we'll use 662x332 dimensions instead of the 495x248 from our UI,
			$scaled_width = $args["scaled_width"];
			$percent_diff = (662-495)/495;
			$scaled_width = $scaled_width *($percent_diff + 1);
			$left = $left *($percent_diff + 1);
			$top = $top *($percent_diff + 1);
			
			$the_image = LP_cropper($file, $scaled_width, 662, 332, $left, $top, true);
			// echo "\n\r the_image : ".$the_image."\n\r";
			$img["file"] = $the_image;
			$extension = pathinfo($the_image, PATHINFO_EXTENSION);
			$img["type"] = "image/".trim($extension);
			$blog_id = LP_get_user_blog_id($uid);
			set_featured_img($args["topic"], $blog_id ,$img, true, false);
		}
	}
}

/* 
 * Updates the settings 'postmeta' of the topic 
 */
 add_action('wp_ajax_LP_update_topic_settings', 'LP_update_topic_settings');
function LP_update_topic_settings()
{
	global $switched;
	$topic_settings = Array(
		"LP_channel" 		=> "LP_channel",
		"new_stiky_drip" 	=> "_stiky_drip",
		"new_language" 		=> "_language",
		"new_industry" 		=> "_industry",
		"new_results" 		=> "_results",
		"new_messages" 		=> "_messages",
		"new_iframe" 		=> "_iframe",
		"new_dripurl" 		=> "_dripurl",
		// "new_trash" 		=> "_trash",
		"new_private" 		=> "_private",
		"new_flip" 			=> "_flip",
		"new_timezone" 		=> "_timezone"
		);
	$arr = $_POST;
	$to_update = array_intersect_key($topic_settings, $arr);
	
	$user_ID = get_current_user_id(); 
	$blog_id = LP_get_user_blog_id($user_ID);
	switch_to_blog($blog_id);
	foreach($to_update as $key=>$val){
		$res = update_post_meta($_POST["topic"], $val, $_POST[$key]);
		if($res === true && $key == "new_private")
			LP_reschedule_all_drips($_POST["topic"]);
	}
	restore_current_blog();
    $updated = LP_get_user_topics($_POST["topic"], true);
	echo json_encode($updated[0]);
	die();
}

function LP_remote_post(){
	// print_r($_POST);
    global $wpdb;
    global $switched;

    if(is_user_logged_in()){
        $user_ID = get_current_user_id(); 
        $user_blogs = get_blogs_of_user( $user_ID );
        $blog_id = LP_get_user_blog_id($user_ID);
        if($_POST["type"] == "drip" 
           && isset($_POST["wptitle"]) 
           && isset($_POST["wpbody"]) 
           && isset($_POST["wpexcerpt"]) 
           && isset($_POST["story_URL"])
           ){
            $post = array(
            'comment_status' => 'open', // 'closed' means no comments.
            'ping_status'    => 'open', // 'closed' means pingbacks or trackbacks turned off
            'post_author'    => $user_ID, //The user ID number of the author.
            'post_content'   => wp_strip_all_tags($_POST["wpbody"]), //The full text of the post.
            'post_excerpt'   => wp_strip_all_tags($_POST["wpexcerpt"]), //For all your post excerpt needs.
            'post_status'    => 'publish', //Set the status of the new post.
            'post_title'     => wp_strip_all_tags($_POST["wptitle"]), //The title of your post.
            'post_type'      => 'post', //You may want to insert a regular post, page, link, a menu item or some custom post type
            'post_parent'    => $_POST["topic"]
            );  
        }elseif($_POST["type"] == "topic" 
                && ($_POST["channel"] > 0 || $_POST["channel"] <= 30) 
                && isset($_POST["wptitle"]) 
                && isset($_POST["wpbody"])
                ){
            $post = array(
            'comment_status' => 'open', // 'closed' means no comments.
            'ping_status'    => 'open', // 'closed' means pingbacks or trackbacks turned off
            'post_author'    => $user_ID, //The user ID number of the author.
            'post_content'   => wp_strip_all_tags($_POST["wpbody"]), //The full text of the post.
            'post_status'    => 'publish', //Set the status of the new post.
            'post_title'     => wp_strip_all_tags($_POST["wptitle"]), //The title of your post.
            'post_type'      => 'topic', //You may want to insert a regular post, page, link, a menu item or some custom post type
            );  
        }else{
            die("0");
        }    
        // print_r($post);
        switch_to_blog($blog_id);
            $post_id = wp_insert_post( $post );
            if($post_id){
                $res = add_post_meta($post_id, 'LP_channel', $_POST["channel"]);
                $topic = LP_get_user_topics($post_id);
                if($topic){
					global $shardb_prefix;
					$global_db = $shardb_prefix."global";
					$prefix = $wpdb->base_prefix;
			
					// $sql = $wpdb->prepare("INSERT INTO `{$global_db}`.`{$prefix}lp_drips` SET `post_id` = %d,`blog_id` = %d, `post_type` = %s, `channel_id` = %d, `post_date` = %s", $topic[0]["ID"], $topic[0]["blog_id"],'topic',$topic[0]["channel_id"],$topic[0]["post_date"]);
					
					$sql = $wpdb->prepare("INSERT INTO `{$global_db}`.`{$prefix}lp_drips`(`post_id`,`temp_name`,`blog_id`,`post_type`,`channel_id`,`post_date`)
										SELECT %d, (IFNULL(max(`temp_name`),0))+1, %d, 'topic', %d, '%s' FROM `{$global_db}`.`{$prefix}lp_drips` WHERE `blog_id` = %d", $topic[0]["ID"], $topic[0]["blog_id"], $topic[0]["channel_id"], $topic[0]["post_date"], $topic[0]["blog_id"]);
					// echo $sql;
					$wpdb->query($sql);
					
					$ftopic = LP_get_user_topics($post_id);
					if($_POST["callback_function"]!=""){
						echo "<script>
								parent.".$_POST["callback_function"]."(".$ftopic[0]["ID"].");
							</script>";
							die();
					}else{
						echo json_encode($ftopic);
						die();
					}
                }else{
                    echo "0";
                }
				
				// print_r($_POST);
                if(isset($_FILES["new_topic_image"])){
					$left = ($_POST["left"] * -1 );
					$top = ($_POST["top"] * -1 );

					$plugin_dir = ABSPATH ."wp-content/plugins/LinkedPost/";
					$uid = get_current_user_id();
					$target = $plugin_dir."temp_upload/temp_".$uid;
					if(!file_exists($target))
						mkdir($target);
					$file = $target."/".$_FILES["new_topic_image"]["name"];
					while(file_exists($file)){
						$tf = explode(".",$_FILES["new_topic_image"]["name"]);
						$tf[count($tf)-2].= "_".rand(100,100000);
						$file = $target."/".implode(".",$tf);
					}
					if(move_uploaded_file($_FILES["new_topic_image"]["tmp_name"],$file)){
						add_post_meta($post_id, '_LP_HD_image', $file);
						// lets recompute so that we'll use 662x332 dimensions instead of the 495x248 from our UI,
						$scaled_width = $_POST["scaled_width"];
						$percent_diff = (662-495)/495;
						$scaled_width = $scaled_width *($percent_diff + 1);
						$left = $left *($percent_diff + 1);
						$top = $top *($percent_diff + 1);
						
						$the_image = LP_cropper($file, $scaled_width, 662, 332, $left, $top, true);
						// var_dump($the_image);
						// $file = explode("/",$img["file"]);
						// $plugin_dir_path = plugin_dir_path(__FILE__);
						// $npath = $plugin_dir_path."temp_upload/temp_".$user_ID."/".end($file);
						$img["file"] = $the_image;
						$extension = pathinfo($the_image, PATHINFO_EXTENSION);
						$img["type"] = "image/".trim($extension);
						set_featured_img($post_id, $blog_id ,$img, true);
					}
                }
            }
        restore_current_blog();
    }else{
        $creds = array();
        $creds['user_login']    = $POST["wpusername"];
        $creds['user_password'] = $POST["wppassword"];;
        $creds['remember']      = true;
        $user = wp_signon( $creds, false );
        if ( is_wp_error($user) ){
            die("0");
        }else{
           LP_remote_post();
        }
    }
    die("1");
}
add_action('wp_ajax_LP_save_add_topic', 'LP_remote_post');

/* 
 * Upload image and saved into temporary directory and session 
 */
function LP_upload_img_to_temp(){
	// Image ration is always w/h 1.8%
    global $current_site;
    $res = tc_handle_upload_prefilter($_FILES["topic_file"],662);

    if(isset($res["error"]) && $res["error"] != 0){
        die($res["error"]);
    }
	
	$LP_siteurl = trim(network_site_url(),"/");
    if(is_user_logged_in()){
	// print_r($_POST);
        if(isset($_FILES["topic_file"])){
			$plugin_dir = "wp-content/plugins/LinkedPost/";
			$uid = get_current_user_id();
			if(!file_exists($plugin_dir."temp_upload/temp_".$uid))
				mkdir($plugin_dir."temp_upload/temp_".$uid);
			$to_temp_file = $plugin_dir ."temp_upload/temp_".$uid."/".$_FILES['topic_file']['name'];

			if(isset($_SESSION[$_POST["session_name"]]["file"])){
				unlink($_SESSION[$_POST["session_name"]]["file"]);
			}
			move_uploaded_file($_FILES["topic_file"]["tmp_name"],$to_temp_file);
			unset($_SESSION[$_POST["session_name"]]);
			$_SESSION[$_POST["session_name"]] = array("file" => $to_temp_file, "type" =>$_FILES["topic_file"]["type"]);
            if($_POST["callback_function"] == "LP_set_topic_thumb"){
                echo "<script>parent.LP_set_topic_thumb('".$_POST["post_id"]."','".$LP_siteurl."/".$to_temp_file."');</script>";
            }elseif($_POST["callback_function"] == "LP_new_topic_define_focal_point"){
				 // echo "<script>parent.LP_new_topic_define_focal_point('".$LP_siteurl."/".$to_temp_file."');</script>";
			}else{
                echo "<script>parent.lp_set_topic_image('".$LP_siteurl."/".$to_temp_file."');</script>";
            }
			die();
        }
    }else{
		die();
	}
}

function LP_saveImage($base64img, $to_filename = "", $target = "")
{
	print_r($_POST);
	echo "\n\r to_filename :".$to_filename."\n\r";
	if($to_filename == ""){
		return false;
	}
    if($target == ""){
		$plugin_dir = ABSPATH ."wp-content/plugins/LinkedPost/";
		$uid = get_current_user_id();
		$target = $plugin_dir."temp_upload/temp_".$uid;
	}
	if(!file_exists($target))
		mkdir($target);
	$file = $target."/".$to_filename;
	echo "\r\n file : ".$file."\n\r";
	while(file_exists($file)){
		$tf = explode(".",$to_filename);
		$tf[count($tf)-2].="_".rand(100,100000);
		$file = $target."/".implode(".",$tf);
	}

    $base64img = str_replace('data:image/jpeg;base64,', '', $base64img);
    $data = base64_decode($base64img);
	echo "\r\n file : ".$file."\n\r";
    if(file_put_contents($file, $data)){
		return $file;
	}else{
		return false;
	}
}

function LP_get_topics_thumb()
{
    if(is_user_logged_in()){
        global $switched;
        $post_ids = explode(",",$_POST["post_ids"]);
        $posts_thumb = array();
        $a = 0;
        $blog_id = LP_get_user_blog_id();
        foreach($post_ids as $post_id){
            $posts_thumb[$a]["post_id"] = $post_id;
            $posts_thumb[$a]["thumbnail"] = LP_get_post_thumb_url($post_id, $blog_id);
            $a++;
        }
        echo json_encode($posts_thumb);
    }
    die();
}
add_action('wp_ajax_LP_get_topics_thumb', 'LP_get_topics_thumb');

function LP_get_post_other_info()
{
        global $switched, $current_site;
        $LP_siteurl = trim(network_site_url(),"/");
        $default_avatar = $LP_siteurl."/wp-content/themes/LinkedPOST/images/author.png";
        $post_items = $_POST["post_items"];
        $post_other_info = array();
        $a = 0;
        foreach($post_items as $post_item){
            $blog_id = $post_item["blog_id"];
            switch_to_blog($blog_id);
                $post_id      = $post_item["post_id"];
                $topic_id     = $post_item["topic_id"];
                $post_author  = $post_item["post_author"];
                $post_other_info[$a]["post_id"]     = $post_id;
                $post_other_info[$a]["blog_id"]     = $blog_id;
                $post_other_info[$a]["topic_id"]    = $topic_id;
                $post_other_info[$a]["post_author"] = $post_author;
                $post_other_info[$a]["thumbnail"]["small"] = LP_get_post_thumb_url($post_id, "","lp-small");
                $post_other_info[$a]["thumbnail"]["medium"] = LP_get_post_thumb_url($post_id, "","lp-medium");
                $post_other_info[$a]["thumbnail"]["large"] = LP_get_post_thumb_url($post_id, "","lp-Large");
				$post_other_info[$a]["thumbnail"]["flarge"] = LP_get_post_thumb_url($post_id, "","large");
				$post_other_info[$a]["thumbnail"]["fmedium"] = LP_get_post_thumb_url($post_id, "","medium");
				$post_other_info[$a]["thumbnail"]["fsmall"] = LP_get_post_thumb_url($post_id, "","small");
				
				$post_other_info[$a]["thumbnail"]["tall"] =  json_decode(get_post_meta($post_id, '_LP_flip_img', true),true);
				if(is_array($post_other_info[$a]["thumbnail"]["tall"])){
					$post_other_info[$a]["thumbnail"]["tall"]["img"] = lp_flip_dir($blog_id)."/".$post_other_info[$a]["thumbnail"]["tall"]["img"];
				}
                
                $user_info = get_blog_option($blog_id,"LP_linkedin_info");
				$profile_pic = ($user_info["linkedin_profile_pic"] ? $user_info["linkedin_profile_pic"] : $user_info["linkedin_profile_thumb"]);
				if($profile_pic){
					$author_avatar = "<img src=\"".$profile_pic."\" width=\"80\" height=\"80\"/>";
				}else{
					$author_avatar = get_avatar($user_email, 72, $default_avatar);
				}
                $post_other_info[$a]["author_avatar"] = $author_avatar;
                
                $the_topic_link = get_permalink($topic_id);
                $post_other_info[$a]["topic_link"] = $the_topic_link;
                
                $user = get_userdata($post_author);
                $user_full_name = $user->data->display_name; 
                $post_other_info[$a]["user_full_name"] = $user_full_name;
            restore_current_blog();
            $a++;
        }
        echo json_encode($post_other_info);
    die();
}
add_action('wp_ajax_LP_get_post_other_info', 'LP_get_post_other_info');
add_action('wp_ajax_nopriv_LP_get_post_other_info', 'LP_get_post_other_info');

function set_featured_img($post_id, $blog_id, $ufile, $delete = true, $isajax = true)
{
    if(is_user_logged_in()){
        $f_type = strtolower($ufile["type"]);
        if($f_type == "image/png" || $f_type == "image/jpg" || $f_type == "image/jpeg" || $f_type == "image/gif"){
            global $wpdb;
            global $switched;
            
            global $current_site;
            $user_info = get_userdata(get_current_user_id());
            $file      = explode("/",$ufile["file"]);
            // print_r($file);
            // $user_ID = get_current_user_id(); 
            // $user_blogs = get_blogs_of_user( $user_ID );
            switch_to_blog($blog_id);
             
            $wp_upload_dir = wp_upload_dir();
            // print_r($wp_upload_dir);
            $filename = end($file);
            $to_new_file = $wp_upload_dir["path"]."/".$filename;
            $a = 0;
            while(file_exists($to_new_file)){
                // echo "File Exists : ".$to_new_file."<br />";
                $a++;
                $exp_name = explode(".",$filename);
                $c = count($exp_name);
                $exp_name[$c-2].="_".$a;
                $filename = implode(".",$exp_name);
                $to_new_file = $wp_upload_dir["path"]."/".$filename;
            }

		   // echo $to_new_file."\n\r";
			if(!file_exists($wp_upload_dir["path"]))
				mkdir($wp_upload_dir["path"]);
		   
		   // $plugin_dir_path = plugin_dir_path(__FILE__);
		   // $npath = $plugin_dir_path."temp_upload/temp_".$user_ID."/".end($file);
		   $npath = $ufile["file"];
		   // echo $npath."\n\r";
		   // var_dump(file_exists($npath));
		   // echo "\n\r".$to_new_file."\n\r";
           if(copy($npath,$to_new_file)){
				if($delete)	unlink($npath);
				$wp_filetype = wp_check_filetype(basename($filename), null );
				$guid = $wp_upload_dir['url'] . '/' . $filename;
				$guid = explode("wp-content",$guid);
				$guid[0]=$guid[0]. $user_info->user_nicename."/";
				$guid_ = implode("wp-content",$guid);
				$attachment = array(
					'guid' => $guid_ , 
					'post_mime_type' => $wp_filetype['type'],
					'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
					'post_content' => '',
					'post_status' => 'inherit'
				);
				
				// print_r($attachment);
				$attach_id = wp_insert_attachment( $attachment , $wp_upload_dir['subdir']."/".$filename );
				// you must first include the image.php file
				// for the function wp_generate_attachment_metadata() to work
				require_once(ABSPATH . 'wp-admin/includes/image.php');
				$attach_data = wp_generate_attachment_metadata( $attach_id, $wp_upload_dir['path'] . '/' . $filename );
				// print_r($attach_data);
				wp_update_attachment_metadata( $attach_id, $attach_data );
				$res = update_post_meta($post_id, '_thumbnail_id', $attach_id);
				restore_current_blog();
				if($res) 
					if($isajax) die();
					else return true;
				else{ 
					if($isajax) die("-0");
					else return false;
				}
			}else{
				if($isajax) die("0-");
				else return false;
			}
        }
    }
    if($isajax) die("0-");
	else return false;
}

add_action('wp_ajax_LP_user_topics', 'LP_user_topics');
function LP_user_topics(){
	global $switched;
	$blog_id = LP_get_user_blog_id();
	switch_to_blog($blog_id);
	if(isset($_POST["topic"]) && $_POST["topic"]!=""){
		$topic_id = $_POST["topic"];
		$deep = true;
	}else{
		$topic_id = 0;
		$deep = false;
	}
    $topics = LP_get_user_topics($topic_id, $deep);
    if($topics){
			echo json_encode($topics);
    }else{
        echo "0";
    }
	restore_current_blog();
    die();
}

function LP_udpate_post($post_id, $post_content, $post_title, $post_excerpt=""){
    if(is_user_logged_in()){
        global $switched;
        $blog_id = LP_get_user_blog_id();
        $db_name = LP_get_blog_db_name($blog_id);

        $my_args = Array(
            "ID"            => $post_id,
            "post_content"  => $post_content,
            "post_title"    => $post_title,
            "post_excerpt"  => $post_excerpt
        );
        
        switch_to_blog($blog_id);
            wp_update_post( $my_args );
        restore_current_blog();
    }
}

function LP_update_topic_submit(){
    if(is_user_logged_in()){
		$user_id = get_current_user_id(); 
        $post_id        = $_POST["post_id"];
        $channel        = $_POST["channel"];
        $post_title     = $_POST["title"];
        $post_content   = $_POST["description"];
        $session_name   = $_POST["session_name"];
        // print_r($_SESSION[$session_name]);
        $blog_id        = LP_get_user_blog_id();
        LP_udpate_post($post_id, $post_content, $post_title);
        update_post_meta($post_id, 'LP_channel', $channel);
        $topic = LP_get_user_topics($post_id);
        if($topic){
            echo json_encode($topic[0]);
        }else{
            echo "0";
        }
        if(isset($_SESSION[$session_name]["file"])){
            $img = $_SESSION[$session_name];
            unset($_SESSION[$session_name]);
			
			$file = explode("/",$img["file"]);
			$plugin_dir_path = plugin_dir_path(__FILE__);
			
			$npath = $plugin_dir_path."temp_upload/temp_".$user_id."/".end($file);
			$img["file"] = $npath;
            set_featured_img($post_id, $blog_id ,$img);
        }
        die();
    }
    die("0");
}
add_action('wp_ajax_LP_update_topic_submit', 'LP_update_topic_submit');


/*** 
 *
 * INSERTS a comment to the main drip 
 */
function LP_submit_comment(){
    if(is_user_logged_in()){
        $time = current_time('mysql');
        $post_id = $_POST["post_id"];
        $blog_id = $_POST["blog_id"];
		$comment = $_POST["comment"];
        if($post_id!="" && $blog_id!="" && $comment!=""){
			$parent = LP_get_drip_parent($post_id, $blog_id);
			if($parent["post_id"]){
				$parent_post_id = $parent["post_id"];
				$parent_blog_id = $parent["blog_id"];
			}else{
				$parent_post_id = $post_id;
				$parent_blog_id = $blog_id;
			}
			$user_id = get_current_user_id();
			switch_to_blog($parent_blog_id);
				$data = array(
					'comment_post_ID' => $parent_post_id,
					'comment_content' => $comment,
					'user_id'         => $user_id
				);

				$com_id = wp_insert_comment($data);
			restore_current_blog();
			if($com_id){
				$return["comment_id"]	= $com_id;
				$return["request"]   	= $_POST;
				$return["pb"]          	= $post_id."-".$blog_id;
				$return["user_avatar"] 	= LP_get_user_avatar($user_id, 40);
				echo json_encode($return);
			}else{
				echo "0";
			}
		}else{
			echo "0";
		}
    }else{
        echo "0";
    }
    die();
}
add_action('wp_ajax_LP_submit_comment', 'LP_submit_comment');

function LP_fetch_drip_list()
{
    $member_chans = "";
    if(is_user_logged_in()){
        $member_chans = LP_get_user_channels();
    }
    echo json_encode(LP_latest_posts($member_chans));
    die();
}
add_action('wp_ajax_LP_fetch_drip_list', 'LP_fetch_drip_list');
add_action('wp_ajax_nopriv_LP_fetch_drip_list', 'LP_fetch_drip_list');

add_action('wp_ajax_LP_fetch_drip_data', 'LP_fetch_drip_data');
function LP_fetch_drip_data()
{
	if(is_user_logged_in()){
		$post_id = $_POST["post_id"];
		$blog_id = $_POST["blog_id"];
		if(!$blog_id || $blog_id="")$blog_id = LP_get_user_blog_id();
		$drip = LP_get_drip($post_id, $blog_id);
			$drip["thumbnail"]["small"]= LP_get_post_thumb_url($post_id, $blog_id,"lp-small");
			$drip["thumbnail"]["fmedium"]= LP_get_post_thumb_url($post_id, $blog_id,"lp-medium");
			
			$drip["blog_id"] = $blog_id;
			$user_topics = LP_get_user_topics();
			$topics = "";
			foreach($user_topics as $key => $u_topic){
                if($post_topic["ID"] == $u_topic["ID"]){
                    $selected = "selected";
                }else{
                    $selected = "";
                }
            $topics.= "<option value=\"".$u_topic["ID"]."\">".$u_topic["post_title"]."</option>";
            }
            
            $num = 5;
			$drip["topics"]           = $topics;
            $drip["latest_tags"]      = LP_get_recommended_tags($post_id, $blog_id, "recent", $num);
            $recommended_tags         = LP_get_recommended_tags($post_id, $blog_id);
			// print_r($recommended_tags);
			$extags = array();
            if(count($recommended_tags) < $num){
                $the_post = get_blog_post($blog_id,$post_id);
                $extra_k = Array();
                foreach($recommended_tags as $tags){
                    $extra_k[] = $tags["name"]; 
                }
                $extags = extractCommonWords(strip_tags($the_post->post_title)." ".strip_tags($the_post->post_content),$extra_k);
            }
            $drip["recommended_tags"] = array_merge($recommended_tags, $extags);
			
			$user_blog_id = LP_get_user_blog_id();
			
			$industries = explode(",",get_blog_option($user_blog_id, "_LPin_industries"));
			
			$my_industries = array();
			foreach($industries as $industry){
				$my_industries[] = get_blog_option($user_blog_id, "_LPin_connection_".$industry);
			}
			
			$drip["my_industries"] = $my_industries;
			echo json_encode($drip);
	}
	die();
}

/**
 * Retrieve the user's message setings. From message page. Executed upon the 'Accept buttons is clicked'
 */
add_action('wp_ajax_LP_fetch_linkedin_message', 'LP_fetch_linkedin_message');
function LP_fetch_linkedin_message(){
    global $wpdb,$db_servers;
    if(is_user_logged_in()){
        $user_id = get_current_user_id();
        $blog_id = LP_get_user_blog_id();
        $total = 0;
        if(isset($_POST["get_all"])){
            $num = $_POST["page"] - 1;
            $linkedin_message = "";
        }else{
            $num = 0;
            $linkedin_message = LP_get_user_blog_option("LINKEDIN_MESSAGE");
            if(is_array($linkedin_message)){
                $linkedin_message["subject"] = stripslashes($linkedin_message["subject"]);
                $linkedin_message["body"] = stripslashes($linkedin_message["body"]);
                $linkedin_message = json_encode($linkedin_message);
            }


            switch_to_blog($blog_id);
            $db_name = LP_get_blog_db_name($blog_id);
            $prefix =  $wpdb->prefix;
            restore_current_blog();
            $sql = "SELECT count(*) FROM `{$db_name}`.`{$prefix}linkedin_connections`";
            $total = $wpdb->get_var($sql);
        }

        $lin_contacts = LP_get_user_linkedin_connections_from_linkedin('', false, $num);

//        if($_POST["get_all"] == "all"){
//            $sliced = array_slice($lin_contacts["contacts"], 50);
//            $lin_contacts["contacts"] = $sliced;
//        }else{
//            $sliced = array_slice($lin_contacts["contacts"], 0, 50);
//            $lin_contacts["contacts"] = $sliced;
//        }
        $toggle_type = get_blog_option($blog_id, "_LPin_toggled_type");

        echo json_encode(array("message_tpl" => $linkedin_message, "linkedin_connections" => $lin_contacts, "toggle_type" => $toggle_type ,"toggled_connections" => $toggled_connections["all"], "page" => $_POST["page"], "total" => $total));
    }
    die();
}

/**
 * This will grab all the user's LinkedIn connections. Executed upon clicking the grab button on the message page.
 */
add_action('wp_ajax_LP_grab_all_linkedin_connections', 'LP_grab_all_linkedin_connections');
function LP_grab_all_linkedin_connections()
{
    if(is_user_logged_in()){
        global $switched, $wpdb, $db_servers;
        $blog_id = LP_get_user_blog_id();
        switch_to_blog($blog_id);
        $db_name = LP_get_blog_db_name($blog_id);
        $prefix =  $wpdb->prefix;
        restore_current_blog();
        $connections = LP_get_user_linkedin_connections_from_linkedin("", false, "", true);
        $sql = "CREATE TABLE IF NOT EXISTS `{$db_name}`.`{$prefix}linkedin_connections` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `linkedin_id` varchar(250) NOT NULL,
        `firstName` varchar(250) NOT NULL,
        `lastName` varchar(250) NOT NULL,
        `industry` varchar(250) NOT NULL,
        `messaging` int(11) NOT NULL,
        `sent_count` int(11) NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `linkedin_id` (`linkedin_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $wpdb->query($sql);
        foreach($connections["contacts"] as $conn){
            $q = "INSERT INTO `{$db_name}`.`{$prefix}linkedin_connections` SET `linkedin_id` = '{$conn["id"]}', `firstName` = '{$conn["firstName"]}', `lastName` = '{$conn["lastName"]}', `industry` = '{$conn["industry"]}', `messaging` = 0, `sent_count` = 0";
            //echo $q."\n\r";
            $wpdb->query($q);
        }
        echo json_encode($connections);
    }
    die();
}

add_action('wp_ajax_LP_update_toggled_connections', 'LP_update_toggled_connections');
function LP_update_toggled_connections()
{
    if(is_user_logged_in()){
        global $industries,$switched, $wpdb, $db_servers;
        $blog_id = LP_get_user_blog_id();
        switch_to_blog($blog_id);
        $db_name = LP_get_blog_db_name($blog_id);
        $prefix =  $wpdb->prefix;
        restore_current_blog();
        if($_POST["connections"] == "all"){
            $sql = "UPDATE `{$db_name}`.`{$prefix}linkedin_connections` SET `messaging` = 1";
            $wpdb->query($sql);
        }elseif(is_array($_POST["connections"])){
            $sql = "UPDATE `{$db_name}`.`{$prefix}linkedin_connections` SET `messaging` = 0";
            $wpdb->query($sql);
            if(count($_POST["connections"]) > 0){
                $WHERE = "WHERE `linkedin_id` IN('".implode("','",$_POST["connections"])."')";
                $sql = "UPDATE `{$db_name}`.`{$prefix}linkedin_connections` SET `messaging` = 1 {$WHERE}";
                //echo $sql;
                $wpdb->query($sql);
            }
        }elseif($_POST["connections"] == "none"){
            $sql = "UPDATE `{$db_name}`.`{$prefix}linkedin_connections` SET `messaging` = 0";
            $wpdb->query($sql);
        }
        LP_save_linkedin_message();
    }
    die();
}

function LP_save_linkedin_message(){
	if(is_user_logged_in()){
		if($_POST["subject"]!="" && $_POST["body"]!=""){
			$message = array(
				"subject"	=> strip_tags($_POST["subject"]),
				"body"		=> strip_tags($_POST["body"])
			);
			$blog_id = LP_get_user_blog_id();
			update_blog_option($blog_id, "LINKEDIN_MESSAGE", $message);
		}
	}
}
add_action('wp_ajax_LP_save_linkedin_message', 'LP_save_linkedin_message');

add_action('wp_ajax_LP_save_redrip', 'LP_save_redrip');
function LP_save_redrip()
{
	if(is_user_logged_in()){
		$new_data = array(
			"analysis" 			=> substr(strip_tags($_POST["analysis"]),0,90),
			"topic"    			=> substr(strip_tags($_POST["topic"]),0,90),
			"ripple_content"	=> substr(strip_tags($_POST["ripple_content"]),0,400),
			"ripple_title"		=> substr(strip_tags($_POST["ripple_title"]),0,90),
			"ripple_tags"		=> strip_tags($_POST["ripple_tags"]),
            "newfile_url"       => $_POST["newfile_url"]
		);
		// print_r($_POST);
		if($new_data["analysis"]!="" && $new_data["topic"]!="" && $new_data["ripple_content"]!="" && $new_data["ripple_title"]!=""){
			if(LP_redrip_this($_POST["post_id"],$_POST["blog_id"], $new_data )) echo "1";
			else echo "0";
            die();
		}
	}
    echo "0";
	die();
}

function LP_redrip_this($post_id, $blog_id, $new_data)
{
// print_r($new_data);
	global $shardb_prefix, $wpdb;
	$topic = LP_get_user_topics($new_data["topic"]);
	if($new_data["analysis"] && $topic){
		$the_drip = get_blog_post($blog_id,$post_id);
		$user_id = get_current_user_id();					
		// if($the_drip->post_author != $user_id){	
			switch_to_blog($blog_id);
				$parent_drip = LP_get_drip_parent($post_id, $blog_id);
				if($parent_drip["id"]){
					$parent_drip_id = $parent_drip["id"];
				}else{
					$parent_drip_id = $parent_drip["drip_id"];
				}

                if($new_data["newfile_url"]==""){
                    $thumb_id = get_post_thumbnail_id($the_drip->ID);
                    $thumb_file = get_post_meta( $thumb_id, "_wp_attached_file", true );
                    $wp_upload_dir = wp_upload_dir();
                    $img["file"] = "/".trim($wp_upload_dir["basedir"])."/".$thumb_file;
                    $img["type"] = "image/jpeg";
                }else{
                    $plugin_dir_path = plugin_dir_path(__FILE__);                   
                    $plugin_dir = "/".trim($plugin_dir_path,"/")."/temp_upload/temp_".$user_id;
					if(!file_exists($plugin_dir))
						mkdir($plugin_dir);
                    
                    if(trim($new_data["ripple_tags"])!=""){
                        $rtags = explode(",", $new_data["ripple_tags"]);
                        $r = 0;
                        $nName = "";
                        $sep = "";
                        foreach($rtags as $tag){
                            if($r == 3) break;
                            $nName.= $sep.$tag;
                            $sep = "-";
                            $r++;
                        }
                    }else{
                         $nName = rand(100,10000);
                    }
                    $nName.="-".rand(100,10000);
                    
                    $ext = explode(".",$new_data["newfile_url"]);
                    $newfile = $plugin_dir ."/".$nName.".".end($ext);
                    if(LP_download_file_from_url($new_data["newfile_url"], $newfile)===false){
                        @mysql_query("ROLLBACK", $wpdb->dbh);
						// echo "a";
                        return false;
                    }else{
                        $img["file"] = $newfile;
                        $img["type"] =  mime_content_type ($newfile);
                    }
                }
			restore_current_blog();
			$new_drip = array(
				'comment_status' => 'open', // 'closed' means no comments.
				'ping_status'    => 'open', // 'closed' means pingbacks or trackbacks turned off
				'post_author'    => $user_id, //The user ID number of the author.
				'post_content'   => $new_data["ripple_content"], //The full text of the post.
				'post_excerpt'   => $new_data["analysis"], //For all your post excerpt needs.
				'post_status'    => 'publish', //Set the status of the new post.
				'post_title'     => $new_data["ripple_title"], //The title of your post.
				'post_type'      => 'post', //You may want to insert a regular post, page, link, a menu item or some custom post type
				'post_parent'    => $new_data["topic"]
				);
			$new_blog = LP_get_user_blog_id();
			switch_to_blog($new_blog);
				remove_action('save_post', 'my_meta_save');
					@mysql_query("BEGIN", $wpdb->dbh);
					$new_id = wp_insert_post( $new_drip );
					if($new_id){
						if(trim($new_data["ripple_tags"])!=""){
							wp_set_post_tags( $new_id, $new_data["ripple_tags"], true );
						}
						$res = add_post_meta($new_id, 'LP_channel', $topic[0]["channel_id"]);
						$res_t = add_post_meta($new_id, '_LP_topic', $new_data["topic"]);
						$global_db = $shardb_prefix."global";
						$prefix = $wpdb->base_prefix;
						
						$sql = $wpdb->prepare("INSERT INTO `{$global_db}`.`{$prefix}lp_drips` SET `parent` = %d,`post_id` = %d, `blog_id` = %d, `post_type` = %s, `story_URL`= %s, `channel_id` = %d, `post_date` = %s", $parent_drip_id, $new_id, $new_blog,'post',$parent_drip["URL"],$topic[0]["channel_id"], date("Y-m-d H:i:s"));
						// echo $sql."\n\r";
						$reslt = $wpdb->query($sql);
						if($reslt===false){
							@mysql_query("ROLLBACK", $wpdb->dbh);
							// echo "b";
							return false;
						}else{
							$sql = $wpdb->prepare("UPDATE `{$global_db}`.`{$prefix}lp_drips` SET `redrips`=(`redrips`+1) WHERE `id` = %d ",$parent_drip_id);
							// echo $sql."\n\r";
							$res2 = $wpdb->query($sql);
							if($res2===false){
								@mysql_query("ROLLBACK", $wpdb->dbh);
								// echo "c";
								return false;
							}else{                              
                                
                                /* SETTING FEATURED IMAGE */
                                if(set_featured_img($new_id, $new_blog ,$img, true, false)){
								// set_featured_img($post_id, $blog_id, $ufile, $delete = true, $isajax = true)
                                        @mysql_query("COMMIT", $wpdb->dbh);
                                        
                                        /* **** SENDING oF MESSAGE **** */
                                        $linkedin_message = LP_get_user_blog_option("LINKEDIN_MESSAGE");
                                        if(!is_array($linkedin_message)){
                                            $message = json_decode($linkedin_message,true);
                                        }else{
                                            $message = $linkedin_message;
                                        }
                                        $user_info = get_userdata($user_id);
                                        $member = array(
                                            "user_firstname" => $user_info->user_firstname,
                                            "user_lastname"	 => $user_info->user_lastname
                                        );
                                        
                                        $contacts = array(
                                                        array(
                                                            "ID" 			=> "W0nFy6wBgs", 
                                                            "first_name" 	=> "Ronnel", 
                                                            "last_name" 	=> "Anasco",
                                                            "industry"		=> "Computers"
                                                            ),
                                                        array(
                                                            "ID" 			=> "aleNWGQy63", 
                                                            "first_name" 	=> "mandy", 
                                                            "last_name" 	=> "Deng",
                                                            "industry"		=> "Events Services"
                                                            ),
                                                        array(
                                                            "ID" 			=> "lREJVtS0u1", 
                                                            "first_name" 	=> "Loui R.", 
                                                            "last_name" 	=> "Byrdziak",
                                                            "industry"		=> "Hospitality"
                                                            )														
                                                    );
                                        
                                        $drip 	= $new_drip;
                                        $drip["topic_name"] = $topic[0]["post_title"];
                                        $drip["channel_name"] = $topic[0]["name"];
                                        $drip["drip_url"] = Lp_get_drip_url($drip);
                                        
                                        foreach($contacts as $contact){
                                            $args = array(
                                                    "message"	=> $message,
                                                    "contact"	=> $contact,
                                                    "member"	=> $member,
                                                    "drip"		=> $drip
                                                );
                                            $details = LP_process_message_template($args);
                                            // if(LP_send_this_message_to_linkedin(array($details["contact"]["ID"]), $details["message"])){
                                                // LP_record_message_sent($args);
                                            // }
                                        }
                                        
                                        /* **** END SENDING oF MESSAGE **** */
										// echo "d";
                                        return true;
                                    }
                                /* END SETTING FEATURED IMAGE */
                                
							}							
						}
					}else{
						@mysql_query("ROLLBACK", $wpdb->dbh);
						// echo "e";
						return false;
					}
				add_action('save_post','my_meta_save');
			restore_current_blog();
		// }
	}
	// echo "f";
	return false;
}

add_action('wp_ajax_LP_save_fresh_drip', 'LP_save_fresh_drip');
function LP_save_fresh_drip()
{
/*print_r($_POST);
print_r($_FILES);*/

	if(is_user_logged_in()){
		$new_data = array(
			"analysis" 			=> substr(strip_tags($_POST["analysis"]),0,90),
			"LP_topic"    		=> $_POST["topic"],
			"ripple_content"	=> substr(strip_tags($_POST["ripple_content"]),0,400),
			"ripple_title"		=> substr(strip_tags($_POST["ripple_title"]),0,90),
			"ripple_tags"		=> strip_tags($_POST["ripple_tags"]),
            "post_status"       => $_POST["post_status"],
            "story_URL"         => $_POST["story_URL"]
		);
		
		$user_id = get_current_user_id();	
		$blog_id = LP_get_user_blog_id();
		switch_to_blog($blog_id);	
			$plugin_dir_path = plugin_dir_path(__FILE__);                   
			$plugin_dir = "/".trim($plugin_dir_path,"/")."/temp_upload/temp_".$user_id;
			if(!file_exists($plugin_dir))
				mkdir($plugin_dir);
			
			if(trim($new_data["ripple_tags"])!=""){
				$rtags = explode(",", $new_data["ripple_tags"]);
				$r = 0;
				$nName = "";
				$sep = "";
				foreach($rtags as $tag){
					if($r == 3) break;
					$nName.= $sep.$tag;
					$sep = "-";
					$r++;
				}
			}else{
				 $nName = rand(100,10000);
			}
            if(isset($_POST["f_img1"])){
                $path1 = $_POST["f_img1"];
            }else{
                $path1 = $_FILES["f_img1"]['name'];
            }
			$ext = reset(explode("?",end(explode(".",$path1))));
            $nName = preg_replace('/[^a-zA-Z0-9-_]/','', $nName);
			$newfile = $plugin_dir ."/".$nName.".".($ext);
			
			while(file_exists($newfile)){
				$nName2 = $nName."-".rand(100,100000);
				$newfile = $plugin_dir ."/".$nName2.".".($ext);
			}

			$image1_secured = false;
			// To Download or Upload
			if(isset($_FILES["f_img1"]['tmp_name']) && $_FILES["f_img1"]['tmp_name'] != ""){
				// So we are uploading ...
                //echo $newfile;
				$image1_secured = move_uploaded_file($_FILES['f_img1']['tmp_name'], $newfile);
			}else{
				// Downloading the image1 from URL
				$image1_secured = LP_download_file_from_url($_POST["f_img1"], $newfile);
			}
			
			$newfile_2 = "";
			
			if($image1_secured != false){
					if(isset($_POST["f_img2"]) && $_POST["f_img2"] == "1"){
						// Use the Downloaded/Uploaded image of image1 for image2
						$newfile_2 = $newfile;
					}elseif(isset($_POST["img2"])){
						if(trim($new_data["ripple_tags"])!=""){
							$rtags = explode(",", $new_data["ripple_tags"]);
							$r = 0;
							$nName = "";
							$sep = "";
							foreach($rtags as $tag){
								if($r == 3) break;
								$nName.= $sep.$tag;
								$sep = "-";
								$r++;
							}
						}else{
							 $nName = rand(100,10000);
						}

                        if(isset($_POST["f_img2"])){
                            $path2 = $_POST["f_img2"];
                        }else{
                            $path2 = $_FILES["f_img2"]['name'];
                        }

						$ext = reset(explode("?",end(explode(".",$path2))));
                        $nName = preg_replace('/[^a-zA-Z0-9-_]/','', $nName);
						$newfile_2 = $plugin_dir ."/".$nName.".".($ext);

						while(file_exists($newfile_2)){
							$nName2 = $nName."-".rand(100,100000);
							$newfile_2 = $plugin_dir ."/".$nName2.".".($ext);
						}
						
						$image2_secured = false;
						// To Download or Upload
						if(isset($_FILES["f_img2"]) && $_FILES["f_img2"]['tmp_name'] != ""){
							// So we are uploading ...
							$image2_secured = move_uploaded_file($_FILES['f_img2']['tmp_name'], $newfile_2);
						}else{
							// Downloading the image2 from URL
							$image2_secured = LP_download_file_from_url($_POST["f_img2"], $newfile_2);
						}
						
						if($image2_secured == false){
                            unlink($newfile);
                            $err = array("error" => "An error occurred while trying to download/upload the 2nd image. Please try to use a different image...");
                            echo json_encode($err);
                            die();
						}
					}

				$scaled_width_1 = ($_POST["img1"]["scaled_width"]) / .55;
				$left_1 = ($_POST["img1"]["left"] * -1) / .55;
				$top_1 = ($_POST["img1"]["top"] * -1) / .55;
				$the_image = LP_cropper($newfile, $scaled_width_1, 495, 275, $left_1, $top_1, true);

				if($the_image != false && $newfile_2 != "" ){
					$scaled_width_2 = ($_POST["img2"]["scaled_width"]) / .55;
                    $image_info = getimagesize($newfile_2);
                    $o_w = $image_info[0];
                    $o_h = $image_info[1];
                    $s_height = $scaled_width_2 / ($o_w/$o_h);

					$left_2 = ($_POST["img2"]["left"] * -1)  / .55;
					$top_2 = $_POST["img2"]["top"]  / .55;
					$towidth_2 = ($_POST["img2"]["towidth"])  / .55;
					$toheight_2 = ($_POST["img2"]["toheight"])  / .55;

                    if( ($top_2 + $s_height) > $toheight_2){
                        $the_height = $s_height - ( ($top_2 + $s_height) - $toheight_2);
                        $the_top = 0;
                        if($the_height > $toheight_2){
                            $the_height = $toheight_2;
                            $the_top = $top_2 * -1;
                        }
                    }elseif($top_2 >= 0){
                        $the_height = $s_height;
                        $the_top = 0;
                    }else{
                        $the_top = $top_2 * -1;
                        $the_height = $s_height - $the_top;
                    }

					$the_image_2 = LP_cropper($newfile_2, $scaled_width_2, $towidth_2, $the_height, $left_2, $the_top, false);

                    if($the_image_2 == false){
                        unlink($the_image);
                        unlink($newfile_2);
                        $err = array("error" => "An error occurred while trying to resize and crop the 2nd image. Please try submitting this driplet again or use a different image...");
                        echo json_encode($err);
                        die();
					}else{
						$plugin_dir2 = "/".trim($plugin_dir_path,"/")."/flip_imgs/u_".$blog_id;
						if(!file_exists($plugin_dir2))
							mkdir($plugin_dir2);
							
						$tf = explode(".",$newfile_2);
						$tf[count($tf)-2].="_".$towidth_2."x".$toheight_2;
						$xtarget = implode(".",$tf);
						
						$fn = explode("/",$xtarget);
						$target = $plugin_dir2."/".end($fn);
						
						while(file_exists($target)){
							$nName2 = rand(100,100000)."-".end($fn);
							$target = $plugin_dir2 ."/".$nName2;
						}
						$extension = strtolower(pathinfo($target, PATHINFO_EXTENSION));
						if(trim($extension) == "png"){
							if(imagepng($the_image_2, $target) == false){
                                $err = array("error" => "Fail to write png file...");
                                echo json_encode($err);
                                die();
                            }
						}elseif(trim($extension) == "jpg" || $extension == "jpeg"){
							if(imagejpeg($the_image_2, $target) == false) {
                                $err = array("error" => "Fail to write jpg file...");
                                echo json_encode($err);
                                die();
                            }
						}elseif(trim($extension) == "gif"){
                            if(imagegif($the_image_2, $target) == false) {
                                $err = array("error" => "Fail to write gif file...");
                                echo json_encode($err);
                                die();
                            }
                        }elseif(trim($extension) == "bmp"){
                            if(imagebmp($the_image_2, $target) == false) {
                                $err = array("error" => "Fail to write bmp file...");
                                echo json_encode($err);
                                die();
                            }
                        }
						
						$exf = explode("/",$target);
						$to_post_meta["img"] = end($exf);
						$img_info = getimagesize($target);
						unset($img_info[3]);
						$to_post_meta["info"] = $img_info;
						$to_post_meta = json_encode($to_post_meta);
						$image_2 = $newfile_2;
						unlink($newfile_2);
					}
				}else{
                    if($the_image == false){
                        $err = array("error" => "An error occurred while trying to resize and crop the 1st image. Please try submitting this driplet again or use a different image...");
                        echo json_encode($err);
                        die();
                    }
                }
			}else{
                $err = array("error" => "An error occurred while trying to download/upload the 1st image. Please try to use a different image...");
                echo json_encode($err);
                die();
			}
			
		restore_current_blog();
		if($the_image != false){
			unlink($newfile);
			$new_data["img1"]["path"] = $the_image;
			remove_action('save_post', 'my_meta_save');
				$new_id = LP_fresh_drip($blog_id, $new_data );
			add_action('save_post','my_meta_save');
			if($new_id !== false){
				global $wpdb, $shardb_prefix;
				$global_db = $shardb_prefix."global";
				$db_name = LP_get_blog_db_name($blog_id);
				$prefix = $wpdb->base_prefix;
				$topic = LP_get_user_topics($_POST["LP_topic"]);

                if($_POST["post_status"] == "drip"){
                    $sql = $wpdb->prepare("INSERT INTO `{$global_db}`.`{$prefix}lp_drips` SET `parent` = %d,`post_id` = %d, `blog_id` = %d, `post_type` = %s, `story_URL`= %s, `channel_id` = %d, `post_date` = %s", 0, $new_id, $blog_id,'post',$_POST["story_URL"],$topic[0]["channel_id"], date("Y-m-d H:i:s"));
                    $reslt = $wpdb->query($sql);
                }

                switch_to_blog($blog_id);
                    if($image_2!=""){
                        add_post_meta($new_id, '_LP_flip_img', $to_post_meta);
                    }

                    // "drip" means this drip is added to buffer, and were returning all the buffer drips from this topic.
                    /*if($_POST["post_status"] == "drip"){
                        $t_drips["is_buffer"] = 1;
                    }else{
                        $t_drs["is_buffer"] = 0;
                    }


                    /*$t_drips["future_drips"] = LP_fetch_user_future_drips($_POST["LP_topic"]);
                    if($t_drips["future_drips"] !== false){
                        $t_drips["drip_stats"] = LP_get_topic_stats($_POST["LP_topic"], $blog_id);
                        echo json_encode($t_drips);
                    }else{
                        $err = array("msg" => "no future drips");
                        echo json_encode($err);
                        die();
                    }*/

                     if($_POST["post_status"] != "drip"){
                         LP_publish_future_drip($new_id);
                     }
                restore_current_blog();
			}else{
                $err = array("error" => "Fail to create Driplet");
                echo json_encode($err);
                die();
			}
		}else{
            $err = array("error" => "Fail to crop image 1...");
            echo json_encode($err);
            die();
		}
	}
    if(isset($_POST["is_ajax"])){
        $res = array("topic_id" => $_POST["LP_topic"]);
        echo json_encode($res);
        die();
    }else{
        echo "<script>parent.LP_close_bottom_and_update_topic(".$_POST["LP_topic"].");</script>";
    }
	die();
}

/* 
 * Saving a fresh drip 
*/
function LP_fresh_drip($blog_id, $data)
{
	if(is_user_logged_in()){
		if($data["analysis"]!="" && $data["LP_topic"]!="" && $data["ripple_content"]!="" && $data["ripple_title"]!="" && ($data["newfile_url"]!="" || $data["img1"] != "") && $data["story_URL"]!=""){

			$user_id = get_current_user_id();	
			if($data["post_status"] == "drip"){
				$post_status = "draft";
			}else{
				$post_status = "publish";
			}
			$new_drip = array(
				'comment_status' => 'open', // 'closed' means no comments.
				'ping_status'    => 'open', // 'closed' means pingbacks or trackbacks turned off
				'post_author'    => $user_id, //The user ID number of the author.
				'post_content'   => $data["ripple_content"], //The full text of the post.
				'post_excerpt'   => $data["analysis"], //For all your post excerpt needs.
				'post_status'    => $post_status, //Set the status of the new post.
				'post_title'     => $data["ripple_title"], //The title of your post.
				'post_type'      => 'post', //You may want to insert a regular post, page, link, a menu item or some custom post type
				'post_parent'    => $data["LP_topic"]
				);
			switch_to_blog($blog_id);				
				if(isset($data["img1"]) && $data["img1"] != ""){
					$img["file"] = $data["img1"]["path"];
					$img["type"] =  mime_content_type ($data["img1"]["path"]);
				}else{
					$plugin_dir_path = plugin_dir_path(__FILE__);                   
					$plugin_dir = "/".trim($plugin_dir_path,"/")."/temp_upload/temp_".$user_id;
					if(!file_exists($plugin_dir))
						mkdir($plugin_dir);
					
					if(trim($data["ripple_tags"])!=""){
						$rtags = explode(",", $data["ripple_tags"]);
						$r = 0;
						$nName = "";
						$sep = "";
						foreach($rtags as $tag){
							if($r == 3) break;
							$nName.= $sep.$tag;
							$sep = "-";
							$r++;
						}
					}else{
						 $nName = rand(100,10000);
					}
					$ext = explode(".",$data["newfile_url"]);
					$newfile = $plugin_dir ."/".$nName.".".end($ext);
					
					while(file_exists($newfile)){
						$nName2 = $nName."-".rand(100,100000);
						$newfile = $plugin_dir ."/".$nName2.".".end($ext);
					}
					if(LP_download_file_from_url($data["newfile_url"], $newfile)===false){
						return false;
					}
					
					$img["file"] = $newfile;
					$img["type"] =  mime_content_type ($newfile);
				}
				$new_id = wp_insert_post( $new_drip );
				$res_t = add_post_meta($new_id, '_LP_topic', $data["LP_topic"]);
				
			restore_current_blog();
				if(set_featured_img($new_id, $blog_id ,$img, true, false)){
					return $new_id;
				}else{
					return false;
				}
		}else{
			return false;
		}
	}else{
		return false;
	}
}

function LP_record_message_sent($linkedin_connection_id)
{
    foreach($linkedin_connection_id as $id){
        global $switched, $wpdb, $db_servers;
        $blog_id = get_current_blog_id();
        switch_to_blog($blog_id);
        $db_name = LP_get_blog_db_name($blog_id);
        $prefix =  $wpdb->prefix;
        restore_current_blog();

        $sql = "UPDATE `{$db_name}`.`{$prefix}linkedin_connections` SET `sent_count`=(`sent_count`+1) WHERE `linkedin_id` = '$id'";
        $wpdb->query($sql);
        $users = get_users(array("blog_id" => $blog_id));
        $user_id = $users[0]->ID;
        update_user_meta($user_id,"_LP_msg_date",current_time( 'mysql', 1 ));
    }
}
/* 
 * SEND message to the list of contacts 
 * $contacts can be an array of contact linkedin IDs or just a string of the single linkedin user ID
 * $message is an array like array("subject" => "", "body" => "")
 */
function LP_send_this_message_to_linkedin($blog_id, $contacts, $message)
{
    try{
        $API_CONFIG = json_decode(LINKEDIN_API_CONFIG,true);
        // print_r($message);
        require_once('linkedin/linkedin_3.2.0.class.php');
        $OBJ_linkedin = new LinkedIn($API_CONFIG);
        $blog_info = get_blog_option($blog_id,"LP_linkedin_info");
        $utoken = maybe_unserialize($blog_info["linkedin_TokenAccess"]);
        $OBJ_linkedin->setTokenAccess($utoken);

        $response = $OBJ_linkedin->message($contacts, $message["subject"], $message["body"], false);

        if($response['success'] === TRUE) {
          // message has been sent
            LP_record_message_sent($contacts);
            return true;
        } else {
            return false;
            // an error occured
    //		echo "Error sending message:<br /><br />RESPONSE:<br /><br /><pre>" . print_r($response, TRUE) . "</pre><br /><br />LINKEDIN OBJ:<br /><br /><pre>" . print_r($OBJ_linkedin, TRUE) . "</pre>";
    //        die();
        }
    }catch(Exception $e){
        echo "Error message from LP_send_this_message_to_linkedin() : ".$e->getMessage()." \n\r";
    }
}

/* 
 *$args are mixed array of message, contacts .....
 */
function LP_process_message_template($args)
{
	$message = $args["message"];
	$contact = $args["contact"];
	$member  = $args["member"];
	$drip    = $args["drip"];
	
	// print_r($message);
	if(!is_array($message)){
		return false;
	}
	if(is_array($contact)){
		$contact_tags = LP_process_contact_tags($contact);
	}
	
	if(is_array($member)){
		$member_tags = LP_process_member_tags($member);
	}
	
	if(is_array($drip)){
		$drip_tags = LP_process_drip_tags($drip);
	}
	
	
	foreach($contact_tags as $tag => $val){
		$message["subject"] = str_replace("[$tag]",$val,$message["subject"]);
		$message["body"] = str_replace("[$tag]",$val,$message["body"]);
	}
	
	foreach($member_tags as $tag => $val){
		$message["subject"] = str_replace("[$tag]",$val,$message["subject"]);
		$message["body"] = str_replace("[$tag]",$val,$message["body"]);
	}

	foreach($drip_tags as $tag => $val){
		$message["subject"] = str_replace("[$tag]",$val,$message["subject"]);
		$message["body"] = str_replace("[$tag]",$val,$message["body"]);
	}
	// print_r($message);
	// echo "\n\r";
	return array(
		"message"	=> $message,
		"contact"	=> $contact,
		"member"	=> $member,
		"drip"		=> $drip
	);
}

function LP_process_drip_tags($drip)
{
	$CONTACT_TAGS = json_decode(CONTACT_TAGS);
	// $drip_tags = array(
		// "DripURL",
		// "ArticleShortURL",
		// "ArticleURL",
		// "DripTitle",
		// "DripTopic",	
		// "DripChannel"
	// );
	if($drip["drip_url"]){
		$tag_val["DripURL"] = $drip["drip_url"];
	}

    if($drip["short_article_url"]){
        $tag_val["ArticleShortURL"] = $drip["short_article_url"];
    }

    if($drip["article_url"]){
        $tag_val["ArticleURL"] = $drip["article_url"];
    }
	
	if($drip["post_title"]){
		$tag_val["DripTitle"] = $drip["post_title"];
	}
	
	if(!$drip["topic_name"]){
		$topic = LP_get_post_topic($drip["post_id"],$drip["blog_id"]);
		$tag_val["DripTopic"] = $topic["post_title"];
	}else{
		$tag_val["DripTopic"] = $drip["topic_name"];
	}
	
	if(!$drip["channel_name"]){
		$channel = get_the_channel($drip["post_id"],$drip["blog_id"]);
		$tag_val["DripChannel"] = $channel["name"];
	}else{
		$tag_val["DripChannel"] = $drip["channel_name"];
	}
	
	return $tag_val;
}

function LP_process_member_tags($member)
{
	$CONTACT_TAGS = json_decode(CONTACT_TAGS);
	// $member_tags = array(
		// "MemberFullName",
		// "MemberFname",
		// "MemberLname"	
	// );
	$fname = array("fname","Fname","fName","FName","first_name","user_firstname");
	$lname = array("lname","Lname","lName","LName","last_name","user_lastname");
	
	foreach($fname as $key){
		if(isset($member[$key])){
			$tag_val["MemberFname"] = $member[$key];
			break;
		}
	}
	
	foreach($lname as $key){
		if(isset($member[$key])){
			$tag_val["MemberLname"] = $member[$key];
			break;
		}
	}
	
	$tag_val["MemberFullName"] = $tag_val["MemberFname"]." ".$tag_val["MemberLname"];
	return $tag_val;
}

function LP_process_contact_tags($contact)
{
	$CONTACT_TAGS = json_decode(CONTACT_TAGS);
	// $contact_tags = array(
		// "ContactFullName",
		// "ContactFname",
		// "ContactLname",
		// "ContactID",	
		// "ContactIndustry"
	// );
	$fname = array("fname","Fname","fName","FName","first_name","firstName");
	$lname = array("lname","Lname","lName","LName","last_name","lastName");
	$contact_id = array("ID","id","user_id");
	
	foreach($fname as $key){
		if(isset($contact[$key])){
			$tag_val["ContactFname"] = $contact[$key];
			break;
		}
	}
	
	foreach($lname as $key){
		if(isset($contact[$key])){
			$tag_val["ContactLname"] = $contact[$key];
			break;
		}
	}
	
	foreach($contact_id as $key){
		if(isset($contact[$key])){
			$tag_val["ContactID"] = $contact[$key];
			break;
		}
	}
	
	if(isset($contact["industry"])){
			$tag_val["ContactIndustry"] = $contact["industry"];
	}
	
	$tag_val["ContactFullName"] = $tag_val["ContactFname"]." ".$tag_val["ContactLname"];
	return $tag_val;
}

function LP_download_file_from_url($url, $newfile)
{
    try{
        $res = @file_put_contents($newfile, file_get_contents($url));
        if($res === false){
            return false;
        }else{
            return array("info" => $res, "newfile" => $newfile);
        }
    }catch (Exception $e){
        set_not_404();
        $succ = true;
    }
    return $succ;
}

function LP_user_has_twitter_token()
{
    if(LP_user_twitter_token()!==false){
        $res = LP_twitter_search("checking");
        if(isset($res->errors)){
            echo "0";
        }else{
            echo "1";
        }
    }else{
        echo "0";
    }
    die();
}
add_action('wp_ajax_LP_user_has_twitter_token', 'LP_user_has_twitter_token');

add_action('wp_ajax_LP_save_topic_collection_setup', 'LP_save_topic_collection_setup');
function LP_save_topic_collection_setup(){
	global $switched;
	$r_links = $_POST["topic_collection_setup"]["rss_feeds"];
	if($r_links !="" ){
		$rss_feed_links = $r_links;
	}else{
		$rss_feed_links = array();
	}
	
	$post_sources = $_POST["topic_collection_setup"]["search_sources"];
	$sources = array("news","blogs","twitter","dripple");
	$s_sources = array();
	foreach($post_sources as $who => $val){
		if(in_array($who,$sources)){
			$s_sources[$who] = intval($val);
		}
	}
	$default_sources = json_decode(LP_get_user_blog_option("TOPIC_SEARCH_SOURCES"),true);
	$msearch_sources = array_merge($default_sources,$s_sources);
	$search_sources = array_merge($s_sources,$msearch_sources);
	
	$keywords = array_slice($_POST["topic_collection_setup"]["keywords"], 0 ,10);
	$rss_keywords = array_slice($_POST["topic_collection_setup"]["rss_keywords"], 0 ,10);
	
	$blog_id = LP_get_user_blog_id();
	$post_id = $_POST["topic_id"];
	
	$meta_value = array(
				"search_sources"	=> $search_sources,
				"search_keywords"	=> $keywords,
				"rss_feed_links"	=> $rss_feed_links,
				"rss_keywords"		=> $rss_keywords
			);
	switch_to_blog($blog_id);
		update_post_meta($post_id, "_collection_setup", json_encode($meta_value));
	restore_current_blog();
	$new_topic = LP_get_user_topics($post_id, true);
	echo json_encode($new_topic[0]);
	die();
}

function LP_home_view()
{
	 if(!session_start()) {
		session_start();
	 }
	 $_SESSION["home_view"] = $_POST["home_view"];
}
add_action('wp_ajax_LP_home_view', 'LP_home_view');


require_once('linkedin/linkedin_3.2.0.class.php');
//////////////////////// Linked Meter - START //////////////////////////////
require_once('meter/meter.php');
//////////////////////// Linked Meter - END ////////////////////////////////

//////////////////////// ADJUST Meter - START //////////////////////////////
require_once('adjust/adjust.php');
//////////////////////// ADJUST Meter - END ////////////////////////////////

///////////////////// LinkedIn Schedule - START ////////////////////////////
require_once('utility/linkedin.php');
///////////////////// LinkedIn Schedule - END //////////////////////////////

///////////////////// twitter - START ////////////////////////////
require_once('utility/twitter.php');
///////////////////// twitter - END //////////////////////////////

///////////////////// facebook - START ////////////////////////////
require_once('utility/facebook.php');
///////////////////// facebook - END //////////////////////////////

///////////////////// facebook api - START //////////////////////////////
function LP_facebook_aouth($CB = "")
{
	require('facebook/src/facebook.php');
	$facebook_api_config = json_decode(FACEBOOK_API_CONFIG,true);
	$facebook = new Facebook($facebook_api_config);

	$loginUrl = $facebook->getLoginUrl()."&display=popup&scope=publish_actions,publish_stream&redirect_uri=$CB";
	wp_redirect( $loginUrl, 302 );
	exit;

}

function LP_ripple_FB_CB($topic_id)
{
	if(is_user_logged_in()){
		require('facebook/src/facebook.php');
		$facebook_api_config = json_decode(FACEBOOK_API_CONFIG,true);
		// print_r($facebook_api_config);
		$facebook = new Facebook($facebook_api_config);
		// print_r($_REQUEST);
		$accessToken = $facebook->getAccessToken();
		// print_r($accessToken);
		$facebook->setExtendedAccessToken();
		$long_lived_accessToken = $facebook->getAccessToken();
		$appToken = $facebook->getApplicationAccessToken();
		$data = $facebook->api('/debug_token','GET',array("input_token"=>$long_lived_accessToken,"access_token"=>$appToken));
		$data = $data["data"];
		$data["access_token"] = $long_lived_accessToken;

		$user_profile = $facebook->api('/me');
		print_r($data);
		print_r($user_profile);
		$blog_id = LP_get_user_blog_id();
		$ripple_facebook = json_decode(get_blog_option($blog_id, "_LP_ripple_facebook"),true);

		$ripple_facebook["f_".$data["user_id"]]["access_token"] 	= $data["access_token"];
		$ripple_facebook["f_".$data["user_id"]]["id"] 				= $data["user_id"];
		$ripple_facebook["f_".$data["user_id"]]["label"] 			= $user_profile["username"];
		$ripple_facebook["f_".$data["user_id"]]["expires_at"] 		= $data["expires_at"];
		$ripple_facebook["f_".$data["user_id"]]["is_valid"] 		= $data["is_valid"];

		update_blog_option($blog_id, "_LP_ripple_facebook", json_encode($ripple_facebook));
		echo "<script>window.opener.LP_ripple_insert_social('".$data["user_id"]."','".$user_profile["username"]."','facebook');</script>";
	}else{
		echo "ERROR?!!!...";
	}
	die();
}
///////////////////// facebook api - END //////////////////////////////
///////////////////// twitter api - START //////////////////////////////
function LP_twitter_aouth($CB = "")
{
    global $current_site;
    /* Start session and load library. */
    session_start();
    require_once('twitter/twitteroauth.php');

    if(isset($_GET["CB"]) && $_GET["CB"]!='')$CB = trim(network_site_url(),"/")."/".$_GET["CB"];
    $twitter_api_config = json_decode(TWITTER_API_CONFIG,true);
    // print_r($twitter_api_config);
    define('CONSUMER_KEY', $twitter_api_config["CONSUMER_KEY"]);
    define('CONSUMER_SECRET',  $twitter_api_config["CONSUMER_SECRET"]);
	if($CB == ""){
		define('OAUTH_CALLBACK', $twitter_api_config["OAUTH_CALLBACK"]);
	}else{
		define('OAUTH_CALLBACK', $CB);
	}

    /* Build TwitterOAuth object with client credentials. */
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
     
    /* Get temporary credentials. */
    $request_token = $connection->getRequestToken(OAUTH_CALLBACK);

    /* Save temporary credentials to session. */
    $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
    $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

    switch ($connection->http_code) {
      case 200:
        /* Build authorize URL and redirect user to Twitter. */
        $url = $connection->getAuthorizeURL($token);
        header('Location: ' . $url); 
        break;
      default:
        /* Show notification if something went wrong. */
        echo 'Could not connect to Twitter. Refresh the page or try again later.';
    }
}

function LP_twitter_CB()
{
    if(is_user_logged_in()){
        if($_REQUEST["oauth_token"]){
		
		session_start();
		require_once('twitter/twitteroauth.php');

		$oauth_verifier = $_REQUEST['oauth_verifier'];
		$oauth_token = $_SESSION['oauth_token'];
		$oauth_token_secret = $_SESSION['oauth_token_secret'];
		$twitter_api_config = json_decode(TWITTER_API_CONFIG,true);
		// print_r($twitter_api_config);
		define('CONSUMER_KEY', $twitter_api_config["CONSUMER_KEY"]);
		define('CONSUMER_SECRET',  $twitter_api_config["CONSUMER_SECRET"]);

		$twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $oauth_token, $oauth_token_secret);

		$access_token = $twitteroauth->getAccessToken($oauth_verifier);
		
		$blog_id = LP_get_user_blog_id();
		update_blog_option($blog_id, "_LP_twitter_token", $access_token);
		echo "<script>window.opener.LP_twitter_CB();</script>";
        }else{
            echo "ERROR?!!!...";
        }
    }
	die();
}

function LP_user_twitter_token(){
    if(is_user_logged_in()){
        $blog_id = LP_get_user_blog_id();
        $twitter_token = get_blog_option($blog_id, "_LP_twitter_token",false);
        return maybe_unserialize($twitter_token);
    }else return false;
}

function LP_twitter_search($term = ""){
	 /* Start session and load library. */
    session_start();
    require_once('twitter/twitteroauth.php');
    
    $twitter_api_config = json_decode(TWITTER_API_CONFIG,true);
    // print_r($twitter_api_config);
    define('CONSUMER_KEY', $twitter_api_config["CONSUMER_KEY"]);
    define('CONSUMER_SECRET',  $twitter_api_config["CONSUMER_SECRET"]);
    define('OAUTH_CALLBACK', $twitter_api_config["OAUTH_CALLBACK"]);
	
	$twitter_token = LP_user_twitter_token();

	if($twitter_token){
		/* Build TwitterOAuth object with client credentials. */
		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $twitter_token["oauth_token"], $twitter_token["oauth_token_secret"]);
        $ajax = false;
        if($term == ""){
            $term = $_POST["term"];
            $ajax = true;
        }
		$tweets = $connection->get("https://api.twitter.com/1.1/search/tweets.json?q=news ".$term." source:tweet_button filter:links&lang=en&count=".$_POST["count"]);
        if($ajax == true){
            echo json_encode($tweets);
        }else{
            return $tweets;
        }
	}else{
		echo "0";
	}
     die();
}

add_action('wp_ajax_LP_twitter_search', 'LP_twitter_search');

function LP_parse_website($url, $elements = array("title"), $metas = array("description")){
	$data = file_get_contents($url);
    
    foreach($http_response_header as $resp){
        if(strpos($resp,"Location")){
            $url = trim(str_replace("Location:","",$resp));
        }
    }
	// print_r($http_response_header);
	$resp = trim($http_response_header[0]);
	// echo $resp."<br/>";
	if($resp == "HTTP/1.1 200 OK" || $resp == "HTTP/1.0 302 Found" || $resp = "HTTP/1.0 301 Moved Permanently"){
		$matched_elements = "";
		foreach($elements as $element){
			$pattern = "/<{$element}>(.*?)<\/{$element}>/";
			preg_match_all($pattern, $data, $matches, PREG_PATTERN_ORDER);
			$matched_elements[$element] = $matches[1][0];
		}
		
		foreach($metas as $meta){
			$matched_metas[$meta] = LP_parseMeta($data, $meta);
		}
		
		$aurl = explode("/",$url);
		array_pop(end($aurl));
		$furl = implode("/",$aurl);
		$the_img = LP_parseIMG($data);
		if(strpos($the_img, $url)===false){
			$the_img = $furl."/".$the_img;
		}
		return array("elements" => $matched_elements, "metas" => $matched_metas, "img"=> $the_img, "url" => $url);
	}
	else return "";
}

function LP_parseMeta($html, $meta) {
	// Get the 'content' attribute value in a <meta name="description" ... />
	$matches = array();
	 
	// Search for <meta name="description" content="Buy my stuff" />
	preg_match('/<meta.*?name=("|\')'.$meta.'("|\').*?content=("|\')(.*?)("|\')/i', $html, $matches);
	if (count($matches) > 4) {
		// print_r($matches);
		return trim($matches[4]);
	}
	 
	// Order of attributes could be swapped around: <meta content="Buy my stuff" name="description" />
	preg_match('/<meta.*?content=("|\')(.*?)("|\').*?name=("|\')'.$meta.'("|\')/i', $html, $matches);
	if (count($matches) > 2) {
		// print_r($matches);
		return trim($matches[2]);
	}
	 
	// No match
	return null;
}

function LP_parseIMG($html) {
	// Get the 'content' attribute value in a <meta name="description" ... />
	$matches = array();
	 
	// Search for <meta name="description" content="Buy my stuff" />
	preg_match('/<img.*?src=("|\')(.*?)("|\')/i', $html, $matches);
	if (count($matches) > 2) {
		// print_r($matches);
		return trim($matches[2]);
	}
	 
	// Order of attributes could be swapped around: <meta content="Buy my stuff" name="description" />
	preg_match('/<img.*?src=("|\')(.*?)("|\').*?/i', $html, $matches);
	if (count($matches) > 2) {
		// print_r($matches);
		return trim($matches[2]);
	}
	 
	// No match
	return null;
}

// function LP_parse_web_page( $url ) {
    // $res = array();
    // $options = array( 
        // CURLOPT_RETURNTRANSFER => true,     // return web page 
        // CURLOPT_HEADER         => false,    // do not return headers 
        // CURLOPT_FOLLOWLOCATION => true,     // follow redirects 
        // CURLOPT_USERAGENT      => "spider", // who am i 
        // CURLOPT_AUTOREFERER    => true,     // set referer on redirect 
        // CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect 
        // CURLOPT_TIMEOUT        => 120,      // timeout on response 
        // CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects 
    // ); 
    // $ch      = curl_init( $url ); 
    // curl_setopt_array( $ch, $options ); 
    // $content = curl_exec( $ch ); 
    // $err     = curl_errno( $ch ); 
    // $errmsg  = curl_error( $ch ); 
    // $header  = curl_getinfo( $ch ); 
    // curl_close( $ch ); 

    // $res['content'] = $content;     
    // $res['url'] = $header['url'];
    // return $res; 
// } 

function LP_parse_web_page(/*resource*/ $ch, /*int*/ &$maxredirect = null) {
    $ch = curl_init( $ch );
    $mr = $maxredirect === null ? 20 : intval($maxredirect);
    if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $mr > 0);
        curl_setopt($ch, CURLOPT_MAXREDIRS, $mr);
    } else {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        if ($mr > 0) {
            $newurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

            $rch = curl_copy_handle($ch);
            curl_setopt($rch, CURLOPT_HEADER, true);
            curl_setopt($rch, CURLOPT_NOBODY, true);
            curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
            curl_setopt($rch, CURLOPT_RETURNTRANSFER, true);
            do {
                curl_setopt($rch, CURLOPT_URL, $newurl);
                $header = curl_exec($rch);
                if (curl_errno($rch)) {
                    $code = 0;
                } else {
                    $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
                    if ($code == 301 || $code == 302) {
                        preg_match('/Location:(.*?)\n/', $header, $matches);
                        $newurl = trim(array_pop($matches));
                    } else {
                        $code = 0;
                    }
                }
                echo '$newurl :'.$newurl." ##";
            } while ($code && --$mr);
            curl_close($rch);
            if (!$mr) {
                if ($maxredirect === null) {
                    trigger_error('Too many redirects. When following redirects, libcurl hit the maximum amount.', E_USER_WARNING);
                } else {
                    $maxredirect = 0;
                }
                return false;
            }
            curl_setopt($ch, CURLOPT_URL, $newurl);
        }
    }
    return array("content"=>curl_exec($ch),"url" => $newurl);
} 
///////////////////// twitter api - END //////////////////////////////

// add_filter( 'image_resize_dimensions', 'custom_image_resize_dimensions', 10, 6 );
function custom_image_resize_dimensions( $payload, $orig_w, $orig_h, $dest_w, $dest_h, $crop ){
	// print_r($_POST);
	// echo "orig_w : ".$orig_w."\n\r";
	// echo "orig_h : ".$orig_h."\n\r";
	// echo "dest_w : ".$dest_w."\n\r";
	// echo "dest_h : ".$dest_h."\n\r";
	// echo "crop   : ".$crop."\n\r";
	// Change this to a conditional that decides whether you 
	// want to override the defaults for this image or not.
	// if( false )
		// return $payload;

	if ( $crop ) {
		// crop the largest possible portion of the original image that we can size to $dest_w x $dest_h
		$aspect_ratio = $orig_w / $orig_h;
		$new_w = min($dest_w, $orig_w);
		$new_h = min($dest_h, $orig_h);

		if ( !$new_w ) {
			$new_w = intval($new_h * $aspect_ratio);
		}

		if ( !$new_h ) {
			$new_h = intval($new_w / $aspect_ratio);
		}

		$size_ratio = max($new_w / $orig_w, $new_h / $orig_h);

		$crop_w = round($new_w / $size_ratio);
		$crop_h = round($new_h / $size_ratio);

		$s_x = floor( ($orig_w - $crop_w) / 2 );
		$s_y = 0; // [[ formerly ]] ==> floor( ($orig_h - $crop_h) / 2 );
		
		// orig_w : 320
		// orig_h : 320
		// dest_w : 150
		// dest_h : 150
		// crop   : 1
	} else {
		// don't crop, just resize using $dest_w x $dest_h as a maximum bounding box
		$crop_w = $orig_w;
		$crop_h = $orig_h;

		$s_x = 0;
		$s_y = 0;

		list( $new_w, $new_h ) = wp_constrain_dimensions( $orig_w, $orig_h, $dest_w, $dest_h );
	}

	// if the resulting image would be the same size or larger we don't want to resize it
	if ( $new_w >= $orig_w && $new_h >= $orig_h )
		return false;
	// the return array matches the parameters to imagecopyresampled()
	// int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h
	$to_return = array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );
	// print_r($to_return);
	return $to_return;
}

function LP_resize_crop_image($img, $to_width = 400, $to_heigth = 500, $focal_point = "")
{
	if($focal_point == ""){
		$dst_x = 0;
		$dst_y = 0;
	}else{
		$image_info = getimagesize($img);
		$o_w = $image_info[0];
		$o_h = $image_info[1];
		list($left,$top) = explode("/", $focal_point);
		$uploadedimage_ratio = $o_w / $o_h;
		$target_ratio = $to_width / $to_heigth ;
		
		if($uploadedimage_ratio == $target_ratio){
			$dst_x = 0;
			$dst_y = 0;
		}elseif($uploadedimage_ratio >=1 && $target_ratio>=1){// from landscape
			$difference = $o_w  - $to_width;
			$scaled_difference = $difference / $target_ratio;		
			// This will be the height if the uploaded image is scaled.
			$to_h = $o_h - $scaled_difference;			
			// Focal Points here... 12 is from the constant 12x12 cells. Also used in the UI during defining the focal point.
			// $from_left = ($to_width/12) * ($left-1); // this is the number of pixels of the from the left to the focal point.
			$from_top = ($to_h/12) * ($top-1); // this is the number of pixels of the from the top to the focal point.
			$from_bottom = ($to_h/12) * (12-$top); // this is the number of pixels of the from the bottom to the focal point.
			
			// Get the difference of the scaled height to the target height;
			$height_difference = $to_h - $to_heigth; // This is the number of pixels to be cut off from the uploaded image to be cropped (resized_height - target_height).
			if($height_difference >=0){
				if($from_top > $height_difference){ // Check if there are enough pixels from the top to the focal point... to make sure we won't be cutting off the focal point.
					//But before we start cropping, we should try to evenly crop from top and bottom
					$dst_x = 0;
					if ($from_bottom >($height_difference/2)){ // then we are going to crop the resized image ($height_difference/2) pixels from the bottom and then another ($height_difference/2) from the top..
						$dst_y = $height_difference/2;
					}else{ // we'll crop the uploaded image $height_difference" pixels from the top...
						$dst_y = $height_difference;
					}
				}
			}else{
				$sw   = $o_h / $target_ratio;
				// echo '$sw   = $o_h / $target_ratio : '."$sw   = $o_h / $target_ratio; \n\r";
				
				if($sw > $o_w){
					$sw = $o_w;
					$sh = $o_w * $target_ratio;
					$is_width = true;
				}else{
					$sh = $o_w;
					$is_width = false;
				}
				
				$sw = floor($sw);
				$from_left = ($o_w/12) * ($left-1); // this is the number of pixels of the from the left to the focal point.
				$from_right = ($o_w/12) * (12-$left+1); // this is the number of pixels of the from the right to the focal point.
				$from_top = ($o_h/12) * ($top-1); // this is the number of pixels of the from the top to the focal point.
				$from_bottom = ($o_h/12) * (12-$top+1); // this is the number of pixels of the from the bottom to the focal point.
				
				//But before we start cropping, we should try to evenly crop from top and bottom
				if($is_width == true){
					$height_difference = $o_h - $sh; // This is the number of pixels to be cut off from the uploaded image to be cropped (resized_height - target_height).
					$dst_x = 0;
					if ($from_bottom >($height_difference/2)){ // then we are going to crop the resized image ($height_difference/2) pixels from the bottom and then another ($height_difference/2) from the top..
						$dst_y = $height_difference/2;
					}else{ // we'll crop the uploaded image $height_difference" pixels from the top...
						if(($top/2) < 6){
							$dst_y = 0;
						}else{
							$dst_y = floor($height_difference);
						}
					}
				}else{
					$width_difference = $o_h - $sh; // This is the number of pixels to be cut off from the uploaded image to be cropped (resized_height - target_height).						
					$dst_y = 0;
					if ($from_right >($width_difference/2)){ // then we are going to crop the resized image ($width_difference/2) pixels from the bottom and then another ($width_difference/2) from the top..
						$dst_x = floor($width_difference/2);
						// echo '$dst_y = floor($width_difference/2); : '."$dst_y = floor($width_difference/2); \n\r";
					}else{ // we'll crop the uploaded image $width_difference" pixels from the top...
						$the_left = $o_h - $from_right;
						if(($to_width/2) <= $from_right){
							$dst_x = $the_left - ($to_width/2);
						}else{
							$the_right = $o_w - $from_left;
							$dst_x = $the_right - ($to_width/2);
						}
					}
				}

			}
		}elseif($uploadedimage_ratio < 1){// from portrait
			$difference = $o_h  - $to_heigth;
			$scaled_difference = $difference / $target_ratio;
			// This will be the width if the uploaded image is scaled.
			$to_w = $o_w - $scaled_difference;
			// Focal Points here... 12 is from the constant 12x12 cells. Also used in the UI during defining the focal point.
			$from_left = ($to_w/12) * ($left-1); // this is the number of pixels of the from the left to the focal point.
			$from_right = ($to_w/12) * (12-$left+1); // this is the number of pixels of the from the right to the focal point.
			// $from_top = ($to_h/12) * ($top-1); // this is the number of pixels of the from the top to the focal point.
			// $from_bottom = ($to_h/12) * (12-$top); // this is the number of pixels of the from the bottom to the focal point.
			
			// Get the difference of the scaled width to the target height;
			$width_difference = $to_w - $to_width; // This is the number of pixels to be cut off from the uploaded image to be cropped (resized_width - target_width).
			if($width_difference >=0){
				if($from_left > $width_difference){ // Check if there are enough pixels from the left to the focal point... to make sure we won't be cutting off the focal point.
					//But before we start cropping, we should try to evenly crop from top and bottom
					$dst_y = 0;
					if ($from_right >($width_difference/2)){ // then we are going to crop the resized image ($width_difference/2) pixels from the left and then another ($width_difference/2) from the right..
						$dst_x = $width_difference/2;
					}else{ // we'll crop the uploaded image $width_difference" pixels from the left...
						$dst_x = $width_difference;
					}
				}
			}else{
				$sh   = $o_w / $target_ratio;
				// echo '$sh   = $o_w / $target_ratio : '."$sh   = $o_w / $target_ratio; \n\r";
				
				if($sh > $o_h){
					$sh = $o_h;
					$sw = $o_h * $target_ratio;
					$is_height = true;
				}else{
					$sw = $o_w;
					$is_height = false;
				}
				
				$sh = floor($sh);
				$from_left = ($o_w/12) * ($left-1); // this is the number of pixels of the from the left to the focal point.
				$from_right = ($o_w/12) * (12-$left+1); // this is the number of pixels of the from the right to the focal point.
				$from_top = ($o_h/12) * ($top-1); // this is the number of pixels of the from the top to the focal point.
				$from_bottom = ($o_h/12) * (12-$top+1); // this is the number of pixels of the from the bottom to the focal point.
				
				//But before we start cropping, we should try to evenly crop from top and bottom
				if($is_height == true){
					$width_difference = $o_w - $sw; // This is the number of pixels to be cut off from the uploaded image to be cropped (resized_height - target_height).
					$dst_y = 0;
					if ($from_right >($width_difference/2)){ // then we are going to crop the resized image ($height_difference/2) pixels from the bottom and then another ($height_difference/2) from the top..
						$dst_x = $width_difference/2;
					}else{ // we'll crop the uploaded image $height_difference" pixels from the top...
						if(($left/2) < 6){
							$dst_x = 0;
						}else{
							$dst_x = floor($width_difference);
						}
					}
				}else{
					$height_difference = $o_h - $sh; // This is the number of pixels to be cut off from the uploaded image to be cropped (resized_height - target_height).						
					$dst_x = 0;
					if ($from_bottom >($height_difference/2)){ // then we are going to crop the resized image ($height_difference/2) pixels from the bottom and then another ($height_difference/2) from the top..
						$dst_y = floor($height_difference/2);
						// echo '$dst_y = floor($height_difference/2); : '."$dst_y = floor($height_difference/2); \n\r";
					}else{ // we'll crop the uploaded image $height_difference" pixels from the top...
						$the_top = $o_h - $from_bottom;
						if(($to_heigth/2) <= $from_bottom){
							$dst_y = $the_top - ($to_heigth/2);
						}else{
							$the_bottom = $o_h - $from_top;
							$dst_y = $the_bottom - ($to_heigth/2);
							// echo '$dst_y = $the_bottom + ($to_heigth/2); : '."$dst_y = $the_bottom + ($to_heigth/2); \n\r";
						}
					}
				}

			}
		}
		// echo "\n\rdst_x : ".$dst_x."\n\r";
		// echo "dst_y : ".$dst_y."\n\r";
		// echo "to_width : ".$to_width."\n\r";
		// echo "to_heigth : ".$to_heigth."\n\r";
		// echo "o_w : ".$o_w."\n\r";
		// echo "o_h : ".$o_h."\n\r";
		// echo "sw : ".$sw."\n\r";
		// echo "sh : ".$sh."\n\r";echo "o_w : ".$o_w."\n\r";
		// echo "from_left : ".$from_left."\n\r";
		// echo "from_right : ".$from_right."\n\r";
		// echo "from_top : ".$from_top."\n\r";
		// echo "from_bottom : ".$from_bottom."\n\r";
		// echo "height_difference : ".$height_difference."\n\r";
		$extension = pathinfo($img, PATHINFO_EXTENSION)."\n\r";
		// var_dump($extension);
		$image_p = imagecreatetruecolor($to_width, $to_heigth);
		if(trim($extension) == "png"){
			imagealphablending( $image_p, false );
			imagesavealpha( $image_p, true );
			$image = imagecreatefrompng($img);
		}elseif(trim($extension) == "jpg" || $extension == "jpeg"){
			$image = imagecreatefromjpeg($img);
		}elseif(trim($extension) == "gif"){
			imagealphablending( $image_p, false );
			imagesavealpha( $image_p, true );
			$image = imagecreatefromgif($img);
		}
		imagecopyresampled($image_p, $image, 0, 0, floor($dst_x), floor($dst_y),$to_width, $to_heigth, floor($sw), floor($sh));
		return $image_p;
	}
}

function LP_cropper($filename, $res_width, $to_width, $to_height, $left=0, $top=0, $save)
{
    try{
        $image_info = getimagesize($filename);
        $o_w = $image_info[0];
        $o_h = $image_info[1];

        $image_p = imagecreatetruecolor($to_width, $to_height);
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if(trim($extension) == "png"){
            imagealphablending( $image_p, false );
            imagesavealpha( $image_p, true );
            $image = imagecreatefrompng($filename);
        }elseif(trim($extension) == "jpg" || $extension == "jpeg"){
            $image = imagecreatefromjpeg($filename);
        }elseif(trim($extension) == "gif"){
            imagealphablending( $image_p, false );
            imagesavealpha( $image_p, true );
            $image = imagecreatefromgif($filename);
        }elseif(trim($extension) == "bmp"){
            $image = imagecreatefrombmp($filename);
        }

        $res_height = ($o_h / $o_w) * $res_width;
        $percent_diff = ($o_w - $res_width)/$res_width;

        $s_width = $to_width * (1+$percent_diff);

        $s_height = $to_height * (1+$percent_diff);

        $s_x = $left * (1+$percent_diff);

        $s_y = $top * (1+$percent_diff);

        // echo "($image_p, $image, 0, 0, $s_x, $s_y, $to_width, $to_height, $s_width, $s_height)\n\r";
        imagecopyresampled($image_p, $image, 0, 0, $s_x, $s_y, $to_width, $to_height, $s_width, $s_height);
        // ($image_p        , $image, 0, 0, $s_x            , $s_y            , $to_width, $to_height, $s_width       , $s_height);
        //(Resource id #182 , $image, 0, 0, -1534.9555677455, -263.56555128908, 495      , 275       , 621.26165660998, 345.14536478332)

        // dist image, src image, "0, 0 " is starting coordinates of dist image, "$s_x, $s_y" is the starting where we start copying pexils from src image, "$to_width, $to_height" is the finished dimension, "$s_width, $s_height" of the ORIGINAL image where we copy all of its pexils...
        if(!$save){
            return $image_p;
        }else{
            // $plugin_dir_path = plugin_dir_path(__FILE__);

            $tf = explode(".",$filename);
            $tf[count($tf)-2].="_".floor($to_width)."x".floor($to_height);
            $target = implode(".",$tf);
            if(trim($extension) == "png"){
                if(imagepng($image_p, $target)) return $target;
                else return false;
            }elseif(trim($extension) == "jpg" || $extension == "jpeg"){
                if(imagejpeg($image_p, $target))return $target;
                else return false;
            }elseif(trim($extension) == "gif"){
                if(imagegif($image_p, $target))return $target;
                else return false;
            }elseif(trim($extension) == "bmp"){
                if(imagebmp($image_p, $target))return $target;
                else return false;
            }
        }
    }catch (Exception $e){
        $succ = false;
        echo $e->getMessage();
    }
    return $succ;
}

/**
 * Creates function imagecreatefrombmp, since PHP doesn't have one
 * @return resource An image identifier, similar to imagecreatefrompng
 * @param string $filename Path to the BMP image
 * @see imagecreatefrompng
 * @author Glen Solsberry <glens@networldalliance.com>
 */
if (!function_exists("imagecreatefrombmp")) {
    function imagecreatefrombmp( $filename ) {
        $file = fopen( $filename, "rb" );
        $read = fread( $file, 10 );
        while( !feof( $file ) && $read != "" )
        {
            $read .= fread( $file, 1024 );
        }
        $temp = unpack( "H*", $read );
        $hex = $temp[1];
        $header = substr( $hex, 0, 104 );
        $body = str_split( substr( $hex, 108 ), 6 );
        if( substr( $header, 0, 4 ) == "424d" )
        {
            $header = substr( $header, 4 );
            // Remove some stuff?
            $header = substr( $header, 32 );
            // Get the width
            $width = hexdec( substr( $header, 0, 2 ) );
            // Remove some stuff?
            $header = substr( $header, 8 );
            // Get the height
            $height = hexdec( substr( $header, 0, 2 ) );
            unset( $header );
        }
        $x = 0;
        $y = 1;
        $image = imagecreatetruecolor( $width, $height );
        foreach( $body as $rgb )
        {
            $r = hexdec( substr( $rgb, 4, 2 ) );
            $g = hexdec( substr( $rgb, 2, 2 ) );
            $b = hexdec( substr( $rgb, 0, 2 ) );
            $color = imagecolorallocate( $image, $r, $g, $b );
            imagesetpixel( $image, $x, $height-$y, $color );
            $x++;
            if( $x >= $width )
            {
                $x = 0;
                $y++;
            }
        }
        return $image;
    }
}
/* 
 * Parse a site using simple html DOM 
 */
add_action('wp_ajax_LP_parse_site', 'LP_parse_site');
function LP_parse_site()
{
	require_once('parser/simple_html_dom.php');
	$html = file_get_html($_POST["url"]);
	$res = array();
	$i = 0;
	foreach($html->find($_POST["tag"]) as $e){
		$r = trim(strip_tags($e->innertext));
		if(strlen($r) > 70){
			$res[$_POST["tag"]][$i] = $r;
			$i++;
		}
	}
	echo json_encode($res);
	die();
}

/* RIPPLE REQUESTS */
add_action('wp_ajax_LP_update_topic_ripple', 'LP_update_topic_ripple');
function LP_update_topic_ripple()
{
    global $switched;
    $topic_id = $_POST["topic"];
    if(intval($topic_id) > 0){
        $blog_id = LP_get_user_blog_id();
        switch_to_blog($blog_id);
        // echo "bid : ".$blog_id;
            update_post_meta($topic_id, "_LP_ripple",json_encode($_POST["ripple_args"]));
        restore_current_blog();
        
        $topic = LP_get_user_topics($topic_id);
        if($topic){
            echo json_encode($topic[0]);
        }else{
            echo "0";
        }
    }
    die();
}

function LP_ripple_TW_CB()
{
    if(is_user_logged_in()){
        if($_REQUEST["oauth_token"]){
		
		session_start();
		require_once('twitter/twitteroauth.php');

		$oauth_verifier = $_REQUEST['oauth_verifier'];
		$oauth_token = $_SESSION['oauth_token'];
		$oauth_token_secret = $_SESSION['oauth_token_secret'];

		$twitteroauth = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET, $oauth_token, $oauth_token_secret);

		$access_token = $twitteroauth->getAccessToken($oauth_verifier);

		$blog_id = LP_get_user_blog_id();
		$ripple_twitter = json_decode(get_blog_option($blog_id, "_LP_ripple_twitter"),true);

		$ripple_twitter["t_".$access_token["user_id"]]["access_token"] 	= $access_token;
		$ripple_twitter["t_".$access_token["user_id"]]["id"] 			= $access_token["user_id"];
		$ripple_twitter["t_".$access_token["user_id"]]["label"] 		= $access_token["screen_name"];

		update_blog_option($blog_id, "_LP_ripple_twitter", json_encode($ripple_twitter));
		echo "<script>window.opener.LP_ripple_insert_social('".$access_token["user_id"]."','".$access_token["screen_name"]."','twitter');</script>";
        }else{
            echo "ERROR?!!!...";
        }
    }
	die();
}
/* END RIPPLE REQUESTS */



function testing(){
    // extract data from cookie stored in json
    $consumer_key = '782a3kg6eadv';
    $cookie_name = "linkedin_oauth_{$consumer_key}";
    $credentials_json = $_COOKIE[$cookie_name]; // where PHP stories cookies
    $credentials = json_decode($credentials_json);

    $consumer_secret = 'd3vPgblSQ9aQmYrA';

    print_r($_COOKIE);
    print_r($credentials);
// validate signature
    if ($credentials->signature_version == 1) {
        if ($credentials->signature_order && is_array($credentials->signature_order)) {
            $base_string = '';
            // build base string from values ordered by signature_order
            foreach ($credentials->signature_order as $key) {
                if (isset($credentials->$key)) {
                    $base_string .= $credentials->$key;
                } else {
                    print "missing signature parameter: $key";
                }
            }
            // hex encode an HMAC-SHA1 string
            $signature =  base64_encode(hash_hmac('sha1', $base_string, $consumer_secret, true));
            // check if our signature matches the cookie's
            if ($signature == $credentials->signature) {
                print "signature validation succeeded";
            } else {
                print "signature validation failed";
            }
        } else {
            print "signature order missing";
        }
    } else {
        print "unknown cookie version";
    }

    print_r($credentials);
    die();
}

function LP_send_linkedin_messages($user_id)
{
    global $switched;
    $blog_id = LP_get_user_blog_id($user_id);
    switch_to_blog($blog_id);
    $post = LP_fetch_blog_latest_drip();
    if($post){
        LP_send_message_to_lin_industry($post["ID"], $user_id);
    }
    restore_current_blog();
}

/**
 * This is what the server crontab is executing every 5 mins.
 *
 * Can also be accessed with this URL http://www.DOMAINNAME.com/LP_msg
 */
function LP_daily_linkedin_messaging()
{
    global $wpdb, $shardb_prefix;
    $global_db = $shardb_prefix."global";
    $wp_prefix = $wpdb->prefix;
    // This is the time this code be executed.. based on each blogs timezone.
    $sched_time = "09 AM";
    $tzs = "'".implode("','",LP_list_current_timezones(date("Y-m-d")." $sched_time"))."'";
    if($tzs!="''"){
        $sql = "SELECT tz.* FROM `{$global_db}`.`{$wp_prefix}usermeta` tz
                LEFT JOIN (SELECT * FROM `{$global_db}`.`{$wp_prefix}usermeta`  WHERE `meta_key` = '_LP_msg_date') u on u.`user_id` = tz.`user_id`
                WHERE
                    tz.`meta_key` = 'timezone_string' AND tz.`meta_value` in ({$tzs})
                    AND (DATE_FORMAT(u.`meta_value`,'%Y-%m-%d') != '".date("Y-m-d",strtotime(current_time( 'mysql', 1 )))."' OR   u.`meta_value` IS NULL)
                    AND tz.`user_id` != 1";
        //echo $sql;
        $users = $wpdb->get_results($sql, ARRAY_A);
        foreach($users as $user){
            LP_send_linkedin_messages($user["user_id"]);
        }
    }
    die();
}

/**
 *
 *This will return all timezones that is having the DateTime @$DateTime...
 *
 */
function LP_list_current_timezones($DateTime = "", $server_tz = "GMT"){
    global $LP_timezones;
    $matched_tz = array();
    if(!strtotime($DateTime)){
        $original_datetime = date("Y-m-d h A");
    }else{
        $original_datetime = $DateTime;
    }
    //echo "Server GMT Time :".$original_datetime."\n\r";
    foreach($LP_timezones as $tz){
        $original_timezone = new DateTimeZone($server_tz);
        // Instantiate the DateTime object, setting it's date, time and time zone.
        $datetime = new DateTime(date("Y-m-d h A"), $original_timezone);
        // Set the DateTime object's time zone to convert the time appropriately.
        try{
            if($target_timezone = new DateTimeZone($tz)){
//                $datetime = new DateTime(date("Y-m-d h A"), $target_timezone);
                $datetime->setTimeZone($target_timezone);

                // Outputs a date/time string based on the time zone you've set on the object.
                $converted_DT = $datetime->format('Y-m-d h A');


                if($original_datetime == $converted_DT){
                    // Print the date/time string.
                    $matched_tz[] = $tz;
                    //echo "$tz : $original_datetime == $converted_DT \n\r";
                }
            }
        }catch (Exception $e) {
            //echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }
    return $matched_tz;
}
?>
