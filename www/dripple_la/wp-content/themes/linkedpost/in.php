<?php
global  $user_id, $current_site, $post_channel, $author_avatar, $user_full_name;
get_header();
$LP_siteurl = trim(network_site_url(),"/");
$default_avatar = $LP_siteurl."/wp-content/themes/linkedpost/images/author.png"; 

$user = get_userdata($user_id);
$user_full_name = $user->data->display_name;

$blog_info = get_blog_option(get_current_blog_id(),"LP_linkedin_info");
$lin_profile = $blog_info["linkedin"];
$profile_pic = ($lin_profile->pictureUrls->values[0] ? $lin_profile->pictureUrls->values[0] : $lin_profile->pictureUrl);
if($profile_pic){
    $author_avatar = "<img src=\"".$profile_pic."\" width=\"80\" height=\"80\" alt=\"$user_full_name\"/>";
}else{
    $author_avatar = get_avatar($lin_profile->emailAddress, 72, $default_avatar);
}
        
?>
</div>
<div id="default_view_cont">
<div id="home">
	<?php get_template_part( "linkedin", "profile" );?>
    <div class="view_mode_container">
		<ul>
			<li class="iconheadli"><i id="topicview" class="topicview ico_class_sm_edit shown active"></i></li>
			<li class="iconheadli"><i id="dripview" class="dripview ico_class_sm_edit shown"></i></li>
		</ul>
	</div>
    <div id="topics_mode" class="topics_mode">
    <?php 
	wp_reset_postdata();
    $the_topics = new WP_Query();
	$the_topics->query(array("post_type"=>"topic","posts_per_page"=>12));
    while ( $the_topics->have_posts() ) : $the_topics->the_post();
		$post_channel = get_the_channel(get_the_ID(), get_current_blog_id());
		get_template_part( "topics", "tile" ); 
	endwhile; 
	wp_reset_postdata();
	?>
    </div>
 
    <div id="drip_mode">
		<?php 
		$the_posts = new WP_Query();
		$the_posts->query(array("post_type"=>"post","posts_per_page"=>12));
		$x=0;
		while ( $the_posts->have_posts() ) : $the_posts->the_post(); $x++;
			get_template_part( "single", "loop" );
		endwhile; 
		wp_reset_postdata();
        ?>
	</div>
</div>
<?php
get_sidebar();
?>
</div>
<?php
get_footer();
?>