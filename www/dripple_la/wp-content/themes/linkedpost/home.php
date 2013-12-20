<?php
/*
	Template Name: Home
*/
get_header();
$comments_count = wp_count_comments();
global $wpdb;         
?>
<div id="home">
	<?php if (query_posts( 'posts_per_page=10' )){ ?>
		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
		<?php $get_post = get_post();
		$postid = get_the_ID();
		$user_email = get_the_author_meta('user_email');
		$author_post_count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_author = '".$get_post->post_author."' AND post_type = 'post' AND post_status = 'publish'");
		?>
	<div class="indgropostcat">
		<div class="divblogpost">
			<div class="titlediv"><?php $cat = get_the_category(); echo $cat[0]->name;?></div>
			<div class="placewtym">New York Times / 3 mins</div>
			<div class="lsymodeimg">
				<img src="<?php bloginfo('template_url');?>/images/lstmode_boxlft.png" alt="">
				<img src="<?php bloginfo('template_url');?>/images/lstmode_boxryt.png" alt="">
			</div>
		</div>
		<div class="authordivindiimg">
        <?php 
            $user_info = get_user_meta(get_the_author_ID());
            $profile_pic = ($user_info["linkedin_profile_pic"][0] ? $user_info["linkedin_profile_pic"][0] : $user_info["linkedin_profile_thumb"][0]);
            if($profile_pic){
                $author_avatar = "<img src=\"".$profile_pic."\" width=\"72\" heigjt=\"72\"/>";
            }else{
                $author_avatar = get_avatar($user_email, 72, $default_avatar); 
            }
        ?>
        
			<a href="<?php the_permalink(); ?>"><?php echo $author_avatar; ?></a>
			<span class="cntartno"><?php echo $author_post_count; ?></span>
			<p class="artclpdiv">articles</p>
		</div>
		<?php $to_cloaked = get_post_cloaked_URL(get_the_ID()); ?>
		<div class="inblogpostbody">
			<div class="authordivindi"><a href="<?php the_permalink(); ?>"><?php the_author(); ?></a></div>
			<p style="width: 563px !important;"><p><a href="<?php echo get_option('siteurl')."/lp/".$to_cloaked; ?>/"><?php echo substr(get_the_excerpt(), 0,90); ?></a></p></p>
			
			
			
			<?php if (has_post_thumbnail()) {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID )); 
			
			?>
			<div class="imgblogpost">
				<a href="<?php echo get_option('siteurl')."/lp/".$to_cloaked; ?>/"><img style="width:180px;" src="<?php echo $image[0];?>" /></a>
			</div>
			<?php } ?>
			
			<div class="sydcontenpost">
			<?php if($to_cloaked!=""){ ?>
			<h2><a href="<?php echo get_option('siteurl')."/lp/".$to_cloaked; ?>/"><?php echo substr(the_title(), 0,120); ?></a></h2>
			<?php }else{ ?>
			<h2><a href="<?php echo get_option('siteurl')."/lp/".$to_cloaked; ?>/"><?php echo substr(the_title(), 0,120); ?></a></h2>
			<?php } ?>
				<!--?php the_excerpt();?> -->
				<p><a href="<?php echo get_option('siteurl')."/lp/".$to_cloaked; ?>/"><?php echo substr(get_the_excerpt(), 0,250); ?></a></p>
			</div>
			<div class="tagpstcommnt">
                <?php the_tags('<ul class="li_tagpost"><li>','</li><li>','</li></ul>'); ?>
            </div>
		</div>
		<div class="comment_read">
			<div class="see_font">
			<!--<span class="commentmore">Comments</span>-->
				<div class="divcomllykimg"><img src="<?php bloginfo('template_url');?>/images/artciletot_logo.png"><span><?php echo do_shortcode('[post_view]'); ?></span></div>
				<div class="divcomllykimg"><img src="<?php bloginfo('template_url');?>/images/viewslogo.png"><span><?php 
						$comments_count = wp_count_comments($postid);
						echo $comments_count->total_comments; ?></span>
				</div>
				<div class="divcomllykimg"><span><?php echo do_shortcode('[dot_recommends]'); ?></span></div>
				<?php if($to_cloaked!=""){ ?>
				<a href="<?php echo get_option('siteurl')."/lp/".$to_cloaked; ?>/"><span class="readmre">Read More</span></a>
				<?php } ?>
			</div>
		</div>
	</div>
		<?php endwhile; ?>
	<?php } ?>
</div>
<?php
get_sidebar();
get_footer();
?>