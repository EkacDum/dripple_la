<?php
get_header();
global $post;
?>
</div>
<div>
<div id="tile_view">
	<div class="topics_head">
		<h2>Hot Drippings</h2>
	</div>
	<?php while ( have_posts() ) : the_post(); 
		$post_id = get_the_ID();
		$blog_id = get_current_blog_id();
		$topic   = LP_get_post_topic($post_id, $blog_id);
		$channel = LP_get_topic_channel($topic["ID"], $blog_id);
	?>
    <div class="left_tiles">
		<div class="post_holder">
			<div class="tile_low">
				<div class="tile_prof_img_div">
					<img src="<?php bloginfo('template_url'); ?>/images/author.png"/>
				</div>
				<div class="tile_prof_det_div">
					<span class="tile_pname"><a><?php the_author();?></a></span>
					<span class="tile_chann GenericSlabBold"><?php echo $channel["name"];?></span>
					<span class="tile_post_title GenericSlabLight"><?php the_title();?></span>
				</div>
				<div class="drip_stats_cont_div">
                    <div>
                        <i class="thumbsup-d-21"></i>
                        <span>12.1K</span>
						<label>ThumbsUp</label>
                    </div>
                    <div>
                        <i class="greydrip-d-21"></i>
                        <span>435</span>
						<label>Ripples</label>
                    </div>
                    <div>
                        <i class="greydrip-d-21"></i>
                        <span>4.5K</span>
						<label>Drips</label>
                    </div>
                    <div>
                        <i class="eye-d-21"></i>
                        <span>361</span>
						<label>Views</label>
                    </div>
				</div>
                <div class="ripples_cont_div">
					<div class="ripple_item alternate">
						<img width="40px" src="http://m.c.lnkd.licdn.com/mpr/mprx/0_tm_nI-SVhd1O6UeF-uLWIlxc2Dc0X4eFrWBdIlao1aifRyxbOePs5AsXG5Bhb0WIPSTeX9r6VC6Y""/>
						<div class="item_details">
							<textarea style="width: calc(100% - 8px); height: 33px;" placeholder="Comment"></textarea>
						</div>
					</div>
                    <div class="ripple_item">
						<img width="40px" src="http://m.c.lnkd.licdn.com/mpr/mprx/0_ukJ160ea4mnds8dzmL4u6gw34S-Q4ieza6Du6y4rFm6nFbxvhCd0epuO9LtMUXWJSbRDdRENiBED"/>
						<div class="item_details">
							<span class="type"><a href="">Mandy Deng</a>, July 15, 11:20AM</span>
							<span class="detail"><b>Dripped</b> this on <a href="">Hotels and Receptions</a></span>
						</div>
					</div>
					<div class="ripple_item alternate">
						<img width="40px" src="http://m.c.lnkd.licdn.com/mpr/mprx/0_cDHk-hYkFAo0oDlrcEmU-_a6FzxxHITrzWpR-_7s4Pw7c7nKUI4eyiRNd408w28p9aosjXAqOnZe"/>
						<div class="item_details">
							<span class="type"><a href="">Loui Byrdziak's</a>, July 15, 11:20AM</span>
							<span class="detail"><b>Shared</b> this on <a href="">LinkedIn</a></span>
						</div>
					</div>
					<div class="ripple_item">
						<img width="40px" src="http://m.c.lnkd.licdn.com/mpr/mprx/0_tm_nI-SVhd1O6UeF-uLWIlxc2Dc0X4eFrWBdIlao1aifRyxbOePs5AsXG5Bhb0WIPSTeX9r6VC6Y"/>
						<div class="item_details">
							<span class="type"><a href="">Ronnel Anasco's</a> analysis on, July 15, 11:20AM</span>
							<span class="detail"><b class="commysis">This is my analysis of this hotel. I love their foods and etc...</b></span>
						</div>
					</div>
					<div class="ripple_item alternate">
						<img width="40px" src="http://m.c.lnkd.licdn.com/mpr/mprx/0_9G8uJ8w-xGxDJ97GN6rPJiw1P6VjzvOGNLN1JiSauLaDoAdCsXK8B_JhgmsGnt0ac3Ct9kx-CSJx"/>
						<div class="item_details">
							<span class="type"><a href="">Ekack Dum's</a> comment, July 15, 11:20AM</span>
							<span class="detail"><b class="commysis">Amazing!, I couldn't believe my eyes when I see you there. I was startled like I couldn''t move and all. But everything else are erfect, except that I hate their music and the way their sound system is disturbing my sleep.</b></span>
						</div>
					</div>
					<div class="ripple_item">
						<img width="40px" src="http://m.c.lnkd.licdn.com/mpr/mprx/0_ukJ160ea4mnds8dzmL4u6gw34S-Q4ieza6Du6y4rFm6nFbxvhCd0epuO9LtMUXWJSbRDdRENiBED"/>
						<div class="item_details">
							<span class="type"><a href="">Mandy Deng</a>, July 15, 11:20AM</span>
							<span class="detail"><b>Redripped</b> this on <a href="">Hotels and Receptions</a></span>
						</div>
					</div>
					<div class="ripple_item alternate">
						<img width="40px" src="http://m.c.lnkd.licdn.com/mpr/mprx/0_cDHk-hYkFAo0oDlrcEmU-_a6FzxxHITrzWpR-_7s4Pw7c7nKUI4eyiRNd408w28p9aosjXAqOnZe"/>
						<div class="item_details">
							<span class="type"><a href="">Loui Byrdziak's</a>, July 15, 11:20AM</span>
							<span class="detail"><b>Shared</b> this on <a href="">LinkedIn</a></span>
						</div>
					</div>
					
					<div class="ripple_item">
						<img width="40px" src="http://m.c.lnkd.licdn.com/mpr/mprx/0_tm_nI-SVhd1O6UeF-uLWIlxc2Dc0X4eFrWBdIlao1aifRyxbOePs5AsXG5Bhb0WIPSTeX9r6VC6Y"/>
						<div class="item_details">
							<span class="type"><a href="">Ronnel Anasco's</a> analysis on, July 15, 11:20AM</span>
							<span class="detail"><b class="commysis">This is my analysis of this hotel. I love their foods and etc...</b></span>
						</div>
					</div>
					<div class="ripple_item alternate">
						<img width="40px" src="http://m.c.lnkd.licdn.com/mpr/mprx/0_9G8uJ8w-xGxDJ97GN6rPJiw1P6VjzvOGNLN1JiSauLaDoAdCsXK8B_JhgmsGnt0ac3Ct9kx-CSJx"/>
						<div class="item_details">
							<span class="type"><a href="">Ekack Dum's</a> comment, July 15, 11:20AM</span>
							<span class="detail"><b class="commysis">Amazing!, I couldn't believe my eyes when I see you there. I was startled like I couldn''t move and all. But everything else are erfect, except that I hate their music and the way their sound system is disturbing my sleep.</b></span>
						</div>
					</div>
					<div class="ripple_item">
						<img width="40px" src="http://m.c.lnkd.licdn.com/mpr/mprx/0_tm_nI-SVhd1O6UeF-uLWIlxc2Dc0X4eFrWBdIlao1aifRyxbOePs5AsXG5Bhb0WIPSTeX9r6VC6Y"/>
						<div class="item_details">
							<span class="type"><a href="">Ronnel Anasco's</a> analysis on, July 15, 11:20AM</span>
							<span class="detail"><b class="commysis">This is my analysis of this hotel. I love their foods and etc...</b></span>
						</div>
					</div>
					<div class="ripple_item alternate">
						<img width="40px" src="http://m.c.lnkd.licdn.com/mpr/mprx/0_9G8uJ8w-xGxDJ97GN6rPJiw1P6VjzvOGNLN1JiSauLaDoAdCsXK8B_JhgmsGnt0ac3Ct9kx-CSJx"/>
						<div class="item_details">
							<span class="type"><a href="">Ekack Dum's</a> comment, July 15, 11:20AM</span>
							<span class="detail"><b class="commysis">Amazing!, I couldn't believe my eyes when I see you there. I was startled like I couldn''t move and all. But everything else are erfect, except that I hate their music and the way their sound system is disturbing my sleep.</b></span>
						</div>
					</div>
					<div class="ripple_item">
						<img width="40px" src="http://m.c.lnkd.licdn.com/mpr/mprx/0_tm_nI-SVhd1O6UeF-uLWIlxc2Dc0X4eFrWBdIlao1aifRyxbOePs5AsXG5Bhb0WIPSTeX9r6VC6Y"/>
						<div class="item_details">
							<span class="type"><a href="">Ronnel Anasco's</a> analysis on, July 15, 11:20AM</span>
							<span class="detail"><b class="commysis">This is my analysis of this hotel. I love their foods and etc...</b></span>
						</div>
					</div>
					<div class="ripple_item alternate">
						<img width="40px" src="http://m.c.lnkd.licdn.com/mpr/mprx/0_9G8uJ8w-xGxDJ97GN6rPJiw1P6VjzvOGNLN1JiSauLaDoAdCsXK8B_JhgmsGnt0ac3Ct9kx-CSJx"/>
						<div class="item_details">
							<span class="type"><a href="">Ekack Dum's</a> comment, July 15, 11:20AM</span>
							<span class="detail"><b class="commysis">Amazing!, I couldn't believe my eyes when I see you there. I was startled like I couldn''t move and all. But everything else are erfect, except that I hate their music and the way their sound system is disturbing my sleep.</b></span>
						</div>
					</div>
                </div>
			</div>
		</div>
	</div>
	<div class="right_tiles">
		<div class="post_holder">
			<div class="ddripdiv">
				<i class="redrip_blue_ico curson_pointer"></i>
			</div>
			<div class="tile_feat_img_div">
				<img src="<?php bloginfo('template_url'); ?>/images/post1.jpg"/>
			</div>
			<div class="tile_low">
				<div class="tile_prof_img_div">
					<img src="<?php bloginfo('template_url'); ?>/images/author.png"/>
				</div>
				<div class="tile_prof_det_div">
					<span class="tile_pname"><a><?php the_author();?></a></span>
					<span class="tile_chann GenericSlabBold"><?php echo $channel["name"];?></span>
					<span class="tile_topic GenericSlabBold"><?php echo $topic["post_title"];?></span>
				</div>
				<div class="tile_post_cont_div">
					<span class="source_site">monster.com / 2 days</span>
					<span class="tile_post_title GenericSlabLight"><?php the_title();?></span>
					<span class="tile_post_cont">
						<?php echo substr($post->post_content,0,190);?>xxx
						<div class="tile_op_div_inner">
							<div class="analysistab">
								<i class="analysis_ico"></i>
								<span class="analysistext GenericSlabBold">Analysis</span>
							</div>
							<span class="GenericSlabBold"><?php the_excerpt();?></span>
						</div>
						<?php echo substr($post->post_content,190);?>
					</span>
				</div>
				<div class="tagpstcommnt">
					<?php echo get_the_term_list( get_the_ID(), 'post_tag', '<ul class="li_tagpost"><li>','</li><li>','</li></ul>'); ?>
				</div>
				<div class="tile_div_ext">
					<ul>
						<li>
							<i class="tile_view_count curson_pointer"></i>
							<span class="stats_text">1</span>
						</li>
						<li>
							<i class="tile_redrips curson_pointer"></i>
							<span class="stats_text">1</span>
						</li>
						<li>
							<i class="tile_comments curson_pointer"></i>
							<span class="stats_text">1</span>
						</li>
						<li>
							<i class="tile_iframe curson_pointer"></i>
							<i class="tile_bookm curson_pointer"></i>
							<i class="tile_mail curson_pointer"></i>
						</li>
					</ul>
				</div>
			</div>
		</div>
    </div>
	<?php endwhile; ?>
</div>
<?php
get_footer();
?>