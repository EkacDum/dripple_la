<?php global $blog_posts;?>
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
    $user_email = $blog_post_info["user_meta"]['user_email'];
    $author_post_count = $blog_post_info["count_user_posts"];
	?>
	<div class="indgropostcat">
		<div class="mash_prof_det_div">
			<span class="mash_hot_chann mash_trend_chann">Marketing<?php 
				$sep = "";
				foreach($blog_post_info["the_channels"] as $chan){echo $sep.$chan["name"];}?>
			</span>
			<div class="placewtym placewtymext">New York Times / 3 mins</div>
			<i class="mash_redrip_ico"></i>
			<i class="markread_ico"></i>
		</div>
		<div class="authordivindiimg bluename tile_prof_img_divext">
        <?php 
			$user_info = get_site_option("LP_linkedin_info");
            $profile_pic = ($user_info["linkedin_profile_pic"] ? $user_info["linkedin_profile_pic"] : $user_info["linkedin_profile_thumb"]);
            if($profile_pic){
                $author_avatar = "<img src=\"".$profile_pic."\" width=\"80\" heigjt=\"80\"/>";
            }else{
                $author_avatar = get_avatar($user_email, 72, $default_avatar);
            }
        ?>
        
			<a href="<?php echo $blog_post_info["the_permalink"]; ?>"><?php echo $author_avatar; ?></a>
			<span class="cntartno"><?php echo $author_post_count; ?></span>
			<p class="artclpdiv">articles</p>
		</div>
		
		<?php 
                if($the_post["post_excerpt"]!=""){
                    $the_excerpt = $the_post["post_excerpt"];
                }elseif($blog_post_info["meta"]["the_excerpt"]!=""){
                    $the_excerpt = $blog_post_info["meta"]["the_excerpt"];
                }else{
                    $the_excerpt = strip_tags($the_post["post_content"]);
                }
				
				$get_the_excerpt = $the_post["post_excerpt"];
                $get_the_content = $the_post["post_content"];
                if($get_the_excerpt!=""){
                    $the_excerpt = $get_the_excerpt;
                }else{
                    $the_excerpt = strip_tags($get_the_content);
                }
				
            $to_cloaked = $blog_post_info["meta"]["cloaked_URL"]; 
            ?>
				
		<div class="inblogpostbody inblogpostbodyext">
			<div class="authordivindi"><a href="<?php echo $blog_post_info["the_permalink"]; ?>"><?php echo $blog_post_info["user_meta"]["display_name"]; ?></a></div>
            <span class="inblogpostbodyspan">Topic Here</span>
            
			<?php if($to_cloaked!=""){ ?>
			<h2 class="posttitle_ext GenericSlabLight"><a href="<?php echo $LP_siteurl."/lp/".$to_cloaked; ?>/"><?php echo substr($the_post["post_title"], 0,120); ?></a></h2>
			<?php }else{ ?>
			<h2 class="posttitle_ext GenericSlabLight"><?php echo substr($the_post["post_title"], 0,120); ?></h2>
			<?php } ?>
			<div class="sydcontenpost">
				<!--?php the_excerpt();?> -->
				<span>
                <?php if ($blog_post_info["has_post_thumbnail"]) {
                $image = $blog_post_info["post_thumbnail"]; 
			?>
                <div class="imgblogpost">
                    <a href="<?php echo get_option('siteurl')."/lp/".$to_cloaked; ?>/"><img style="width:220px;" src="<?php echo $image[0];?>" /></a>
                </div>
			<?php } ?>
			
			<div class="tile_op_div_inner tile_op_div_innerext">
				<div class="analysistab">
					<i class="analysis_ico"></i>
					<span class="analysistext GenericSlabBold">Analysis</span>
				</div>
				<span class="GenericSlabBold">
				<?php if($to_cloaked!=""){?>
					<a href="<?php echo get_option('siteurl')."/lp/".$to_cloaked; ?>/"><?php echo substr($the_excerpt, 0,90); ?></a>
				<?php }else{?>
					<?php echo substr($the_excerpt, 0,90); ?>
				<?php } ?>
				</span>
			</div>
                <p><?php echo substr($get_the_content, 0,400); ?></p>
                </span>
			</div>
			<div class="tagpstcommnt">
                <?php echo $blog_post_info["the_tags"]; ?>
            </div>
		</div>
		
		
		
		
		<div class="comment_read">
			<div class="see_font">
			<!--<span class="commentmore">Comments</span>-->
				<div class="divcomllykimg"><img src="<?php bloginfo('template_url');?>/images/artciletot_logo.png"><span><?php echo $blog_post_info["post_view"]; ?></span></div>
				<div class="divcomllykimg"><img src="<?php bloginfo('template_url');?>/images/viewslogo.png"><span><?php echo $the_post["comment_count"]; ?></span>
				</div>
				<div class="divcomllykimg"><span><?php echo $blog_post_info["dot_recommends"]; ?></span></div>
				<?php if($to_cloaked!=""){ ?>
				<a href="<?php echo $LP_siteurl."/lp/".$to_cloaked; ?>/"><span class="readmre">Read More</span></a>
				<?php } ?>
			</div>
		</div>
	</div>
		<?php endforeach; ?>
</div>