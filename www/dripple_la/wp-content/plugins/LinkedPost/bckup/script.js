localStorage.clear();
var ajaxurl = linkedIn_AJAX.ajaxurl;
var user_nicename = linkedIn_AJAX.user_nicename;
var user_display_name = linkedIn_AJAX.user_display_name;
var is_forms = linkedIn_AJAX.is_forms;
var current_user = linkedIn_AJAX.current_user;
var linkedInWindow;
// var sub = "http://localhost/vardynamic/";
// var sub = "http://sanyahaitun.com/";
var sub = linkedIn_AJAX.site_url;
// var search_sources = linkedIn_AJAX.search_sources;
var drip_settings = linkedIn_AJAX.drip_settings;;

var select_hour = "";
var sel = "";

var LP_channel_settings;

var LP_user_topics;

var LP_debug = true;

for(var a=1; a<=12; a++){
    if(a<10){
        to_ = "0"+a;
    }else{
        to_ = a;
    }
    if(a==0){
        // sel = "selected=\"selected\"";
    }else{
        sel = "";
    }
    select_hour+="<option "+sel+" value=\""+to_+"\">"+to_+"</option>";
}

var select_mins = "";
var sel = "";
for(var a=0; a<60; a++){
    if(a<10){
        to_ = "0"+a;
    }else{
        to_ = a;
    }
    if(a==0){
        // sel = "selected=\"selected\"";
    }else{
        sel = "";
    }
    select_mins+="<option "+sel+" value=\""+to_+"\">"+to_+"</option>";
}


heavyImage = new Image(); 
heavyImage.src = sub+"wp-content/themes/linkedpost/images/drip_c.gif";
var d_days = new Array('monday','tuesday','wednesday','thursday','friday','saturday','sunday');

var drips;
var preloaded_images=[];
LP_preload_the_images();
jQuery("document").ready(function(){
    localStorage.setItem("current_user",current_user);
    // localStorage.getItem("current_user");
	set_drips_con_width();
	jQuery(".linkedIn_register_button").live("click",linkedin_register);
	jQuery(".slide_register_button").live("click",function(){
		jQuery(".signlogindivhead").animate({height:"toggle"},200);
	});
	jQuery(".linkedin_submit_reg").live("click",linkedin_submit_reg_form)
    jQuery(".linkedIn_login_button").live("click",linkedin_login);
    jQuery("#btnsubmituser").live("click",lwp_register);
    jQuery("#loginfldhead").live("click",lwp_login);
    jQuery("#btnsubmituser2").live("click",lwp_register_2); 
    jQuery("ul#drip_days > li").live("click",LP_toggle_day);
    jQuery(".litymhddrip_addimg").live("click",LP_add_drip);
    jQuery(".t_h, .t_m, .t_ampm").live("change",LP_save_topic_meter);
    
    jQuery("div.clockimgdrip").live("click",LP_remove_drip_time);
    
    jQuery("ul#drip_nav li").live("click",drip_slideDown_page);
	jQuery(".arrowprev, .arrownext").live("click",function(){
		drip_sliding_page(this);
		return false;
	});
    
    jQuery("#adjust_preview input#drip_title, #adjust_preview textarea#the_adjust, #adjust_preview textarea#the_content").live("focusout",LP_delay_to_save);
	
    jQuery(".edit_img").live("click",show_uploader_form);
    jQuery("#close_uploader_form").live("click",function(){
        jQuery(".LP_uploader_form").hide();
        jQuery("#LP_file").val("");
        jQuery("#LP_drip_id").val("");
    });
    
    jQuery("#LP_file").live("change",update_featured_image);
    //prepare_meter_form();
    // fetch_all_drips();
	
	jQuery("li.chan_lis i").live("mouseover",function(){
		animslide_ufollow(this);
	});
	jQuery("li.chan_lis i").live("mouseout",function(){
		animslout_ufollow(this);
	});
	
	jQuery(".followchandiv,.ufollowchandiv").live("click",LP_toggle_follow);
	
	jQuery(".arrowbot").live("click",function(){
		jQuery(".dripgrpDIV").stop().animate({"height" : 0},100,function(){
			jQuery("#t_splash_topic_tabs").hide();
			jQuery(".dripheadbtn").height(0);
		});
		jQuery(".arrowbot").removeClass("arrowbot").addClass("arrowdown");
	});
	
	jQuery(".arrowdown").live("click",function(){
		if(jQuery("ul#drip_nav li p.active").length > 0){
			jQuery("ul#drip_nav li p.active").parent().click();
		}else{
			jQuery("ul#drip_nav li:first").click();
		}
	})
	
	if(is_forms){
		jQuery("#save_add_topic").live("click",LP_submit_add_topic);
		jQuery("#addtopic_feat_img").live("change",LP_add_topic_uploader_submit);
		LP_get_user_topics(true);
		jQuery("#update_add_topic").live("click",LP_set_update_topic_form);
		jQuery(".update_topic_feat_img").live("change",LP_update_topic_uploader_submit);
	}
	
	LP_fetch_home_data();
    
    jQuery("i#toggle_list_view,i#toggle_default_view,i#toggle_list_view").live("click",toggle_home_view);	
    
    var who = "";
    jQuery(".comment_box").live("keydown",function(key){
        if(who == ""){
            who = key.which;
        }else{
			if( key.which == 13){
				return false;
			}
		}
    });
    
    jQuery(".comment_box").live("keyup",function(key){
        var dthis = jQuery(this);
        if(key.which == who){
            if(who == 13){
                // Submitting comment...
                var param   = jQuery(dthis).attr("param");
                var pb      = param.split("-");
                var comment = jQuery.trim(jQuery(dthis).val());
                if(pb[0]!="" && pb[1]!="" && comment!=""){
                    var data = {
                        action  : "LP_submit_comment",
                        post_id : pb[0],
                        blog_id : pb[1],
                        comment : comment
                    };
                    jQuery.post(ajaxurl,data,function(r){
                        // alert(r);
                        var comment_data = jQuery.parseJSON(r);
                        // alert("aaaa");
                        LP_append_comment(comment_data);
                    });
					jQuery(dthis).val("").blur();
                }
			return false;
            }
            who = "";
        }
        
    });
	
	/* *** THE FLIPPING *** */
	jQuery(".tile_feat_img_div, .imgblogpost").live("mouseenter",function(){
	   jQuery(this).parent().flippy({
			duration: "300",
			verso: jQuery(this).parent().find(".backx_flip").html()
		}); 
	}); 

	jQuery(".reverseflip_me").live("mouseout",function(){
		 jQuery(this).parent().flippyReverse();
	});
	
	jQuery("i.flip_me").live("click",function(){
		var obj = jQuery(this);
		var is_mommy = false;
		var stop = 10;
		while(!is_mommy){
			stop--;
			obj = jQuery(obj).parent();
			is_mommy = jQuery(obj).hasClass("flipbox-container");
			if(stop <= 0) return false;
		}
		// alert(jQuery(".back_flip",obj).attr("class"));
		// alert(stop);
		jQuery(".the_flipping",obj).flippy({
			duration: "300",
			verso: jQuery(".back_flip",obj).html(),
            onReverseFinish  : function(){
                // alert(jQuery(this).attr("class"));
                jQuery(this).removeAttr("style");
            }
		}); 
	}); 
	
	jQuery("i.reverse_me").live("click",function(){
		var obj = jQuery(this);
		var is_mommy = false;
		var stop = 10;
		while(!is_mommy){
			stop--;
			obj = jQuery(obj).parent();
			is_mommy = jQuery(obj).hasClass("flipbox-container");
			if(stop <= 0) return false;
		}
		jQuery(".the_flipping",obj).flippyReverse();
	});
	/* *** END OF THE FLIPPING *** */
	
	
	/* ****** DRIP FORM ***** */
	jQuery(".redrip_this").live("click",LP_redrip_this);
	jQuery(".close_redrip_form").live("click",LP_close_redrip_form);
	jQuery("#save_redrip").live("click",LP_save_redrip);
	jQuery("#save_buffer").live("click",LP_save_redrip);
	
	jQuery("input.redrip_tags").live("keyup",function(){
		var nsize = (jQuery(this).val()).length;
		if(nsize<3)nsize=3;
		jQuery(this).attr("size",nsize);
	});
	
	jQuery("input.redrip_tags").live("blur",function(){
		if(jQuery.trim(jQuery(this).val()) == ""){
			jQuery(this).removeClass("active");
		}
	});
	
	jQuery("input.redrip_tags").live("click",function(){
		if(jQuery(this).hasClass("active")){
			jQuery(this).removeClass("active").blur();
		}else{
			jQuery(this).addClass("active");
		}
		LP_suggest_images(false);
	});
	
	jQuery(".footersplash_industry .industryname").live("click",function(){
		var ndx =  jQuery(this).parent().index();
		LP_toggle_industry(ndx, 0);
	});
	jQuery("select#num_contacts_industry").live("change",function(){
		var limit = jQuery(this).val();
		var ndx =  jQuery(this).attr("param");
		LP_toggle_industry(ndx, limit);
	});
	
	jQuery("ul#image_suggestions img").live("click",LP_set_drip_image);
    
    jQuery(".redrip_form select#new_topic").live("change",function(){
        var topic_id = jQuery(this).val();
		localStorage.setItem("topics_in_collection_setup",topic_id);
		LP_set_active_tab();
		LP_reset_splash_screens();
		// LP_topic_rss_suggestions(topic_id, "LP_splash_bottom_rss_suggest");
    });
	
	jQuery(".redrip_form .unlock").live("click",LP_unlock_splash);
	
	jQuery(".add_fresh_drip").live("click",LP_fresh_drip_form);
	jQuery(".b_slpash_right div.item").live("hover",LP_set_drip_zone);
	
	/* ****** END OF DRIP FORM ***** */
	
	/* **** LinkedIn Message Form **** */
	jQuery("#update_linkedin_message").live("click",LP_update_linkedin_message);
	
	/* **** END LinkedIn Message Form **** */
	
	/* **** TOPIC COLLECTION SETUP **** */
	audioElement = document.createElement('audio');
	audioElement.setAttribute('src', 'http://www.freesfx.co.uk/rx2/mp3s/5/5471_1335188072.mp3');
	audioElement.addEventListener("load", function() {
			audioElement.play();
		}, true);
	
	jQuery( "#search_source_cont" ).sortable({
        placeholder: "search_sort_cont",
        forcePlaceholderSize :true,
        handle: 'i',
        update : function(event, ui){
					LP_save_topic_collection_setup();
				}
    });
	
	jQuery( "#search_keywords #keywords" ).sortable({
        placeholder: "keword_sort_cont",
        forcePlaceholderSize :true,
        handle: 'i.the_move',
		update : function(event, ui){
					LP_save_topic_collection_setup();
				}
    });
	
	jQuery( "#topic_collection_rss_setup #selected_rss" ).sortable({
        placeholder: "keword_sort_cont",
        forcePlaceholderSize :true,
        handle: 'i.the_move',
        update : function(event, ui){
					LP_save_topic_collection_setup();
				}
    });
	
    jQuery("#topic_keyword_preview").live("click",function(){
        jQuery("#topic_collection_setup .results_news_tpl1").empty();
        var topic_id = localStorage.getItem("topics_in_collection_setup");
		do_search(topic_id);
    });
    
    jQuery("#topic_collection_setup .topic_keyword").live("keypress",function(e){
		if(e.keyCode  ==  13){
			jQuery("#topic_collection_setup .results_news_tpl1").empty();
			var topic_id = localStorage.getItem("topics_in_collection_setup");
			do_search(topic_id);
		}
	});
    
	jQuery("#toggle_twitter_search").live("click",LP_user_has_twitter_token);
    jQuery("#toggle_gblog_search, #toggle_gnews_search, #toggle_dripple_search").live("click",LP_toggle_source_button);
	jQuery("#toggle_rss_search").live("click", LP_topic_rss_page);
	jQuery("#btn_back_to_search").live("click", LP_topic_setup_page);
	jQuery("#topic_rss_keyword_preview").live("click", function(){
		jQuery("#topic_collection_rss_setup .results_news_tpl1").empty();
		LP_topic_rss_keyword_preview();
	});
	
	jQuery("#topic_collection_rss_setup .topic_keyword").live("keypress",function(e){
		if(e.keyCode  ==  13){
			jQuery("#topic_collection_rss_setup .results_news_tpl1").empty();
			LP_topic_rss_keyword_preview();
		}
	});
	
	jQuery("#topic_collection_setup .topic_keyword").live("blur",LP_save_topic_collection_setup);
	jQuery("#topic_collection_rss_setup .swooosh_RSS").live("click",LP_swooosh_rss);
	jQuery(".topic_add_keyword").live("click",LP_add_keyword_input);
	jQuery("#keywords .the_trash").live("click",LP_remove_keyword_input);
	jQuery("#topic_collection_rss_setup #selected_rss .the_trash").live("click",LP_remove_rss_from_list);
	jQuery(".to_topic_setup").live("click",LP_goto_topic_setup);
    jQuery("#topic_feeds_preview").live("click",LP_feeds_collections);
	/* **** END TOPIC COLLECTION SETUP **** */
	
	/* TOPIC TAB */
	jQuery(".topic_tab_group li").live("click",function(){
		jQuery(".topic_tab_group li").removeClass("active");
		jQuery(this).addClass("active");
		jQuery(".topic_tabs").removeClass("active");
		var indx = parseInt(jQuery(this).text()) - 1;
		localStorage.setItem("current_tab_group",indx+1);
		jQuery(".topic_tabs").eq(indx).addClass("active");
		jQuery(".redrip_form .topic_tabs").eq(indx).addClass("active");
	});
	
	jQuery(".topic_tabs .topic_tab,.topic_tabs .tab_info").live("click",function(){
		jQuery("ul.topic_tabs li").removeClass("active");
		jQuery(this).parent().parent().addClass("active");
		var topic_id = jQuery(this).attr("param");
		LP_set_current_topic(topic_id);
		
		if(jQuery(".redrip_form").is(":visible")){
			LP_console("emptying...");
			jQuery("ul#image_suggestions").empty();
			LP_suggest_images(true);
		}
		
	});
	/* END TOPIC TAB */
	// LP_reset_splash_screens();
	
	/* ADJUST PAGE */
	jQuery("#adjust_sched .adjust_topics").live("hover",update_adjust_preview);
	/* END ADJUST PAGE */
});


/* ##################################################################################### */
/* ##################################################################################### */
/* ##################################################################################### */
/* ##################################################################################### */
/* ##################################################################################### */
/* ##################################################################################### */
/* ##################################################################################### */

function LP_set_drip_zone(){
	var story_URL = jQuery(".story_URL",this).attr("href");
	var title = jQuery(".title",this).text();
	var content = jQuery(".content",this).text();
	
	jQuery(".redrip_form .inblogpostbody #ripple_title").val(title);
	jQuery(".redrip_form .inblogpostbody #ripple_content").val(content);
	jQuery(".redrip_form .inblogpostbody #story_URL").val(story_URL);
}

function LP_set_active_tab(){
	var topic_id = localStorage.getItem("topics_in_collection_setup");
	jQuery("#t_splash_topic_tabs li").removeClass("active");
	jQuery("#t_splash_topic_tabs li div span.topic_tab:[param='"+topic_id+"']").parent().parent().addClass("active");
	jQuery(".redrip_form #t_splash_topic_tabs li").removeClass("active");
	jQuery(".redrip_form #t_splash_topic_tabs span.topic_tab:[param='"+topic_id+"']").parent().parent().addClass("active");
}

function LP_set_current_topic(topic_id){
	localStorage.setItem("topics_in_collection_setup",topic_id);
	LP_reset_splash_screens();
}

function LP_reset_splash_screens(){
	LP_init_topic_collection_forms();
	LP_bsplash_suggest();
	LP_reset_meter();
	LP_set_adjust_page();
}

function LP_populate_topic_tabs(){
	jQuery("#t_splash_cont #t_splash_topic_tabs ul.topic_tabs").empty();
	var user_topics = jQuery.parseJSON(localStorage.getItem("user_topics"));
	var t_count = user_topics.length;
	var u_topics = [];
	var a = 0;
	for (var ID in user_topics){
        var val = user_topics[ID];
		u_topics[a] = {};
		u_topics[a] = val;
		a++;
	}
	u_topics.sort(reverseSortTopic);
	var aa = 0;
	var groups = 1;
	jQuery("#t_splash_cont #t_splash_topic_tabs").append("<ul class=\"topic_tabs\"></ul>");
	jQuery.each(u_topics,function(i, v){
		var isactive = "";
		if(v.ID == localStorage.getItem("topics_in_collection_setup")){
			var isactive = "active";
		}
		var item = "<li class=\""+isactive+"\">\
						<div>\
							<span param=\""+v.ID+"\" class=\"topic_tab\">"+v.short_name+"\
								<span class=\"arrow_down\">&#9660;</span>\
								<span class=\"arrow_up\">&#9650;</span>\
							</span>\
							<div param=\""+v.ID+"\" class=\"tab_info\">\
								<span><i class=\"topic_drip_tab_"+(aa+1)+"\"></i>"+v.post_title+"</span>\
							</div>\
						</div>\
					</li>";
		jQuery("#t_splash_cont #t_splash_topic_tabs ul.topic_tabs:last").append(item);
		aa++;
		if(aa>=10 && i<49){
			jQuery("#t_splash_cont #t_splash_topic_tabs").append("<ul class=\"topic_tabs\"></ul>");
			aa = 0;
			groups++;
			if(groups >= 2){
				if(groups==2){
					jQuery("#t_splash_cont #t_splash_topic_tabs ul.topic_tab_group").prepend("<li param=\"1\">1</li>");
				}
				jQuery("#t_splash_cont #t_splash_topic_tabs ul.topic_tab_group").prepend("<li param=\""+groups+"\">"+groups+"</li>");
			}
		}
	});
}

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

function LP_prepare_rss_item(res){
    var domain = res.link;
    domain = domain.replace("www.","");
    domain = domain.replace("http://","");
    domain = domain.replace("https://","");
    var sdomain = domain.split("/");
    
    var item = {
        title			: res.title,
        src				: res.author,
        publishedDate 	: res.publishedDate,
		article_url		: res.link,
        domain			: sdomain[0],
        content			: res.contentSnippet,
		favico			: "https://www.google.com/s2/favicons?domain="+res.link
    };
    return item;
} 

function LP_goto_topic_setup(topic_id){
	if(typeof topic_id == "string" || typeof topic_id == "number"){
		// localStorage.setItem("topics_in_collection_setup",topic_id);
		LP_set_current_topic(topic_id);
	}else{
		var ch = false;
		var the_t = jQuery(this);
		var topic;
		while(ch===false){
			the_t = jQuery(the_t).parent();
			if(jQuery(the_t).hasClass("post_holder_addtopic")){
				topic = jQuery(the_t).attr("param");
				// localStorage.setItem("topics_in_collection_setup",topic);
				LP_set_current_topic(topic);
				ch = true;
			}
		}
	}
	jQuery(".managetopicsdiv").hide();
	LP_init_topic_collection_forms();
	jQuery("ul#drip_nav li:first").click();
}

var keywords_ready = false;
function LP_init_topic_collection_forms(){
	LP_set_search_source_button();
	LP_set_search_keywords();
	LP_set_rss_links();
	var topic_id = localStorage.getItem("topics_in_collection_setup");
	do_search(topic_id);
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

/* GLOBALS */
google.load("feeds", "1");
var the_rss_keywords = new Array();
function LP_topic_rss_keyword_preview(){
	jQuery("#topic_collection_rss_setup .topic_keyword").each(function(i){		
		var this_val = jQuery.trim(jQuery(this).val());
		if(this_val !="" ){
			var q = "\""+this_val+"\"";
			the_rss_keywords[q] = i;
			jQuery("#topic_collection_rss_setup .cols .results_news_tpl1").append("<div style=\"display:none;\" class=\"search_section_title\"><span>"+q+"</span></div>");
			google.feeds.findFeeds(q, LP_RSS_Search_Done);
		}
	});
}

function LP_RSS_Search_Done(results){
	if (!results.error) {
		jQuery.each(results.entries,function(i,val){
			var domain = val.url;
			domain = domain.replace("www.","");
			domain = domain.replace("http://","");
			domain = domain.replace("https://","");
			var sdomain = domain.split("/");
			item = {
				title	: val.title,
				src		: "RSS Feeds",
				domain  : sdomain[0],
				url		: val.url,
				publishedDate : val.publishedDate,
				content	: val.contentSnippet,
				favico	: "https://www.google.com/s2/favicons?domain="+val.link,
				keywords: results.query
			};
			var the_item = LP_news_res_tpl1(item);
			jQuery("#topic_collection_rss_setup .cols .results_news_tpl1 .search_section_title:eq("+the_rss_keywords[results.query]+")").append(the_item);

		});
		
		jQuery("#topic_collection_rss_setup .cols .results_news_tpl1 .search_section_title:eq("+the_rss_keywords[results.query]+")").show();
		jQuery("#topic_collection_rss_setup .results_news_tpl1").mCustomScrollbar("destroy");
		jQuery("#topic_collection_rss_setup .results_news_tpl1").mCustomScrollbar({
			theme:"dark-thin",
			mouseWheelPixels: 200
		});
	}
}
function LP_topic_setup_page(){
	jQuery("#topic_collection_rss_setup").hide();
	jQuery("#topic_collection_setup").show();
}

/* Globals */
var Gfeed_loaded = false;
function LP_topic_rss_page(){
	// if(Gfeed_loaded == false){
		// google.load("feeds", "1");
		// Gfeed_loaded = true;
	// }
	jQuery("#topic_collection_setup").hide();
	jQuery("#topic_collection_rss_setup").show();
}

function LP_toggle_source_button(){
	if(jQuery(this).hasClass("active")){
		jQuery(this).removeClass("active");
	}else{
		jQuery(this).addClass("active");
	}
	
	LP_save_topic_collection_setup();
    // var the_s = jQuery(this).attr("id");
    // if(the_s == "toggle_gblog_search"){
        // if(search_sources_m[1].v == 0){
            // search_sources_m[1].v = 1;
            // jQuery(this).addClass("active");
        // }else{
            // search_sources_m[1].v = 0;
            // jQuery(this).removeClass("active");
        // }
    // }else{
        // if(search_sources_m[0].v == 0){
            // search_sources_m[0].v = 1;
            // jQuery(this).addClass("active");
        // }else{
            // search_sources_m[0].v = 0;
            // jQuery(this).removeClass("active");
        // }
    // }
}
function LP_user_has_twitter_token(){
	var toggle_twitter_search = jQuery(this);
	if(jQuery(this).hasClass("active")){
		jQuery(this).removeClass("active");
	}else{
		var data = {
			action : "LP_user_has_twitter_token"
		};
		jQuery.post(ajaxurl,data,function(r){
			if(r != "1"){
				LP_twitter_oauth();
			}else{
				jQuery(toggle_twitter_search).addClass("active");
			} 
		});
	}
}

/* GLOBAL */
var twitterWindow;
function LP_twitter_oauth(){
    twitterWindow = window.open(sub+"twitter/aouth/","twitter",'width=400,height=400,addressbar=no');
}

function LP_twitter_CB(){
	twitterWindow.close();
	jQuery("#toggle_twitter_search").addClass("active");
}

// function LP_topic_keyword_search_preview(){
	// jQuery("#topic_collection_setup .cols .results_news_tpl1").empty();
	// jQuery(".topic_keyword").each(function(){
		// var this_val = jQuery(this).val();
		// if(jQuery.trim(this_val) !="" ){
			// var q = this_val;
            // do_search(q);
		// }
	// });
	
	// do_search(q);
    // if(search_sources["google blog"]){
        // googleSearch({term:q,type:"blogs"});
    // }
    
    // if(search_sources["google news"]){
        // googleSearch({term:q,type:"news",LP_call_back:"LP_topic_keyword_search_resuslts"});
	// }
    // if(search_sources["twitter"]){
        // LP_twitter_search({term:q,count:8,LP_call_back:"LP_topic_keyword_search_resuslts"});
    // }
    // if(search_sources["dripple"]){
      
    // }
// }
    
/* GLOBALS */
var sort_source = {};
var sort_section = {};
function do_search(topic_id){
	LP_topic_suggestions(topic_id, "LP_tsearch_suggestions", "#t_splash_google_suggestions");
	// jQuery("#topic_collection_setup .results_news_tpl1").empty();
	// sort_section["news"] = {};
	// sort_section["blogs"] = {};
	// sort_section["twitter"] = {};
    // var setup = LP_get_topic_collection_setup();
    // if(typeof setup == "object"){
		// var a = 0;
		// var col = setup.search_sources
		// for (var key in col) {
			// var val = col[key];
			// switch (key){
				// case "news":
					// if(val == 1){
						// sort_source["news"] = a;
						// jQuery("#topic_collection_setup .cols .results_news_tpl1").append("<div style=\"display:none;\" class=\"source_search_section_title\"><span>Google News</span></div>");
						// a++;
					// }
				// break;
				
				// case "blogs":
					// if(val == 1){
						// sort_source["blogs"] = a;
						// jQuery("#topic_collection_setup .cols .results_news_tpl1").append("<div style=\"display:none;\" class=\"source_search_section_title\"><span>Google Blogs</span></div>");
						// a++;
					// }
				// break;
				
				// case "twitter":
					// if(val == 1){
						// sort_source["twitter"] = a;
						// jQuery("#topic_collection_setup .cols .results_news_tpl1").append("<div style=\"display:none;\" class=\"source_search_section_title\"><span>Twitter</span></div>");
						// a++;
					// }
				// break;
			// }
			// if(val == 1){
				// var n = 0;
				// var b = 0;
				// var t = 0;
				// jQuery("#topic_collection_setup .topic_keyword").each(function(i){
					// var this_val = jQuery(this).val();
					// if(jQuery.trim(this_val) !="" ){
						// var q = this_val;
						// switch (key){
							// case "news":								
								// sort_section["news"][q] = n;
								// jQuery("#topic_collection_setup .cols .results_news_tpl1 .source_search_section_title:eq("+sort_source["news"]+")").append("<div class=\"search_section_title\"><span>"+q+"</span></div>");
								// n++;
								// googleSearch({term:q,type:"news",LP_call_back:"LP_search_done"});
							// break;
							
							// case "blogs":								
								// sort_section["blogs"][q] = b;
								// jQuery("#topic_collection_setup .cols .results_news_tpl1 .source_search_section_title:eq("+sort_source["blogs"]+")").append("<div class=\"search_section_title\"><span>"+q+"</span></div>");
								// b++;
								// googleSearch({term:q,type:"blogs",LP_call_back:"LP_search_done"});
							// break;
							
							// case "twitter":								
								// sort_section["twitter"][q] = t;
								// jQuery("#topic_collection_setup .cols .results_news_tpl1 .source_search_section_title:eq("+sort_source["twitter"]+")").append("<div class=\"search_section_title\"><span>"+q+"</span></div>");
								// t++;
								// LP_twitter_search({term:q,count:8,LP_call_back:"LP_search_done"});
							// break;
						// }
					// }
				// });
			// }
		// }  
    // }
}

function LP_write_source_search_sections(target){
    var setup           = LP_get_topic_collection_setup();
    var search_sources  = setup.search_sources;
	var keywords       = setup.search_keywords;
    for (var source in search_sources){
        var val = search_sources[source];
        if(val==1){
            var sLabel = "";
            if(source == "news"){
                sLabel = "Google News";
            }else if(source == "blogs"){
                sLabel = "Google Blogs";
            }else if(source == "twitter"){
                sLabel = "Twitter Search";
            }
            var k_section = "";
            for(var key in keywords){
                var q = keywords[key];
                k_section+= "<div id=\""+q.replace(/ /gi, '_')+"\" class=\"search_section_title\"><span>"+q+"</span></div>";
            }
            jQuery(target).append("<div style=\"display:none;\" id=\""+source+"\" class=\"source_search_section_title\"><span>"+sLabel+"</span>"+k_section+"</div>");
        }
    }
}

function LP_prepare_news_item(res, showimage){
    var domain = res.unescapedUrl;
    domain = domain.replace("www.","");
    domain = domain.replace("http://","");
    domain = domain.replace("https://","");
    var sdomain = domain.split("/");
    
    var item = {
        title			: res.title,
        src				: "Google News",
        publishedDate 	: res.publishedDate,
		article_url		: res.unescapedUrl,
        domain			: sdomain[0],
        content			: res.content,
		keywords		: res.keywords,
		favico			: "https://www.google.com/s2/favicons?domain="+res.unescapedUrl
    };
    if(showimage){
        if(res.hasOwnProperty("image")){
            item.image = res.image.tbUrl;
        }
    }
    return item;
}   
   
function LP_prepare_blogs_item(res, showimage){   
    var domain = res.blogUrl;
    domain = domain.replace("www.","");
    domain = domain.replace("http://","");
    domain = domain.replace("https://","");
    var sdomain = domain.split("/");
    item = {
        title			: res.title,
        src				: "Google Blogs",
        domain  		: sdomain,
        publishedDate 	: res.publishedDate,
		article_url		: res.postUrl,
        content			: res.content,
		keywords		: res.keywords,
		favico			: "https://www.google.com/s2/favicons?domain="+res.blogUrl
    };
    return item;
}

function LP_prepare_twitter_item(res, showimage){
    var item = {
        title			: res.text,
        src				: "@"+res.user.screen_name,
        // url			: res.user.url,
        domain  		: "twitter.com",
        publishedDate 	: res.created_at,
		keywords		: res.keywords,
		favico			: "https://www.google.com/s2/favicons?domain="+res.user.url
    };
    if(showimage){
        item.image = res.user.profile_image_url
    }
    if(res.hasOwnProperty("parsed")){
        if(res.parsed.hasOwnProperty("elements")){
            if(res.parsed.elements.title){
                item.title = res.parsed.elements.title+"("+res.text+")";
                var domain = res.parsed.url;
                domain = domain.replace("www.","");
                domain = domain.replace("http://","");
                domain = domain.replace("https://","");
                var sdomain = domain.split("/");
                item.domain = sdomain;
            }
            
            if(res.parsed.hasOwnProperty("img")){
                if(showimage){
                    item.image = res.parsed.img;
                }
            }
        }
    }
    return item;
}
function LP_search_suggestions(search_results){
    /* checking if source sections are present */
    if(jQuery(search_results.target+" .source_search_section_title").length == 0){
        LP_write_source_search_sections(search_results.target);
    }
    
    var the_item = "";
    jQuery.each(search_results.results,function(i,val){
		val.keywords = search_results.q;
        var item;
        if(val.hasOwnProperty("retweet_count")){
            item = LP_prepare_twitter_item(val,false);
        }
        if(val.hasOwnProperty("GsearchResultClass")){
            if(val.GsearchResultClass == "GnewsSearch"){
                item = LP_prepare_news_item(val,false);
            }else if(val.GsearchResultClass == "GblogSearch"){
                item = LP_prepare_blogs_item(val,false);
            }
        }
        
        the_item+= LP_news_res_tpl1(item);
    });
		
	jQuery(search_results.target+" #"+search_results.type+" #"+(search_results.q).replace(/ /gi, '_')).append(the_item);
	jQuery(search_results.target+" #"+search_results.type).show();
	
	jQuery(search_results.target).mCustomScrollbar("destroy");
	jQuery(search_results.target).mCustomScrollbar({
		theme:"dark-thin"
	});
}

function LP_tsearch_suggestions(search_results){ 
    /* checking if source sections are present */
    if(jQuery(search_results.target+" .source_search_section_title").length == 0){
        LP_write_source_search_sections(search_results.target);
    }
    
    var the_item = "";
    jQuery.each(search_results.results,function(i,val){
		val.keywords = search_results.q;
        var item;
        if(val.hasOwnProperty("retweet_count")){
            item = LP_prepare_twitter_item(val,false);
        }
        if(val.hasOwnProperty("GsearchResultClass")){
            if(val.GsearchResultClass == "GnewsSearch"){
                item = LP_prepare_news_item(val,false);
            }else if(val.GsearchResultClass == "GblogSearch"){
                item = LP_prepare_blogs_item(val,false);
            }
        }
        
        the_item+= LP_news_res_tpl1(item);
    });
		
	jQuery(search_results.target+" #"+search_results.type+" #"+(search_results.q).replace(/ /gi, '_')).append(the_item);
	jQuery(search_results.target+" #"+search_results.type).show();
	
	jQuery(search_results.target).mCustomScrollbar("destroy");
	jQuery(search_results.target).mCustomScrollbar({
		theme:"dark-thin"
	});
}

function LP_topic_suggestions(topic_id, callback, target){
    var args = {
        topic_id    : topic_id,
        callback    : callback,
		target	: target
    };

	jQuery(target).empty();
    LP_do_search(args);
}

/* 
 * args should have the property of topic_id and allback.
 */
function LP_do_search(args){
    var setup           = LP_get_topic_collection_setup(args.topic_id);
    var search_sources  = setup.search_sources;
    var keywords        = setup.search_keywords;
    var callback        = args.callback;

    for (var source in search_sources){
        var val = search_sources[source];
        if(val == 1){
            for(var key in keywords){
                var q = keywords[key];
                if(source!=="twitter"){
                   googleSearch({term:q,type:source, LP_call_back:callback, target:args.target});
                }else{
                   LP_twitter_search({term:q, count:8, LP_call_back:callback, target:args.target});
                }
            }
        }
    }
}

function LP_get_object_property(obj,property){
    var a = 0;
    for (var key in obj){
        if(key == property){
            var p = {
                property : property,
                val      : val,
                index    : a
            };
            return p;
        }
        a++;
    }
    
    return false;
}

function LP_get_array_key(arr,val){
    for (var key in arr){
        if(arr[key] == val){
            var p = {
                val      : val,
                index    : key
            };
            return p;
        }
    }
    
    return false;
}

function LP_get_topic_collection_setup(topic){
    if(typeof topic != "string" && typeof topic != "number"){
        topic = localStorage.getItem("topics_in_collection_setup");
    }else{
        localStorage.setItem("topics_in_collection_setup",topic);
    }
    if(typeof topic == "number" || typeof topic == "string"){
        var user_topics = jQuery.parseJSON(localStorage.getItem("user_topics"));
        if(user_topics[topic].hasOwnProperty("collection_setup")){
            return jQuery.parseJSON(user_topics[topic].collection_setup);
        }else{
            var default_setup = jQuery.parseJSON(localStorage.getItem("default_collection_setup"));
            return default_setup;
        }
    }else{
        return false;
    }
}

function LP_search_done(results){
	var the_item = "";
    jQuery.each(results.results,function(i,val){
		var item;
        if(val.hasOwnProperty("retweet_count")){
            item = {
                // image	: val.user.profile_image_url
                title	: val.text,
                src		: "@"+val.user.screen_name,
                //article_url : val.unescapedUrl,
                domain  : "twitter.com",
                publishedDate : val.created_at
            };
			if(val.hasOwnProperty("parsed")){
				if(val.parsed.hasOwnProperty("elements")){
					if(val.parsed.elements.title){
						item.title = val.parsed.elements.title+"("+val.text+")";
						var domain = val.parsed.url;
                        domain = domain.replace("www.","");
                        domain = domain.replace("http://","");
                        domain = domain.replace("https://","");
                        var sdomain = domain.split("/");
						item.domain = sdomain;
					}
					
					if(val.parsed.hasOwnProperty("img")){
						// item.image = val.parsed.img;
					}
				}
			}
            the_item+= LP_news_res_tpl1(item);
        }else if(val.hasOwnProperty("GsearchResultClass")){
                    if(val.GsearchResultClass == "GnewsSearch"){
                        var domain = val.unescapedUrl;
                        domain = domain.replace("www.","");
                        domain = domain.replace("http://","");
                        domain = domain.replace("https://","");
                        var sdomain = domain.split("/");
                        
                        item = {
                            title	: val.title,
							article_url : val.unescapedUrl,
                            src		: "Google News",
                            publishedDate : val.publishedDate,
                            domain	: sdomain[0],
							content	: val.content
                        };
                        if(val.hasOwnProperty("image")){
                            // item.image = val.image.tbUrl;
                        }
                        the_item+= LP_news_res_tpl1(item);
                        // jQuery("#topic_collection_setup .results_news_tpl1").append(the_item);
                    }else if(val.GsearchResultClass == "GblogSearch"){
                            var domain = val.blogUrl;
                            domain = domain.replace("www.","");
                            domain = domain.replace("http://","");
                            domain = domain.replace("https://","");
                            var sdomain = domain.split("/");
                            item = {
                                title	: val.title,
								article_url : val.postUrl,
                                src		: "Google Blogs",
                                domain  : sdomain,
                                publishedDate : val.publishedDate,
								content	: val.content
                            };
                        
                            the_item+= LP_news_res_tpl1(item);
                            // jQuery("#topic_collection_setup .results_news_tpl1").append(the_item);
                    }
        }
		
		// jQuery("#topic_collection_setup .topic_keyword").each(function(){
			// var kword = jQuery(this).val();
			// var keywords = "";
			// var sepx = "";
			// if(kword!=""){
				// var pattern = "\b"+kword+"+\b";
				// var subject = item.title;
				// alert(subject);
				// if(regexCheckMatch(pattern, subject)){
					// keywords+= sepx+kword;
					// sepx = ", ";
				// }else{
					// if(val.hasOwnProperty("content")){
						// subject = val.content;
						// if(regexCheckMatch(pattern, subject)){
							// keywords+= sepx+kword;
							// sepx = ", ";
						// }
					// }
				// }
			// }
		// });
		// item.keywords = "--";
		// alert(item.keywords);
		// var the_item = LP_news_res_tpl1(item);
		// jQuery("#topic_collection_setup .results_news_tpl1").append(the_item);
    });
	
	jQuery("#topic_collection_setup .cols .results_news_tpl1 .source_search_section_title:eq("+sort_source[results.type]+") .search_section_title:eq("+sort_section[results.type][results.q]+")").append(the_item);
	console.log(results.type+":"+results.q+"="+sort_section[results.type][results.q]);
	jQuery("#topic_collection_setup .cols .results_news_tpl1 .source_search_section_title:eq("+sort_source[results.type]+")").show();
	jQuery("#topic_collection_setup .results_news_tpl1").mCustomScrollbar("destroy");
	jQuery("#topic_collection_setup .results_news_tpl1").mCustomScrollbar({
		theme:"dark-thin",
		mouseWheelPixels: 200
	});
	// jQuery.each(results_news,function(i,val){
		// var domain = val.unescapedUrl;
		// domain = domain.replace("www.","");
		// domain = domain.replace("http://","");
		// domain = domain.replace("https://","");
		// var sdomain = domain.split("/");
		
		// var item = {
			// title	: val.title,
			// src		: "Google News",
			// publishedDate : val.publishedDate,
			// domain	: sdomain[0]
		// };
		// if(val.hasOwnProperty("image")){
			// item.image = val.image.tbUrl;
		// }
		// var the_item = LP_news_res_tpl1(item);
		// jQuery("#topic_collection_setup .results_news_tpl1").append(the_item);
	// });
	
	// jQuery.each(results_tweets,function(i,val){
		// var item = {
			// image	: val.user.profile_image_url,
			// title	: val.text,
			// src		: "@"+val.user.screen_name,
			// url		: val.user.url
		// };
		
		// var the_item = LP_news_res_tpl1(item);
		// jQuery("#topic_collection_setup .results_news_tpl1").append(the_item);
	// });
	
	// item.image.tbUrl
}


function regexCheckMatch(pattern, subject){
	alert(pattern+"  ##  "+subject);
	var re = new RegExp(pattern);
	if (subject.match(re)) {
		return true;
	} else {
		return false;
	}
}

function LP_news_res_tpl1(item){
	var the_img = "";
	var the_c = " nothumb";
	if(item.hasOwnProperty("image")){
		the_img ="<div class=\"img_cont\">\
						<img src=\""+item.image+"\"/>\
					</div>";
		the_c = "";
	}
	var the_content = "";
	if(item.hasOwnProperty("content")){
		the_content = "<span class=\"content body\">"+item.content+"</span>"
	}
	
	var favico = "";
	if(item.hasOwnProperty("favico")){
		favico = "<img src=\""+item.favico+"\" />";
	}else{
		favico = "<i class=\"greyredrip-d-18\"></i>";
	}
	
	var link = "";
    var swoosh = "";
	if(item.hasOwnProperty("url")){
        link = "param=\""+item.url+"\"";
        swoosh = "<i class=\"blueanalysis-d-18 swooosh_RSS\" "+link+">";
    }
	
	var keywords = "";
	
	if(item.hasOwnProperty("keywords")){
		keywords = "<span>"+item.keywords+"</span>";
	}
	
	var the_item = "<div class=\"item\">\
						"+the_img+"\
						<div class=\"detail"+the_c+"\">\
							<div class=\"origin\">\
								"+favico+"\
								<div class=\"info\">\
									<span>"+item.domain+"</span>\
									<span>"+item.src+"/"+item.publishedDate+"</span>\
									<div class=\"keywords\">"+swoosh+"</i><a class=\"story_URL\" href=\""+item.article_url+"\" target=\"_blank\"><i class=\"popup-d-18\"></i></a>"+keywords+"</div>\
								</div>\
							</div>\
							<span class=\"title\">"+item.title+"</span>\
							"+the_content+"\
						</div>\
					</div>";
	return the_item;
}

function LP_set_drip_image(){
	var img_url = jQuery(this).attr("alt");
    img_newfile_url = img_url;
	jQuery(".redrip_form .imgblogpostx > img").attr("src",img_url);
}

/* google api Globals */
var image_page = 0;
// var gapi_key = "AIzaSyBSVSToM2ksdiLAavFpYho-QWf9APNfwuM";
// var gapi_key = "AIzaSyDM4NO8BxDj-oruewHTHFpBmEBOWG0cSrc";
var gapi_key = "AIzaSyBJs9OdhrZ96rRp86-ipDVH690STpblC8M";

var googlesapi = "https://www.googleapis.com/customsearch/v1?key="+gapi_key+"&cx=015453469409247312102:y4s93jpb1j4&alt=json&searchType=image&imgSize=large";
function LP_suggest_images(call_scroll){
	var the_q = jQuery(".redrip_form #ripple_title").val()+" ";
	var sep = "";
	jQuery(".redrip_form input.redrip_tags.active").each(function(){
		var the_tag = jQuery(this).val();
		if(the_tag!=""){
			the_q+= sep+the_tag;
			sep = " ";
		}
	});
	
	var collection_setup = LP_get_topic_collection_setup();
	jQuery.each(collection_setup["search_keywords"],function(i,v){
		the_q+= sep+v;
		sep = " ";
	});
	
	jQuery.get(googlesapi,{q:the_q,num:10,start:1},function(r){
		// alert(r);
		// var res = jQuery.parseJSON(r);
		var images = r.items;
		jQuery("ul#image_suggestions").empty();
		jQuery.each(images,function(i,val){
			// alert(val.link);
			// alert(val.image.thumbnailLink);
			jQuery("ul#image_suggestions").append("<li><img alt=\""+val.link+"\" src=\""+val.image.thumbnailLink+"\"></li>");
		});
		var twidth = 115 * (jQuery("ul#image_suggestions li").length);
		jQuery("ul#image_suggestions").css("width",twidth+"px");
		if(call_scroll){
			jQuery("#image_suggestions_cont").mCustomScrollbar({
				horizontalScroll:true,
				scrollButtons:{
					enable:true
				},
				theme:"dark-thin",
				callbacks:{
					onTotalScroll:function(){
						// alert(r.queries.nextPage[0].count);
						if(r.queries.nextPage[0].count>=1){
							LP_suggest_images_more();
						}
					}
				}
			});
		}
	});
}

function LP_suggest_images_more(){
	image_page++;
	var the_q = jQuery(".redrip_form #ripple_title").val()+" ";
	var sep = "";
	jQuery(".redrip_form input.redrip_tags.active").each(function(){
		var the_tag = jQuery(this).val();
		if(the_tag!=""){
			the_q+= sep+the_tag;
			sep = " ";
		}
	});
	
	var collection_setup = LP_get_topic_collection_setup();
	jQuery.each(collection_setup["search_keywords"],function(i,v){
		the_q+= sep+v;
		sep = " ";
	});
	
	jQuery.get(googlesapi,{q:the_q,num:10,start:(image_page*10+1)},function(r){
		var images = r.items;
		jQuery.each(images,function(i,val){
			jQuery("ul#image_suggestions").append("<li><img  alt=\""+val.link+"\" src=\""+val.image.thumbnailLink+"\"></li>");
		});
		var twidth = 115 * (jQuery("ul#image_suggestions li").length);
		jQuery("ul#image_suggestions").css("width",twidth+"px").parent().css("width",twidth+"px");
		jQuery("#image_suggestions_cont").mCustomScrollbar("update");
	});
}

function LP_update_linkedin_message(){
	var data = {
		action : "LP_save_linkedin_message",
		subject: jQuery("#linkedin_message_forms #message_subject").val(),
		body   : jQuery("#linkedin_message_forms #message_body").val()
	};
	jQuery("#linkedin_message_forms").animate({opacity:.5},100);
	jQuery.post(ajaxurl,data,function(r){
		jQuery("#linkedin_message_forms").animate({opacity:1},100);
    });
}

function LP_toggle_industry(ndx, limit){	
	var the_num = 0;
	
	if(limit == 0){
		the_num = to_drip_data["my_industries"][ndx]["count"];
	}else{
		the_num = limit;
	}
	var the_contacts = "<tr>\
							<th>First Name</th>\
							<th>Last Name</th>\
							<th><select id=\"num_contacts_industry\" param=\""+ndx+"\">";
                            var selected = "";
							for(var i = to_drip_data["my_industries"][ndx]["count"]; i>0; i--){
                                if(the_num==i){
                                    selected = " Selected";
                                }else{
                                    selected = "";
                                }
								the_contacts+="<option value=\""+i+"\""+selected+">"+i+"</option>";
							}
							the_contacts+="</select>Industry</th>\
						</tr>";
	jQuery.each(to_drip_data["my_industries"][ndx]["contacts"],function(index,value){
		the_contacts+="<tr param=\""+value["id"]+"\">\
							<td>"+value["firstName"]+"</td>\
							<td>"+value["lastName"]+"</td>";
							if(value["industry"].length >= 18 ){
								the_contacts+="<td title=\""+value["industry"]+"\">"+(value["industry"]).slice(0,15)+"...</td>";
							}else{
								the_contacts+="<td title=\""+value["industry"]+"\">"+value["industry"]+"</td>";
							}
						the_contacts+="</tr>";
		the_num--;
		if(the_num<=0){
			return false;
		}
	});
	
	jQuery("table.industry_table").html(the_contacts).mCustomScrollbar({
		theme:"dark-thin"
	});
}

function LP_append_comment(com_data){
    var comment = "<div class=\"ripple_item alternate\">\
                        "+com_data["user_avatar"]+"\
                        <div class=\"item_details\">\
                            <span class=\"type\"><a href=\"\">"+linkedIn_AJAX.user_display_name+"'s</a> comment, July 15, 11:20AM</span>\
                            <span class=\"detail\"><b class=\"commysis\">"+(com_data["request"]["comment"]).replace(/\\/g, '')+"</b></span>\
                        </div>\
                    </div>";
    // alert(com_data["pb"]);
    jQuery(comment).insertAfter(jQuery(".cb_"+com_data["pb"]).eq(0).siblings(":last"));
	jQuery(comment).insertAfter(jQuery(".cb_"+com_data["pb"]).eq(1).siblings(":last"));
}

function LP_close_redrip_form(){
	jQuery(".redrip_form").animate({height:"toggle"},50);
}


/* Globals */
var img_newfile_url;

/* 
 *SAVES THE Re-drip 
 */
function LP_save_redrip(){
	jQuery("#save_redrip").attr("id","save_redripx");
	jQuery("#save_buffer").attr("id","save_bufferx");
	var is_fresh 		= jQuery(obj).parent().parent().find("input#is_fresh").val();
	if(is_fresh == ""){
		/* We are saving this a Re-Drip */
		action = "LP_save_redrip";
	}else{
		/* We are saving this a Fresh Drip */
		action = "LP_save_fresh_drip";
	}
	var obj     		= jQuery(this);
	var post_id 		= jQuery(obj).siblings("input#post_id").val();
	var blog_id 		= jQuery(obj).siblings("input#blog_id").val();
	var analysis 		= jQuery(obj).parent().parent().find("textarea#analysis").val();
	var ripple_content 	= jQuery(obj).parent().parent().find("textarea#ripple_content").val();
	var ripple_title 	= jQuery(obj).parent().parent().find("input#ripple_title").val();
	var topic 			= localStorage.getItem("topics_in_collection_setup");
	var story_URL		= jQuery(obj).parent().parent().find("input#story_URL").val();;
	var ripple_tags		= "";
	var sep = "";
	
	/* Is this going to buffer or publish right away? */
	var post_status = "";
	if(jQuery(obj).attr("id") == "save_bufferx"){
		post_status = "drip";
	}
	
	jQuery(obj).parent().parent().find("input.redrip_tags.active").each(function(){
		var tag = jQuery.trim(jQuery(this).val());
		if(tag!=""){
			ripple_tags+= sep+jQuery(this).val();
			sep = ",";
		}
	});
	
	var data = {
		action 			: action,
		post_id 		: post_id,
		blog_id 		: blog_id,
		analysis		: analysis,
		topic 			: topic,
		LP_topic		: topic,
		ripple_content	: ripple_content,
		ripple_title	: ripple_title,
		ripple_tags		: ripple_tags,
		newfile_url     : img_newfile_url,
		story_URL     	: story_URL,
		post_status     : post_status
	}
	
	LP_console(JSON.stringify(data));
	
	jQuery.post(ajaxurl,data,function(r){
	LP_console(r);
		if(r!="0" && r!="1"){
			LP_close_redrip_form();
			var future_drips = jQuery.parseJSON(r);
			jQuery.each(future_drips,function(i,v){
				var topic_obj = {};
				topic_obj = LP_get_localStorage_topic(i);
				topic_obj["future_drips"] = v;
				LP_update_localStorage_topic(topic_obj);
				LP_reset_splash_screens();
			});
		}else if(r=="1"){
			LP_close_redrip_form();
		}else{
			jQuery("#save_redripx").attr("id","save_redrip");
			jQuery("#save_bufferx").attr("id","save_buffer");
			
			if(ripple_title == ""){
				jQuery(".redrip_form input#ripple_title").addClass("required");
			}else{
				jQuery(".redrip_form input#ripple_title").removeClass("required");
			}
			
			if(analysis == ""){
				jQuery(".redrip_form textarea#analysis").addClass("required");
			}else{
				jQuery(".redrip_form textarea#analysis").removeClass("required");
			}
			
			if(ripple_content == ""){
				jQuery(".redrip_form textarea#ripple_content").addClass("required");
			}else{
				jQuery(".redrip_form textarea#ripple_content").removeClass("required");
			}
		}
	});
}

function LP_fresh_drip_form(){
            img_newfile_url = "";
			var to_drip_data = "";
			jQuery(".redrip_form").remove();
			var drip_form = LP_append_redrip_form(to_drip_data);
			jQuery("body").append(drip_form);
			var tstt = jQuery("#t_splash_topic_tabs").clone();
			jQuery("#re_drip_extension").append(tstt);
			LP_set_active_tab();
			jQuery(".redrip_form").find("input#is_fresh").val("fresh");
			LP_set_tab_groups();
			LP_bsplash_suggest();
			jQuery(".redrip_form #t_splash_topic_tabs").show();
			jQuery(".redrip_form").animate({height:"toggle"},100,function(){
				LP_lock_splash_screens(false);
			});
            LP_suggest_images(true);
            jQuery(".footersplash_industry").mCustomScrollbar({
                theme:"dark-thin"
            });
}

// Global 
var to_drip_data;
function LP_redrip_this(){
    var obj = jQuery(this);
    var post_id = jQuery(obj).attr("id");
    var blog_id = jQuery(obj).attr("param");
    jQuery(".redrip_form").remove();
    var data = {
        action  : "LP_fetch_drip_data",
        post_id : post_id,
        blog_id : blog_id
    };
    jQuery.post(ajaxurl,data,function(r){
		// alert(r);
        to_drip_data = jQuery.parseJSON(r);
		if(r!='0'){
            img_newfile_url = "";
			
			var drip_form = LP_append_redrip_form(to_drip_data);
			jQuery("body").append(drip_form);
			var tstt = jQuery("#t_splash_topic_tabs").clone();
			jQuery("#re_drip_extension").append(tstt);
			LP_set_active_tab();
			LP_set_tab_groups();
			LP_bsplash_suggest();
			jQuery(".redrip_form #t_splash_topic_tabs").show();
			jQuery(".redrip_form").animate({height:"toggle"},100,function(){
				LP_lock_splash_screens(false);
			});
            LP_suggest_images(true);
            jQuery(".footersplash_industry").mCustomScrollbar({
                theme:"dark-thin"
            });
		}
    });
}

function LP_bsplash_suggest(){
	if(jQuery(".redrip_form").length>0){
		var topic_id = localStorage.getItem("topics_in_collection_setup");
		/* jQuery(".redrip_form select#new_topic").val(topic_id); */
		LP_topic_suggestions(topic_id, "LP_search_suggestions", "#b_splash_google_suggestions");
		LP_feeds_suggestions(topic_id);
	}
}

function LP_append_redrip_form(drip){	
	var the_thumb = "";
	if(typeof drip == "object"){
		if(drip["thumbnail"]["fmedium"]){
			the_thumb = drip["thumbnail"]["small"];

		}
		
		var my_industries = "";
		jQuery.each(drip["my_industries"],function(index,value){
			my_industries+= "<li>\
								<span class=\"industrycount\">"+value["count"]+"</span>\
								<span class=\"industryname\">"+value["name"]+"</span>\
							</li>";
		});
		
		var drip_tags = "";
		jQuery.each(drip["latest_tags"], function(index, value){
			drip_tags+=" <li>\
						<input class=\"redrip_tags\" type=\"text\" size=\""+(value.name).length+"\" value=\""+value.name+"\"/>\
					</li>";
		});
				
		var i = 0; 
		var drip_recommened_tags = "";		
		jQuery.each(drip["recommended_tags"], function(index, value){
			if(i==5){
				return false;
			}
			if(typeof value === "object"){
				var tag = value.name;
			}else{
				var tag = index;
			}
			
			drip_recommened_tags+=" <li>\
							<input class=\"redrip_tags\" type=\"text\" size=\""+tag.length+"\" value=\""+tag+"\"/>\
						</li>";
			i++;
		});
		
		if(i<5){
			for(var a=i; a<5; a++){
				drip_recommened_tags+=" <li>\
								<input class=\"redrip_tags\" type=\"text\" size=\"3\" value=\"\"/>\
							</li>";
			}
		}
				
		var topics 			= drip["topics"];
		var post_title 		= drip["post_title"].slice(0,90);
		var post_excerpt 	= drip["post_excerpt"].slice(0,90);
		var post_content	= drip["post_content"].slice(0,400);
		var ID 				= drip["ID"];
		var blog_id			= drip["blog_id"];
	}else{
		var drip_tags = "";
		var drip_recommened_tags ="";
		var my_industries ="";
		var topics = "";
		var post_title = "";
		var post_excerpt = "";
		var post_content = "";
		var ID = "";
		var blog_id = "";
	}
	
	var drip_form= "<div class=\"redrip_form\">\
	<div id=\"re_drip_extension\">\
		<!---div id=\"b_splash_bar\">Splash Screen Ripple<i style=\"float:right;margin:17px;\" class=\"close-l-18 unlock\"></i></div--->\
	</div>\
    <div>\
        <div class=\"b_splash_left\">\
		<br />\
		<br />\
            <table class=\"industry_table\" border=\"0\" cellspacing=\"5\">\
                <tr>\
                    <th>First Name</th>\
                    <th>Last Name</th>\
                    <th><select name=\"\"><option value=\"999\">999</option></select>Industry</th>\
                </tr>\
            </table>\
        </div>\
        <div class=\"inblogpostbody inblogpostbodyext inblogpostrippleext\">\
			<div id=\"image_suggestions_cont\">\
                <ul id=\"image_suggestions\">\
                </ul>\
			</div>\
            <!---i class=\"doublearrowdown-l-18 close_redrip_form\" style=\"float:right\"></i><br--->\
            <span class=\"inblogpostbodyspan\" style=\"clear: both;display: block;overflow: hidden;\">\
                <div class=\"custom_select\" id=\"LP_channels_select\">\
                    <div></div>\
                    <!---select class=\"new_topic_channel addtopic_sel\" id=\"new_topic\">\
                        "+topics+"\
                    </select---->\
                </div>\
				<!----span class=\"inblogpostbodyspan topic_ripple\">"+topics+"</span---->\
            </span>\
            <h2 class=\"posttitle_ext GenericSlabLight\"><input id=\"ripple_title\" class=\"ripple_title\" style=\"width:465px\" type=\"text\" value=\""+post_title+"\" /></h2>\
            <div class=\"sydcontenpost\">\
                <span style=\"overflow:hidden;\">\
				<div class=\"imgblogpostx\">\
							<img width=\"104px\" src=\""+the_thumb+"\"/>\
						</div>\
                    <div class=\"tile_op_div_inner tile_op_div_innerext drip_analysis ripple_analysis\">\
                        <div class=\"analysistab\">\
                            <!---i class=\"analysis_ico\"></i--->\
                            <span class=\"analysistext GenericSlabBold\">Analysis</span>\
                        </div>\
                        <span class=\"GenericSlabBold\">\
                            <textarea style=\"max-width:194px; max-height:144px;\" maxlength=\"90\" id=\"analysis\" name=\"analysis\">"+post_excerpt+"</textarea>\
                        </span>\
                    </div>\
                </span>\
				<textarea style=\"max-width:465px; max-height:169px;\" maxlength=\"400\" class=\"ripple_content\" id=\"ripple_content\">"+post_content+"</textarea>\
				<div class=\"ripple_tags_container\">\
					<span>Recent Tags:</span>\
					<ul>"+drip_tags;
            
        drip_form+="</ul>\
				</div>\
				<div class=\"ripple_tags_container\">\
					<span>Recommended Tags:</span>\
					<ul>"+drip_recommened_tags;
        drip_form+="</ul>\
				</div>\
                <div class=\"ripple_tags_container\" style=\"width:475px;\">\
					<span>Industry Keywords:</span>\
					<ul class=\"footersplash_industry\">\
						"+my_industries+"\
					</ul>\
				</div>\
            </div>\
            <span style=\"float: right;margin-right: 50px;\">start a</span><br />\
            <span class=\"save_add_topic save_add_ripple\" id=\"save_redrip\">Dripple</span>\
            <span class=\"save_add_topic save_add_ripple\" id=\"save_buffer\">buffer</span>\
            <input type=\"hidden\" id=\"post_id\" value=\""+ID+"\"/>\
			<input type=\"hidden\" id=\"blog_id\" value=\""+blog_id+"\"/>\
			<input type=\"hidden\" id=\"is_fresh\" value=\"\"/>\
			<input type=\"hidden\" id=\"story_URL\" value=\"\"/>\
        </div>\
        <div class=\"b_slpash_right\">\
            <div id=\"right_form\">\
                <div class=\"cols\">\
                    <div class=\"results_news_tpl1 b_splash\" id=\"b_splash_google_suggestions\">\
                    </div>\
                </div>\
				<div class=\"cols\">\
                    <div class=\"results_news_tpl1 b_splash\" id=\"b_splash_rss_suggestions\">\
                    </div>\
                </div>\
            </div>\
        </div>\
    </div>\
</div>";
return drip_form;
}

function LP_show_list_item_details(){
	var indx     = jQuery(this).index();
	var obj       = jQuery(this);
	var post_id   = jQuery(obj).attr("param");
	
	var open_the_item  = LP_list_view_open(indx);

	var open_item = jQuery(obj).siblings(".list_noheight");
	if(jQuery(open_item).length > 0){
		var open_item_index = jQuery(open_item).index();
		var close_the_item  = LP_list_view_item(open_item_index);
		jQuery(open_item).replaceWith(close_the_item);
	}
	jQuery(obj).replaceWith(open_the_item);
}

function toggle_home_view(){
    var obj = jQuery(this);
    var home_view = jQuery(obj).attr("param");
    var clicked = jQuery(obj).attr("id");
    jQuery("i#toggle_list_view, i#toggle_default_view,i#toggle_list_view").removeClass("active");
    jQuery(obj).addClass("active");
    jQuery("#list_view_cont, #default_view_cont, #tile_view_cont").fadeOut(0);
    jQuery("#"+home_view).fadeIn(350);
}

// global variables to be used in types of views
var drips_list;
var hot_drips;
var new_drips;
var trending_drips;
var hot_topics;

var drips_other_info;

function LP_fetch_home_data(){
    LP_fetch_drip_list();
}

function LP_fetch_drip_list(){
    var data = {
        action : "LP_fetch_drip_list"
    };
    jQuery.post(ajaxurl,data,function(r){
		//alert(r);
        drips_list = jQuery.parseJSON(r);        
        LP_prepare_list_view();
    });
}

function LP_list_view_item(index){
	// alert(index);
	var drip_item = drips_list[index];
	// alert(drip_item.ID);
	var list_item = "<li param=\""+drip_item["ID"]+"\">\
                            <span class=\"summary_li\">\
                                <div class=\"bookmark_li\"><i class=\"bookmark_ico\"></i></div>\
                                <div class=\"origin_li\">Article Source</div>\
                                <div class=\"title_li\">"+drip_item["post_title"]+"</div>\
                                <span>\
                                <div class=\"time_li\">3h</div>\
                                <div class=\"menu_li\">\
                                    <span>3h</span>\
                                    <i class=\"redrip_ico\"></i>\
                                    <i class=\"like_ico\"></i>\
                                    <i class=\"email_ico\"></i>\
                                    <i class=\"markread_ico\"></i>\
                                </div>\
                                "+drip_item["post_content"]+"\
                                </span>\
                            </span>\
                        </li>";
	return list_item;
}

function LP_prepare_list_view(){
    var post_items = new Array();
    jQuery.each(drips_list,function(index,value){
        var drip_item = value;
        // alert(drip_item["ID"]);
        post_items[index] = {post_id : drip_item["ID"],blog_id : drip_item["blog_id"], topic_id : drip_item["topic_id"], post_author : drip_item["post_author"]};
        var list_item = LP_list_view_item(index);
        jQuery("#list_view_cont ul#unsorted_list").append(list_item);
    });
    
    var data = {
        action : "LP_get_post_other_info",
        post_items : post_items
    };
    jQuery.post(ajaxurl,data,function(r){
        // alert(r);
        drips_other_info = jQuery.parseJSON(r);
        LP_prepare_tile_view();
		jQuery("#list_view_cont > ul#unsorted_list > li").live("click",LP_show_list_item_details);
    });
}

function LP_list_view_open(index){
	var drip_item = drips_list[index];
	var the_thumb = "";
	if(drips_other_info[index]["thumbnail"]["small"]){
		the_thumb = "<div class=\"flip_def_image_cont\">\
                        <div class=\"flip_image_rot\">\
                            <div class=\"imgblogpost\">\
                                <a href=\""+drip_item["cloaked_URL"]+"\"><img src=\""+drips_other_info[index]["thumbnail"]["small"]+"\"></a>\
                            </div>\
                           <div class=\"backx_flip\" style=\"display:none\">\
                                <div class=\"reverseflip_me\">\
                                    <img width=\"220px\" src=\""+drips_other_info[index]["thumbnail"]["fmedium"]+"\"/>\
                                </div>\
                            </div>\
                         </div>\
                    </div>";
	}
	var list_item = "<li class=\"list_noheight\" param=\""+drip_item["ID"]+"\">\
					<div class=\"flipbox-container\">\
						<div class=\"indgropostcat the_flipping\">\
							<div class=\"mash_prof_det_div\">\
								<span class=\"mash_hot_chann mash_trend_chann\"><a href=\""+sub+"channel/"+drip_item["channel_name"]+"/\">"+drip_item["channel_name"]+"</a></span>\
								<div class=\"placewtym placewtymext\">New York Times / 3 mins</div>\
								<i class=\"turn-l-18 flip_me extra\"></i>";
								if(is_forms){
									list_item+="<i class=\"blueredrip-dg-18 redrip_this\" id=\""+drip_item["ID"]+"\" param=\""+drip_item["blog_id"]+"\"></i>\
									<i class=\"close-l-18\"></i>";
								}
							list_item+="</div><div class=\"authordivindiimg bluename tile_prof_img_divextmore\">\
								<a href=\"localhost/sanyahuo/hello-world/\"><a href=\""+drips_other_info[index]["topic_link"]+"\">"+drips_other_info[index]["author_avatar"]+"</a>\
								<span class=\"cntartno\">1</span><p class=\"artclpdiv\">articles</p></div>\
							<div class=\"inblogpostbody inblogpostbodyext\">\
								<div class=\"authordivindi\"><a href=\""+drips_other_info[index]["topic_link"]+"\">"+drips_other_info[index]["user_full_name"]+"</a></div>\
								<span class=\"inblogpostbodyspan\"><a href=\""+drips_other_info[index]["topic_link"]+"\">"+drip_item["topic_name"]+"</a></span>\
											<h2 class=\"posttitle_ext GenericSlabLight\"><a href=\""+drip_item["cloaked_URL"]+"/\">"+drip_item["post_title"]+"</a></h2>\
											<div class=\"sydcontenpost\">\
									<span>\
										"+the_thumb+"\
								<div class=\"tile_op_div_inner tile_op_div_innerext drip_analysis\">\
									<div class=\"analysistab\">\
										<i class=\"analysis_ico\"></i>\
										<span class=\"analysistext GenericSlabBold\">Analysis</span>\
									</div>";
								if(drip_item["cloaked_URL"]!=""){
									list_item+="<span class=\"GenericSlabBold\"><a href=\""+drip_item["cloaked_URL"]+"/\">"+drip_item["post_excerpt"].slice(0,90)+"</a></span>";
								}else{
									list_item+="<span class=\"GenericSlabBold\">"+drip_item["post_excerpt"].slice(0,90)+"</span>";
								}
								list_item+="</div>\
									<p>"+drip_item["post_content"]+"</p>\
									</span>\
								</div>\
								<div class=\"tagpstcommnt tagpstcommntext\">\
									<ul class=\"li_tagpost\"><li><a href=\"localhost/sanyahuo/tag/china/\" rel=\"tag\">China</a></li><li><a href=\"localhost/sanyahuo/tag/question/\" rel=\"tag\">Question</a></li></ul></div>\
							</div>\
							<div class=\"comment_read\">\
								<div class=\"divcomllykimg\"><i class=\"eye-l-18\"></i><span>1.1K</span></div>\
								<div class=\"divcomllykimg\"><i class=\"greytotldrips-l-18\"></i><span>.5K</span></div>\
								<div class=\"divcomllykimg\"><i class=\"thumbsup-l-18\"></i><span>15</span></div>\
								<div class=\"divcomllykimg\"><i class=\"bubble-l-18\"></i><span>4</span></div>";
								if(drip_item["cloaked_URL"]!=""){
									list_item+="<div class=\"divcomllykimgright\"><a href=\""+drip_item["story_URL"]+"\" target=\"_blank\"><i class=\"greyanalysis-l-18\"></i></a></div>";
								}else{
									list_item+="<div class=\"divcomllykimgright\"><i class=\"greyanalysis-l-18\"></i></div>";
								}
								list_item+="<div class=\"divcomllykimgright\"><i class=\"ribbon-l-18\"></i></div>\
								<div class=\"divcomllykimgright\"><i class=\"envelop-l-18\"></i></div>\
							</div>\
						</div>\
						<!----------------->\
						<div class=\"back_flip\" style=\"display:none\">\
						<br>\
							<div class=\"postx_holder\">\
								<div class=\"tile_low\">\
									<div class=\"tile_prof_img_div\">\
										<a href=\"http://www.drippost.com/100025/topic/health-is-wealth/\"><img src=\"http://m.c.lnkd.licdn.com/mpr/mprx/0_iHwY2daW-pUZWkOWbeIKoH2wBgdsmQYWbEz1oHpLVJeJWb2FL4oteCirJRf\" width=\"80\" height=\"80\"></a>\
									</div>\
									<span style=\"position: absolute;z-index: 10000;top: 15px;right: 10px;\" class=\"reverse_me\"><i class=\"turn-l-18 reverse_me extra\"></i></span>\
									<div class=\"tilex_prof_det_div\">\
										<span class=\"tile_pname\"><a href=\"http://www.drippost.com/100025/topic/health-is-wealth/\">Mandy Deng</a></span>\
										<span class=\"tile_chann GenericSlabBold\"><a href=\"http://www.drippost.com/channel/health_care/\">health care</a></span>\
											<h2 class=\"tile_post_title GenericSlabLight\"><a href=\"/lp/000034/\">REDRIP TEST The 5 Best One Liners From Wall Street’s Top Cop</a></h2>\
									</div>\
									<div class=\"drip_stats_cont_div\">\
										<div>\
											<i class=\"thumbsup-l-21\"></i>\
											<span>12.1K</span>\
											<label>ThumbsUp</label>\
										</div>\
										<div>\
											<i class=\"ripples-l-21\"></i>\
											<span>435</span>\
											<label>Ripples</label>\
										</div>\
										<div>\
											<i class=\"greydrip-l-21\"></i>\
											<span>4</span>\
											<label>Drips</label>\
										</div>\
										<div>\
											<i class=\"eye-l-21\"></i>\
											<span>361</span>\
											<label>Views</label>\
										</div>\
									</div>\
									<div class=\"ripples_cont_div\">\
										<div class=\"ripple_item alternate cb_63-22\">\
											<img src=\"http://m.c.lnkd.licdn.com/mpr/mprx/0_t4BNFB7FGpKxojCRtsbsC9CbbIT0eyCRPIdJ3nrd27hy7grvAH9Z_KbAsXy\" width=\"40\" height=\"40\">\
											<div class=\"item_details\">\
												<textarea param=\"63-22\" class=\"comment_box\" style=\"width: calc(100% - 8px); height: 33px;\" placeholder=\"Comment\"></textarea>\
											</div>\
										</div>\
											<div class=\"ripple_item  alternate\">\
													<a class=\"ripple_avatar_a\" href=\"http://www.drippost.com/in/100025/\"><img src=\"http://m.c.lnkd.licdn.com/mpr/mprx/0_iHwY2daW-pUZWkOWbeIKoH2wBgdsmQYWbEz1oHpLVJeJWb2FL4oteCirJRf\" width=\"40\" height=\"40\"></a>\
													<div class=\"item_details\">\
														<span class=\"type\"><a href=\"http://www.drippost.com/in/100025/\">Mandy Deng</a>, July 18, 01:07AM</span>\
														<span class=\"detail\"><b>Dripped</b> this on <a href=\"http://www.drippost.com/100025/topic/what-is-buzz/\">What is Buzz</a></span>\
													</div>\
												</div>\
												<div class=\"ripple_item  \">\
													<a class=\"ripple_avatar_a\" href=\"http://www.drippost.com/in/100022/\"><img src=\"http://m.c.lnkd.licdn.com/mpr/mprx/0_t4BNFB7FGpKxojCRtsbsC9CbbIT0eyCRPIdJ3nrd27hy7grvAH9Z_KbAsXy\" width=\"40\" height=\"40\"></a>\
													<div class=\"item_details\">\
														<span class=\"type\"><a href=\"http://www.drippost.com/in/100022/\">Ronnel Añasco</a>, July 18, 10:07AM</span>\
														<span class=\"detail\"><b>Redripped</b> this on <a href=\"http://www.drippost.com/100022/topic/obesity/\">Obesity</a></span>\
													</div>\
												</div>\
													<div class=\"ripple_item  alternate\">\
													<a class=\"ripple_avatar_a\" href=\"http://www.drippost.com/in/100022/\"><img src=\"http://m.c.lnkd.licdn.com/mpr/mprx/0_t4BNFB7FGpKxojCRtsbsC9CbbIT0eyCRPIdJ3nrd27hy7grvAH9Z_KbAsXy\" width=\"40\" height=\"40\"></a>\
													<div class=\"item_details\">\
														<span class=\"type\"><a href=\"http://www.drippost.com/in/100022/\">Ronnel Añasco</a>, July 18, 11:07AM</span>\
														<span class=\"detail\"><b>Redripped</b> this on <a href=\"http://www.drippost.com/100022/topic/best-hotel/\">Best Hotel</a></span>\
													</div>\
												</div>\
												<div class=\"ripple_item  \">\
													<a class=\"ripple_avatar_a\" href=\"http://www.drippost.com/in/100025/\"><img src=\"http://m.c.lnkd.licdn.com/mpr/mprx/0_iHwY2daW-pUZWkOWbeIKoH2wBgdsmQYWbEz1oHpLVJeJWb2FL4oteCirJRf\" width=\"40\" height=\"40\"></a>\
													<div class=\"item_details\">\
														<span class=\"type\"><a href=\"http://www.drippost.com/in/100025/\">Mandy Deng</a>, July 19, 10:07AM</span>\
														<span class=\"detail\"><b>Redripped</b> this on <a href=\"http://www.drippost.com/100025/topic/health-is-wealth/\">Health Is Wealth</a></span>\
													</div>\
												</div>\
											</div>\
								</div>\
							</div>\
						</div>\
						<!----------------->\
					</div>\
					</li>";
	return list_item;
}

function LP_prepare_tile_view(){
    var position = "left";
    jQuery.each(drips_list,function(index,value){
        var drip_item = value;
		var the_thumb = "";
		if(drips_other_info[index]["thumbnail"]["large"]){
			the_thumb = "<div class=\"flip_image_cont\">\
                <div class=\"flip_image_rot\">\
                    <div class=\"tile_feat_img_div\">\
                       <img src=\""+drips_other_info[index]["thumbnail"]["large"]+"\">\
                    </div>\
                    <div class=\"backx_flip\" style=\"display:none\">\
                        <div class=\"reverseflip_me\">\
                            <img width=\"490px\" src=\""+drips_other_info[index]["thumbnail"]["flarge"]+"\"/>\
                        </div>\
                    </div>\
                </div>\
            </div>";
		}else{
			the_thumb = "<br />";
		}
        var tile_item = "<div class=\"post_holder\">\
				"+the_thumb+"\
			<div class=\"tile_low\">\
				<div class=\"tile_prof_img_div\"><a href=\""+drips_other_info[index]["topic_link"]+"\">"+drips_other_info[index]["author_avatar"]+"</a>\
				</div>\
				<div class=\"tile_prof_det_div\">\
					<span class=\"tile_pname\">\
						<a href=\""+drips_other_info[index]["topic_link"]+"\">"+drips_other_info[index]["user_full_name"]+"</a>\
						<i class=\"turn-l-18 flip_me extra\"></i>";
						if(is_forms && current_user!=drip_item["post_author"]){
							tile_item+= "<i class=\"blueredrip-dg-18 redrip_this\" id=\""+drip_item["ID"]+"\" param=\""+drip_item["blog_id"]+"\"></i>\
										<i class=\"close-l-18\"></i>";
						}
					tile_item+= "</span>\
					<span class=\"tile_chann GenericSlabBold\"><a href=\""+sub+"channel/"+drip_item["channel_name"]+"/\">"+drip_item["channel_name"]+"</a></span>\
					<span class=\"tile_topic GenericSlabBold\"><a href=\""+drips_other_info[index]["topic_link"]+"\">"+drip_item["topic_name"]+"</a></span>\
				</div>\
				<div class=\"tile_post_cont_div\">\
					<span class=\"source_site\" style=\"width:100%\">monster.com / 2 days</span>\
					<span class=\"tile_post_title GenericSlabLight\"><a href=\""+drip_item["cloaked_URL"]+"/\" target=\"_blank\">"+drip_item["post_title"]+"</a></span>\
					<span class=\"tile_post_cont\">";
                    if(drip_item["post_excerpt"]){
						tile_item+= drip_item["post_content"].slice(0,190);
						tile_item+= "<div class=\"tile_op_div_inner\">\
							<div class=\"analysistab\">\
								<i class=\"analysis_ico\"></i>\
								<span class=\"analysistext GenericSlabBold\">Analysis</span>\
							</div>\
							<span class=\"GenericSlabBold\"><a href=\""+drip_item["cloaked_URL"]+"/\">"+drip_item["post_excerpt"]+"</a></span>\
						</div>";
						tile_item+= drip_item["post_content"].slice(190);
                    }else{
                        tile_item+= drip_item["post_content"];
                    }
					tile_item+= "</span>\
					<div class=\"tagpstcommnt\">\
					<ul class=\"li_tagpost\">\
                        <li><a href=\"http://www.drippost.com/blog/tag/advertising/\" rel=\"tag\">advertising</a></li>\
                        <li><a href=\"http://www.drippost.com/blog/tag/branding/\" rel=\"tag\">branding</a></li>\
                        <li><a href=\"http://www.drippost.com/blog/tag/media/\" rel=\"tag\">media</a></li>\
                    </ul>\
                </div>\
				</div>\
				<div class=\"comment_read\">\
					<div class=\"divcomllykimg\"><i class=\"eye-l-18\"></i><span>1.1K</span></div>\
					<div class=\"divcomllykimg\"><i class=\"greytotldrips-l-18\"></i><span>.5K</span></div>\
					<div class=\"divcomllykimg\"><i class=\"thumbsup-l-18\"></i><span>15</span></div>\
					<div class=\"divcomllykimg\"><i class=\"bubble-l-18\"></i><span>4</span></div>";
					if(drip_item["cloaked_URL"]!=""){
						tile_item+="<div class=\"divcomllykimgright\"><a href=\""+drip_item["story_URL"]+"\" target=\"_blank\"><i class=\"greyanalysis-l-18\"></i></a></div>";
					}else{
						tile_item+="<div class=\"divcomllykimgright\"><i class=\"greyanalysis-l-18\"></i></div>";
					}
					tile_item+="<div class=\"divcomllykimgright\"><i class=\"ribbon-l-18\"></i></div>\
					<div class=\"divcomllykimgright\"><i class=\"envelop-l-18\"></i></div>\
				</div>\
		</div>";
        jQuery("#tile_view_cont ."+position+"_tiles").append(tile_item);
        if(position == "left") position = "right";
        else position = "left"; 
    });
}

function LP_set_update_topic_form(){
    var obj = jQuery(this);
    if(jQuery(obj).text() == "Save" && jQuery(obj).attr("param")== "free"){
        LP_update_topic_submit(obj);
    }else{
        var item = jQuery(obj).parent().parent();
        if(jQuery("form",item).length == 0){
            var index = (jQuery(item).index())-1;
            var post_id = jQuery(item).attr("param");
            var thumb_form = "<span class=\"feat_img_info\">Drop image here to replace thumbnail.</span>\
                            <form class=\"LP_update_topic_uploader\" name=\"LP_update_topic_uploader\" method=\"post\" action=\""+sub+"lp_add_topic_thumb\" target=\"LP_topic_iframe_"+post_id+"\" enctype=\"multipart/form-data\">\
                                <input class=\"update_topic_feat_img\" id=\"update_topic_feat_img\" name=\"topic_file\" type=\"file\">\
                                <input type=\"hidden\" id=\"usession_name\" name=\"session_name\" value=\"temp_file_"+post_id+"\">\
                                <input type=\"hidden\" name=\"post_id\" value=\""+post_id+"\" />\
                                <input type=\"hidden\" name=\"callback_function\" value=\"LP_set_topic_thumb\" />\
                            </form>\
                            <iframe name=\"LP_topic_iframe_"+post_id+"\" style=\"display:none;\"></iframe>";
            jQuery(".addtopic_feat_img_div",item).append(thumb_form);
            var topic_channel = jQuery("#LP_channels_select").clone();
            jQuery("select",topic_channel).val(LP_user_topics[index]["channel_id"]);
            jQuery(".topic_chann",item).replaceWith(topic_channel);
            jQuery(".topic_post_title",item).replaceWith("<input type=\"text\" class=\"new_topic_title\" placeholder=\"Title\" id=\"new_topic_title\" value=\""+LP_user_topics[index]["post_title"]+"\">");
            jQuery(".topic_post_cont",item).replaceWith("<textarea class=\"new_topic_description\" placeholder=\"Short description\" id=\"new_topic_description\" maxlength=\"250\">"+LP_user_topics[index]["post_content"]+"</textarea>");
            jQuery(".topic_post_cont_div",item).addClass("topic_post_cont_div_edit");
            jQuery(obj).text("Save");
        }
    }
}

function LP_update_topic_submit(obj){
    jQuery(obj).attr("param","busy");
    var item = jQuery(obj).parent().parent();
    var post_id = jQuery(item).attr("param");
    var channel = jQuery("select.new_topic_channel",item).val();
    var title = jQuery("input.new_topic_title",item).val();
    var description = jQuery("textarea.new_topic_description",item).val();
    var session_name = jQuery("input#usession_name",item).val();
    var data = {
        action       : "LP_update_topic_submit",
        post_id      : post_id,
        channel      : channel,
        title        : title,
        description  : description,
        session_name : session_name
    };
    jQuery.post(ajaxurl,data,function(r){
        // alert(r);
        var topic = jQuery.parseJSON(r);
        var xitem = jQuery(".post_holder_addtopic[param='"+topic["ID"]+"']");
        var topic_thumb = jQuery(".addtopic_feat_img_div img",xitem);
        var index = (jQuery(xitem).index())-1;
        LP_user_topics[index] = topic;
		LP_update_user_topics_storage(LP_user_topics);
	
        var updated_topic = LP_topic_form_loop(topic);
        jQuery(xitem).replaceWith(updated_topic);
        jQuery(".post_holder_addtopic").eq(index).find(".addtopic_feat_img_div img").replaceWith(topic_thumb);
    });
}

function LP_update_user_topics_storage(user_topics){
	var default_collection_setup = JSON.stringify({
            search_sources  : {news:1,blogs:0,twitter:0,dripple:0},
            search_keywords : {},
            rss_feed_links  : {}
        });
	localStorage.setItem("default_collection_setup",default_collection_setup);
	var user_topic_ids = "";
	var sep = "";
	var ut = {};
	jQuery.each(ret,function(i,val){
		if(val.collection_setup === null){
			val.collection_setup = default_collection_setup;
		}
		ut[val.ID] = val;
		user_topic_ids+=sep+val.ID;
		sep = ",";
	});
	localStorage.setItem("topics_in_collection_setup",ret[0].ID);
	localStorage.setItem("user_topics",JSON.stringify(ut));
	LP_fetch_all_topic_future_drips(user_topic_ids);
	LP_populate_topic_tabs();
}

function LP_fetch_all_topic_future_drips(user_topic_ids){
	var data = {
		action : "LP_fetch_all_topic_future_drips",
		user_topic_ids : user_topic_ids
	};
	
	jQuery.post(ajaxurl,data,function(r){
		if(r!="0"){
			var future_drips = jQuery.parseJSON(r);
			jQuery.each(future_drips,function(i,v){
				var topic_obj = {};
				topic_obj = LP_get_localStorage_topic(i);
				topic_obj["future_drips"] = v;
				LP_update_localStorage_topic(topic_obj);
			});
		}
	});
}
function LP_set_topic_thumb(post_id, new_thumb){
    var item = jQuery(".post_holder_addtopic[param='"+post_id+"']");
    jQuery("div.addtopic_feat_img_div > img",item).attr("src",new_thumb);
    jQuery("#update_add_topic",item).attr("param","free");
}

function LP_get_user_topics(populate){
    // Should only allow if logged in.
    var data = {
        action  : 'LP_user_topics'
    };
    jQuery.post(ajaxurl,data,function(r){
        ret = jQuery.parseJSON(r);
        LP_user_topics = ret;
        LP_update_user_topics_storage(ret);
        if(populate){
            LP_populate_topic_form();
        }
    });
}

function LP_populate_topic_form(){
    var post_ids = "";
    var sep = "";
    jQuery.each(LP_user_topics, function(index,value){
        var topic   = value;
        post_ids    = post_ids + sep + topic["ID"];
        sep         = ",";
        var topic_item = LP_topic_form_loop(topic);
        // jQuery(topic_item).addAttr("id",index);
        jQuery(".managetopicsdiv").append(topic_item);
    });
    
    LP_fetch_set_topic_thumb(post_ids);
}

function LP_fetch_set_topic_thumb(post_ids){
    var data = {
            action   : "LP_get_topics_thumb",
            post_ids : post_ids
        };
    
    jQuery.post(ajaxurl,data,function(r){
        var posts_thumb = jQuery.parseJSON(r);
        jQuery.each(posts_thumb, function(index,value){
            if(value["thumbnail"]!="0"){
                LP_set_topic_thumb(value["post_id"], value["thumbnail"]);
            }
        });
    });
}
function LP_topic_form_loop(topic){
var prof_pic = jQuery(".tile_prof_img_div img").attr("src");
var user_identity = jQuery(".topic_pname:first").text();
var topic_item = "<div class=\"post_holder_addtopic\" param=\""+topic["ID"]+"\">\
                        <div class=\"addtopic_feat_img_div\">\
                            <img src=\"\">\
                        </div>\
                        <div class=\"tile_low\">\
                            <div class=\"tile_prof_img_div addtopic_prof_img_div\">\
                                <img src=\""+prof_pic+"\">\
                            </div>\
                            <div class=\"topic_prof_det_div\">\
                                <span class=\"topic_pname\"><a>"+user_identity+"</a></span>\
                                <span class=\"topic_chann  addtopic_chann GenericSlabBold\">"+topic["name"]+"</span>\
                            </div>\
                            <div class=\"topic_post_cont_div addtopic_post_cont_div\">\
                                <span class=\"topic_post_title addtopic_post_title GenericSlabLight to_topic_setup\">"+topic["post_title"]+"</span>\
                                <span class=\"topic_post_cont\">"+topic["post_content"]+"</span>\
                            </div>\
                            <span class=\"save_add_topic\" id=\"update_add_topic\" param=\"free\">Update</span>\
                        </div>\
                    </div>";
    return topic_item;
}

var add_topic_busy = true;
var add_topic_init = true;
function LP_add_topic_uploader_submit(){
	if(!add_topic_busy || add_topic_init == true){
		jQuery("#LP_add_topic_uploader").submit();
		add_topic_busy = true;
		add_topic_init = false;
	}else{
		return false;
	}
}

function LP_update_topic_uploader_submit(){
    var obj = jQuery(this);
    var isBusy = jQuery(obj).parent().parent().parent().find("#update_add_topic").attr("param");
    if(isBusy == "free"){
        jQuery(obj).parent().parent().parent().find("#update_add_topic").attr("param","busy");
        jQuery(obj).parent().submit();
    }
}

function lp_set_topic_image(src){
	jQuery("#LP_feat_img_ul").attr("src",src);
	add_topic_busy = false;
}

function LP_submit_add_topic(){
	if(!add_topic_busy){
		add_topic_busy = true;
		var topic_channel = jQuery("#new_topic_channel").val();
		var topic_title = jQuery("#new_topic_title").val();
		var topic_description = jQuery("#new_topic_description").val();
		var session_name = jQuery("#session_name").val();
		if(topic_channel && topic_title && topic_description){
			var data = {
				action  		: "LP_save_add_topic",
				type    		: "topic",
				channel 		: topic_channel,
				wpbody  		: topic_description,
				wptitle 		: topic_title,
				session_name 	: session_name
			};
			jQuery.post(ajaxurl,data,function(r){
				add_topic_busy = false;
                var topic = jQuery.parseJSON(r);
                var temp_topics = new Array();
                temp_topics[0] = topic;
				
				var default_collection_setup = JSON.stringify({
					search_sources  : {news:1,blogs:0,twitter:0,dripple:0},
					search_keywords : {},
					rss_feed_links  : {}
				});
				var ut={};
					ut[topic.ID] = topic;
					if(topic.collection_setup === null){
						topic.collection_setup = default_collection_setup;
					}
                jQuery.each(LP_user_topics,function(index, value){
                    var i = index + 1;
                    temp_topics[i] = value;
					
					if(value.collection_setup === null){
						value.collection_setup = default_collection_setup;
					}
					ut[value.ID] = value;
					
                });
				
				localStorage.setItem("user_topics",JSON.stringify(ut));		
                
                LP_user_topics = temp_topics;
                var new_topic = LP_topic_form_loop(topic);
                
                jQuery(new_topic).insertAfter("#add_topic_cont");
                LP_fetch_set_topic_thumb(topic["ID"]);
				LP_populate_topic_tabs();
				if(r != "0"){
					 jQuery("#new_topic_channel").val('1');
					 jQuery("#new_topic_title").val('');
					 jQuery("#new_topic_description").val('');
					 jQuery("#LP_feat_img_ul").replaceWith("<img id=\"LP_feat_img_ul\" src=\"\"/>");
					 jQuery("#addtopic_feat_img").val("");
					 /* SHOW TOPIC COLLECTION SETUP */
					 jQuery(".managetopicsdiv").hide();
					 jQuery("#topic_collection_setup").attr("para",topic["ID"]).show();
					 LP_goto_topic_setup(topic["ID"]);
				}
			});
		}else{
			alert("Please fill-up form");
		}
	}
}

function LP_toggle_follow(){
	var obj = jQuery(this);
	var data = {
		action : "LP_toggle_follow",
		channel : jQuery(this).parent().parent().attr("param")
	};
	
	jQuery.post(ajaxurl,data,function(r){
		// alert(r);
		ret = jQuery.parseJSON(r);
		LP_channel_settings = ret.LP_channel_settings;
	});

	if(jQuery(obj).parent().parent().hasClass("active")){
        jQuery(obj).parent().parent().removeClass("active");
		LP_move_to_unactive(jQuery(obj).parent().parent());
	}else{
        jQuery(obj).parent().parent().addClass("active");
		LP_move_to_active(jQuery(obj).parent().parent());
	}
}

function LP_move_to_unactive(obj){
    if(jQuery(obj).hasClass("xactive")){
        var p = jQuery(".chan_lis.active").last();
        jQuery(obj).removeClass("xactive")
                   .addClass("active");
    }else{
        var p = jQuery(".chan_lis.active").last();
    }
	jQuery(obj).insertAfter(jQuery(p));
	jQuery(".followchandiv,.ufollowchandiv").css("display","none");
}

function LP_move_to_active(obj){
	jQuery("ul#chansort").prepend(jQuery(obj));
	jQuery(".followchandiv,.ufollowchandiv").css("display","none");
}

function animslide_ufollow(obj){
	if(jQuery(obj).parent().hasClass("active")){
		jQuery(".ufollowchandiv",obj).css("display","block");
	}else{
		jQuery(".followchandiv",obj).css("display","block");
	}
	jQuery(obj).stop().animate({"width":187},100);
}

function animslout_ufollow(obj){
	// alert("I am f***** OUT!!!");
	jQuery(obj).stop().animate({"width":26},100,function(){
		if(jQuery(obj).parent().hasClass("active")){
			jQuery(".ufollowchandiv",obj).css("display","none");
		}else{
			jQuery(".followchandiv",obj).css("display","none");
		}
	});
}

function update_featured_image(){
    var LP_file = jQuery("#LP_file").val();
    var drip_id = jQuery("#LP_drip_id").val();
    var the_iframe_uploader = jQuery("#LP_uploader_iframe");
    if(the_iframe_uploader.length > 0){
        //Do nothing...
    }else{
        jQuery("body").append("<iframe id=\"LP_uploader_iframe\" name=\"LP_uploader_iframe\" style=\"display:none;\"></iframe>");
    }
   
    if(LP_file!="" && drip_id!=""){
        jQuery("#LP_uploader").submit();
    }
}

function lp_update_image(img,drip_id){
    jQuery("li#drip_"+drip_id+" div.imgblogpost > img").attr("src",img);
    jQuery(".LP_uploader_form").hide();
    jQuery("#LP_file").val("");
    jQuery("#LP_drip_id").val("");
}

function show_uploader_form(){
    var drip_id = jQuery(this).parent().parent().parent().parent().attr("param");
    var the_uploader = jQuery(".LP_uploader_form");
    if(the_uploader.length > 0){
        jQuery("#LP_drip_id").val(drip_id);
    }else{
        jQuery("body").append("<div class=\"LP_uploader_form\">\
         <form id=\"LP_uploader\" name=\"LP_uploader\" method=\"post\" action=\""+ sub+"LP_drip/" +"\" target=\"LP_uploader_iframe\" enctype=\"multipart/form-data\">\
        <div class=\"featttldiv\">\
        <span>Insert Media</span>\
        <div id=\"close_uploader_form\">X</div>\
        </div>\
        <div class=\"featcontdiv\">\
        <div class=\"centercontfeat\">\
        <div class=\"labelfeat\">Drop files anywhere to upload.</div>\
        <input type=\"file\" name=\"LP_file\" id=\"LP_file\" />\
        <input type=\"hidden\" id=\"LP_drip_id\" name=\"LP_drip_id\" value=\""+drip_id+"\" />\
        <div class=\"labelbotfeat\">Maximum upload file size: 2MB.</div>\
        </div>\
        </div>\
        <div class=\"featsubdiv\">\
        <div id=\"updtbtnfeat\">Update</div>\
        </form>\
        </div>\
        <iframe id=\"LP_uploader_iframe\" name=\"LP_uploader_iframe\" style=\"display:none;\"></iframe>");
    }
    // Put the form into the center of screen first before showing...
    LP_pop_form("LP_uploader_form");
}

function LP_delay_to_save(){
     setTimeout(function(){
        LP_check_to_save();
     },50);
}

function LP_check_to_save(){
	var post_id 		= jQuery("#adjust_preview input#post_id").val();
	if(post_id!=""){
		var d_title    	 = jQuery("#adjust_preview input#drip_title").is(":focus");
		var t_adjust     = jQuery("#adjust_preview textarea#the_adjust").is(":focus");
		var t_content    = jQuery("#adjust_preview textarea#the_content").is(":focus");
		if(!d_title && !t_adjust && !t_content){
			LP_update_drip();
		}
	}
}

function LP_update_drip(){
	var title 		= jQuery("#adjust_preview input#drip_title").val();
	var excerpt			= jQuery("#adjust_preview textarea#the_adjust").val();
	var post_content 	= jQuery("#adjust_preview textarea#the_content").val();
	var post_id 		= jQuery("#adjust_preview input#post_id").val();
	LP_adjust_form_higlight();
	if(post_id!=""){
		if(LP_check_if_changed(post_id)){

			if(excerpt!="" && title!="" && post_content!="" && post_id!=""){
				var data = {
					action  : "LP_adjust_update_drip_post",
					title   : title,
					excerpt : excerpt,
					content : post_content,
					post_id : post_id
				};
				jQuery("#adjust_sched .adjust_topics[param='"+post_id+"'] .adjust_topic_title span").text(title);
				jQuery.post(ajaxurl,data,function(r){

					drip_obj = jQuery.parseJSON(r);
					LP_update_localStorage_drip(drip_obj);
				});
			}
		}
	}
}

function LP_adjust_form_higlight(){
	// var title 		= jQuery("#adjust_preview input#drip_title").val();
	// var excerpt			= jQuery("#adjust_preview textarea#the_adjust").val();
	// var post_content 	= jQuery("#adjust_preview textarea#the_content").val();
	// if(excerpt==""){
		// jQuery("#adjust_preview textarea#the_adjust").css("background-color","#5D4546");
	// }else{
		// jQuery("#adjust_preview textarea#the_adjust").css("background-color","#3B3C3D");
	// }
	// if(title==""){
		// jQuery("#adjust_preview input#drip_title").css("background-color","#5D4546");
	// }else{
		// jQuery("#adjust_preview input#drip_title").css("background-color","#3B3C3D");
	// }
	// if(post_content==""){
		// jQuery("#adjust_preview textarea#the_content").css("background-color","#5D4546");
	// }else{
		// jQuery("#adjust_preview textarea#the_content").css("background-color","#3B3C3D");
	// }
}

function LP_check_if_changed(drip_id){
	var title 		= jQuery("#adjust_preview input#drip_title").val();
	var excerpt			= jQuery("#adjust_preview textarea#the_adjust").val();
	var post_content 	= jQuery("#adjust_preview textarea#the_content").val();
	
	var drip = LP_get_localStorage_drip(drip_id);
	var excerpt_o 		= drip.post_excerpt;
	var title_o 		= drip.post_title;
	var post_content_o 	= drip.post_content;

	if(excerpt_o != excerpt || title_o != title || post_content_o != post_content){
		return true;
	}else{
		return false;
	}
}

jQuery(function() {
	
	jQuery( "#chansort" ).sortable({
        placeholder: "chan-state-highlight",
        forcePlaceholderSize :true, 
        update : function(event, ui){
                        var indx = ui.item.index();
                        var obj = jQuery("#chansort li").eq(indx);
                        if(jQuery(obj).hasClass("active") && jQuery(obj).prev().hasClass("active")== false){
                            jQuery(obj).removeClass("active").addClass("xactive");
                            LP_move_to_unactive(obj);
                        }
                        LP_udpate_sort_channels();
                    }
    });
    jQuery( "#chansort" ).disableSelection();
});

function LP_udpate_sort_channels(){
    var channels = "";
    var sep = "";
    jQuery("#chansort > li").each(function(){
        channels+= sep+ jQuery(this).attr("param");
        sep = ",";
    });
    var data = {
        action    : "LP_udpate_sort_channels",
        channels  : channels
    };

    jQuery.post(ajaxurl,data,function(r){
        // alert(r);
        ret = jQuery.parseJSON(r);
        channels = ret.drips;
    });
}

// rearrange Drips while waiting for the real data
function LP_adjust_correct_dates(){
    var a = 0;
    var title_index = 0;
    var date_titles = jQuery("li.date_title");
    var num_sched = drip_settings.drip_time.length;
    var new_groupd = true;
    jQuery("#sort_us > li").each(function(){
        var t = jQuery(this);
        if(new_groupd){
            new_groupd = false;
            // alert(title_index);
            jQuery(jQuery("li:eq("+title_index+")",date_titles)).insertBefore(t);
        }
        a++;
        if(a>=num_sched){
            a=0;
            new_groupd = true;
            title_index++;
        }
    });
}

function set_drips_con_width(){
	var xwidth = jQuery(window).width();
	jQuery("div.drips_con").css("width",xwidth+"px");
	jQuery(".dripheadbtn").css("width",(xwidth*5+350)+"px");
	// LP_pop_form("arrowsdiv");
	// jQuery(".arrowsdiv").css("top","300px");
}

function drip_slideDown_page(){
	LP_set_active_tab();
	LP_reset_splash_screens();
	jQuery("#t_splash_topic_tabs").show();
	LP_set_tab_groups();
    var obj = jQuery(this);
    var to_page = jQuery(obj).index();
    var xwidth = jQuery(window).width();
    var to_margin = to_page * xwidth;
    
    //Set the container to its current height.
    var cheight = jQuery(".dripgrpDIV").height();
    hide_all_head_forms_but("aaa");
    jQuery(".dripgrpDIV").css("height",cheight+"px").show();
    
    var page_height = jQuery(".dripheadbtn > div").eq(to_page).height();
	// if(page_height< 500 ) page_height = 500;
    page_height = 363;
	
    //hide the pages and set the correct margin for the page to show
    jQuery(".dripheadbtn").animate({"opacity":0},50,function(){
        jQuery(this).css("margin-left","-"+to_margin+"px")
                    .css("margin-top","-"+page_height+"px")
                    .css("height",page_height+"px")
                    .css("opacity",1);
			if(cheight < page_height){
				jQuery(".dripheadbtn").animate({"margin-top" : "-"+(page_height-cheight)+"px"},50,function(){
					jQuery(".dripgrpDIV").css("height","auto");
					jQuery(".dripheadbtn").animate({"margin-top" : 0},100,function(){
						jQuery("ul#drip_nav li p").removeClass("active");
						jQuery("p",obj).addClass("active");
                        LP_lock_splash_screens(true);
					});
				});
			}else{
				jQuery(".dripheadbtn").animate({"margin-top" : 0},100,function(){
					jQuery("ul#drip_nav li p").removeClass("active");
					jQuery("p",obj).addClass("active");
					jQuery(".dripgrpDIV").animate({"height" : (page_height+30)+"px"},100);
				});
			}
			jQuery(".arrowdown").removeClass("arrowdown").addClass("arrowbot");
    }); 
	jQuery("#adjust_sched").mCustomScrollbar("update");
}

/* 
 * Checks and Sets current tab group
 * will use the first group if none is set 
 */
function LP_set_tab_groups(){
	var current_tab_group = localStorage.getItem("current_tab_group");
	
	if(typeof current_tab_group != 'string' && typeof current_tab_group != 'number'){
		var num_ = jQuery("#t_splash_cont #t_splash_topic_tabs ul.topic_tabs").length;
		current_tab_group = num_;
	}
	localStorage.setItem("current_tab_group", current_tab_group);
	
	jQuery(".topic_tab_group li").removeClass("active");
	jQuery(".topic_tab_group li[param='"+current_tab_group+"']").addClass("active");
	
	jQuery("#t_splash_topic_tabs ul.topic_tabs").removeClass("active");
	jQuery("#t_splash_topic_tabs ul.topic_tabs").eq(current_tab_group-1).addClass("active");
	if(jQuery(".redrip_form").length>0){
		LP_console("group tab : "+current_tab_group);
		jQuery(".redrip_form .topic_tab_group li").removeClass("active");
		jQuery(".redrip_form .topic_tab_group li[param='"+current_tab_group+"']").addClass("active");
		
		jQuery(".redrip_form #t_splash_topic_tabs ul.topic_tabs").removeClass("active");
		jQuery(".redrip_form #t_splash_topic_tabs ul.topic_tabs").eq(current_tab_group-1).addClass("active");
	}
}

function LP_lock_splash_screens(fromTop){
    if(fromTop===true){
        if(jQuery(".redrip_form").is(":visible")){
            jQuery(".redrip_form").animate({bottom:-10},20,function(){
                jQuery(".redrip_form").animate({bottom:0},10,function(){
                    // LP_snap_lock();
                });
            });
        }
    }else{
		if(jQuery("#grpinddivlog").height()>0){
			 jQuery("#t_splash_cont").animate({top:-10},20,function(){
				jQuery("#t_splash_cont").animate({top:0},10,function(){
                    // LP_snap_lock();
                });
			 });
		}		
	}
}

function LP_snap_lock(){
    jQuery(".redrip_form #re_drip_extension").animate({top:-160},10,function(){
        jQuery(".redrip_form #re_drip_extension").animate({top:-150},10,function(){
			/* jQuery(".redrip_form #re_drip_extension").css("box-shadow","0 -10px 50px #2D2D2D"); */
		});
    });
	// jQuery(".redrip_form #t_splash_topic_tabs").animate({top:50},20,function(){
		 // jQuery(".redrip_form #t_splash_topic_tabs").css("z-index",3);
	// });
}

function LP_unlock_splash(){
	jQuery(".redrip_form #re_drip_extension").animate({top:-65},10,function(){
        jQuery(".redrip_form #re_drip_extension").css("box-shadow","");
    });
	jQuery(".redrip_form #t_splash_topic_tabs").animate({top:1},20,function(){
		jQuery(".redrip_form #t_splash_topic_tabs").css("z-index",2);
		jQuery(".redrip_form").slideDown(500,function(){
			jQuery(this).remove();
		});
		jQuery(".dripgrpDIV").stop().animate({"height" : 0},100,function(){
			jQuery("#t_splash_topic_tabs").hide();
			jQuery(".dripheadbtn").height(0);
		});
	});
		
}

function drip_sliding_page(obj){
	var me = jQuery(obj).attr("class");
    if(jQuery("ul#drip_nav li p.active").length > 0){
        if(me == "arrowprev"){
            if(jQuery("ul#drip_nav li p.active").parent().is(":first-child")){
                var toSlideTo = jQuery("ul#drip_nav li:last");
            }else{
                var toSlideTo = jQuery("ul#drip_nav li p.active").parent().prev();
            }
        }else if(me == "arrownext"){
            if(jQuery("ul#drip_nav li p.active").parent().is(":last-child")){
                // alert("i am here");
                var toSlideTo = jQuery("ul#drip_nav li:first");
            }else{
                // alert("else i am here");
                var toSlideTo = jQuery("ul#drip_nav li p.active").parent().next();
            }
        }
    }else{
        var toSlideTo = jQuery("ul#drip_nav li").eq(0);
    }
    
    
    var t_drip = jQuery(toSlideTo).index();
    var xwidth = jQuery(window).width();
    var to_margin = t_drip * xwidth;
    
    var page_height = parseInt(jQuery(".dripheadbtn > div").eq(t_drip).height());
	
	if(page_height< 500 ) page_height = 500;
    
    jQuery(".dripheadbtn").animate({"margin-left":"-"+to_margin+"px"},200,function(){
        jQuery("ul#drip_nav li p").removeClass("active");
        jQuery("p",toSlideTo).addClass("active");
        jQuery(".dripgrpDIV").animate({"height" : (page_height+30)+"px"},100);
		jQuery(".dripheadbtn").height(page_height);
    });
    if(jQuery(".dripgrpDIV").is(':visible')== false){
        jQuery(".dripgrpDIV").animate({height:'toggle'},200);
		jQuery(".dripheadbtn").height(page_height);
    }
}

function LP_set_adjust_page(){
	jQuery(".drips_con #adjust_sched").empty();
	jQuery("#adjust_preview").css("visibility","hidden");
    var topic_id = localStorage.getItem("topics_in_collection_setup");
	var the_topic = LP_get_localStorage_topic(topic_id);
	var future_drips = the_topic.future_drips;
	var date_label = "";
    var the_day = "";
    var the_time = "";
	var date_actual = "";
	for(var drip_id in future_drips){        
		var future_drip = future_drips[drip_id];
        if(future_drip.post_status == "future"){
            if(date_label == "" || date_label!= future_drip.date_label){
                date_label = future_drip.date_label;
                jQuery(".drips_con #adjust_sched").append("<h2 class=\"today_title\"><div class=\"sepa_hori\"></div>"+future_drip.date_label+" - "+future_drip.date_actual+"</h2>");
            }
            var item = LP_adjut_item_template(future_drip);
            jQuery(".drips_con #adjust_sched").append(item);
            the_day = future_drip.d_day;
            the_time = future_drip.post_time;
			date_actual = future_drip.date_actual;
        }
	}
    
    var meter = LP_get_topic_meter(topic_id);
    if(typeof meter.drip_time != "object"){
		meter.drip_time = {};
		meter.drip_time["09:00 am"] = "09:00 am";
	}
    if(typeof meter.drip_day != "object"){
		meter.drip_day = {};
		meter.drip_day = {
            monday      : "true",
            tuesday     : "true",
            wednesday   : "true",
            thursday    : "true",
            friday      : "true",
            saturday    : "true",
            sunday      : "true"
        };
	}
    var draft_drips = "";
    var d_days_i = 0;
    jQuery.each(d_days,function(i,v){
        if(the_day == v){
            d_days_i = i;
            return false;
        }
    });

    var sstart = false;
	var date_arr = date_actual.split("-");
	date_actual = new Date(date_arr[0],date_arr[1],date_arr[2]);
    for(var drip_id in future_drips){
        var future_drip = future_drips[drip_id];

        if(future_drip.post_status == "draft"){
            draft_drips+="";
            var the_next = "x";
            var this_time = "";
            /* LOOP until time is found and use the next time to it... */
            for(var key in meter.drip_time){
                if(the_next === true){
                    this_time = key;
                    the_next = false;
                    break;
                }
                if(the_time == key){
                    the_next = true;
                }
            }
            
            if(the_next == true || the_next == 'x'){
                for(var key in meter.drip_time){
                    this_time = key;
                    d_days_i++;
					date_label = d_days[d_days_i];
					var tdate = new Date(date_actual.getFullYear(),date_actual.getMonth(),date_actual.getDate());
					date_actual = new Date(parseInt(tdate.getTime())+86400000);
					//+" - "+date_actual.getFullYear()+"-"+date_actual.getMonth()+"-"+date_actual.getDate()
                    jQuery(".drips_con #adjust_sched").append("<h2 class=\"today_title\"><div class=\"sepa_hori\"></div>"+date_label+" - "+date_actual.getFullYear()+"-"+date_actual.getMonth()+"-"+date_actual.getDate()+"</h2>");
                    break;
                }
            }
            the_time = this_time;
            future_drip.post_time = this_time;
            var item = LP_adjut_item_template(future_drip);
            jQuery(".drips_con #adjust_sched").append(item);
            if(d_days_i>=6) d_days_i =0;
        }
    }
	jQuery("#adjust_sched").mCustomScrollbar({
		theme:"dark-thin"
	});
	
	jQuery("#adjust_sched .mCSB_container").sortable({
        placeholder: "adjust_border",
        forcePlaceholderSize :true,
        handle: 'i.greycross-d-21',
		cancel: ".today_title",
        start : function(event, ui){
				// LP_console("start : "+ui.item.index());
			},
		update : function(event, ui){
				var topic_id = localStorage.getItem("topics_in_collection_setup");
				var the_indx = ui.item.index();
				var post_id = jQuery(ui.item).attr("param");
				var before = jQuery(ui.item).prev().attr("param");
				var after  = jQuery(ui.item).next().attr("param");
				
				if(typeof before != "string" && typeof before != "number"){
					before = jQuery(ui.item).prev().prev().attr("param");
				}
				
				var data = {
					action 	: "LP_reorder_drips",
					before	: before,
					after	: after,
					topic_id: topic_id,
					post_id : post_id
				};
				jQuery.post(ajaxurl,data,function(r){
					if(r!="0"){
						var future_drips = jQuery.parseJSON(r);
						jQuery.each(future_drips,function(i,v){
							var topic_obj = {};
							topic_obj = LP_get_localStorage_topic(i);
							topic_obj["future_drips"] = v;
							LP_update_localStorage_topic(topic_obj);
							LP_reset_splash_screens();
						});
					}
				});
			}
	});
}

function update_adjust_preview(){

	LP_update_drip();

	var drip_id = jQuery(this).attr("param");

	var drip = LP_get_localStorage_drip(drip_id);

	jQuery("#adjust_preview .adjust_topic_feat_img_div img").attr("src",drip.thumb_medium);
	jQuery("#adjust_preview .topic_pname a").text(user_display_name);
	jQuery("#adjust_preview .adjust_text_chann a").text(drip.channel_name);
	jQuery("#adjust_preview .inblogpostbodyspan a").text(drip.topic_name);
	jQuery("#adjust_preview input#drip_title").val((drip.post_title).slice(0,90));
	jQuery("#adjust_preview textarea#the_adjust").val((drip.post_excerpt).slice(0,190));
	jQuery("#adjust_preview textarea#the_content").val((drip.post_content).slice(0,400));
	jQuery("#adjust_preview input#post_id").val(drip_id);
	jQuery("#adjust_preview").css("visibility","visible");
	LP_adjust_form_higlight();
}

function LP_adjut_item_template(drip){
	var param = drip.ID+" ";// don't remove the space or trouble!...
    var item = "<div class=\"adjust_topics\" param=\""+param+"\">\
					<div class=\"today_icons\">\
						<i class=\"bluedrip-d-21 adjust_icon adjust_time\">"+drip.post_time+"</i>\
						<i class=\"greycross-d-21 adjust_icon adjust_move\"></i>\
					</div>\
					<div class=\"adjust_topic_title\">\
						<span>"+drip.post_title+"</span>\
						<i class=\"ribbon-d-18\"></i>\
						<i class=\"trash-d-18\"></i>\
						<i class=\"greyanalysis-d-18\"></i>\
					</div>\
				</div>";
	return item;
}

function linkedin_register(){
    linkedInWindow = window.open(sub+"linkedInP/?lType=initiate&rtype=register","linkedInP",'width=400,height=400,addressbar=no');
    return false;
}

function linkedin_login(){
    linkedInWindow = window.open(sub+"linkedInP/?lType=initiate&rtype=login","linkedInP",'width=400,height=400,addressbar=no');
    return false;
}

function linkedin_callback_register(res){   
    jQuery(".signlogindivhead").css("display","none");
    jQuery(".signdiv").css("display","none");
    jQuery(".registerhead").css("display","none");
    
    // jQuery(".linkedin_reg_cont").hide();       
    // jQuery(".linkedin_reg_form_cont .linkedIn_pubImg").attr("src",res.pictureUrl);
    // jQuery(".linkedin_reg_form_cont .linkedIn_pubName").html(res.firstName+" "+res.lastName+"<br><span class=\"linkedIn_pubEmail\" style=\"font-size:10px\">"+res.emailAddress+"</span>");
    jQuery(".createaccdiv #namesigner").text(res.firstName+" "+res.lastName);
    if(res.pictureUrls._total>0){
        jQuery(".createaccdiv #li_avatar").attr("src",res.pictureUrls.values[0]);
    }
    jQuery(".createaccdiv .uname").val("");
    jQuery(".createaccdiv .lpass").val("");
    jQuery(".createaccdiv .lemail").val(res.emailAddress);
    jQuery(".createaccdiv").show();
    linkedInWindow.close();
    return false;
}

function linkedin_callback_login(res){
    linkedInWindow.close();
    window.location = sub+"linkedInP/?rtype=register";
    return false;
}
function linkedin_callback_redirect(url){
    linkedInWindow.close();
    window.location = url;
    return false;
}

function linkedin_submit_reg_form(){
    var btn = jQuery(this);
    jQuery(btn).hide();
    jQuery(btn).next().show();
    jQuery(".error_msg").html("");
    jQuery(".createaccdiv .errimg").removeClass("errimg");
    // var lpass = jQuery(".createaccdiv .lpass").val();
    // var cpass = jQuery(".createaccdiv .cpass").val();
    var lemail = jQuery(".createaccdiv .lemail").val();
    var lblogname = jQuery(".createaccdiv .lblogname").val();
    // if(lpass!="" & lpass == cpass){
        // if(jQuery(".createaccdiv .lemail").val()!=""){
            jQuery(".createaccdiv .cpass").removeClass("errimg");
            var data = {
                action  : "linkedP_check_login",
                email   : lemail,
                domain  : lblogname
            }
            jQuery.post(ajaxurl,data,function(res){
            // alert(res);
                if(res=="good"){
                    jQuery(".error_msg").html("");
                    // jQuery(".createaccdiv .cpass").removeClass("errimg");
                    // jQuery(".createaccdiv .lemail").removeClass("errimg");
                    
                    var data2 = {
                        action  : "linkedP_registe_new_site",
                        // uname   : lemail,
                        // lemail  : lemail,
                        domain  : lblogname,
                        // cpass   : cpass,
                        // lpass   : lpass,
                        lType   : 'default',
                        rtype   : 'register',
                        isajax  : 1
                    }
                    jQuery.post(ajaxurl,data2,function(res){
					// alert(res);
                        var suc = jQuery.parseJSON(res);
                        if(suc.success){
                            window.location = suc.member_page_url;
                        }
                    });
                }else{
                    var errs = jQuery.parseJSON(res);
                    if(errs.email){
                        jQuery(".error_msg").append("<li>"+errs.email+"</li>");
                        jQuery(".createaccdiv .lemail").addClass("errimg");
                    }
                    
                    if(errs.domain){
                        jQuery(".error_msg").append("<li>"+errs.domain+"</li>");
                        jQuery(".createaccdiv .lblogname").addClass("errimg");
                    }
                    jQuery(".error_msg").show();
                    jQuery(btn).show();
                    jQuery(btn).next().hide();
                }
            }); 
        // }else{
            // jQuery(".createaccdiv .lemail").removeClass("errimg");
            // jQuery(".error_msg").append("<li>Email address is required.</li>");
            // jQuery(".createaccdiv .lemail").addClass("errimg");
            // jQuery(".error_msg").show();
            // jQuery(btn).show();
            // jQuery(btn).next().hide();
        // }
    // }else{
        // alert(lpass+" == "+cpass);
        // jQuery(".error_msg").append("<li>Please input and confirm your password correctly.</li>");
        // jQuery(".createaccdiv .cpass").addClass("errimg");
        // jQuery(".createaccdiv .lpass").addClass("errimg");
        // jQuery(".error_msg").show();
        // jQuery(btn).show();
        // jQuery(btn).next().hide();
    // }
   // errimg
   //error_msg
}

function linkedin_callback_register_error(){
    window.location = sub+"user-already-exists/";
}

function lwp_login(){
    var uname  = jQuery("#wp_ulogin").val();
    var upass   = jQuery("#wp_upass").val();
    if(uname!="" && upass!=""){ 
        // jQuery("#wp_log_form").submit();
        var data = {
                action : "lwp_login",
                wp_ulogin :uname,
                wp_upass : upass
            }
        jQuery.post(ajaxurl,data,function(res){

                // window.location = sub+"welcome";
            var suc = jQuery.parseJSON(res);
            if(suc.success){
                window.location = suc.member_page_url;
            }else{
                jQuery(".error_msg_login").html("");
                jQuery(".error_msg_login").append("<li>"+suc.error+"</li>");
                jQuery(".error_msg_login").show();
            }
        });
    }else{
        jQuery(".error_msg_login").html("");
        jQuery(".error_msg_login").append("<li>Please fill-up the form.</li>");
        jQuery(".error_msg_login").show();
    }
    return false;
}

function lwp_register(){
    jQuery("#wp_reg_form .errimg").removeClass("errimg");
    var acc_type = jQuery('input[name=acctype]:checked', '#wp_reg_form').val()
    var terms_agree = jQuery('input[name=terms_agree]:checked', '#wp_reg_form').val()
    var uemail  = jQuery("#uemail").val();
    var fname   = jQuery("#fname").val();
    var lname   = jQuery("#lname").val();
    var upass   = jQuery("#upass").val();
    var ucpass  = jQuery("#ucpass").val();
    // alert(acc_type+" "+terms_agree);
    if(terms_agree=="Yes" && uemail!="" && fname!="" && lname!="" && upass!="" && ucpass!=""){
        if(upass == ucpass){
            if(validateEmail(uemail)){
                var data = {
                    action : 'lwp_validate_user_form',
                    email : uemail,
                    upass : upass,
                    fname : fname,
                    lname : lname,
                    acc_type : acc_type,
                };
                // jQuery("#wp_reg_form").submit();
                 jQuery.post(ajaxurl,data,function(res){
                    // alert(res);
                    if(res == '1'){
                        jQuery(document).scrollTop(0);
                        jQuery(".signlogindivhead").css("display","none");
                        jQuery(".createaccdiv").css("display","none");
                        jQuery(".signdiv").css("display","none");
                        jQuery("#mnamesigner").text(fname+" "+lname);
                        jQuery(".registerhead").css("display","block");
                    }else{
                        jQuery("#wp_reg_form span.err").remove();
                        jQuery("#wp_reg_form").prepend("<span class=\"err\">"+res+"</span>");
                        jQuery("#wp_reg_form #uemail").addClass("errimg");
                    }
                 });
            }else{
                jQuery("#wp_reg_form span.err").remove();
                jQuery("#wp_reg_form").prepend("<span class=\"err\"><p class=\"error\">Please input correct email address</p></span>");
                jQuery("#wp_reg_form #uemail").addClass("errimg");
                // alert("Please input correct email address.");
            }
        }else{
            jQuery("#wp_reg_form span.err").remove();
            jQuery("#wp_reg_form").prepend("<span class=\"err\"><p class=\"error\">Please input and confirm your password correctly.</p></span>");
            jQuery("#wp_reg_form #upass").addClass("errimg");
            // alert("Please input and confirm your password correctly.");
        }
    }else{
        if(terms_agree!="Yes"){
            jQuery("#wp_reg_form span.err").remove();
            jQuery("#wp_reg_form").prepend("<span class=\"err\"><p class=\"error\">You need to agree to our Terms of Service and Privacy Policy in order to register to LinkedPost.</p></span>");
            // jQuery("#wp_reg_form #uemail").addClass("errimg");
            // alert("You need to agree to our Terms of Service and Privacy Policy in order to register to LinkedPost.");
        }else{
            jQuery("#wp_reg_form span.err").remove();
            jQuery("#wp_reg_form").prepend("<span class=\"err\"><p class=\"error\">Please fill-up all fields.</p></span>");
            // alert("Please fill-up all fields.");
            if(uemail == "")
                jQuery("#wp_reg_form #uemail").addClass("errimg");
            
            if(fname == "")
                jQuery("#wp_reg_form #fname").addClass("errimg");
                
            if(lname == "")
                jQuery("#wp_reg_form #lname").addClass("errimg");
                
            if(upass == "")
                jQuery("#wp_reg_form #upass").addClass("errimg");
            
            if(ucpass == "")
                jQuery("#wp_reg_form #ucpass").addClass("errimg");
        }
    }
}

function lwp_register_2(){
    // form 1
    var acc_type = jQuery('input[name=acctype]:checked', '#wp_reg_form').val()
    var terms_agree = jQuery('input[name=terms_agree]:checked', '#wp_reg_form').val()
    var uemail  = jQuery("#uemail").val();
    var fname   = jQuery("#fname").val();
    var lname   = jQuery("#lname").val();
    var upass   = jQuery("#upass").val();
    var ucpass  = jQuery("#ucpass").val();
    
    // form 2
    var lttl        = jQuery(".lttl").val();
    var lucty       = jQuery(".lucty").val();
    var lcurpos     = jQuery(".lcurpos").val();
    var lcurcom     = jQuery(".lcurcom").val();
    var lcomcty     = jQuery(".lcomcty").val();
    var lcntry      = jQuery(".lcntry").val();
    var lblognme    = jQuery(".lblognme").val();
    
    var data = {
        action      :   "lwp_wpmu_user_signup",
        acc_type    :   acc_type,
        terms_agree :   terms_agree,
        email       :   uemail,
        fname       :   fname,
        lname       :   lname,
        upass       :   upass,
        ucpass      :   ucpass,
        title       :   lttl,
        city        :   lucty,
        current_pos :   lcurpos,
        current_com :   lcurcom,
        com_city    :   lcomcty,
        com_country :   lcntry,
        blogname    :   lblognme
    };
    
    jQuery.post(ajaxurl,data,function(res){
        alert(res);
        if(res == "1"){
            window.location = sub+"activate-your-account";
        }else{
            alert("An error occured during the registraation process. Please contact site administrator...");
        }
    });
}

function validateEmail(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}


//////////////////////// Linked Meter - START //////////////////////////////
function LP_get_topic_meter(topic_id){
    if(typeof topic_id != "string" && typeof topic_id != "number"){
        topic_id = localStorage.getItem("topics_in_collection_setup");
    }
	var user_topics = jQuery.parseJSON(localStorage.getItem("user_topics"));
	var the_topic_meter	= user_topics[topic_id].LP_topic_meter;
	return jQuery.parseJSON(the_topic_meter);
}

//function prepare_meter_form()
function LP_reset_meter(){
	var meter = LP_get_topic_meter();
	jQuery("ul#drip_days > li").each(function(i){
        if(meter.drip_day[d_days[i]] == 1 || meter.drip_day[d_days[i]] == "true"){
            jQuery(this).attr("class",'lidripsched');
        }else{
            jQuery(this).attr("class",'lidripsched_nonact');
        }
    });
	
	jQuery("li.litymhddrip").remove();
	if(typeof meter.drip_time != "object"){
		meter.drip_time = {};
		meter.drip_time["09:00 am"] = "09:00 am";
	}
	
	jQuery.each(meter.drip_time,function(index,value){
		var the_time = value;

		jQuery(".tymdripDIV > ul").append("<li class=\"litymhddrip\" param=\""+the_time+"\"> \
			<div class=\"clockimgdrip\">\
				<i class=\"trash-l-18\"></i>\
			</div>\
			<div class=\"tymdrip_hrs\">\
			<select class=\"t_h\">"+select_hour+"</select>\
			</div>\
			<div class=\"tymdrip_mins\">\
			<select class=\"t_m\">"+select_mins+"</select>\
			</div>\
			<div class=\"tymdrip_ampm\">\
			<select class=\"t_ampm\">\
			<option value=\"am\">am</option>\
			<option value=\"pm\">pm</option>\
			</select>\
			</div>\
			</li>");
			
			var t_time  = the_time.split(":");
			var t_h     = t_time[0];
			var amp     = t_time[1].split(" ");
			var t_m     = amp[0];
			var t_ampm  = amp[1];
			jQuery(".tymdripDIV > ul > li.litymhddrip:last .t_h").val(t_h);
			jQuery(".tymdripDIV > ul > li.litymhddrip:last .t_m").val(t_m);
			jQuery(".tymdripDIV > ul > li.litymhddrip:last .t_ampm").val(t_ampm);
	});
}

function LP_toggle_day(){
    var nth     = jQuery(this).index();
    var toggle  = jQuery(this).hasClass("lidripsched");
	
	var to_c = "lidripsched_nonact";
	if(!toggle){
		to_c = "lidripsched";
	}
		
	jQuery(this).attr("class",to_c);
	LP_save_topic_meter();
}

function LP_save_topic_meter(){
	var topic_meter = {
			drip_day	: {},
			drip_time	: {}
		};

	jQuery("ul#drip_days li").each(function(i,v){
		topic_meter.drip_day[d_days[i]] = jQuery(this).hasClass("lidripsched");
	});
	
	jQuery(".tymdripDIV > ul li").each(function(i,v){
		var the_time = jQuery("select.t_h",this).val()+":"+jQuery("select.t_m",this).val()+" "+jQuery("select.t_ampm",this).val();
		topic_meter.drip_time[the_time] = the_time;
	});
	
	var data = {
		action	: "LP_save_topic_meter",
		topic_meter : topic_meter,
		topic_id	: localStorage.getItem("topics_in_collection_setup")
	};
	jQuery.post(ajaxurl,data,function(r){
		var topic_obj = jQuery.parseJSON(r);
		LP_update_localStorage_topic(topic_obj)
		LP_fetch_all_topic_future_drips(localStorage.getItem("topics_in_collection_setup"));
	});
}

function LP_add_drip(){
	var item = "<li class=\"litymhddrip\" param=\"00:00 am\">\
					<div class=\"clockimgdrip\">\
						<i class=\"trash-l-18\"></i>\
					</div>\
					<div class=\"tymdrip_hrs\">\
						<select class=\"t_h\">"+select_hour+"</select>\
					</div>\
					<div class=\"tymdrip_mins\">\
						<select class=\"t_m\">"+select_mins+"</select>\
					</div>\
					<div class=\"tymdrip_ampm\">\
						<select class=\"t_ampm\">\
							<option value=\"am\">am</option>\
							<option value=\"pm\">pm</option>\
						</select>\
					</div>\
				</li>";
   jQuery(".tymdripDIV ul").append(item);
   LP_save_topic_meter();
}

function LP_remove_drip_time(){
   jQuery(this).parent().remove();
   LP_save_topic_meter();
}

/* function prepare_meter_form(){
    jQuery("ul#drip_days > li").each(function(i){
        if(drip_settings.drip_day[[i]] == '1'){
            jQuery(this).attr("class",'lidripsched');
        }else{
            jQuery(this).attr("class",'lidripsched_nonact');
        }
    });
    jQuery("li.litymhddrip").remove();
    jQuery.each(drip_settings.drip_time,function(index,value){
        var the_time = value;
        jQuery("<li class=\"litymhddrip\" param=\""+the_time+"\"> \
            <div class=\"clockimgdrip\"><img src=\""+sub+"wp-content/themes/linkedpost/images/clockimg.png\"></div>\
            <div class=\"tymdrip_hrs\">\
            <select class=\"t_h\">"+select_hour+"</select>\
            </div>\
            <div class=\"tymdrip_mins\">\
            <select class=\"t_m\">"+select_mins+"</select>\
            </div>\
            <div class=\"tymdrip_ampm\">\
            <select class=\"t_ampm\">\
            <option value=\"am\">am</option>\
            <option value=\"pm\">pm</option>\
            </select>\
            </div>\
            </li>")
            .insertBefore(".tymdripDIV > ul > li.litymhddrip_addimg");
            
            var t_time  = the_time.split(":");
            var t_h     = t_time[0];
            var amp     = t_time[1].split(" ");
            var t_m     = amp[0];
            var t_ampm  = amp[1];
            jQuery(".tymdripDIV > ul > li.litymhddrip:last .t_h").val(t_h);
            jQuery(".tymdripDIV > ul > li.litymhddrip:last .t_m").val(t_m);
            jQuery(".tymdripDIV > ul > li.litymhddrip:last .t_ampm").val(t_ampm);
    });
} */
//////////////////////// Linked Meter - END ////////////////////////////////


function LP_pop_form(the_class){
    var xwidth = jQuery(window).width()/2;
    var xheight = jQuery(window).height()/2;

    var xpopWidth = jQuery("."+the_class).width()/2;
    var xpopHeight = jQuery("."+the_class).height()/2;

    var xleft = xwidth-xpopWidth;
    var xtop = xheight-xpopHeight;

    jQuery("."+the_class).css("left",xleft);
    jQuery("."+the_class).css("top",xtop);

    var isHidden=jQuery("."+the_class).css("display");
    
     
    if (isHidden == "none"){
        jQuery("."+the_class).slideDown(100);
    }
    else jQuery("."+the_class).slideUp(100);
 }
 
 
 /* GOOGLE SEARCH */
 
 
 
/* GLOBALS */
var config = {
	searchSite	: false,
	type		: 'news',
	append		: false,
	perPage		: 8,			// A maximum of 8 is allowed by Google
	page		: 0				// The start page
}
var results_webs;
var results_videos;
var results_news;	
var results_images;

var results_blogs;
var results_tweets;
var results_dripples;
function googleSearch(settings){
	settings = jQuery.extend({},config,settings);

	// URL of Google's AJAX search API
	var apiURL = 'http://ajax.googleapis.com/ajax/services/search/'+settings.type+'?v=1.0&q='+settings.term+'&callback=?';

	jQuery.getJSON(apiURL,{rsz:settings.perPage,start:settings.page*settings.perPage},function(r){
		var results = r.responseData.results;	
		var to_return = {results:results,type:settings.type,q:settings.term,target:settings.target};
	
		if(settings.LP_call_back!=""){
			eval(settings.LP_call_back+"(to_return);");
		}
	});
}


/* TWITTER SEARCH */
function LP_twitter_search(settings){
    var twitter_data = {
		action : "LP_twitter_search"
	};
    
	settings = jQuery.extend({},twitter_data,settings);
	jQuery.post(ajaxurl,settings,function(r){
		result = jQuery.parseJSON(r);
		var to_return = {results:result.statuses,type:'twitter',q:settings.term,target:settings.target};
        if(settings.LP_call_back!=""){
            eval(settings.LP_call_back+"(to_return);");
        }
	});
}

var results_all;
function LP_combine_search_results(){
	var temp = new Array({tambling:0});
    if(results_news){
        temp = temp.concat(results_news);
    }
    if(results_blogs){
        temp = temp.concat(results_blogs);
    }
    if(results_tweets){
        temp = temp.concat(results_tweets);
    }
	results_all = temp;
    // alert(temp.length);
}

function LP_save_topic_collection_setup(){
	var topic = localStorage.getItem("topics_in_collection_setup");
	if(topic !== null){
		var search_sources 	={};
		var keywords		={};
		var rss_feeds		={};
		
		jQuery("#topic_collection_setup #search_source_cont .blue_button").each(function(i){
			var who = jQuery(this).attr("param");
			if(jQuery(this).hasClass("active")){
				search_sources[who] = 1;
			}else{
				search_sources[who] = 0;
			}
		});
		
		jQuery("#topic_collection_setup #keywords .topic_keyword").each(function(i){
			var k = jQuery.trim(jQuery(this).val());
			if(k != ""){
				keywords[i] = k;
			}
		});
		
		jQuery("#topic_collection_rss_setup #selected_rss .item").each(function(i){
			var link = jQuery.trim(jQuery(this).attr("param"));
			if(link != ""){
				var display_domain = jQuery("span > span",this).text();
				rss_feeds[i] = {
					display_domain	: display_domain,
					link			: link
				};
			}
		});
		
		var topic_collection_setup = {
			search_sources	: search_sources,
			keywords		: keywords,
			rss_feeds		: rss_feeds
		};
		
		var data = {
			action 					: "LP_save_topic_collection_setup",
			topic_id				: topic,
			topic_collection_setup	: topic_collection_setup
		}
		
		jQuery.post(ajaxurl,data,function(r){
			var updated_topic = jQuery.parseJSON(r);
			LP_update_localStorage_topic(updated_topic);
		});
	}
}

function LP_update_localStorage_topic(topic_obj){
	var u_topics = jQuery.parseJSON(localStorage.getItem("user_topics"));
	u_topics[topic_obj.ID] = topic_obj;
	localStorage.setItem("user_topics",JSON.stringify(u_topics));
}

function LP_get_localStorage_topic(topic_id){
	if(typeof topic_id != "string" && typeof topic_id!= "number"){
		topic_id = localStorage.getItem("topics_in_collection_setup");
	}	
	var u_topics = jQuery.parseJSON(localStorage.getItem("user_topics"));
	return u_topics[topic_id];
}

function LP_get_localStorage_drip(drip_id){
	var topic_id = localStorage.getItem("topics_in_collection_setup");
	var u_topics = jQuery.parseJSON(localStorage.getItem("user_topics"));
	var the_topic = u_topics[topic_id];
	var the_drip = the_topic.future_drips[drip_id];
	return the_drip;
}

function LP_update_localStorage_drip(drip_obj){
	var u_topics = jQuery.parseJSON(localStorage.getItem("user_topics"));
	for(var topic_id in drip_obj){
		var topic = drip_obj[topic_id];
		for(var drip_id in topic){
			var drip = topic[drip_id];
			u_topics[topic_id]["future_drips"][drip_id] = drip;
		}
	}
	
	localStorage.setItem("user_topics",JSON.stringify(u_topics));
}

function LP_preload_the_images(){
	var images = [
		sub+"wp-content/themes/linkedpost/images/topic_tabl.png",
		sub+"wp-content/themes/linkedpost/images/topic_tabr.png",
		sub+"wp-content/themes/linkedpost/images/topic_tabla.png",
		sub+"wp-content/themes/linkedpost/images/topic_tabra.png"
	];
	var c_images = images.length;
	for(var a=0; a< c_images; a++){
		var timage = images[a];
		preloaded_images[a] = {};
		preloaded_images[a] = new Image();
		preloaded_images[a].src = timage;
	}
}

/* ############################################### */
function sortFunction(a,b){ 
    var dateA;
	var dateB;
	if(a.hasOwnProperty("publishedDate")){
		dateA= new Date(a.publishedDate).getTime();
	}else if(a.hasOwnProperty("created_at")){
		dateA= new Date(a.created_at).getTime();
	}
	
	if(b.hasOwnProperty("publishedDate")){
			dateB= new Date(b.publishedDate).getTime();
	}else if(b.hasOwnProperty("created_at")){
		dateB= new Date(b.created_at).getTime();
	}
    // var dateB = new Date(b.date).getTime();
    return dateA > dateB ? -1 : 1;  
};

function reverseSortTopic(a,b){ 
    var aID = parseInt(a.ID);
	var bID = parseInt(b.ID);
    return bID > aID ? -1 : 1;  
};

function LP_console(msg){
    if(LP_debug === true)
        console.log(msg);
}

function LP_trim(string, totrim){
    var string = string.replace("/^"+totrim+"+/i", '');
    var string = string.replace("/"+totrim+"+$/i", '');
    return string;
}

// (function($) {
	// $.extend({
		// LP_animate: function(css,speed,callback) {
			// var setting = $.extend({
				// update_val: function(css) {}
			// });
			// var current_val = jQuery(this).css(css);
			// LP_debug = true;
			// LP_console(current_val);
		// }
	// });
// })(jQuery);