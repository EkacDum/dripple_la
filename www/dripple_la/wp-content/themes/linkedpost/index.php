<?php
get_header();
?>
<div id="home">
    <?php if ( have_posts() ) while ( have_posts() ) : the_post();?>
	<div class="indgropostcat">
		<div class="inblogpostbody">
			<div class="sydcontenpost">
				<h1><?php the_title();?></h1>
						<?php the_content(); ?>
			</div>
		</div>
	</div>
    <?php endwhile; ?>
</div>
<?php
get_sidebar();
get_footer();
?>