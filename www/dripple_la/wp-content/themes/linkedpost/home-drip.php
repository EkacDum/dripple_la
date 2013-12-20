<script>
jQuery(".reverse_me").live("click",function(){
     jQuery(".post_holder").flippyReverse();
});
 
jQuery(".flip_me").live("click",function(){
    jQuery(this).parent().parent().parent().flippy({
        duration: "300",
        verso: jQuery("#back_flip > .post_holder").html()
    });
});

jQuery(".tile_feat_img_div").live("mouseenter",function(){
   jQuery(this).parent().flippy({
        duration: "300",
        verso: jQuery(this).parent().find(".back_flip").html()
    }); 
}); 

jQuery(".reverseflip_me").live("mouseout",function(){
     jQuery(this).parent().flippyReverse();
});
</script>
</div>
<div>
<div id="tile_view">
	<div class="topics_head">
		<h2>Hot Drippings</h2>
	</div>
    <div class="left_tiles">
        <div class="flipbox-container">
        <div class="post_holder">
			<div class="ddripdiv">
				<i class="redrip_blue_ico curson_pointer"></i>
			</div>
            <div class="flip_image_cont">
                <div class="flip_image_rot">
                    <div class="tile_feat_img_div">
                        <img src="<?php bloginfo('template_url'); ?>/images/post1.jpg"/>
                    </div>
                    <div class="back_flip" style="display:none">
                        <div class="reverseflip_me">
                            <img width="490px" src="http://static.giantbomb.com/uploads/original/12/128387/2435292-3160011176-iphone.jpeg"/>
                        </div>
                    </div>
                </div>
            </div>
			<div class="tile_low">
				<div class="tile_prof_img_div">
					<img src="<?php bloginfo('template_url'); ?>/images/author.png"/>
				</div>
				<div class="tile_prof_det_div">
					<span class="tile_pname"><a>Ryan Holmes</a></span>
					<span class="tile_chann GenericSlabBold">Marketing</span>
					<span class="tile_topic GenericSlabBold">The Secrets to Stelar Marketing</span>
				</div>
				<div class="tile_post_cont_div">
					<span class="source_site">monster.com / 2 days</span>
                    <span style="float:right;" class="flip_me"><i class="cycle-d-21"></i></span>
					<span class="tile_post_title GenericSlabLight">Everything That You Should Do Right In An Interview</span>
					<span class="tile_post_cont">
						You and any third party web font hosting service are responsible for ensuring that the font software in the self-hosting kit, in its original format, can only be used on the Web Sites for which the self-hosting kit was
						<div class="tile_op_div_inner">
							<div class="analysistab">
								<i class="analysis_ico"></i>
								<span class="analysistext GenericSlabBold">Analysis</span>
							</div>
							<span class="GenericSlabBold">Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet.Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet.Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet.Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet</span>
						</div>
						You and any third party web font hosting service are responsible for ensuring that the font software in the self-hosting kit, in its original format, can only be used on the Web Sites for which the self-hosting kit was downloaded and cannot be used or referenced by any other web site. This includes, but is not limited to installing adequate technical protection measures that restrict the use and/or access to the font software, for instance by utilizing JavaScript or access control mechanism for cross-origin resource sharing and protecting against use on web sites other than the Web Sites for which the self-hosting kit was downloaded by restricting domain access only to such Web Sites. You must also retain the pageview tracking code on any Web Site that you self-host. In the event this Agreement terminates for any reason, the font software included with the self-hosting kit must be deleted from the server and all copies must be destroyed or returned to Monotype Imaging.
						You and any third party web font hosting service are responsible for ensuring that the font software in the self-hosting kit, in its original format, can only be used on the Web Sites for which the self-hosting kit was downloaded and cannot be used or referenced by any other web site. This includes, but is not limited to installing adequate technical protection measures that restrict the use and/or access to the font software, for instance by utilizing JavaScript or access control mechanism for cross-origin resource sharing and protecting against use on web sites other than the Web Sites for which the self-hosting kit was downloaded by restricting domain access only to such Web Sites. You must also retain the pageview tracking code on any Web Site that you self-host. In the event this Agreement terminates for any reason, the font software included with the self-hosting kit must be deleted from the server and all copies must be destroyed or returned to Monotype Imaging.
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
        
        <div id="back_flip" style="display:none">
        <div class="post_holder">
                <div class="tile_low">
                    <div class="tile_prof_img_div">
                        <img src="<?php bloginfo('template_url'); ?>/images/author.png"/>
                    </div>
                    <div class="tile_prof_det_div">
                        <span class="tile_pname"><a>Ryan Holmes</a></span>
                        <span style="float:right;" class="reverse_me"><i class="cycle-d-21"></i></span>
                        <span class="tile_chann GenericSlabBold">Marketing</span>
                        <span class="tile_post_title GenericSlabLight">Everything That You Should Do Right In An Interview</span>
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
                            <img width="40px" src="http://m.c.lnkd.licdn.com/mpr/mprx/0_tm_nI-SVhd1O6UeF-uLWIlxc2Dc0X4eFrWBdIlao1aifRyxbOePs5AsXG5Bhb0WIPSTeX9r6VC6Y"/>
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
    </div>
	<div class="right_tiles">
    </div>
</div>