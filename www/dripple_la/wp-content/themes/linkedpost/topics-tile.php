<?php 
    global  $user_id, $current_site, $post_channel, $author_avatar, $user_full_name; 	
	
	if(!$user_full_name) {
		$authorID = $post->post_author;
		$user = get_userdata($authorID);
		$user_full_name = $user->user_firstname." ".$user->user_lastname;
	}
    if(has_post_thumbnail()){
        $the_thumb = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), "lp-topic-medium");
		// print_r($the_thumb);
        $posts_thumb= $the_thumb[0];
    }else{
        $posts_thumb = "";
    }
?>
<div class="post_holder_topic">
	<div class="tile_xfeat_img_div topic_feat_img_div">
		<img width="320" height="160" src="<?php echo $posts_thumb ; ?>" alt="<?php the_title();?>" />
	</div>
	<div class="tile_low">
		<div class="tile_prof_img_div topic_prof_img_div">
			<a href="<?php the_permalink(); ?>"><?php echo $author_avatar; ?></a>
		</div>
		<div class="topic_prof_det_div">
			<span class="topic_pname"><a href="<?php the_permalink(); ?>"><?php echo $user_full_name; ?></a></span>
			<span class="topic_chann GenericSlabBold"><a href="<?php echo LP_channel_url($post_channel["name"]); ?>"><?php echo $post_channel["name"];?></a></span>
		</div>
		<div class="topic_post_cont_div" style="height:191px;">
            <span class="topic_drips_count">4 drips / last <?php echo LP_get_post_age(get_the_date( "U", get_the_ID()));?></span>
			<span class="topic_post_title GenericSlabLight"><a href="<?php the_permalink(); ?>"><?php the_title();?></a></span>
			<span class="topic_post_cont">
				<?php echo get_the_content();?>
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