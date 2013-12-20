<?php

function LP_publish_drip_twitter($post_id, $twitter_token)
{
	global $current_site;
	$post = get_post($post_id);
	$blog_id = get_current_blog_id();
	$storyurl = LP_get_post_story_URL($post_id, $blog_id);
	require_once(dirname(__FILE__).'/../twitter/twitteroauth.php');
	$twitter_api_config = json_decode(TWITTER_API_CONFIG,true);
	// define('CONSUMER_KEY', $twitter_api_config["CONSUMER_KEY"]);
	// define('CONSUMER_SECRET',  $twitter_api_config["CONSUMER_SECRET"]);
	$connection = new TwitterOAuth($twitter_api_config["CONSUMER_KEY"], $twitter_api_config["CONSUMER_SECRET"], $twitter_token["oauth_token"], $twitter_token["oauth_token_secret"]);
	$url = "https://api.twitter.com/1.1/statuses/update.json";
	
	$short_url = base_convert($storyurl["id"],10,36);
	if(strlen($short_url) < 6){
		$zeros = "000000";
		$short_url = substr($zeros,0,(6 - strlen($short_url))).$short_url;
	}
	$parameters = array(
			"status" => substr($post->post_title,0,130)." http://drip.li/".$short_url
		);
	$response = $connection->post($url, $parameters);
	// print_r($response);
}