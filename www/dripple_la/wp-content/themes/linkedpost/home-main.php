<?php
/*
	Template Name: Home
*/
get_header();
global $wpdb;
global $current_site;
global $blog_posts;
$LP_siteurl = "http://".$current_site->domain;

$member_chans = "";
if(is_user_logged_in()){
	$member_chans = LP_get_user_channels();
}
// print_r($member_chans);
$blog_posts = LP_latest_posts($member_chans); 
// print_r($blog_posts);

global $test_view_type;
$view = $test_view_type;
switch($view){
    case "drip":
        get_template_part( "home", "drip" );
    break;
	case "tile":
        get_template_part( "home", "tile" );
    break;
    case "mash":
        get_template_part( "home", "mash" );
    break;
    case "icons":
       get_template_part( "home", "icons" );
    break;
    default :
        get_template_part( "home", "default" );
    break;
}
get_footer();
?>