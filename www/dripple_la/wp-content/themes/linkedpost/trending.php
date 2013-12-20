<?php
/*
	Template Name: Trending
*/
get_header();
$comments_count = wp_count_comments();
// print_r(do_shortcode('[most_view]')); 

?>
<?php $posts_per_page = get_query_var('posts_per_page'); ?>
<?php $paged = intval(get_query_var('paged')); ?>
<?php $paged = ($paged) ? $paged : 1; ?>
<?php $args=array(
 'posts_per_page'      => 15, 
 'post_type'     => 'post', 
 'key' => 'views',
 'orderby' => 'meta_value_num', 
 'order'    => 'ASC',
 'post_status' => 'publish'
);

print_r(query_posts($args)); ?>

<div id="home">
	<?php if (query_posts( 'posts_per_page=10' )){ ?>
		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
		<?php $postid = get_the_ID(); ?>
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
			<img src="<?php bloginfo('template_url');?>/images/author.png">
			<span class="cntartno">2,671</span>
			<p class="artclpdiv">articles</p>
		</div>
		<div class="inblogpostbody">
			<div class="authordivindi"><?php the_author(); ?></div>
			<p style="width: 563px !important;"><?php echo word_trim(get_the_excerpt(), 20,' ...');?></p>
			<div class="imgblogpost"><?php if ( has_post_thumbnail() ) { the_post_thumbnail('medium'); } ?></div>
			<div class="sydcontenpost">
				<h2><?php the_title();?></h2>
				<?php the_excerpt();?>
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
				<a href="<?php the_permalink(); ?>"><span class="readmre">Read More</span></a>
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