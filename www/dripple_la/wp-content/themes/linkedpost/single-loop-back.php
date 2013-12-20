<?php
if($post->ID)
    $arg = array(
            "post__not_in"   => array($post->ID),
            "posts_per_page" => 10
            );
else 
    $arg = "&posts_per_page=10";
?>

<?php if (query_posts( $arg )){ ?>
		<?php if ( have_posts() ) while ( have_posts() ) : the_post();?>
	<div class="indgropostcat">
		<div class="divblogpost">
				<div class="mash_prof_det_div">
					<span class="mash_hot_chann mash_trend_chann">
						<?php 
							$sep = "";
							foreach(get_the_channel(get_the_ID(), get_current_blog_id()) as $chan){echo $sep.$chan["name"];}
						?>Marketing
					</span>
					<span class="source_site mash_source_site">monster.com / 2 days</span>
					<i class="mash_redrip_ico"></i>
					<i class="markread_ico"></i>
				</div>
			<!-- <div class="titlediv gray_chann"><?php 
            $sep = "";
            foreach(get_the_channel(get_the_ID(), get_current_blog_id()) as $chan){echo $sep.$chan["name"];}?>
			</div> -->
			<div class="placewtym">New York Times / 3 mins</div>
			<div class="lsymodeimg">
				<img src="<?php bloginfo('template_url');?>/images/lstmode_boxlft.png" alt="">
				<img src="<?php bloginfo('template_url');?>/images/lstmode_boxryt.png" alt="">
			</div>
		</div>
		<div class="inblogpostbody">
			<div class="authordivindiimg bluename">
				<a href="<?php the_permalink(); ?>"><img src="<?php bloginfo('template_url');?>/images/publi_logo.png"></a>
			</div>
			<div class="authordivindi"><a href="<?php the_permalink(); ?>"><?php echo get_the_author_meta("display_name"); ?></a></div>
            <span>Topic Here</span>
            <?php 
                $get_the_excerpt = get_the_excerpt();
                $get_the_content = get_the_content();
                if($get_the_excerpt!=""){
                    $the_excerpt = $get_the_excerpt;
                }else{
                    $the_excerpt = strip_tags($get_the_content);
                }
            $to_cloaked =  get_post_meta( get_the_ID(), 'cloaked_URL' ); 
            if(count($to_cloaked)>0){
                $to_cloaked = $to_cloaked["cloaked_URL"];
            }else{
                $to_cloaked = "";
            }
            ?>
            
			<?php if($to_cloaked!=""){ ?>
			<h2><a href="<?php echo $LP_siteurl."/lp/".$to_cloaked; ?>/"><?php echo substr(the_title(), 0,120); ?></a></h2>
			<?php }else{ ?>
			<h2><?php echo substr(the_title(), 0,120); ?></h2>
			<?php } ?>
			<div class="sydcontenpost">
				<!--?php the_excerpt();?> -->
				<span>
                <?php if (has_post_thumbnail()) {
                    $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID())); 
                ?>
                    <div class="imgblogpost">
                        <img style="width:180px;" src="<?php echo $image[0];?>" />
                    </div>
                <?php } ?>
            <?php if($to_cloaked!=""){?>
                <p><a href="<?php echo get_option('siteurl')."/lp/".$to_cloaked; ?>/"><?php echo substr($the_excerpt, 0,90); ?></a></p>
            <?php }else{?>
                <p style="width: 563px !important;"><p><?php echo substr($the_excerpt, 0,90); ?></p>
            <?php } ?>
                
                <p><?php echo substr($get_the_content, 0,400); ?></p>
                </span>
			</div>
			<div class="tagpstcommnt">
                <?php echo get_the_term_list( get_the_ID(), 'post_tag', '<ul class="li_tagpost"><li>','</li><li>','</li></ul>'); ?>
            </div>
		</div>
		<div class="comment_read">
			<div class="see_font">
			<!--<span class="commentmore">Comments</span>-->
				<div class="divcomllykimg"><img src="<?php bloginfo('template_url');?>/images/artciletot_logo.png"><span><?php echo do_shortcode('[post_view]'); ?></span></div>
				<div class="divcomllykimg"><img src="<?php bloginfo('template_url');?>/images/viewslogo.png"><span><?php $comments_count = wp_count_comments( get_the_ID() ); echo $comments_count->approved;?></span>
				</div>
				<div class="divcomllykimg"><span><?php echo do_shortcode('[dot_recommends]'); ?></span></div>
				<?php if($to_cloaked!=""){ ?>
				<a href="<?php echo $LP_siteurl."/lp/".$to_cloaked; ?>/"><span class="readmre">Read More</span></a>
				<?php } ?>
			</div>
		</div>
	</div>
		<?php endwhile; 
        }
        ?>