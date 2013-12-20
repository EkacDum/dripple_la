function LP_feeds_collections(){
	jQuery("#topic_collection_rss_setup .cols .results_news_tpl1").empty();
	var setup = LP_get_topic_collection_setup();
	var rss_feed_links = setup.rss_feed_links;
	jQuery.each(rss_feed_links, function(i,val){
		var feed = new google.feeds.Feed(val.link);
		feed.setNumEntries(10);
		feed.load(LP_feed_loaded);
	});
}

function LP_feed_loaded(result){
	if (!result.error) {
		var the_item = "";
		for (var i = 0; i < result.feed.entries.length; i++) {
			var entry = result.feed.entries[i];
			var item = LP_prepare_rss_item(entry);
			the_item+= LP_news_res_tpl1(item);
		}
		
		jQuery("#topic_collection_rss_setup .cols .results_news_tpl1").append("<div class=\"search_section_title\">"+the_item+"</div>");
		jQuery("#topic_collection_rss_setup .cols .results_news_tpl1").mCustomScrollbar("destroy");
		jQuery("#topic_collection_rss_setup .cols .results_news_tpl1").mCustomScrollbar({
			theme:"dark-thin",
			mouseWheelPixels: 200
		});
	}
}

function LP_set_rss_links(){
	jQuery("#topic_collection_rss_setup #selected_rss").empty();
	jQuery("#topic_collection_rss_setup #keywords").empty();
	jQuery("#topic_collection_rss_setup .topic_add_keyword").trigger("click");
	jQuery("#topic_collection_rss_setup .results_news_tpl1").empty();
	
	var setup = LP_get_topic_collection_setup();
	var rss_feed_links = setup.rss_feed_links;
	jQuery.each(rss_feed_links, function(i,val){
		var the_short	= "";
		if(val.display_domain.length <= 15) the_short = "short";
		var favico = "https://www.google.com/s2/favicons?domain="+val.display_domain;
		var the_swoosh = "<div class=\"item\" param=\""+val.link+"\">\
								<span>\
									<i class=\"greycross-d-21 rss-drag-ico the_move\"></i>\
									<span class=\""+the_short+"\">"+val.display_domain+"</span>\
								</span>\
								<div>\
									<i class=\"trash-d-18 the_trash\"></i>\
									<img src=\""+favico+"\">\
								</div>\
							</div>";
		jQuery("#topic_collection_rss_setup #selected_rss").append(the_swoosh);
	});
}

function LP_set_search_keywords(){
	jQuery("#topic_collection_setup #keywords").empty();
	var setup = LP_get_topic_collection_setup();
	var search_keywords = setup.search_keywords;
	if(search_keywords !==null){
		jQuery.each(search_keywords, function(i,val){
			if(i<=9){
				var the_key = "<div>\
								<span>\
									<i class=\"greycross-d-18 the_move\"></i>\
									<input class=\"topic_keyword\" type=\"text\" placeholder=\"keyword\" value=\""+val+"\"/>\
								</span>\
								<i class=\"trash-d-18 the_trash\"></i>\
							</div>";
				jQuery("#topic_collection_setup #keywords").append(the_key);
			}
		});
	}else{
		var the_key = "<div>\
							<span>\
								<i class=\"greycross-d-18 the_move\"></i>\
								<input class=\"topic_keyword\" type=\"text\" placeholder=\"keyword\"/>\
							</span>\
							<i class=\"trash-d-18 the_trash\"></i>\
						</div>";
		jQuery("#topic_collection_setup #keywords").append(the_key);
	}
}

function LP_set_search_source_button(){
	var setup = LP_get_topic_collection_setup();
	if(typeof setup == "object"){
		jQuery("#topic_collection_setup #search_source_cont").empty();
		var col = setup.search_sources;
		for (var key in col) {
			var val = col[key];
			// alert(key+" : "+val);
			var active = "";
			if(1 == val){
				active = "active";
			}
			switch (key){
				case "news":
					var tb = "<span>\
								<span class=\"blue_button "+active+"\" id=\"toggle_gnews_search\" param=\"news\">Google News<i class=\"greycross-d-21\"></i></span>\
							</span>";
					jQuery("#topic_collection_setup #search_source_cont").append(tb);
				break;
				
				case "blogs":
					var tb = "<span>\
								<span class=\"blue_button "+active+"\" id=\"toggle_gblog_search\" param=\"blogs\">Google Blogs<i class=\"greycross-d-21\"></i></span>\
							</span>";
					jQuery("#topic_collection_setup #search_source_cont").append(tb);
				break;
				
				case "twitter":
					var tb = "<span>\
								<span class=\"blue_button "+active+"\" id=\"toggle_twitter_search\" param=\"twitter\">Twitter Search<i class=\"greycross-d-21\"></i></span>\
							</span>";
					jQuery("#topic_collection_setup #search_source_cont").append(tb);
				break;
				
				case "dripple":
					var tb = "<span>\
								<span class=\"blue_button "+active+"\" id=\"toggle_dripple_search\" param=\"dripple\">Dripple Search<i class=\"greycross-d-21\"></i></span>\
							</span>";
					jQuery("#topic_collection_setup #search_source_cont").append(tb);
				break;
			}
		}
	}
}

function LP_remove_rss_from_list(){
	jQuery(this).parent().parent().remove();
	LP_save_topic_collection_setup();
}

function LP_remove_keyword_input(){
	var the_input = jQuery(this).parent();
	var num_kwords = jQuery(the_input).parent().find(">div").length;
	if(num_kwords==1){
		jQuery("input",the_input).val("");
	}else{
		jQuery(the_input).remove();
	}
	LP_save_topic_collection_setup();
}

function LP_add_keyword_input(){
	var the_keywords = jQuery(this).parent().parent().find("div#keywords");
	var num_kwords = jQuery("> div",the_keywords).length;
	if(num_kwords<=9){
		var the_key = "<div>\
							<span>\
								<i class=\"greycross-d-18 the_move\"></i>\
								<input class=\"topic_keyword\" type=\"text\" placeholder=\"keyword\"/>\
							</span>\
							<i class=\"trash-d-18 the_trash\"></i>\
						</div>";
		jQuery(the_keywords).append(the_key);
	}
}

var audioElement;
function LP_swooosh_rss(){
	var link = jQuery(this).attr("param");
	var obj 		= jQuery(this).parent().parent().parent();
	var the_img 	= jQuery("img",obj).attr("src");
	var the_domain	= jQuery(".info > span:eq(0)",obj).text();
	var the_short	= "";
	if(the_domain.length <= 15) the_short = "short";
	var the_swoosh = "<div class=\"item\" param=\""+link+"\">\
							<span>\
								<i class=\"greycross-d-21 rss-drag-ico the_move\"></i>\
								<span class=\""+the_short+"\">"+the_domain+"</span>\
							</span>\
							<div>\
								<i class=\"trash-d-18 the_trash\"></i>\
								<img src=\""+the_img+"\">\
							</div>\
						</div>";
	jQuery("#topic_collection_rss_setup #selected_rss").append(the_swoosh);
	audioElement.play();
	LP_save_topic_collection_setup();
}