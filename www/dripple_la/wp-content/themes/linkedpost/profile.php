<?php
/*
	Template Name: Profile
*/
get_header();
?>
<div id="home">
	<div class="indgropostcat">
		<div class="profile_div">
			<img src="<?php bloginfo('template_url');?>/images/profile_pic.png" />
			<div class="prof_det">
				<span id="prof_name">Dr. Perry Wong Msc.D</span>
				<span id="prof_pos">Private Equity Director at Sequoia Ethereal Equity Development</span>
				<span id="prof_loc">Newport Beach, California | Real Estate</span>
				<div class="prof_wrkeduc">
					<span class="prof_wrkedul">Current:</span><span class="prof_wrkedui">Sequoia Ethereal Equity Development</span>
				</div>
				<div class="prof_wrkeduc">
					<span class="prof_wrkedul">Previous:</span><span class="prof_wrkedui">U.P Investments, Capital Market</span>
				</div>
				<div class="prof_wrkeduc">
					<span class="prof_wrkedul">Education:</span><span class="prof_wrkedui">University of Southern California</span>
				</div>
				<div class="prof_linkedin">LinkedIn Profile</div>
			</div>
		</div>
		<div id="rankingdiv">
			<div id="rankdiv">
				<span id="rank_fo">1245</span>
			</div>
			<span id="rank_l">ranking</span>
		</div>
		<div class="see_more">
			<div class="see_font"><span style="float: left;margin-left: 300px;">&or;</span> <span style="margin-top: 2px;float: left;">See More</span> <span style="float: left;">&or;</span></div>
		</div>
	</div>
	<?php if (query_posts( 'posts_per_page=10' )){ ?>
		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
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
			<img src="<?php bloginfo('template_url');?>/images/publi_logo.png">
		</div>
		<div class="inblogpostbody">
			<div class="authordivindi"><?php the_author(); ?></div>
			<p style="width: 563px !important;"><?php echo word_trim(get_the_excerpt(), 20,' ...');?></p>
			<div class="imgblogpost"><?php if ( has_post_thumbnail() ) { the_post_thumbnail('medium'); } ?></div>
			<div class="sydcontenpost">
				<h2><?php the_title();?></h2>
				<?php the_excerpt();?>
			</div>
		</div>
		<div class="comment_read">
			<div class="see_font">
			<!--<span class="commentmore">Comments</span>-->
				<div class="divcomllykimg"><img src="<?php bloginfo('template_url');?>/images/artciletot_logo.png"><span>6,500</span></div>
				<div class="divcomllykimg"><img src="<?php bloginfo('template_url');?>/images/viewslogo.png"><span>337</span></div>
				<div class="divcomllykimg"><img src="<?php bloginfo('template_url');?>/images/commentlogo.png"><span>6,160</span></div>
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