<?php global $blog_posts, $switched;
// print_r($blog_posts);
$which_view = $_SESSION["home_view"];
$default = "display:none;";
$list = "display:none;";
$tile = "display:none;";
if($which_view == "default" || $which_view == "") $default = "display:block;";
elseif($which_view == "list") $list = "display:block;";
elseif($which_view == "tile") $tile = "display:block;";
?>
</div>
<div id="default_view_cont" style="<?php echo $default;?>">
	<div id="home">
	<?php 

		$i=0;
		if (count($blog_posts)>0 ) foreach ( $blog_posts as $the_post ) : 
		// $the_post = $the_p;
		$post_meta = array("cloaked_URL","the_excerpt");
		$user_meta = array("user_email","display_name");
		$blog_post_info = get_blog_post_info($the_post["blog_id"], $the_post["ID"], $the_post["post_author"], $user_meta, $post_meta );
		// print_r($blog_post_info);
		$post_ID = $the_post["ID"];
		$postid = $post_ID;
		$blog_id = $the_post["blog_id"];
		$user_email = $blog_post_info["user_meta"]['user_email'];
		$author_post_count = $blog_post_info["count_user_posts"];
        
        switch_to_blog($the_post["blog_id"]);
            $the_topic_link = get_permalink($the_post["topic_id"]);
        restore_current_blog();
		
		$drip_ripples = LP_get_drip_ripples($post_ID, $blog_id);
		        
        $comment_ripples = LP_get_comment_ripples($post_ID, $blog_id);
		
		$all_ripples = array_merge($drip_ripples,$comment_ripples);
		usort($all_ripples,"sortRipples");
		$display_domain = str_replace("https://","",$the_post["story_URL"]);
		$display_domain = str_replace("http://","",$display_domain);
		$display_domain = str_replace("www.","",$display_domain);
		$display_domain = explode("/",$display_domain);
		?>
		<div class="flipbox-container">
			<div class="indgropostcat the_flipping">
				<div class="mash_prof_det_div">
					<span class="mash_hot_chann mash_trend_chann mash_def_chann"><a href="<?php echo LP_channel_url($the_post["channel_name"]); ?>"><?php echo $the_post["channel_name"]; ?></a></span>
					<div class="placewtym placewtymext"><span><?php echo $display_domain[0]; ?></span> / <?php echo LP_get_post_age(strtotime($the_post["post_date"]));?></div>
						<i class="turn-l-18 flip_me extra"></i>
					<?php if(is_user_logged_in()){ ?>
						 <?php //if($the_post["post_author"] != get_current_user_id() && !LP_is_has_redripped($post_ID, $blog_id)){?>
						<i class="blueredrip-dg-18 redrip_this" id="<?php echo $the_post["ID"]; ?>" param="<?php echo $the_post["blog_id"]; ?>"></i>
						<?php  //}?>
						<i class="close-l-18"></i>
					<?php  }?>
				</div>
				<div class="authordivindiimg bluename tile_prof_img_divext">
				<?php 
					$user_info = get_blog_option($the_post["blog_id"],"LP_linkedin_info");
					$profile_pic = ($user_info["linkedin_profile_pic"] ? $user_info["linkedin_profile_pic"] : $user_info["linkedin_profile_thumb"]);
					if($profile_pic){
						$author_avatar = "<img src=\"".$profile_pic."\" width=\"80\" height=\"80\" alt=\"".$blog_post_info["user_meta"]["display_name"]."\"/>";
					}else{
						$author_avatar = get_avatar($user_email, 72, $default_avatar);
					}
				?>
				
					<a href="<?php echo $the_topic_link;?>"><?php echo $author_avatar; ?></a>
					<span class="cntartno"><?php echo $author_post_count; ?></span>
					<p class="artclpdiv">articles</p>
				</div>
				
				<?php 
					$the_excerpt = $the_post["post_excerpt"];
					$get_the_content = $the_post["post_content"];
						
					//$to_cloaked = $blog_post_info["meta"]["cloaked_URL"];
					$to_cloaked = "";
					$story_URL = LP_get_post_story_URL($post_ID, $the_post["blog_id"]);
					// echo "## : ".$post_id." -- ".$the_post["blog_id"];
					// print_r($story_URL);
					if(count($story_URL) && $story_URL["story_URL"]!=""){
						$to_cloaked = "0000".$story_URL["id"];
					}
					?>
				<div class="inblogpostbody inblogpostbodyext">
					<div class="det_cont">
						<div class="authordivindi"><a href="<?php echo LP_get_user_url($the_post["post_author"])?>"><?php echo $blog_post_info["user_meta"]["display_name"]; ?></a></div>
						<span class="inblogpostbodyspan"><a href="<?php echo $the_topic_link;?>"><?php echo $the_post["topic_name"];?></a></span>
						<?php if($to_cloaked!=""){ ?>
							<h2 class="posttitle_ext GenericSlabLight"><a href="<?php echo $LP_siteurl."/lp/".$to_cloaked; ?>/" target="_blank"><?php echo substr(strip_tags($the_post["post_title"]), 0,120); ?></a></h2>
						<?php }else{ ?>
							<h2 class="posttitle_ext GenericSlabLight"><?php echo substr(strip_tags($the_post["post_title"]), 0,120); ?></h2>
						<?php } ?>
					</div>
					<div class="sydcontenpost">
						<!--?php the_excerpt();?> -->
						<div>
					<?php if ($blog_post_info["has_post_thumbnail"]){
						$image = $blog_post_info["post_thumbnail"];
						// print_r($image);
                        $no_flip = "";
                        if($blog_post_info["back_flip_image"]==""){
                            $no_flip = "no-flip";
                        }else{
							$imgflip = json_decode($blog_post_info["back_flip_image"],true);
							if($imgflip === NULL){
								$no_flip = "no-flip";
							}
						}
						
						?>
						<div class="flip_def_image_cont">
							<div class="flip_image_rot">
								<div class="imgblogpost <?php echo $no_flip;?>">
									<a href="<?php echo get_option('siteurl')."/lp/".$to_cloaked; ?>/"><img width="<?php echo $image[1];?>" height="<?php echo $image[2];?>" src="<?php echo $image[0];?>" alt="<?php echo $blog_post_info["the_tags"]; ?>" /></a>
								</div>
                                <?php 
								if($no_flip != "no-flip"){
									$flip_img = $imgflip["img"];
									$nh = floor($imgflip["info"][1] - ($imgflip["info"][1] *(($imgflip["info"][0] - 302)/$imgflip["info"][0])));
									$flip_dimensions = "width=\"302\" height=\"".$nh."\" ";
								?>
							   <div class="backx_flip" style="display:none">
									<div class="reverseflip_me">
										<img <?php echo $flip_dimensions;?> src="<?php echo lp_flip_dir($the_post["blog_id"])."/".$flip_img;?>" alt="<?php echo $blog_post_info["the_tags"]; ?>"/>
									</div>
								</div>
                                <?php }?>
							 </div>
						</div>
					<?php } ?>
					<?php if($the_excerpt){ ?>
					<div class="tile_op_div_inner tile_op_div_innerext drip_analysis">
						<div class="analysistab">
							<i class="analysis_ico"></i>
							<span class="analysistext GenericSlabBold">Analysis</span>
						</div>
						<span class="GenericSlabBold">
							<?php echo substr(strip_tags($the_excerpt), 0,90); ?>
						</span>
					</div>
					<?php } ?>
						<p><?php echo substr(strip_tags($get_the_content), 0,400); ?></p>
						</div>
					</div>
					<div class="tagpstcommnt">
					<?php if($blog_post_info["the_tags"]!=""){?>
						<ul class="li_tagpost"><li>
						<?php 
							$tags = explode(",",$blog_post_info["the_tags"]); 
							echo implode("</li><li>",$tags);
						?>
						</li></ul>
					<?php } ?>
					</div>
				</div>
				<div class="comment_read">
					<div class="divcomllykimg"><i class="eye-l-18"></i><span>1.1K</span></div>
					<div class="divcomllykimg"><i class="greytotldrips-l-18"></i><span>.5K</span></div>
					<div class="divcomllykimg"><i class="thumbsup-l-18"></i><span>15</span></div>
					<div class="divcomllykimg"><i class="bubble-l-18"></i><span>4</span></div>
					<?php if($to_cloaked!=""){ ?>
					<div class="divcomllykimgright"><a href="<?php echo $story_URL["story_URL"]; ?>" target="_blank"><i class="greyanalysis-l-18"></i></a></div>
					<?php } ?>
					<div class="divcomllykimgright"><i class="ribbon-l-18"></i></div>
					<div class="divcomllykimgright"><i class="envelop-l-18"></i></div>
				</div>
			</div>
			<div class="back_flip" style="display:none">
			<br />
				<div class="postx_holder">
					<div class="tile_low">
						<div class="tile_prof_img_div">
							<a href="<?php echo $the_topic_link;?>"><?php echo $author_avatar; ?></a>
						</div>
						<span style="position: absolute;z-index: 10000;top: 15px;right: 10px;" class="reverse_me"><i class="turn-l-18 reverse_me extra"></i></span>
						<div class="tilex_prof_det_div">
							<span class="tile_pname"><a href="<?php echo $the_topic_link;?>"><?php echo $blog_post_info["user_meta"]["display_name"]; ?></a></span>
							<span class="tile_chann GenericSlabBold"><a href="<?php echo LP_channel_url($the_post["channel_name"]); ?>"><?php echo $the_post["channel_name"]; ?></a></span>

							<?php if($to_cloaked!=""){ ?>
								<h2 class="tile_post_title GenericSlabLight"><a href="<?php echo $LP_siteurl."/lp/".$to_cloaked; ?>/"><?php echo substr(strip_tags($the_post["post_title"]), 0,120); ?></a></h2>
							<?php }else{ ?>
								<h2 class="tile_post_title GenericSlabLight"><?php echo substr(strip_tags($the_post["post_title"]), 0,120); ?></h2>
							<?php } ?>

						</div>					
						<div class="drip_stats_cont_div">
							<div>
								<i class="thumbsup-l-21"></i>
								<span>12.1K</span>
								<label>ThumbsUp</label>
							</div>
							<div>
								<i class="ripples-l-21"></i>
								<span>435</span>
								<label>Ripples</label>
							</div>
							<div>
								<i class="greydrip-l-21"></i>
								<span><?php echo count($drip_ripples);?></span>
								<label>Drips</label>
							</div>
							<div>
								<i class="eye-l-21"></i>
								<span>361</span>
								<label>Views</label>
							</div>
						</div>
						<div class="ripples_cont_div">
							<?php if(is_user_logged_in()){ ?>
							<div class="ripple_item alternate cb_<?php echo $the_post["ID"]."-".$the_post["blog_id"];?>">
								<?php echo LP_get_user_avatar(get_current_user_id(), 40); ?>
								<div class="item_details">
									<textarea param="<?php echo $the_post["ID"]."-".$the_post["blog_id"];?>" class="comment_box" style="width: calc(100% - 8px); height: 33px;" placeholder="Comment"></textarea>
								</div>
							</div>
							<?php }?>
							<?php 
                            $re = "D";
							$alternate = "alternate";
                            foreach($all_ripples as $ripple){
								if(is_array($ripple) && isset($ripple["post_type"])){
									switch_to_blog($ripple["blog_id"]);
										$new_topic_link = get_permalink($ripple["topic_id"]);
									restore_current_blog();
                                    $user = get_userdata($ripple["post_author"]);
									$user_url = LP_get_user_url($ripple["post_author"]);
							?>
                                    <div class="ripple_item  <?php echo $alternate;?>">
										<a class="ripple_avatar_a" href="<?php echo $user_url; ?>"><?php echo LP_get_user_avatar($ripple["post_author"], 40); ?></a>
                                        <div class="item_details">
                                            <span class="type"><a href="<?php echo $user_url; ?>"><?php echo $user->display_name;?></a>, <?php echo date("F d, H:mA",strtotime($ripple["post_modified_gmt"])); ?></span>
                                            <span class="detail"><b><?php echo $re;?>ripped</b> this on <a href="<?php echo $new_topic_link; ?>"><?php echo $ripple["topic_name"]; ?></a></span>
                                        </div>
                                    </div>
							<?php 
									$re = "Red";
								}elseif(is_object($ripple) && isset($ripple->comment_ID)){
									$user = get_userdata($ripple->user_id);
									$user_url = LP_get_user_url($ripple->user_id);
							?>
									<div class="ripple_item  <?php echo $alternate;?>">
										<a class="ripple_avatar_a" href="<?php echo $user_url; ?>"><?php echo LP_get_user_avatar($ripple->user_id, 40); ?></a>
										<div class="item_details">
											<span class="type"><a href="<?php echo $user_url; ?>"><?php echo $user->display_name;?>'s</a> comment, <?php echo date("F d, H:mA",strtotime($ripple->comment_date)); ?></span>
											<span class="detail"><b class="commysis"><?php echo $ripple->comment_content; ?></b></span>
										</div>
									</div>
								<?php }
								if($alternate == "alternate"){
									$alternate = "";
								}else{
									$alternate = "alternate";
								}
							}?>
						</div>
					</div>
				</div>
			</div>
		</div>
			<?php endforeach; ?>
	</div>
	<?php
	get_sidebar();
	?>
</div>
<!-- list view -->
<div id="list_view_cont" style="<?php echo $list;?>">
    <div class="topics_head">
        <h2>Drip List</h2>
    </div>
    <ul id="unsorted_list"></ul>
</div>
<!-- end list view -->

<!-- Tile View -->
<div id="tile_view_cont" style="<?php echo $tile;?>">
	<div class="topics_head">
		<h2>Hot Drippings</h2>
	</div>
    <div class="left_tiles">
    </div>
    <div class="right_tiles">
    </div>
</div>
<!-- End Tile View -->