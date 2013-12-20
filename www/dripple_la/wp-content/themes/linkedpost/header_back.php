<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
    <?php wp_head(); ?>
		<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/css/linkedpost.css"/>
		<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/css/content.css"/>
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=sanchezsemibold"/>
		<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/css/reset.css"/>
			<script src="<?php bloginfo( 'template_url' ); ?>/js/jQuery1.7.js" type="text/javascript"></script>
	<script src="<?php bloginfo( 'template_url' ); ?>/js/lnkedpst.js" type="text/javascript"></script>
    <!----->
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/jquery-ui-1.10.3/themes/base/jquery.ui.all.css">
	<script src="<?php bloginfo('template_url'); ?>/jquery-ui-1.10.3/ui/jquery.ui.core.js"></script>
	<script src="<?php bloginfo('template_url'); ?>/jquery-ui-1.10.3/ui/jquery.ui.widget.js"></script>
	<script src="<?php bloginfo('template_url'); ?>/jquery-ui-1.10.3/ui/jquery.ui.mouse.js"></script>
	<script src="<?php bloginfo('template_url'); ?>/jquery-ui-1.10.3/ui/jquery.ui.sortable.js"></script>
	</head>	
<body>
<div id="mainDiv">
	<div id="headDIV">
		<div class="DIVcontainer">
			<a href="<?php echo get_option('siteurl'); ?>"><img src="<?php bloginfo('template_url');?>/images/head_logo.png" alt=""></a>
			<div class="logindivusersindi">
				<ul>
                <?php if(!is_user_logged_in()){?>
					<li class="signinhed">Sign In</li>
					<li class="creatacctheadlog">Create Account</li>
                <?php }else{
                global $user_identity;
                get_currentuserinfo();
                ?>
                <li class="LP_logout">Welcome, <?php echo $user_identity;?>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="/llogin/?rtype=logout">Logout</a></li>
                <?php } ?>
				</ul>
			</div>
		</div>
	</div>
	<div id="underheadDIV">
		<div class="DIVcontainer">
			<div id="ullitrunav">
				<ul>
                <?php global $current_site; ?>
					<li class="linavtru"><a href="#">Home</a>
						<div class="submensydDIV">
							<ul class="ulsubmentrulnkd">
								<li class="submenlinkindnav"><a href="<?php echo "http://".$current_site->domain; ?>/">LinkedPost Home</a></li>
								<li class="submenlinkindnav"><a href="#">Advertise On LP</a></li>
							</ul>
						</div>
					</li>
                    <?php if(is_user_logged_in()){?>
					<li class="linavtru"><a href="#">Profile</a>
						<div class="submensydDIV">
							<ul class="ulsubmentrulnkd">
                            <?php
                            $user_info = get_userdata(get_current_user_id());
                            $profile_url =  "http://".$current_site->domain."/in/".$user_info->user_nicename."/";
                            
                            $user_blogs = get_blogs_of_user( get_current_user_id() );
                            $blog_id = 0;
                            foreach($user_blogs as $blog){
                               if($blog->userblog_id != 1){
                                    $blog_id = $blog->userblog_id;
                                    break;
                               }
                            }
                            
                            switch_to_blog($blog_id);
                            $LP_siteurl = get_option('siteurl');
                            restore_current_blog();
        
                            ?>
								<li class="submenlinkindnav"><a href="<?php echo $profile_url; ?>">View Profile</a></li>
								<li class="submenlinkindnav"><a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php">Edit Profile</a></li>
							</ul>
						</div>
					</li>
					<li class="linavtru"><a href="#">Submit</a>
						<div class="submensydDIV">
							<ul class="ulsubmentrulnkd">
								<li class="submenlinkindnav"><a href="<?php echo $LP_siteurl; ?>/wp-admin/post-new.php">Submit Post</a></li>
								<li class="submenlinkindnav"><a class="meter_drips">Meter Drips</a></li>
								<li class="submenlinkindnav"><a class="adjust_drips">Adjust Drips</a></li>
								<li class="submenlinkindnav"><a href="#">Analyze Drips</a></li>
							</ul>
						</div>
					</li>
                    <?php } ?>
					<li class="linavtru"><a href="#">Trending</a>
						<div class="submensydDIV">
							<ul class="ulsubmentrulnkd">
								<li class="submenlinkindnav"><a href="http://sanyahaitun.com/trending-top-ten/">Top Ten</a></li>
								<li class="submenlinkindnav"><a href="http://sanyahaitun.com/trending-this-week/">This Week</a></li>
								<li class="submenlinkindnav"><a href="http://sanyahaitun.com/trending-this-month/">This Month</a></li>
							</ul>
						</div>
					</li>
					<li class="linavtru2"><a id="cat_acsshov" href="#">Channels</a>
						<div id="submendivoutsyd">
							<div id="submendiv">
								<ul class="ulsubmentru">
									<?php wp_list_categories(array("hide_empty"=>0,"title_li"=>'')); ?>
								</ul>
							</div>
						</div>
					</li>
				</ul>
			</div>
			<div id="srchnavhead">
				<input class="serchclass" id="searchbtn" type="search" value="">
				<span>search</span>
			</div>
		</div>
		
	</div>

	<div id="grpinddivlog">
		<div class="DRIPbtn_head">
				<div class="DRIPbtn_head_div">
					<ul>
						<li class="liDRIPbtn">
							<div class="contntwrkiconhead">
								<p class="contbtndrip active">in</p>
								<img class="imgDRIPicon"src="<?php bloginfo('template_url');?>/images/obs_icon.png">
							</div>
							<div class="btnntwrkicon">
								<div class="txtwrkicon"><p>Click to activate In</p></div>
								<div class="imgwrkicon"><img src="<?php bloginfo('template_url');?>/images/hoverimg_down.png"></div>
							</div>
						</li>
						<li class="liDRIPbtn">
							<div class="contntwrkiconhead">
								<p class="contbtndrip">f</p>
								<img class="imgDRIPicon"src="<?php bloginfo('template_url');?>/images/obs_icon.png">
							</div>
							<div class="btnntwrkicon">
								<div class="txtwrkicon"><p>Click to activate Facebook</p></div>
								<div class="imgwrkicon"><img src="<?php bloginfo('template_url');?>/images/hoverimg_down.png"></div>
							</div>
						</li>
						<li class="liDRIPbtn">
							<div class="contntwrkiconhead">
								<p class="contbtndrip">tw</p>
								<img class="imgDRIPicon"src="<?php bloginfo('template_url');?>/images/obs_icon.png">
							</div>
							<div class="btnntwrkicon">
								<div class="txtwrkicon"><p>Click to activate twitter</p></div>
								<div class="imgwrkicon"><img src="<?php bloginfo('template_url');?>/images/hoverimg_down.png"></div>
							</div>
						</li>
					</ul>
					<ul id="drip_nav">
						<li class="liDRIPbtn lidripnewbtn">
							<p class="contbtndrip" id="drip_collect">collect</p>
							<img class="imgDRIPicon"src="<?php bloginfo('template_url');?>/images/obs_icon.png">
						</li>
						<li class="liDRIPbtn">
								<p class="contbtndrip" id="drip_meter">meter</p>
								<img class="imgDRIPicon"src="<?php bloginfo('template_url');?>/images/obs_icon.png">
						</li>
						<li class="liDRIPbtn">
							<p class="contbtndrip" id="drip_adjust">adjust</p>
							<img class="imgDRIPicon"src="<?php bloginfo('template_url');?>/images/obs_icon.png">
						</li>
						<li class="liDRIPbtn">
							<p class="contbtndrip" id="drip_analyze">analyze</p>
							<img class="imgDRIPicon"src="<?php bloginfo('template_url');?>/images/obs_icon.png">
						</li>
						<li class="liDRIPbtn">
							<p class="contbtndrip lidripnewright" id="chanclick">channel</p>
							<img class="imgDRIPicon"src="<?php bloginfo('template_url');?>/images/obs_icon.png">
						</li>
					</ul>
				</div>
				<div class="btniconDRIPhed">
					<ul>
						<!-- <li class="iconheadli"><div id="chckdwn"></div></li> -->
						<li class="iconheadli"><div id="qmark"></div></li> 
						<li class="iconheadli"><div id="ribdwn"></div></li>
						<li class="iconheadli"><div id="gearbtrn"></div></li>
						<li class="iconheadli"><div id="srchicon"></div></li> 
						<!--<li class="iconheadli"><div id="arrowryt"></div></li>-->
						<li class="iconheadli"><div id="arrowdown"></div></li>
					</ul>
				</div>
				<div class="type_city">
					<input type="text" placeholder="type a city"/>
					<span>local time is currently xxxx</span>
				</div>
			</div>
		<div class="signlogindivhead">
			<h2>Get starting posting in less than 2 minutes</h2>
			<span class="colspandiv span01headfree">it's for free</span>
			<div>
                <div class="lndinimg linkedIn_register_button" href="#"><img src="<?php bloginfo('template_url');?>/images/signlinkedin.png"></div>
                <!--<span class="signupspan">Signup with LinkedIn button</span>-->
            </div>

			<img id="imgordivbrdr" src="<?php bloginfo('template_url');?>/images/orimg.png">
			<h3>Create your news blog here</h3>
            <form id="wp_reg_form" method="post" action="llogin/?rtype=register">
			<div class="grploginnput">
				<div class="frmdicnput">
					<ul>
						<li><input class="inputbodyclass" name="email" id="uemail" type="text" value="" placeholder="Email"></li>
						<li><input class="inputbodyclass" name="fname" id="fname" type="text" value="" placeholder="First Name"></li>
						<li><input class="inputbodyclass" name="lname" id="lname" type="text" value="" placeholder="Last Name"></li>
						<li><input class="inputbodyclass" name="upass" id="upass" type="password" value="" placeholder="Password"></li>
						<li><input class="inputbodyclass" name="ucpass" id="ucpass" type="password" value="" placeholder="Password Again"></li>
					</ul>
				</div>
				<div class="radiobtnsub">
					<span class="spantermsservices">This account is for:</span>
					<ul>	
						<li>
							<input type="radio" id="ind" value="individual" name="acctype" checked />
							<label for="ind" class="spantermsservices">an individual(me)</label>
						</li>
						<li>
							<input type="radio" id="org" value="organizatioin" name="acctype" />
							<label for="org" class="spantermsservices">an Organization</label>
						</li>
						<li id="chckbxli">
							<input type="checkbox" value="Yes" id="termsagree" name="terms_agree">
							<span class="spantermsservices"><label for="termsagree">I agree to LinkedPost</label> <a class="afnt" href="<?php bloginfo('template_url');?>/terms-of-service/">Terms of Service</a> and <a class="afnt" href="<?php bloginfo('template_url');?>/privacy-policy/">Privacy Policy</a></span>
						</li>
					</ul>
					<div id="btnsubmituser">Sign Up Now</div>
				</div>
			</div>
            </form>
			<span class="spanmebrlog">Already a member? <div id="termadiv" class="signinhed">Login</div></span>
		</div>
		<div class="registerhead">
			<h2>Please complete your profile information</h2>
			<p class="connected">
				Signing up as
				<strong id="mnamesigner"></strong>
			</p>
            
			<div class="regformdiv">
				<div class="left_sgnacc">
					<img id="li_avatar" src="<?php bloginfo('template_url');?>/images/def_ac.png" alt="Profile avatar">
				</div>
				<div class="right_sgnacc">
					<input type="text" name="lblognme" class="lblognme" placeholder="Blog Name">
					<input type="file" name="lpic" id="lupic" class="lupic" multiple="true">
					<input type="text" name="lttl" class="lttl" placeholder="Title">
					<input type="text" name="lucty" class="lucty" placeholder="City">
					<input type="text" name="lcurpos" class="lcurpos" placeholder="Current Position">
					<input type="text" name="lcurcom" class="lcurcom" placeholder="Current Company">
					<input type="text" name="lcomcty" class="lcomcty" placeholder="City">
					<input type="text" name="lcntry" class="lcntry" placeholder="Country">
					<p style="display:none;">
						By creating an account, I agree to LinkedPOST's
						<a class="afnt" href="<?php bloginfo('template_url');?>/terms-of-service/">
						<strong>Terms of Service</strong>
						</a>
						and
						<a class="afnt" href="<?php bloginfo('template_url');?>/privacy-policy/">
						<strong>Privacy Policy</strong>
						</a>
					</p>
					<div class="submitcont">
						<input class="buttonblue linkedin_submit_reg" id="btnsubmituser2" type="button" value="Create Account">
					</div>
				</div>
			</div>
		</div>
		<div class="createaccdiv">
			<h2>Create your account</h2>
			<p class="connected">
				Connected to
				<span id="netwrksign">LinkedIn</span>
				as
				<strong id="namesigner"></strong>
			</p>
			<form id="linkedin_reg_form" class="crtacc_inf" method="post" action="linkedInP/?rtype=register">
				<ul class="error_msg">
				</ul>
				<div class="left_crtacc">
					<img id="li_avatar" src="<?php bloginfo('template_url');?>/images/def_ac.png" alt="Profile avatar">
				</div>
				<div class="right_crtacc">
					<input type="text" name="lblogname" class="lblogname" placeholder="Blog Name">
					<input class="lemail" name="lemail" type="email" placeholder="Email">
					<input type="password" name="lpass" class="lpass" placeholder="Password">
					<input class="cpass" name="cpass" type="password" placeholder="Password Again">
					<p>
						By creating an account, I agree to LinkedPOST's
						<a class="afnt" href="<?php bloginfo('template_url');?>/terms-of-service/">
						<strong>Terms of Service</strong>
						</a>
						and
						<a class="afnt" href="<?php bloginfo('template_url');?>/privacy-policy/">
						<strong>Privacy Policy</strong>
						</a>
					</p>
					<div class="submitcont">
						<input class="buttonblue linkedin_submit_reg" type="button" value="Create Account">
						<span class="spin" style="display: none;"><img src="<?php bloginfo('template_url');?>/images/drip_c.gif" /></span>
					</div>
				</div>
			</form>
		</div>
		<div class="signdiv">
			<div class="signlog">
				<div class="lndinimg linkedIn_login_button" href="#">
					<img src="<?php bloginfo('template_url');?>/images/loglinkedin.png">
				</div>
			</div>
			<div class="form_conta">
				<form id="wp_log_form" class="formlogin" method="post" action="llogin/?rtype=login">
					<ul class="error_msg_login">
					</ul>
					<input class="nputsygnfld" type="email" value="" id="wp_ulogin" name="wp_ulogin" placeholder="Email">
					<input class="nputsygnfld" type="password" value="" id="wp_upass" name="wp_upass" placeholder="Password">
					<div class="signcont">
						<input class="buttonbluer" id="loginfldhead" type="button" value="Login">
						<span class="spin" style="display: none;"></span>
					</div>
				<a href="<?php bloginfo('template_url');?>/wp-login.php?action=lostpassword" class="colspandiv span01headfree forg">Forgot your password?</a>
				</form>
			</div>
			<span class="spanmebrsign">No account? <a class="termadiv">Signup</a></span>
		</div>
		<div class="dripgrpDIV" style="display: none;">
			<div class="arrowsdiv">
				<div class="arrowprev"></div>
				<div class="arrownext"></div>
			</div>
			<div class="dripheadbtn">
				<div class="drips_con">
					<div class="collect_dripdiv">
						Collect Tab
					</div>
				</div>
				<div class="drips_con">
					<div class="DRIPdivcrtacc_inf">
						<div class="DRIPsched">
							<div class="imgDRipimg">
								<div class="imgheaddrip">
									<div class="txtheadDIV"><p>Meter your drips day and time</p></div>
									<div class="imgarwdwon"><img src="<?php bloginfo('template_url'); ?>/images/hoverimg_down.png"/></div>
								</div>
								<img src="<?php bloginfo('template_url'); ?>/images/drip_scheduleimg.png">
							</div>
							<ul id="drip_days">
								<li class="lidripsched">
									<a>Monday</a>
									<div class="hovrrytDIV">
										<p>Toggle the days to drip</p>
										<img src="<?php bloginfo('template_url'); ?>/images/toggleimgdrip.png">
									</div>
								</li>
								<li class="lidripsched">
									<a>Tuesday</a>
									<div class="hovrrytDIV">
										<p>Toggle the days to drip</p>
										<img src="<?php bloginfo('template_url'); ?>/images/toggleimgdrip.png">
									</div>
								</li>
								<li class="lidripsched">
									<a>Wednesday</a>
									<div class="hovrrytDIV">
										<p>Toggle the days to drip</p>
										<img src="<?php bloginfo('template_url'); ?>/images/toggleimgdrip.png">
									</div>
								</li>
								<li class="lidripsched">
									<a>Thursday</a>
									<div class="hovrrytDIV">
										<p>Toggle the days to drip</p>
										<img src="<?php bloginfo('template_url'); ?>/images/toggleimgdrip.png">
									</div>
								</li>
								<li class="lidripsched">
									<a>Friday</a>
									<div class="hovrrytDIV" id="toggle_default">
										<p>Toggle the days to drip</p>
										<img src="<?php bloginfo('template_url'); ?>/images/toggleimgdrip.png">
									</div>
								</li>
								<li class="lidripsched_nonact">
									<a>Saturday</a>
									<div class="hovrrytDIV">
										<p>Toggle the days to drip</p>
										<img src="<?php bloginfo('template_url'); ?>/images/toggleimgdrip.png">
									</div>
								</li>
								<li class="lidripsched_nonact">
									<a>Sunday</a>
									<div class="hovrrytDIV">
										<p>Toggle the days to drip</p>
										<img src="<?php bloginfo('template_url'); ?>/images/toggleimgdrip.png">
									</div>
								</li>
							</ul>
						</div>
						<div class="tymdripDIV">
							<ul>
								<li class="litymhddrip_addimg">
									<img src="<?php bloginfo('template_url'); ?>/images/drip_addimg.png">
									<div id="imghoveraadddays">
										<div><img src="<?php bloginfo('template_url'); ?>/images/hoverimg_up.png"/></div>
										<div id="textme8drip"><p>Meter the times to drip</p></div>
									</div>
								</li>
							</ul>
						</div>
						<div class="dripbucketDIV">
							<div class="imgDRipimg">
								<div class="imgheaddrip">
									<div class="txtheadDIV"><p>Bucket contains all your drips</p></div>
									<div class="imgarwdwon"><img src="<?php bloginfo('template_url'); ?>/images/hoverimg_down.png"/></div>
								</div>
								<img src="<?php bloginfo('template_url'); ?>/images/dripbucket.png">
							</div>
							<ul>
								<li class="libucketdrip">
									<p class="dripbucketDAy" id="drip_adjust">Today 21</p>
									<img class="bucket_imgicon" src="<?php bloginfo('template_url'); ?>/images/dripbucket_imgicon.png">
									<div class="dayhovermsg">
										<div class="dayhoverimgarrow"><img src="<?php bloginfo('template_url'); ?>/images/bucket_dayimgdrip.png"/></div>
										<div class="hovermsgp"><p>Click to days drips</p></div>
									</div>
								</li>
								<li class="libucketdrip">
									<p class="dripbucketDAy" id="drip_adjust">Thursday 22</p>
									<img class="bucket_imgicon" src="<?php bloginfo('template_url'); ?>/images/dripbucket_imgicon.png">
									<div class="dayhovermsg">
										<div class="dayhoverimgarrow"><img src="<?php bloginfo('template_url'); ?>/images/bucket_dayimgdrip.png"/></div>
										<div class="hovermsgp"><p>Click to days drips</p></div>
									</div>
								</li>
								<li class="libucketdrip">
									<p class="dripbucketDAy" id="drip_adjust">Friday 23</p>
									<img class="bucket_imgicon" src="<?php bloginfo('template_url'); ?>/images/dripbucket_imgicon.png">
									<div class="dayhovermsg">
										<div class="dayhoverimgarrow"><img src="<?php bloginfo('template_url'); ?>/images/bucket_dayimgdrip.png"/></div>
										<div class="hovermsgp"><p>Click to days drips</p></div>
									</div>
								</li>
								<li class="libucketdrip">
									<p class="dripbucketDAy" id="drip_adjust">Monday 24</p>
									<img class="bucket_imgicon" src="<?php bloginfo('template_url'); ?>/images/dripbucket_imgicon.png">
									<div class="dayhovermsg">
										<div class="dayhoverimgarrow"><img src="<?php bloginfo('template_url'); ?>/images/bucket_dayimgdrip.png"/></div>
										<div class="hovermsgp"><p>Click to days drips</p></div>
									</div>
								</li>
								<li class="libucketdrip">
									<p class="dripbucketDAy" id="drip_adjust">Tuesday 25</p>
									<img class="bucket_imgicon" src="<?php bloginfo('template_url'); ?>/images/dripbucket_imgicon.png">
									<div class="dayhovermsg">
										<div class="dayhoverimgarrow"><img src="<?php bloginfo('template_url'); ?>/images/bucket_dayimgdrip.png"/></div>
										<div class="hovermsgp"><p>Click to days drips</p></div>
									</div>
								</li>
								<li class="libucketdrip">
									<p class="dripbucketDAy" id="drip_adjust">Thursday 26</p>
									<img class="bucket_imgicon" src="<?php bloginfo('template_url'); ?>/images/dripbucket_imgicon.png">
									<div class="dayhovermsg">
										<div class="dayhoverimgarrow"><img src="<?php bloginfo('template_url'); ?>/images/bucket_dayimgdrip.png"/></div>
										<div class="hovermsgp"><p>Click to days drips</p></div>
									</div>
								</li>
								<li class="libucketdrip">
									<p class="dripbucketDAy" id="drip_adjust">Wednes. 27</p>
									<img class="bucket_imgicon" src="<?php bloginfo('template_url'); ?>/images/dripbucket_imgicon.png">
									<div class="dayhovermsg">
										<div class="dayhoverimgarrow"><img src="<?php bloginfo('template_url'); ?>/images/bucket_dayimgdrip.png"/></div>
										<div class="hovermsgp"><p>Click to days drips</p></div>
									</div>
								</li>
								<li class="libucketdrip">
									<p class="dripbucketDAy" id="drip_adjust">Thursday 28</p>
									<img class="bucket_imgicon" src="<?php bloginfo('template_url'); ?>/images/dripbucket_imgicon.png">
									<div class="dayhovermsg">
										<div class="dayhoverimgarrow"><img src="<?php bloginfo('template_url'); ?>/images/bucket_dayimgdrip.png"/></div>
										<div class="hovermsgp"><p>Click to days drips</p></div>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<div class="drips_con">
					<h2>Drip Schedules</h2>
					<div class="latest_post">	
						<ul id="sort_us"></ul>
					</div>
				</div>
			<div class="drips_con">
				<div class="analyze_dripdiv">
					Analyze Tab
				</div>
			</div>
			<div class="drips_con">
				<div class="DRIPdivcrtacc_cha">
					<div class="DRIPdivcrtacc_overflown">
						<ul id="chansort">
							<?php 
							global $LP_channel_settings;
							foreach($LP_channel_settings as $key => $channel):
								$active = "";
								if($channel["active"] == 1)$active = "active";
							?>
							<li class="chan_lis frstfont <?php echo $active;?>" param="<?php echo $key;?>">
								<i class="chandrop">
									<div class="followchandiv">
										<div class="followchan"></div>
										<span>Follow</span>
									</div>
									<div class="ufollowchandiv">
										<div class="ufollowchan"></div>
										<span>Unfollow</span>
									</div>
								</i>
								<?php echo $channel["label"];?>
							</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
			<div id="arrowup"></div>
            </div>
			<!-- Latest Post-->	
		</div>
		<!--<div id="whiteclosediv">
			<span id="closedivfld">X</span>
		</div>-->	
	</div>
	<div id="content">
		<div class="lnkrotateadz">
			<a href="#">Advertisement Link -</a> <span>Center Rotational Link</span>
		</div>
		<div id="contenrBODY">
    <script type="text/javascript">
        <?php $timestamp = time();?>
        // jQuery(document).ready(function(){
            // jQuery(function() {
                // jQuery('#lupic').uploadify({
                    // 'formData'     : {
                        // 'timestamp' : '<?php echo $timestamp;?>',
                        // 'token'     : '<?php echo md5('unique_salt' . $timestamp);?>'
                    // },
                    // 'swf'      : '/wp-content/plugins/LinkedPost/uploadify/uploadify.swf?t=<?php echo $timestamp;?>',
                    // 'uploader' : 'lp_uploadify'
                // });
                // alert("test");
            // });
        // });
    </script>