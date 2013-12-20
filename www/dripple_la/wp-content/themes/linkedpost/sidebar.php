<?php 
$LP_siteurl = trim(network_site_url(),"/");
$default_avatar = $LP_siteurl."/wp-content/themes/linkedpost/images/author.png"; ?>
<div id="sidebar">
	<div>
        
		<div class="sybarDIvyd" id="f_cont" style="opacity:.9!important;">
            <div id="floating_menu">
                <?php if(!is_user_logged_in()){?>
                <div id="ajoinfreeimg_frame">
                    <div id="ajoinfreeimg" class="creatacctheadlog">
                        <!-- <img/ src="<?php bloginfo('template_url');?>/images/free+_join.png" alt="Free to join"> -->
						<div class="joinfree">FREE TO JOIN</div>
                    </div>
                </div>
                <?php } ?>
				<ul>
                    <li class="imglogosubmit twttrov"><a href="http://twitter.com/home?status=Get+starting+posting+in+less+than+2+minutes+at+Drip+Post%20-%20http%3A%2F%2Fwww.drippost.com%2F%20" target="_blank"/></a></li>
                    <li class="imglogosubmit fbov"><a href="https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Fwww.drippost.com%2F&t=Drip+Post" target="_blank"/></a></li>
                    <li class="imglogosubmit gpov"><a href="https://plus.google.com/share?url=http%3A%2F%2Fwww.drippost.com%2F" target="_blank"/></a></li>
                    <li class="imglogosubmit diggov"><a href="http://digg.com/submit?phase=2&url=http%3A%2F%2Fwww.drippost.com%2F&title=Drip+Post&bodytext=Drip+Post" target="_blank"/></a></li>
                    <li class="imglogosubmit liov"><a href="http://www.linkedin.com/shareArticle?mini=true&url=http%3A%2F%2Fwww.drippost.com%2F&title=Drip+Post&source=Drip+Post+-+Drip+Post+News+Analyst&summary=Drip+Post" target="_blank"/></a></li>
                    <li class="imglogosubmit suov"><a href="http://www.stumbleupon.com/submit?url=http%3A%2F%2Fwww.drippost.com%2F&title=Drip+Post" target="_blank"/></a></li>
                </ul>
                <div class="socsiteborder"></div>
            </div>
        </div>
		<div class="sybarDIvyd">
			<div class="titlediv side_title">Top Influencers</div>
			<div class="inf_cont inf_contimg">
				<?php 
                global $current_site;
				$top_influecers = LP_get_top_influencers();
				foreach($top_influecers as $you){
					$this_blog = get_blog_option($you["blog_id"], "LP_linkedin_info");
					$user_info = $this_blog["linkedin"];
					$udata = get_userdata( $comment["user_id"] );
					$profile_pic = ($user_info->pictureUrls->values[0] ? $user_info->pictureUrls->values[0] : $user_info->pictureUrl);
							
					// $user_ = get_user_meta($you["post_author"]);
                    $udata = get_userdata( $you["post_author"] );
					$profile_pic = ($user_info->pictureUrl) ? $user_info->pictureUrl : $user_info->pictureUrls->values[0];
					if($profile_pic){
						$author_avatar = "<img src=\"".$profile_pic."\" width=\"72\" height=\"72\" alt=\"".$user_info->firstName." ".$user_info->lastName."\"/>";
					}else{
						$author_avatar = get_avatar($LP_linkedin_info["LP_email"], 72, $default_avatar);
					}
				?>
				<div class="inf_div">
				   <a href="<?php echo $LP_siteurl."/in/".$udata->user_nicename;?>/" title="<?php echo $you["blog_id"]." ".$user_info->firstName." ".$user_info->lastName; ?>"><?php echo $author_avatar;?></a>
					<div class="inf_num"><a href="<?php echo $LP_siteurl."/in/".$udata->user_nicename;?>/"><?php echo $you["totposts"]; ?></a></div>
				</div>
				<?php }?>
			</div>
		</div>
		<!-- <div class="sybarDIvyd">
			<div class="titlediv side_title">Ads by LinkedPOST Members</div>
			<div class="inf_cont">
				<div class="ad_div">
					<img src="<?php bloginfo('template_url');?>/images/ads.png" />
					<div class="ad_det">
						<span class="ad_title">Are you a Hotel Owner?</span>
						<span class="ad_desc">Swap your hotel room colleagues around the world. Enjoy</span>
					</div>
				</div>
				<div class="ad_div">
					<img src="<?php bloginfo('template_url');?>/images/ads.png" />
					<div class="ad_det">
						<span class="ad_title">Are you a Hotel Owner?</span>
						<span class="ad_desc">Swap your hotel room colleagues around the world. Enjoy</span>
					</div>
				</div>
				<div class="ad_div">
					<img src="<?php bloginfo('template_url');?>/images/ads.png" />
					<div class="ad_det">
						<span class="ad_title">Are you a Hotel Owner?</span>
						<span class="ad_desc">Swap your hotel room colleagues around the world. Enjoy</span>
					</div>
				</div>
			</div>
		</div> -->
		<!--div class="sybarDIvyd">
			<div class="titlediv side_title">Online Users</div>
			<div class="inf_cont">
				<div class="ad_div ol_div">
					<span class="curr_ol"><?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('UserOnline')): endif; ?></span>
				</div>
			</div>
		</div-->
		<div class="sybarDIvyd">
			<div class="titlediv side_title">Recent Comments</div>
			<div class="inf_cont">
				<div class="ad_div cmnt_div">
					<?php 
						// $args = array(
							// 'number' => '5'
						// );
						// $comments = get_comments($args);	
                        global $current_site;
                        $comments = LP_get_latest_comments();
						foreach($comments as $comment){ 
							$this_blog = get_blog_option($comment["blog_id"], "LP_linkedin_info");
							$user_info = $this_blog["linkedin"];
                            $udata = get_userdata( $comment["user_id"] );
							$profile_pic = ($user_info->pictureUrls->values[0] ? $user_info->pictureUrls->values[0] : $user_info->pictureUrl);
							if($profile_pic){
								$author_avatar = "<img src=\"".$profile_pic."\" width=\"42\" height=\"42\"/>";
							}else{
								$author_avatar = get_avatar($comment["comment_author_email"], 42, $default_avatar);
							}		
						?> 					
					<div class="cmnt_det">
						<div class="cmnt_content">
							<span class="cmnt_nme"><a href="<?php echo $LP_siteurl."/in/".$udata->user_nicename;?>/"><?php echo $comment["comment_author"]; ?></a></span>
							<span class="cmnt">&nbsp;- <?php echo $comment["comment_content"] ?> -&nbsp;</span>
						</div>
						<div class="comment_gravatar comment_gravatar_s"><a href="<?php echo $LP_siteurl."/in/".$udata->user_nicename;?>/"><?php echo $author_avatar; ?></a></div>
						<!--<span class="read_mre">Read >></span>-->
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="sybarDIvyd">
			<div class="titlediv side_title">1,239 People Likes LinkedPOST</div>
			<div class="inf_cont">
				<div class="inf_contimg like_div">
					<img width="71" height="72" src="<?php bloginfo('template_url');?>/images/author.png" alt=""/>
					<img width="71" height="72" src="<?php bloginfo('template_url');?>/images/author.png" alt="" />
					<img width="71" height="72" src="<?php bloginfo('template_url');?>/images/author.png" alt="" />
					<img width="71" height="72" src="<?php bloginfo('template_url');?>/images/author.png" alt="" />
					<img width="71" height="72" src="<?php bloginfo('template_url');?>/images/author.png" alt="" />
					<img width="71" height="72" src="<?php bloginfo('template_url');?>/images/author.png" alt="" />
					<img width="71" height="72" src="<?php bloginfo('template_url');?>/images/author.png" alt="" />
					<img width="71" height="72" src="<?php bloginfo('template_url');?>/images/author.png" alt="" />
					<img width="71" height="72" src="<?php bloginfo('template_url');?>/images/author.png" alt="" />
					<img width="71" height="72" src="<?php bloginfo('template_url');?>/images/author.png" alt="" />
				</div>
			</div>
		</div>
	</div>
</div>