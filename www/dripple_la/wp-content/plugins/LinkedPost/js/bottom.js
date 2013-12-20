function LP_set_drip_zone(){
	var story_URL = jQuery(".story_URL",this).attr("href");
	var title = jQuery(".title",this).text();
	var content = jQuery(".content",this).text();
	
	jQuery(".redrip_form .inblogpostbody #ripple_title").val(title);
	jQuery(".redrip_form .inblogpostbody #ripple_content").val(content);
	jQuery(".redrip_form .inblogpostbody #story_URL").val(story_URL);
}

function LP_feeds_suggestions(topic_id){
	jQuery(".redrip_form #b_splash_rss_suggestions").empty();
	var setup = LP_get_topic_collection_setup(topic_id);
	var rss_feed_links = setup.rss_feed_links;
	if(rss_feed_links.length>0){
		jQuery.each(rss_feed_links, function(i,val){
			var feed = new google.feeds.Feed(val.link);
			feed.setNumEntries(10);
			feed.load(LP_bsplash_feed_loaded);
		});
	}
}

function LP_bsplash_feed_loaded(result){
	if (!result.error) {
		var the_item = "";
		for (var i = 0; i < result.feed.entries.length; i++) {
			var entry = result.feed.entries[i];
			var item = LP_prepare_rss_item(entry);
			the_item+= LP_news_res_tpl1(item);
		}
		
		jQuery(".redrip_form #b_splash_rss_suggestions").append("<div class=\"search_section_title\">"+the_item+"</div>");
		jQuery(".redrip_form #b_splash_rss_suggestions").mCustomScrollbar("destroy");
		jQuery(".redrip_form #b_splash_rss_suggestions").mCustomScrollbar({
			theme:"dark-thin",
			mouseWheelPixels: 200
		});
	}
}