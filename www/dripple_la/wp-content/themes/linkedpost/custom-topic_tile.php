<?php 
    global $current_site, $post_channel, $the_topic; 	
	
	$authorID = $the_topic["post_author"];
	$user = get_userdata($authorID);
	$user_full_name = $user->user_firstname." ".$user->user_lastname;
	
	$LP_siteurl = "http://".$current_site->domain;
	$default_avatar = $LP_siteurl . "/wp-content/themes/linkedpost/images/author.png";
	
	$user_info = get_blog_option($the_topic["blog_id"],"LP_linkedin_info");
	$profile_pic = ($user_info["linkedin_profile_pic"] ? $user_info["linkedin_profile_pic"] : $user_info["linkedin_profile_thumb"]);
	if($profile_pic){
		$author_avatar = "<img src=\"".$profile_pic."\" width=\"80\" heigjt=\"80\"/>";
	}else{
		$author_avatar = get_avatar($user_email, 72, $default_avatar);
	}
	// print_r($the_topic);
	switch_to_blog($the_topic["blog_id"]);
	    if(has_post_thumbnail($the_topic["ID"])){
			$the_thumb = wp_get_attachment_image_src( get_post_thumbnail_id($the_topic["ID"]), "topic-thumb-medium");
			$posts_thumb = $the_thumb[0];
			
			$the_thumb = wp_get_attachment_image_src( get_post_thumbnail_id($the_topic["ID"]), "large");
			$posts_fthumb = $the_thumb[0];
		}else{
			$posts_thumb = "";
			$posts_fthumb = "";
		}
		$the_topic_link = get_permalink($the_topic["ID"]);
	restore_current_blog();
?>

<div class="post_holder_topic">
	<div class="tilex_feat_img_div topic_feat_img_div">
		<img width="320" src="<?php echo $posts_thumb ; ?>" />
	</div>
	
	<!----div class="flip_image_cont">
		<div class="flip_image_rot">
			<div class="tile_feat_img_div">
			   <img width="320" src="<?php echo $posts_thumb ; ?>" />
			</div>
			<div class="backx_flip" style="display:none">
				<div class="reverseflip_me">
					<img width="320" src="<?php echo $posts_fthumb ; ?>" />
				</div>
			</div>
		</div>
	</div--->


	<div class="tile_low">
		<div class="tile_prof_img_div topic_prof_img_div">
			<a href="<?php echo $the_topic_link; ?>"><?php echo $author_avatar; ?></a>
		</div>
		<div class="topic_prof_det_div">
			<span class="topic_pname"><a href="<?php echo $the_topic_link; ?>"><?php echo $user_full_name; ?></a></span>
			<span class="topic_chann GenericSlabBold"><a href="<?php echo LP_channel_url($post_channel["name"]); ?>"><?php echo $post_channel["name"];?></a></span>
		</div>
		<div class="topic_post_cont_div" style="height:191px;">
            <span class="topic_drips_count">4 drips / last <?php echo LP_get_post_age(get_the_date( "U", $the_topic["ID"]));?></span>
			<span class="topic_post_title GenericSlabLight"><a href="<?php echo $the_topic_link; ?>"><?php echo $the_topic["post_title"];?></a></span>
			<span class="topic_post_cont">
				<?php echo $the_topic["post_content"];?>
			</span>
		</div>
		<div class="comment_read">
			<div class="divcomllykimg"><i class="eye-l-18"></i><span>1.1K</span></div>
			<div class="divcomllykimg"><i class="greytotldrips-l-18"></i><span>.5K</span></div>
			<div class="divcomllykimg"><i class="thumbsup-l-18"></i><span>15</span></div>
			<div class="divcomllykimg"></div>
			<div class="divcomllykimgright"><i class="ribbon-l-18"></i></div>
			<div class="divcomllykimgright"><i class="envelop-l-18"></i></div>
		</div>	
	</div>
</div>