<?php
get_header("dark");
global $story_URL, $current_site, $blog_id, $the_post_id;
// global $current_site;
$LP_siteurl = "http://".$current_site->domain;

// $postdata = get_blog_post($blog_id, $post_id);
$postdata = get_post( $the_post_id );
$authorID = $postdata->post_author;
$user = get_userdata($authorID);
?>	
</div>
<div class="tile_op_div_inner tile_op_div_innerext iframe_excerpt">
	<div class="analysistab" style="margin-top:25px;">
		<i class="analysis_ico"></i>
		<span class="analysistext GenericSlabBold">Analysis</span>
	</div>
	<span class="GenericSlabBold">
		<?php echo $postdata->post_excerpt;?>
	</span>
</div>
<div class="iframe_view_cont" style="height:100%;">
<div id="iframe_view">
    <div class="prev_iframe">
        <i class="dbl_arrow_left_ico"></i>
    </div>
    <div class="menu-iframe">			
		<div class="topics_head">
			<h2><?php echo $user->user_firstname; ?>'s Drip - <?php echo $postdata->post_title;?></h2>
		</div>
        <div class="menu_framed">
			<ul>
				<li>
					<i class="cls_iframe"></i>
				</li>
				<li>
					<i class="src_iframe"></i>
					<p>1.1k</p>
				</li>
				<li>
					<i class="like_iframe"></i>
				</li>
				<li>
					<i class="mail_iframe"></i>
				</li>
				<li>
					<i class="bookm_iframe"></i>
				</li>
			</ul>
		</div>
		<div class="iframe">
			<div class="frame_overlay"></div>
            <iframe src="<?php echo $story_URL;?>"></iframe>
        </div>
        <div class="ddripdiv iframe_redrip">
            <i class="redrip_blue_ico curson_pointer"></i>
        </div>
    </div>
	
    <div class="next_iframe">
        <i class="dbl_arrow_right_ico"></i>
    </div>
</div>
<?php
get_footer("dark");
?>