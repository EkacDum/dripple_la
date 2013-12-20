<?php 
global $current_site;
// $LP_siteurl = "http://".$current_site->domain;
$LP_siteurl = trim(network_site_url(),"/");
$user_info = get_blog_option(LP_get_user_blog_id(),"LP_linkedin_info");
$profile_pic = ($user_info["linkedin_profile_pic"] ? $user_info["linkedin_profile_pic"] : $user_info["linkedin_profile_thumb"]);
if($profile_pic){
    $author_avatar = "<img src=\"".$profile_pic."\" alt=\"User profile image\" width=\"80\" height=\"80\"/>";
}else{
    $author_avatar = get_avatar($user_email, 72, $default_avatar);
}
?>
<div id="notif_bar"></div>
<div id="grpinddivlog">
    <div class="signlogindivhead">
        <h2>Get starting posting in less than 2 minutes</h2>
        <span class="colspandiv span01headfree">it's for free</span>
        <div>



            <div id="fb-root"></div>
            <script>

                window.fbAsyncInit = function() {
                    FB.init({
                        appId      : '173362096203111',
                        status     : true, // check login status
                        cookie     : true, // enable cookies to allow the server to access the session
                        xfbml      : true,  // parse XFBML
                        oauth : true
                    });
                    <?php if(!is_user_logged_in()){?>
                    // Here we subscribe to the auth.authResponseChange JavaScript event. This event is fired
                    // for any authentication related change, such as login, logout or session refresh. This means that
                    // whenever someone who was previously logged out tries to log in again, the correct case below
                    // will be handled.
                    FB.Event.subscribe('auth.authResponseChange', function(response) {
                        // Here we specify what we do with the response anytime this event occurs.
                        if (response.status === 'connected') {
                            // The response object is returned with a status field that lets the app know the current
                            // login status of the person. In this case, we're handling the situation where they
                            // have logged in to the app.
                            testAPI();
                        } else if (response.status === 'not_authorized') {
                            // In this case, the person is logged into Facebook, but not into the app, so we call
                            // FB.login() to prompt them to do so.
                            // In real-life usage, you wouldn't want to immediately prompt someone to login
                            // like this, for two reasons:
                            // (1) JavaScript created popup windows are blocked by most browsers unless they
                            // result from direct interaction from people using the app (such as a mouse click)
                            // (2) it is a bad experience to be continually prompted to login upon page load.
                            FB.login();
                        } else {
                            // In this case, the person is not logged into Facebook, so we call the login()
                            // function to prompt them to do so. Note that at this stage there is no indication
                            // of whether they are logged into the app. If they aren't then they'll see the Login
                            // dialog right after they log in to Facebook.
                            // The same caveats as above apply to the FB.login() call here.
                            FB.login();
                        }
                    });
                    <?php }?>
                };

                // Load the SDK asynchronously
                (function(d){
                    var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
                    if (d.getElementById(id)) {return;}
                    js = d.createElement('script'); js.id = id; js.async = true;
                    js.src = "//connect.facebook.net/en_US/all.js";
                    ref.parentNode.insertBefore(js, ref);
                }(document));

                // Here we run a very simple test of the Graph API after login is successful.
                // This testAPI() function is only called in those cases.
                function testAPI() {
                    console.log('Welcome!  Fetching your information.... ');
                    FB.api('/me', function(response) {
                        var data ={
                            action  : "LP_fb_login",
                            fb_user : response
                        };
                        jQuery.post(ajaxurl,data,function(r){
                            //LP_console(r);
                            window.location.replace(r);
                        });
                    });
                }
            </script>

			<div class="lndinimg" id="lin_JSAPI_login"><script type="IN/Login"></script></div>
			<div class="lndinimg" id="lin_auto_login" style="display:none"></div>
            <div class="lndinimg linkedIn_register_button"><img width="292" height="45" alt="<?php bloginfo('template_url');?>/images/signlinkedin.png" src="<?php bloginfo('template_url');?>/images/signlinkedin.png"></div>
            <div class="lndinimg FB_register_button"><img width="292" height="45" alt="<?php bloginfo('template_url');?>/images/signlinkedin.png" src="<?php bloginfo('template_url');?>/images/signlinkedin.png"></div>
            <div class="lndinimg TW_register_button"><img width="292" height="45" alt="<?php bloginfo('template_url');?>/images/signlinkedin.png" src="<?php bloginfo('template_url');?>/images/signlinkedin.png"></div>
            <span class="header_form_agree">By clicking the button. You agree to our Policies and connect your linkedin account with us.</span>
        </div>
    </div>
    <!-- div class="registerhead">
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
    </div -->
    <!-- div class="createaccdiv">
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
                <input class="lemail" name="lemail" type="hidden" placeholder="Email">
                <input type="hidden" name="lpass" class="lpass" placeholder="Password">
                <input class="cpass" name="cpass" type="hidden" placeholder="Password Again">
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
    </div -->
    <div class="signdiv">
        <div class="signlog">
            <div class="lndinimg linkedIn_login_button">
                <img width="292" height="45" src="<?php bloginfo('template_url');?>/images/loglinkedin.png" alt="Login with Linkedin">
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
	<?php if(is_user_logged_in()){ ?>
    <div class="dripgrpDIV" style="display: none;">
		<div class="arrowprev gradient_1"><i class="arrowleft-d-30"></i></div>
		<div class="arrownext gradient_1"><i class="arrowright-d-30"></i></div>
        <div class="dripheadbtn">
            <div class="drips_con" id="scoop">
                <div class="containforfix">
					<h2>Scoop</h2>
                    <div class="body collect_dripdiv">
						<div id="topic_collection_setup">
							<div>
								<div id="left_form">
									<div class="search_sources">
										<?php 
										$gnew = "";
										$gblog = "";                        
										$stwitter = "";                        
										$sdripple = "";                   
										$search_sources = json_decode(LP_get_user_blog_option("TOPIC_SEARCH_SOURCES"),true);
										if($search_sources["news"]){
											$gnew = "active";
										}
										if($search_sources["blogs"]){
											$gblog = "active";
										}
										if($search_sources["twitter"] && LP_user_twitter_token()!==false){
											$stwitter = "active";
										}
										if($search_sources["dripple"]){
											$sdripple = "active";
										}
										?>
										<div id="search_source_cont">
										</div>
										<span class="btn2" id="toggle_rss_search">RSS Feeds</span>
									</div>
									<div class="search_keywords">
										<div class="keywords">
											<div>
												<span>
													<i class="greycross-d-18 the_move"></i>
													<input class="topic_keyword" type="text" placeholder="keyword"/>
												</span>
												<i class="trash-d-18 the_trash"></i>
											</div>
										</div>
										<div class="search_btns">
											<!-- span class="blue_button topic_add_keyword">Add Keyword</span -->
											<div class="button2 topic_add_keyword">
												<div class="label">
													<div class="handle">
														<i class="key-l-18"></i>
													</div>
													<span>Add keyword</span>
													<div class="handle right">
														<i class="drop_add"></i>
													</div>
												</div>
											</div>
											<div class="button2 topic_add_keyword filter">
												<div class="label">
													<div class="handle">
														<i class="key-l-18"></i>
													</div>
													<span>Filter keyword</span>
													<div class="handle right">
														<i class="drop_add"></i>
													</div>
												</div>
											</div>
											<span class="btn2" id="topic_keyword_preview">Preview</span>
											<div id="search_highlight">
												<div></div>
												<div></div>
												<div></div>
												<div></div>
											</div>
											<span class="keyword_ideas"><a href="http://www.wordtracker.com" target="_blank">Keyword Ideas</a></span>
										</div>
									</div>
								</div>
								<div class="right_form">
									<div id="scoop_view"><i class="view_list-l-40 view list"></i><i class="view_section-l-40 view section active"></i></div>
									<span class="stats_search_results"></span><input type="text" id="search_scoop_results" class="input_search_results" placeholder="Search" />
									<div class="quick_search"></div>
									<div class="cols">
										<div class="results_news_tpl1 drippleScrollbar" id="t_splash_google_suggestions"></div>
									</div>
								</div>
							</div>
						</div>
						
						<div id="topic_collection_rss_setup">
							<div>
								<div id="leftr_form">
									<div class="search_sources">
										<span class="btn2 active" id="btn_back_to_search"><i class="left_arr"></i>Google Search</span>
										<span class="btn2 active" id="toggle_rss">RSS Feeds<i class="drop_arr" style="float: right;margin-top: 10px;"></i></span>										
										<span class="items_pointer"></span>
										<div id="selected_rss"></div>
										<span class="mblue_button" id="topic_feeds_preview">Preview</span>
									</div>
									<div class="search_keywords">
										<div class="keywords">
											<div>
												<span>
													<i class="greycross-d-18 the_move"></i>
													<input class="topic_keyword" type="text" placeholder="keyword"/>
												</span>
												<i class="trash-d-18 the_trash"></i>
											</div>
										</div>
										<div class="search_btns">

											<div class="button2 topic_add_keyword">
												<div class="label">
													<div class="handle">
														<i class="key-l-18"></i>
													</div>
													<span>Add keyword</span>
													<div class="handle right">
														<i class="drop_add"></i>
													</div>
												</div>
											</div>
											
											<div class="button2 topic_add_keyword filter">
												<div class="label">
													<div class="handle">
														<i class="key-l-18"></i>
													</div>
													<span>Filter keyword</span>
													<div class="handle right">
														<i class="drop_add"></i>
													</div>
												</div>
											</div>
											
											<span class="btn2" id="topic_rss_keyword_preview">Preview</span>
											<span class="keyword_ideas"><a href="http://www.wordtracker.com" target="_blank">Keyword Ideas</a></span>
										</div>
									</div>
								</div>
								<div class="right_form">
									<span class="stats_search_results"></span><input type="text" id="search_rss_results" class="input_search_results" placeholder="Search" />
									<div class="cols">
										<div class="results_news_tpl1 drippleScrollbar"></div>
									</div>
								</div>
							</div>
						</div>
                    </div>
                </div>
            </div>
			<div class="drips_con" id="topic">
                <div class="containforfix topic_tab" style="position:relative;">
					<h2>Topic</h2>
					<!-- div class="the_cropper">
						<div class="cropper_rotate"><div class="drag"></div><div class="rail"></div></div>
						<div class="cropper_cont">
							<div class="cropper_handle"></div>
							<div class="cropper_mask"></div>
							<img class="cropper_subject" id="cropper_subject" src="<?php bloginfo('template_url'); ?>/images/trans.png" alt=""/>
						</div>
						<div style="width:495px;margin:30px auto;">
							<div class="cropper_zoom"><div class="drag"></div><div class="rail"></div></div>
							<div class="cropper_btns">
								<span class="buttons2" id="replace_subject">Browse</span>
								<span class="buttons2" id="done_cropping">Apply</span>
							</div>
						</div>
					</div -->
					
					<div class="the_cropper new_topic_image">
						<div class="cropper_cont" style="float:right;">
							<div class="pre_loading">Loading...</div>
							<div class="cropper_handle"><textarea class="url_catcher"></textarea></div>
							<div class="cropper_mask"></div>
							<img class="cropper_subject" id="cropper_subject" src="">
						</div>
						<div style="width: 312px;float: left;">
							<div class="scroller_label">zoom</div>
							<div class="cropper_zoom"><div class="drag"></div><div class="rail"></div></div>
							<div class="scroller_label">rotate</div>
							<div class="cropper_rotate"><div class="drag"></div><div class="rail"></div></div>							
							<div class="cropper_btns">
								<span class="buttons2" id="replace_subject">Browse</span>
								<span class="buttons2" id="done_cropping">Apply</span>
							</div>
						</div>
					</div>
						
						
					<div class="body current_topic">
						<div class="post_holder_addtopic flipper flipbox-container current">
							<div class="the_flipping">
								<form id="LP_current_topic_form" name="LP_current_topic_form" method="post" action="<?php echo $LP_siteurl; ?>/lp_update_current_topic" target="x" enctype="multipart/form-data">
									<div class="addtopic_feat_img_div current">
										<i class="turn-l-18 flip_me"></i>
										<!-- span class="feat_img_info">Drop image here to update thumbnail.</span -->
										<img id="mcurrent_topic_image" src="<?php bloginfo('template_url'); ?>/images/trans.png" alt="">
										
											<input class="current_topic_image addtopic_feat_img" id="current_topic_image" name="current_topic_image" type="file">
											<div class="fake_upload_button">Upload Image</div>
											<input type="hidden" id="iframe_name" name="iframe_name" value="">
											<input type="hidden" class="topic" name="topic" value="">
											<input type="hidden" class="scaled_width" name="scaled_width" value="">
											<input type="hidden" class="top" name="top" value="">
											<input type="hidden" class="left" name="left" value="">
											<input type="hidden" class="deg" name="deg" value="">
										
									</div>
									<div class="tile_low">
										<div class="topic_post_cont_div addtopic_post_cont_div">
											<span class="topic_chann  addtopic_chann" id="current_topic_channel"></span>
											<span class="topic_post_title addtopic_post_title to_topic_setup">
											<textarea placeholder="Topic Title" class="topic_title" rows="2" id="current_topic_title" name="current_topic_title"></textarea></span>
											<span class="topic_post_cont"><textarea  placeholder="Topic Description" class="topic_description" id="current_topic_content" name="current_topic_content"></textarea></span>
										</div>
										<span class="buttons2" id="update_currenttopic" style="float:right;">Update</span>
									</div>
								</form>
							</div>
							
							<div class="back_flip" style="display:none;">
								<div class="back_flipper">
									<div style="padding-left: 25px;">
										<select class="custom_select2" id="current_topic_channel_back">
											<option value="1">buzz</option>
											<option value="2">editor picks</option>
											<option value="3">tech</option>
											<option value="4">big business</option>
											<option value="5">us news</option>
											<option value="6">world news</option>
											<option value="7">social media</option>
											<option value="8">ideas</option>
											<option value="9">advice</option>
											<option value="10">higher education</option>
											<option value="11">media</option>
											<option value="12">small business</option>
											<option value="13">leadership</option>
											<option value="14">marketing</option>
											<option value="15">china</option>
											<option value="16">economy</option>
											<option value="17">retail</option>
											<option value="18">career</option>
											<option value="19">management</option>
											<option value="20">law</option>
											<option value="21">networking</option>
											<option value="22">hotel</option>
											<option value="23">manufacturing</option>
											<option value="24">health care</option>
											<option value="25">trade</option>
											<option value="26">europe</option>
											<option value="27">energy</option>
											<option value="28">stocks</option>
											<option value="29">politics</option>
											<option value="30">wealth</option>
										</select>
										<input type="hidden" id="current_topic_channel_back_cs2" value="channel" />
										
										<select class="custom_select2" id="current_stiky_drip">
											<option value="sticky drip">Sticky Drip</option>
										</select>
										<input type="hidden" id="current_stiky_drip_cs2" value="Sticky Drip" />
										
										<select class="custom_select2" id="current_industry">
											<?php
											global $industries;
											foreach($industries as $industry){
												$industy_options.= "<option value=\"".$industry["code"]."\">".$industry["description"]."</option>";
											}
											echo $industy_options;
											?>
										</select>
										<input type="hidden" id="current_industry_cs2" value="Industry" />
										
										<select class="custom_select2" id="current_language">
										<?php global $LP_language_options;
											echo $LP_language_options;
										?>
										</select>
										<input type="hidden" id="current_language_cs2" value="Language" />
										
									</div>
									<div style="padding:50px 0;">
										<div style="float:left;width:50%;">
											<div class="item">
												<span class="label">Results</span>
												<input type="checkbox" id="current_results" class="checkbox topic_reults custom_checkbox" value="10"/>
												<input type="hidden" id="current_results_cb" value="20"/>
											</div>
											
											<div class="item">
												<span class="label">Messages</span>
												<input type="checkbox" id="current_message" class="checkbox topic_reults custom_checkbox" value="Yes"/>
												<input type="hidden" id="current_message_cb" value="No"/>
											</div>
											
											<div class="item">
												<span class="label">Iframe</span>
												<input type="checkbox" id="current_iframe" class="checkbox topic_reults custom_checkbox" value="Yes"/>
												<input type="hidden" id="current_iframe_cb" value="No"/>
											</div>
											
											<div class="item">
												<span class="label">Drip URL</span>
												<input type="checkbox" id="current_dripurl" class="checkbox topic_reults custom_checkbox" value="Yes"/>
												<input type="hidden" id="current_dripurl_cb" value="No"/>
											</div>
										</div>
										
										<div style="float:right;width:50%;">
											<div class="item">
												<span class="label">Trash</span>
												<input type="checkbox" id="current_trash" class="checkbox topic_reults custom_checkbox" value="Yes"/>
												<input type="hidden" id="current_trash_cb" value="No"/>
											</div>
											
											<div class="item">
												<span class="label">Private</span>
												<input type="checkbox" id="current_private" class="checkbox topic_reults custom_checkbox" value="Yes"/>
												<input type="hidden" id="current_private_cb" value="No"/>
											</div>
											
											<div class="item">
												<span class="label">Flip</span>
												<input type="checkbox" id="current_flip" class="checkbox topic_reults custom_checkbox" value="Click"/>
												<input type="hidden" id="current_flip_cb" value="Hover"/>
											</div>
											
											<div class="item">
												<span class="label">Timezone</span>
												<input type="checkbox" id="current_timezone" class="checkbox topic_reults custom_checkbox" value="Lock"/>
												<input type="hidden" id="current_timezone_cb" value="Unlock"/>
											</div>
										</div>
									</div>									
									<!-- span class="buttons2" id="update_topic_back" style="float: right;width: 105px;text-align: center;">Update</span -->
									<span class="buttons2 reverse_flip" id="current_back_flip" style="float: right;width: 105px;text-align: center;margin-right:10px;">Flip</span>
									
								</div>
							</div>
						</div>
						
						<div class="post_holder_addtopic new_topic flipbox-container">
							<div class="the_flipping">
								<div class="addtopic_feat_img_div adding_new_topic">
									<i class="turn-l-18 flip_me" style="display:none;"></i>
									<!-- span class="feat_img_info">Drop image here to update thumbnail.</span -->
									<img id="LP_new_topic_image" src="<?php bloginfo('template_url'); ?>/images/trans.png" alt="">
									<form id="LP_new_topic_uploader" name="LP_new_topic_uploader" method="post" action="<?php echo $LP_siteurl; ?>/LP_update_topic_thumb" target="LP_new_topic_uploader_iframe" enctype="multipart/form-data">
										<input class="addtopic_feat_img" id="new_topic_image" name="new_topic_image" type="file">
										<div class="fake_upload_button">Upload Image</div>
										<input type="hidden" id="callback_function" name="callback_function" value="LP_new_topic_CB">
										<input type="hidden" class="topic" name="topic" value="">
										<input type="hidden" id="type" name="type" value="">
										<!-- input type="hidden" id="channel" name="channel" value="" -->
										<input type="hidden" id="wpbody" name="wpbody" value="">
										<input type="hidden" id="wptitle" name="wptitle" value="">
										<input type="hidden" class="scaled_width" name="scaled_width" value="">
										<input type="hidden" class="top" name="top" value="">
										<input type="hidden" class="left" name="left" value="">
									</form>
								</div>
								<div class="tile_low">
									<div class="topic_post_cont_div addtopic_post_cont_div">
										<span class="topic_chann  addtopic_chann">
											&nbsp;
										</span>
										<span class="topic_post_title addtopic_post_title to_topic_setup">
											<textarea placeholder="Topic Title" class="new_topic_title"></textarea>
										</span>
										<span class="topic_post_cont">
											<textarea  placeholder="Topic Description" class="new_topic_description"></textarea>
										</span>
									</div>
									<span class="buttons2" style="float: right;width: 105px;text-align: center;" id="create_new_topic">Next</span>
								</div>
							</div>
							<iframe id="LP_new_topic_uploader_iframe" name="LP_new_topic_uploader_iframe" style="display:none;"></iframe>
							<div class="back_flip" style="display:none;">
								<div class="back_flipper">
									<input type="hidden" id="topic_back" name="topic_back" value="">
									<div style="padding-left: 25px;">
										<select class="custom_select2" id="new_topic_channel">
											<option value="0" selected>channel</option>
											<option value="1">buzz</option>
											<option value="2">editor picks</option>
											<option value="3">tech</option>
											<option value="4">big business</option>
											<option value="5">us news</option>
											<option value="6">world news</option>
											<option value="7">social media</option>
											<option value="8">ideas</option>
											<option value="9">advice</option>
											<option value="10">higher education</option>
											<option value="11">media</option>
											<option value="12">small business</option>
											<option value="13">leadership</option>
											<option value="14">marketing</option>
											<option value="15">china</option>
											<option value="16">economy</option>
											<option value="17">retail</option>
											<option value="18">career</option>
											<option value="19">management</option>
											<option value="20">law</option>
											<option value="21">networking</option>
											<option value="22">hotel</option>
											<option value="23">manufacturing</option>
											<option value="24">health care</option>
											<option value="25">trade</option>
											<option value="26">europe</option>
											<option value="27">energy</option>
											<option value="28">stocks</option>
											<option value="29">politics</option>
											<option value="30">wealth</option>
										</select>
										<input type="hidden" id="new_topic_channel_cs2" value="channel" />
										
										<select class="custom_select2" id="new_stiky_drip">
											<option value="sticky drip">Sticky Drip</option>
										</select>
										<input type="hidden" id="new_stiky_drip_cs2" value="Sticky Drip" />
										
										<select class="custom_select2" id="new_industry">
											<?php echo $industy_options; ?>
										</select>
										<input type="hidden" id="new_industry_cs2" value="Industry" />
										
										<select class="custom_select2" id="new_language">
											<?php global $LP_language_options;
												echo $LP_language_options;
											?>
										</select>
										<input type="hidden" id="new_language_cs2" value="Language" />
										
									</div>
									<div style="padding:50px 0;">
										<div style="float:left;width:50%;">
											<div class="item">
												<span class="label">Results</span>
												<input type="checkbox" id="new_results" class="checkbox topic_reults custom_checkbox" value="10"/>
												<input type="hidden" id="new_results_cb" value="20"/>
											</div>
											
											<div class="item">
												<span class="label">Messages</span>
												<input type="checkbox" id="new_messages" class="checkbox topic_reults custom_checkbox" value="Yes"/>
												<input type="hidden" id="new_messages_cb" value="No"/>
											</div>
											
											<div class="item">
												<span class="label">Iframe</span>
												<input type="checkbox" id="new_iframe" class="checkbox topic_reults custom_checkbox" value="Yes"/>
												<input type="hidden" id="new_iframe_cb" value="No"/>
											</div>
											
											<div class="item">
												<span class="label">Drip URL</span>
												<input type="checkbox" id="new_dripurl" class="checkbox topic_reults custom_checkbox" value="Yes"/>
												<input type="hidden" id="new_dripurl_cb" value="No"/>
											</div>
										</div>
										
										<div style="float:right;width:50%;">
											<div class="item">
												<span class="label">Trash</span>
												<input type="checkbox" id="new_trash" class="checkbox topic_reults custom_checkbox" value="Yes"/>
												<input type="hidden" id="new_trash_cb" value="No"/>
											</div>
											
											<div class="item">
												<span class="label">Private</span>
												<input type="checkbox" id="new_private" class="checkbox topic_reults custom_checkbox" value="Yes"/>
												<input type="hidden" id="new_private_cb" value="No"/>
											</div>
											
											<div class="item">
												<span class="label">Flip</span>
												<input type="checkbox" id="new_flip" class="checkbox topic_reults custom_checkbox" value="Click"/>
												<input type="hidden" id="new_flip_cb" value="Hover"/>
											</div>
											
											<div class="item">
												<span class="label">Timezone</span>
												<input type="checkbox" id="new_timezone" class="checkbox topic_reults custom_checkbox" value="Lock"/>
												<input type="hidden" id="new_timezone_cb" value="Unlock"/>
											</div>
										</div>
									</div>
									<span class="buttons2" id="create_new_topic_back" style="float: right;width: 105px;text-align: center;">Next</span>
									<span class="buttons2 reverse_flip" style="float: right;width: 105px;text-align: center;margin-right:10px;">Back</span>
								</div>
							</div>
						</div>
		
					</div>
				</div>
			</div>
            <div class="drips_con" id="drip">
                <div class="containforfix">
					<h2>Drip</h2>
                    <div class="body DRIPdivcrtacc_inf">
                        <div class="DRIPsched">
                            <ul id="drip_days">
                                <li class="button_gray lidripsched">
                                    Monday
                                    <div class="hovrrytDIV">
                                        <p>Toggle the days to drip</p>
                                        <img width="13" height="25" src="<?php bloginfo('template_url'); ?>/images/toggleimgdrip.png" alt="">
                                    </div>
                                </li>
                                <li class="button_gray lidripsched">
                                    <a>Tuesday</a>
                                    <div class="hovrrytDIV">
                                        <p>Toggle the days to drip</p>
                                        <img width="13" height="25" src="<?php bloginfo('template_url'); ?>/images/toggleimgdrip.png" alt="">
                                    </div>
                                </li>
                                <li class="button_gray lidripsched">
                                    Wednesday
                                    <div class="hovrrytDIV">
                                        <p>Toggle the days to drip</p>
                                        <img width="13" height="25" src="<?php bloginfo('template_url'); ?>/images/toggleimgdrip.png" alt="">
                                    </div>
                                </li>
                                <li class="button_gray lidripsched">
                                    Thursday
                                    <div class="hovrrytDIV">
                                        <p>Toggle the days to drip</p>
                                        <img width="13" height="25" src="<?php bloginfo('template_url'); ?>/images/toggleimgdrip.png" alt="">
                                    </div>
                                </li>
                                <li class="button_gray lidripsched">
                                    Friday
                                    <div class="hovrrytDIV" id="toggle_default">
                                        <p>Toggle the days to drip</p>
                                        <img width="13" height="25" src="<?php bloginfo('template_url'); ?>/images/toggleimgdrip.png" alt="">
                                    </div>
                                </li>
                                <li class="button_gray lidripsched">
                                    Saturday
                                    <div class="hovrrytDIV">
                                        <p>Toggle the days to drip</p>
                                        <img width="13" height="25" src="<?php bloginfo('template_url'); ?>/images/toggleimgdrip.png" alt="">
                                    </div>
                                </li>
                                <li class="button_gray lidripsched">
                                    Sunday
                                    <div class="hovrrytDIV">
                                        <p>Toggle the days to drip</p>
                                        <img width="13" height="25" src="<?php bloginfo('template_url'); ?>/images/toggleimgdrip.png" alt="">
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="tymdripDIV">
						<div class="csb_main">
                            <ul id="meter_times" class="csb_body"></ul>
							<div class="csb_rail"><div class="csb_handle"></div></div>
						</div>
							<div class="litymhddrip_addimg">
								<div class="button2">
									<div class="label">
										<div class="handle">
											<i class="clock-l-18"></i>
										</div>
										<span>Add time</span>
										<div class="handle right">
											<i class="drop_add"></i>
										</div>
									</div>
								</div>
							</div>
							<div class="drip_timzone" style="clear:both;">
								<select name="timezone_string" id="timezone_string">
<optgroup label="Africa">
<option value="Africa/Abidjan">Abidjan</option>
<option value="Africa/Accra">Accra</option>
<option value="Africa/Addis_Ababa">Addis Ababa</option>
<option value="Africa/Algiers">Algiers</option>
<option value="Africa/Asmara">Asmara</option>
<option value="Africa/Bamako">Bamako</option>
<option value="Africa/Bangui">Bangui</option>
<option value="Africa/Banjul">Banjul</option>
<option value="Africa/Bissau">Bissau</option>
<option value="Africa/Blantyre">Blantyre</option>
<option value="Africa/Brazzaville">Brazzaville</option>
<option value="Africa/Bujumbura">Bujumbura</option>
<option value="Africa/Cairo">Cairo</option>
<option value="Africa/Casablanca">Casablanca</option>
<option value="Africa/Ceuta">Ceuta</option>
<option value="Africa/Conakry">Conakry</option>
<option value="Africa/Dakar">Dakar</option>
<option value="Africa/Dar_es_Salaam">Dar es Salaam</option>
<option value="Africa/Djibouti">Djibouti</option>
<option value="Africa/Douala">Douala</option>
<option value="Africa/El_Aaiun">El Aaiun</option>
<option value="Africa/Freetown">Freetown</option>
<option value="Africa/Gaborone">Gaborone</option>
<option value="Africa/Harare">Harare</option>
<option value="Africa/Johannesburg">Johannesburg</option>
<option value="Africa/Juba">Juba</option>
<option value="Africa/Kampala">Kampala</option>
<option value="Africa/Khartoum">Khartoum</option>
<option value="Africa/Kigali">Kigali</option>
<option value="Africa/Kinshasa">Kinshasa</option>
<option value="Africa/Lagos">Lagos</option>
<option value="Africa/Libreville">Libreville</option>
<option value="Africa/Lome">Lome</option>
<option value="Africa/Luanda">Luanda</option>
<option value="Africa/Lubumbashi">Lubumbashi</option>
<option value="Africa/Lusaka">Lusaka</option>
<option value="Africa/Malabo">Malabo</option>
<option value="Africa/Maputo">Maputo</option>
<option value="Africa/Maseru">Maseru</option>
<option value="Africa/Mbabane">Mbabane</option>
<option value="Africa/Mogadishu">Mogadishu</option>
<option value="Africa/Monrovia">Monrovia</option>
<option value="Africa/Nairobi">Nairobi</option>
<option value="Africa/Ndjamena">Ndjamena</option>
<option value="Africa/Niamey">Niamey</option>
<option value="Africa/Nouakchott">Nouakchott</option>
<option value="Africa/Ouagadougou">Ouagadougou</option>
<option value="Africa/Porto-Novo">Porto-Novo</option>
<option value="Africa/Sao_Tome">Sao Tome</option>
<option value="Africa/Tripoli">Tripoli</option>
<option value="Africa/Tunis">Tunis</option>
<option value="Africa/Windhoek">Windhoek</option>
</optgroup>
<optgroup label="America">
<option value="America/Adak">Adak</option>
<option value="America/Anchorage">Anchorage</option>
<option value="America/Anguilla">Anguilla</option>
<option value="America/Antigua">Antigua</option>
<option value="America/Araguaina">Araguaina</option>
<option value="America/Argentina/Buenos_Aires">Argentina - Buenos Aires</option>
<option value="America/Argentina/Catamarca">Argentina - Catamarca</option>
<option value="America/Argentina/Cordoba">Argentina - Cordoba</option>
<option value="America/Argentina/Jujuy">Argentina - Jujuy</option>
<option value="America/Argentina/La_Rioja">Argentina - La Rioja</option>
<option value="America/Argentina/Mendoza">Argentina - Mendoza</option>
<option value="America/Argentina/Rio_Gallegos">Argentina - Rio Gallegos</option>
<option value="America/Argentina/Salta">Argentina - Salta</option>
<option value="America/Argentina/San_Juan">Argentina - San Juan</option>
<option value="America/Argentina/San_Luis">Argentina - San Luis</option>
<option value="America/Argentina/Tucuman">Argentina - Tucuman</option>
<option value="America/Argentina/Ushuaia">Argentina - Ushuaia</option>
<option value="America/Aruba">Aruba</option>
<option value="America/Asuncion">Asuncion</option>
<option value="America/Atikokan">Atikokan</option>
<option value="America/Bahia">Bahia</option>
<option value="America/Bahia_Banderas">Bahia Banderas</option>
<option value="America/Barbados">Barbados</option>
<option value="America/Belem">Belem</option>
<option value="America/Belize">Belize</option>
<option value="America/Blanc-Sablon">Blanc-Sablon</option>
<option value="America/Boa_Vista">Boa Vista</option>
<option value="America/Bogota">Bogota</option>
<option value="America/Boise">Boise</option>
<option value="America/Cambridge_Bay">Cambridge Bay</option>
<option value="America/Campo_Grande">Campo Grande</option>
<option value="America/Cancun">Cancun</option>
<option value="America/Caracas">Caracas</option>
<option value="America/Cayenne">Cayenne</option>
<option value="America/Cayman">Cayman</option>
<option value="America/Chicago">Chicago</option>
<option value="America/Chihuahua">Chihuahua</option>
<option value="America/Costa_Rica">Costa Rica</option>
<option value="America/Creston">Creston</option>
<option value="America/Cuiaba">Cuiaba</option>
<option value="America/Curacao">Curacao</option>
<option value="America/Danmarkshavn">Danmarkshavn</option>
<option value="America/Dawson">Dawson</option>
<option value="America/Dawson_Creek">Dawson Creek</option>
<option value="America/Denver">Denver</option>
<option value="America/Detroit">Detroit</option>
<option value="America/Dominica">Dominica</option>
<option value="America/Edmonton">Edmonton</option>
<option value="America/Eirunepe">Eirunepe</option>
<option value="America/El_Salvador">El Salvador</option>
<option value="America/Fortaleza">Fortaleza</option>
<option value="America/Glace_Bay">Glace Bay</option>
<option value="America/Godthab">Godthab</option>
<option value="America/Goose_Bay">Goose Bay</option>
<option value="America/Grand_Turk">Grand Turk</option>
<option value="America/Grenada">Grenada</option>
<option value="America/Guadeloupe">Guadeloupe</option>
<option value="America/Guatemala">Guatemala</option>
<option value="America/Guayaquil">Guayaquil</option>
<option value="America/Guyana">Guyana</option>
<option value="America/Halifax">Halifax</option>
<option value="America/Havana">Havana</option>
<option value="America/Hermosillo">Hermosillo</option>
<option value="America/Indiana/Indianapolis">Indiana - Indianapolis</option>
<option value="America/Indiana/Knox">Indiana - Knox</option>
<option value="America/Indiana/Marengo">Indiana - Marengo</option>
<option value="America/Indiana/Petersburg">Indiana - Petersburg</option>
<option value="America/Indiana/Tell_City">Indiana - Tell City</option>
<option value="America/Indiana/Vevay">Indiana - Vevay</option>
<option value="America/Indiana/Vincennes">Indiana - Vincennes</option>
<option value="America/Indiana/Winamac">Indiana - Winamac</option>
<option value="America/Inuvik">Inuvik</option>
<option value="America/Iqaluit">Iqaluit</option>
<option value="America/Jamaica">Jamaica</option>
<option value="America/Juneau">Juneau</option>
<option value="America/Kentucky/Louisville">Kentucky - Louisville</option>
<option value="America/Kentucky/Monticello">Kentucky - Monticello</option>
<option value="America/Kralendijk">Kralendijk</option>
<option value="America/La_Paz">La Paz</option>
<option value="America/Lima">Lima</option>
<option value="America/Los_Angeles">Los Angeles</option>
<option value="America/Lower_Princes">Lower Princes</option>
<option value="America/Maceio">Maceio</option>
<option value="America/Managua">Managua</option>
<option value="America/Manaus">Manaus</option>
<option value="America/Marigot">Marigot</option>
<option value="America/Martinique">Martinique</option>
<option value="America/Matamoros">Matamoros</option>
<option value="America/Mazatlan">Mazatlan</option>
<option value="America/Menominee">Menominee</option>
<option value="America/Merida">Merida</option>
<option value="America/Metlakatla">Metlakatla</option>
<option value="America/Mexico_City">Mexico City</option>
<option value="America/Miquelon">Miquelon</option>
<option value="America/Moncton">Moncton</option>
<option value="America/Monterrey">Monterrey</option>
<option value="America/Montevideo">Montevideo</option>
<option value="America/Montreal">Montreal</option>
<option value="America/Montserrat">Montserrat</option>
<option value="America/Nassau">Nassau</option>
<option value="America/New_York">New York</option>
<option value="America/Nipigon">Nipigon</option>
<option value="America/Nome">Nome</option>
<option value="America/Noronha">Noronha</option>
<option value="America/North_Dakota/Beulah">North Dakota - Beulah</option>
<option value="America/North_Dakota/Center">North Dakota - Center</option>
<option value="America/North_Dakota/New_Salem">North Dakota - New Salem</option>
<option value="America/Ojinaga">Ojinaga</option>
<option value="America/Panama">Panama</option>
<option value="America/Pangnirtung">Pangnirtung</option>
<option value="America/Paramaribo">Paramaribo</option>
<option value="America/Phoenix">Phoenix</option>
<option value="America/Port-au-Prince">Port-au-Prince</option>
<option value="America/Port_of_Spain">Port of Spain</option>
<option value="America/Porto_Velho">Porto Velho</option>
<option value="America/Puerto_Rico">Puerto Rico</option>
<option value="America/Rainy_River">Rainy River</option>
<option value="America/Rankin_Inlet">Rankin Inlet</option>
<option value="America/Recife">Recife</option>
<option value="America/Regina">Regina</option>
<option value="America/Resolute">Resolute</option>
<option value="America/Rio_Branco">Rio Branco</option>
<option value="America/Santa_Isabel">Santa Isabel</option>
<option value="America/Santarem">Santarem</option>
<option value="America/Santiago">Santiago</option>
<option value="America/Santo_Domingo">Santo Domingo</option>
<option value="America/Sao_Paulo">Sao Paulo</option>
<option value="America/Scoresbysund">Scoresbysund</option>
<option value="America/Shiprock">Shiprock</option>
<option value="America/Sitka">Sitka</option>
<option value="America/St_Barthelemy">St Barthelemy</option>
<option value="America/St_Johns">St Johns</option>
<option value="America/St_Kitts">St Kitts</option>
<option value="America/St_Lucia">St Lucia</option>
<option value="America/St_Thomas">St Thomas</option>
<option value="America/St_Vincent">St Vincent</option>
<option value="America/Swift_Current">Swift Current</option>
<option value="America/Tegucigalpa">Tegucigalpa</option>
<option value="America/Thule">Thule</option>
<option value="America/Thunder_Bay">Thunder Bay</option>
<option value="America/Tijuana">Tijuana</option>
<option value="America/Toronto">Toronto</option>
<option value="America/Tortola">Tortola</option>
<option value="America/Vancouver">Vancouver</option>
<option value="America/Whitehorse">Whitehorse</option>
<option value="America/Winnipeg">Winnipeg</option>
<option value="America/Yakutat">Yakutat</option>
<option value="America/Yellowknife">Yellowknife</option>
</optgroup>
<optgroup label="Antarctica">
<option value="Antarctica/Casey">Casey</option>
<option value="Antarctica/Davis">Davis</option>
<option value="Antarctica/DumontDUrville">DumontDUrville</option>
<option value="Antarctica/Macquarie">Macquarie</option>
<option value="Antarctica/Mawson">Mawson</option>
<option value="Antarctica/McMurdo">McMurdo</option>
<option value="Antarctica/Palmer">Palmer</option>
<option value="Antarctica/Rothera">Rothera</option>
<option value="Antarctica/South_Pole">South Pole</option>
<option value="Antarctica/Syowa">Syowa</option>
<option value="Antarctica/Vostok">Vostok</option>
</optgroup>
<optgroup label="Arctic">
<option value="Arctic/Longyearbyen">Longyearbyen</option>
</optgroup>
<optgroup label="Asia">
<option value="Asia/Aden">Aden</option>
<option value="Asia/Almaty">Almaty</option>
<option value="Asia/Amman">Amman</option>
<option value="Asia/Anadyr">Anadyr</option>
<option value="Asia/Aqtau">Aqtau</option>
<option value="Asia/Aqtobe">Aqtobe</option>
<option value="Asia/Ashgabat">Ashgabat</option>
<option value="Asia/Baghdad">Baghdad</option>
<option value="Asia/Bahrain">Bahrain</option>
<option value="Asia/Baku">Baku</option>
<option value="Asia/Bangkok">Bangkok</option>
<option value="Asia/Beirut">Beirut</option>
<option value="Asia/Bishkek">Bishkek</option>
<option value="Asia/Brunei">Brunei</option>
<option value="Asia/Choibalsan">Choibalsan</option>
<option value="Asia/Chongqing">Chongqing</option>
<option value="Asia/Colombo">Colombo</option>
<option value="Asia/Damascus">Damascus</option>
<option value="Asia/Dhaka">Dhaka</option>
<option value="Asia/Dili">Dili</option>
<option value="Asia/Dubai">Dubai</option>
<option value="Asia/Dushanbe">Dushanbe</option>
<option value="Asia/Gaza">Gaza</option>
<option value="Asia/Harbin">Harbin</option>
<option value="Asia/Hebron">Hebron</option>
<option value="Asia/Ho_Chi_Minh">Ho Chi Minh</option>
<option value="Asia/Hong_Kong">Hong Kong</option>
<option value="Asia/Hovd">Hovd</option>
<option value="Asia/Irkutsk">Irkutsk</option>
<option value="Asia/Jakarta">Jakarta</option>
<option value="Asia/Jayapura">Jayapura</option>
<option value="Asia/Jerusalem">Jerusalem</option>
<option value="Asia/Kabul">Kabul</option>
<option value="Asia/Kamchatka">Kamchatka</option>
<option value="Asia/Karachi">Karachi</option>
<option value="Asia/Kashgar">Kashgar</option>
<option value="Asia/Kathmandu">Kathmandu</option>
<option value="Asia/Khandyga">Khandyga</option>
<option value="Asia/Kolkata">Kolkata</option>
<option value="Asia/Krasnoyarsk">Krasnoyarsk</option>
<option value="Asia/Kuala_Lumpur">Kuala Lumpur</option>
<option value="Asia/Kuching">Kuching</option>
<option value="Asia/Kuwait">Kuwait</option>
<option value="Asia/Macau">Macau</option>
<option value="Asia/Magadan">Magadan</option>
<option value="Asia/Makassar">Makassar</option>
<option value="Asia/Manila">Manila</option>
<option value="Asia/Muscat">Muscat</option>
<option value="Asia/Nicosia">Nicosia</option>
<option value="Asia/Novokuznetsk">Novokuznetsk</option>
<option value="Asia/Novosibirsk">Novosibirsk</option>
<option value="Asia/Omsk">Omsk</option>
<option value="Asia/Oral">Oral</option>
<option value="Asia/Phnom_Penh">Phnom Penh</option>
<option value="Asia/Pontianak">Pontianak</option>
<option value="Asia/Pyongyang">Pyongyang</option>
<option value="Asia/Qatar">Qatar</option>
<option value="Asia/Qyzylorda">Qyzylorda</option>
<option value="Asia/Rangoon">Rangoon</option>
<option value="Asia/Riyadh">Riyadh</option>
<option value="Asia/Sakhalin">Sakhalin</option>
<option value="Asia/Samarkand">Samarkand</option>
<option value="Asia/Seoul">Seoul</option>
<option value="Asia/Shanghai">Shanghai</option>
<option value="Asia/Singapore">Singapore</option>
<option value="Asia/Taipei">Taipei</option>
<option value="Asia/Tashkent">Tashkent</option>
<option value="Asia/Tbilisi">Tbilisi</option>
<option value="Asia/Tehran">Tehran</option>
<option value="Asia/Thimphu">Thimphu</option>
<option value="Asia/Tokyo">Tokyo</option>
<option value="Asia/Ulaanbaatar">Ulaanbaatar</option>
<option value="Asia/Urumqi">Urumqi</option>
<option value="Asia/Ust-Nera">Ust-Nera</option>
<option value="Asia/Vientiane">Vientiane</option>
<option value="Asia/Vladivostok">Vladivostok</option>
<option value="Asia/Yakutsk">Yakutsk</option>
<option value="Asia/Yekaterinburg">Yekaterinburg</option>
<option value="Asia/Yerevan">Yerevan</option>
</optgroup>
<optgroup label="Atlantic">
<option value="Atlantic/Azores">Azores</option>
<option value="Atlantic/Bermuda">Bermuda</option>
<option value="Atlantic/Canary">Canary</option>
<option value="Atlantic/Cape_Verde">Cape Verde</option>
<option value="Atlantic/Faroe">Faroe</option>
<option value="Atlantic/Madeira">Madeira</option>
<option value="Atlantic/Reykjavik">Reykjavik</option>
<option value="Atlantic/South_Georgia">South Georgia</option>
<option value="Atlantic/Stanley">Stanley</option>
<option value="Atlantic/St_Helena">St Helena</option>
</optgroup>
<optgroup label="Australia">
<option value="Australia/Adelaide">Adelaide</option>
<option value="Australia/Brisbane">Brisbane</option>
<option value="Australia/Broken_Hill">Broken Hill</option>
<option value="Australia/Currie">Currie</option>
<option value="Australia/Darwin">Darwin</option>
<option value="Australia/Eucla">Eucla</option>
<option value="Australia/Hobart">Hobart</option>
<option value="Australia/Lindeman">Lindeman</option>
<option value="Australia/Lord_Howe">Lord Howe</option>
<option value="Australia/Melbourne">Melbourne</option>
<option value="Australia/Perth">Perth</option>
<option value="Australia/Sydney">Sydney</option>
</optgroup>
<optgroup label="Europe">
<option value="Europe/Amsterdam">Amsterdam</option>
<option value="Europe/Andorra">Andorra</option>
<option value="Europe/Athens">Athens</option>
<option value="Europe/Belgrade">Belgrade</option>
<option value="Europe/Berlin">Berlin</option>
<option value="Europe/Bratislava">Bratislava</option>
<option value="Europe/Brussels">Brussels</option>
<option value="Europe/Bucharest">Bucharest</option>
<option value="Europe/Budapest">Budapest</option>
<option value="Europe/Busingen">Busingen</option>
<option value="Europe/Chisinau">Chisinau</option>
<option value="Europe/Copenhagen">Copenhagen</option>
<option value="Europe/Dublin">Dublin</option>
<option value="Europe/Gibraltar">Gibraltar</option>
<option value="Europe/Guernsey">Guernsey</option>
<option value="Europe/Helsinki">Helsinki</option>
<option value="Europe/Isle_of_Man">Isle of Man</option>
<option value="Europe/Istanbul">Istanbul</option>
<option value="Europe/Jersey">Jersey</option>
<option value="Europe/Kaliningrad">Kaliningrad</option>
<option value="Europe/Kiev">Kiev</option>
<option value="Europe/Lisbon">Lisbon</option>
<option value="Europe/Ljubljana">Ljubljana</option>
<option value="Europe/London">London</option>
<option value="Europe/Luxembourg">Luxembourg</option>
<option value="Europe/Madrid">Madrid</option>
<option value="Europe/Malta">Malta</option>
<option value="Europe/Mariehamn">Mariehamn</option>
<option value="Europe/Minsk">Minsk</option>
<option value="Europe/Monaco">Monaco</option>
<option value="Europe/Moscow">Moscow</option>
<option value="Europe/Oslo">Oslo</option>
<option value="Europe/Paris">Paris</option>
<option value="Europe/Podgorica">Podgorica</option>
<option value="Europe/Prague">Prague</option>
<option value="Europe/Riga">Riga</option>
<option value="Europe/Rome">Rome</option>
<option value="Europe/Samara">Samara</option>
<option value="Europe/San_Marino">San Marino</option>
<option value="Europe/Sarajevo">Sarajevo</option>
<option value="Europe/Simferopol">Simferopol</option>
<option value="Europe/Skopje">Skopje</option>
<option value="Europe/Sofia">Sofia</option>
<option value="Europe/Stockholm">Stockholm</option>
<option value="Europe/Tallinn">Tallinn</option>
<option value="Europe/Tirane">Tirane</option>
<option value="Europe/Uzhgorod">Uzhgorod</option>
<option value="Europe/Vaduz">Vaduz</option>
<option value="Europe/Vatican">Vatican</option>
<option value="Europe/Vienna">Vienna</option>
<option value="Europe/Vilnius">Vilnius</option>
<option value="Europe/Volgograd">Volgograd</option>
<option value="Europe/Warsaw">Warsaw</option>
<option value="Europe/Zagreb">Zagreb</option>
<option value="Europe/Zaporozhye">Zaporozhye</option>
<option value="Europe/Zurich">Zurich</option>
</optgroup>
<optgroup label="Indian">
<option value="Indian/Antananarivo">Antananarivo</option>
<option value="Indian/Chagos">Chagos</option>
<option value="Indian/Christmas">Christmas</option>
<option value="Indian/Cocos">Cocos</option>
<option value="Indian/Comoro">Comoro</option>
<option value="Indian/Kerguelen">Kerguelen</option>
<option value="Indian/Mahe">Mahe</option>
<option value="Indian/Maldives">Maldives</option>
<option value="Indian/Mauritius">Mauritius</option>
<option value="Indian/Mayotte">Mayotte</option>
<option value="Indian/Reunion">Reunion</option>
</optgroup>
<optgroup label="Pacific">
<option value="Pacific/Apia">Apia</option>
<option value="Pacific/Auckland">Auckland</option>
<option value="Pacific/Chatham">Chatham</option>
<option value="Pacific/Chuuk">Chuuk</option>
<option value="Pacific/Easter">Easter</option>
<option value="Pacific/Efate">Efate</option>
<option value="Pacific/Enderbury">Enderbury</option>
<option value="Pacific/Fakaofo">Fakaofo</option>
<option value="Pacific/Fiji">Fiji</option>
<option value="Pacific/Funafuti">Funafuti</option>
<option value="Pacific/Galapagos">Galapagos</option>
<option value="Pacific/Gambier">Gambier</option>
<option value="Pacific/Guadalcanal">Guadalcanal</option>
<option value="Pacific/Guam">Guam</option>
<option value="Pacific/Honolulu">Honolulu</option>
<option value="Pacific/Johnston">Johnston</option>
<option value="Pacific/Kiritimati">Kiritimati</option>
<option value="Pacific/Kosrae">Kosrae</option>
<option value="Pacific/Kwajalein">Kwajalein</option>
<option value="Pacific/Majuro">Majuro</option>
<option value="Pacific/Marquesas">Marquesas</option>
<option value="Pacific/Midway">Midway</option>
<option value="Pacific/Nauru">Nauru</option>
<option value="Pacific/Niue">Niue</option>
<option value="Pacific/Norfolk">Norfolk</option>
<option value="Pacific/Noumea">Noumea</option>
<option value="Pacific/Pago_Pago">Pago Pago</option>
<option value="Pacific/Palau">Palau</option>
<option value="Pacific/Pitcairn">Pitcairn</option>
<option value="Pacific/Pohnpei">Pohnpei</option>
<option value="Pacific/Port_Moresby">Port Moresby</option>
<option value="Pacific/Rarotonga">Rarotonga</option>
<option value="Pacific/Saipan">Saipan</option>
<option value="Pacific/Tahiti">Tahiti</option>
<option value="Pacific/Tarawa">Tarawa</option>
<option value="Pacific/Tongatapu">Tongatapu</option>
<option value="Pacific/Wake">Wake</option>
<option value="Pacific/Wallis">Wallis</option>
</optgroup>
</select>

<input type="hidden" id="timezone_string_cs2" value="timezone" />

								<div id="imghoveraadddays">
									<div><img width="25" height="13" src="<?php bloginfo('template_url'); ?>/images/hoverimg_up.png" alt="" /></div>
									<div id="textme8drip"><p>Set your Timezone</p></div>
								</div>
							</div>

                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="drips_con" id="bucket">
                <div class="containforfix">
					<h2>Bucket</h2>
					<div class="body">
					<div id="adjust_preview">
						<div class="today_preview_div">
							<div class="tile_xfeat_img_div adjust_topic_feat_img_div">
								<div id="frontflip">
									<img width="385" height="1" src="<?php bloginfo('template_url'); ?>/images/trans.png" alt=""/>
								</div>
								<div class="backx_flip" style="display:none">
									<div class="backflip">
										<img width="385" height="1" src="<?php bloginfo('template_url'); ?>/images/trans.png" alt="">
									</div>
								</div>
							</div>
							<div class="tile_low">
								<div class="topic_prof_det_div adjust_topic_prof_det_div">
									<span class="channel adjust_text">Channel</span>
									<span class="topic adjust_text">Topic</span>
									<span class="adjust_text adjust_text_title">
										<!--input type="text" id="drip_title" maxlength="200"-->
										<textarea id="drip_title" maxlength="200" rows="1" cols="44" placeholder="Title"></textarea>
										<span class="expand_input"><i class="expand_inputs-down-s"></i></span>
									</span>
								</div>
								<div class="hori_divider"></div>
								<div class="tile_op_div_inner adjust_drip_analysis">
									<div class="analysistab">
										<span class="GenericSlabBold adjust_analysistext">Analysis</span>
									</div>
									<span class=" adjust_text the_adjust">
										<textarea class="GenericSlabBold" id="the_adjust" maxlength="700" cols="40" rows="3" placeholder="Analysis"></textarea>
									</span>
									<span class="expand_input"><i class="expand_inputs-down-s"></i></span>
								</div>
								<div class="hori_divider"></div>
								<span class="adjust_text adjust_text_ext the_content">
									<textarea id="the_content" maxlength="2500" rows="3" cols="45" placeholder="Content Body"></textarea>
									<span class="expand_input"><i class="expand_inputs-down-s"></i></span>
								</span>
								<input type="hidden" id="post_id" />
								<input type="hidden" id="is_history" />
							</div>
							<ul id="drip_tags">
							</ul>
						</div>
						<span class="btn2 preview_btn" id="update_drip">Update</span>
						<span class="btn2 preview_btn redrip_this">Redrip</span>
					</div>
					<div class="sepa_vert adjust_sepa"></div>
					<div id="adjust_sched">
						<div id="history_drips"></div>
						<div id="future_drips"></div>
					</div>
					</div>
				</div>
            </div>
			<div class="drips_con" id="publish">
				<div class="containforfix">
					<h2>Publish</h2>
					<div class="body">
						
					</div>
				</div>
			</div>
			<div class="drips_con" id="ripple">
				<div class="containforfix">
					<h2>Ripple</h2>
					<div class="body">
						<div class="ripple_col" id="activated">
							<span class="buttons2 ripple_head">Activated</span>
                            <div class="scroll_cont">
                                <div class="items_cont">
                                    <div class="items"></div>
                                </div>
							</div>
						</div>
						<div class="ripple_col" id="linkedin">
							<span class="buttons2 ripple_head">LinkedIn <span id="update_linkedin_list">Update</span></span>
                            <div class="scroll_cont">
                                <div class="items_cont">
                                    <div class="items li"></div>
                                </div>
							</div>
						</div>
						<div class="ripple_col" id="othersocials">
							<span class="buttons2 ripple_head">Other Socials</span>
                            <div class="scroll_cont">
                                <div class="items_cont others">
                                    <div class="items others"></div>
                                </div>
							</div>
						</div>
						<div class="ripple_col" id="summary">
							<iframe id="LP_soc_Oauth_iframe" name="LP_soc_Oauth_iframe"></iframe>
						</div>
					</div>
				</div>
			</div>
			<div class="drips_con" id="channel">
				<div class="containforfix DRIPdivcrtacc_overflown">
					<h2>Channel</h2>
					<div class="body">
						<ul id="chansort">
						<?php 
						global $LP_channel_settings;
						foreach($LP_channel_settings as $key => $channel):
							$active = "";
							$ufollow = "+ Follow";
							if($channel["active"] == 1){
								$active = "active";
								$ufollow = "- Unfollow";
							}
						?>						
						<li class="chan_lis <?php echo $active;?>" param="<?php echo $key;?>">
							<?php
								$short_key = $key;
								if(strlen($key)> 12){
								$short_key = substr($key,0,11)."...";
							?>
							<ul>
								<li class="chan_lis child" param="ideas">
									<i class="bluewhitedoubledrip-d-30"></i>
									<span class="chan_spans"><?php echo $key;?></span>
									<span class="UFollow" title="higher education">+ Follow</span>
								</li>
							</ul>
							<?php }?>
							
							<i class="bluewhitedoubledrip-d-30"></i>
							<span class="chan_spans"><?php echo $short_key;?></span>
							<span class="UFollow"><?php echo $ufollow;?></span>
						</li>    
						<?php endforeach; ?>
					</ul>
					</div>
				</div>
			</div>
        </div>
        <!-- div class="containom">
            <div class="arrowbot arrowext ico_class_sm_bar"></div>
        </div -->
        <!-- Latest Post -->	
    </div>
    <?php global $user_identity;?>
    <div class="managetopicsdiv">
        <div class="post_holder_addtopic" id="add_topic_cont">
            <div class="addtopic_feat_img_div">
                <span class="feat_img_info">Drop image here to upload.</span>
                <img id="LP_feat_img_ul" src="<?php bloginfo('template_url'); ?>/images/trans.png" alt=""/>
                <form id="LP_add_topic_uploader" name="LP_add_topic_uploader" method="post" action="<?php echo $LP_siteurl; ?>/lp_add_topic_thumb" target="LP_topic_uploader_iframe" enctype="multipart/form-data">
                    <input class="addtopic_feat_img" id="addtopic_feat_img" name="topic_file" type="file"/>
                    <input type="hidden" id="session_name" name="session_name" value="<?php echo "temp_file_".rand(10,100000)?>" />
                </form>
                <iframe id="LP_topic_uploader_iframe" name="LP_topic_uploader_iframe" style="display:none;"></iframe>
                <!-- <img src="<?php bloginfo('template_url'); ?>/images/post1.jpg"/> -->
            </div>
            <div class="tile_prof_img_div addtopic_prof_img_div">
                <?php echo $author_avatar;?>
            </div>
            <div class="topic_prof_det_div">
                <span class="topic_pname"><a><?php echo $user_identity;?></a></span>
                <div class="custom_select" id="LP_channels_select">
                    <div></div>
                    <select class="new_topic_channel addtopic_sel" id="new_topic_channel">
                        <?php $channels = LP_get_channels();?>
                        <?php foreach($channels as $channel):?>
                        <option value="<?php echo $channel["id"]?>"><?php echo $channel["name"]?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <input type="text" class="new_topic_title" placeholder="Title" id="new_topic_title">
            <textarea  class="new_topic_description" placeholder="Short description" id="new_topic_description" maxlength="250"></textarea>
            <span class="save_add_topic" id="save_add_topic">Next</span>
        </div>
    </div>
    
    <div id="manage_message_template">
        <div class="containforfix">
            <h2>Linkedin Message</h2>
            <?php if(!LP_blog_has_messaging()){ $no_messaging = "no_messaging"?>
                <div><span class="buttons2" id="grab_linkedin_connections">Grab all LinkedIn connections</span></div>
            <?php }?>

            <div class="body <?php echo $no_messaging;?>">
                <div id="mright">
                    <div id="messages_table_cont" class="drippleScrollbar">
                        <table class="messages_table">
                            <thead>
                            <tr>
                                <th style="width:55px;"><!-- span id="sel_all">All</span> / --><span id="sel_none">None</span></th>
                                <th style="width:100px;">First Name</th>
                                <th style="width:125px;">Last Name</th>
                                <th>Industry</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div id="pagination"></div>
                </div>
                <div id="msg_form_cont">
                    <div id="linkedin_message_forms">
                        <label>Subject:</label>
                        <input type="text" id="message_subject" style="height:25px;">
                        <label>Body:</label>
                        <textarea id="message_body"></textarea>
                        <!-- span class="buttons2" id="update_linkedin_message">Update</span -->
                        <span class="buttons2" id="accept_linkedin_connections">Accept</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    <!--<div id="whiteclosediv">
        <span id="closedivfld">X</span>
    </div>-->	
</div>
<?php if(is_user_logged_in()){?>
<div id="t_splash_topic_tabs" class="tab_bar_drag">
	<div class="drag_cont"></div>
	<div class="splash_colors">
		<span param="#8B8C73"></span>
		<span param="#877762"></span>
		<span param="#7B9CA3"></span>
		<span param="#7F8080"></span>
	</div>
	<ul class="topic_tab_group"></ul>
	<i style="position: absolute;right: 15px;top: 30px;" class="close_splash-20 unlock"></i>
</div>
<?php } ?>