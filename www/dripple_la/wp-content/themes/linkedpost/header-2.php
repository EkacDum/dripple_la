<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
    global $switched;
    global $wpdb;
    $uri = explode("/",trim($_SERVER["REQUEST_URI"],"/"));
    $cloaked = $uri[1];
    $res = $wpdb->get_results("SELECT * FROM wp_post_iframe WHERE `id`=".intval($cloaked),ARRAY_A);    
?>
<html>
	<head>
            <?php wp_head(); ?>
		<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/css/reset.css"/>
		<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/css/linkedpost.css"/>
			<script src="<?php bloginfo( 'template_url' ); ?>/js/jQuery1.7.js" type="text/javascript"></script>
	<script src="<?php bloginfo( 'template_url' ); ?>/js/lnkedpst.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/css/content.css"/>
</head>	
<body>
<div id="mainDiv" class="main_add">
	
		<div id="headDIV">
			<div class="head2maindivwidth">
				<?php 
                switch_to_blog($res[0]["blog_id"]);
                query_posts("p=".$res[0]["post_id"]);
                if ( have_posts() ) while ( have_posts() ) : the_post();
				$to_story = get_post_meta(get_the_ID(),"story_URL",true);
				?>
				<div class="DIVcontainer divconthead2">
					<div id="imghead2divmain">
						<a href="<?php echo get_option('siteurl'); ?>"><img src="<?php bloginfo('template_url');?>/images/head_logo.png" alt=""></a>
						<div class="header_floater heade2titlesydnew"><a href="<?php the_permalink(); ?>"><?php echo get_the_title(); ?></a></div>
					</div>
					<div class="newhead_div">
						<ul>
							<li class="head2newimgsocnet"><?php echo do_shortcode('[dot_recommends]'); ?></li>
							<!-- <li class="head2newimgsocnet"><a href="#">Share</a></li> 
							<li class="head2newimgsocnet"><a href="#"><img class="mtw" src="<?php bloginfo('template_url');?>/images/minitwitter.png" alt=""></a></li>
							<li class="head2newimgsocnet"><a href="#"><img class="mfb" src="<?php bloginfo('template_url');?>/images/minifb.png" alt=""></a></li>
							<li class="head2newimgsocnet"><a href="#"><img class="mtw" src="<?php bloginfo('template_url');?>/images/minigoogle.png" alt=""></a></li>
							<li class="head2newimgsocnet"><a href="#"><img class="mtw" src="<?php bloginfo('template_url');?>/images/minidigg.png" alt=""></a></li>
							<li class="head2newimgsocnet"><a href="#"><img class="mtw" src="<?php bloginfo('template_url');?>/images/minili.png" alt=""></a></li>
							<li class="head2newimgsocnet"><a href="#"><img class="msu" src="<?php bloginfo('template_url');?>/images/minisu.png" alt=""></a></li> -->
							<li class="head2newimgsocnet"><a href="#"><?php echo do_shortcode('[wpsr_socialbts services="twitter,facebook,googleplus,digg,linkedin,stumbleupon"]');?></a></li>
						</ul>
					</div>
					<!--<a href="<?php echo $to_story; ?>"><div class="story_close">X</div></a>-->
				</div>
				<?php endwhile; ?>
				<div id="grpinddivlog" style="display: none;">
			<div class="signlogindivhead" style="display: block;">
				<h2>Get starting posting in less than 2 minutes</h2>
				<span class="colspandiv span01headfree">it's for free</span>
				<div>
					<div href="#" class="lndinimg linkedIn_register_button"><img src="http://sanyahaitun.com/wp-content/themes/LinkedPOST/images/signlinkedin.png"></div>
					<!--<span class="signupspan">Signup with LinkedIn button</span>-->
				</div>

				<img src="http://sanyahaitun.com/wp-content/themes/LinkedPOST/images/orimg.png" id="imgordivbrdr">
				<h3>Create your news blog here</h3>
				<form action="llogin/?rtype=register" method="post" id="wp_reg_form">
				<div class="grploginnput">
					<div class="frmdicnput">
						<ul>
							<li><input type="text" placeholder="Email" value="" id="uemail" name="email" class="inputbodyclass"></li>
							<li><input type="text" placeholder="First Name" value="" id="fname" name="fname" class="inputbodyclass"></li>
							<li><input type="text" placeholder="Last Name" value="" id="lname" name="lname" class="inputbodyclass"></li>
							<li><input type="password" placeholder="Password" value="" id="upass" name="upass" class="inputbodyclass"></li>
							<li><input type="password" placeholder="Password Again" value="" id="ucpass" name="ucpass" class="inputbodyclass"></li>
						</ul>
					</div>
					<div class="radiobtnsub">
						<span class="spantermsservices">This account is for:</span>
						<ul>	
							<li>
								<input type="radio" checked="" name="acctype" value="individual" id="ind">
								<label class="spantermsservices" for="ind">an individual(me)</label>
							</li>
							<li>
								<input type="radio" name="acctype" value="organizatioin" id="org">
								<label class="spantermsservices" for="org">an Organization</label>
							</li>
							<li id="chckbxli">
								<input type="checkbox" name="terms_agree" id="termsagree" value="Yes">
								<span class="spantermsservices"><label for="termsagree">I agree to LinkedPost</label> <a href="http://sanyahaitun.com/wp-content/themes/LinkedPOST/terms-of-service/" class="afnt">Terms of Service</a> and <a href="http://sanyahaitun.com/wp-content/themes/LinkedPOST/privacy-policy/" class="afnt">Privacy Policy</a></span>
							</li>
						</ul>
						<div id="btnsubmituser"></div>
					</div>
				</div>
				</form>
				<span class="spanmebrlog">Already a member? <a class="termadiv">Login</a></span>
			</div>
			<div class="createaccdiv" style="display: none;">
				<h2>Create your account</h2>
				<p class="connected">
					Connected to
					<span id="netwrksign">LinkedIn</span>
					as
					<strong id="namesigner"></strong>
				</p>
				<form action="linkedInP/?rtype=register" method="post" class="crtacc_inf" id="linkedin_reg_form">
					<ul class="error_msg">
					</ul>
					<div class="left_crtacc">
						<img alt="Profile avatar" src="http://sanyahaitun.com/wp-content/themes/LinkedPOST/images/def_ac.png" id="li_avatar">
					</div>
					<div class="right_crtacc">
						<input type="email" placeholder="Email" name="lemail" class="lemail">
						<input type="password" placeholder="Password" class="lpass" name="lpass">
						<input type="password" placeholder="Password Again" name="cpass" class="cpass">
						<p>
							By creating an account, I agree to LinkedPOST's
							<a href="http://sanyahaitun.com/wp-content/themes/LinkedPOST/terms-of-service/" class="afnt">
							<strong>Terms of Service</strong>
							</a>
							and
							<a href="http://sanyahaitun.com/wp-content/themes/LinkedPOST/privacy-policy/" class="afnt">
							<strong>Privacy Policy</strong>
							</a>
						</p>
						<div class="submitcont">
							<input type="button" value="" class="buttonblue linkedin_submit_reg">
							<span style="display: none;" class="spin"></span>
						</div>
					</div>
				</form>
			</div>
			<div class="signdiv" style="display: none;">
				<div class="signlog">
					<div href="#" class="lndinimg linkedIn_login_button">
						<img src="http://sanyahaitun.com/wp-content/themes/LinkedPOST/images/loglinkedin.png">
					</div>
				</div>
				<div class="form_conta">
					<form action="llogin/?rtype=login" method="post" class="formlogin" id="wp_log_form">
						<ul class="error_msg_login">
						</ul>
						<input type="email" placeholder="Email" name="wp_ulogin" id="wp_ulogin" value="" class="nputsygnfld">
						<input type="password" placeholder="Password" name="wp_upass" id="wp_upass" value="" class="nputsygnfld">
						<div class="signcont">
							<input type="button" value="" id="loginfldhead" class="buttonbluer">
							<span style="display: none;" class="spin"></span>
						</div>
					<a class="colspandiv span01headfree forg" href="http://sanyahaitun.com/wp-content/themes/LinkedPOST/wp-login.php?action=lostpassword">Forgot your password?</a>
					</form>
				</div>
				<span class="spanmebrsign">No account? <a class="termadiv">Signup</a></span>
			</div>
		</div>
			</div>
		</div>
		<div id="nwlnkadzjoindiv">
			<div id="joinfreedivnew" class="creatacctheadlog">
				<span>FREE TO JOIN</span>
			</div>
			<div class="lnkrotateadz2">
				<a href="#">Advertisemetn Link -</a> <span>Center Rotational Link</span>
			</div>
		</div>
</div>
