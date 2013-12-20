<?php
global $current_site;
$LP_siteurl = "http://".$current_site->domain;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
    <?php wp_head(); ?>
		<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/css/linkedpost.css"/>
		<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/css/ico.css"/>
		<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/css/content.css"/>
        <link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/css/views.css"/>
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
<div id="mainDiv" class="dark">
	<div id="headDIV">
		<div class="DIVcontainer">
			<div class="logindivusersindi">
				<ul>
                <?php if(is_user_logged_in()){
                global $user_identity;
                get_currentuserinfo();
                ?>
                <li class="LP_logout"><?php echo $user_identity;?><!--&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="/llogin/?rtype=logout">Logout</a></li> -->
                <?php } ?>
				</ul>
			</div>
		</div>
		<div class="DRIPbtn_head">
			<a class="dp_logo_a" href="<?php echo $LP_siteurl; ?>"><img src="<?php bloginfo('template_url');?>/images/logo-dp-sm.png" alt=""></a>
			<?php if(is_user_logged_in()){?>
				<div class="DRIPbtn_head_div">
					<ul>
						<li class="liDRIPbtn">
							<div class="contntwrkiconhead">
								<p class="contbtndrip active">in</p>
								<div class="imgDRIPicon"></div>
							</div>
							<div class="btnntwrkicon">
								<div class="txtwrkicon"><p>Click to activate In</p></div>
								<div class="imgwrkicon"><img src="<?php bloginfo('template_url');?>/images/hoverimg_down.png"></div>
							</div>
						</li>
						<li class="liDRIPbtn">
							<div class="contntwrkiconhead">
								<p class="contbtndrip">f</p>
								<div class="imgDRIPicon"></div>
							</div>
							<div class="btnntwrkicon">
								<div class="txtwrkicon"><p>Click to activate Facebook</p></div>
								<div class="imgwrkicon"><img src="<?php bloginfo('template_url');?>/images/hoverimg_down.png"></div>
							</div>
						</li>
						<li class="liDRIPbtn">
							<div class="contntwrkiconhead">
								<p class="contbtndrip">tw</p>
								<div class="imgDRIPicon"></div>
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
							<div class="imgDRIPicon"></div>
						</li>
						<li class="liDRIPbtn">
								<p class="contbtndrip" id="drip_meter">meter</p>
								<div class="imgDRIPicon"></div>
						</li>
						<li class="liDRIPbtn">
							<p class="contbtndrip" id="drip_adjust">adjust</p>
							<div class="imgDRIPicon"></div>
						</li>
						<li class="liDRIPbtn">
							<p class="contbtndrip" id="drip_analyze">analyze</p>
							<div class="imgDRIPicon"></div>
						</li>
						<li class="liDRIPbtn">
							<p class="contbtndrip lidripnewright" id="chanclick">channel</p>
							<div class="imgDRIPicon"></div>
						</li>
					</ul>
				</div>
				<?php }?>
				<div class="btniconDRIPhed">
					<ul>
						<!-- <li class="iconheadli"><div id="chckdwn"></div></li> -->
						<?php if(is_user_logged_in()){?>
							<li class="iconheadli">
								<div class="hovrrytDIV">
									<p>Toggle the days to drip</p>
									<img src="<?php bloginfo('template_url'); ?>/images/hoverimg_down.png">
								</div>
								<a href="/llogin/?rtype=logout"><div id="proiconout" class="proiconout shown"></div></a>
							</li>
						<?php }else{?>
							<li class="iconheadli">
								<div class="hovrrytDIV">
									<p>Toggle the days to drip</p>
									<img src="<?php bloginfo('template_url'); ?>/images/hoverimg_down.png">
								</div>
								<div id="proiconin" class="proiconin slide_register_button shown"></div>
							</li>
						<?php }?>
						<li class="iconheadli"><i id="srchicon" class="srchicon shown"></i></li>
						<li class="iconheadli"><i id="gearbtrn" class="gearbtrn"></i></li>
						<li class="iconheadli"><i id="qmark" class="qmark"></i></li>
						<?php if(is_home()){?>
							<li class="iconheadli"><i id="toggle_default_view" param="default_view_cont" class="defaultv shown active"></i></li> 
							<li class="iconheadli"><i id="toggle_list_view" param="list_view_cont" class="listv shown"></i></li> 
							<li class="iconheadli"><i id="toggle_list_view" param="tile_view_cont" class="tilev shown"></i></li> 
							<li class="iconheadli"><i id="mashv" class="mashv shown"></i></li> 
							<li class="iconheadli"><i id="tzone" class="tzone"></i></li> 
						<?php }?>  
						<?php if(is_user_logged_in()){?>
                        <li class="iconheadli">
							<div class="hovrrytDIV">
								<p>Toggle the days to drip</p>
								<img src="<?php bloginfo('template_url'); ?>/images/hoverimg_down.png">
							</div>
							<i id="tcreate" class="tcreate add_topic_btn shown"></i>
						</li>
                        <?php }?>                        
					</ul>
				</div>
				<div class="type_city">
					<input type="text" placeholder="type a city"/>
					<span>local time is currently xxxx</span>
				</div>
			</div>
	</div>
	<?php get_template_part( "header", "forms" );?>
	<div id="content" class="largeframe">
		<div id="contenrBODY">