<?php
/*
	Template Name: Topics
*/
global $current_site;
$LP_siteurl = trim(network_site_url(),"/");
get_header();
$default_avatar = $LP_siteurl . "/wp-content/themes/linkedpost/images/author.png";

?>
<?php global $user_id, $author_avatar, $user_full_name, $post_channel; ?>
</div>
<div>
<div id="tile_view">
	<div class="topics_head">
		<h2>Dripping Hot Topics</h2>
	</div>
    <div class="topics_cont">
        <?php
        $user = get_userdata($user_id);
        // print_r($user);
        $user_full_name = $user->data->display_name;
       	   
        $blog_info = get_blog_option(get_current_blog_id(),"LP_linkedin_info");
        $lin_profile = $blog_info["linkedin"];
		$profile_pic = ($lin_profile->pictureUrls->values[0] ? $lin_profile->pictureUrls->values[0] : $lin_profile->pictureUrl);

        if($profile_pic){
            $author_avatar = "<img src=\"".$profile_pic."\" width=\"80\" heigjt=\"80\"/>";
        }else{
            $author_avatar = get_avatar($user_email, 72, $default_avatar);
        }
                
        
        query_posts("post_type=topic");
        while ( have_posts() ) : the_post();
        $post_channel = get_the_channel(get_the_ID(), get_current_blog_id());
        ?>
		<?php get_template_part( "topics", "tile" );?>
        <?php endwhile; ?>
    </div>
</div>
<?php
get_footer();
?>