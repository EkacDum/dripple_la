<?php
/*
Plugin Name: LinkedPost Driping
*/
global $LP_user_drip_settings;


add_action("init", "LinkedPost_js_init");
function LinkedPost_js_init()
{
    if(!session_start()) {
        throw new LinkedInException('This script requires session support, which appears to be disabled according to session_start().');
    }

    wp_register_style( 'LP-style', plugins_url('/style.css', __FILE__) );
    wp_enqueue_style( 'LP-style' );
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
            "site_url"          => "http://".$current_site->domain."/", 
            "is_forms"          => is_user_logged_in(), 
            );
	
	if(is_user_logged_in()){
		$LP_siteurl = "http://".$current_site->domain;
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
	}
    wp_enqueue_script('LinkedPost_js', plugins_url('/LinkedPost/script.js'), array('jquery') );
    wp_localize_script('LinkedPost_js', 'linkedIn_AJAX', $js_global);
}

function LinkedPost_handle($content){
    $uri = explode("/",trim($_SERVER["REQUEST_URI"],"/"));
    
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
	}elseif(is_404() && (end($uri)=="linkedInP" || prev($uri)=="linkedInP")){
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
    }elseif(is_404() && $uri[0]=="in"){
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
    }elseif(is_404() && $uri[0]=="lp_add_topic_thumb"){
        set_not_404();
        LP_upload_img_to_temp();
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
    }elseif(is_404() && $uri[0]=="twitter"){
		set_not_404();
		if($uri[1]=="aouth"){
			LP_twitter_aouth();
		}elseif($uri[1]=="CB"){
			set_not_404();
			LP_twitter_CB();
		}
    }elseif(is_404() && $uri[0]=="maintain"){
		set_not_404();
		LP_maintain_enough_schedules(5);
		die();
    }else{
		LP_process_url();
	}
}
add_action( 'template_redirect', 'LinkedPost_handle' );

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

function LP_toggle_follow(){
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
add_action('wp_ajax_LP_toggle_follow', 'LP_toggle_follow');

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
add_action('wp_ajax_LP_udpate_sort_channels', 'LP_udpate_sort_channels');

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
	$base_url = "http://".$current_site->domain."/";
	$topic_name = sanitize_title($drip["topic_name"]);
	$channel_name = sanitize_title($drip["channel_name"]);
	$drip_name = sanitize_title($drip["post_title"]);
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
        $redirect =  "http://".$current_site->domain."/in/".$user_info->user_nicename."/";
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
            $OBJ_linkedin->setScope("r_basicprofile%20r_emailaddress%20r_network%20rw_nus%20r_fullprofile%20w_messages");
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
                 if(is_user_logged_in()){
                    $user_info = get_user_meta(get_current_user_id());
                    if($_GET["CB"]==1){
                        $utoken = $_SESSION['oauth']['linkedin']['access'];
                    }else{
                        $utoken = maybe_unserialize($user_info["linkedin_TokenAccess"][0]);
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
					echo $myindustries;
					$meta = array_merge($meta,$industries);
					$meta["_LPin_industries"] = $myindustries;
					// print_r($meta);
                    
                    // LP_update_user_meta(get_current_user_id(), $meta);
					$blog_id = LP_get_user_blog_id();
					LP_update_blog_meta($blog_id, $meta);
                    die(1);
                }
            }
        break;
       }
	} catch(LinkedInException $e) {
	  // exception raised by library call
	  echo $e->getMessage();
      die();
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
                $redirect_to = "http://".$current_site->domain."/in/".$user_info->user_nicename."/";
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

function LP_login_user_by_LinkedIn($linkedIn_ID ,$from_popup = false){
    $args = array(
        'meta_key'     => 'linkedin_uid',
        'meta_value'   => $linkedIn_ID
    );
    
    $the_user = get_users( $args );         
    $user_id = $the_user[0]->ID;
    $user = get_user_by( "id", $user_id );  
    $user_name = $user->data->user_login;
    wp_set_current_user($user_id, $user_name);
    wp_set_auth_cookie($user_id);
    do_action('wp_login', $user_name);
    global $current_site;
    $user_info = get_userdata($user_id);
    $res = array(
        "success"           =>  true,
        "member_page_url"   =>  "http://".$current_site->domain."/in/".$user_info->user_nicename."/"
    );
    if(!$from_popup){
        header('Location: ' .$res["member_page_url"]);
    }else{
        echo "<html><head><script>opener.linkedin_callback_redirect(\"".$res["member_page_url"]."\");</script></head><body></body></html>";
        die();
    }
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
            "member_page_url"   =>  "http://".$current_site->domain."/in/".$user_info->user_nicename."/"
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
                    // you must first include the image.php file
                    // for the function wp_generate_attachment_metadata() to work
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    $attach_data = wp_generate_attachment_metadata( $attach_id, $wp_upload_dir['path'] . '/' . $filename );
                    // print_r($attach_data);
                    wp_update_attachment_metadata( $attach_id, $attach_data );
                    add_post_meta($post_s[0]["post_id"], '_thumbnail_id', $attach_id);
                    restore_current_blog();
                    echo "<script>parent.lp_update_image('".$guid_ ."','".$drip_id."');</script>";
                    break;
                }
            }
        }
    }
    die();
}

function LP_remote_post(){
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
                    echo json_encode($ftopic[0]);
                }else{
                    echo "0";
                }
                if(isset($_SESSION[$_POST["session_name"]]["file"])){
                    $img = $_SESSION[$_POST["session_name"]];
                    unset($_SESSION[$_POST["session_name"]]);
					
					$file = explode("/",$img["file"]);
					$plugin_dir_path = plugin_dir_path(__FILE__);
					$npath = $plugin_dir_path."temp_upload/temp_".$user_ID."/".end($file);
					$img["file"] = $npath;
					set_featured_img($post_id, $blog_id ,$img);
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

function LP_upload_img_to_temp(){
    global $current_site;
    $res = tc_handle_upload_prefilter($_FILES["topic_file"]);
    // print_r($res);
    if(isset($res["error"]) && $res["error"] != 0){
        // echo " ERROR !!!<br />";
        die($res["error"]);
    }
	
	$LP_siteurl = "http://".$current_site->domain;
    // echo "0 - WHAT???<br />";
    if(is_user_logged_in()){
		// echo "1 - I am in<br />";
        if(isset($_FILES["topic_file"])){
        // echo "2 - I am in<br />";
			$plugin_dir = "wp-content/plugins/LinkedPost/";
			$uid = get_current_user_id();
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
            }else{
                echo "<script>parent.lp_set_topic_image('".$LP_siteurl."/".$to_temp_file."');</script>";
            }
			die();
        }
    }else{
		die();
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
        $LP_siteurl = "http://".$current_site->domain;
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
                $post_other_info[$a]["thumbnail"]["small"] = LP_get_post_thumb_url($post_id, "","topic-thumb-small");
                $post_other_info[$a]["thumbnail"]["medium"] = LP_get_post_thumb_url($post_id, "","topic-thumb-medium");
                $post_other_info[$a]["thumbnail"]["large"] = LP_get_post_thumb_url($post_id, "","topic-thumb-Large");
				$post_other_info[$a]["thumbnail"]["flarge"] = LP_get_post_thumb_url($post_id, "","large");
				$post_other_info[$a]["thumbnail"]["fmedium"] = LP_get_post_thumb_url($post_id, "","medium");
				$post_other_info[$a]["thumbnail"]["fsmall"] = LP_get_post_thumb_url($post_id, "","small");
                
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

function LP_user_topics(){
    $topics = LP_get_user_topics();
    if($topics){
        echo json_encode($topics);
    }else{
        echo "0";
    }
    die();
}
add_action('wp_ajax_LP_user_topics', 'LP_user_topics');

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

function LP_fetch_drip_data()
{
	if(is_user_logged_in()){
		$post_id = $_POST["post_id"];
		$blog_id = $_POST["blog_id"];
		$drip = LP_get_drip($post_id, $blog_id);
			$drip["thumbnail"]["small"]= LP_get_post_thumb_url($post_id, $blog_id,"topic-thumb-small");
			$drip["thumbnail"]["fmedium"]= LP_get_post_thumb_url($post_id, $blog_id,"medium");
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
add_action('wp_ajax_LP_fetch_drip_data', 'LP_fetch_drip_data');

function LP_fetch_linkedin_message(){
	if(is_user_logged_in()){
		$linkedin_message = LP_get_user_blog_option("LINKEDIN_MESSAGE");
		if(is_array($linkedin_message)){
			$linkedin_message["subject"] = stripslashes($linkedin_message["subject"]);
			$linkedin_message["body"] = stripslashes($linkedin_message["body"]);
			echo json_encode($linkedin_message);
		}else{
			echo $linkedin_message;
		}
	}
    die();
}
add_action('wp_ajax_LP_fetch_linkedin_message', 'LP_fetch_linkedin_message');

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
		if($new_data["analysis"]!="" && $new_data["topic"]!="" && $new_data["ripple_content"]!="" && $new_data["ripple_title"]!=""){
			if(LP_redrip_this($_POST["post_id"],$_POST["blog_id"], $new_data )) echo "1";
			else echo "0";
            die();
		}
	}
    echo "0";
	die();
}
add_action('wp_ajax_LP_save_redrip', 'LP_save_redrip');

function LP_redrip_this($post_id, $blog_id, $new_data)
{
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
						$global_db = $shardb_prefix."global";
						$prefix = $wpdb->base_prefix;
				
						$sql = $wpdb->prepare("INSERT INTO `{$global_db}`.`{$prefix}lp_drips` SET `parent` = %d,`post_id` = %d, `blog_id` = %d, `post_type` = %s, `story_URL`= %s, `channel_id` = %d, `post_date` = %s", $parent_drip_id, $new_id, $new_blog,'post',$parent_drip["URL"],$topic[0]["channel_id"], date("Y-m-d H:i:s"));
						// echo $sql."\n\r";
						$reslt = $wpdb->query($sql);
						if($reslt===false){
							@mysql_query("ROLLBACK", $wpdb->dbh);
							return false;
						}else{
							$sql = $wpdb->prepare("UPDATE `{$global_db}`.`{$prefix}lp_drips` SET `redrips`=(`redrips`+1) WHERE `id` = %d ",$parent_drip_id);
							// echo $sql."\n\r";
							$res2 = $wpdb->query($sql);
							if($res2===false){
								@mysql_query("ROLLBACK", $wpdb->dbh);
								return false;
							}else{                              
                                
                                /* SETTING FEATURED IMAGE */
                                if(set_featured_img($new_id, $new_blog ,$img, false, false)){
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
                                            if(LP_send_this_message_to_linkedin(array($details["contact"]["ID"]), $details["message"])){
                                                // LP_record_message_sent($args);
                                            }
                                        }
                                        
                                        /* **** END SENDING oF MESSAGE **** */
                                        return true;
                                    }
                                /* END SETTING FEATURED IMAGE */
                                
							}							
						}
					}else{
						@mysql_query("ROLLBACK", $wpdb->dbh);
						return false;
					}
				add_action('save_post','my_meta_save');
			restore_current_blog();
		// }
	}
	return false;
}

function LP_record_message_sent($args)
{

}
/* 
 * SEND message to the list of contacts 
 * $contacts can be an array of contact linkedin IDs or just a string of the single linkedin user ID
 * $message is an array like array("subject" => "", "body" => "")
 */
function LP_send_this_message_to_linkedin($contacts, $message)
{
	$API_CONFIG = json_decode(LINKEDIN_API_CONFIG,true);
	// print_r($message);
	if($_SESSION['oauth']['linkedin']['authorized'] === TRUE){
		// include the LinkedIn class
		require_once('linkedin/linkedin_3.2.0.class.php');
        $OBJ_linkedin = new LinkedIn($API_CONFIG);
        $OBJ_linkedin->setTokenAccess($_SESSION['oauth']['linkedin']['access']);
    }
	$response = $OBJ_linkedin->message($contacts, $message["subject"], $message["body"], false);
	if($response['success'] === TRUE) {
	  // message has been sent
		return true;
	} else {
		// an error occured
		echo "Error sending message:<br /><br />RESPONSE:<br /><br /><pre>" . print_r($response, TRUE) . "</pre><br /><br />LINKEDIN OBJ:<br /><br /><pre>" . print_r($OBJ_linkedin, TRUE) . "</pre>";
		die();
	}
}

/* 
 *$args are mixed array of message, contacts .....
 */
function LP_process_message_template($args)
{
	// print_r($args);
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
		// "DripTitle",
		// "DripTopic",	
		// "DripChannel"
	// );
	if($drip["drip_url"]){
		$tag_val["DripURL"] = $drip["drip_url"];
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
	$fname = array("fname","Fname","fName","FName","first_name");
	$lname = array("lname","Lname","lName","LName","last_name");
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
    $res = file_put_contents($newfile, file_get_contents($url));
    if($res === false){
        return false;
    }else{
        return array("info" => $res, "newfile" => $newfile);
    }
    
}

function LP_user_has_twitter_token()
{
    if(LP_user_twitter_token()!==false){
        echo "1";
    }else{
        echo "0";
    }
    die();
}
add_action('wp_ajax_LP_user_has_twitter_token', 'LP_user_has_twitter_token');

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
	
	$blog_id = LP_get_user_blog_id();
	$post_id = $_POST["topic_id"];
	
	$meta_value = array(
				"search_sources"	=> $search_sources,
				"search_keywords"	=> $keywords,
				"rss_feed_links"	=> $rss_feed_links
			);
	switch_to_blog($blog_id);
		update_post_meta($post_id, "_collection_setup", json_encode($meta_value));
	restore_current_blog();
	$new_topic = LP_get_user_topics($post_id);
	echo json_encode($new_topic[0]);
	die();
}

add_action('wp_ajax_LP_save_topic_collection_setup', 'LP_save_topic_collection_setup');
//////////////////////// Linked Meter - START //////////////////////////////
require_once('meter/meter.php');
//////////////////////// Linked Meter - END ////////////////////////////////

//////////////////////// ADJUST Meter - START //////////////////////////////
require_once('adjust/adjust.php');
//////////////////////// ADJUST Meter - END ////////////////////////////////

///////////////////// LinkedIn Schedule - START ////////////////////////////
require_once('utility/linkedin.php');
///////////////////// LinkedIn Schedule - END //////////////////////////////


///////////////////// twitter api - START //////////////////////////////
function LP_twitter_aouth()
{
    /* Start session and load library. */
    session_start();
    require_once('twitter/twitteroauth.php');
    
    $twitter_api_config = json_decode(TWITTER_API_CONFIG,true);
    // print_r($twitter_api_config);
    define('CONSUMER_KEY', $twitter_api_config["CONSUMER_KEY"]);
    define('CONSUMER_SECRET',  $twitter_api_config["CONSUMER_SECRET"]);
    define('OAUTH_CALLBACK', $twitter_api_config["OAUTH_CALLBACK"]);

    /* Build TwitterOAuth object with client credentials. */
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
     
    /* Get temporary credentials. */
    $request_token = $connection->getRequestToken(OAUTH_CALLBACK);

    /* Save temporary credentials to session. */
    $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
    $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
     
    /* If last connection failed don't display authorization link. */
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

		$twitteroauth = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET, $oauth_token, $oauth_token_secret);

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

function LP_twitter_search(){
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
		// print_r($_POST);
		$tweets = $connection->get("https://api.twitter.com/1.1/search/tweets.json?q=news ".$_POST["term"]." filter:links&count=".$_POST["count"]);
		// foreach($tweets->statuses as $k => $tweet){
			// preg_match_all('/(http|www)\S+/', $tweet->text, $arr, PREG_PATTERN_ORDER);
			// $url = $arr[0][0];
			// if($url){
				// $tweets->statuses[$k]->parsed = LP_parse_website($url);
			// }
		// }
		echo json_encode($tweets);
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
?>
