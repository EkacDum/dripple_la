<?php
/*
	Template Name: Trending This Week
*/
get_header();
?>
<div id="home">
	<?php 
	$curYear = date('Y'); 
	$date_string = date('Y-m-d'); 
	$week = date("W", strtotime($date_string));
	$thedate = $curYear.$week;
	if (query_posts( array( 
		  "orderby" => "meta_value_num",
		  "meta_key" => "_count-views_week-".$thedate."",
		  "order" => "DESC"
		  ))){ 
	?>
	<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	<?php $postid = get_the_ID(); 
	$comments_count = wp_count_comments();
	?>
	<?php $to_cloaked = get_post_meta(get_the_ID(),"cloaked_URL",true); ?>
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
			<a href="<?php the_permalink();?>"><img src="<?php bloginfo('template_url');?>/images/author.png"></a>
			<span class="cntartno">2,671</span>
			<p class="artclpdiv">articles</p>
		</div>
		<div class="inblogpostbody">
			<div class="authordivindi"><a href="<?php the_permalink(); ?>"><?php the_author(); ?></a></div>
			<p style="width: 563px !important;"><p><a href="<?php echo get_option('siteurl')."/".$to_cloaked; ?>/"><?php echo substr(get_the_excerpt(), 0,90); ?></a></p></p>
			<div class="imgblogpost"><?php if ( has_post_thumbnail() ) { the_post_thumbnail('medium'); } ?></div>
			<div class="sydcontenpost">
			<?php if($to_cloaked!=""){ ?>
			<h2><a href="<?php echo get_option('siteurl')."/".$to_cloaked; ?>/"><?php echo substr(the_title(), 0,120); ?></a></h2>
			<?php }else{ ?>
			<h2><?php echo substr(the_title(), 0,120); ?></h2>
			<?php } ?>
				<!--?php the_excerpt();?> -->
				<p><?php echo substr(get_the_excerpt(), 0,250); ?></p>
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
				<a href="<?php echo get_option('siteurl')."/".$to_cloaked; ?>/"><span class="readmre">Read More</span></a>
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