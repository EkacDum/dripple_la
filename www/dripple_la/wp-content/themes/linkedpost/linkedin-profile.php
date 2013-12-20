<?php
$LP_siteurl = trim(network_site_url(),"/");
$default_avatar = $LP_siteurl."/wp-content/themes/linkedpost/images/author.png"; 

$get_current_blog_id = get_current_blog_id();

// Lest get the user linkedin information saved in our database for this blog...
$LP_linkedin_info = get_blog_option($get_current_blog_id, "LP_linkedin_info");
$lin_profile = $LP_linkedin_info["linkedin"];
?>
<div class="indgropostcat">
    <div class="profile_div">
    <?php 
    $profile_pic = ($lin_profile->pictureUrls->values[0] ? $lin_profile->pictureUrls->values[0] : $lin_profile->pictureUrl);
               
    if($profile_pic){
        $author_avatar = "<img src=\"".$profile_pic."\" width=\"199\" height=\"199\" alt=\"".$lin_profile->firstName." ".$lin_profile->lastName."\"/>";
    }else{
        $author_avatar = get_avatar($lin_profile->emailAddress, 199, $default_avatar); 
    }
    echo $author_avatar;
    ?>
        <div class="prof_det">
            <span id="prof_name"><?php echo $lin_profile->firstName; ?> <?php echo $lin_profile->lastName; ?></span>
            <span id="prof_pos"><?php if($lin_profile->positions->values[0]->title){echo $lin_profile->positions->values[0]->title; ?> at <?php echo $lin_profile->positions->values[0]->company->name; }?></span>
            <span id="prof_loc"><?php if($lin_profile->company_ext->locations->values[0]->address->city){ ?><?php echo $lin_profile->company_ext->locations->values[0]->address->city; ?>,  <?php echo ucwords($lin_profile->company_ext->locations->values[0]->address->countryCode); ?>  | <?php echo $lin_profile->positions->values[0]->company->industry; ?><?php }?></span>
            <div class="prof_wrkeduc">
                <?php if($lin_profile->positions->values[0]->company->name){ ?><span class="prof_wrkedul">Current:</span><span class="prof_wrkedui"><?php echo $lin_profile->positions->values[0]->company->name; ?></span>
                <?php }?>
            </div>
            <div class="prof_wrkeduc">
            <?php if($lin_profile->positions->values){ $prev = maybe_unserialize($lin_profile->positions->values); ?>
                <span class="prof_wrkedul">Previous:</span><span class="prof_wrkedui">
                <?php
                    // print_r($prev);
                    $sep = "";
                    foreach($prev as $company){
                        if($company->isCurrent ==""){
                            echo $sep.$company->company->name;
                            $sep = ", ";
                        }
                    }
                ?>
                </span>
            <?php }?>
            </div>
            <div class="prof_wrkeduc">
                <?php if($lin_profile->educations->values[0]->schoolName){ ?>
                <span class="prof_wrkedul">Education:</span><span class="prof_wrkedui"><?php echo $lin_profile->educations->values[0]->schoolName; ?></span>
                <?php }?>
            </div>
			<div class="in_proffo">
				<a href="https://www.linkedin.com/profile/view?id=<?php echo $LP_linkedin_info["linkedin_uid"]; ?>" target="_blank"><div class="prof_linkedin">LinkedIn</div></a>
				<div class="follow_in">+ Follow</div>
				<!-- <div class="followed_in">Following</div> -->
			</div>
        </div>
    </div>

    <div id="rankingdiv" style="height:175px;">
        <div id="rankdiv">
            <span id="rank_fo"><?php $count_posts = wp_count_posts(); echo $count_posts->publish;?></span>
        </div>
        <span id="rank_l">articles</span>
    </div>
    <div class="proflinkli">
        <?php $string = str_replace('http://www.', '', $lin_profile->publicProfileUrl);?>
        <a href="<?php echo $lin_profile->publicProfileUrl; ?>"><?php echo $string;?></a>
    </div>
    <?php if($lin_profile!="false"){?>
    <?php if($lin_profile->summary){ ?>
     <div class="prof_summary">
            <h2><?php echo $lin_profile->firstName; ?> <?php echo $lin_profile->lastName; ?>'s Summary</h2><br />
            <p>Summary<br /><?php echo $lin_profile->summary; ?></p>
    </div>
   <?php }?>
    <div class="see_more">
        <div class="see_font"><?php if($lin_profile->summary){ ?><img id="left_arr_c_more" src="<?php bloginfo('template_url');?>/images/moredown.png" alt=""/><img id="left_arr_c_more2" src="<?php bloginfo('template_url');?>/images/moreup.png" alt=""/> <span id="c_more" style="margin-top: 2px;float: left;">See More</span> <img id="right_arr_c_more" src="<?php bloginfo('template_url');?>/images/moredown.png" alt=""/><img id="right_arr_c_more2" src="<?php bloginfo('template_url');?>/images/moreup.png" alt=""/><?php }?></div>
    </div>
   <?php } ?>
</div>