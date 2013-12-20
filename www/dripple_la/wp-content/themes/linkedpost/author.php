<?php
get_header();
if(!$post->post_author){
    $uri = explode("/",trim($_SERVER["REQUEST_URI"],"/"));
    global $wpdb;
    $res = $wpdb->get_results("(SELECT `path` FROM `wp_blogs` WHERE `path` = '/".$_POST['domain']."/') 
                UNION ALL 
                (SELECT `path` FROM `wp_signups` WHERE `path` = '/".$_POST['domain']."/')",ARRAY_A);
    $user = $wpdb->get_results("SELECT * FROM `wp_users` WHERE `user_nicename`='".trim(end($uri))."'",ARRAY_A);
    // print_r($user);
    $user_id = $user[0]["ID"];
}else{
$user_id = $post->post_author;
}

$linkedIn_profile = do_shortcode('[LP_set_user user_id="'.$user_id.'"]');
if($linkedIn_profile != "false"){
    $linkedIn_profile = json_decode($linkedIn_profile);
    // print_r($linkedIn_profile);
}
$user_info = get_user_meta($user_id);
?>
<div id="home">
	<div class="indgropostcat">

			<div class="profile_div">
            <?php 
            $profile_pic = ($user_info["linkedin_profile_pic"][0] ? $user_info["linkedin_profile_pic"][0] : $user_info["linkedin_profile_thumb"][0]);
            if($profile_pic){?>
                <img src="<?php echo $profile_pic; ?>" />
            <?php }else{?>
				<img src="http://sanyahaitun.com/wp-content/themes/LinkedPOST/images/def_ac.png" />
            <?php }?>
				<div class="prof_det">
					<span id="prof_name"><?php echo $user_info["first_name"][0]; ?> <?php echo $user_info["last_name"][0]; ?></span>
					<span id="prof_pos"><?php if($user_info["LP_prof_title"][0]){echo $user_info["LP_prof_title"][0]; ?> at <?php echo $user_info["LP_current_company"][0]; }?></span>
					<span id="prof_loc"><?php if($user_info["LP_company_city"][0]){ ?><?php echo $user_info["LP_company_city"][0]; ?>,  <?php echo ucwords($user_info["LP_company_country"][0]); ?>  | <?php echo $user_info["LP_company_industry"][0]; ?><?php }?></span>
					<div class="prof_wrkeduc">
						<?php if($user_info["LP_current_company"][0]){ ?><span class="prof_wrkedul">Current:</span><span class="prof_wrkedui"><?php echo $user_info["LP_current_company"][0]; ?></span>
                        <?php }?>
					</div>
					<div class="prof_wrkeduc">
                    <?php if($user_info["LP_previous_companies"][0]){ $prev = maybe_unserialize($user_info["LP_previous_companies"][0]); ?>
						<span class="prof_wrkedul">Previous:</span><span class="prof_wrkedui">
                        <?php
                            // print_r($prev);
                            foreach($prev as $company){
                                if($company->isCurrent ==""){
                                    echo $company->company->name;
                                    break;
                                }
                            }
                        ?>
                        </span>
                    <?php }?>
					</div>
                    <?php if($linkedIn_profile!="false"){?>
					<div class="prof_wrkeduc">
						<?php if($linkedIn_profile->educations->values[0]->schoolName){ ?>
                        <span class="prof_wrkedul">Education:</span><span class="prof_wrkedui"><?php echo $linkedIn_profile->educations->values[0]->schoolName; ?></span>
                        <?php }?>
					</div>
					<a href="<?php echo $linkedIn_profile->publicProfileUrl; ?>" target="_blank"><div class="prof_linkedin">LinkedIn Profile</div></a>
                    <?php } ?>
				</div>
			</div>

			<div id="rankingdiv" style="height:175px;">
				<div id="rankdiv">
					<span id="rank_fo">1245</span>
				</div>
				<span id="rank_l">ranking</span>
			</div>
            <?php if($linkedIn_profile!="false"){?>
            <?php if($linkedIn_profile->summary || $linkedIn_profile->interests || $linkedIn_profile->specialties){ ?>
             <div class="prof_summary">
                    <h2><?php echo $linkedIn_profile->firstName; ?> <?php echo $linkedIn_profile->lastName; ?>'s Summary</h2><br />
                <?php if($linkedIn_profile->summary){?>
                    <p>Summary<br /><?php echo $linkedIn_profile->summary; ?></p>
                <?php }?>
                <?php if($linkedIn_profile->interests){?>
                    <p>Interests<br /><?php echo $linkedIn_profile->interests; ?></p>
                 <?php }?>
                <?php if($linkedIn_profile->specialties){?>
                    <p>Specialties<br /><?php echo $linkedIn_profile->specialties; ?></p>
                 <?php }?>
            </div>
           <?php }?>
			<div class="see_more">
				<div class="see_font"><?php if($linkedIn_profile->summary){ ?><img id="left_arr_c_more" src="<?php bloginfo('template_url');?>/images/moredown.png"/><img id="left_arr_c_more2" src="<?php bloginfo('template_url');?>/images/moreup.png"/> <span id="c_more" style="margin-top: 2px;float: left;">See More</span> <img id="right_arr_c_more" src="<?php bloginfo('template_url');?>/images/moredown.png"/><img id="right_arr_c_more2" src="<?php bloginfo('template_url');?>/images/moreup.png"/><?php }?></div>
			</div>
           <?php } ?>
		</div>
	<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>	
	<?php $postid = get_the_ID(); 
	$comments_count = wp_count_comments();
	?>	
	<?php $to_cloaked = get_post_meta(get_the_ID(),"cloaked_URL",true); ?>
	
	<div class="indgropostcat">
		<div class="divblogpost">
			<div class="titlediv"><?php $cat = get_the_category(); echo $cat[0]->name;?></div>
			<div class="placewtym">New York Times / 3 mins</div>
			<div class="lsymodeimg">
				<img src="<?php bloginfo('template_url');?>/images/lstmode_boxlft.png" alt="">
				<img src="<?php bloginfo('template_url');?>/images/lstmode_boxryt.png" alt="">
			</div>
		</div>
		<div class="authordivindiimg">
			<a href="<?php the_permalink() ?>"><img src="<?php bloginfo('template_url');?>/images/author.png"></a>
			<span class="cntartno">2,671</span>
			<p class="artclpdiv">articles</p>
		</div>
		<div class="inblogpostbody">
			<div class="authordivindi"><a href="<?php the_permalink(); ?>"><?php the_author(); ?></a></div>
			<p style="width: 563px !important;"><p><a href="<?php echo get_option('siteurl')."/".$to_cloaked; ?>/"><?php echo substr(get_the_excerpt(), 0,90); ?></a></p></p>
			
			<?php if (has_post_thumbnail()) {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID )); 
			
			?>
			<div class="imgblogpost">
				<img style="width:180px;" src="<?php echo $image[0];?>" />
			</div>
			<?php } ?>
			
			<div class="sydcontenpost">
			<?php if($to_cloaked!=""){ ?>
			<h2><a href="<?php echo get_option('siteurl')."/".$to_cloaked; ?>/"><?php echo substr(the_title(), 0,120); ?></a></h2>
			<?php }else{ ?>
			<h2><?php echo substr(the_title(), 0,120); ?></h2>
			<?php } ?>
				<!--?php the_excerpt();?> -->
				<p><?php echo substr(get_the_excerpt(), 0,250); ?></p>
			</div>
			<div class="tagpstcommnt">
                <?php the_tags('<ul class="li_tagpost"><li>','</li><li>','</li></ul>'); ?>
            </div>
		</div>
		<div class="comment_read">
			<div class="see_font">
			<!--<span class="commentmore">Comments</span>-->
				<div class="divcomllykimg"><img src="<?php bloginfo('template_url');?>/images/artciletot_logo.png"><span><?php echo do_shortcode('[post_view]'); ?></span></div>
				<div class="divcomllykimg"><img src="<?php bloginfo('template_url');?>/images/viewslogo.png"><span><?php 
						$comments_count = wp_count_comments($postid);
						echo $comments_count->total_comments; ?></span>
				</div>
				<div class="divcomllykimg"><span><?php echo do_shortcode('[dot_recommends]'); ?></span></div>
				<a href="<?php echo get_option('siteurl')."/".$to_cloaked; ?>/"><span class="readmre">Read More</span></a>
			</div>
		</div>
	</div>
		<?php endwhile; ?>
</div>
<?php
get_sidebar();
get_footer();
?>