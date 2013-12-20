<?php
/*
	Template Name: Home Members
*/
get_header();
global $wpdb;
global $current_site;
global $LP_channel_settings;

$LP_siteurl = "http://".$current_site->domain;

$member_chans = array();
foreach($LP_channel_settings as $chan){
	if($chan["active"]== 1)
		$member_chans[]= $chan["id"];
}

$blog_posts = LP_latest_channel_posts($member_chans);    
?>
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
		<div class="divblogpost">
			<div class="titlediv"><?php 
            $sep = "";
            foreach($blog_post_info["the_channels"] as $chan){echo $sep.$chan["name"];}?></div>
			<div class="placewtym">New York Times / 3 mins</div>
			<div class="lsymodeimg">
				<img src="<?php bloginfo('template_url');?>/images/lstmode_boxlft.png" alt="">
				<img src="<?php bloginfo('template_url');?>/images/lstmode_boxryt.png" alt="">
			</div>
		</div>
		<div class="authordivindiimg">
        <?php 
            // $user_info = get_user_meta($the_post["post_author"]);
			$user_info = get_blog_option($the_post["blog_id"], "LP_linkedin_info");
            $profile_pic = ($user_info["linkedin_profile_pic"] ? $user_info["linkedin_profile_pic"] : $user_info["linkedin_profile_thumb"]);
            if($profile_pic){
                $author_avatar = "<img src=\"".$profile_pic."\" width=\"72\" heigjt=\"72\"/>";
            }else{
                $author_avatar = get_avatar($user_email, 72, $default_avatar);
            }
        ?>
        
			<a href="<?php echo $blog_post_info["the_permalink"]; ?>"><?php echo $author_avatar; ?></a>
			<span class="cntartno"><?php echo $author_post_count; ?></span>
			<p class="artclpdiv">articles</p>
		</div>
		<div class="inblogpostbody">
			<div class="authordivindi"><a href="<?php echo $blog_post_info["the_permalink"]; ?>"><?php echo $blog_post_info["user_meta"]["display_name"]; ?></a></div>
            <?php 
                if($the_post["post_excerpt"]!=""){
                    $the_excerpt = $the_post["post_excerpt"];
                }elseif($blog_post_info["meta"]["the_excerpt"]!=""){
                    $the_excerpt = $blog_post_info["meta"]["the_excerpt"];
                }else{
                    $the_excerpt = strip_tags($the_post["post_content"]);
                }
            $to_cloaked = $blog_post_info["meta"]["cloaked_URL"]; 
            if($to_cloaked!=""){
            ?>
			<p style="width: 563px !important;"><p><a href="<?php echo $LP_siteurl."/lp/".$to_cloaked; ?>/"><?php echo substr($the_excerpt, 0,90); ?></a></p></p>
            <?php }else{?>
            <p style="width: 563px !important;"><p><a href="<?php echo get_option('siteurl')."/lp/".$to_cloaked; ?>/"><?php echo substr($the_excerpt, 0,90); ?></a></p></p>
            <?php } ?>
            
			<?php if ($blog_post_info["has_post_thumbnail"]) {
                $image = $blog_post_info["post_thumbnail"]; 
			?>
                <div class="imgblogpost">
                    <a href="<?php echo get_option('siteurl')."/lp/".$to_cloaked; ?>/"><img style="width:180px;" src="<?php echo $image[0];?>" /></a>
                </div>
			<?php } ?>
			
			<div class="sydcontenpost">
			<?php if($to_cloaked!=""){ ?>
			<h2><a href="<?php echo $LP_siteurl."/lp/".$to_cloaked; ?>/"><?php echo substr($the_post["post_title"], 0,120); ?></a></h2>
			<?php }else{ ?>
			<h2><?php echo substr($the_post["post_title"], 0,120); ?></h2>
			<?php } ?>
				<!--?php the_excerpt();?> -->
				<p><a href="<?php echo get_option('siteurl')."/lp/".$to_cloaked; ?>/"><?php echo substr($the_excerpt, 0,400); ?></a></p>
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
<?php
get_sidebar();
get_footer();
?>