<?php
global $current_site, $author_avatar, $user_full_name;
get_header();
$LP_siteurl = trim(network_site_url(),"/");
$default_avatar = $LP_siteurl."/wp-content/themes/LinkedPOST/images/author.png";

$user_info = get_blog_option(get_current_blog_id(),"LP_linkedin_info");
$profile_pic = ($user_info["linkedin_profile_pic"] ? $user_info["linkedin_profile_pic"] : $user_info["linkedin_profile_thumb"]);

if($profile_pic){
    $author_avatar = "<img src=\"".$profile_pic."\" width=\"80\" height=\"80\"/>";
}else{
    $author_avatar = get_avatar($user_email, 72, $default_avatar);
}
?>
aaaaaaaaaaaaaa
<?php bloginfo('template_url'); ?>
</div>
<div id="default_view_cont">
<div id="home">
	<?php get_template_part( "linkedin", "profile" );?>
	<div class="view_mode_container">
		<li class="iconheadli"><i id="topicview" class="topicview ico_class_sm_edit shown"></i></li>
		<li class="iconheadli"><i id="dripview" class="dripview ico_class_sm_edit shown active"></i></li>
	</div>
	<div id="drip_mode" style="display:block">
		<?php if ( have_posts() ) while ( have_posts() ) : the_post();
				$post_id = get_the_ID();
				$blog_id = get_current_blog_id();
				$post_channel = get_the_channel($post_id, $blog_id);
				$post_thumb = LP_get_post_thumb_url($post_id, $blog_id, $size = "lp-thumb-xlarge");
		?>
		<div class="single_topic_holder">		
			<div class="tile_xfeat_img_div">                
				<img width="662px" src="<?php echo $post_thumb;?>" />
			</div>			
			<div class="tile_low">											
				<div class="tile_post_cont_div">					
					<span class="topic_chann GenericSlabBold single_topic"><a href="<?php echo LP_channel_url($post_channel["name"]); ?>"><?php echo $post_channel["name"];?></a></span>		
					<span class="topic_drips_count">4 drips / last <?php echo LP_get_post_age(get_the_date( "U", get_the_ID()));?></span>
					<span class="single_topic tile_post_title GenericSlabBold"><?php the_title();?></span>	
					<span class="tile_post_cont single_topic"><?php the_content();?></span>			
				</div>							
				<div class="comment_read">
					<div class="divcomllykimg"><i class="eye-l-18"></i><span>1.1K</span></div>
					<div class="divcomllykimg"><i class="greytotldrips-l-18"></i><span>.5K</span></div>
					<div class="divcomllykimg"><i class="thumbsup-l-18"></i><span>15</span></div>
					<div class="divcomllykimg"></div>
					<div class="divcomllykimgright"><i class="ribbon-l-18"></i></div>
					<div class="divcomllykimgright"><i class="envelop-l-18"></i></div>
				</div>		
			</div>		
		</div>
		<?php endwhile; ?>
		<?php 
		$args = array(
			"post_type"     => "post",
			"post_parent"   => $post_id,
		);
		query_posts($args);
		if ( have_posts() ) while ( have_posts() ) : the_post();
			get_template_part( "single", "loop" );
		endwhile;
		?>
	</div>	
	<div id="topics_mode" class="topics_mode" style="display:none">
	<?php 
    query_posts("post_type=topic&posts_per_page=12");
    while ( have_posts() ) : the_post();
    $post_channel = get_the_channel(get_the_ID(), get_current_blog_id());
    ?>
    <?php get_template_part( "topics", "tile" );?>
    <?php endwhile; ?>
	</div>
</div>
<script>

	var time = new Date().getTime();

	function refresh() {
		if(new Date().getTime() - time >= 60000) 
		window.location.reload(true);
		else 
		setTimeout(refresh, 10000);
	}
	
	jQuery(document.body).bind("mousemove keypress", function(e) {
		time = new Date().getTime();
	});

	setTimeout(refresh, 10000);
</script>
<?php
get_sidebar();
get_footer();
?>