<?php

function LP_publishToLikedInWall($id, $whereToPost = "", $postPrivacy ='anyone' , $postData = NULL) {

    $API_CONFIG = json_decode(LINKEDIN_API_CONFIG,true);

    require_once(WP_PLUGIN_DIR ."/LinkedPost/linkedin/linkedin_3.2.0.class.php");
    
    if($postData == NULL) {
        $postData = getPostDetailByID($id);
    }

    if( !empty($postData['author_id']) ) {
        // $user_info  = get_user_meta($postData['author_id']);
		$blog_id = get_current_blog_id();
		$user_info = get_blog_option($blog_id,"LP_linkedin_info");
		
        $utoken     = maybe_unserialize($user_info["linkedin_TokenAccess"]);

        $OBJ_linkedin = new LinkedIn($API_CONFIG);
        $OBJ_linkedin->setTokenAccess($utoken);
    }else{
        return false;
    }

    if(trim($whereToPost['profile']) == '' && !is_array($whereToPost['group']))
        return false;
    
    if(count($postData) > 0) {

        $content = array();
        if( !empty($postData['story_URL']) ) {
            // $content['submitted-url'] = ($postData['link'] == 'post_link' ? get_permalink($id) : $postData['link']);
            $content['submitted-url'] = htmlentities($postData['story_URL']);
        }
        if( !empty($postData['name']) ) {
            $content['title'] = substr($postData['name'],0,200);
        }
        if( !empty($postData['description']) ) {
            $content['description'] = substr($postData['description'],0,256);
        }
        if( !empty($postData['comment']) ) {
            $content['comment'] =  substr($postData['comment'],0,700 );
        }
        if( !empty($postData['picture']) ) {
            $content['submitted-image-url'] = $postData['picture'];
        }
        
        try{
            if( trim($whereToPost['profile']) == 'on' ) {
                $postPrivacy = $postPrivacy == 'anyone' ? false : true;
				// print_r($content); 
                $response = $OBJ_linkedin->share('new', $content, $postPrivacy);
                //LP_send_message_to_lin_industry($id, $postData['author_id']);
            }
            if(is_array(($whereToPost['group'])) && count($whereToPost['group'])>0) {
				$File = "lin_logs_".$postData['author_id'].".txt";
				$Handle = fopen($File, 'a');
                foreach($whereToPost['group'] as $group_id ) {
                    if( !empty($postData['discussion_title']) ) {
                        $content['discussion_title'] = substr($postData['discussion_title'],0,200);
                    }
                    if( !empty($postData['discussion_summary']) ) {
                        $content['discussion_summary'] = substr($postData['discussion_summary'],0,256);
                    }
					// print_r($content);
                    $response = $OBJ_linkedin->createPost($group_id, $content);
					// print_r($response);
					
					
					$datax = '<?xml version="1.0" encoding="UTF-8"?>				 
					<post>
						<title>'. $content["title"] . '</title>
						<summary>' . $content["comment"] . '</summary>
						<content>
							<submitted-url>' . $content["submitted-url"] . '</submitted-url>
							<submitted-image-url>' . $content["submitted-image-url"] . '</submitted-image-url>
							<title>' . $content["title"] . '</title>
							<description>' . $content["description"] . '</description>
						</content>
					</post>';
					
					$Data = "\n\r\n\r----------------------\n\r";
					$Data.= "USER : {$postData['author_id']}\n\r";
					$Data.= "DATE : ".date("Y-m-d h:i:s")."\n\r";
					$Data.= "GROUP ID : $group_id\n\r";
					$Data.= "REQUEST : $datax\n\r";
					$Data.= json_encode($response) ."\n\r";
					$Data.= "----------------------\n\r"; 
					fwrite($Handle, $Data); 
					 
                }
				fclose($Handle);
            }
            
            if($response['success'] === TRUE) {
                return true;
            }else{
                return $response;
            }
        }
        catch(LinkedInException $e) {
            echo $e->getMessage();
            die();
        }
    }
}

function getPostDetailByID($id,$blog_id = 0){
	// global $switched;

    if((int)$id > 0){
		if($blog_id == 0){
			// $blog_id = LP_get_user_blog_id();
			$blog_id = get_current_blog_id();
		}

		// switch_to_blog($blog_id);
		$post = get_post( $id, "ARRAY_A");	
		$post_thumbnail = LP_get_post_thumb_url($id, $blog_id);
		$to_return = array(
            'name' 					=> $post["post_title"],
            'link' 					=> $post["guid"],
            'description' 			=> $post["post_content"],
            'comment' 				=> $post["post_excerpt"],
            'picture'	 			=> $post_thumbnail,
            'discussion_title' 		=> $post["post_title"],
            'discussion_summary' 	=> $post["post_content"],
            'author_id'             => $post["post_author"]
        );
		$story_URL = LP_get_post_story_URL($id, $blog_id);
		$to_return["story_URL"] = $story_URL["story_URL"];
		// restore_current_blog();
        return $to_return;
    }
    return array();
}

function LP_publish_drip($post_id,$published =1)
{
    global $wpdb;
    $current_blog = get_current_blog_id();
    $prefix = "wp_";
    if($current_blog == 1){
        $wp_lp_drip_queue = $prefix."lp_drip_queue";
        $wp_posts = $prefix."posts";
    }else{
        $wp_lp_drip_queue = $prefix.$current_blog."_lp_drip_queue";
        $wp_posts = $prefix.$current_blog."_posts";
    }
    $sql = "UPDATE `$wp_lp_drip_queue` SET `is_published`=$published WHERE `blog_id`=$current_blog AND `post_id`=$post_id";
    $wpdb->query($sql);
    return $post_id;
}

function get_drip_status($post_id)
{
    global $wpdb;
    $current_blog = get_current_blog_id();
    $prefix = $wpdb->base_prefix;
    if($current_blog == 1){
        $wp_lp_drip_queue = $prefix."lp_drip_queue";
        $wp_posts = $prefix."posts";
    }else{
        $wp_lp_drip_queue = $prefix.$current_blog."_lp_drip_queue";
        $wp_posts = $prefix.$current_blog."_posts";
    }
    $sql = "SELECT * FROM `$wp_lp_drip_queue` WHERE `blog_id`=$current_blog AND `post_id`=$post_id";
    $drip = $wpdb->get_results($sql,ARRAY_A);
    if(isset($drip[0]["is_published"]))
        return $drip[0]["is_published"];
    else return 1;
}

function LP_update_drip_topic($post_id,$blog_id)
{
	global $wpdb;
	$db_name = LP_get_blog_db_name($blog_id);
	$prefix = $wpdb->base_prefix;
	$topic_id = get_post_meta( $post_id, '_LP_topic', true );
	$sql = "UPDATE `{$db_name}`.`{$prefix}{$blog_id}_posts` SET `post_parent`={$topic_id} WHERE `ID` = {$post_id}";
	$wpdb->query($sql);
}

function LP_ripple_this_drip($post_id)
{

	$topic_id = get_post_meta( $post_id, '_LP_topic', true );
	// echo "$topic_id = get_post_meta( $post_id, '_LP_topic', true );";
	$blog_id = get_current_blog_id();
	// echo "$blog_id = get_current_blog_id();";
	// Get topic ripple settings
	$topic_ripple = json_decode(get_post_meta($topic_id,"_LP_ripple",true));
	if(isset($topic_ripple->li_activity)){
		// Set to post to linkedin user activity
        $whereToPost["profile"] = "on";
	}
	
	if(isset($topic_ripple->li_groups->groups) && count($topic_ripple->li_groups->groups) > 0){
		foreach($topic_ripple->li_groups->groups as $key => $val){
			// Listing all linkedin user group ids
			$whereToPost["group"][$key] = $val->id;
		}
	}
	// posting to Twitter
	$ripple_twitter = json_decode(get_blog_option($blog_id, "_LP_ripple_twitter"),true);
	if(isset($topic_ripple->twitter)){
		foreach($topic_ripple->twitter->account as $key => $val){
			// starting to riple to twitter here.
			$twitter_token = $ripple_twitter["t_".$val->id]["access_token"];
			LP_publish_drip_twitter($post_id, $twitter_token);
		}
	}
	
	// posting to Facebook
	$ripple_facebook = json_decode(get_blog_option($blog_id, "_LP_ripple_facebook"),true);
	if(isset($topic_ripple->facebook)){
		foreach($topic_ripple->facebook->account as $key => $val){
			// starting to riple to facebook here.
			$facebook_token = $ripple_facebook["f_".$val->id]["access_token"];
			LP_publish_drip_facebook($post_id, $facebook_token);
		}
	}

	//posting to linkedin
	if(is_array($whereToPost)){
		LP_publishToLikedInWall($post_id, $whereToPost);
	}
	LP_call_maintain_num_dirps($post_id);
}

function LP_publish_future_drip($post_id)
{
	$post_type = get_post_type($post_id);
	$post_status = get_post_status($post_id);
    if($post_type == "post"){
		$blog_id = get_current_blog_id();
		LP_update_drip_topic($post_id,$blog_id);
        if($post_status=="publish"){
			LP_ripple_this_drip($post_id);
        }
    }
    return $post_id;
}
add_filter ( 'publish_future_post', 'LP_publish_future_drip' );


/* LINKEDIN GROUPS */
add_action('wp_ajax_LP_fetch_ripple_socials', 'LP_fetch_ripple_socials');
function LP_fetch_ripple_socials()
{
	$blog_socials = LP_fetch_blog_socials();
	$system_socials = LP_fetch_system_socials();
	if(count($blog_socials) > 0){
		$ripple_others = array_merge($system_socials["others"],$blog_socials["others"]);
		$system_socials["others"] = $ripple_others;
	}
	echo json_encode($system_socials);
    die();
}

add_action('wp_ajax_LP_fetch_user_lin_groups', 'LP_fetch_user_lin_groups');
function LP_fetch_user_lin_groups()
{
    $API_CONFIG = json_decode(LINKEDIN_API_CONFIG,true);
    require_once(WP_PLUGIN_DIR ."/LinkedPost/linkedin/linkedin_3.2.0.class.php");

    $blog_id = LP_get_user_blog_id();
    $user_info = get_blog_option($blog_id,"LP_linkedin_info");
    
    $utoken     = maybe_unserialize($user_info["linkedin_TokenAccess"]);
    
    $OBJ_linkedin = new LinkedIn($API_CONFIG);
    $OBJ_linkedin->setTokenAccess($utoken);
    $groupMemberships["member"]     = $OBJ_linkedin->groupMemberships(":(group:(id,name,counts-by-category,small-logo-url))","member",500);
    $groupMemberships["moderator"]  = $OBJ_linkedin->groupMemberships(":(group:(id,name,counts-by-category,small-logo-url))","moderator",500);
    $groupMemberships["manager"]    = $OBJ_linkedin->groupMemberships(":(group:(id,name,counts-by-category,small-logo-url))","manager",500);
    $groupMemberships["owner"]      = $OBJ_linkedin->groupMemberships(":(group:(id,name,counts-by-category,small-logo-url))","owner",500);
    // print_r($groupMemberships);
    $groups = array();
    foreach($groupMemberships as $key => $state){
        $linkedin = json_decode($state["linkedin"]);
        foreach($linkedin->values as $group){
            $groups[] = $group;
        }
    }
    update_blog_option($blog_id, "_LP_lin_groups", json_encode($groups));
    LP_fetch_ripple_socials();
    die();
}

function LP_fetch_blog_socials()
{
	$blog_id = LP_get_user_blog_id();
	$blog_socials = array();
	$ripple_twitter = json_decode(get_blog_option($blog_id, "_LP_ripple_twitter"));
	if($ripple_twitter){
		$blog_socials["others"]["twitter"]["accounts"] 		= $ripple_twitter;
		$blog_socials["others"]["twitter"]["smallLogoUrl"] 	= "http://www.drippost.com/wp-content/themes/linkedpost/images/twitter-icon.png";
		$blog_socials["others"]["twitter"]["soc_name"] 		= "Twitter";
	}
	
	$ripple_facebook = json_decode(get_blog_option($blog_id, "_LP_ripple_facebook"));
	if($ripple_facebook){
		$blog_socials["others"]["facebook"]["accounts"] 		= $ripple_facebook;
		$blog_socials["others"]["facebook"]["smallLogoUrl"] 	= "https://www.google.com/s2/favicons?domain=http://newsroom.fb.com/";
		$blog_socials["others"]["facebook"]["soc_name"] 		= "Facebook";
	}
	
	return $blog_socials;
}

function LP_fetch_system_socials()
{
    global $switched;
    $blog_id = LP_get_user_blog_id();
    switch_to_blog($blog_id);
    $groups = json_decode(get_blog_option($blog_id, "_LP_lin_groups"));
	restore_current_blog();
    $socials["others"] = array(
                        "twitter"   => array("id" => "", "name" => "Twitter", "smallLogoUrl" => "http://www.drippost.com/wp-content/themes/linkedpost/images/twitter-icon.png"),
                        "facebook"  => array("id" => "", "name" => "Facebook", "smallLogoUrl" => "https://www.google.com/s2/favicons?domain=http://newsroom.fb.com/")
                        );
                        
    $socials["linkedin"] = array(
                        "li_activity"   => array("id" => "", "name" => "LinkedIn Activity", "smallLogoUrl" => "https://www.google.com/s2/favicons?domain=http://engineering.linkedin.com/"),
                        "li_groups"     => array("id" => "", "name" => "LinkedIn Groups","groups" => $groups, "smallLogoUrl" => "https://www.google.com/s2/favicons?domain=http://engineering.linkedin.com/")
                        );
	return $socials;
}
/* END LINKEDIN GROUPS */


function LP_send_message_to_lin_industry($post_id, $user_id, $re_fill = false, $ex_industry_name = "", $count = 10){
    $blog_id = get_current_blog_id();
    $topic = LP_get_post_topic($post_id, $blog_id);
    $channel = LP_get_topic_channel($topic["ID"],$blog_id);
    $topic_id = $topic["ID"];
    /* **** SENDING oF MESSAGE **** */
    $linkedin_message = LP_get_user_blog_option("LINKEDIN_MESSAGE",$user_id);
    //echo $linkedin_message;
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
    if(!$re_fill){
        $topic_industry = get_post_meta($topic_id, "_industry", true);
        $lin_contacts = LP_get_user_linkedin_connections($user_id);
        global $industries;
        foreach($industries as $industry){
            if($industry["code"] == $topic_industry){
                $industry_name = $industry["description"];
                $contacts = $lin_contacts[$industry_name];
                break;
            }
        }
    }else{
        $lin_contacts = LP_get_user_linkedin_connections($user_id, false, $ex_industry_name);
        $contacts = $lin_contacts["all"];
    }
   /* [firstName] => Craig
    [lastName] => Wilson
    [id] => Otr-lzXrJX
    [industry] => Hospitality*/

    $drip 	= get_post($post_id);
    $storyurl = LP_get_post_story_URL($post_id, $blog_id);

    $drip->topic_name = $topic["post_title"];
    $drip->channel_name = $channel["name"];
    $drip->drip_url = Lp_get_drip_url($drip);
    $drip->short_article_url = LP_generate_short_drip_url($storyurl["id"]);
    $drip->article_url = $storyurl["story_URL"];
    $odrip = json_encode($drip);
    $drip = json_decode($odrip, true);
    $a = 0;
    if(is_array($contacts) && count($contacts["contacts"]>0)){
        $all_concatcts = $contacts["contacts"];
        foreach($all_concatcts as $contact){
            $args = array(
                "message"	=> $message,
                "contact"	=> $contact,
                "member"	=> $member,
                "drip"		=> $drip
            );
            $details = LP_process_message_template($args);
            $res = LP_send_this_message_to_linkedin($blog_id, array($details["contact"]["id"]), $details["message"]);
            $a++;
            if($a>=$count){break;}
        }
    }
    if($a < $count){
        if(!$re_fill){
            LP_send_message_to_lin_industry($post_id, $user_id, true, $industry_name, (10-$a));
        }
    }

    /* **** END SENDING oF MESSAGE **** */
}