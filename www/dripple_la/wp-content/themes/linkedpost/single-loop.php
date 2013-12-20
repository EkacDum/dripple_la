<?php 
global  $user_id, $current_site, $post_channel, $user_full_name, $author_avatar; 
$LP_siteurl = "http://".$current_site->domain;

if(has_post_thumbnail()){
    $the_thumb = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), "topic-thumb-medium");
    $posts_thumb= $the_thumb[0];
}else{
    $posts_thumb = "";
}

$post_id = get_the_ID();
$blog_id = get_current_blog_id();
$topic = LP_get_post_topic($post_id, $blog_id);
$post_thumb = LP_get_post_thumb_url($post_id, $blog_id, $size = "topic-thumb-medium");


if(!$user_full_name) {
	$authorID = $post->post_author;
	$user = get_userdata($authorID);
	$user_full_name = $user->user_firstname ." ". $user->user_lastname;
}

if(!$author_avatar){
	$user_info = get_blog_option(get_current_blog_id(),"LP_linkedin_info");
	$profile_pic = ($user_info["linkedin_profile_pic"] ? $user_info["linkedin_profile_pic"] : $user_info["linkedin_profile_thumb"]);
	if($profile_pic){
		$author_avatar = "<img src=\"".$profile_pic."\" width=\"80\" height=\"80\" alt=\"$user_full_name\"/>";
	}else{
		$author_avatar = get_avatar($user_email, 72, $default_avatar);
	}
}

$drip_ripples = LP_get_drip_ripples($post_id, $blog_id);
		        
$comment_ripples = LP_get_comment_ripples($post_id, $blog_id);

$the_tags = get_the_term_list( $post_id, 'post_tag',"" ,',','');

$all_ripples = array_merge($drip_ripples,$comment_ripples);
usort($all_ripples,"sortRipples");
?>
<div class="flipbox-container">
	<div class="indgropostcat the_flipping">
		<div class="mash_prof_det_div">
			<span class="mash_hot_chann mash_trend_chann"><a href="<?php echo LP_channel_url($post_channel["name"]); ?>"><?php echo $post_channel["name"];?></a></span>
			<div class="placewtym placewtymext">New York Times / 3 mins</div>
				<i class="turn-l-18 flip_me extra"></i>
			<?php if(is_user_logged_in()){ ?>
				<?php if(get_the_author_ID() != get_current_user_id() && !LP_is_has_redripped($post_id, $blog_id)){?>
				<i class="blueredrip-dg-18 redrip_this" id="<?php the_ID();?>" param="<?php echo get_current_blog_id(); ?>"></i>
				<?php  }?>
				<i class="close-l-18"></i>
			<?php  }?>
		</div>
		<div class="authordivindiimg bluename tile_prof_img_divext">   
			<a href="<?php echo $topic_URL;?>"><?php echo $author_avatar; ?></a>
			<span class="cntartno">10</span>
			<p class="artclpdiv">articles</p>
		</div>
				
		<div class="inblogpostbody inblogpostbodyext">
		<?php $topic_URL = get_permalink($topic["ID"]);?>
			<div class="authordivindi"><a href="<?php echo $topic_URL;?>"><?php echo $user_full_name; ?></a></div>
			<span class="inblogpostbodyspan"><a href="<?php echo $topic_URL;?>"><?php echo $topic["post_title"];?></a></span>
			<?php
				$story_URL = LP_get_post_story_URL($post_id, $blog_id);
				$the_URL = false;
				if(count($story_URL) && $story_URL["story_URL"]!=""){
					$the_URL = $LP_siteurl."/lp/0000".$story_URL["id"]."/";
				?>
				<h2 class="posttitle_ext GenericSlabLight"><a href="<?php echo $the_URL;?>"><?php the_title();?></a></h2>
				<?php
				}else{?>
				<h2 class="posttitle_ext GenericSlabLight"><?php the_title();?></h2>
				<?php }?>
			<div class="sydcontenpost">
				<!--?php the_excerpt();?> -->
				<div>
				<?php 
				if($post_thumb){
					if($the_URL){
						$p_thumb = "<a href=\"".$the_URL."\"><img width=\"302\" height=\"168\" src=\"".$post_thumb."\" alt=\"$the_tags\"/></a>";
					}else{
						$p_thumb = "<img width=\"302\" height=\"168\" src=\"".$post_thumb."\" alt=\"$the_tags\" />";
					}?>
				
				<div class="flip_def_image_cont">
					<div class="flip_image_rot">
                        <?php 
						$_LP_flip_img = get_post_meta($post_id, "_LP_flip_img", true);

                        $no_flip = "";
                        if($_LP_flip_img == ""){
                            $no_flip = "no-flip";
                        }
						
						
						$no_flip = "";
                        if($_LP_flip_img == ""){
                            $no_flip = "no-flip";
                        }else{
							$imgflip = json_decode($_LP_flip_img,true);
							if($imgflip === NULL){
								$no_flip = "no-flip";
							}
						}
						
                        ?>
						<div class="imgblogpost <?php echo $no_flip;?>">
							<?php echo $p_thumb;?>
						</div>
                        <?php 
							if($no_flip != "no-flip"){
								$flip_img = $imgflip["img"];
								$nh = floor($imgflip["info"][1] - ($imgflip["info"][1] *(($imgflip["info"][0] - 302)/$imgflip["info"][0])));
								$flip_dimensions = "width=\"302\" height=\"".$nh."\" ";
						?>
						<div class="backx_flip" style="display:none">
							<div class="reverseflip_me">
								<img <?php echo $flip_dimensions; ?> src="<?php echo lp_flip_dir($blog_id)."/".$flip_img;?>" alt="<?php echo $the_tag;?>"/>
							</div>
						</div>
                        <?php }?>
					 </div>
				</div>
				<?php }?>                    
						
				<div class="tile_op_div_inner tile_op_div_innerext drip_analysis">
					<div class="analysistab">
						<i class="analysis_ico"></i>
						<span class="analysistext GenericSlabBold">Analysis</span>
					</div>
					<span class="GenericSlabBold">
						<?php if($the_URL){?>
							<a href="<?php echo $the_URL;?>"><?php echo substr(get_the_excerpt(), 0,90);?></a>
						<?php }else{?>
							<?php echo substr(get_the_excerpt(), 0,90);?>
						<?php }?>
					</span>
				</div>
				<p><?php echo get_the_content();?></p>
				</div>
			</div>
			<div class="tagpstcommnt">
			<?php 
				if($the_tags!=""){?>
				<ul class="li_tagpost"><li>
				<?php 
					$tags = explode(",",$the_tags); 
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
			<?php if($the_URL){?>
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
				<span class="tile_pname"><a href="<?php echo $topic_URL;?>"><?php echo $user_full_name; ?></a></span>
				<span class="tile_chann GenericSlabBold"><a href="<?php echo LP_channel_url($post_channel["name"]); ?>"><?php echo $post_channel["name"];?></a></span>	
				<?php
				if(count($story_URL) && $story_URL["story_URL"]!=""){
					$the_URL = $LP_siteurl."/lp/0000".$story_URL["id"]."/";
				?>
					<h2 class="tile_post_title GenericSlabLight"><a href="<?php echo $the_URL;?>"><?php the_title();?></a></h2>
				<?php
				}else{?>
					<h2 class="tile_post_title GenericSlabLight"><?php the_title();?></h2>
				<?php }?>

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
				<div class="ripple_item alternate cb_<?php echo $post_id."-".$blog_id;?>">
					<?php echo LP_get_user_avatar(get_current_user_id(), 40); ?>
					<div class="item_details">
						<textarea param="<?php echo $post_id."-".$blog_id;?>" class="comment_box" style="width: calc(100% - 8px); height: 33px;" placeholder="Comment"></textarea>
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
						<div class="ripple_item <?php echo $alternate;?>">
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
						<div class="ripple_item <?php echo $alternate;?>">
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
