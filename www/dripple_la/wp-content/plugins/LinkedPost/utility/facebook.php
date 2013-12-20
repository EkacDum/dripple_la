<?php

function LP_publish_drip_facebook($post_id, $facebook_token)
{
	global $current_site;
	$post = get_post($post_id);
	$blog_id = get_current_blog_id();
	$storyurl = LP_get_post_story_URL($post_id, $blog_id);
	require_once(dirname(__FILE__).'/../facebook/src/facebook.php');
	$facebook_api_config = json_decode(FACEBOOK_API_CONFIG,true);
	
	$facebook = new Facebook($facebook_api_config);
	$facebook->setAccessToken($facebook_token);
	
	$short_url = base_convert($storyurl["id"],10,36);
	if(strlen($short_url) < 6){
		$zeros = "000000";
		$short_url = substr($zeros,0,(6 - strlen($short_url))).$short_url;
	}
	
	$post_thumbnail = LP_get_post_thumb_url($post_id, $blog_id);
	
	$parameters = array(
			"message" 	=> substr($post->post_title,0,130),
			"picture"	=> $post_thumbnail,
			"link"		=> "http://drip.li/".$short_url,
			"description" => $post->post_excerpt
		);
	$response = $facebook->api('/me/feed','POST', $parameters);
	print_r($response);
}