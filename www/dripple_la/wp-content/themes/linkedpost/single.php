<?php
global  $user_id, $current_site, $post_channel, $author_avatar, $user_full_name;
get_header();
$LP_siteurl = "http://".$current_site->domain;
$default_avatar = $LP_siteurl."/wp-content/themes/LinkedPOST/images/author.png";
?>
</div>
<div id="default_view_cont">
<div id="home">
	<?php get_template_part( "linkedin", "profile" );?>
    <div class="view_mode_container">
		<li class="iconheadli"><i id="topicview" class="topicview ico_class_sm_edit shown active"></i></li>
		<li class="iconheadli"><i id="dripview" class="dripview ico_class_sm_edit shown"></i></li>
	</div>
    <?php if ( have_posts() ) while ( have_posts() ) : the_post();
            $post_id = get_the_ID();
            $blog_id = get_current_blog_id();
            $post_channel = get_the_channel($post_id, $blog_id);
            $post_thumb = LP_get_post_thumb_url($post_id, $blog_id, $size = "lp-thumb-xlarge");
    ?>
    <div class="single_topic_holder">			
        <div class="ddripdiv">				
            <i class="redrip_blue_ico curson_pointer"></i>			
        </div>			
        <div class="tile_feat_img_div">                
            <img width="662px" src="<?php echo $post_thumb;?>" />
        </div>			
        <div class="tile_low">											
            <div class="tile_post_cont_div">					
            <span class="source_site"><?php echo $post_channel["name"];?></span>					
            <span class="tile_post_title GenericSlabBold"><?php the_title();?></span>	
            <span class="tile_post_cont"><?php the_content();?></span>			
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
    <?php endwhile; ?>
    <?php 
    $args = array(
        "post_type"     => "post",
        "post_parent"   => $post_id,
    );
    query_posts($args);
    if ( have_posts() ) while ( have_posts() ) : the_post();
        get_template_part( "single", "loop" );
    endwhile;
    ?>
</div>
<?php
get_sidebar();
get_footer();
?>