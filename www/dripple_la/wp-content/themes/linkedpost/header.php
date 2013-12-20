<?php
global $current_site;
// $LP_siteurl = "http://".$current_site->domain;
$LP_siteurl = trim(network_site_url(),"/");
?>
<!DOCTYPE html>
<html style="margin-top:0!important;">
	<head>
		<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
		<title>Dripple</title>    
		<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/css/linkedpost.css"/>
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<?php wp_head(); ?>
        <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.mousewheel.js"></script>
         <?php if(!is_user_logged_in()){?>
		<script type="text/javascript" src="http://platform.linkedin.com/in.js">
			api_key: 7526oyd3xlrpgh
			onLoad: onLinkedInLoad
			authorize: true
			credentials_cookie: true
		</script>
        <script>
			var should_login = false;
            function onLinkedInLoad(){
				IN.Event.on(IN, "auth", onLinkedInAuth);
				if(IN.User.isAuthorized()){
					should_login = false;
					jQuery("#lin_JSAPI_login").css("display","none");
					jQuery("#lin_auto_login").show();
					jQuery("#lin_auto_login").live("click",LP_lin_auto_login);
				}else{
					should_login = true;
				}
            }

            function onLinkedInAuth() {
				IN.API.Profile("me").result(LP_IN_logged_user);
				if(should_login){
					LP_lin_auto_login();
				}
            }

            function LP_IN_logged_user(profile){
                console.log(profile);
            }
			
			function LP_lin_auto_login(){
				if(!is_forms){
					LP_console("loging in...");
					localStorage.clear();
					jQuery.post('<?php echo $LP_siteurl."/testing";?>','',function(r){
						console.log(r);
						var res = JSON.parse(r);
						if(res.hasOwnProperty("linkedin_callback_redirect")){
							window.location = res.linkedin_callback_redirect;
						}
					});
				}
			}
			<?php } ?>
        </script>
	</head>	
<body>
<div class="bottom_drag_containment"></div>
<div class="header_drag_containment2" style="position:fixed;top:75px;width:1px;height:calc(100% - 150px);"></div>
<div class="header_drag_containment">
		<div id="t_splash_cont">
			<div id="headDIV">
				<div class="DIVcontainer">
					<div class="logindivusersindi">
						<ul>
						<?php if(is_user_logged_in()){
							global $user_identity, $user_ID;
							get_currentuserinfo();
							// echo "user_ID : ".$user_ID;
							?>
							<li class="LP_logout"><a href="<?php echo LP_get_user_url($user_ID);?>"><?php echo $user_identity;?></a></li>
						<?php }else{ ?>
							<li><span class="slide_register_button" style="cursor:pointer;">Sign In</span></li>
						<?php }?>
						</ul>
					</div>
				</div>
				<div class="DRIPbtn_head">
					<a class="dp_logo_a" href="<?php echo $LP_siteurl; ?>"><img width="92" height="58" src="<?php bloginfo('template_url');?>/images/logo-dp-sm.png" alt=""></a>
						<div class="btniconDRIPhed">
							<ul>
								<!-- <li class="iconheadli"><div id="chckdwn"></div></li> -->
								<?php if(is_user_logged_in()){?>
									<li class="iconheadli">
										<a href="/llogin/?rtype=logout" onclick="FB.logout();"><div id="proiconout" class="proiconout shown"></div></a>
									</li>
								<?php }else{?>
									<li class="iconheadli">
										<div id="proiconin" class="proiconin slide_register_button shown"></div>
									</li>
								<?php }?>
								<li class="iconheadli"><i id="srchicon" class="srchicon shown"></i></li>
								<li class="iconheadli"><i id="gearbtrn" class="gearbtrn"></i></li>
								<li class="iconheadli"><i id="qmark" class="qmark"></i></li>
								<?php 
									$ishome = "";
									if(is_home()){
										$ishome = "home";
									}
									
									$which_view = $_SESSION["home_view"];
									$default = "";
									$list = "";
									$tile = "";
									if($which_view == "default" || $which_view == "") $default = "active";
									elseif($which_view == "list") $list = "active";
									elseif($which_view == "tile") $tile = "active";
								?>
									<li class="iconheadli view_types"><i id="toggle_default_view" class="defaultv shown <?php echo $ishome;?> <?php echo $default;?>"></i></li> 
									<li class="iconheadli view_types"><i id="toggle_list_view" class="listv shown <?php echo $ishome;?> <?php echo $list;?>"></i></li> 
									<li class="iconheadli view_types"><i id="toggle_tile_view" class="tilev shown <?php echo $ishome;?> <?php echo $tile;?>"></i></li> 
									<li class="iconheadli view_types"><i id="mashv" class="mashv shown <?php echo $ishome;?>"></i></li> 
									<!-- li class="iconheadli"><i id="tzone" class="tzone"></i></li --> 
								<?php if(is_user_logged_in()){?>
									<!--i class="iconheadli topic_functions"><i id="tcreate" class="tcreate add_topic_btn shown"></i></li-->
									<li class="iconheadli topic_functions"><i class="envelop-d-18 manage_linkedin_message"></i></li>
									<li class="iconheadli"><i class="blueredrip-l-21 add_fresh_drip incomplete"></i></li>
								<?php }?>                        
							</ul>
						</div>
						<div class="type_city">
							<input type="text" placeholder="type a city"/>
							<span>local time is currently</span>
						</div>
					<?php if(is_user_logged_in()){?>
						<div class="DRIPbtn_head_div">
							<ul id="drip_nav">
							</ul>
						</div>
					<?php }?>
					</div>
			</div>
			<?php get_template_part( "header", "forms" );?>
		</div>
	</div>
<div id="mainDiv">
	<div id="content">
		<div class="lnkrotateadz">
			<a href="#">Advertisement Link -</a> <span>Center Rotational Link</span>
		</div>
		<div id="contenrBODY">