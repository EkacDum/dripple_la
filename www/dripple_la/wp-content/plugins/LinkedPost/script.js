// localStorage.clear();
localStorage.removeItem("user_socials");
localStorage.removeItem("user_topics");
var ajaxurl = linkedIn_AJAX.ajaxurl;
var user_nicename = linkedIn_AJAX.user_nicename;
var user_display_name = linkedIn_AJAX.user_display_name;
var is_forms = linkedIn_AJAX.is_forms;
var is_home = linkedIn_AJAX.is_home;
var LP_blog_has_messaging  = linkedIn_AJAX.LP_blog_has_messaging ;
var current_user = linkedIn_AJAX.current_user;
var time_zone = linkedIn_AJAX.time_zone;
var linkedInWindow;
// var sub = "http://localhost/vardynamic/";
// var sub = "http://sanyahaitun.com/";
var sub = linkedIn_AJAX.site_url;
// var search_sources = linkedIn_AJAX.search_sources;
var drip_settings = linkedIn_AJAX.drip_settings;

var select_hour = "";
var sel = "";

var LP_channel_settings;

var LP_user_topics;

var LP_debug = true;


// var gapi_key = "AIzaSyBSVSToM2ksdiLAavFpYho-QWf9APNfwuM";
var gapi_key = "AIzaSyDM4NO8BxDj-oruewHTHFpBmEBOWG0cSrc";
// var gapi_key = "AIzaSyBJs9OdhrZ96rRp86-ipDVH690STpblC8M";

var LP_delay_update_timeout;

for (var a=1; a<=12; a++){
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
for (var a=0; a<60; a+=5){
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
var d_days2 = new Array('sunday','monday','tuesday','wednesday','thursday','friday','saturday');

var drips;
var preloaded_images=[];
LP_preload_the_images();
var LP_dripzone_locked = false;
// loadjscssfile("https://www.google.com/jsapi", "js");
google.load("feeds", "1");
function feedsLoaded(){
//DOING NOTHING HERE..
}

jQuery("document").ready(function(){
    localStorage.setItem("current_user",current_user);
    // localStorage.getItem("current_user");

    LP_set_top_tabs();
    set_drips_con_width();
    jQuery(window).resize(set_drips_con_width);
    jQuery(".linkedIn_register_button").live("click",linkedin_register);
    jQuery(".FB_register_button").live("click",fb_register);
    jQuery(".TW_register_button").live("click",tw_register);
    jQuery(".slide_register_button").live("click",function(){
        jQuery(".signlogindivhead").animate({height:"toggle"},200);
    });
    jQuery(".linkedin_submit_reg").live("click",linkedin_submit_reg_form);
    jQuery(".linkedIn_login_button").live("click",linkedin_login);
    jQuery("#btnsubmituser").live("click",lwp_register);
    jQuery("#loginfldhead").live("click",lwp_login);
    jQuery("#btnsubmituser2").live("click",lwp_register_2);
    jQuery("ul#drip_days > li").live("click",LP_toggle_day);
    jQuery(".litymhddrip_addimg").live("click",LP_add_drip);
    jQuery(".t_h, .t_m, .t_ampm").live("change",LP_save_topic_meter);

    jQuery(".del_drip_time").live("click",LP_remove_drip_time);

    jQuery("ul#drip_nav li").live("click",drip_slideDown_page);
    jQuery(".arrowprev, .arrownext").live("click",drip_sliding_page);

    // jQuery("#adjust_preview textarea#drip_title, #adjust_preview textarea#the_adjust, #adjust_preview textarea#the_content").live("focusout",LP_delay_to_save);
    jQuery("#adjust_preview #update_drip").live("click",LP_update_drip);
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

    jQuery(".chan_lis i").live("click",LP_toggle_follow);

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
        LP_get_user_topics();
        // jQuery("#update_add_topic").live("click",LP_set_update_topic_form);
        jQuery(".update_topic_feat_img").live("change",LP_update_topic_uploader_submit);
        jQuery("#update_currenttopic").live("click",LP_update_topic_submit);
    }

    if(is_home){
        LP_fetch_home_data();
    }

    jQuery("i#toggle_list_view,i#toggle_default_view,i#toggle_tile_view").live("click",toggle_home_view);

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
    jQuery("#adjust_preview .adjust_topic_feat_img_div #frontflip").live("mouseenter",flip_the_preview_thumb);

    jQuery("#adjust_sched .today_icons i").live("click",function(){
        if(jQuery(".adjust_topic_feat_img_div > #frontflip").is(":visible")==true){
            flip_the_preview_thumb();
        }else{
            reverse_flip_the_preview_thumb();
        }
    });

    jQuery("#adjust_preview .backflip").live("mouseout",reverse_flip_the_preview_thumb);


    jQuery(".tile_feat_img_div, .imgblogpost").live("mouseenter",function(){
        var obj = jQuery(this);
        if(jQuery(this).hasClass("no-flip")==false){
            jQuery(obj).parent().flippy({
                duration: "300",
                verso: jQuery(obj).parent().find(".backx_flip").html(),
                onReverseFinish :function(i) {
                    // jQuery(".tile_feat_img_div, .imgblogpost").parent().removeAttr("style");
                }
            });
        }
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
                // jQuery(this).removeAttr("style");
                LP_change_splash_colors();
            },
            onFinish : function(){
                LP_set_topic_page();
            }
        });
    });

    jQuery("i.reverse_me, .reverse_flip").live("click",function(){
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
    //jQuery("#save_redrip, #save_buffer").live("click",LP_save_redrip);
    jQuery("#save_redrip, #skip_and_buffer, #make_a_buffer, #drip_now, #skip_and_drip_now").live("click",LP_save_redrip);
    jQuery("#goto_dripzone_2").live("click",LP_next_dz);
    jQuery(".back_dz").live("click",LP_back_dz);

    jQuery(".logmeout").live("click",function(){
        FB.logout();
        window.location.replace(sub+"llogin/?rtype=logout");
    });
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
        LP_suggest_images();
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

    //jQuery("ul#image_suggestions img").live("click",LP_set_drip_image);
//    jQuery(".b_slpash_right_images #right_form .cols img").live("click",LP_set_drip_image);

    jQuery(".redrip_form select#new_topic").live("change",function(){
        var topic_id = jQuery(this).val();
        LP_set_current_topic(topic_id);
        // localStorage.setItem("topics_in_collection_setup",topic_id);
        LP_set_active_tab();
        LP_reset_splash_screens();
        // LP_topic_rss_suggestions(topic_id, "LP_splash_bottom_rss_suggest");
    });

    jQuery("#bottom_main .unlock").live("click",function(){LP_unlock_splash("bottom");});
    jQuery("#t_splash_cont .unlock").live("click",function(){LP_unlock_splash("top");});

    jQuery(".add_fresh_drip").live("click",LP_fresh_drip_form);
    // jQuery(".b_slpash_right div.item").live("hover",function(){
    // LP_set_drip_zone(this);
    // });

    jQuery("#drip_zone textarea").live("change", LP_grow_textarea);
    jQuery("#drip_zone textarea").live("keyup", LP_grow_textarea);

    jQuery(".b_slpash_right .search_accept,.b_slpash_right .search_extra").live("click",function(){
        var load_extra = false;
        if(jQuery(this).hasClass("search_extra")) load_extra = true;
        // var obj = jQuery(this).parent().parent().parent();
        var obj = jQuery(this).parent().parent().parent().parent().parent().parent();
        LP_set_drip_zone(obj);
        LP_suggest_analysis(obj,load_extra);
        jQuery(".b_slpash_right div.item").removeClass("active");
        jQuery(obj).addClass("active");
    });
    jQuery("#search_from_article_suggestions").live("keyup",LP_search_from_article_suggestions);

    jQuery("#search_tabs span").live("click",function(){
        if(jQuery(this).hasClass("disabled") == false){
            var obj = jQuery(this);
            var who = jQuery(this).attr("id");
            jQuery("#bottom_main .cols").hide();
            jQuery("#bottom_main .cols."+who).show();
            jQuery("#search_tabs span").removeClass("active");
            jQuery(obj).addClass("active");
        }
    });

    jQuery(".the_cropper .drag_drop .cropper_file").live("change",LP_set_cropper_subject);
    jQuery(".the_cropper .url_catcher").live("input propertychange",function(){
        var g_url = jQuery(this).val();
        var url = GetUrlValue(g_url ,"imgurl");
        if(typeof url == "undefined"){
            LP_prepare_dripzone_image(g_url);
        }else{
            LP_prepare_dripzone_image(url);
        }
        jQuery("ul.image_suggestions li img.active").show().removeClass("active");
        jQuery(this).val("");
    });

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
        // handle: 'i',
        update : function(event, ui){
            LP_save_topic_collection_setup();
        }
    });

    jQuery( ".search_keywords .keywords" ).sortable({
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

    jQuery("#search_highlight div").live("click",LP_set_search_highlight);

    jQuery("#topic_collection_setup .topic_keyword").live("keypress",function(e){
        if(e.keyCode  ==  13){
            jQuery("#topic_collection_setup .results_news_tpl1").empty();
            var topic_id = localStorage.getItem("topics_in_collection_setup");
            // LP_save_topic_collection_setup();
            do_search(topic_id);
            jQuery("#topic_collection_setup .topic_add_keyword:not(.filter)").trigger("click");
        }
    });

    jQuery("#toggle_twitter_search").live("click",LP_user_has_twitter_token);
    jQuery("#toggle_gblog_search, #toggle_gnews_search, #toggle_dripple_search").live("click",LP_toggle_source_button);
    jQuery("#toggle_rss_search").live("click", LP_topic_rss_page);
    jQuery("#toggle_rss").live("click", LP_RSS_Show_List);
    jQuery("#btn_back_to_search").live("click", LP_topic_setup_page);
    jQuery("#topic_rss_keyword_preview").live("click", function(){
        jQuery("#topic_collection_rss_setup .results_news_tpl1").empty();
        LP_topic_rss_keyword_preview();
    });

    jQuery("#topic_collection_rss_setup .topic_keyword").live("keypress",function(e){
        if(e.keyCode  ==  13){
            jQuery("#topic_collection_rss_setup .results_news_tpl1").empty();
            LP_save_topic_collection_setup();
            LP_topic_rss_keyword_preview();
            jQuery("#topic_collection_rss_setup .topic_add_keyword").trigger("click");
        }
    });

    jQuery("#topic_collection_setup .topic_keyword, #topic_collection_rss_setup .topic_keyword").live("blur",function(){
        if(jQuery(this).val()!=""){
            LP_save_topic_collection_setup();
        }
    });
    jQuery("#topic_collection_rss_setup .swooosh_RSS").live("click",LP_swooosh_rss);
    jQuery(".expand_r, .expand_g").live("click",function(){
        if(jQuery(this).hasClass("down_arr_s")){
            jQuery(this)
                .parent()
                .addClass("expanded")
                .find(".content.body")
                .stop()
                .animate({height:"toggle"},100,function(){
                    //jQuery("#topic_collection_rss_setup .results_news_tpl1").mCustomScrollbar("update");
                });
            jQuery(this).removeClass("down_arr_s").addClass("up_arr_s");
        }else{
            jQuery(this)
                .parent()
                .removeClass("expanded")
                .find(".content.body")
                .stop()
                .animate({height:"toggle"},100,function(){
                    //jQuery("#topic_collection_rss_setup .results_news_tpl1").mCustomScrollbar("update");
                });
            jQuery(this).removeClass("up_arr_s").addClass("down_arr_s");
        }
    });

    jQuery(".expand_g_all, .expand_all_sections").live("click",function(){
        if(jQuery(this).hasClass("expand_g_all")){
            var has_class = jQuery(this).hasClass("down_arr_s");
            var the_parent = jQuery(this).parent().parent();
        }else{
            //            This is where the expand/collapse on all articles is happening...
            var has_class = jQuery("i:eq(0)",this).hasClass("down_arr_s");
            var the_parent = jQuery(this).parent();
        }

        if(has_class){
            jQuery(".item .content.body",the_parent).css("display","block");
            jQuery(".item",the_parent).addClass("expanded");
            jQuery(".down_arr_s",the_parent).removeClass("down_arr_s").addClass("up_arr_s");
        }else{
            jQuery(".item .content.body",the_parent).css("display","none");
            jQuery(".item",the_parent).removeClass("expanded");
            jQuery(".up_arr_s",the_parent).removeClass("up_arr_s").addClass("down_arr_s");
        }
    });


    jQuery(".topic_add_keyword").live("click",LP_add_keyword_input);
    jQuery("#search_scoop_results").live("keyup",function(){
        jQuery(".quick_search span").removeClass("active");
        LP_search_from_scoop(jQuery(this).val());
    });

    jQuery("#scoop_view i").live("click",function(){
        jQuery("#scoop_view i").removeClass("active");
        jQuery(this).addClass("active");
        LP_search_from_scoop(jQuery("#search_scoop_results").val());
    });

    jQuery(".quick_search span").live("click",function(){
        if(jQuery(this).hasClass("active")){
            jQuery(this).removeClass("active");
            jQuery("#topic_collection_setup .search_section_title").show();
            jQuery("#topic_collection_setup .cols div.item").show();
        }else{
            jQuery(".quick_search span").removeClass("active");
            jQuery(this).addClass("active");
            jQuery("#topic_collection_setup .search_section_title").hide();
            jQuery("#topic_collection_setup .cols div.item").show();
            var the_keyword = jQuery(this).contents(':not(span)').text();
            the_keyword = the_keyword.replace(/ /ig,"_");
            the_keyword = the_keyword.replace(".","");
            jQuery("#topic_collection_setup .source_search_section_title #"+the_keyword+"").show();
        }
        jQuery("#t_splash_google_suggestions").mCustomScrollbar("update");
    });
    jQuery("#search_rss_results").live("keyup",LP_search_from_rss);
    jQuery(".keywords .the_trash").live("click",LP_remove_keyword_input);
    jQuery("#topic_collection_rss_setup #selected_rss .the_trash").live("click",LP_remove_rss_from_list);
    jQuery("#topic_feeds_preview").live("click",LP_feeds_collections);



    jQuery(".drip_timzone #timezone_string").live("change",LP_save_topic_meter);
    /* **** END TOPIC COLLECTION SETUP **** */

    /* TOPIC TAB */
    jQuery(".topic_tab_group li").live("click",function(){
        jQuery(".topic_tab_group li").removeClass("active");
        jQuery(this).addClass("active");
        jQuery(".topic_tabs").removeClass("active");
        var indx = parseInt(jQuery(this).text()) - 1;
        localStorage.setItem("current_tab_group",indx+1);
        jQuery(".topic_tabs").eq(indx).addClass("active");
        jQuery("#bottom_main .topic_tabs").eq(indx).addClass("active");
    });

    jQuery(".topic_tabs .topic_tab,.topic_tabs .tab_info").live("click",function(){
        jQuery("ul.topic_tabs li").removeClass("active");
        // jQuery(this).parent().parent().addClass("active");

        var topic_id = jQuery(this).attr("param");
        LP_set_current_topic(topic_id);

        if(jQuery(".redrip_form").is(":visible")){
            jQuery("ul#image_suggestions").empty();
            LP_suggest_images();
        }

    });

    jQuery(".topic_tabs .topic_tab").live("mouseover",function(){
        var win_width = jQuery(window).width();
        var info_width = jQuery(this).next().width();
        var parent_pos = jQuery(this).parent().parent().position();
        var tot_width = info_width + parent_pos.left;
        if(tot_width >= (win_width-80)) jQuery(this).next().addClass("left");
        else jQuery(this).next().removeClass("left");
    })


    jQuery("#grpinddivlog,#bottom_main").live("click",function(){
        jQuery(".header_drag_containment,#bottom_main").css("z-index",1);
        if(jQuery(this).attr("id")=="bottom_main"){
            jQuery("#bottom_main").css("z-index",2);
        }else{
            jQuery(".header_drag_containment").css("z-index",2);
        }
    });

    jQuery(".header_drag_containment #t_splash_topic_tabs,#bottom_main #t_splash_topic_tabs").live("mousedown",function(){
        jQuery(".header_drag_containment,#bottom_main").css("z-index",1);
        if(jQuery(this).parent().attr("id")=="t_splash_cont"){
            jQuery(".header_drag_containment").css("z-index",2);
        }else{
            jQuery("#bottom_main").css("z-index",2);
        }
    });

    if(is_forms){
        jQuery(".splash_colors span").live("click",LP_change_splash_colors);
        jQuery("#new_topic_image, #current_topic_image").live("change",LP_new_topic_image);
        jQuery("#done_cropping").live("click",LP_done_cropping);
        jQuery("#done_cropping_2").live("click",LP_done_cropping_2);
        jQuery("#create_new_topic").live("click",LP_submit_add_topic);
        jQuery("#create_new_topic_back").live("click",LP_new_topic_back);
        // jQuery("#update_topic_back").live("click",LP_update_topic_back);
        jQuery(".body.current_topic .post_holder_addtopic.current .the_flipping input, .body.current_topic .post_holder_addtopic.current .the_flipping select").live("change",function(){
            clearTimeout(LP_delay_update_timeout);
            if(LP_dont_update === false){
                if(jQuery(this).attr("id")=="current_trash"){
                    LP_update_topic_back();
                }else{
                    LP_delay_update_timeout = setTimeout(LP_update_topic_back, 1000);
                }
            }
        });

        LP_fetch_ripple_socials();
    }

    /* END TOPIC TAB */
    // LP_reset_splash_screens();

    /* ADJUST PAGE */
    jQuery("#adjust_sched .adjust_topics").live("hover",update_adjust_preview);
    jQuery("#adjust_sched .adjust_topics").live("click",function(){
        if(is_froozen == false) is_froozen = true;
        else is_froozen = false;
    });
    jQuery(".expand_input, #adjust_preview textarea").live("click",LP_expand_input);
    jQuery(".delete_buffer").live("click",LP_delete_buffer);
    jQuery(".slide_delete_buffer").live("click",LP_slide_delete);
    jQuery(".delete_cancel").live("click",LP_slideout_delete);

    /* END ADJUST PAGE */
    jQuery.expr[':'].icontains = function(a, i, m) {
        return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
    };

    jQuery("input.autosize").live("keyup",function(){
        var nsize = (jQuery(this).val()).length;
        if(nsize<3)nsize=3;
        jQuery(this).attr("size",nsize);
    });



    jQuery("#notif_bar #delete_this_topic").live("click",LP_delete_this_topic);
    jQuery("#notif_bar #cancel").live("click",function(){
        jQuery("#notif_bar").hide().empty();
    });

    jQuery("#analysis_suggestions .item").live("click",LP_set_analysis_suggestion);

    /*	jQuery("#image_suggestions_main .arrow.left").live("click",function(){
     to_pos = parseInt(curr_position) + parseInt(page_width);
     jQuery("#image_suggestions_cont").mCustomScrollbar("scrollTo",to_pos);
     });

     jQuery("#image_suggestions_main .arrow.right").live("click",function(){
     to_pos = parseInt(curr_position) - parseInt(page_width);
     jQuery("#image_suggestions_cont").mCustomScrollbar("scrollTo",to_pos);
     });*/

});


/* ##################################################################################### */
/* ##################################################################################### */
/* ##################################################################################### */
/* ##################################################################################### */
/* ##################################################################################### */
/* ##################################################################################### */
/* ##################################################################################### */

function LP_change_splash_colors(){
    if(jQuery(this).hasClass("active")){
        jQuery(".splash_colors span").removeClass("active");
        jQuery(".dripgrpDIV,#headDIV, .redrip_form").css("background-color","");
        jQuery("#t_splash_topic_tabs,#bottom_main #t_splash_topic_tabs").css("background-color","");
        jQuery(".body.current_topic .the_flipping, .body.current_topic .back_flipper").css("background-color","#3B3C3D");
    }else{
        if(typeof jQuery(this) == "undefined"){
            var the_color = jQuery(".splash_colors span.active").css("background-color");
            var the_tab_color = jQuery(".splash_colors span.active").attr("param");
        }else{
            jQuery(".splash_colors span").removeClass("active");
            jQuery(this).addClass("active");
            var the_color = jQuery(this).css("background-color");
            var the_tab_color = jQuery(this).attr("param");
        }
        jQuery(".dripgrpDIV,#headDIV, .redrip_form").css("background-color",the_color);
        jQuery("#t_splash_topic_tabs,#bottom_main #t_splash_topic_tabs").css("background-color",the_tab_color);
        jQuery(".body.current_topic .the_flipping, .body.current_topic .back_flipper").css("background-color",the_color);
    }
}

var keyword_count = {};
function LP_set_quick_search(){
    var current_topic   = localStorage.getItem("topics_in_collection_setup");
    var setup           = LP_get_topic_collection_setup(current_topic);
    var search_keywords = setup.search_keywords;
    jQuery(".quick_search").empty();
    if(search_keywords !==null){
        jQuery.each(search_keywords, function(i,val){
            if(i<=9){
                if(val.charAt(0)!="-"){
                    keyword_count[val] = 0;
                    jQuery(".quick_search").append("<span><span class=\"keyword_counter\" param=\""+val+"\">0</span>"+val+"</span>");
                }
            }
        });
    }
}

function LP_set_search_highlight(){
    if(jQuery(this).hasClass("active")){
        jQuery(this).removeClass("active");
        jQuery("#search_highlight div").removeClass("active");
    }else{
        jQuery("#search_highlight div").removeClass("active");
        jQuery(this).addClass("active");
    }
    LP_highlight_search_results();
}

function LP_highlight_search_results(){
    var the_color = "#FFFFFF";
    if(jQuery("#search_highlight div.active").length >= 1){
        the_color = jQuery("#search_highlight div.active").css("background-color");
    }

    jQuery("#topic_collection_setup .results_news_tpl1 .item .content.body b").css("color",the_color);
    jQuery("#topic_collection_setup .results_news_tpl1 .search_section_title .item .title b").css("color",the_color);

    jQuery("#topic_collection_rss_setup .results_news_tpl1 .item .content.body b").css("color",the_color);
    jQuery("#topic_collection_rss_setup .results_news_tpl1 .search_section_title .item .title b").css("color",the_color);
}

function flip_the_preview_thumb(){
    if(jQuery(".adjust_topic_feat_img_div").hasClass("no-flip") == false){
        jQuery(".adjust_topic_feat_img_div").flippy({
            duration: "300",
            verso: jQuery(".adjust_topic_feat_img_div .backx_flip").html()
        });
    }
}

function reverse_flip_the_preview_thumb(){
    jQuery(".adjust_topic_feat_img_div").flippyReverse();
}

function LP_slide_delete(){
    var obj = jQuery(this);
    jQuery(obj).parent().find("i").hide();
    jQuery(obj).parent().find(".delete_buffer_cont").show().animate({"margin-right":0},50);
}

function LP_slideout_delete(){
    var obj = jQuery(this);
    jQuery(obj).parent().animate({"margin-right":-125},50,function(){
        jQuery(obj).parent().parent().find("i").show();
    });
}

function LP_delete_buffer(){
    var data = {
        action	: "LP_delete_buffer",
        post_id : jQuery(this).attr("id")
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
            jQuery("#adjust_preview input#post_id").val("");
        }
    });
}

function LP_expand_input(){
    var obj = jQuery(this);
    var tagname = jQuery(obj).tagName;
    var t_textarea = jQuery(obj).parent().find("textarea");
    var t_row = jQuery(t_textarea).attr("param");
    if(typeof t_row == "undefined"){
        t_row = jQuery(t_textarea).attr("rows");
        jQuery(t_textarea).attr("param",t_row);
    }
    var cols = jQuery(t_textarea).attr("cols");
    var rows = jQuery(t_textarea).attr("rows");
    var txt_id = jQuery(t_textarea).attr("id");

    //Collapse other textarea 
    if(txt_id =="the_content"){
        var l_height= 16;
        jQuery("textarea#the_adjust").animate({height:"48px"},200,function(){
            jQuery(this).attr("rows",3);
        })
            .parent().parent().find("i.expand_inputs-down-s").removeClass("up");

        jQuery("textarea#drip_title").animate({height:"16px"},200,function(){
            jQuery(this).attr("rows",1);
        })
            .parent().find("i.expand_inputs-down-s").removeClass("up");

    }else if(txt_id =="the_adjust"){
        var l_height= 16;
        jQuery("textarea#the_content").animate({height:"48px"},200,function(){
            jQuery(this).attr("rows",3);
        })
            .parent().find("i.expand_inputs-down-s").removeClass("up");

        jQuery("textarea#drip_title").animate({height:"16px"},200,function(){
            jQuery(this).attr("rows",1);
        })
            .parent().find("i.expand_inputs-down-s").removeClass("up");
    }else if(txt_id =="drip_title"){
        var l_height= 16;
        jQuery("textarea#the_content").animate({height:"48px"},200,function(){
            jQuery(this).attr("rows",3);
        })
            .parent().find("i.expand_inputs-down-s").removeClass("up");

        jQuery("textarea#the_adjust").animate({height:"48px"},200,function(){
            jQuery(this).attr("rows",3);
        })
            .parent().parent().find("i.expand_inputs-down-s").removeClass("up");
    }

    if(t_row == rows || jQuery(obj).is("textarea") == true){
        var str = jQuery(t_textarea).val();

        var linecount = 0;
        jQuery.each(str.split("\n"), function(i,v) {
            linecount += Math.ceil( v.length / cols ); // take into account long lines
        });

        var lc = linecount + 1;
        if(lc < 3) lc = 3;
        if(txt_id == "the_adjust"){
            jQuery(t_textarea).parent().parent().find("i.expand_inputs-down-s").addClass("up");
        }else{
            jQuery(t_textarea).parent().find("i.expand_inputs-down-s").addClass("up");
        }
        jQuery(t_textarea).focus().stop().animate({height:(lc*l_height)+"px"},200,function(){
            jQuery(this).attr("rows",lc);
        });
    }else{
        jQuery("i.expand_inputs-down-s",obj).removeClass("up");
        jQuery(t_textarea).blur().animate({height:(t_row*l_height)+"px"},200,function(){
            jQuery(this).attr("rows",t_row);
        });
    }
}

/* search from results */
function LP_search_from_article_suggestions(){
    var kword = jQuery(this).val();
    if(kword != ""){
        jQuery(".b_slpash_right .cols div.item").hide();
        jQuery(".b_slpash_right .cols div.item span.title:icontains('"+kword+"')").parent().parent().show();
    }else{
        jQuery(".b_slpash_right .cols div.item").show();
    }
}

function LP_search_from_scoop(search_text){
    var kword = "";
    if(typeof search_text == "string" || typeof search_text == "number"){
        kword = search_text;
    }else{
        kword = jQuery(this).val();
    }
    var view_type = jQuery("#scoop_view i.active").hasClass("section");
    if(kword != ""){
        if(view_type == true){
            jQuery("#topic_collection_setup .cols .search_section_title.list").remove();
            jQuery("#topic_collection_setup .cols .search_section_title").show();
            jQuery("#topic_collection_setup .cols div.item").hide();
            jQuery("#topic_collection_setup .cols div.item span.title:icontains('"+kword+"')").parent().parent().show();
        }else{
            jQuery("#topic_collection_setup .cols .search_section_title").hide();
            jQuery("#topic_collection_setup .cols .search_section_title.list").remove();
            jQuery("#topic_collection_setup .cols .source_search_section_title")
                .each(function(i,v){
                    var obj = jQuery(this);
                    var the_source = jQuery(obj).attr("param");
                    var the_id = jQuery(obj).attr("id")+"_list";
                    var the_section = "<div id=\""+the_id+"\" class=\"search_section_title list\">\
										<div class=\"source_keyword\">\
											<i class=\"magnify-d-21\"></i>\
											<span>"+the_source+"</span>\
											<i class=\"down_arr_s expand_g_all\"></i>\
										</div>";
                    jQuery(obj).append(the_section);

                    var the_res = [];
                    jQuery("div.item span.title:icontains('"+kword+"')",obj).each(function(a,val){
                        var to_key = jQuery(this).parent().parent().attr("param");
                        var item = jQuery(this).parent().parent().clone();
                        the_res[a] = {date:to_key,item:item};
                    });

                    the_res.sort(function(a,b){
                        return b.date - a.date;
                    });

                    jQuery(the_res).each(function(x,val){
                        jQuery("#topic_collection_setup #"+the_id,obj).append(val.item);
                    });
                });
        }
    }else{
        if(view_type == true){
            jQuery("#topic_collection_setup .cols .search_section_title.list").remove();
            jQuery("#topic_collection_setup .cols .search_section_title").show();
            jQuery("#topic_collection_setup .cols div.item").show();
        }else{
            jQuery("#topic_collection_setup .cols .search_section_title").hide();
            jQuery("#topic_collection_setup .cols .search_section_title.list").remove();
            jQuery("#topic_collection_setup .cols .source_search_section_title")
                .each(function(i,v){
                    var obj = jQuery(this);
                    var the_source = jQuery(obj).attr("param");
                    var the_id = jQuery(obj).attr("id")+"_list";
                    var the_section = "<div id=\""+the_id+"\" class=\"search_section_title list\">\
										<div class=\"source_keyword\">\
											<i class=\"magnify-d-21\"></i>\
											<span>"+the_source+"</span>\
											<i class=\"down_arr_s expand_g_all\"></i>\
										</div>";
                    jQuery(obj).append(the_section);

                    var the_res = [];
                    jQuery("div.item",obj).each(function(a,val){
                        var to_key = jQuery(this).attr("param");
                        var item = jQuery(this).clone();
                        the_res[a] = {date:to_key,item:item};
                    });

                    the_res.sort(function(a,b){
                        return b.date - a.date;
                    });

                    jQuery(the_res).each(function(x,val){
                        jQuery("#topic_collection_setup #"+the_id,obj).append(val.item);
                    });
                });
        }
    }
    jQuery("#topic_collection_setup .results_news_tpl1").mCustomScrollbar("update");
}

function LP_search_from_rss(key){
    var kword = jQuery(this).val();
    if(kword != ""){
        if(LP_is_URL(kword)){
            var feed = new google.feeds.Feed(kword);

            if(feed.hasOwnProperty("error") == false && jQuery("#selected_rss .item[param='"+kword+"']").length == 0 && key.which == 13){
                jQuery("#topic_collection_rss_setup .cols div.item").show();
                feed.setNumEntries(10);
                feed.load(LP_swooosh_Feed);
            }else{
                jQuery("#topic_collection_rss_setup .cols div.item").hide();
                jQuery("#topic_collection_rss_setup .cols div.item span.title:icontains('"+kword+"')").parent().parent().show();
            }
        }else{
            jQuery("#topic_collection_rss_setup .cols div.item").hide();
            jQuery("#topic_collection_rss_setup .cols div.item span.title:icontains('"+kword+"')").parent().parent().show();
        }
    }else{
        jQuery("#topic_collection_rss_setup .cols div.item").show();
    }
    jQuery("#topic_collection_rss_setup .results_news_tpl1").mCustomScrollbar("update");
}

function LP_swooosh_Feed(res){
    var feed     = res.feed;

    var domain = feed.link.replace("www.","");
    domain = domain.replace("http://","");
    domain = domain.replace("https://","");
    var the_domain = domain.split("/");
    var the_feed = {
        link		: feed.feedUrl,
        title		: feed.title,
        keyword		: "",
        the_img		: "https://www.google.com/s2/favicons?domain="+the_domain[0],
        the_domain	: the_domain[0]
    };
    LP_swooosh_rss(the_feed);
}
/* END search from results */

function LP_grow_textarea(){
    var obj 		= jQuery(this);
    jQuery(obj).css("height","");
    var str 		= jQuery(obj).val();
    var cols 		= parseInt(jQuery(obj).attr("cols"));
    var line_height = parseInt(jQuery(obj).css("line-height"));
    var linecount   = 0;
    var sh = jQuery(obj).prop('scrollHeight');
    // jQuery.each(str.split("\n"), function(i,v) {
    // linecount += Math.ceil( v.length / cols )+1;
    // });
    // var new_height = linecount * line_height;
    var new_height = sh;
    if(new_height < line_height) new_height = line_height;
    jQuery(obj).height(new_height);
    if(jQuery(obj).attr("id") == "ripple_content"){
        jQuery(".redrip_form .inblogpostbody #analysis_back").height(new_height);
    }
    jQuery("#ripple_content_cont").mCustomScrollbar("update");
}

function LP_set_drip_zone(obj){
    if(LP_dripzone_locked == false){
        var story_URL = jQuery(".story_URL",obj).attr("href");
        var title = jQuery(".title",obj).text();
        var content = LP_trim(jQuery(".item_content",obj).val()," ");
        var img_url = jQuery(".img_cont img",obj).attr("src");

        jQuery(".redrip_form .imgblogpostx > img").attr("src",img_url);
        LP_set_bcropper_images(img_url);
        jQuery(".redrip_form .inblogpostbody #ripple_title").val(title).trigger("change");
        jQuery(".redrip_form .inblogpostbody #ripple_content").val(content).trigger("change");
        jQuery(".redrip_form .inblogpostbody #story_URL").val(story_URL);
        jQuery(".redrip_form .inblogpostbody #img1").val(img_url);
    }
}

function LP_set_bcropper_images(img_url){
    if(typeof img_url !="undefined"){
        jQuery(".redrip_form #drip_zone_2 img#cropper_subject").removeAttr("style").attr("src",img_url);
        LP_drip_zone2(img_url);
    }
}

function LP_suggest_analysis(obj, load_extra){
    var url = jQuery(".story_URL",obj).attr("href");
    LP_console(url);
    LP_parse_site(url, "p", load_extra);
}

function LP_parse_site(url, tag, load_extra){
    var data = {
        action 	: "LP_parse_site",
        url		: url,
        tag		: tag
    };
    jQuery.post(ajaxurl,data,function(r){
        var res = jQuery.parseJSON(r);
        var extra_texts      = jQuery(".redrip_form .inblogpostbody #ripple_content").val();
        var a = 0;
        jQuery.each(res.p, function(i,v){
            var val = jQuery("<div/>").html(v).text();
            if(val.length > 30 && a < 2){
                a++;
                if(extra_texts.length > 0 && a==1){
                    extra_texts = extra_texts+"\n\r";
                }
                extra_texts+= val+"\n\r";
                delete res.p[i];
            }
        });
        LP_set_analysis_suggestions(res);
        if(load_extra == true){
            jQuery(".redrip_form .inblogpostbody #ripple_content").val(extra_texts).trigger("change");
            jQuery("#bottom_main #t_splash_topic_tabs").stop().animate({top:0},200);
            jQuery("#bottom_main #redrip_height").stop().animate({height:75},200,function(){
                var win_h = jQuery(window).height();
                var offset = jQuery(".results_news_tpl1.b_splash").offset();
                var n_h = win_h - offset.top - 15;

                jQuery(".results_news_tpl1.b_splash").height(n_h);
                jQuery(".results_news_tpl1.b_splash").mCustomScrollbar("update");
                setTimeout(LP_update_scroll_ripple_content_cont,500);
            });
        }
    });
}

function LP_update_scroll_ripple_content_cont(){
    jQuery("#ripple_content_cont").mCustomScrollbar("update");
}

function LP_set_analysis_suggestion(){
    var analysis = jQuery(this).text();
    jQuery(".redrip_form .inblogpostbody #analysis_back").html(analysis);
    jQuery(".redrip_form .inblogpostbody #analysis").val(analysis).trigger("change");
}

function LP_set_analysis_suggestions(suggestions){
    var counter = 0;
    jQuery("#analysis_suggestions").mCustomScrollbar("destroy");
    jQuery("#analysis_suggestions").empty().append("<div class=\"source_keyword gradient\"><i class=\"magnify-d-21\"></i><span>Story Analyser</span></div>");
    // jQuery.each(suggestions.p,function(i, v){
    for (var i in suggestions.p){
        var v = suggestions.p[i];
        var val = jQuery("<div/>").html(v).text();
        val = val.match(/(.+?)[.!?]/g);
        if(val != "null" && val != null && typeof val !="null"){
            if(counter < 10){
                if(val[0].length > 60){
                    counter++;
                    jQuery("#analysis_suggestions").append("<div class=\"item\">"+val[0]+" <div class=\"right_pointer\"></div></div>");
                }
            }else{
                return true;
            }
        }
    }
    // });

    if(counter == 0){
        jQuery("#analysis_suggestions").append("<div class=\"empty\">No Suggestions Found</div>");
    }

    jQuery("#analysis_suggestions").mCustomScrollbar({
        theme:"dark-thick",
        mouseWheelPixels: 200,
        scrollInertia:10
    });
}

function LP_set_active_tab(){
    var topic_id = localStorage.getItem("topics_in_collection_setup");
    jQuery("#t_splash_topic_tabs li").removeClass("active");
    jQuery("#t_splash_topic_tabs li div span.topic_tab[param='"+topic_id+"']").parent().parent().addClass("active");
    jQuery("#bottom_main #t_splash_topic_tabs li").removeClass("active");
    jQuery("#bottom_main #t_splash_topic_tabs span.topic_tab[param='"+topic_id+"']").parent().parent().addClass("active");
}

function LP_is_topic_complete(topic_id){
    if(typeof topic_id != "string" && typeof topic_id!= "number"){
        topic_id = localStorage.getItem("topics_in_collection_setup");
    }
    var topic = LP_get_localStorage_topic(topic_id);
    if(typeof topic == "undefined"){
        LP_correct_topics_in_collection_setup();
        return LP_is_topic_complete();
    }
    var collection_setup = JSON.parse(topic.collection_setup);
    var channel = topic.channel_id;
    if((typeof collection_setup.rss_feed_links.length == "undefined" && typeof collection_setup.search_keywords.length == "undefined") || channel == ""){
        return false;
    }else{
        return true;
    }
}

function LP_correct_topics_in_collection_setup(){
    var u_topics = jQuery.parseJSON(localStorage.getItem("user_topics"));
    for(var k in u_topics){
        var ID = u_topics[k].ID;
        break;
    }
    LP_set_current_topic(ID);
}

function LP_set_drip_button(){
    if(LP_is_topic_complete()){
        jQuery("i.add_fresh_drip").removeClass("incomplete");
    }else{
        jQuery("i.add_fresh_drip").addClass("incomplete");
    }
}

function LP_set_current_topic(topic_id){
    localStorage.setItem("topics_in_collection_setup",topic_id);
    jQuery("#notif_bar").hide().empty();
    LP_reset_splash_screens();
    LP_set_topic_colors();
    LP_set_drip_button();
    jQuery("ul.topic_tabs li span.topic_tab[param='"+topic_id+"']").parent().parent().addClass("active");
}

function LP_reset_splash_screens(){
    LP_init_topic_collection_forms();
    LP_bsplash_suggest();
    LP_reset_meter();
    LP_set_adjust_page();
    LP_set_topic_page();
    LP_set_ripple_page();
}

function LP_generate_topic_stickydrip_options(){
    var the_topic = LP_get_localStorage_topic();

    var future_drips = the_topic.future_drips;
    var history_drips = the_topic.history_drips;
    if(typeof future_drips != "undefined"){
        for (var key in future_drips){
            var future_drip = future_drips[key];
        }
    }
}

var LP_dont_update = true;
function LP_set_topic_page(){
    var the_topic = LP_get_localStorage_topic();
    LP_generate_topic_stickydrip_options();
    var thumb 	= the_topic.images["lp-topic-medium"];
    var tite 	= the_topic.post_title;
    var content = the_topic.post_content;
    var channel = the_topic.name;

    jQuery(".body.current_topic #mcurrent_topic_image").attr("style","").attr("src",thumb);
    jQuery(".body.current_topic #current_topic_channel").empty().text(channel);
    jQuery(".body.current_topic #current_topic_title").val(tite);
    jQuery(".body.current_topic #current_topic_content").val(content);
    LP_dont_update = true;
    /* BACK FLIP */
    if(the_topic.hasOwnProperty("channel_id")){
        jQuery(".body.current_topic #current_topic_channel_back").val(the_topic.channel_id).trigger("change");
    }else{
        jQuery(".body.current_topic #current_topic_channel_back").val(0).trigger("change");
    }

    if(the_topic.post_fields.hasOwnProperty("_stiky_drip"))
        jQuery(".body.current_topic #current_stiky_drip").val(the_topic.post_fields._stiky_drip[0]).trigger("change");
    else
        jQuery(".body.current_topic #current_stiky_drip").val(0).trigger("change");

    if(the_topic.post_fields.hasOwnProperty("_industry"))
        jQuery(".body.current_topic #current_industry").val(the_topic.post_fields._industry[0]).trigger("change");
    else
        jQuery(".body.current_topic #current_industry").val(0).trigger("change");

    if(the_topic.post_fields.hasOwnProperty("_language"))
        jQuery(".body.current_topic #current_language").val(the_topic.post_fields._language[0]).trigger("change");
    else
        jQuery(".body.current_topic #current_language").val(0).trigger("change");

    if(the_topic.post_fields.hasOwnProperty("_results"))
        LP_set_radio(".body.current_topic #current_results",the_topic.post_fields._results[0]);
    else
        LP_set_radio(".body.current_topic #current_results", false);

    if(the_topic.post_fields.hasOwnProperty("_messages"))
        LP_set_radio(".body.current_topic #current_message",the_topic.post_fields._messages[0]);
    else
        LP_set_radio(".body.current_topic #current_message", false);


    if(the_topic.post_fields.hasOwnProperty("_iframe"))
        LP_set_radio(".body.current_topic #current_iframe",the_topic.post_fields._iframe[0]);
    else
        LP_set_radio(".body.current_topic #current_iframe", false);


    if(the_topic.post_fields.hasOwnProperty("_dripurl"))
        LP_set_radio(".body.current_topic #current_dripurl",the_topic.post_fields._dripurl[0]);
    else
        LP_set_radio(".body.current_topic #current_dripurl", false);


    /* if(the_topic.post_fields.hasOwnProperty("_trash"))
     LP_set_radio(".body.current_topic #current_trash",the_topic.post_fields._trash[0]);
     else*/
    LP_set_radio(".body.current_topic #current_trash", false);


    if(the_topic.post_fields.hasOwnProperty("_private"))
        LP_set_radio(".body.current_topic #current_private",the_topic.post_fields._private[0]);
    else
        LP_set_radio(".body.current_topic #current_private", false);


    if(the_topic.post_fields.hasOwnProperty("_flip"))
        LP_set_radio(".body.current_topic #current_flip",the_topic.post_fields._flip[0]);
    else
        LP_set_radio(".body.current_topic #current_flip", false);


    if(the_topic.post_fields.hasOwnProperty("_timezone"))
        LP_set_radio(".body.current_topic #current_timezone",the_topic.post_fields._timezone[0]);
    else
        LP_set_radio(".body.current_topic #current_timezone", false);


    jQuery(".body.current_topic div.custom_checkbox").remove();
    jQuery(".body.current_topic input.CR").addClass("custom_checkbox").removeClass("CR");
    LP_custom_checkbox();

    LP_dont_update = false;
}

function LP_set_radio(radio,onoff)
{
    if(onoff == false || onoff == "false"){
        jQuery(radio).removeAttr("checked");
    }else{
        jQuery(radio).attr("checked","checked");
    }
}

function LP_update_topic_back(){
    if(LP_dont_update === false){
        var data = {
            action				: "LP_update_topic_settings",
            topic				: localStorage.getItem("topics_in_collection_setup"),
            LP_channel 			: jQuery(".body.current_topic #current_topic_channel_back").val(),
            new_stiky_drip 		: jQuery(".body.current_topic #current_stiky_drip").val(),
            new_industry 		: jQuery(".body.current_topic #current_industry").val(),
            new_language 		: jQuery(".body.current_topic #current_language").val(),
            new_results 		: jQuery(".body.current_topic #current_results").is(":checked"),
            new_messages 		: jQuery(".body.current_topic #current_message").is(":checked"),
            new_iframe 			: jQuery(".body.current_topic #current_iframe").is(":checked"),
            new_dripurl 		: jQuery(".body.current_topic #current_dripurl").is(":checked"),
            new_trash 			: jQuery(".body.current_topic #current_trash").is(":checked"),
            new_private 		: jQuery(".body.current_topic #current_private").is(":checked"),
            new_flip 			: jQuery(".body.current_topic #current_flip").is(":checked"),
            new_timezone 		: jQuery(".body.current_topic #current_timezone").is(":checked")
        };

        if(data.new_trash == "true" || data.new_trash === true){
            LP_notify_delete_topic(data.topic);
        }else{
            if(data.LP_channel > 0 && (data.topic != "" || typeof data.topic != 'undefined')){
                jQuery.post(ajaxurl,data,function(r){
                    if(r!="0"){
                        var updated_topic = jQuery.parseJSON(r);
                        LP_update_localStorage_topic(updated_topic);
                    }
                });
            }
        }
    }
}

function LP_notify_delete_topic(topic){
    var tpl = "<span>Are you sure you want to delete this topic?</span><span class=\"topic_btn\" id=\"delete_this_topic\">Yes</span><span class=\"topic_btn\" id=\"cancel\">No</span>";
    jQuery("#notif_bar").empty().append(tpl).fadeIn(100);
}

function LP_populate_topic_tabs(){
    jQuery("#t_splash_cont #t_splash_topic_tabs ul.topic_tabs").remove();
    jQuery("#t_splash_cont #t_splash_topic_tabs ul.topic_tab_group").empty();
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
    var current_topic = localStorage.getItem("topics_in_collection_setup");
    jQuery("#t_splash_cont #t_splash_topic_tabs").append("<ul class=\"topic_tabs\"></ul>");
    jQuery.each(u_topics,function(i, v){
        var isactive = "";
        if(v.ID == current_topic){
            var isactive = "active";
        }

        if(aa>=10){
            jQuery("#t_splash_cont #t_splash_topic_tabs").append("<ul class=\"topic_tabs\"  class=\""+isactive+"\"></ul>");
            aa = 0;
            groups++;
            if(groups >= 2){
                if(groups==2){
                    jQuery("#t_splash_cont #t_splash_topic_tabs ul.topic_tab_group").prepend("<li param=\"1\">1</li>");
                }
                jQuery("#t_splash_cont #t_splash_topic_tabs ul.topic_tab_group").prepend("<li param=\""+groups+"\">"+groups+"</li>");
            }
        }

        var stats = '';
        if(typeof v.drip_stats == 'object'){
            stats = "<div class=\"stats\">"+v.drip_stats.buffered+" buffered / "+v.drip_stats.days+" days / "+v.drip_stats.dripped+" drips</div>";
        }

        var item = "<li class=\""+isactive+"\">\
						<div>\
							<span param=\""+v.ID+"\" class=\"topic_tab\">"+v.short_name+"\
								<span class=\"arrow_down\">&#9660;</span>\
								<span class=\"arrow_up\">&#9650;</span>\
							</span>\
							<div param=\""+v.ID+"\" class=\"tab_info\">"+stats+"\
								<span><i class=\"topic_drip_tab_"+(aa+1)+"\"></i>"+v.post_title+"</span>\
							</div>\
						</div>\
					</li>";
        jQuery("#t_splash_cont #t_splash_topic_tabs ul.topic_tabs:last").append(item);

        if(isactive == "active") jQuery("#t_splash_cont #t_splash_topic_tabs ul.topic_tabs:last").addClass("active");

        if(aa>=10)aa=0; else aa++;
    });
    LP_set_topic_colors();
}

function LP_set_topic_colors(){
    var the_topic_color = jQuery("#t_splash_cont .topic_tabs.active li.active .topic_tab").css("color");
    jQuery(".contbtndrip").css("color","#FFFFFF");
    jQuery(".contbtndrip.active").css("color",the_topic_color);
    jQuery("#notif_bar, .bottom_notif_bar").css("background-color",the_topic_color);
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
            theme:"dark-thick",
            mouseWheelPixels: 200,
            scrollInertia:10
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
            item.with_extra = true;
            the_item+= LP_news_res_tpl1(item);
        }

        var domain = result.feed.feedUrl;
        domain = domain.replace("www.","");
        domain = domain.replace("http://","");
        domain = domain.replace("https://","");
        var sdomain = domain.split("/");

        jQuery(".redrip_form #b_splash_rss_suggestions").append("<div class=\"search_section_title\"><div class=\"source_keyword gradient\"><img src=\"https://www.google.com/s2/favicons?domain="+result.feed.feedUrl+"\" /><span>"+sdomain[0]+"</span><span>RSS</span></div>"+the_item+"</div>");
        jQuery(".redrip_form #b_splash_rss_suggestions").mCustomScrollbar("destroy");
        jQuery(".redrip_form #b_splash_rss_suggestions").mCustomScrollbar({
            theme:"dark-thick",
            mouseWheelPixels: 200,
            scrollInertia:10
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
        LP_set_current_topic(topic_id);
    }else{
        var ch = false;
        var the_t = jQuery(this);
        var topic;
        while(ch===false){
            the_t = jQuery(the_t).parent();
            if(jQuery(the_t).hasClass("post_holder_addtopic")){
                topic = jQuery(the_t).attr("param");
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
    LP_set_rss_links();
    LP_set_search_source_button();
    LP_set_search_keywords();
    var topic_id = localStorage.getItem("topics_in_collection_setup");
    do_search(topic_id);
}

function LP_set_rss_links(){
    jQuery("#topic_collection_rss_setup #selected_rss").empty();
    /* jQuery("#topic_collection_rss_setup .keywords").empty(); 
     jQuery("#topic_collection_rss_setup .topic_add_keyword").trigger("click");*/
    jQuery("#topic_collection_rss_setup .results_news_tpl1").empty();
    var setup = LP_get_topic_collection_setup();
    var rss_feed_links = setup.rss_feed_links;
    jQuery.each(rss_feed_links, function(i,val){
        var the_short	= "";
        if(val.hasOwnProperty("display_domain")){
            the_title = "";
            if(val.hasOwnProperty("title")) the_title = val.title.replace(/'/ig,"");
            if(val.display_domain.length <= 15) the_short = "short";
            var favico = "https://www.google.com/s2/favicons?domain="+val.display_domain;
            var args = {
                link			: val.link,
                display_domain	: val.display_domain,
                title			: the_title,
                keyword			: val.keyword,
                the_short		: the_short,
                favico			: favico
            };
            var the_swoosh = LP_rss_tpl(args);
            jQuery("#topic_collection_rss_setup #selected_rss").append(the_swoosh);
        }
    });
    LP_show_hide_selected_rss();
}

function LP_rss_tpl(args){
    // var the_swoosh = "<div class=\"item\" param=\""+args.link+"\">\
    // <span class=\"args\" param=\"{display_domain:'"+args.display_domain+"',link:'"+args.link+"',title:'"+args.title+"',keyword:'"+args.keyword+"'}\">\
    // <i class=\"greycross-d-21 rss-drag-ico the_move\"></i>\
    // <span class=\""+args.the_short+"\">"+args.display_domain+"</span>\
    // </span>\
    // <div>\
    // <i class=\"trash-d-18 the_trash\"></i>\
    // <img src=\""+args.favico+"\">\
    // </div>\
    // </div>";
    // return the_swoosh;

    var the_args = {
        display_domain	: args.display_domain,
        link			: args.link,
        title			: args.title,
        keyword			: args.keyword
    };

    var the_input_args = jQuery("<input>").attr({
        type: 'hidden',
        class: 'args',
        value: JSON.stringify(the_args)
    });
    var the_swoosh = jQuery("<div>").attr({class:"item",param:args.link})
        .append("<span>\
								<i class=\"greycross-d-21 rss-drag-ico the_move\"></i>\
								<span class=\""+args.the_short+"\">"+args.display_domain+"</span>\
							</span>\
							<div>\
								<i class=\"trash-d-18 the_trash\"></i>\
								<img src=\""+args.favico+"\">\
							</div>")
        .append(the_input_args);
    return the_swoosh;
}

function LP_set_search_keywords(rssonly){
    jQuery("#topic_collection_rss_setup .keywords").empty();
    var setup = LP_get_topic_collection_setup();
    var rss_keywords = setup.rss_keywords;
    var search_keywords = setup.search_keywords;

    if(typeof rssonly != "boolean"){
        jQuery("#topic_collection_setup .keywords").empty();
        if(search_keywords !==null){
            jQuery.each(search_keywords, function(i,val){
                if(i<=9){
                    var the_key = LP_keyword_input_tpl1(val);
                    jQuery("#topic_collection_setup .keywords").append(the_key);
                }
            });
        }else{
            var the_key = LP_keyword_input_tpl1("");
            jQuery("#topic_collection_setup .keywords").append(the_key);
        }
    }

    if(rss_keywords !==null && typeof rss_keywords != 'undefined'){
        jQuery.each(rss_keywords, function(i,val){
            if(i<=9){
                var the_key = LP_keyword_input_tpl1(val);
                jQuery("#topic_collection_rss_setup .keywords").append(the_key);
            }
        });
    }else{
        if(search_keywords !==null){
            jQuery.each(search_keywords, function(i,val){
                if(i<=9){
                    var the_key = LP_keyword_input_tpl1(val);
                    jQuery("#topic_collection_rss_setup .keywords").append(the_key);
                }
            });
        }else{
            var the_key = LP_keyword_input_tpl1("");
            jQuery("#topic_collection_rss_setup .keywords").append(the_key);
        }
    }
}

function LP_keyword_input_tpl1(val){
    var filter_class = "";
    if(val.charAt(0)=="-"){
        filter_class = "filter";
        val = val.substring(1);
    }

    var the_input = "<div class=\""+filter_class+"\">\
						<span class=\"the_handle\">\
							<i class=\"greycross-d-18 the_move\"></i>\
							<input class=\"btn2 topic_keyword "+filter_class+"\" type=\"text\" placeholder=\"keyword\" value=\""+val+"\"/>\
						</span>\
						<i class=\"key_trash-l-18\">\
							<i class=\"key-l-18\"></i>\
							<i class=\"trash-d-18 the_trash\"></i>\
						</i>\
					</div>";
    return the_input;
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
								<span class=\"btn2 source "+active+"\" id=\"toggle_gnews_search\" param=\"news\">Google News</span>\
							</span>";
                    jQuery("#topic_collection_setup #search_source_cont").append(tb);
                    break;

                case "blogs":
                    var tb = "<span>\
								<span class=\"btn2 source "+active+"\" id=\"toggle_gblog_search\" param=\"blogs\">Google Blogs</span>\
							</span>";
                    jQuery("#topic_collection_setup #search_source_cont").append(tb);
                    break;

                case "twitter":
                    var tb = "<span>\
								<span class=\"btn2 source "+active+"\" id=\"toggle_twitter_search\" param=\"twitter\">Twitter Search</span>\
							</span>";
                    jQuery("#topic_collection_setup #search_source_cont").append(tb);
                    break;

                case "dripple":
                    var tb = "<span>\
								<span class=\"btn2 source "+active+"\" id=\"toggle_dripple_search\" param=\"dripple\">Dripple Search</span>\
							</span>";
                    jQuery("#topic_collection_setup #search_source_cont").append(tb);
                    break;
            }
        }

        var rss_feed_links = setup.rss_feed_links;
        if(rss_feed_links !==null && typeof rss_feed_links != 'undefined' && rss_feed_links.length > 0){
            jQuery("#topic_collection_setup #toggle_rss_search").addClass("active");
        }else{
            jQuery("#topic_collection_setup #toggle_rss_search").removeClass("active");
        }
    }
}

function LP_remove_rss_from_list(){
    jQuery(this).parent().parent().remove();
    LP_show_hide_selected_rss();
    LP_save_topic_collection_setup();
}

function LP_show_hide_selected_rss(){
    if(jQuery("#topic_collection_rss_setup #selected_rss").is(":empty")){
        jQuery("#topic_collection_rss_setup #selected_rss").hide();
        jQuery("#topic_collection_rss_setup .items_pointer").hide();
    }else{
        jQuery("#topic_collection_rss_setup #selected_rss").show();
        jQuery("#topic_collection_rss_setup .items_pointer").show();
    }
}
function LP_remove_keyword_input(){
    var the_input = jQuery(this).parent().parent();
    var num_kwords = jQuery(the_input).parent().find(">div").length;
    if(num_kwords==1){
        jQuery("input",the_input).val("");
    }else{
        jQuery(the_input).remove();
    }
    LP_save_topic_collection_setup();
}

function LP_add_keyword_input(){
    var is_filter = "";
    if(jQuery(this).hasClass("filter")){
        is_filter = "-";
    }
    var the_keywords = jQuery(this).parent().parent().find("div.keywords");
    var num_kwords = jQuery("> div",the_keywords).length;
    if(num_kwords<=9){
        // var the_key = "<div>\
        // <span class=\"the_handle\">\
        // <i class=\"greycross-d-18 the_move\"></i>\
        // <input class=\"btn2 topic_keyword "+filter_class+"\" type=\"text\" placeholder=\"keyword\"/>\
        // </span>\
        // <i class=\"key_trash-l-18\">\
        // <i class=\"key-l-18\"></i>\
        // <i class=\"trash-d-18 the_trash\"></i>\
        // </i>\
        // </div>";
        var the_key = LP_keyword_input_tpl1(is_filter);
        jQuery(the_keywords).append(the_key)
            .find("input.topic_keyword:last").focus();
    }
}

var audioElement;
function LP_swooosh_rss(the_feed){
    if(typeof the_feed == "object" && the_feed.hasOwnProperty("link")){
        var link 		= the_feed.link;
        var title 		= the_feed.title;
        var keyword 	= the_feed.keyword;
        var the_img 	= the_feed.the_img;
        var the_domain	= the_feed.the_domain;
    }else{
        var link 		= jQuery(this).attr("param");
        var obj 		= jQuery(this).parent().parent().parent().parent();
        jQuery(obj).parent().find(".title b").removeAttr("style");
        var title 		= jQuery(obj).parent().find(".title a").attr("param");
        var keyword 	= jQuery(".keyword",obj).text();
        var the_img 	= jQuery("img",obj).attr("src");
        var the_domain	= jQuery(".info > span:eq(0)",obj).text();
    }
    title 			= title.replace(/<b>/ig,"");
    title 			= title.replace(/<\/b>/ig,"");
    var the_short	= "";
    if(the_domain.length <= 15) the_short = "short";

    var args = {
        link			: link,
        display_domain	: the_domain,
        title			: title.replace(/'/ig,""),
        keyword			: keyword,
        the_short		: the_short,
        favico			: the_img
    };
    var the_swoosh = LP_rss_tpl(args);


    jQuery("#topic_collection_rss_setup #selected_rss").append(the_swoosh).show();
    jQuery("#topic_collection_rss_setup .items_pointer").show();
    audioElement.play();

    jQuery(obj).parent().parent().animate({opacity:0},100,function(){
        jQuery(this).remove();
        jQuery("#topic_collection_rss_setup .results_news_tpl1").mCustomScrollbar("update");
    });
    LP_save_topic_collection_setup();
}

/* GLOBALS */
var the_rss_keywords = new Array();
function LP_topic_rss_keyword_preview(){
    var setup           = LP_get_topic_collection_setup();
    var keywords        = setup.rss_keywords;
    var neg_keywords 	= "";
    for (var key in keywords){
        var k = keywords[key];
        if(k.charAt(0)=="-"){
            k = k.substring(1);
            neg_keywords+= ' -"'+k+'"';
        }
    }


    rss_total_articles = 0;
    rss_total_feeds_resutls = 0;
    jQuery("#topic_collection_rss_setup .topic_keyword").each(function(i){
        var this_val = jQuery.trim(jQuery(this).val());
        if(this_val !="" ){
            if(this_val.charAt(0) !="-"){
                the_rss_keywords[q] = i;
                var q = '"'+this_val+'"';
                the_rss_keywords[q] = i;
                q+=neg_keywords;
                jQuery("#topic_collection_rss_setup .cols .results_news_tpl1").append("<div style=\"display:none;\" class=\"search_section_title\"><div class=\"source_keyword\"><i class=\"magnify-d-21\"></i><span>RSS Feeds</span><span>"+this_val+"</span><i class=\"down_arr_s expand_g_all\"></i></div></div>");
                google.feeds.findFeeds(q, LP_RSS_Search_Done);
            }
        }
    });
}

function LP_RSS_Search_Done(results){
    if (!results.error) {
        jQuery.each(results.entries,function(i,val){
            var tq = results.query.split("-");
            results.query = tq[0].trim(" ");
            var from_results = jQuery("#topic_collection_rss_setup .cols .results_news_tpl1 .item .keywords .swooosh_RSS[param='"+val.url+"']").length;
            var from_feedlist = jQuery("#topic_collection_rss_setup #selected_rss .item[param='"+val.url+"']").length;
            if(from_results == 0 && from_feedlist == 0){
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
                    favico	: "https://www.google.com/s2/favicons?domain="+val.link,
                    keywords: results.query.replace(/"/g,''),
                    is_RSS  : true
                };

                var the_item = LP_news_res_tpl1(item);
                jQuery("#topic_collection_rss_setup .cols .results_news_tpl1 .search_section_title:eq("+the_rss_keywords[results.query]+")").append(the_item);
                LP_load_this_feed(val.url);
            }
        });

        jQuery("#topic_collection_rss_setup .cols .results_news_tpl1 .search_section_title:eq("+the_rss_keywords[results.query]+")").show();
        if(jQuery("#topic_collection_rss_setup .results_news_tpl1").hasClass("mCustomScrollbar")){
            jQuery("#topic_collection_rss_setup .results_news_tpl1").mCustomScrollbar("update");
        }else{
            jQuery("#topic_collection_rss_setup .results_news_tpl1").mCustomScrollbar({
                theme:"dark-thick",
                mouseWheelPixels: 200,
                scrollInertia:10
            });
        }
    }
}

function LP_RSS_Show_List(){
    rss_total_articles = 0;
    rss_total_feeds_resutls = 0;
    if(jQuery("#topic_collection_rss_setup .results_news_tpl1").hasClass("mCustomScrollbar")){
        jQuery("#topic_collection_rss_setup .results_news_tpl1").mCustomScrollbar("destroy");
    }
    jQuery("#topic_collection_rss_setup .cols .results_news_tpl1").empty().append("<div style=\"display:none;\" class=\"search_section_title\"><div class=\"source_keyword\"><i class=\"magnify-d-21\"></i><span>RSS Feeds</span><i class=\"down_arr_s expand_g_all\"></i></div></div>");
    var setup = LP_get_topic_collection_setup();
    var rss_feed_links = setup.rss_feed_links;
    jQuery.each(rss_feed_links, function(i,val){
        var from_results = jQuery("#topic_collection_rss_setup .cols .results_news_tpl1 .item .keywords .swooosh_RSS[param='"+val.url+"']").length;
        var from_feedlist = jQuery("#topic_collection_rss_setup #selected_rss .item[param='"+val.url+"']").length;
        if(from_results == 0 && from_feedlist == 0){
            var domain = val.display_domain;
            domain = domain.replace("www.","");
            domain = domain.replace("http://","");
            domain = domain.replace("https://","");
            var sdomain = domain.split("/");
            item = {
                title	: val.title,
                src		: "RSS Feeds",
                domain  : val.display_domain,
                url		: val.link,
                favico	: "https://www.google.com/s2/favicons?domain="+val.display_domain,
                keywords: val.keyword,
                is_RSS  : true,
                no_swoosh: true
            };

            var the_item = LP_news_res_tpl1(item);
            jQuery("#topic_collection_rss_setup .cols .results_news_tpl1 .search_section_title:eq(0)").append(the_item);
            LP_load_this_feed(val.link);
        }
    });

    jQuery("#topic_collection_rss_setup .cols .results_news_tpl1 .search_section_title:eq(0)").show();

    jQuery("#topic_collection_rss_setup .results_news_tpl1").mCustomScrollbar({
        theme:"dark-thick",
        mouseWheelPixels: 200,
        scrollInertia:10
    });
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
    if(jQuery("#topic_collection_rss_setup .keywords .topic_keyword").length == 1){
        if(jQuery("#topic_collection_rss_setup .keywords .topic_keyword").val()==""){
            LP_set_search_keywords(true);
        }
    }
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
                LP_save_topic_collection_setup();
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
    LP_save_topic_collection_setup();
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
    LP_set_quick_search();
    scoop_total_search_resutls = 0;
    LP_topic_suggestions(topic_id, "LP_tsearch_suggestions", "#t_splash_google_suggestions");
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
            //var double_arrow = false;
            for (var key in keywords){
                var q = keywords[key];
                if(q.charAt(0)!="-"){
                    var the_id = q.replace(/ /gi, '_');
                    the_id = the_id.replace(".", "");
                    /* var dbl = "";
                     if(double_arrow == false){
                     dbl = "<div class=\"expand_all_sections\"><i class=\"down_arr_s\"></i><i class=\"down_arr_s\"></i></div>";
                     double_arrow = true;
                     }*/
                    k_section+= "<div id=\""+the_id+"\" class=\"search_section_title\"><div class=\"source_keyword gradient\"><i class=\"magnify-d-21\"></i><span>"+sLabel+"</span><span>"+q+"</span><i class=\"down_arr_s expand_g_all\"></i></div></div>";
                }
            }
            jQuery(target).append("<div style=\"display:none;\" id=\""+source+"\" class=\"source_search_section_title\" param=\""+sLabel+"\">"+k_section+"</div>");
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

function LP_prepare_dripple_item(res,showimage){
    var the_url = res.guid;
    var the_fav = res.guid;

    var item = {
        title			: res.post_title,
        src				: "Dripple",
        article_url		: res.story_URL,
//        story_URL		: res.story_URL,
        domain  		: "dripple.com",
        publishedDate 	: res.post_date,
        content			: res.post_content,
        keywords		: res.keywords,
        favico			: "https://www.google.com/s2/favicons?domain="+the_fav,
        showimage       : true
    };
    if(showimage){
        item.dripple_image = res.post_thumbnail;
    }
    LP_console(item);
    return item;
}

function LP_prepare_twitter_item(res, showimage){
    var the_url = "";
    var the_fav = "";
    if(res.entities.urls.length>=1){
        the_url = res.entities.urls[0].url;
        the_fav = res.entities.urls[0].expanded_url;
    }else{
        if(res.hasOwnProperty("retweeted_status")){
            if(res.retweeted_status.entities.urls.length>=1){
                the_url = res.retweeted_status.entities.urls[0].url;
                the_fav = res.retweeted_status.entities.urls[0].expanded_url;
            }
        }
    }
    var item = {
        title			: res.text,
        src				: "@"+res.user.screen_name,
        article_url		: the_url,
        domain  		: "twitter.com",
        publishedDate 	: res.created_at,
        keywords		: res.keywords,
        favico			: "https://www.google.com/s2/favicons?domain="+the_fav,
        is_twitter		: true
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

function LP_search_suggestions_twitter(search_results){
    var the_q = search_results.q.split("-");
    var the_keyword = the_q[0].replace(/\+/g," ");
    search_results.q = the_keyword;
    if(search_results.target == "#b_splash_google_suggestions"){
        search_results.target = "#b_splash_twitter_suggestions";
    }
    LP_search_suggestions(search_results);
}

function LP_search_suggestions_dripple(search_results){
    var the_q = search_results.q.split("-");
    var the_keyword = the_q[0].replace(/\+/g," ");
    search_results.q = the_keyword;
    if(search_results.target == "#b_splash_google_suggestions"){
        search_results.target = "#b_splash_dripple_suggestions";
    }
    LP_search_suggestions(search_results);
}

function LP_search_suggestions(search_results){
    /* checking if source sections are present */
    if(jQuery(search_results.target+" .source_search_section_title").length == 0){
        LP_write_source_search_sections(search_results.target);
    }

    var the_q = search_results.q.split("-");
    var the_keyword =  the_q[0].trim();

    var re = new RegExp('"',"ig");
    the_keyword = the_keyword.replace(re, '');
    search_results.q = the_keyword;
    var the_item = "";
    if(typeof search_results.results !="undefined"){
        search_results.results.sort(function(a,b){
            if(a.hasOwnProperty("publishedDate")){
                return (new Date(b.publishedDate)) - (new Date(a.publishedDate));
            }else{
                return (new Date(b.created_at)) - (new Date(a.created_at));
            }
        });
    }
    jQuery.each(search_results.results,function(i,val){
        val.keywords = search_results.q;
        var item = {};
        var the_url = "";
        if(val.hasOwnProperty("retweet_count")){
            item = LP_prepare_twitter_item(val,true);
            if(val.entities.urls.length>=1){
                the_url = val.entities.urls[0].url;
            }else{
                if(val.hasOwnProperty("retweeted_status")){
                    if(val.retweeted_status.entities.urls.length>=1){
                        the_url = val.retweeted_status.entities.urls[0].url;
                    }
                }
            }
        }
        if(val.hasOwnProperty("GsearchResultClass")){
            if(val.GsearchResultClass == "GnewsSearch"){
                item = LP_prepare_news_item(val,true);
                the_url = val.unescapedUrl;
            }else if(val.GsearchResultClass == "GblogSearch"){
                item = LP_prepare_blogs_item(val,true);
                the_url = val.postUrl;
            }
        }

        if(val.hasOwnProperty("post_type")){
            item = LP_prepare_dripple_item(val,true);
            the_url = val.guid;
        }

        if(jQuery(".b_slpash_right a.story_URL[href='"+the_url+"']").length == 0){
            item.showimage = true;
            item.with_extra = true;
            the_item+= LP_news_res_tpl1(item);
        }
    });

    var the_id = (search_results.q).replace(/ /gi, '_');
    the_id = the_id.replace(".", '');
    var re = new RegExp('"',"ig");
    the_id = the_id.replace(re, '');

    jQuery(search_results.target+" #"+search_results.type+" #"+the_id).append(the_item);
    jQuery(search_results.target+" #"+search_results.type).show();

    jQuery(search_results.target).mCustomScrollbar("destroy");
    jQuery(search_results.target).mCustomScrollbar({
        theme:"dark-thick",
        scrollInertia:10,
        advanced:{updateOnContentResize : true}
    });
}

var scoop_total_search_resutls  = 0;
function LP_tsearch_suggestions_twitter(search_results){
    var the_q = search_results.q.split("-");
    var the_keyword = the_q[0].replace(/\+/g," ");
    search_results.q = the_keyword;
    LP_tsearch_suggestions(search_results);
}

function LP_tsearch_suggestions(search_results){
    var the_q = search_results.q.split("-");
    var the_keyword =  the_q[0].trim();

    var re = new RegExp('"',"ig");
    the_keyword = the_keyword.replace(re, '');
    search_results.q = the_keyword;

    /* checking if source sections are present */
    if(jQuery(search_results.target+" .source_search_section_title").length == 0){
        LP_write_source_search_sections(search_results.target);
    }

    var current_topic   = localStorage.getItem("topics_in_collection_setup");
    var setup           = LP_get_topic_collection_setup(current_topic);
    var num_keywords    = LP_count_keywords(setup.search_keywords);
    var the_item = "";
    if(typeof search_results.results !="undefined"){
        search_results.results.sort(function(a,b){
            if(a.hasOwnProperty("publishedDate")){
                return (new Date(b.publishedDate)) - (new Date(a.publishedDate));
            }else{
                return (new Date(b.created_at)) - (new Date(a.created_at));
            }
        });
    }
    jQuery.each(search_results.results,function(i,val){
        val.keywords = search_results.q;
        var item;
        var the_url = "";
        if(val.hasOwnProperty("retweet_count")){
            item = LP_prepare_twitter_item(val,false);
            if(val.entities.urls.length>=1){
                the_url = val.entities.urls[0].url;
            }else{
                if(val.hasOwnProperty("retweeted_status")){
                    if(val.retweeted_status.entities.urls.length>=1){
                        the_url = val.retweeted_status.entities.urls[0].url;
                    }
                }
            }
        }
        if(val.hasOwnProperty("GsearchResultClass")){
            if(val.GsearchResultClass == "GnewsSearch"){
                item = LP_prepare_news_item(val,false);
                the_url = val.unescapedUrl;
            }else if(val.GsearchResultClass == "GblogSearch"){
                item = LP_prepare_blogs_item(val,false);
                the_url = val.postUrl;
            }
        }

        if(jQuery("#t_splash_google_suggestions a.story_URL[href='"+the_url+"']").length == 0){
            the_item+= LP_news_res_tpl1(item);
            scoop_total_search_resutls++;
            jQuery("#topic_collection_setup .stats_search_results").text(num_keywords+" Keywords / "+scoop_total_search_resutls+" Articles");

            var kwc  = null;
            var kwt  = null;

            var patt = the_keyword.replace(".","\.");
            var k_word = patt.split(" ");


            var stop_words = ["a","able","about","across","after","all","almost","also","am","among","an","and","any","are","as","at","be","because","been","but","by","can","cannot","could","dear","did","do","does","either","else","ever","every","for","from","get","got","had","has","have","he","her","hers","him","his","how","however","i","if","in","into","is","it","its","just","least","let","like","likely","may","me","might","most","must","my","neither","no","nor","not","of","off","often","on","only","or","other","our","own","rather","said","say","says","she","should","since","so","some","than","that","the","their","them","then","there","these","they","this","tis","to","too","twas","us","wants","was","we","were","what","when","where","which","while","who","whom","why","will","with","would","yet","you","your"];
            var key_count = 0;
            for (var key in k_word){
                var w = k_word[key];
                if(jQuery.inArray(w,stop_words) == -1){
                    var kwt_c = 0;
                    var kwc_c = 0;
                    var re = new RegExp(w,"ig");
                    kwt = item.title.match(re);
                    if(kwt != null){
                        kwt_c = kwt.length;
                    }

                    re = new RegExp(w,"ig");
                    if(item.hasOwnProperty("content")){
                        kwc = item.content.match(re);
                        if(kwc != null){
                            kwc_c = kwc.length;
                        }
                    }
                    key_count+= kwt_c + kwc_c;
                }
            }
            keyword_count[the_keyword]+=key_count;
            jQuery(".quick_search .keyword_counter[param='"+the_keyword+"']").text(keyword_count[the_keyword]);
        }

    });

    var the_id = the_keyword.replace(/ /gi, '_');
    the_id = the_id.replace(".", '');

    jQuery(search_results.target+" #"+search_results.type+" #"+the_id).append(the_item);
    jQuery(search_results.target+" #"+search_results.type).show();

    jQuery(search_results.target).mCustomScrollbar("destroy");
    jQuery(search_results.target).mCustomScrollbar({
        theme:"dark-thick",
        scrollInertia:10
    });
    LP_highlight_search_results();
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
    var neg_keywords 	= "";
    for (var key in keywords){
        var k = keywords[key];
        if(k.charAt(0)=="-"){
            k = k.substring(1);
            neg_keywords+= ' -"'+k+'"';
        }
    }
    for (var source in search_sources){
        var val = search_sources[source];
        if(val == 1){
            for (var key in keywords){
                var q = keywords[key];
                if(q.charAt(0)!="-"){
                    if(source=="twitter"){
                        q = q.replace(/ /ig,"+");
                        neg_keywords = neg_keywords.replace(/ /ig,"");
                        LP_twitter_search({term:q+neg_keywords, count:8, LP_call_back:callback+"_twitter", target:args.target});
                    }else if(source == "dripple"){
                        drippleSearch({term:q,type:source, LP_call_back:callback+"_dripple", target:args.target});
                    }else{
                        q = '"'+q+'"'+neg_keywords;
                        googleSearch({term:q,type:source, LP_call_back:callback, target:args.target});
                    }
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

    var default_setup = jQuery.parseJSON(localStorage.getItem("default_collection_setup"));

    if(typeof topic == "number" || typeof topic == "string"){
        var user_topics = jQuery.parseJSON(localStorage.getItem("user_topics"));
        if(typeof user_topics[topic] != "undefined" && user_topics!= null){
            if(user_topics[topic].hasOwnProperty("collection_setup")){
                if(user_topics[topic].collection_setup != null){
                    var the_setup = JSON.parse(user_topics[topic].collection_setup.replace(/\\'/ig,""));
                    return the_setup;
                }else{
                    return default_setup;
                }
            }else{
                return default_setup;
            }
        }else{
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
            }
        }
    });

    jQuery("#topic_collection_setup .cols .results_news_tpl1 .source_search_section_title:eq("+sort_source[results.type]+") .search_section_title:eq("+sort_section[results.type][results.q]+")").append(the_item);
    console.log(results.type+":"+results.q+"="+sort_section[results.type][results.q]);
    jQuery("#topic_collection_setup .cols .results_news_tpl1 .source_search_section_title:eq("+sort_source[results.type]+")").show();
    jQuery("#topic_collection_setup .results_news_tpl1").mCustomScrollbar("destroy");
    jQuery("#topic_collection_setup .results_news_tpl1").mCustomScrollbar({
        theme:"dark-thick",
        mouseWheelPixels: 200,
        scrollInertia:10
    });
}


function regexCheckMatch(pattern, subject){
    //alert(pattern+"  ##  "+subject);
    var re = new RegExp(pattern);
    if (subject.match(re)) {
        return true;
    } else {
        return false;
    }
}

function LP_grabbed_img_setter(url,target){
    if(jQuery("#item_"+target+" .detail .origin").length > 0){
        var the_img ="<div class=\"img_cont google_res\">\
							<img src=\""+url+"\"/>\
						</div>";
        jQuery("#item_"+target+" .content.body div.img_cont").remove();
        jQuery("#item_"+target+" .content.body").prepend(the_img);
        jQuery("#item_"+target+" .detail").addClass("withthumb");
        if(typeof LP_timeIntervals["t_"+target] != "undefined")
            clearTimeout(LP_timeIntervals["t_"+target].timer);
    }else{
        if(LP_timeIntervals.hasOwnProperty("t_"+target) == false){
            LP_timeIntervals["t_"+target] = {counter:0};
        }else{
            LP_timeIntervals["t_"+target].counter++;
        }

        if(LP_timeIntervals["t_"+target].counter > 5){
            clearTimeout(LP_timeIntervals["t_"+target].timer);
        }else{
            LP_timeIntervals["t_"+target].timer  = setTimeout(function(){LP_grabbed_img_setter(url,target)},500);
        }
    }
}

var LP_timeIntervals= {};
var LP_while_counter= {};
function LP_grab_img_CB(results){
    var target = results.target;
    for(var r in results.results){
        var res = results.results[r];
        if(typeof res !="undefined"){
            if((res.width >= 495 && res.height >= 275)){
                var url = res.unescapedUrl;
                LP_grabbed_img_setter(url,target);
                return true;
            }
        }
    }
    return true;
    // Here we are going to use images from suggestions.
    var ilength = jQuery("#image_suggestions li").length;
    var rand = Math.floor((Math.random()*ilength));
    var src = jQuery("#image_suggestions li:eq("+rand+") img").attr("src");
    LP_timeIntervals["t_"+target] = {c:0,counter:0,timer : ""};
    while(typeof src == "undefined" && LP_timeIntervals["t_"+target].c <10){
        rand = Math.floor((Math.random()*ilength));
        src = jQuery("#image_suggestions li:eq("+rand+") img").attr("src");
        LP_timeIntervals["t_"+target].c++;
    }

    if(typeof src == "undefined"){
        //LP_timeIntervals["t_"+target].timer  = setTimeout("LP_reassign_drip_image('"+target+"')",3000);
        return false;
    }
    var the_img ="<div class=\"img_cont\">\
						<img src=\""+src+"\"/>\
					</div>";
    jQuery("#item_"+results.target+" .content.body div.img_cont").remove();
    jQuery("#item_"+results.target+" .content.body").prepend(the_img);
    jQuery("#item_"+results.target+" .detail").addClass("withthumb");
}

function LP_reassign_drip_image(target){
    LP_timeIntervals["t_"+target].counter++;
    var current_topic = LP_get_localStorage_topic();
    var ilength = current_topic.cached_images.length;
    var rand = Math.floor((Math.random()*ilength));
    var src = current_topic.cached_images[rand];
    LP_timeIntervals["t_"+target].c = 0;
    while(typeof src == "undefined" && LP_timeIntervals["t_"+target].c <10){
        rand = Math.floor((Math.random()*ilength));
        src = current_topic.cached_images[rand];
        LP_timeIntervals["t_"+target].c++;
    }

    if(typeof src == "undefined" && LP_timeIntervals["t_"+target].counter <= 10){
        LP_timeIntervals["t_"+target].timer  = setTimeout("LP_reassign_drip_image('"+target+"')",3000);
        return false;
    }

    if(typeof src != "undefined"){
        var the_img ="<div class=\"img_cont\">\
							<img src=\""+src+"\"/>\
						</div>";
        jQuery("#item_"+target+" .content.body").prepend(the_img);
        jQuery("#item_"+target+" .detail").addClass("withthumb");
        clearTimeout(LP_timeIntervals["t_"+target].timer);
    }
}

function LP_news_res_tpl1(item){

    var the_c = "";
    var the_item_id = "";
    var the_img = "";
    if(item.hasOwnProperty("showimage")){
        if(item.hasOwnProperty("dripple_image") == false){
            // Here is where we go grab the site image....
            var site = "";
            if(item.hasOwnProperty("article_url")){
                site = item.article_url;
            }else if(item.hasOwnProperty("url")){
                site = item.url
            }
            var gkw = "";
            if(item.hasOwnProperty("keywords")){
                gkw = item.keywords;
            }
            var rand = Math.floor((Math.random()*1000+1));
            var target = new Date().getTime()+"_"+rand;
            googleSearch({term:gkw, as_sitesearch:site, type:"images", LP_call_back:"LP_grab_img_CB", target:target, imgsz : "vga"});
            the_item_id = 'id="item_'+target+'"';

            var current_topic = LP_get_localStorage_topic();
            var ilength = current_topic.cached_images.length;
            var rand = Math.floor((Math.random()*ilength));
            var src = current_topic.cached_images[rand];
        }else{
            var src = item.dripple_image;
//            var story_URL = item.story_URL;
        }
        the_img ="<div class=\"img_cont topic_res\">\
							<img src=\""+src+"\"/>\
						</div>";
    }

    var the_content = "";
    var the_content_h = "";
    if(item.hasOwnProperty("content")){
        var tc = item.content.replace(/<b>/ig,"");
        tc = tc.replace(/<\/b>/ig,"");
        the_content = "<div class=\"content body\">"+the_img+tc+"</div>";
        the_content_h = "<input type=\"hidden\" class=\"item_content\" value=\""+tc+"\" />";
    }

    var favico = "";
    if(item.hasOwnProperty("favico")){
        var re = new RegExp("\/t.co","ig");
        var t = item.favico.match(re);

        re = new RegExp("\/twitter.co","ig");
        var t2 = item.favico.match(re);
        if(t != null || t2 != null){
            favico = "<img src=\""+sub+"/wp-content/themes/linkedpost/images/twitter-icon.png\" />";
        }else{
            favico = "<img src=\""+item.favico+"\" />";
        }
    }else{
        favico = "<i class=\"greyredrip-d-18\"></i>";
    }

    var link = "";
    var swoosh = "";
    if(item.hasOwnProperty("url")){
        link = "param=\""+item.url+"\"";
        if(item.hasOwnProperty("is_RSS")){
            if(item.hasOwnProperty("no_swoosh") == false){
                swoosh = "<a><span class=\"circle_popout swooosh_RSS\" "+link+"></span></a>";
            }
        }
    }

    var keywords = "";

    if(item.hasOwnProperty("keywords")){
        if(item.keywords!=""){
            keywords = "<span class=\"keyword\">"+item.keywords+"</span>";
        }else{
            keywords = "<span class=\"keyword\">Custom RSS</span>";
        }
    }

    var article_url = "";

    if(item.hasOwnProperty("article_url")){
        article_url = "<a class=\"story_URL\" href=\""+item.article_url+"\" target=\"_blank\"><span class=\"circle_popout\"></span></a>";
    }

    var publishedDate = "";
    var param_date = "";
    if(item.hasOwnProperty("publishedDate")){
        publishedDate = LP_get_age(item.publishedDate);
        param_date = new Date(item.publishedDate);
        param_date = param_date.getTime();
    }

    var the_title = item.title;
    the_title = the_title.replace(/<b>/ig,"");
    the_title = the_title.replace(/<\/b>/ig,"");
    the_title = the_title.replace("...","");
    var the_domain = item.domain;
    if(item.hasOwnProperty("is_twitter")){
        var cut_title = the_title.split("http");
        the_title = cut_title[0];

        if(cut_title[1]!= ""){
            the_content = "<div class=\"content body\">http"+cut_title[1]+"</div>";
            the_content_h = "<input type=\"hidden\" class=\"item_content\" value=\"http"+cut_title[1]+"\" />";
        }
        the_domain = item.src;
    }

    if(item.hasOwnProperty("keywords")){
        if(item.keywords!= ""){
            var stop_words = ["a","able","about","across","after","all","almost","also","am","among","an","and","any","are","as","at","be","because","been","but","by","can","cannot","could","dear","did","do","does","either","else","ever","every","for","from","get","got","had","has","have","he","her","hers","him","his","how","however","i","if","in","into","is","it","its","just","least","let","like","likely","may","me","might","most","must","my","neither","no","nor","not","of","off","often","on","only","or","other","our","own","rather","said","say","says","she","should","since","so","some","than","that","the","their","them","then","there","these","they","this","tis","to","too","twas","us","wants","was","we","were","what","when","where","which","while","who","whom","why","will","with","would","yet","you","your"];

            var kwords = item.keywords.split(" ");

            for (var key in kwords){
                var w = kwords[key];
                if(jQuery.inArray(w,stop_words) == -1){
                    var re = new RegExp(w,"ig");
                    the_title = the_title.replace(re,"<b>"+w+"</b>");
                    the_content = the_content.replace(re,"<b>"+w+"</b>");
                }
            }
        }
    }

    var expander = "expand_g";
    if(item.hasOwnProperty("is_RSS") && item.is_RSS == true){
        expander = "expand_r";
        the_title = "<a href=\""+item.url+"\" param=\""+the_title+"\" target=\"_blank\">"+the_title+"</a>";
    }

    var extra_btns = "";
    if(item.hasOwnProperty("with_extra")){
        // extra_btns ="<span class=\"small_buttons1 search_item_btns search_accept\">Brief</span>\
        // <span class=\"small_buttons1 search_item_btns search_extra\">Story</span>";
        extra_btns = "<a><span class=\"circle_popout search_extra\"></a><a></span><span class=\"circle_popout search_accept\"></span></a>";
    }
    var the_item = "<div class=\"item\" "+the_item_id+" param=\""+param_date+"\">\
						<div class=\"detail"+the_c+"\">\
							<div class=\"origin\">\
								"+favico+"\
								<div class=\"info\">\
									<span>"+the_domain+"</span>\
									<span class=\"feed_stats\">"+publishedDate+"</span>\
									<div class=\"keywords\">"+keywords+article_url+swoosh+extra_btns+"</div>\
								</div>\
							</div>\
							<span class=\"title\">"+the_title+"</span>\
							"+the_content+"\
							"+the_content_h+"\
						</div>\
						<i class=\"down_arr_s "+expander+"\""+link+"></i>\
					</div>";
    return the_item;
}

function LP_load_this_feed(link){
    var feed = new google.feeds.Feed(link);
    feed.setNumEntries(10);
    feed.load(LP_load_this_feed_CB);
}

var rss_total_articles = 0;
var rss_total_feeds_resutls = 0;
function LP_load_this_feed_CB(result){
    if (!result.error) {
        rss_total_feeds_resutls++;
        var the_item = "<div class=\"content body\"><ol start=\"1\" type=\"1\">";
        var the_feed_url = result.feed.feedUrl;
        var current_topic = localStorage.getItem("topics_in_collection_setup");
        var setup           = LP_get_topic_collection_setup(current_topic);
        var num_keywords    = LP_count_keywords(setup.rss_keywords);
        var feed_article_count = 0;
        var feed_stat_keyword_count = 0;
        var the_feed = jQuery("#topic_collection_rss_setup .results_news_tpl1 .item .expand_r[param='"+the_feed_url+"']").parent();
        var the_keyword = jQuery(the_feed).find(".keywords > span.keyword").text();
        if(result.feed.entries.length > 0){
            var first_date = new Date(result.feed.entries[0].publishedDate);
            first_date = first_date.getTime();
            var youngest  = first_date;
            var eldest    = first_date;
            for (var i = 0; i < result.feed.entries.length; i++) {
                var entry = result.feed.entries[i];
                var this_date = new Date(entry.publishedDate);
                this_date = this_date.getTime();
                if(this_date > youngest) youngest = this_date;
                if(this_date < eldest) eldest = this_date;

                if(the_keyword!=""){
                    var re = new RegExp(the_keyword,"ig");
                    var the_title = entry.title.replace(re,"<b>"+the_keyword+"</b>");
                    var kw = entry.title.match(re);
                }else{
                    var the_title = entry.title;
                    var kw = null;
                }
                if(kw != null){
                    feed_stat_keyword_count+=kw.length;
                }
                the_item+= "<li><a href=\""+entry.link+"\" target=\"_blank\">"+the_title+"</a><span class=\"rss_publishedDate\" param=\""+entry.publishedDate+"\">"+LP_get_age(entry.publishedDate)+"</span></li>";

                feed_article_count++;
                rss_total_articles++;
                jQuery("#topic_collection_rss_setup .stats_search_results").text(num_keywords+" Keywords / "+rss_total_feeds_resutls+" Feeds / "+rss_total_articles+" Articles");
            }
        }
        the_item+="</ol></div>";


        if(feed_article_count > 1){

            var diff = youngest - eldest;
            var indays = " / "+LP_convert_milliseconds_to_age(diff);

            var is_plural = "Keyword";
            if(feed_stat_keyword_count > 1){
                is_plural = "Keywords";
            }

            var kws = " / "+feed_stat_keyword_count + " "+is_plural;

            var feed_stats = LP_get_age(youngest)+" / "+feed_article_count+" articles"+indays+kws;
            jQuery(the_feed).find(".info .feed_stats").text(feed_stats);
            if(the_keyword!=""){
                var re = new RegExp(the_keyword,"ig");
                jQuery(the_item.replace(re,"<b>"+the_keyword+"</b>")).insertAfter(jQuery(the_feed).find(".title"));
            }else{
                jQuery(the_item).insertAfter(jQuery(the_feed).find(".title"));
            }
        }else{
            jQuery(the_feed).remove();
        }


    }
    LP_highlight_search_results();
}

function LP_count_keywords(kwords){
    var c = 0;
    for (var k in kwords){
        var kw = kwords[k];
        if(kw.charAt(0) != "-"){
            c++;
        }
    }
    return c;
}

/*var curr_position = 0;
 var page_width = 650;
 function LP_check_all_loaded(the_counter){
 eval(the_counter+'++;');
 if(jQuery("#image_suggestions_cont").hasClass("mCustomScrollbar")==false){
 jQuery("#image_suggestions_cont").mCustomScrollbar({
 horizontalScroll:true,
 autoDraggerLength: true,
 advanced:{
 updateOnBrowserResize:true,   
 autoExpandHorizontalScroll:true
 },
 theme:"LP_thick",
 callbacks:{
 onTotalScroll:function(){
 if(still_loading_more == false){
 still_loading_more = true;
 LP_suggest_images_more();
 }
 },
 onScroll:function(){
 curr_position = mcs.left * -1;
 }
 },
 scrollInertia:50
 });
 }else if(eval(the_counter) >5){
 setTimeout(crap,300);		
 }
 }*/

//var still_loading_more = false;
//var LP_set_image_suggestions_counter;
function LP_set_image_suggestions(results){
    //still_loading_more = false;
    var current_topic = LP_get_localStorage_topic();
    var images = results.results;
    //jQuery("ul#image_suggestions").empty();
    jQuery.each(images,function(i,val){
        /*var the_new_img = jQuery("<img/>")
         .load(function() {
         if(current_topic.hasOwnProperty("cached_images") == false){
         current_topic.cached_images = [];
         current_topic.cached_images[0] = jQuery(this).attr("src");
         }else{
         var cl = current_topic.cached_images.length;
         current_topic.cached_images[cl] = jQuery(this).attr("src");
         }
         LP_update_localStorage_topic(current_topic);
         })
         .error(function(){
         //LP_check_all_loaded("LP_set_image_suggestions_counter");
         })
         .attr("src", val.unescapedUrl)
         .attr("alt",val.unescapedUrl);*/
        if(current_topic.hasOwnProperty("cached_images") == false){
            current_topic.cached_images = [];
            current_topic.cached_images[0] = val.unescapedUrl;
        }else{
            var cl = current_topic.cached_images.length;
            current_topic.cached_images[cl] = val.unescapedUrl;
        }
    });
    LP_update_localStorage_topic(current_topic);
    /*var the_title = jQuery(".redrip_form #ripple_title").val();
     if(the_title != "" ){
     googleSearch({term:the_title,type:"images", LP_call_back:"LP_set_more_images_suggestions", target:"none"});
     }*/
}

/*var LP_set_more_images_suggestions_counter;
 function LP_set_more_images_suggestions(results){
 still_loading_more = false;
 var images = results.results;
 LP_set_more_images_suggestions_counter = 0;
 jQuery.each(images,function(i,val){
 if(jQuery("ul#image_suggestions li img[alt='"+val.unescapedUrl+"']").length ==0){
 var the_new_img = jQuery("<img/>")
 .load(function() {
 var twidth = 162 * (jQuery("ul#image_suggestions li").length + 1);
 jQuery("ul#image_suggestions").css("width",twidth+"px");
 var li = jQuery("<li/>");
 jQuery(li).append(this);
 jQuery("ul#image_suggestions").append(li);
 LP_check_all_loaded("LP_set_more_images_suggestions_counter");

 })
 .error(function(){
 LP_check_all_loaded("LP_set_more_images_suggestions_counter");
 })
 .attr("src", val.unescapedUrl)
 .attr("alt",val.unescapedUrl);			
 }
 });
 }*/

/* google api Globals */
var image_page = 1;
// var googlesapi = "https://www.googleapis.com/customsearch/v1?key="+gapi_key+"&cx=015453469409247312102:y4s93jpb1j4&alt=json&searchType=image&imgSize=large";
function LP_suggest_images(){
    var current_topic = LP_get_localStorage_topic();
    if(current_topic.hasOwnProperty("cached_images") == false){
        image_page = 0;
        var the_q ="";
        var sep = "";
        jQuery(".redrip_form input.redrip_tags.active").each(function(){
            var the_tag = jQuery(this).val();
            if(the_tag!=""){
                the_q+= sep+the_tag;
                sep = " ";
            }
        });
        var collection_setup = LP_get_topic_collection_setup();
        if(collection_setup.search_keywords !=null && collection_setup.search_keywords!=""){
            var neg = "";
            jQuery.each(collection_setup.search_keywords,function(i,v){
                if(v.charAt(0)!="-"){
                    the_q+= sep+v;
                    sep = " ";
                }else{
                    neg+= ' -"'+v.substring(1)+'"';
                }
            });
            //LP_set_image_suggestions_counter = 0;
            googleSearch({term:the_q+neg,type:"images", LP_call_back:"LP_set_image_suggestions", target:"none"});
//            googleSearch({term:the_q+neg,type:"images", page:1, LP_call_back:"LP_set_image_suggestions", target:"none"});
            /*var the_title = jQuery(".redrip_form #ripple_title").val();
             if(the_title == "" ){
             googleSearch({term:the_q+neg,type:"images", page:1, LP_call_back:"LP_set_more_images_suggestions", target:"none"});
             }*/
        }
    }
}
/*
 function LP_suggest_images_more(){
 image_page++;
 var the_title = jQuery(".redrip_form #ripple_title").val()+" ";
 var the_q ="";
 var sep = "";
 jQuery(".redrip_form input.redrip_tags.active").each(function(){
 var the_tag = jQuery(this).val();
 if(the_tag!=""){
 the_q+= sep+the_tag;
 sep = " ";
 }
 });

 var collection_setup = LP_get_topic_collection_setup();
 var neg = "";
 jQuery.each(collection_setup["search_keywords"],function(i,v){
 if(v.charAt(0)!="-"){
 the_q+= sep+v;
 sep = " ";
 }else{
 neg+= ' -"'+v.substring(1)+'"';
 }
 });
 var the_title = jQuery(".redrip_form #ripple_title").val();
 if(the_title != "" ){
 googleSearch({term:the_title+neg,type:"images",page:image_page, LP_call_back:"LP_set_more_images_suggestions", target:"none"});
 }
 googleSearch({term:the_q+neg,type:"images",page:image_page, LP_call_back:"LP_set_more_images_suggestions", target:"none"});
 }*/

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
    for (var i = to_drip_data["my_industries"][ndx]["count"]; i>0; i--){
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
        theme:"dark-thick",
        scrollInertia:10
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

function LP_close_redrip_form(msg){
    if(typeof msg == "string" && msg!=""){
        var tpl = "<span>"+msg+"</span>";
        jQuery(".redrip_form #notif_bar").empty().append(tpl).fadeIn(100,function(){LP_unlock_splash("bottom");});
    }else{
        LP_unlock_splash("bottom");
    }
}

function LP_notify_processing_drip(msg){
    var tpl = "<span>"+msg+"</span>";
    jQuery(".redrip_form #notif_bar").empty().append(tpl).fadeIn(100);
}

function LP_goto_dripzone_2(){
    // jQuery(".dz").removeClass("active");
    // jQuery("#drip_zone").animate({"margin-top":"100%"},300,function(){
    // jQuery("#drip_zone").hide().css("margin-top","");
    // });
    // jQuery("#drip_zone_2").animate({"height":'toggle'},300,function(){
    // jQuery(this).addClass("active");
    // });
    // LP_next_dz(this);
}

// function LP_goto_dripzone_3(){
// jQuery(".dz").removeClass("active");
// jQuery("#drip_zone_2").animate({"margin-top":"100%"},300,function(){
// jQuery("#drip_zone_2").hide().css("margin-top","");
// });
// jQuery("#drip_zone_3").animate({"height":'toggle'},300,function(){
// jQuery(this).addClass("active");
// });
// }

// global
var right_images_count =0;
var g_q = "";
function LP_set_b_slpash_right_images(results){
    var b = 0;
    var the_q = results.q;
    for(var a in results.results.cursor.pages){
        if(a>0){
            if(b==5)break;
            googleSearch({term:the_q,type:"images", LP_call_back:"LP_set_CB_slpash_right_images", target:"none", page: (parseInt(a)+1)});
            b++;
        }
    }

    if(results.hasOwnProperty("is_more") == false){
        the_new_img = [];
        // Ues the topioc keywords this time as the keywords for searching images...
        var setup           = LP_get_topic_collection_setup();
        var search_sources  = setup.search_sources;
        var keywords        = setup.search_keywords;
        var neg_keywords 	= "";
        the_q = "";
        var sep = "";
        for (var key in keywords){
            var k = keywords[key];
            if(k.charAt(0)=="-"){
                k = k.substring(1);
                neg_keywords+= ' -"'+k+'"';
            }else{
                the_q+=sep+k;
                sep = " ";
            }
        }

        right_images_count =0;
        jQuery(".b_slpash_right_images #images_tabs").empty();
        jQuery(".b_slpash_right_images #images_tabs").append("<span class='active loading'>page 1</span>");
        jQuery(".b_slpash_right_images #right_form .cols").remove();
        jQuery(".b_slpash_right_images #right_form").append("<div class=\"cols\"><ul class=\"image_suggestions\"></ul></div>");
    }
    jQuery.each(results.results.results,function(i,v){
        var the_css = "width= '176'";
        if(parseInt(v.tbHeight) < parseInt(v.tbWidth) ){
            the_css = "height= '150'";
        }


        if(right_images_count >= 16){
            jQuery(".b_slpash_right_images #images_tabs").append("<span class=\"inactivex\">page "+(jQuery(".b_slpash_right_images #images_tabs span").length + 1)+"</span>");
            jQuery(".b_slpash_right_images #right_form").append("<div class=\"cols\" style=\"display:none;\"><ul class=\"image_suggestions\"></ul></div>");
            right_images_count = 0;
        }
        right_images_count++;
        if(results.hasOwnProperty("is_more") == false){
            LP_preload_images_right(v.unescapedUrl,2);
        }
        jQuery(".b_slpash_right_images #right_form .cols:last ul").append("<li><img class=\"loading\" "+the_css+" alt=\"" +v.unescapedUrl+"\" src=\"" +v.tbUrl+"\"/></li>");
    });

    jQuery(".b_slpash_right_images").show();
    if(results.hasOwnProperty("is_more") == false){
        g_q = the_q+neg_keywords;
        //setTimeout('googleSearch({term:g_q,type:"images", LP_call_back:"LP_suggest_images_from_topic_keywords", target:"none", return:"responseDate"});',2000);
    }

    jQuery(".b_slpash_right_images #images_tabs span").live("click",function(){
        if(jQuery(this).hasClass("inactive") == false){
            var i = jQuery(this).index();
            jQuery(".b_slpash_right_images #images_tabs span.active").removeClass("active");
            jQuery(this).addClass("active");
            jQuery(".b_slpash_right_images #right_form .cols").hide();
            jQuery(".b_slpash_right_images #right_form .cols").eq(i).show();
        }
    });
}

function LP_suggest_images_from_topic_keywords(responseDate){
    responseDate.is_more = "true";
    LP_set_b_slpash_right_images(responseDate);
}

function LP_set_CB_slpash_right_images(results){
    jQuery.each(results.results,function(i,v){
        var the_css = "width= '176'";
        if(parseInt(v.tbHeight) < parseInt(v.tbWidth) ){
            the_css = "height= '150'";
        }
        if(right_images_count >= 16){
            var tab_c = jQuery(".b_slpash_right_images #images_tabs span").length;

            jQuery(".b_slpash_right_images #images_tabs").append("<span class=\"inactivex\">page "+(tab_c + 1)+"</span>");

            jQuery(".b_slpash_right_images #right_form").append("<div class=\"cols\" style=\"display:none;\"><ul class=\"image_suggestions\"></ul></div>");
            right_images_count = 0;
        }
        right_images_count++;

        if(jQuery(".b_slpash_right_images #images_tabs span").length == 1){
            LP_preload_images_right(v.unescapedUrl,1);
        }
        jQuery(".b_slpash_right_images #right_form .cols:last ul").append("<li><img class=\"loading\" "+the_css+" alt=\"" +v.unescapedUrl+"\" src=\"" +v.tbUrl+"\"/></li>");
    });
    jQuery(".b_slpash_right_images #right_form .cols li").draggable({
        revert: true,
        addClasses: false,
        stop: function(event,ui){
            jQuery(ui.helper).animate({opacity:1});
        }
    });
}

var the_new_img = [];
function LP_preload_images_right(url,x){
    var c = the_new_img.length;
    the_new_img[c] = new Image();
    the_new_img[c].onload = function() {
        var obj = jQuery(this);
        var the_src = jQuery(obj).attr("src");
        var tb = jQuery(".b_slpash_right_images #right_form .cols ul li img[alt='"+the_src+"']");
        jQuery(obj).attr("width",jQuery(tb).attr("width")).attr("height",jQuery(tb).attr("height")).attr("alt",the_src);
        jQuery(tb).removeClass("loading").replaceWith(obj);
        LP_check_images_page_loading();
    };
    the_new_img[c].onerror = function(){
        var the_src = jQuery(obj).attr("src");
        LP_console("error loading : "+the_src);
        jQuery(".b_slpash_right_images #right_form .cols ul li img[alt='"+the_src+"']").remove();
    };
    the_new_img[c].src = url;
}

var g_indx = 0;
var recheck_count = 0;
function LP_check_images_page_loading(){
    g_indx = jQuery(".b_slpash_right_images #images_tabs span.loading").index();
    if(jQuery(".b_slpash_right_images #right_form .cols:eq("+g_indx+") img.loading").length > 0){
        // Do nothing...
    }else{
        LP_console("loading new page");
        var n = jQuery(".b_slpash_right_images #images_tabs span.loading").next();
//        jQuery(".b_slpash_right_images #images_tabs span.loading").removeClass("inactive").removeClass("loading");
        jQuery(".b_slpash_right_images #images_tabs span.loading").removeClass("loading");
        if(jQuery(n).is("span")){
            recheck_count = 0;
            jQuery(n).addClass("loading");
            var im = jQuery(n).index();
            the_new_img = [];
            jQuery(".b_slpash_right_images #right_form .cols:eq("+im+") img").each(function(i,v){
                var url = jQuery(this).attr("alt");

                LP_preload_images_right(url,4);
            });
        }else{
            recheck_count++;
            if(recheck_count <= 5){
                setTimeout("LP_recheck_image_pages(g_indx)",2000);
            }
        }
    }
}

function LP_recheck_image_pages(i){
    LP_console("recheck");
    var n = jQuery(".b_slpash_right_images #images_tabs span:eq("+i+")").next();
    if(jQuery(n).length >0 ){
        jQuery(n).addClass("loading");
        var im = jQuery(n).index();
        the_new_img = [];
        jQuery(".b_slpash_right_images #right_form .cols:eq("+im+") img").each(function(i,v){
            var url = jQuery(this).attr("alt");
            LP_preload_images_right(url,3);
        });
    }
}

function LP_next_dz(){
    var the_title = jQuery("#drip_zone textarea#ripple_title").val();
    var the_analysis = jQuery("#drip_zone textarea#analysis").val();
    var the_content = jQuery("#drip_zone textarea#ripple_content").val();
    if(the_title != "" && the_analysis != "" && the_content != ""){
        jQuery(".redrip_form #notif_bar").fadeOut();
        obj = jQuery(".dz.active");
        jQuery("#dz_cont #drip_zone_2 h2, #dz_cont #drip_zone_3 h2").remove();
        jQuery("#dz_cont #drip_zone_2, #dz_cont #drip_zone_3").prepend("<h2 class=\"dz_title\">"+the_title+"</h2>");
        jQuery(obj).animate({"margin-top":"100%"},100,function(){
            jQuery(this).hide();
            jQuery(this).removeClass("active");
            jQuery(this).next().css({"margin-top":"-100%"}).show().animate({"margin-top":0},300,function(){
                jQuery(this).addClass("active");
                if( jQuery(this).attr("id")== "drip_zone_2"){
                    // Do the image search here using the dripzone title as the keyword...
                    var the_title = jQuery("textarea#ripple_title").val();
                    googleSearch({term:the_title,type:"images", LP_call_back:"LP_set_b_slpash_right_images", target:"none", return:"responseDate"});
                }else{
                    jQuery(".b_slpash_right_images").show();
                }
            });
        });
        jQuery(".b_splash_left, .b_slpash_right").css("display","none");
    }else{
        LP_notify_processing_drip("Please fill-up all fields...");
    }
}

function LP_back_dz(){
    var obj = jQuery(".dz.active");

    jQuery(obj).animate({"margin-top":"100%"},100,function(){
        jQuery(this).hide();
        jQuery(this).removeClass("active");
        jQuery(this).prev().css({"margin-top":"-100%"}).show().animate({"margin-top":0},300,function(){
            jQuery(this).addClass("active");
            if(jQuery(".dz.active").attr("id") == "drip_zone"){
                jQuery(".b_slpash_right_images").hide();
                jQuery(".b_splash_left, .b_slpash_right").css("display","block");
            }
        });
    });
}

/* 
 *SAVES THE Re-drip 
 */
function LP_save_redrip(){
    /* jQuery("#save_redrip").attr("id","save_redripx");
     jQuery("#save_buffer").attr("id","save_bufferx"); */
    var obj       = jQuery(this);
    var the_drip_form = jQuery(".redrip_form");
    var is_fresh  = jQuery(the_drip_form).find("input#is_fresh").val();
    var notif_msg = "";
    var action = "";
    if(is_fresh != "" || jQuery(obj).hasClass("buffer")==true){
        /* We are saving this a Fresh Drip */
        action = "LP_save_fresh_drip";
        notif_msg = "Adding a Drip to your Buffer.";
        var post_status = "drip";
        if(jQuery(obj).attr("id") == "drip_now" || jQuery(obj).attr("id") == "skip_and_drip_now"){
            post_status = "publish";
        }
    }else{
        /* We are saving this as a Re-Drip */
        action = "LP_save_redrip";
        notif_msg = "Publishing your drip now...";
        var post_status = "redrip";
    }

    var post_id 		= jQuery(the_drip_form).find("input#post_id").val();
    var blog_id 		= jQuery(the_drip_form).find("input#blog_id").val();
    var analysis 		= jQuery(the_drip_form).find("textarea#analysis").val();
    var ripple_content 	= jQuery(the_drip_form).find("textarea#ripple_content").val();
    var ripple_title 	= jQuery(the_drip_form).find("textarea#ripple_title").val();
    var topic 			= localStorage.getItem("topics_in_collection_setup");
    var story_URL		= jQuery(the_drip_form).find("input#story_URL").val();
    var the_img1		= jQuery(the_drip_form).find("input#img1").val();
    var ripple_tags		= "";
    var sep = "";

    jQuery(the_drip_form).find("input.redrip_tags.active").each(function(){
        var tag = jQuery.trim(jQuery(this).val());
        if(tag!=""){
            ripple_tags+= sep+jQuery(this).val();
            sep = ",";
        }
    });

    var img1 = {
        path			: the_img1,
        top				: parseFloat(jQuery("#drip_zone_2 .the_cropper img.cropper_subject").css("top")) - LP_zoom(40,zoom),
        left			: parseFloat(jQuery("#drip_zone_2 .the_cropper img.cropper_subject").css("left")) - LP_zoom(40,zoom),
        scaled_width	: parseFloat(jQuery("#drip_zone_2 .the_cropper img.cropper_subject").css("width")),
        scaled_height	: parseFloat(jQuery("#drip_zone_2 .the_cropper img.cropper_subject").css("height"))
    };

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
        newfile_url     : the_img1,
        story_URL     	: story_URL,
        post_status     : post_status,
        img1			: img1
    }
    /*
     jQuery("#LP_save_drip_form input[name='post_id']").val(post_id);
     jQuery("#LP_save_drip_form input[name='blog_id']").val(blog_id);
     jQuery("#LP_save_drip_form input[name='analysis']").val(analysis);
     jQuery("#LP_save_drip_form input[name='topic']").val(topic);
     jQuery("#LP_save_drip_form input[name='LP_topic']").val(topic);
     jQuery("#LP_save_drip_form input[name='ripple_content']").val(ripple_content);
     jQuery("#LP_save_drip_form input[name='ripple_title']").val(ripple_title);
     jQuery("#LP_save_drip_form input[name='ripple_tags']").val(ripple_tags);
     jQuery("#LP_save_drip_form input[name='newfile_url']").val(the_img1);
     jQuery("#LP_save_drip_form input[name='story_URL']").val(story_URL);
     jQuery("#LP_save_drip_form input[name='img1[path]']").val(img1.path);
     jQuery("#LP_save_drip_form input[name='img1[top]']").val(img1.top);
     jQuery("#LP_save_drip_form input[name='img1[left]']").val(img1.left);
     jQuery("#LP_save_drip_form input[name='img1[scaled_width]']").val(img1.scaled_width);
     jQuery("#LP_save_drip_form input[name='img1[scaled_height]']").val(img1.scaled_height);
     jQuery("#LP_save_drip_form input[name='post_status']").val(post_status);*/

    var oMyForm = new FormData();
    oMyForm.append("action", action);
    oMyForm.append("post_id", post_id);
    oMyForm.append("blog_id", blog_id);
    oMyForm.append("post_status", post_status);
    oMyForm.append("is_ajax", 1);

    oMyForm.append("analysis", analysis);
    oMyForm.append("topic", topic);
    oMyForm.append("LP_topic", topic);
    oMyForm.append("ripple_content", ripple_content);
    oMyForm.append("ripple_title", ripple_title);
    oMyForm.append("ripple_tags", ripple_title);
    //oMyForm.append("newfile_url", the_img1); // Find a way to remove this...
    oMyForm.append("story_URL", story_URL);

    var i1 = jQuery("#drip_zone_2 .the_cropper img.cropper_subject").attr("src");
    if(i1.substring(0,5) == "data:"){
        oMyForm.append("f_img1", g_f_img1.file, g_f_img1.filename);
    }else{
        oMyForm.append("f_img1", i1);
    }

    oMyForm.append("img1[top]", img1.top);
    oMyForm.append("img1[left]", img1.left);
    oMyForm.append("img1[scaled_width]", img1.scaled_width);
    oMyForm.append("img1[scaled_height]", img1.scaled_height);


    var img2;
    if(jQuery(obj).attr("id") == "make_a_buffer" || jQuery(obj).attr("id") == "drip_now"){
        var img2 = {
            path			: jQuery("#drip_zone_3 .the_cropper img.cropper_subject").attr("src"),
            top				: parseFloat(jQuery("#drip_zone_3 .the_cropper img.cropper_subject").css("top")) - LP_zoom(40,zoom),
            left			: parseFloat(jQuery("#drip_zone_3 .the_cropper img.cropper_subject").css("left")) - LP_zoom(40,zoom),
            towidth			: parseFloat(jQuery("#drip_zone_3 .the_cropper .cropper_cont").css("width"))-(LP_zoom(40,zoom) * 2),
            toheight		: parseFloat(jQuery("#drip_zone_3 .the_cropper .cropper_cont").css("height"))-(LP_zoom(40,zoom) * 2),
            scaled_width	: parseFloat(jQuery("#drip_zone_3 .the_cropper img.cropper_subject").css("width")),
            scaled_height	: parseFloat(jQuery("#drip_zone_3 .the_cropper img.cropper_subject").css("height"))
        };

        /*data.img2 = img2;

         jQuery("#LP_save_drip_form input[name='img2[path]']").val(img2.path);
         jQuery("#LP_save_drip_form input[name='img2[top]']").val(img2.top);
         jQuery("#LP_save_drip_form input[name='img2[left]']").val(img2.left);
         jQuery("#LP_save_drip_form input[name='img2[towidth]']").val(img2.towidth);
         jQuery("#LP_save_drip_form input[name='img2[toheight]']").val(img2.toheight);
         jQuery("#LP_save_drip_form input[name='img2[scaled_width]']").val(img2.scaled_width);
         jQuery("#LP_save_drip_form input[name='img2[scaled_height]']").val(img2.scaled_height);

         if(jQuery("#LP_save_drip_form input[name='use_img1']").val() == "true"){
         jQuery("#LP_save_drip_form input[name='f_img2']").remove();
         jQuery("#LP_save_drip_form input[name='img2[path]']").val("");
         }

         if(jQuery("#LP_save_drip_form input[name='f_img2']").length > 0){
         if(jQuery("#LP_save_drip_form input[name='f_img2']").val() != ""){
         var path2 = jQuery("#LP_save_drip_form input[name='f_img2']");
         jQuery("#LP_save_drip_form input[name='img2[path]']").val(path2[0].files[0].name);
         }
         }*/
        oMyForm.append("img2[top]", img2.top);
        oMyForm.append("img2[left]", img2.left);
        oMyForm.append("img2[towidth]", img2.towidth);
        oMyForm.append("img2[toheight]", img2.toheight);
        oMyForm.append("img2[scaled_width]", img2.scaled_width);
        oMyForm.append("img2[scaled_height]", img2.scaled_height);

        if(jQuery("#drip_zone_2 .the_cropper img.cropper_subject").attr("src") == jQuery("#drip_zone_3 .the_cropper img.cropper_subject").attr("src")){
            oMyForm.append("f_img2", "1");
        }else{
            var i2 = jQuery("#drip_zone_3 .the_cropper img.cropper_subject").attr("src");
            if(i2.substring(0,5) == "data:"){
                oMyForm.append("f_img2", g_f_img2.file, g_f_img2.filename);
            }else{
                oMyForm.append("f_img2", i2);
            }
        }

    }

    /*if(jQuery("#LP_save_drip_form input[name='f_img1']").length > 0){
     if(jQuery("#LP_save_drip_form input[name='f_img1']").val() != ""){
     var path1 = jQuery("#LP_save_drip_form input[name='f_img1']");
     jQuery("#LP_save_drip_form input[name='img1[path]']").val(path1[0].files[0].name);
     }
     }*/

    var has_error = false;
    /* if(img_newfile_url == ""){
     jQuery(".redrip_form .imgblogpostx").addClass("required");
     has_error = true;
     }else{
     jQuery(".redrip_form .imgblogpostx").removeClass("required");
     } */
    if(topic == "" || typeof topic == "null" || topic == null){
        has_error = true;
    }

    if(ripple_title == ""){
        jQuery(".redrip_form input#ripple_title").addClass("required");
        has_error = true;
    }else{
        jQuery(".redrip_form input#ripple_title").removeClass("required");
    }

    if(analysis == ""){
        jQuery(".redrip_form textarea#analysis").addClass("required");
        has_error = true;
    }else{
        jQuery(".redrip_form textarea#analysis").removeClass("required");
    }

    if(ripple_content == ""){
        jQuery(".redrip_form textarea#ripple_content").addClass("required");
        has_error = true;
    }else{
        jQuery(".redrip_form textarea#ripple_content").removeClass("required");
    }

    jQuery("#make_a_buffer").attr("id","make_a_bufferx");
    jQuery("#skip_and_buffer").attr("id","skip_and_bufferx");
    jQuery("#drip_now").attr("id","drip_nowx");
    jQuery("#skip_and_drip_now").attr("id","skip_and_drip_nowx");

    if(has_error === false){
        LP_notify_processing_drip(notif_msg);
        //jQuery("form#LP_save_drip_form").submit();
        var oReq = new XMLHttpRequest();
        oReq.open("POST", ajaxurl);
        oReq.send(oMyForm);
        oReq.onload = function(oEvent) {
            if (oReq.status == 200) {
                var res = JSON.parse(oEvent.target.response);
                if(res.hasOwnProperty("error")){
                    LP_notify_processing_drip(res.error);
                    jQuery("#make_a_bufferx").attr("id","make_a_buffer");
                    jQuery("#skip_and_bufferx").attr("id","skip_and_buffer");
                    jQuery("#drip_nowx").attr("id","drip_now");
                    jQuery("#skip_and_drip_nowx").attr("id","skip_and_drip_now");
                }else if(res.hasOwnProperty("topic_id")){
                    LP_close_bottom_and_update_topic(res.topic_id);
                }
            } else {
                LP_console("error man... ");
                LP_console(oReq);
                LP_notify_processing_drip("An error occured while trying to download the images. Please try to use diffirent image...");
                //oOutput.innerHTML = "Error " + oReq.status + " occurred uploading your file.<br \/>";
                jQuery("#make_a_bufferx").attr("id","make_a_buffer");
                jQuery("#skip_and_bufferx").attr("id","skip_and_buffer");
                jQuery("#drip_nowx").attr("id","drip_now");
                jQuery("#skip_and_drip_nowx").attr("id","skip_and_drip_now");
            }
        };
        return true;
    }else{
        jQuery("#make_a_bufferx").attr("id","make_a_buffer");
        jQuery("#skip_and_bufferx").attr("id","skip_and_buffer");
        jQuery("#drip_nowx").attr("id","drip_now");
        jQuery("#skip_and_drip_nowx").attr("id","skip_and_drip_now");
    }
}

function LP_close_bottom_and_update_topic(topic_id){
    LP_refresh_topic(topic_id);
    LP_close_redrip_form("Your Drip has been successfully added to your Topic.");
}

function LP_update_topic_tab_info(topic_id){
    topic_obj = {};
    topic_obj = LP_get_localStorage_topic(topic_id);
    jQuery("ul.topic_tabs .topic_tab[param='"+topic_obj.ID+"']").text(topic_obj.short_name);
    jQuery("ul.topic_tabs .tab_info[param='"+topic_obj.ID+"'] .stats").text(topic_obj.drip_stats.buffered+" buffered / "+topic_obj.drip_stats.days+" days / "+topic_obj.drip_stats.dripped+" drips");
    // var the_i = jQuery("ul.topic_tabs .tab_info[param='"+topic_obj.ID+"'] .stats i");
    // jQuery("ul.topic_tabs .tab_info[param='"+topic_obj.ID+"'] .stats").next().html(the_i+topic_obj.post_title);
}

function LP_fresh_drip_form(){
    if(LP_is_topic_complete()){
        LP_timeIntervals = {};
        if(jQuery("#bottom_main").length > 0){
            LP_unlock_splash("bottom");
            return false;
        }
        jQuery(".redrip_form .inblogpostbody #img1").val("");
        var to_drip_data = "";
        jQuery("#bottom_main").remove();
        var drip_form = LP_append_redrip_form(to_drip_data);
        jQuery("body").append(drip_form);
        var holder =document.getElementById("bottom_main");
        holder.ondragover = function (e) {
            if(jQuery(e.target).hasClass("url_catcher") == false){
                e.preventDefault();
            }
        };
        //holder.ondragend = function () { return false; };
        holder.ondrop = function (e) {
            if(jQuery(e.target).hasClass("url_catcher") == false){
                e.preventDefault();
            }else{
                if(e.dataTransfer.files.length > 0){
                    e.preventDefault();
                    LP_set_cropper_subject_from_file(e.dataTransfer.files);
                }
            }
        };

        jQuery("#bottom_main .cropper_cont").droppable({
            accept: "ul.image_suggestions li",
            activeClass: "ui-state-hover",
            drop: function( event, ui ) {
                var img_url = jQuery("img",ui.helper).attr("alt");
                jQuery(ui.helper).css("opacity",0);
                jQuery("ul.image_suggestions li img.active").show().removeClass("active");
                jQuery("img",ui.helper).addClass("active").hide();
                LP_prepare_dripzone_image(img_url);
            }
        });

        var tstt = jQuery("#t_splash_topic_tabs").clone();

        jQuery("#redrip_height").append(tstt);

        jQuery("#bottom_main .tab_bar_drag").removeClass("ui-draggable").css("top","");

        LP_set_active_tab();
        jQuery(".redrip_form").find("input#is_fresh").val("fresh");
        LP_set_tab_groups();
        LP_bsplash_suggest();
        jQuery(".splash_colors span.active").removeClass("class").trigger("click");
        jQuery("#bottom_main #t_splash_topic_tabs").show().css("top","100%").animate({top:"0px"},200);
        jQuery("#redrip_height").css("height","100%");
        jQuery("#bottom_main .redrip_form").show();
        jQuery("#redrip_height").animate({height:"75px"},200,function(){
            var win_h = jQuery(window).height();
            var offset = jQuery(".results_news_tpl1.b_splash").offset();
//			var n_h = win_h - offset.top - 15;
            var n_h = win_h - 149 - 15;

            jQuery(".results_news_tpl1.b_splash").height(n_h);
            jQuery(".results_news_tpl1.b_splash").mCustomScrollbar("update");
            setTimeout(LP_update_scroll_ripple_content_cont,500);
        });

        LP_suggest_images();
        jQuery(".footersplash_industry").mCustomScrollbar({
            theme:"dark-thick",
            scrollInertia:10
        });
        LP_reset_draggable_bottom_bar();

        jQuery("#ripple_content_cont").mCustomScrollbar({
            theme:"dark-thick",
            scrollInertia:10
        });
    }
}

// Global 
var to_drip_data;
function LP_redrip_this(){
    LP_timeIntervals = {};
    var obj = jQuery(this);
    var post_id = jQuery(obj).attr("id");
    var blog_id = jQuery(obj).attr("param");
    jQuery("#bottom_main").remove();
    var data = {
        action  : "LP_fetch_drip_data",
        post_id : post_id,
        blog_id : blog_id
    };
    jQuery.post(ajaxurl,data,function(r){
        to_drip_data = jQuery.parseJSON(r);
        if(r!='0'){
            jQuery(".redrip_form .inblogpostbody #img1").val("");
            var drip_form = LP_append_redrip_form(to_drip_data);
            jQuery("body").append(drip_form);
            var tstt = jQuery("#t_splash_topic_tabs").clone();
            jQuery("#redrip_height").append(tstt);
            jQuery("#bottom_main .tab_bar_drag").removeClass("ui-draggable").css("top","");

            LP_set_active_tab();
            LP_set_tab_groups();
            LP_bsplash_suggest();
            jQuery(".splash_colors span.active").removeClass("class").trigger("click");
            jQuery("#bottom_main #t_splash_topic_tabs").show().css("top","100%").animate({top:"0px"},200);
            jQuery("#redrip_height").css("height","100%");
            jQuery("#bottom_main .redrip_form").show();
            jQuery("#redrip_height").animate({height:"75px"},200,function(){
                var win_h = jQuery(window).height();
                var offset = jQuery(".results_news_tpl1.b_splash").offset();
                var n_h = win_h - offset.top - 15;

                jQuery(".results_news_tpl1.b_splash").height(n_h);
                jQuery(".results_news_tpl1.b_splash").mCustomScrollbar("update");
                setTimeout(LP_update_scroll_ripple_content_cont,500);
            });
            LP_suggest_images();
            jQuery(".footersplash_industry").mCustomScrollbar({
                theme:"dark-thick",
                scrollInertia:10
            });
            LP_reset_draggable_bottom_bar();

            jQuery("#ripple_content_cont").mCustomScrollbar({
                theme:"dark-thick",
                scrollInertia:10
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
        var dbl = "<div class=\"expand_all_sections\"><i class=\"down_arr_s\"></i><i class=\"down_arr_s\"></i></div>";
        var setup = LP_get_topic_collection_setup(topic_id);
        if(setup.search_sources.blogs == 1 || setup.search_sources.news == 1){
            jQuery("#bottom_main #search_tabs #google_tab").removeClass("disabled").removeClass("active");
            jQuery("#bottom_main .cols.google_tab").prepend(dbl);
        }else{
            jQuery("#bottom_main #search_tabs #google_tab").addClass("disabled").removeClass("active");
        }

        if(setup.search_sources.twitter == 1){
            jQuery("#bottom_main #search_tabs #twitter_tab").removeClass("disabled").removeClass("active");
            jQuery("#bottom_main .cols.twitter_tab").prepend(dbl);
        }else{
            jQuery("#bottom_main #search_tabs #twitter_tab").addClass("disabled").removeClass("active");
        }

        if(setup.search_sources.dripple == 1){
            jQuery("#bottom_main #search_tabs #dripple_tab").removeClass("disabled").removeClass("active");
            jQuery("#bottom_main .cols.dripple_tab").prepend(dbl);
        }else{
            jQuery("#bottom_main #search_tabs #dripple_tab").addClass("disabled").removeClass("active");
        }

        if(setup.rss_feed_links.length >= 1){
            jQuery("#bottom_main #search_tabs #rss_tab").removeClass("disabled").removeClass("active");
            jQuery("#bottom_main .cols.rss_tab").prepend(dbl);
        }else{
            jQuery("#bottom_main #search_tabs #rss_tab").addClass("disabled").removeClass("active");
        }

        jQuery("#bottom_main #search_tabs span:not(.disabled)").eq(0).addClass("active");
        var who = jQuery("#bottom_main #search_tabs span.active").attr("id");
        jQuery("#bottom_main .cols."+who).show();
    }
}

function LP_append_redrip_form(drip){
    if(typeof drip != "object"){
        var drip2 = LP_get_localStorage_topic();
    }
    var the_thumb = "";
    if(typeof drip == "object"){
        if(drip["thumbnail"]["fmedium"]){
            the_thumb = drip["thumbnail"]["small"];
            jQuery(".redrip_form .inblogpostbody #img1").val(drip["thumbnail"]["fmedium"]);
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
            for (var a=i; a<5; a++){
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

    var drip_form= "\
	<div id=\"bottom_main\" style=\"z-index:3\">\
		<div id=\"redrip_height\"></div>\
		<div class=\"redrip_form\">\
		<div id=\"re_drip_extension\">\
		</div>\
		<div>\
			<div class=\"b_splash_left\">\
                <div class=\"drippleScrollbar\" id=\"analysis_suggestions\"></div>\
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
				<div style=\"min-height:30px;width:100%;\"><div class=\"bottom_notif_bar\" id=\"notif_bar\"></div></div>\
				<div id=\"image_suggestions_main\">\
					<div id=\"image_suggestions_cont\">\
						<ul id=\"image_suggestions\">\
						</ul>\
					</div>\
					<div class=\"arrow left\"><i class=\"left_arr\" style=\"margin-top: 6px;margin-left: 0px;\"></i></div>\
					<div class=\"arrow right\"><i class=\"right_arr\" style=\"margin-top: 6px;margin-left: 0px;\"></i></div>\
				</div>\
				<div id=\"dz_cont\">\
					<div id=\"drip_zone\" class=\"dz active\">\
						<div style=\"width:100%\">\
							<textarea maxlength=\"150\" id=\"ripple_title\" cols=\"60\" class=\"ripple_title\">"+post_title+"</textarea>\
						</div>\
						<div class=\"sydcontenpost\" style=\"min-height:235px;\">\
							<span style=\"overflow:hidden;\">\
							<div class=\"imgblogpostx drip_thumb\">\
										<img width=\"295px\" src=\""+the_thumb+"\"/>\
									</div>\
								<div class=\"tile_op_div_inner tile_op_div_innerext drip_analysis ripple_analysis\">\
									<div class=\"analysistab\">\
										<i class=\"analysis_ico\"></i>\
										<span class=\"analysistext\">Analysis</span>\
									</div>\
									<span class=\"\">\
										<textarea cols=\"34\" maxlength=\"400\" id=\"analysis\" name=\"analysis\">"+post_excerpt+"</textarea>\
										<div id=\"analysis_back\">"+post_excerpt+"</div>\
									</span>\
								</div>\
							</span>\
							<div id=\"ripple_content_cont\" class=\"drippleScrollbar\">\
								<textarea maxlength=\"2000\" class=\"ripple_content\" cols=\"65\" id=\"ripple_content\">"+post_content+"</textarea>\
							</div>\
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
						<!--span id=\"proccessing_drip\" style=\"float: right;margin-right: 50px;\">start a</span><br />\
						<span class=\"save_add_topic save_add_ripple\" id=\"save_redrip\">Dripple</span>\
						<span class=\"save_add_topic save_add_ripple buffer\" id=\"save_buffer\">buffer</span-->\
						<span class=\"buttons2\" style=\"float: right;width: 105px;text-align: center;\" id=\"goto_dripzone_2\">Next</span>\
						<input type=\"hidden\" id=\"post_id\" value=\""+ID+"\"/>\
						<input type=\"hidden\" id=\"blog_id\" value=\""+blog_id+"\"/>\
						<input type=\"hidden\" id=\"is_fresh\" value=\"\"/>\
						<input type=\"hidden\" id=\"story_URL\" value=\"\"/>\
						<input type=\"hidden\" id=\"img1\" value=\"\"/>\
					</div>\
					<div id=\"drip_zone_2\" class=\"dz\">\
						<div class=\"the_cropper\">\
							<div class=\"cropper_cont\">\
							    <div class=\"pre_loading\">Loading...</div>\
								<div class=\"cropper_handle\"><textarea class=\"url_catcher\"></textarea></div>\
								<div class=\"cropper_mask\"></div>\
								<img class=\"cropper_subject\" id=\"cropper_subject\" src=\"\">\
							</div>\
							<div style=\"width: 312px;float: left;\">\
							    <div class=\"scroller_label\">zoom</div>\
								<div class=\"cropper_zoom\"><div class=\"drag\"></div><div class=\"rail\"></div></div>\
								<div class=\"scroller_label\">rotate</div>\
								<div class=\"cropper_rotate\"><div class=\"drag\"></div><div class=\"rail\"></div></div>\
								<div class=\"drag_drop\"><input type=\"file\" class=\"cropper_file\" param=\"LP_drip_zone2\" /><span>Add File</span></div>\
								<div class=\"cropper_btns\">\
									<span class=\"buttons2 back_dz\" param=\"2\">Back</span>\
									<span class=\"buttons2\" id=\"done_cropping_2\">Next</span>\
									<span class=\"buttons2\" id=\"skip_and_buffer\">Bucket</span>\
									<span class=\"buttons2\" id=\"skip_and_drip_now\">Drip Now</span>\
								</div>\
							</div>\
						</div>\
					</div>\
					<div id=\"drip_zone_3\" class=\"dz\">\
						<div class=\"the_cropper\">\
							<div class=\"cropper_cont\">\
							    <div class=\"pre_loading\">Loading...</div>\
								<div class=\"cropper_handle\"><textarea class=\"url_catcher\"></textarea></div>\
								<div class=\"cropper_mask\"></div>\
								<img class=\"cropper_subject\" id=\"cropper_subject\" src=\"\">\
							</div>\
							<div style=\"width: 312px;float: left;\">\
							    <div class=\"scroller_label\">zoom</div>\
								<div class=\"cropper_zoom\"><div class=\"drag\"></div><div class=\"rail\"></div></div>\
								<div class=\"cropper_taller\"><div class=\"drag\"></div><div class=\"rail\"></div></div>\
								<!-- div class=\"scroller_label\">width</div>\
							    <div class=\"cropper_subject_width\"><div class=\"drag\"></div><div class=\"rail\"></div></div>\
							    <div class=\"scroller_label\">height</div>\
							    <div class=\"cropper_subject_height\"><div class=\"drag\"></div><div class=\"rail\"></div></div -->\
								<div class=\"scroller_label\">rotate</div>\
							    <div class=\"cropper_rotate\"><div class=\"drag\"></div><div class=\"rail\"></div></div>\
								<div class=\"drag_drop\"><input type=\"file\" class=\"cropper_file\" param=\"LP_drip_zone3\" /><span>Add File</span></div>\
								<div class=\"cropper_btns\">\
									<span class=\"buttons2 back_dz\" param=\"3\">Back</span>\
									<span class=\"buttons2\" id=\"make_a_buffer\">Bucket</span>\
									<span class=\"buttons2\" id=\"drip_now\">Drip Now</span>\
								</div>\
							</div>\
						</div>\
					</div>\
				</div>\
			</div>\
			<div class=\"b_slpash_right\">\
				<div id=\"right_form\">\
					<div>\
						<div id=\"search_tabs\">\
							<span id=\"google_tab\">Google</span>\
							<span id=\"twitter_tab\">Twitter</span>\
							<span id=\"rss_tab\">RSS</span>\
							<span id=\"dripple_tab\">Dripple</span>\
						</div>\
						<input type=\"text\" class=\"gradient\" id=\"search_from_article_suggestions\" placeholder=\"Filter Items\">\
					</div>\
					<div class=\"cols google_tab\">\
						<div class=\"results_news_tpl1 b_splash drippleScrollbar left\" id=\"b_splash_google_suggestions\">\
						</div>\
					</div>\
					<div class=\"cols twitter_tab\">\
						<div class=\"results_news_tpl1 b_splash drippleScrollbar left\" id=\"b_splash_twitter_suggestions\">\
						</div>\
					</div>\
					<div class=\"cols rss_tab\">\
						<div class=\"results_news_tpl1 b_splash drippleScrollbar left\" id=\"b_splash_rss_suggestions\">\
						</div>\
					</div>\
					<div class=\"cols dripple_tab\">\
						<div class=\"results_news_tpl1 b_splash drippleScrollbar left\" id=\"b_splash_dripple_suggestions\">\
						</div>\
					</div>\
				</div>\
			</div>\
		    <div class=\"b_slpash_right_images\">\
				<div id=\"right_form\">\
					<div>\
						<div id=\"images_tabs\">\
							<span>page 1</span>\
							<span>page 2</span>\
						</div>\
					</div>\
					<div class=\"cols\">\
						<div class=\"page\">\
						</div>\
					</div>\
				</div>\
			</div>\
		</div>\
	</div>\
	<div style=\"display:none;\">\
		<form target=\"LP_save_drip_iframe\" id=\"LP_save_drip_form\" method=\"POST\" action=\""+sub+"LP_save_drip_iframe\" enctype=\"multipart/form-data\">\
			<input type=\"text\" name=\"post_id\" />\
			<input type=\"text\" name=\"blog_id\" />\
			<input type=\"text\" name=\"analysis\" />\
			<input type=\"text\" name=\"topic\" />\
			<input type=\"text\" name=\"LP_topic\" />\
			<input type=\"text\" name=\"ripple_content\" />\
			<input type=\"text\" name=\"ripple_title\" />\
			<input type=\"text\" name=\"ripple_tags\" />\
			<input type=\"text\" name=\"newfile_url\" />\
			<input type=\"text\" name=\"story_URL\" />\
			<input type=\"text\" name=\"post_status\" />\
			\
			<input type=\"text\" name=\"img1[path]\" />\
			<input type=\"text\" name=\"img1[top]\" />\
			<input type=\"text\" name=\"img1[left]\" />\
			<input type=\"text\" name=\"img1[scaled_width]\" />\
			<input type=\"text\" name=\"img1[scaled_height]\" />\
			\
			<input type=\"text\" name=\"img2[path]\" />\
			<input type=\"text\" name=\"img2[top]\" />\
			<input type=\"text\" name=\"img2[left]\" />\
			<input type=\"text\" name=\"img2[towidth]\" />\
			<input type=\"text\" name=\"img2[toheight]\" />\
			<input type=\"text\" name=\"img2[scaled_width]\" />\
			<input type=\"text\" name=\"img2[scaled_height]\" />\
			\
			<input type=\"text\" name=\"use_img1\" />\
			\
			<input type=\"file\" name=\"f_img1\" />\
			<input type=\"file\" name=\"f_img2\" />\
			<input type=\"submit\" name=\"submitme\" value=\"submit\" />\
		</form>\
		<iframe id=\"LP_save_drip_iframe\" name=\"LP_save_drip_iframe\"></iframe>\
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
    var reload = true;
    if(jQuery(obj).hasClass("home")){
        reload = false;
        // var home_view = jQuery(obj).attr("param");
        var home_view = jQuery(obj).attr("id");
        // var clicked = jQuery(obj).attr("id");
        jQuery("i#toggle_list_view, i#toggle_default_view,i#toggle_list_view").removeClass("active");
        jQuery(obj).addClass("active");
        jQuery("#list_view_cont, #default_view_cont, #tile_view_cont").fadeOut(0);
        var cont =  home_view.split("_");
        jQuery("#"+cont[1]+"_"+cont[2]+"_cont").fadeIn(350);
    }

    var param = (jQuery(obj).attr("id")).split("_");
    var data = {
        action 		: "LP_home_view",
        home_view	: param[1]
    };
    jQuery.post(ajaxurl,data,function(r){
        if(reload == true){
            location.replace(sub);
        }
    });
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
        var no_flip = "";
        if(drips_other_info[index]["thumbnail"]["tall"] == null){
            no_flip = "no-flip";
        }else{
            var bimg = drips_other_info[index]["thumbnail"]["tall"];
        }

        the_thumb = "<div class=\"flip_def_image_cont\">\
                        <div class=\"flip_image_rot\">\
                            <div class=\"imgblogpost "+no_flip+"\">\
                                <a href=\""+drip_item["cloaked_URL"]+"\"><img width=\"302\" height=\"168\" src=\""+drips_other_info[index]["thumbnail"]["small"]+"\"></a>\
                            </div>";
        if(drip_item["_LP_flip_img"] != ""){
            var nh = Math.floor(bimg["info"][1] - (bimg["info"][1] *((bimg["info"][0] - 302)/bimg["info"][0])));
            the_thumb+= "<div class=\"backx_flip\" style=\"display:none\">\
                            <div class=\"reverseflip_me\">\
                                <img width=\"302\" height=\""+nh+"\" src=\""+bimg["img"]+"\"/>\
                            </div>\
                        </div>";
        }
        the_thumb+= "</div>\
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
								<div class=\"det_cont\">\
									<div class=\"authordivindi\"><a href=\""+drips_other_info[index]["topic_link"]+"\">"+drips_other_info[index]["user_full_name"]+"</a></div>\
									<span class=\"inblogpostbodyspan\"><a href=\""+drips_other_info[index]["topic_link"]+"\">"+drip_item["topic_name"]+"</a></span>\
									<h2 class=\"posttitle_ext GenericSlabLight\"><a href=\""+drip_item["cloaked_URL"]+"/\">"+drip_item["post_title"]+"</a></h2>\
								</div>\
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

function LP_new_height(original_width, original_height, target_width){
    if(original_width > target_width){
        var nh = Math.floor(original_height - (original_height *((original_width - target_width)/original_width)));
    }else{
        var nh = Math.floor(original_height + (original_height *((target_width - original_width)/original_width)));
    }
    return nh;
}

function LP_prepare_tile_view(){
    var position = "left";
    jQuery.each(drips_list,function(index,value){
        var drip_item = value;
        var the_thumb = "";
        var no_flip = "";
        var backFlipper = "";
        if(drips_other_info[index]["thumbnail"]["tall"] == null){
            no_flip = "no-flip";
        }else{
            var bimg = drips_other_info[index]["thumbnail"]["tall"];
            // var nh = Math.floor(bimg["info"][1] - (bimg["info"][1] *((bimg["info"][0] - 490)/bimg["info"][0])));
            var nh = LP_new_height(bimg["info"][0], bimg["info"][1], 490);
            backFlipper = "<div class=\"backx_flip\" style=\"display:none\">\
                        <div class=\"reverseflip_me\">\
                            <img width=\"490\" height=\""+nh+"\"src=\""+bimg["img"]+"\"/>\
                        </div>\
                    </div>";
        }

        if(drips_other_info[index]["thumbnail"]["large"]){
            the_thumb = "<div class=\"flip_image_cont\">\
                <div class=\"flip_image_rot\">\
                    <div class=\"tile_feat_img_div "+no_flip+"\">\
                       <img width=\"495\" height=\"275\" src=\""+drips_other_info[index]["thumbnail"]["large"]+"\">\
                    </div>"+backFlipper+"\
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

// function LP_set_update_topic_form(){
// var obj = jQuery(this);
// if(jQuery(obj).text() == "Save" && jQuery(obj).attr("param")== "free"){
// LP_update_topic_submit(obj);
// }else{
// var item = jQuery(obj).parent().parent();
// if(jQuery("form",item).length == 0){
// var index = (jQuery(item).index())-1;
// var post_id = jQuery(item).attr("param");
// var thumb_form = "<span class=\"feat_img_info\">Drop image here to replace thumbnail.</span>\
// <form class=\"LP_update_topic_uploader\" name=\"LP_update_topic_uploader\" method=\"post\" action=\""+sub+"lp_add_topic_thumb\" target=\"LP_topic_iframe_"+post_id+"\" enctype=\"multipart/form-data\">\
// <input class=\"update_topic_feat_img\" id=\"update_topic_feat_img\" name=\"topic_file\" type=\"file\">\
// <input type=\"hidden\" id=\"usession_name\" name=\"session_name\" value=\"temp_file_"+post_id+"\">\
// <input type=\"hidden\" name=\"post_id\" value=\""+post_id+"\" />\
// <input type=\"hidden\" name=\"callback_function\" value=\"LP_set_topic_thumb\" />\
// </form>\
// <iframe name=\"LP_topic_iframe_"+post_id+"\" style=\"display:none;\"></iframe>";
// jQuery(".addtopic_feat_img_div",item).append(thumb_form);
// var topic_channel = jQuery("#LP_channels_select").clone();
// jQuery("select",topic_channel).val(LP_user_topics[index]["channel_id"]);
// jQuery(".topic_chann",item).replaceWith(topic_channel);
// jQuery(".topic_post_title",item).replaceWith("<input type=\"text\" class=\"new_topic_title\" placeholder=\"Title\" id=\"new_topic_title\" value=\""+LP_user_topics[index]["post_title"]+"\">");
// jQuery(".topic_post_cont",item).replaceWith("<textarea class=\"new_topic_description\" placeholder=\"Short description\" id=\"new_topic_description\" maxlength=\"250\">"+LP_user_topics[index]["post_content"]+"</textarea>");
// jQuery(".topic_post_cont_div",item).addClass("topic_post_cont_div_edit");
// jQuery(obj).text("Save");
// }
// }
// }

// function LP_update_topic_submit(obj){
// jQuery(obj).attr("param","busy");
// var item = jQuery(obj).parent().parent();
// var post_id = jQuery(item).attr("param");
// var channel = jQuery("select.new_topic_channel",item).val();
// var title = jQuery("input.new_topic_title",item).val();
// var description = jQuery("textarea.new_topic_description",item).val();
// var session_name = jQuery("input#usession_name",item).val();
// var data = {
// action       : "LP_update_topic_submit",
// post_id      : post_id,
// channel      : channel,
// title        : title,
// description  : description,
// session_name : session_name
// };
// jQuery.post(ajaxurl,data,function(r){
// var topic = jQuery.parseJSON(r);
// var xitem = jQuery(".post_holder_addtopic[param='"+topic["ID"]+"']");
// var topic_thumb = jQuery(".addtopic_feat_img_div img",xitem);
// var index = (jQuery(xitem).index())-1;
// LP_user_topics[index] = topic;
// LP_update_user_topics_storage(LP_user_topics);

// var updated_topic = LP_topic_form_loop(topic);
// jQuery(xitem).replaceWith(updated_topic);
// jQuery(".post_holder_addtopic").eq(index).find(".addtopic_feat_img_div img").replaceWith(topic_thumb);
// });
// }

function LP_update_topic_submit(topic_id){
    if(typeof topic_id != "sting" && typeof topic_id != "number")
        topic_id = localStorage.getItem("topics_in_collection_setup");
    var iframe_name = new Date().getTime();
    iframe_name = "form_"+iframe_name;
    var the_iframe = "<iframe id=\""+iframe_name+"\" name=\""+iframe_name+"\"></iframe>";
    jQuery("body").append(the_iframe);
    jQuery("#LP_current_topic_form input#iframe_name").val(iframe_name);
    jQuery("#LP_current_topic_form input.topic").val(topic_id);
    jQuery("#LP_current_topic_form").attr("target",iframe_name).submit();
}

function LP_refresh_topic(topic_id){
    var data = {
        action	: "LP_user_topics",
        topic   : topic_id
    };
    jQuery.post(ajaxurl,data,function(r){
        if(r!="0"){
            var updated_topic = jQuery.parseJSON(r);
            LP_update_localStorage_topic(updated_topic[0]);
            LP_reset_splash_screens();
        }
    });
}

function LP_delete_this_topic(topic_id){
    if(typeof topic_id != "sting" && typeof topic_id != "number")
        topic_id = localStorage.getItem("topics_in_collection_setup");

    var data = {
        action	: "LP_delete_this_topic",
        topic   : topic_id
    };
    jQuery.post(ajaxurl,data,function(r){
        if(r!="0"){
            var user_topics = jQuery.parseJSON(r);
            LP_update_user_topics_storage(user_topics);
            LP_populate_topic_tabs();
        }
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
    jQuery.each(user_topics,function(i,val){
        if(val.collection_setup === null){
            val.collection_setup = default_collection_setup;
        }
        ut[val.ID] = val;
        user_topic_ids+=sep+val.ID;
        sep = ",";
    });

    localStorage.setItem("user_topics",JSON.stringify(ut));
    if(typeof localStorage.getItem("topics_in_collection_setup") == "object" && localStorage.getItem("topics_in_collection_setup")== null){
        LP_set_current_topic(user_topics[0].ID);
    }else{
        LP_set_drip_button();
    }
    LP_fetch_all_topic_future_drips(user_topic_ids);
    LP_fetch_all_topic_history_drips(user_topic_ids);
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

function LP_fetch_all_topic_history_drips(user_topic_ids){
    var data = {
        action : "LP_fetch_all_topic_history_drips",
        user_topic_ids : user_topic_ids
    };

    jQuery.post(ajaxurl,data,function(r){
        if(r!="0"){
            var history_drips = jQuery.parseJSON(r);
            jQuery.each(history_drips,function(i,v){
                var topic_obj = {};
                topic_obj = LP_get_localStorage_topic(i);
                topic_obj["history_drips"] = v;
                LP_update_localStorage_topic(topic_obj);
            });
            LP_set_adjust_page();
        }
    });
}

function LP_set_topic_thumb(post_id, new_thumb){
    var item = jQuery(".post_holder_addtopic[param='"+post_id+"']");
    jQuery("div.addtopic_feat_img_div > img",item).attr("src",new_thumb);
    jQuery("#update_add_topic",item).attr("param","free");
}

function LP_get_user_topics(){
    // Should only allow if logged in.
    var data = {
        action  : 'LP_user_topics'
    };
    jQuery.post(ajaxurl,data,function(r){
        ret = jQuery.parseJSON(r);
        LP_user_topics = ret;
        LP_update_user_topics_storage(ret);
        LP_suggest_images();
//        if(populate){
//            LP_populate_topic_form();
//        }
    });
}

/*function LP_populate_topic_form(){
 var post_ids = "";
 var sep = "";
 jQuery.each(LP_user_topics, function(index,value){
 var topic   = value;
 post_ids    = post_ids + sep + topic["ID"];
 sep         = ",";
 var topic_item = LP_topic_form_loop(topic);
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
 }*/

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
        var obj = jQuery(this);
        while(jQuery(obj).is("form") == false){
            obj = jQuery(obj).parent();
        }
        jQuery(obj).submit();
        add_topic_busy = true;
        add_topic_init = false;
    }else{
        return false;
    }
}

function LP_set_new_topic_form_image(image_details){
    var css = {
        left	: image_details.left,
        top		: image_details.top,
        width	: image_details.width,
        position: "absolute"
    };

    if(image_details.param == "new"){
        jQuery(".body.current_topic .adding_new_topic img#LP_new_topic_image").css(css).attr("src",image_details.src);
        if(image_details.deg !="") jQuery(".body.current_topic .adding_new_topic img#LP_new_topic_image").addClass(image_details.deg);
    }else{
        jQuery(".body.current_topic img#mcurrent_topic_image").css(css).attr("src",image_details.src);
        if(image_details.deg !=""){
            jQuery(".body.current_topic img#mcurrent_topic_image").addClass(image_details.deg);
            jQuery(".body.current_topic .addtopic_feat_img_div.current #deg").val(image_details.deg);
        }

        jQuery(".body.current_topic .addtopic_feat_img_div.current .scaled_width").val(image_details.width);
        jQuery(".body.current_topic .addtopic_feat_img_div.current .top").val(image_details.top);
        jQuery(".body.current_topic .addtopic_feat_img_div.current .left").val(image_details.left);
    }
    var gh 	= jQuery("#grpinddivlog").height();
    jQuery(".topic_tab .the_cropper").animate({top:gh},300,function(){
        jQuery(this).hide();
    });

    var the_mt = (parseFloat(jQuery(".body.current_topic").height()) + 27) * -1;
    jQuery(".body.current_topic").css({"margin-top":the_mt, "visibility": "visible"}).animate({"margin-top":5},300,function(){

    });
}

function LP_done_cropping(){
    var obj = jQuery(this);
    var the_parent = false;
    while(!the_parent){
        if(jQuery(obj).hasClass("the_cropper")) {
            the_parent = true;
            break;
        }else{
            obj = jQuery(obj).parent();
        }
    }

    var the_subject = jQuery(obj).find("img.cropper_subject");

    var deg = "";
    if(jQuery(the_subject).hasClass("rot_0"))deg = "rot_0";
    if(jQuery(the_subject).hasClass("rot_90"))deg = "rot_90";
    if(jQuery(the_subject).hasClass("rot_180"))deg = "rot_180";
    if(jQuery(the_subject).hasClass("rot_270"))deg = "rot_270";

    var image_details = {
        src		: jQuery(the_subject).attr("src"),
        width	: parseFloat(jQuery(the_subject).css("width")),
        left 	: parseFloat(jQuery(the_subject).css("left")) - 40,
        top		: parseFloat(jQuery(the_subject).css("top")) - 40,
        deg		: deg,
        param	: jQuery(this).attr("param")
    };
    LP_set_new_topic_form_image(image_details);
}

function LP_done_cropping_2(){
    LP_next_dz();
    var img_url = jQuery("#drip_zone_2 .the_cropper img.cropper_subject").attr("src");
    if(jQuery("#drip_zone_3 img.cropper_subject").attr("src") == ""){
        jQuery(".redrip_form #notif_bar").addClass("noAction");
        if(LP_drip_zone3(img_url) != false){
            jQuery("form#LP_save_drip_form input[name='use_img1']").val("true");
        }else{
            jQuery("form#LP_save_drip_form input[name='use_img1']").val("false");
        }
    }
}

function LP_new_topic_image(){
    if(jQuery(this).hasClass("current_topic_image")){
        jQuery(".topic_tab .the_cropper #done_cropping").attr("param","current");
    }else{
        jQuery(".topic_tab .the_cropper #done_cropping").attr("param","new");
    }
    var file = jQuery(this).get(0).files[0];
    var textType = /image.*/;

    if (file.type.match(textType)) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var the_image 	= reader.result;
            var gh 			= jQuery("#grpinddivlog").height();
            jQuery(".topic_tab .the_cropper .cropper_subject").attr("src",the_image).attr('style','');

            jQuery(".topic_tab .the_cropper img.cropper_subject").removeClass("rot_90");
            jQuery(".topic_tab .the_cropper img.cropper_subject").removeClass("rot_180");
            jQuery(".topic_tab .the_cropper img.cropper_subject").removeClass("rot_270");
            jQuery(".topic_tab .the_cropper img.cropper_subject").removeClass("rot_0");

            jQuery(".body.current_topic").animate({"margin-top":gh+"px"},300,function(){
                jQuery(this).css("visibility","hidden");
            });
            jQuery(".topic_tab .the_cropper").show();
            var subject_width 			= jQuery(".topic_tab .the_cropper .cropper_subject").width();
            var subject_height 			= jQuery(".topic_tab .the_cropper .cropper_subject").height();
            var cropper_dimension		= {width:495,height:248};
            var cropper_padding 		= 40;
            var to_style;
            var init_width;

            var width_diff = subject_width - cropper_dimension.width;
            // var height_diff = subject_height - cropper_dimension.height;

            if(subject_width > subject_height){
                var new_h = cropper_dimension.width / (subject_width/subject_height);
                if(new_h < cropper_dimension.height){
                    var new_w = (subject_width/subject_height) * cropper_dimension.height;
                    to_style = {"width" : new_w, "min-width":new_w,"left":cropper_padding, top:0};
                    init_width = new_w;
                }else{
                    to_style = {"width" : cropper_dimension.width, "min-width":cropper_dimension.width,"left":cropper_padding, top:0};
                    init_width = cropper_dimension.width;
                }
            }else{
                var new_w = cropper_dimension.height / (subject_height/subject_width);
                init_width = cropper_dimension.width;
                new_w = cropper_dimension.width;
                to_style = {"width" : new_w, "min-width":cropper_dimension.width,"left":cropper_padding, top:0};
            }

            var to_zoom_init = 0;
            var to_grow = 0;
            var LP_close_bottom_and_update_topic = 0;
            if(to_style.hasOwnProperty("width")){
                if(width_diff > (cropper_padding * 2)){
                    to_grow = (cropper_padding * 2);
                    to_style.left = 0;
                }else{
                    to_grow = width_diff;
                    to_style.left = cropper_padding - width_diff;
                }
                to_zoom_init = (to_grow / width_diff) * 270;
                to_style.width+= to_grow;
            }

            jQuery(".topic_tab .the_cropper .cropper_subject").css(to_style);
            jQuery(".topic_tab .the_cropper .cropper_zoom .drag").css("left",to_zoom_init);
            jQuery(".topic_tab .the_cropper").animate({"top":"50px"},300,function(){
                var the_cropper = jQuery(this);
                var start_offset;
                var subject_start_offset;
                var cropper_width = cropper_dimension.width;
                var cropper_height = cropper_dimension.height;

                var l_limit = cropper_padding;
                var t_limit = cropper_padding;
                var r_limit;
                var b_limit;
                jQuery(".cropper_handle",the_cropper).draggable({
                    delay : 0,
                    revert: true,
                    revertDuration: 10,
                    scroll: false,
                    start : function(e,u){
                        start_offset 			= jQuery(u.helper).offset();
                        subject_start_offset 	= {top: parseFloat(jQuery(".cropper_subject",the_cropper).css("top")),left:parseFloat(jQuery(".cropper_subject",the_cropper).css("left"))};
                        subject_width 			= jQuery(".cropper_subject",the_cropper).width();
                        subject_height 			= jQuery(".cropper_subject",the_cropper).height();

                        r_limit	= cropper_width - subject_width + cropper_padding;
                        b_limit	= cropper_height - subject_height + cropper_padding;
                    },
                    drag : function(e,u){
                        var t_offset	= jQuery(u.helper).offset(); // takes the offset position of the bar while dragging
                        var n_top 		= t_offset.top - start_offset.top; // takes the difference of the top before dragged and while dragged. The result is the distance dragged
                        var n_left 		= t_offset.left - start_offset.left; // takes the difference of the left before dragged and while dragged. The result is the distance dragged

                        var s_offset = {
                            top : subject_start_offset.top + n_top,
                            left: subject_start_offset.left + n_left
                        };
                        if(s_offset.left < r_limit) s_offset.left = r_limit;
                        if(s_offset.left > l_limit) s_offset.left = l_limit;

                        if(parseFloat(s_offset.top) < parseFloat(b_limit)) s_offset.top = b_limit;
                        if(s_offset.top > t_limit) s_offset.top = t_limit;

                        jQuery(".cropper_subject",the_cropper).css(s_offset);
                    }
                });


                jQuery(".cropper_zoom .drag",the_cropper).draggable({
                    delay : 0,
                    containment : ".cropper_zoom",
                    axis: "x",
                    drag : function(e,u){
                        var moved 	= parseFloat(jQuery(u.helper).css("left"));
                        var ratio	= moved / parseFloat(jQuery(u.helper).parent().width());
                        var grow	= ratio * width_diff;
                        var new_width = init_width + grow;
                        var the_img = jQuery(u.helper).parent().parent().parent().find("img.cropper_subject");
                        jQuery(the_img).width(new_width);
                        var the_left = parseFloat(jQuery(the_img).css("left"));
                        var the_top = parseFloat(jQuery(the_img).css("top"));
                        var the_height = parseFloat(jQuery(the_img).css("height"));

                        if(parseFloat(new_width + the_left) < parseFloat(cropper_dimension.width + cropper_padding)){
                            var dl = parseFloat(cropper_dimension.width + cropper_padding) - parseFloat(new_width + the_left);
                            the_left+=dl;
                            jQuery(the_img).css("left",the_left);
                        }

                        if(parseFloat(the_height + the_top) < parseFloat(cropper_dimension.height + cropper_padding)){
                            var dt = parseFloat(cropper_dimension.height + cropper_padding) - parseFloat(the_height + the_top);
                            the_top+=dt;
                            jQuery(the_img).css("top",the_top);
                        }
                    }
                });

                jQuery(".cropper_rotate .drag",the_cropper).draggable({
                    delay : 0,
                    containment : ".cropper_rotate",
                    axis: "y",
                    drag : function(e,u){
                        var moved 	= parseFloat(jQuery(u.helper).css("top"));
                        var ratio	= moved / parseFloat(jQuery(u.helper).parent().width());
                        var degrees	= ratio * 360;
                        var the_image = jQuery(u.helper).parent().parent().find("img.cropper_subject");
                        if(moved <62){
                            jQuery("img.cropper_subject").removeClass("rot_90");
                            jQuery("img.cropper_subject").removeClass("rot_180");
                            jQuery("img.cropper_subject").removeClass("rot_270");
                            jQuery("img.cropper_subject").addClass("rot_0");
                        }else if(moved >= 62 && moved < 124){
                            jQuery("img.cropper_subject").removeClass("rot_0");
                            jQuery("img.cropper_subject").removeClass("rot_180");
                            jQuery("img.cropper_subject").removeClass("rot_270");
                            jQuery("img.cropper_subject").addClass("rot_90");
                        }else if(moved >= 124 && moved < 186){
                            jQuery("img.cropper_subject").removeClass("rot_0");
                            jQuery("img.cropper_subject").removeClass("rot_90");
                            jQuery("img.cropper_subject").removeClass("rot_270");
                            jQuery("img.cropper_subject").addClass("rot_180");
                        }else if(moved >= 186){
                            jQuery("img.cropper_subject").removeClass("rot_0");
                            jQuery("img.cropper_subject").removeClass("rot_90");
                            jQuery("img.cropper_subject").removeClass("rot_180");
                            jQuery("img.cropper_subject").addClass("rot_270");
                        }
                        // jQuery(u.helper).parent().parent().find("img.cropper_subject").css({'-webkit-transform' : 'rotate('+degrees+'deg)',
                        // '-moz-transform' : 'rotate('+degrees+'deg)',  
                        // '-ms-transform' : 'rotate('+degrees+'deg)',  
                        // '-o-transform' : 'rotate('+degrees+'deg)',  
                        // 'transform' : 'rotate('+degrees+'deg)',  
                        // 'zoom' : 1});
                    }
                });
            });
        }
        reader.readAsDataURL(file);
    }else{
        LP_console("File not supported!");
    }
}
var zoom = .55;
function LP_drip_zone2(img_url){

    var the_image 	= img_url;
    var exts = the_image.slice(-4).toLowerCase();
    if(exts == "jpeg" || exts == ".jpg" || exts == ".png" || exts == ".gif"){
        // Do nothing
    }else{
        if(exts == ".bmp"){
            jQuery("#drip_zone_2 .cropper_cont .pre_loading").hide();
            return false;
        }
        exts = g_f_img1.slice(-4).toLowerCase();
        if(exts == "jpeg" || exts == ".jpg" || exts == ".png" || exts == ".gif"){
            // Do nothing
        }else{
            jQuery("#drip_zone_2 .cropper_cont .pre_loading").hide();
            return false;
        }
    }
    var theImage = new Image();
    theImage.onload = function(){//do nothing
    };
    theImage.src = img_url;

    var gh 			= jQuery("#grpinddivlog").height();
    var set_img = jQuery("<img/>").attr("src",img_url).addClass("cropper_subject");
//	jQuery("#drip_zone_2 .the_cropper .cropper_subject").attr("src",the_image).removeAttr('style');
    jQuery("#drip_zone_2 .the_cropper .cropper_subject").replaceWith(set_img);

    jQuery("#drip_zone_2 .the_cropper img.cropper_subject").removeClass("rot_90");
    jQuery("#drip_zone_2 .the_cropper img.cropper_subject").removeClass("rot_180");
    jQuery("#drip_zone_2 .the_cropper img.cropper_subject").removeClass("rot_270");
    jQuery("#drip_zone_2 .the_cropper img.cropper_subject").removeClass("rot_0");


    var imageWidth = theImage.width;
    var imageHeight = theImage.height;

    jQuery("#drip_zone_2 .the_cropper").show();
    var subject_width 			= LP_zoom(parseInt(imageWidth), zoom);
    var subject_height 			= LP_zoom(parseInt(imageHeight), zoom);
    var cropper_padding 		= LP_zoom(40,zoom);
    var cropper_dimension		= {};
    cropper_dimension.width 	= LP_zoom(495,zoom);
    cropper_dimension.height 	= LP_zoom(275,zoom);

    if(subject_width < cropper_dimension.width || subject_height < cropper_dimension.height){		/* Tell user to use bigger picture */
        LP_notify_processing_drip("Please use a bigger picture. Picture size should be at least 495x275 pixels.");
        jQuery("#drip_zone_2 .the_cropper .cropper_subject").attr("src","").removeAttr('style');
        return false;
    }else{
        jQuery(".redrip_form #notif_bar").fadeOut(100);
    }

    var cropper_css		= {width:cropper_dimension.width+(cropper_padding*2),height:cropper_dimension.height+(cropper_padding*2)};
    jQuery("#drip_zone_2 .cropper_cont").css(cropper_css);

    var to_style;
    var init_width;

    var width_diff = subject_width - cropper_dimension.width;
    // var height_diff = subject_height - cropper_dimension.height;

    if(subject_width > subject_height){
        var new_h = cropper_dimension.width / (subject_width/subject_height);
        if(new_h < cropper_dimension.height){
            var new_w = (subject_width/subject_height) * cropper_dimension.height;
            to_style = {"width" : new_w, "min-width":new_w,"left":cropper_padding, top:0};
            init_width = new_w;
        }else{
            to_style = {"width" : cropper_dimension.width, "min-width":cropper_dimension.width,"left":cropper_padding, top:0};
            init_width = cropper_dimension.width;
        }
    }else{
        var new_w = cropper_dimension.height / (subject_height/subject_width);
        init_width = cropper_dimension.width;
        new_w = cropper_dimension.width;
        to_style = {"width" : new_w, "min-width":cropper_dimension.width,"left":cropper_padding, top:0};
    }

    var to_zoom_init = 0;
    var to_grow = 0;
    var to_growh = 0;
    if(to_style.hasOwnProperty("width")){
        if(width_diff > (cropper_padding * 2)){
            to_grow = (cropper_padding * 2);
            to_style.left = 0;
            to_zoom_init = (to_grow / width_diff) * 270;
        }else{
            to_grow = width_diff;
            to_style.left = cropper_padding - width_diff;
            to_zoom_init = 0;
        }

        to_style.width+= to_grow;
    }

    jQuery("#drip_zone_2 .the_cropper .cropper_subject").css(to_style);
    if(width_diff>0){
        jQuery("#drip_zone_2 .the_cropper .cropper_zoom").show();
        jQuery("#drip_zone_2 .the_cropper .cropper_zoom .drag").css("left",to_zoom_init);
    }else{
        jQuery("#drip_zone_2 .the_cropper .cropper_zoom").hide();
    }
    var the_cropper = jQuery("#drip_zone_2 .the_cropper");
    var start_offset;
    var subject_start_offset;
    var cropper_width = cropper_dimension.width;
    var cropper_height = cropper_dimension.height;

    var l_limit = cropper_padding;
    var t_limit = cropper_padding;
    var r_limit;
    var b_limit;
    jQuery(".cropper_handle",the_cropper).draggable({
        handle : ".url_catcher",
        cancel : "",
        delay : 0,
        revert: true,
        revertDuration: 10,
        scroll: false,
        start : function(e,u){
            start_offset 			= jQuery(u.helper).offset();
            subject_start_offset 	= {top: parseFloat(jQuery(".cropper_subject",the_cropper).css("top")),left:parseFloat(jQuery(".cropper_subject",the_cropper).css("left"))};
            subject_width 			= jQuery(".cropper_subject",the_cropper).width();
            subject_height 			= jQuery(".cropper_subject",the_cropper).height();

            r_limit	= cropper_width - subject_width + cropper_padding;
            b_limit	= cropper_height - subject_height + cropper_padding;
        },
        drag : function(e,u){
            var t_offset	= jQuery(u.helper).offset(); // takes the offset position of the bar while dragging
            var n_top 		= t_offset.top - start_offset.top; // takes the difference of the top before dragged and while dragged. The result is the distance dragged
            var n_left 		= t_offset.left - start_offset.left; // takes the difference of the left before dragged and while dragged. The result is the distance dragged

            var s_offset = {
                top : subject_start_offset.top + n_top,
                left: subject_start_offset.left + n_left
            };
            if(s_offset.left < r_limit) s_offset.left = r_limit;
            if(s_offset.left > l_limit) s_offset.left = l_limit;

            if(parseFloat(s_offset.top) < parseFloat(b_limit)) s_offset.top = b_limit;
            if(s_offset.top > t_limit) s_offset.top = t_limit;

            jQuery(".cropper_subject",the_cropper).css(s_offset);
        }
    });


    jQuery(".cropper_zoom .drag",the_cropper).draggable({
        delay : 0,
        containment : "#drip_zone_2 .cropper_zoom",
        axis: "x",
        drag : function(e,u){
            var moved 	= parseFloat(jQuery(u.helper).css("left"));
            var ratio	= moved / parseFloat(jQuery(u.helper).parent().width());
            var grow	= ratio * width_diff;
            var new_width = init_width + grow;
            var the_img = jQuery(u.helper).parent().parent().parent().find("img.cropper_subject");
            jQuery(the_img).width(new_width);
            var the_left = parseFloat(jQuery(the_img).css("left"));
            var the_top = parseFloat(jQuery(the_img).css("top"));
            var the_height = parseFloat(jQuery(the_img).css("height"));

            if(parseFloat(new_width + the_left) < parseFloat(cropper_dimension.width + cropper_padding)){
                var dl = parseFloat(cropper_dimension.width + cropper_padding) - parseFloat(new_width + the_left);
                the_left+=dl;
                jQuery(the_img).css("left",the_left);
            }

            if(parseFloat(the_height + the_top) < parseFloat(cropper_dimension.height + cropper_padding)){
                var dt = parseFloat(cropper_dimension.height + cropper_padding) - parseFloat(the_height + the_top);
                the_top+=dt;
                jQuery(the_img).css("top",the_top);
            }
        }
    });

    jQuery(".cropper_rotate .drag",the_cropper).draggable({
        delay : 0,
        containment : "#drip_zone_2 .cropper_rotate",
        axis: "x",
        drag : function(e,u){
            var moved 	= parseFloat(jQuery(u.helper).css("left"));
            var ratio	= moved / parseFloat(jQuery(u.helper).parent().width());
            var degrees	= ratio * 360;
            var the_image = jQuery(u.helper).parent().parent().find("img.cropper_subject");
            if(moved <62){
                jQuery("img.cropper_subject",the_cropper).removeClass("rot_90");
                jQuery("img.cropper_subject",the_cropper).removeClass("rot_180");
                jQuery("img.cropper_subject",the_cropper).removeClass("rot_270");
                jQuery("img.cropper_subject",the_cropper).addClass("rot_0");
            }else if(moved >= 62 && moved < 124){
                jQuery("img.cropper_subject",the_cropper).removeClass("rot_0");
                jQuery("img.cropper_subject",the_cropper).removeClass("rot_180");
                jQuery("img.cropper_subject",the_cropper).removeClass("rot_270");
                jQuery("img.cropper_subject",the_cropper).addClass("rot_90");
            }else if(moved >= 124 && moved < 186){
                jQuery("img.cropper_subject",the_cropper).removeClass("rot_0");
                jQuery("img.cropper_subject",the_cropper).removeClass("rot_90");
                jQuery("img.cropper_subject",the_cropper).removeClass("rot_270");
                jQuery("img.cropper_subject",the_cropper).addClass("rot_180");
            }else if(moved >= 186){
                jQuery("img.cropper_subject",the_cropper).removeClass("rot_0");
                jQuery("img.cropper_subject",the_cropper).removeClass("rot_90");
                jQuery("img.cropper_subject",the_cropper).removeClass("rot_180");
                jQuery("img.cropper_subject",the_cropper).addClass("rot_270");
            }
        }
    });

    jQuery(".cropper_handle",the_cropper)
        .mousewheel(function(event, delta, deltaX, deltaY){
            var moffset = {x : event.offsetX, y : event.offsetY};
            var u = {helper: jQuery(".cropper_zoom .drag",the_cropper)};
            var moved 	= parseFloat(jQuery(u.helper).css("left"))+(deltaY*10);
            var scroll_width = parseFloat((jQuery(u.helper).parent().width())-25);
            if(moved < 0 ) moved = 0;
            else if(moved > scroll_width) moved = scroll_width;
            jQuery(u.helper).css("left",moved);

            var ratio	= moved / scroll_width;
            var grow	= ratio * width_diff;
            var new_width = init_width + grow;
            var the_img = jQuery(u.helper).parent().parent().parent().find("img.cropper_subject");
            var simg = {width: jQuery(the_img).width(), height : jQuery(the_img).height()};

            jQuery(the_img).width(new_width);
            var the_left = parseFloat(jQuery(the_img).css("left"));
            var the_top = parseFloat(jQuery(the_img).css("top"));
            var the_height = parseFloat(jQuery(the_img).css("height"));
            if(Math.floor(new_width) != simg.width){
                // this should be the computation for the growth percentag.
                var growth = (new_width- simg.width)/simg.width;
                the_left = ((((the_left * -1) + moffset.x) * (1+growth)) - moffset.x ) * -1;
                the_top  = ((((the_top  * -1) + moffset.y) * (1+growth)) - moffset.y ) * -1;

                if(parseFloat(new_width + the_left) < parseFloat(cropper_dimension.width + cropper_padding)){
                    var dl = parseFloat(cropper_dimension.width + cropper_padding) - parseFloat(new_width + the_left);
                    the_left+=dl;
                }

                if(parseFloat(the_height + the_top) < parseFloat(cropper_dimension.height + cropper_padding)){
                    var dt = parseFloat(cropper_dimension.height + cropper_padding) - parseFloat(the_height + the_top);
                    the_top+=dt;
                }

                if(the_left > cropper_padding) the_left = cropper_padding;
                if(the_top > cropper_padding) the_top = cropper_padding;
                jQuery(the_img).css("left",the_left);
                jQuery(the_img).css("top",the_top);
            }
        });
}

function LP_set_drip_image(img_url){
    if(typeof img_url != "string"){
        img_url = jQuery(this).attr("alt");
    }
    if(jQuery(".dz.active").attr("id") == "drip_zone_3"){
        LP_drip_zone3(img_url);
        jQuery("form#LP_save_drip_form input[name='use_img1']").val("false");
        jQuery("form#LP_save_drip_form input[name='f_img2']").remove();
    }else{
        jQuery(".redrip_form .imgblogpostx > img").attr("src",img_url);
        jQuery(".redrip_form .inblogpostbody #img1").val(img_url);
        jQuery("form#LP_save_drip_form input[name='f_img1']").remove();
        LP_set_bcropper_images(img_url);
    }
}

var g_f_img1;
var g_f_img2;

function LP_set_cropper_subject_from_file(files){
    var file = files[0];
    var textType = /image.*/;

    if (file.type.match(textType)) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var the_image 	= reader.result;
            LP_set_drip_image(the_image);
            jQuery("ul.image_suggestions li img.active").show().removeClass("active");
        }
        reader.readAsDataURL(file);
        if(jQuery(".dz.active").attr("id") == "drip_zone_3"){
            g_f_img2 = {file: files[0], filename : files[0].name};
        }else{
            g_f_img1 = {file: files[0], filename : files[0].name};
        }

    }
}

//global
var dripzone_image = [];
function LP_prepare_dripzone_image(img_url){

    var theImage = new Image();
    theImage.src = img_url;
    var imageWidth = theImage.width;
    if(typeof imageWidth == "number" && imageWidth > 0){
        LP_set_drip_image(img_url);
        if(jQuery(".dz.active").attr("id") == "drip_zone_3"){
            img = 1;
            jQuery("#drip_zone_3 .cropper_cont .pre_loading").hide();
            jQuery("#drip_zone_3 .cropper_cont .cropper_subject").show();
        }else{
            jQuery("#drip_zone_2 .cropper_cont .pre_loading").hide();
            jQuery("#drip_zone_2 .cropper_cont .cropper_subject").show();
        }
        return true;
    }

    var img = 0;
    if(jQuery(".dz.active").attr("id") == "drip_zone_3"){
        img = 1;
        jQuery("#drip_zone_3 .cropper_cont .pre_loading").show();
        jQuery("#drip_zone_3 .cropper_cont .cropper_subject").hide();
    }else{
        jQuery("#drip_zone_2 .cropper_cont .pre_loading").show();
        jQuery("#drip_zone_2 .cropper_cont .cropper_subject").hide();
    }

    dripzone_image[img]   =   jQuery("<img/>")
        .attr("src",img_url)
        //.unbind("load")
        .load(function() {
            var img_url = jQuery(this).attr("src");
            LP_set_drip_image(img_url);
            if(jQuery(".dz.active").attr("id") == "drip_zone_3"){
                jQuery("#drip_zone_3 .cropper_cont .pre_loading").hide();
                jQuery("#drip_zone_3 .cropper_cont .cropper_subject").show();
            }else{
                jQuery("#drip_zone_2 .cropper_cont .pre_loading").hide();
                jQuery("#drip_zone_2 .cropper_cont .cropper_subject").show();
            }
        })
        .error(function(){
            LP_console("hase error");
            LP_notify_processing_drip("Error while trying to load the image... Please try using different image.");
        });
}

function LP_set_cropper_subject(){
    var obj = jQuery(this);
    var file = jQuery(obj).get(0).files[0];
    var textType = /image.*/;

    if (file.type.match(textType)) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var the_image 	= reader.result;
            eval(jQuery(obj).attr("param")+"(the_image);");
            eval(jQuery(obj).attr("param")+"_set_file(obj);");
        }
        reader.readAsDataURL(file);
    }
}

function LP_drip_zone3_set_file(obj){
    // clone the original input file .. and use it to replace to itself...
    // the original input file then be transferred to the hidden form to be submitted later...
    var tfile =  jQuery(obj);
    var tparent =  jQuery(obj).parent();
    var tfile_clone =  jQuery(obj).clone();
    jQuery(tparent).append(tfile_clone);
    jQuery("form#LP_save_drip_form input[name='f_img2']").remove();
    jQuery(tfile).attr("name","f_img2");
    jQuery("form#LP_save_drip_form").append(tfile);
    jQuery("form#LP_save_drip_form input[name='use_img1']").val("false");
}

function LP_drip_zone2_set_file(obj){
    // clone the original input file .. and use it to replace to itself...
    // the original input file then be transferred to the hidden form to be submitted later...
    var tfile =  jQuery(obj);
    var tparent =  jQuery(obj).parent();
    var tfile_clone =  jQuery(obj).clone();
    jQuery(tparent).append(tfile_clone);

    if(jQuery("form#LP_save_drip_form input[name='use_img1']").val() == "true"){
        jQuery("form#LP_save_drip_form input[name='use_img1']").val("false");
        var to2 = jQuery("form#LP_save_drip_form input[name='f_img1']");
        jQuery("form#LP_save_drip_form input[name='f_img2']").remove();
        jQuery(to2).attr("name","f_img2");
        jQuery("form#LP_save_drip_form").append(to2);
    }else{
        jQuery("form#LP_save_drip_form input[name='f_img1']").remove();
    }

    jQuery(tfile).attr("name","f_img1");
    jQuery("form#LP_save_drip_form").append(tfile);
}

function LP_zoom(sub,val){
    return (sub * val);
}

function LP_drip_zone3(img_url){

    var the_image 	= img_url;
    var exts = the_image.slice(-4).toLowerCase();
    if(exts == "jpeg" || exts == ".jpg" || exts == ".png" || exts == ".gif"){
        // Do nothing
    }else{
        if(exts == ".bmp"){
            jQuery("#drip_zone_3 .cropper_cont .pre_loading").hide();
            return false;
        }
        exts = g_f_img2.slice(-4).toLowerCase();
        if(exts == "jpeg" || exts == ".jpg" || exts == ".png" || exts == ".gif"){
            // Do nothing
        }else{
            jQuery("#drip_zone_3 .cropper_cont .pre_loading").hide();
            return false;
        }
    }

    var gh 			= jQuery("#grpinddivlog").height();
    var min_image_height = LP_zoom(550,zoom);
    var image_maxh = 1200;
    var set_img = jQuery("<img/>").attr("src",img_url).addClass("cropper_subject");
//	jQuery("#drip_zone_3 .the_cropper .cropper_subject").attr("src",the_image).removeAttr('style');
    jQuery("#drip_zone_3 .the_cropper .cropper_subject").replaceWith(set_img);

    jQuery("#drip_zone_3 .the_cropper img.cropper_subject").removeClass("rot_90");
    jQuery("#drip_zone_3 .the_cropper img.cropper_subject").removeClass("rot_180");
    jQuery("#drip_zone_3 .the_cropper img.cropper_subject").removeClass("rot_270");
    jQuery("#drip_zone_3 .the_cropper img.cropper_subject").removeClass("rot_0");

    var theImage = new Image();
    theImage.src = img_url;
    var imageWidth = theImage.width;
    var imageHeight = theImage.height;

    jQuery("#drip_zone_3 .the_cropper").show();
    var subject_width 			= LP_zoom(parseInt(imageWidth),zoom);
    var subject_height 			= LP_zoom(parseInt(imageHeight),zoom);
    var cropper_padding 		= LP_zoom(40,zoom);
    var max_cropped_height 		= LP_zoom(image_maxh,zoom);
    var cropper_dimension		= {};
    cropper_dimension.width 	= LP_zoom(495,zoom);

    var cropper_taller_width 	= parseFloat(jQuery("#drip_zone_3 .the_cropper .cropper_taller").width()) - parseFloat(jQuery("#drip_zone_3 .the_cropper .cropper_taller .drag").width());
    var scrollers_width = cropper_taller_width;
    /* Determining the height of the cropper */
    var cheight = subject_height - (((subject_width - cropper_dimension.width)/subject_width)*subject_height);
    /*var cropper_init_height 		= LP_zoom(550,zoom);*/
    var cropper_init_height 		= LP_zoom(image_maxh,zoom);

//	var cropper_taller_init_height 	= 0;
//	if(cheight > cropper_init_height){
//		cropper_dimension.height = cheight;
//		cropper_taller_init_height = cropper_taller_width*((cheight - cropper_init_height)/(max_cropped_height - cropper_init_height));
//	}else{
//		cropper_dimension.height = cropper_init_height;
//	}
    cropper_dimension.height = cropper_init_height;

//	jQuery("#drip_zone_3 .the_cropper .cropper_taller .drag").css("top",cropper_taller_init_height);

    var cropper_css		= {width:cropper_dimension.width+(cropper_padding*2),height:cropper_dimension.height+(cropper_padding*2)};

    jQuery("#drip_zone_3 .cropper_cont").css(cropper_css);


    if(parseInt(imageWidth) < 495 || parseInt(imageHeight) < 550){
        /* Tell user to use bigger picture */
        jQuery("#drip_zone_3 .the_cropper .cropper_subject").attr("src","").removeAttr('style');
        if(jQuery(".redrip_form #notif_bar").hasClass("noAction") == false){
            LP_notify_processing_drip("Please use a bigger picture. Picture size should be at least 495x550 pixels.");
        }else{
            jQuery(".redrip_form #notif_bar").removeClass("noAction");
        }
        return false;
    }else{
        jQuery(".redrip_form #notif_bar").removeClass("noAction");
        jQuery(".redrip_form #notif_bar").fadeOut(100);
    }

    var to_style;
    var init_width;



    var new_w = min_image_height / (subject_height/subject_width);
    if(new_w >= cropper_dimension.width){
        to_style = {"width" : new_w, "min-width":new_w, "left": cropper_padding, "top": cropper_padding};
        init_width = new_w;
    }else{
        var new_h = cropper_dimension.width / (subject_width/subject_height);
        new_w = new_w = new_h / (subject_height/subject_width);
        to_style = {"width" : new_w, "min-width":new_w, "left": cropper_padding, "top": cropper_padding};
        init_width = new_w;
    }

    var width_diff = subject_width - init_width;
    var to_zoom_init = 0;
    var to_grow = 0;

    jQuery("#drip_zone_3 .the_cropper .cropper_subject").css(to_style);
    if(width_diff>0){
        jQuery("#drip_zone_3 .the_cropper .cropper_zoom").show();
        jQuery("#drip_zone_3 .the_cropper .cropper_zoom .drag").css("left",0);
    }else{
        jQuery("#drip_zone_3 .the_cropper .cropper_zoom").hide();
    }
    var the_cropper = jQuery("#drip_zone_3 .the_cropper");
    var start_offset;
    var subject_start_offset;
    var cropper_width = cropper_dimension.width;
    var cropper_height = cropper_dimension.height;

    var l_limit = cropper_padding;
    var t_limit = cropper_padding;
    var r_limit;
    var b_limit;

    jQuery(".cropper_handle",the_cropper).draggable({
        handle : ".url_catcher",
        cancel : "",
        delay : 0,
        revert: true,
        revertDuration: 10,
        scroll: false,
        start : function(e,u){
            start_offset 			= jQuery(u.helper).offset();
            subject_start_offset 	= {top: parseFloat(jQuery(".cropper_subject",the_cropper).css("top")),left:parseFloat(jQuery(".cropper_subject",the_cropper).css("left"))};
            subject_width 			= jQuery(".cropper_subject",the_cropper).width();
            subject_height 			= jQuery(".cropper_subject",the_cropper).height();

            r_limit	= cropper_width - subject_width + cropper_padding;
            b_limit	= cropper_height - subject_height + cropper_padding;
        },
        drag : function(e,u){
            var t_offset	= jQuery(u.helper).offset(); // takes the offset position of the bar while dragging
            var n_top 		= t_offset.top - start_offset.top; // takes the difference of the top before dragged and while dragged. The result is the distance dragged
            var n_left 		= t_offset.left - start_offset.left; // takes the difference of the left before dragged and while dragged. The result is the distance dragged

            var s_offset = {
                top : subject_start_offset.top + n_top,
                left: subject_start_offset.left + n_left
            };
            if(s_offset.left < r_limit) s_offset.left = r_limit;
            if(s_offset.left > l_limit) s_offset.left = l_limit;

            // THis prevents from dragging way up the image....
            /*if(parseFloat(s_offset.top) < parseFloat(b_limit)) s_offset.top = b_limit;*/

            // This prevents from dragging way down the image...
            /*if(s_offset.top > t_limit) s_offset.top = t_limit;*/
            var to_top = 0;
            var min_h = LP_zoom(550,zoom);
            var i_h = parseInt(jQuery(".cropper_subject",the_cropper).height());
            var visible_height = s_offset.top - cropper_padding + i_h;

            if(visible_height > cropper_init_height){
                var v_h = i_h - (visible_height - cropper_init_height);
                if(v_h < min_h){
                    s_offset.top = s_offset.top - (min_h - v_h);
                }
            }else{
                if(visible_height < min_h){
                    s_offset.top = s_offset.top + (min_h - visible_height);
                }
            }
            jQuery(".cropper_subject",the_cropper).css(s_offset);
        }
    });


    jQuery(".cropper_zoom .drag",the_cropper).draggable({
        delay : 0,
        containment : "#drip_zone_3 .cropper_zoom",
        axis: "x",
        drag : function(e,u){
            var moved 	= parseFloat(jQuery(u.helper).css("left"));
            var ratio	= moved / parseFloat((jQuery(u.helper).parent().width())-25);
            var grow	= ratio * width_diff;
            var new_width = init_width + grow;
            var the_img = jQuery(u.helper).parent().parent().parent().find("img.cropper_subject");
            jQuery(the_img).width(new_width);
            var the_left = parseFloat(jQuery(the_img).css("left"));
            var the_top = parseFloat(jQuery(the_img).css("top"));
            var the_height = parseFloat(jQuery(the_img).css("height"));

            if(parseFloat(new_width + the_left) < parseFloat(cropper_dimension.width + cropper_padding)){
                var dl = parseFloat(cropper_dimension.width + cropper_padding) - parseFloat(new_width + the_left);
                the_left+=dl;
                jQuery(the_img).css("left",the_left);
            }

            if(parseFloat(the_height + the_top) < parseFloat(LP_zoom(550,zoom) + cropper_padding)){
                // var dt = parseFloat(cropper_dimension.height + cropper_padding) - parseFloat(the_height + the_top);
                // the_top+=dt;
                jQuery(the_img).css("top",cropper_padding);
            }
        }
    });

    jQuery(".cropper_taller .drag",the_cropper).draggable({
        delay : 0,
        containment : "#drip_zone_3 .cropper_taller",
        axis: "x",
        drag : function(e,u){
            var moved 	= parseFloat(jQuery(u.helper).css("left"));
            var ratio	= moved / cropper_taller_width;
            var max_h = max_cropped_height;
            if(subject_height < max_h){
                max_h = subject_height;
            }
            var grow	= ratio * (max_h - cropper_init_height);
            var new_height = cropper_init_height + grow;
            cropper_dimension.height = new_height;
            var cropper_cont = jQuery("#drip_zone_3 .cropper_cont");
            jQuery(cropper_cont).height(new_height + (cropper_padding*2));
        }
    });

    jQuery(".cropper_rotate .drag",the_cropper).draggable({
        delay : 0,
        containment : "#drip_zone_3 .cropper_rotate",
        axis: "x",
        drag : function(e,u){
            var moved 	= parseFloat(jQuery(u.helper).css("left"));
            var ratio	= moved / parseFloat(jQuery(u.helper).parent().height());
            var degrees	= ratio * 360;
            var the_image = jQuery(u.helper).parent().parent().find("img.cropper_subject");
            if(moved <62){
                jQuery("img.cropper_subject",the_cropper).removeClass("rot_90");
                jQuery("img.cropper_subject",the_cropper).removeClass("rot_180");
                jQuery("img.cropper_subject",the_cropper).removeClass("rot_270");
                jQuery("img.cropper_subject",the_cropper).addClass("rot_0");
            }else if(moved >= 62 && moved < 124){
                jQuery("img.cropper_subject",the_cropper).removeClass("rot_0");
                jQuery("img.cropper_subject",the_cropper).removeClass("rot_180");
                jQuery("img.cropper_subject",the_cropper).removeClass("rot_270");
                jQuery("img.cropper_subject",the_cropper).addClass("rot_90");
            }else if(moved >= 124 && moved < 186){
                jQuery("img.cropper_subject",the_cropper).removeClass("rot_0");
                jQuery("img.cropper_subject",the_cropper).removeClass("rot_90");
                jQuery("img.cropper_subject",the_cropper).removeClass("rot_270");
                jQuery("img.cropper_subject",the_cropper).addClass("rot_180");
            }else if(moved >= 186){
                jQuery("img.cropper_subject",the_cropper).removeClass("rot_0");
                jQuery("img.cropper_subject",the_cropper).removeClass("rot_90");
                jQuery("img.cropper_subject",the_cropper).removeClass("rot_180");
                jQuery("img.cropper_subject",the_cropper).addClass("rot_270");
            }
        }
    });

    jQuery(".cropper_subject_width .drag",the_cropper).draggable({
        delay : 0,
        containment : "#drip_zone_3 .cropper_subject_width",
        axis: "x",
        drag : function(e,u){

        }
    });

    jQuery(".cropper_subject_height .drag",the_cropper).draggable({
        delay : 0,
        containment : "#drip_zone_3 .cropper_subject_height",
        axis: "x",
        drag : function(e,u){

        }
    });

    jQuery(".cropper_handle",the_cropper)
        .mousewheel(function(event, delta, deltaX, deltaY) {
            var moffset = {x : event.offsetX, y : event.offsetY};
            var u = {helper: jQuery(".cropper_zoom .drag",the_cropper)};
            var moved 	= parseFloat(jQuery(u.helper).css("left"))+(deltaY*10);
            var scroll_width = parseFloat((jQuery(u.helper).parent().width())-25);
            if(moved < 0 ) moved = 0;
            else if(moved > scroll_width) moved = scroll_width;
            jQuery(u.helper).css("left",moved);

            var ratio	= moved / scroll_width;
            var grow	= ratio * width_diff;
            var new_width = init_width + grow;
            var the_img = jQuery(u.helper).parent().parent().parent().find("img.cropper_subject");
            var simg = {width: jQuery(the_img).width(), height : jQuery(the_img).height()};

            jQuery(the_img).width(new_width);
            var the_left = parseFloat(jQuery(the_img).css("left"));
            var the_top = parseFloat(jQuery(the_img).css("top"));
            var the_height = parseFloat(jQuery(the_img).css("height"));

            if(Math.floor(new_width) != simg.width){
                // this should be the computation for the growth percentage.
                var growth = (new_width- simg.width)/simg.width;
                the_left = ((((the_left * -1) + moffset.x) * (1+growth)) - moffset.x ) * -1;
                the_top  = ((((the_top  * -1) + moffset.y) * (1+growth)) - moffset.y ) * -1;

                if(parseFloat(new_width + the_left) < parseFloat(cropper_dimension.width + cropper_padding)){
                    var dl = parseFloat(cropper_dimension.width + cropper_padding) - parseFloat(new_width + the_left);
                    the_left+=dl;
                }

                if(parseFloat(the_height + the_top) < parseFloat(LP_zoom(550,zoom) + cropper_padding)){
                    // var dt = parseFloat(cropper_dimension.height + cropper_padding) - parseFloat(the_height + the_top);
                    // the_top+=dt;
                    the_top = cropper_padding;
                }

                if(the_left > cropper_padding) the_left = cropper_padding;
                jQuery(the_img).css("left",the_left);
                jQuery(the_img).css("top",the_top);
            }
        });

    return true;
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

function LP_new_topic_back(){
    var data = {
        action				: "LP_update_topic_settings",
        topic				: jQuery("#topic_back").val(),
        LP_channel 			: jQuery(".body.current_topic #new_topic_channel").val(),
        new_stiky_drip 		: jQuery(".body.current_topic #new_stiky_drip").val(),
        new_industry 		: jQuery(".body.current_topic #new_industry").val(),
        new_language 		: jQuery(".body.current_topic #new_language").val(),
        new_results 		: jQuery(".body.current_topic #new_results").is(":checked"),
        new_messages 		: jQuery(".body.current_topic #new_messages").is(":checked"),
        new_iframe 			: jQuery(".body.current_topic #new_iframe").is(":checked"),
        new_dripurl 		: jQuery(".body.current_topic #new_dripurl").is(":checked"),
        new_trash 			: jQuery(".body.current_topic #new_trash").is(":checked"),
        new_private 		: jQuery(".body.current_topic #new_private").is(":checked"),
        new_flip 			: jQuery(".body.current_topic #new_flip").is(":checked"),
        new_timezone 		: jQuery(".body.current_topic #new_timezone").is(":checked")
    };
    if(data.LP_channel > 0 && (data.topic != "" || typeof data.topic != 'undefined')){
        jQuery.post(ajaxurl,data,function(r){
            /* SHOW TOPIC COLLECTION SETUP */
            LP_goto_topic_setup(data.topic);
        });
    }
}

function LP_submit_add_topic(){
    var obj = jQuery(this);
    if(jQuery(obj).hasClass("busy") == false){
        jQuery(obj).addClass("busy");
        var topic_title = jQuery(".body.current_topic .new_topic_title").val();
        var topic_description = jQuery(".body.current_topic .new_topic_description").val();
        var session_name = jQuery("#session_name").val();

        if(topic_title && topic_description){
            var data = {
                action  		: "LP_save_add_topic",
                type    		: "topic",
                topic    		: jQuery("form#LP_new_topic_uploader .topic").val(),
                // channel 		: topic_channel,
                wpbody  		: topic_description,
                wptitle 		: topic_title
                // the_image		: jQuery(".body.current_topic img#LP_new_topic_image").attr("src"),
                // fakepath		: jQuery(".body.current_topic #new_topic_image").val(),
                // scaled_width	: parseFloat(jQuery(".body.current_topic img#LP_new_topic_image").css("width")),
                // top				: parseFloat(jQuery(".body.current_topic img#LP_new_topic_image").css("top")),
                // left			: parseFloat(jQuery(".body.current_topic img#LP_new_topic_image").css("left"))
            };
            jQuery("form#LP_new_topic_uploader #type").val("topic");
            // jQuery("form#LP_new_topic_uploader #channel").val(topic_channel);
            jQuery("form#LP_new_topic_uploader #wpbody").val(topic_description);
            jQuery("form#LP_new_topic_uploader #wptitle").val(topic_title);
            jQuery("form#LP_new_topic_uploader .scaled_width").val(parseFloat(jQuery(".body.current_topic img#LP_new_topic_image").css("width")));
            jQuery("form#LP_new_topic_uploader .top").val(parseFloat(jQuery(".body.current_topic img#LP_new_topic_image").css("top")));
            jQuery("form#LP_new_topic_uploader .left").val(parseFloat(jQuery(".body.current_topic img#LP_new_topic_image").css("left")));


            if(jQuery("form#LP_new_topic_uploader .topic").val()!=""){
                data.action = "LP_update_topic";
            }


            jQuery.post(ajaxurl,data,function(r){
                jQuery(obj).removeClass("busy").addClass("update");
                var topic = jQuery.parseJSON(r);

                var temp_topics = new Array();
                temp_topics[0] = topic[0];

                var default_collection_setup = JSON.stringify({
                    search_sources  : {news:1,blogs:0,twitter:0,dripple:0},
                    search_keywords : {},
                    rss_feed_links  : {}
                });
                var ut={};
                ut[topic[0].ID] = topic[0];
                if(topic[0].collection_setup === null){
                    topic[0].collection_setup = default_collection_setup;
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
                LP_populate_topic_tabs();
                if(r != "0"){
                    jQuery("form#LP_new_topic_uploader .topic").val(topic[0].ID);
                    jQuery("form#LP_new_topic_uploader").submit();
                    jQuery("#topic_back").val(topic[0].ID);
                    jQuery(".body.current_topic .post_holder_addtopic.new_topic i.flip_me").trigger("click");
                }
            });
        }else{
            alert("Please fill-up form");
        }
    }
}

function LP_toggle_follow(){
    var obj = jQuery(this);
    var the_parent = jQuery(this).parent();
    if(jQuery(the_parent).hasClass("child")){
        the_parent = jQuery(the_parent).parent().parent();
    }
    var data = {
        action : "LP_toggle_follow",
        channel : jQuery(the_parent).attr("param")
    };

    jQuery.post(ajaxurl,data,function(r){
        // alert(r);
        ret = jQuery.parseJSON(r);
        LP_channel_settings = ret.LP_channel_settings;
    });

    if(jQuery(the_parent).hasClass("active")){
        jQuery(the_parent).removeClass("active");
        jQuery(".UFollow",the_parent).text("+ Follow");
        LP_move_to_unactive(jQuery(the_parent));
    }else{
        jQuery(the_parent).addClass("active");
        jQuery(".UFollow",the_parent).text("- Unfollow");
        LP_move_to_active(jQuery(the_parent));
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
    // jQuery(".followchandiv,.ufollowchandiv").css("display","none");
}

function LP_move_to_active(obj){
    jQuery("ul#chansort").prepend(jQuery(obj));
    // jQuery(".followchandiv,.ufollowchandiv").css("display","none");
}

function animslide_ufollow(obj){
    // if(jQuery(obj).parent().hasClass("active")){
    // jQuery(".ufollowchandiv",obj).css("display","block");
    // }else{
    // jQuery(".followchandiv",obj).css("display","block");
    // }
    jQuery(obj).siblings(".UFollow").stop().animate({"margin-left":0},100);
}

function animslout_ufollow(obj){
    // jQuery(obj).stop().animate({"width":26},100,function(){
    // if(jQuery(obj).parent().hasClass("active")){
    // jQuery(".ufollowchandiv",obj).css("display","none");
    // }else{
    // jQuery(".followchandiv",obj).css("display","none");
    // }
    // });
    jQuery(obj).siblings(".UFollow").stop().animate({"margin-left":"100%"},100);
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
        var d_title    	 = jQuery("#adjust_preview textarea#drip_title").is(":focus");
        var t_adjust     = jQuery("#adjust_preview textarea#the_adjust").is(":focus");
        var t_content    = jQuery("#adjust_preview textarea#the_content").is(":focus");
        if(!d_title && !t_adjust && !t_content){
            LP_update_drip();
        }
    }
}

function LP_update_drip(){
    var title 		    = jQuery("#adjust_preview textarea#drip_title").val();
    var excerpt			= jQuery("#adjust_preview textarea#the_adjust").val();
    var post_content 	= jQuery("#adjust_preview textarea#the_content").val();
    var post_id 		= jQuery("#adjust_preview input#post_id").val();

    LP_adjust_form_higlight();
    if(post_id!=""){
        if(LP_check_if_changed(post_id)){
            if(excerpt!="" && title!="" && post_content!="" && post_id!=""){
                jQuery("#adjust_preview textarea").removeClass("required");
                var data = {
                    action  : "LP_adjust_update_drip_post",
                    title   : title,
                    excerpt : excerpt,
                    content : post_content,
                    post_id : post_id
                };

                jQuery("#adjust_sched .adjust_topics[param='"+post_id+"'] .adjust_topic_title span").text(title);
                jQuery("#update_drip").text("Saving...").css("cursor","progress");
                jQuery.post(ajaxurl,data,function(r){
                    drip_obj = jQuery.parseJSON(r);
                    LP_update_localStorage_drip(drip_obj);
                    jQuery("#update_drip").text("Update").css("cursor","pointer");
                });
            }else{
                if(excerpt==""){
                    jQuery("#adjust_preview textarea#the_adjust").addClass("required");
                }else{
                    jQuery("#adjust_preview textarea#the_adjust").removeClass("required");
                }

                if(title==""){
                    jQuery("#adjust_preview textarea#drip_title").addClass("required");
                }else{
                    jQuery("#adjust_preview textarea#drip_title").removeClass("required");
                }

                if(post_content==""){
                    jQuery("#adjust_preview textarea#the_content").addClass("required");
                }else{
                    jQuery("#adjust_preview textarea#the_content").removeClass("required");
                }
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
    var title 			= jQuery("#adjust_preview textarea#drip_title").val();
    var excerpt			= jQuery("#adjust_preview textarea#the_adjust").val();
    var post_content 	= jQuery("#adjust_preview textarea#the_content").val();
    var is_history		= jQuery("#adjust_preview input#is_history").val();
    if(is_history == "true"){
        var drip = LP_get_localStorage_history_drip(drip_id);
    }else{
        var drip = LP_get_localStorage_future_drip(drip_id);
    }
    var excerpt_o 		= drip.post_excerpt;
    var title_o 		= drip.post_title;
    var post_content_o 	= drip.post_content;

    if((excerpt_o != excerpt || title_o != title || post_content_o != post_content) && is_history!="true"){
        return true;
    }else{
        return false;
    }
}

jQuery(function() {

    jQuery( "#chansort" ).sortable({
        placeholder: "chan-state-highlight",
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
    var num_tabs = jQuery(".dripheadbtn .drips_con").length;
    jQuery("div.drips_con").css("width",xwidth+"px");
    jQuery(".dripheadbtn").css("width",(100*num_tabs)+"%");
    // jQuery(".dripheadbtn").css("width",(xwidth*num_tabs+350)+"px");
    drip_sliding_page('snap');
}

function LP_set_top_tabs(){
    jQuery(".dripheadbtn .drips_con").each(function(i,v){
        var who = jQuery(this).attr("id");
        jQuery("#drip_nav").append("<li class=\"liDRIPbtn\">\
									<p class=\"contbtndrip\" id=\"drip_"+who+"\">"+who+"</p>\
									<!---div class=\"imgDRIPicon\"></div---->\
							</li>");
    });
}

/* 
 * This initiates the draggable for the top splash screen bar 
 */
function LP_reset_draggable_bar(){
    var t_splash_top;
    var top_splash_height;
    // jQuery(".header_drag_containment").css("height",win_height+"px").css("background-color","red");
    jQuery("#t_splash_cont .tab_bar_drag").draggable({
        axis:'y',
        handle: '.drag_cont',
        delay : 0,
        containment : ".header_drag_containment2",
        scroll: false,
        start : function(e,u){
            var win_height 	= jQuery(window).height() - 80 - 150;
            var t_offset = jQuery(u.helper).offset();
            t_splash_top = t_offset.top; // Get the top value of the bar right at the start of dragging
            top_splash_height = jQuery(".dripgrpDIV").height(); // the height of the top splash screen before dragged
            hide_page_slider_arrows();
        },
        drag : function(){
            var t_offset = jQuery(this).offset(); // takes the offset position of the bar while dragging
            var n_top = t_offset.top - t_splash_top; // takes the difference of the top before dragged and while dragged. The result is the distance dragged
            page_height = top_splash_height + n_top; // Adding the height of the top screen and the distance dragged is the new height for the top screen

            jQuery(".dripgrpDIV").height(page_height);
        },
        stop : function(){
            var t_offset = jQuery(this).offset();
            var n_top = t_offset.top - t_splash_top;

            page_height = top_splash_height + n_top;
            jQuery(".dripgrpDIV").height(page_height);

            var n_h = page_height - 95;

            var cal_height = n_h - parseInt(jQuery(".quick_search").height());

            jQuery("#t_splash_google_suggestions").height(cal_height);
            jQuery("#topic_collection_rss_setup .results_news_tpl1").height(n_h);
            jQuery("#buffer #adjust_sched").height(n_h);

            jQuery("#topic_collection_rss_setup .results_news_tpl1").mCustomScrollbar("destroy");
            jQuery("#topic_collection_rss_setup .results_news_tpl1").mCustomScrollbar({
                theme:"dark-thick",
                scrollInertia:10
            });

            if(jQuery("#t_splash_google_suggestions").hasClass("mCustomScrollbar")){
                jQuery("#t_splash_google_suggestions").mCustomScrollbar("update");
            }else{
                jQuery("#t_splash_google_suggestions").mCustomScrollbar({
                    theme:"dark-thick",
                    scrollInertia:10
                });
            }

            jQuery("#buffer #adjust_sched").mCustomScrollbar("update");
            place_page_slider_arrows();
            LP_update_top_scrolls();
        }
    });
}

function LP_reset_draggable_bottom_bar(){
    var bottom_splash_height;
    var t_splash_top;
    var init_height = "";

    jQuery("#bottom_main .tab_bar_drag").draggable({
        axis:'y',
        // handle:'i.drag_topic_tab_bar',
        delay : 0,
        containment : ".bottom_drag_containment",
        scroll: false,
        start : function(){
            var t_offset = jQuery(this).offset();
            t_splash_top = t_offset.top;
            bottom_splash_height = jQuery("#redrip_height").height();
            if(init_height == "") init_height = bottom_splash_height;
        },
        drag : function(){
            var t_offset = jQuery(this).offset();
            var n_top = t_offset.top - t_splash_top;
            var b_height = bottom_splash_height + n_top;
            jQuery("#redrip_height").height(b_height);
        },
        stop : function(){
            var t_offset = jQuery(this).offset();
            var n_top = t_offset.top - t_splash_top;
            var b_height = bottom_splash_height + n_top;
            jQuery("#redrip_height").height(b_height);

            var win_h = jQuery(window).height();
            var offset = jQuery(".results_news_tpl1.b_splash:first").offset();
//			var n_h = win_h - offset.top - 15;
            var n_h = win_h - (t_offset.top + 149) - 15;

            jQuery(".results_news_tpl1.b_splash").height(n_h);
            jQuery(".results_news_tpl1.b_splash").each(function(){
                jQuery(this).mCustomScrollbar("update");
            });
        }
    });
    // jQuery(".header_drag_containment,#bottom_main").css("z-index",1);
    // jQuery("#bottom_main").css("z-index",2);
}

var page_height = 363;
function drip_slideDown_page(){
    // google.load("feeds", "1",{"callback" : feedsLoaded});
    LP_reset_draggable_bar();
    jQuery("#notif_bar").hide().empty();
    jQuery(".topic_functions").show();
    jQuery(".view_types").hide();
    if(jQuery("p.contbtndrip",this).hasClass("active")){
        LP_unlock_splash("top");
        return false;
    }
    jQuery("p.contbtndrip").removeClass("active");
    jQuery("p.contbtndrip",this).addClass("active");
    var topic_id = localStorage.getItem("topics_in_collection_setup");
    if(topic_id != null){
        LP_set_active_tab();
        LP_reset_splash_screens();
        jQuery("#t_splash_topic_tabs").show();

        LP_set_tab_groups();
        LP_set_topic_colors();
        var the_obj 	= jQuery("#t_splash_topic_tabs .topic_tabs.active li.active .tab_info");
        var win_width 	= jQuery(window).width();
        var info_width 	= jQuery(the_obj).width();
        // var parent_pos 	= jQuery(the_obj).parent().parent().position();
        var parent_pos 	= jQuery(the_obj).parent().parent().css("left");
        // var tot_width 	= info_width + parent_pos.left;
        var tot_width 	= info_width + parseFloat(parent_pos);
        if(tot_width 	>= (win_width-80)) jQuery(the_obj).addClass("left");
        else jQuery(the_obj).removeClass("left");

        var obj = jQuery(this);
        var to_page = jQuery(obj).index();
        var xwidth = jQuery(window).width();
        var to_margin = to_page * xwidth;
        var to_margin_top = jQuery(".dripheadbtn .drips_con").eq(to_page).height();
        hide_all_head_forms_but("aaa");
        jQuery(".dripgrpDIV").css("height",page_height+"px").show();

        //hide the pages and set the correct margin for the page to show
        jQuery(".dripheadbtn").animate({"opacity":0},50,function(){
            jQuery(this).css("margin-left","-"+to_margin+"px")
                .css("margin-top","-"+to_margin_top+"px")
                .css("opacity",1)
                .animate({"margin-top":0});
        });
        jQuery("#adjust_sched").mCustomScrollbar("update");

        jQuery("#t_splash_cont").css("padding-bottom","50px");
        place_page_slider_arrows();
        csb();
    }else{
        if(jQuery(this).index()==1){
            jQuery("#t_splash_topic_tabs").show();
            var obj = jQuery(this);
            var to_page = jQuery(obj).index();
            var xwidth = jQuery(window).width();
            var to_margin = to_page * xwidth;
            var to_margin_top = jQuery(".dripheadbtn .drips_con").eq(to_page).height();
            hide_all_head_forms_but("aaa");
            jQuery(".dripgrpDIV").css("height",page_height+"px").show();

            //hide the pages and set the correct margin for the page to show
            jQuery(".dripheadbtn").animate({"opacity":0},50,function(){
                jQuery(this).css("margin-left","-"+to_margin+"px")
                    .css("margin-top","-"+to_margin_top+"px")
                    .css("opacity",1)
                    .animate({"margin-top":0});
            });
            jQuery("#t_splash_cont").css("padding-bottom","50px");
        }
    }


}


function place_page_slider_arrows(){
    jQuery(".arrowprev").css("top",(page_height/2)-32+"px").css("left",0).show();
    jQuery(".arrownext").css("top",(page_height/2)-32+"px").css("right",0).show();
}

function hide_page_slider_arrows(){
    jQuery(".arrowprev").hide();
    jQuery(".arrownext").hide();
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
        jQuery("#bottom_main .topic_tab_group li").removeClass("active");
        jQuery("#bottom_main .topic_tab_group li[param='"+current_tab_group+"']").addClass("active");

        jQuery("#bottom_main #t_splash_topic_tabs ul.topic_tabs").removeClass("active");
        jQuery("#bottom_main #t_splash_topic_tabs ul.topic_tabs").eq(current_tab_group-1).addClass("active");
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

function LP_unlock_splash(the_t){
    var obj = jQuery(this);
    var closed = false;
    if(typeof the_t == "undefined" || typeof the_t == null){
        the_t = "top";
    }

    if(the_t == "bottom"){
        var dragger_h = jQuery("#redrip_height").height();
        var doc_height = jQuery(window).height();
        var to_top = doc_height - dragger_h + 20;
        jQuery("#bottom_main").stop().animate({"margin-top" : to_top},100,function(){
            jQuery("#bottom_main").remove();
        });
    }else{
        jQuery(".dripgrpDIV").stop().animate({"height" : 0},100,function(){
            jQuery("#t_splash_topic_tabs").hide();
            jQuery(".topic_functions").hide();
            jQuery(".view_types").show();
        });

        jQuery("#t_splash_topic_tabs").draggable("destroy").css("top","");
        jQuery("#t_splash_cont").css("padding-bottom","");
        page_height = 363;
        jQuery("p.contbtndrip").removeClass("active");
        hide_page_slider_arrows();
        jQuery("#grpinddivlog > div").hide();
        jQuery(".header_drag_containment").css("height","auto");
    }
}

function drip_sliding_page(reslide){
    var me = jQuery(this);
    var xwidth = jQuery(window).width();

    if((typeof reslide == 'boolean' && reslide == true) || reslide == 'snap'){
        var toSlideTo = jQuery("ul#drip_nav li p.active").parent();
    }else if(jQuery("ul#drip_nav li p.active").length > 0){
        if(jQuery(me).hasClass("arrowprev")){
            if(jQuery("ul#drip_nav li p.active").parent().is(":first-child")){
                var count = jQuery("#drip_nav li").length;
                var to_mar = (count+1)*xwidth;
                jQuery(".dripheadbtn").css("margin-left","-"+to_mar+"px");
                jQuery("ul#drip_nav li p").removeClass("active");
                jQuery("ul#drip_nav li:last p").addClass("active");
                drip_sliding_page(true);
                return false;
            }else{
                var toSlideTo = jQuery("ul#drip_nav li p.active").parent().prev();
            }
        }else if(jQuery(me).hasClass("arrownext")){
            if(jQuery("ul#drip_nav li p.active").parent().is(":last-child")){
                var count = jQuery("#drip_nav li").length;
                var to_mar = (count+1)*xwidth;
                jQuery(".dripheadbtn").css("margin-left",to_mar+"px");
                jQuery("ul#drip_nav li p").removeClass("active");
                jQuery("ul#drip_nav li:first p").addClass("active");
                drip_sliding_page(true);
                return false;
            }else{
                var toSlideTo = jQuery("ul#drip_nav li p.active").parent().next();
            }
        }
    }else{
        var toSlideTo = jQuery("ul#drip_nav li").eq(0);
    }


    var t_drip = jQuery(toSlideTo).index();
    var to_margin = t_drip * xwidth;

    if(reslide == 'snap'){
        jQuery(".dripheadbtn").css("margin-left","-"+to_margin+"px");
        jQuery("ul#drip_nav li p").removeClass("active");
        jQuery("p",toSlideTo).addClass("active");
        LP_set_topic_colors();
    }else{
        jQuery(".dripheadbtn").animate({"margin-left":"-"+to_margin+"px"},200,function(){
            jQuery("ul#drip_nav li p").removeClass("active");
            jQuery("p",toSlideTo).addClass("active");
            LP_set_topic_colors();
        });
    }
}

function LP_is_topic_private(topic_id){
    if(typeof topic_id != "string" && typeof topic_id != "number")
        topic_id = localStorage.getItem("topics_in_collection_setup");
    var topic = LP_get_localStorage_topic(topic_id);
    if(topic.post_fields.hasOwnProperty("_private")){
        if(topic.post_fields["_private"][0] == "true" || topic.post_fields["_private"][0] === true)
            return true;
        else return false;
    }else{
        return false;
    }
}

function LP_set_adjust_page(){
    jQuery(".drips_con #adjust_sched #future_drips, .drips_con #adjust_sched #history_drips").empty();
    jQuery("#adjust_preview").css("visibility","hidden");
    var topic_id = localStorage.getItem("topics_in_collection_setup");
    var the_topic = LP_get_localStorage_topic(topic_id);
    var future_drips = the_topic.future_drips;
    var history_drips = the_topic.history_drips;

    var date_label = "";
    var the_day = "";
    var the_time = "";
    var date_actual = "";

    for (var drip_id in history_drips){
        var history_drip = history_drips[drip_id];

        if(history_drip.post_status == "publish"){
            if(date_label == "" || date_label!= history_drip.date_label){
                date_label = history_drip.date_label;
                jQuery(".drips_con #adjust_sched #history_drips").append("<div class=\"sepa_hori\"></div><h2 class=\"today_title\">"+date_label+"<span style=\"float:right;\">"+history_drip.date_actual_formatted+"</span></h2>");
            }
            var item = LP_adjut_item_template(history_drip);
            jQuery(".drips_con #adjust_sched #history_drips").append(item);
        }
    }

    var date_label = "";
    var the_day = "";
    var the_time = "";
    var date_actual = "";
    if(LP_is_topic_private() == false){
        for (var drip_id in future_drips){
            var future_drip = future_drips[drip_id];
            if(future_drip.post_status == "future"){
                // if(date_label == "" || date_label!= future_drip.date_label){
                if(date_actual == "" || date_actual!= future_drip.date_actual){
                    date_label = future_drip.date_label;
                    jQuery(".drips_con #adjust_sched #future_drips").append("<div class=\"sepa_hori\"></div><h2 class=\"today_title\">"+date_label+"<span style=\"float:right;\">"+future_drip.date_actual_formatted+"</span></h2>");
                }
                // future_drip.post_title = future_drip.date_actual+" : "+future_drip.post_title;
                var item = LP_adjut_item_template(future_drip);
                jQuery(".drips_con #adjust_sched #future_drips").append(item);
                the_day = future_drip.d_day;
                the_time = future_drip.post_time;
                date_actual = future_drip.date_actual;
            }
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

    if(d_days_i >= 6) d_days_i = -1;
    var sstart = false;
    var date_arr = date_actual.split("-");
    date_actual = new Date(date_arr[0],parseInt(date_arr[1])-1,date_arr[2],1,1,1);

    if(LP_is_topic_private() == true){
        jQuery(".drips_con #adjust_sched #future_drips").append("<div class=\"sepa_hori\"></div><h2 class=\"today_title\">Drafts</h2>");
    }

    for (var drip_id in future_drips){
        var future_drip = future_drips[drip_id];

        if(future_drip.post_status == "draft"){
            draft_drips+="";
            var the_next = "x";
            var this_time = "";
            /* LOOP until time is found and use the next time to it... */
            for (var key in meter.drip_time){
                if(the_next === true){
                    this_time = key;
                    the_next = false;
                    break;
                }
                if(the_time == key){
                    the_next = true;
                }
            }
            if(LP_is_topic_private() == false){
                if(the_next == true || the_next == 'x'){
                    for (var key in meter.drip_time){
                        this_time = key;
                        d_days_i++;
                        var tdate = new Date(date_actual.getFullYear(),date_actual.getMonth(),date_actual.getDate(),1,1,1);
                        date_actual = new Date(tdate.getTime()+86400000);
                        var day_i = date_actual.getDay();
                        var day_toggled = meter.drip_day[d_days2[day_i]];
                        var dave=0;
                        while(day_toggled !="true" ){
                            date_actual = new Date(date_actual.getTime()+86400000);
                            day_i = date_actual.getDay();
                            day_toggled = meter.drip_day[d_days2[day_i]];
                            dave++;
                            if(dave >20)day_toggled="true";
                        }
                        date_label = d_days2[date_actual.getDay()];
                        var the_month = get_textualMonth(date_actual.getMonth());
                        jQuery(".drips_con #adjust_sched #future_drips").append("<div class=\"sepa_hori\"></div><h2 class=\"today_title\">"+date_label+"<span style=\"float:right;\">"+getOrdinal(date_actual.getDate())+" "+the_month.month+"</span></h2>");
                        break;
                    }
                }
            }
            the_time = this_time;
            future_drip.post_time = this_time;
            var item = LP_adjut_item_template(future_drip);
            jQuery(".drips_con #adjust_sched #future_drips").append(item);
            if(d_days_i>=6) d_days_i =-1;
        }
    }

    if(jQuery("#adjust_sched").hasClass("mCustomScrollbar")==false){
        jQuery("#adjust_sched").mCustomScrollbar({
            theme:"dark-thick",
            scrollInertia:10
        });
    }else{
        jQuery("#adjust_sched").mCustomScrollbar("update");
    }

    jQuery("#adjust_sched #future_drips").sortable({
        placeholder: "adjust_border",
        forcePlaceholderSize :true,
        // handle: 'i.greycross-d-21',
        cancel: ".today_title",
        start : function(event, ui){
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

    setTimeout(function(){
        jQuery("#adjust_sched").mCustomScrollbar("scrollTo","#future_drips");
    },50);

}

var is_froozen = false;
function update_adjust_preview(){
    if(is_froozen == true) return;
    // LP_update_drip();
    jQuery("#adjust_preview textarea").removeClass("required");
    jQuery("#update_drip").text("Update").css("cursor","pointer");
    var drip_id = jQuery(this).attr("param");
    var is_history = jQuery(this).hasClass("history");
    jQuery("#adjust_preview .preview_btn").hide();
    if(is_history){
        var drip = LP_get_localStorage_history_drip(drip_id);
        jQuery("#adjust_preview textarea#drip_title").attr("readonly",true);
        jQuery("#adjust_preview textarea#the_adjust").attr("readonly",true);
        jQuery("#adjust_preview textarea#the_content").attr("readonly",true);
        jQuery("#adjust_preview .redrip_this").show();
    }else{
        var drip = LP_get_localStorage_future_drip(drip_id);
        jQuery("#adjust_preview textarea#drip_title").attr("readonly",false);
        jQuery("#adjust_preview textarea#the_adjust").attr("readonly",false);
        jQuery("#adjust_preview textarea#the_content").attr("readonly",false);
        jQuery("#adjust_preview #update_drip").show();
    }

    var flip_image = JSON.parse(drip.lp_flip_img);
    if(typeof flip_image == "null" || typeof flip_image == "undefined" || flip_image == null){
        jQuery("#adjust_preview .adjust_topic_feat_img_div").addClass("no-flip");
        jQuery("#adjust_preview .adjust_topic_feat_img_div .backflip img").removeAttr("height").attr("src","");
    }else{
        jQuery("#adjust_preview .adjust_topic_feat_img_div .backflip img").removeAttr("height").attr("src",drip.flip_dir+flip_image.img);
        jQuery("#adjust_preview .adjust_topic_feat_img_div").removeClass("no-flip")
    }

    jQuery("#adjust_preview .adjust_topic_feat_img_div #frontflip img").removeAttr("height").attr("src",drip.lp_drip_img);

    // jQuery("#adjust_preview .topic_pname a").text(user_display_name);
    jQuery("#adjust_preview .channel").text("Channel : "+drip.channel_name);
    jQuery("#adjust_preview .topic").text("Topic : "+drip.topic_name);

    var hdom = jQuery("<div></div>").html(drip.post_title);
    var the_title = jQuery(hdom).text();
    jQuery(hdom).html(drip.post_excerpt);
    var the_excerpt = jQuery(hdom).text();
    jQuery(hdom).html(drip.post_content);
    var the_post_content = jQuery(hdom).text();

    jQuery("#adjust_preview textarea#drip_title").val(the_title.slice(0,200));
    jQuery("#adjust_preview textarea#the_adjust").val(the_excerpt.slice(0,700));
    jQuery("#adjust_preview textarea#the_content").val(the_post_content.slice(0,256));

    jQuery("#adjust_preview input#post_id").val(drip_id);
    jQuery("#adjust_preview input#is_history").val(is_history);

    jQuery("#adjust_preview .redrip_this").attr("id",drip_id);

    jQuery("#adjust_preview ul#drip_tags").empty();
    for (var key in drip.tags){
        var t_tag = drip.tags[key];
        jQuery("#adjust_preview ul#drip_tags").append("<li><input type=\"text\" size=\""+t_tag.name.length+"\" value=\""+t_tag.name+"\" class=\"button2 item autosize\"><span class=\"del_tag\">x</span></li>");
    }

    jQuery("#adjust_preview").css("visibility","visible");
    LP_adjust_form_higlight();
}

function LP_adjut_item_template(drip){
    var param = drip.ID+" ";// don't remove the space or trouble!...
    var title = "";
    if(drip.post_title.length >=70){
        title = drip.post_title.slice(0,67)+"...";
    }else{
        title = drip.post_title;
    }
    var is_history = "";
    var del_class  = "delete_buffer";
    var del_label  = "delete";
    var del_icon   = "trash-d-18";
    if(drip.post_status == "publish"){
        is_history = "history";
        del_class  = "redrip_buffer redrip_this";
        del_label  = "redrip";
        // del_icon   = "blueredrip-d-21";
        del_icon   = "greyredrip-d-18";
    }
    var time_label = drip.post_time.replace(/^0/, '');
    if(LP_is_topic_private() == true){
        time_label = "--";
    }

    var item = "<div class=\"adjust_topics "+is_history+"\" param=\""+param+"\">\
					<div class=\"today_icons\">\
						<span class=\"adjust_time adjust_icon\">"+time_label+"</span>\
						<i class=\"bluewhitedoubledrip-d-30 adjust_icon adjust_move\"></i>\
					</div>\
					<div class=\"title_pointer\"></div>\
					<div class=\"adjust_topic_title\">\
						<span>"+title+"</span>\
						<div id=\"i_container\">\
							<i class=\"ribbon-d-18\"></i>\
							<i class=\""+del_icon+" slide_delete_buffer\"></i>\
							<a href=\""+drip.story_URL+"\" target=\"_blank\"><i class=\"greyanalysis-d-18\"></i></a>\
							<div class=\"delete_buffer_cont\">\
								<div class=\""+del_class+" button2\" id=\""+param+"\">"+del_label+"</div>\
								<div class=\"delete_cancel button2\">cancel</div>\
							</div>\
						</div>\
					</div>\
				</div>";
    return item;
}

function fb_register(){
    FB.login(function(response) {
        LP_console("response");
        LP_console(response);
    }, {scope: 'email'});
}

function tw_register(){
    linkedInWindow = window.open(sub+"twitter/aouth/?CB=twitter/LP_tw_login/","linkedInP",'width=400,height=400,addressbar=no');
    return false;
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

function social_callback_redirect(url){
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
                    acc_type : acc_type
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
            alert("An error occurred during the registraation process. Please contact site administrator...");
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
    jQuery("ul#drip_days > li").removeClass("on");
    jQuery("ul#drip_days > li").removeClass("off");
    jQuery("ul#drip_days > li").each(function(i){
        if(meter.drip_day[d_days[i]] == 1 || meter.drip_day[d_days[i]] == "true"){
            jQuery(this).addClass("on");
        }else{
            jQuery(this).addClass("off");
        }
    });

    // jQuery(".tymdripDIV ul#meter_times").mCustomScrollbar("destroy");
    jQuery(".tymdripDIV ul#meter_times").empty();
    if(typeof meter.drip_time != "object"){
        meter.drip_time = {};
        meter.drip_time["09:00 am"] = "09:00 am";
    }

    jQuery.each(meter.drip_time,function(index,value){
        var the_time = value;

        // <div class=\"clockimgdrip\">\
        // <i class=\"trash-l-18\"></i>\
        // </div>\

        jQuery(".tymdripDIV ul#meter_times").append("<li class=\"litymhddrip\" param=\""+the_time+"\"> \
			<div class=\"tymdrip_hrs\">\
			<select class=\"t_h custom_select1 extra\">"+select_hour+"</select>\
			</div>\
			<div class=\"tymdrip_mins\">\
			<select class=\"t_m custom_select1\">"+select_mins+"</select>\
			</div>\
			<div class=\"tymdrip_ampm\">\
			<select class=\"t_ampm custom_select1\">\
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
        jQuery(".tymdripDIV ul#meter_times > li.litymhddrip:last .t_h").val(t_h);
        jQuery(".tymdripDIV ul#meter_times > li.litymhddrip:last .t_m").val(t_m);
        jQuery(".tymdripDIV ul#meter_times > li.litymhddrip:last .t_ampm").val(t_ampm);
    });
    LP_generate_custom_select1();
    jQuery(".drip_timzone #timezone_string").val(time_zone);
    if(jQuery(".drip_timzone #timezone_string").hasClass("CS_M")==false){
        jQuery(".drip_timzone #timezone_string").addClass("custom_select2");
        LP_generate_custom_select2();
    }
}

function LP_toggle_day(){
    var nth     = jQuery(this).index();
    var toggle  = jQuery(this).hasClass("on");

    var to_c = "off";
    if(!toggle){
        to_c = "on";
    }
    jQuery(this).removeClass("off").removeClass("on");
    jQuery(this).addClass(to_c);
    LP_save_topic_meter();
}

function LP_save_topic_meter(){
    var topic_meter = {
        drip_day	: {},
        drip_time	: {}
    };

    jQuery("ul#drip_days li").each(function(i){
        topic_meter.drip_day[d_days[i]] = jQuery(this).hasClass("on");
    });

    jQuery(".tymdripDIV ul#meter_times li.litymhddrip").each(function(){
        var t_this = jQuery(this);
        var the_time = jQuery("select.t_h",t_this).val()+":"+jQuery("select.t_m",t_this).val()+" "+jQuery("select.t_ampm",t_this).val();
        topic_meter.drip_time[the_time] = the_time;
    });

    var timezonex = jQuery(".drip_timzone select#timezone_string").val();
    time_zone = timezonex;
    var data = {
        action	: "LP_save_topic_meter",
        topic_meter : topic_meter,
        timezone	: timezonex,
        topic_id	: localStorage.getItem("topics_in_collection_setup")
    };
    jQuery.post(ajaxurl,data,function(r){
        var topic_obj = jQuery.parseJSON(r);
        LP_update_localStorage_topic(topic_obj)
        LP_set_adjust_page();
    });
}

function LP_add_drip(){
    // <div class=\"clockimgdrip\">\
    // <i class=\"trash-l-18\"></i>\
    // </div>\

    var item = "<li class=\"litymhddrip\" param=\"00:00 am\">\
					<div class=\"tymdrip_hrs\">\
						<select class=\"t_h custom_select1 extra\">"+select_hour+"</select>\
					</div>\
					<div class=\"tymdrip_mins\">\
						<select class=\"t_m custom_select1\">"+select_mins+"</select>\
					</div>\
					<div class=\"tymdrip_ampm\">\
						<select class=\"t_ampm custom_select1\">\
							<option value=\"am\">am</option>\
							<option value=\"pm\">pm</option>\
						</select>\
					</div>\
				</li>";
    jQuery(".tymdripDIV ul#meter_times").append(item);
    LP_generate_custom_select1();
    LP_save_topic_meter();
    csb();
}

function LP_remove_drip_time(){
    jQuery(this).parent().parent().parent().remove();
    csb();
    LP_save_topic_meter();
}
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
    rsz		    : 8,			// A maximum of 8 is allowed by Google
    page		: 0,				// The start page
    as_rights	: "cc_publicdomain"
}
var results_webs;
var results_videos;
var results_news;
var results_images;

var results_blogs;
var results_tweets;
var results_dripples;
function googleSearch(settings){
    var s = "";
    if(localStorage.getItem("current_user") == 52) s="s";
    if(settings.term != null && settings.term !=""){
        settings = jQuery.extend({},config,settings);
        if(settings.type == "dripple") return false;
        // URL of Google's AJAX search API
        var apiURL = 'http'+s+'://ajax.googleapis.com/ajax/services/search/'+settings.type+'?v=1.0&q='+settings.term;
        var args = {};

        if(settings.type == "images"){
            args = {
                start	: settings.page*settings.rsz,
                rsz		: 8,
                imgsz   : "xga",
                imgar   : "t|xt",
                ift     : "jpg"
            }
            if(settings.hasOwnProperty("imgsz")){
                args.imgsz = settings.imgsz;
            }

            // apiURL+= "&tbs=isz:lt,islt:vga,iar:t";
        }else{
            args = {
                rsz		: 8,
                start	: settings.page*settings.rsz
            }
        }
        if(typeof settings.as_sitesearch !="undefined"){
            args.as_sitesearch = settings.as_sitesearch;
        }

        apiURL+= "&callback=?";

        jQuery.getJSON(apiURL,args,function(r){
            if(r.responseStatus == 200){
                var results = r.responseData.results;
                var to_return;
                if(settings.return == "responseDate"){
                    to_return = {results:r.responseData,type:settings.type,q:settings.term,target:settings.target};
                }else{
                    to_return = {results:results,type:settings.type,q:settings.term,target:settings.target};
                }


                if(settings.LP_call_back!=""){
                    eval(settings.LP_call_back+"(to_return);");
                }
            }
        });
    }
}

function drippleSearch(args){
    jQuery.post(sub+"?s="+args.term,{},function(r){
        var res = JSON.parse(r);
        LP_console(res);
        var to_return = {results:res,type:'dripple',q:args.term,target:args.target};
        eval(args.LP_call_back+"(to_return);");
    });
}

/* TWITTER SEARCH */
function LP_twitter_search(settings){
    if(settings.term != null && settings.term !=""){
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
        var rss_keywords	={};

        jQuery("#topic_collection_setup #search_source_cont .source").each(function(i){
            var who = jQuery(this).attr("param");
            if(jQuery(this).hasClass("active")){
                search_sources[who] = 1;
            }else{
                search_sources[who] = 0;
            }
        });

        var setup           = LP_get_topic_collection_setup(topic);
        var search_keywords = setup.search_keywords;
        var r_keywords 		= setup.rss_keywords;
        if(r_keywords ==  null)r_keywords = 0;

        jQuery("#topic_collection_setup .keywords .topic_keyword").each(function(i){
            var k = jQuery.trim(jQuery(this).val());
            if(k != ""){
                if(jQuery(this).hasClass("filter")){
                    k = "-"+k;
                }else{
                    k = k;
                }

                keywords[i] = k;
                if(jQuery.inArray(k,search_keywords) == -1 && r_keywords.length < 10){
                    var the_key = LP_keyword_input_tpl1(k);
                    jQuery("#topic_collection_rss_setup .keywords").append(the_key);
                }
            }
        });


        jQuery("#topic_collection_rss_setup .keywords .topic_keyword").each(function(i){
            var k = jQuery.trim(jQuery(this).val());
            if(k != ""){
                if(jQuery(this).hasClass("filter")){
                    k = "-"+k;
                }else{
                    k = k;
                }
                rss_keywords[i] = k;
            }
        });

        jQuery("#topic_collection_rss_setup #selected_rss .item").each(function(i){
            var link = jQuery.trim(jQuery(this).attr("param"));
            if(link != ""){
                var the_a = jQuery.parseJSON(jQuery(".args",this).val());
                rss_feeds[i]  = the_a;
            }
        });

        var topic_collection_setup = {
            search_sources	: search_sources,
            keywords		: keywords,
            rss_feeds		: rss_feeds,
            rss_keywords	: rss_keywords
        };

        var data = {
            action 					: "LP_save_topic_collection_setup",
            topic_id				: topic,
            topic_collection_setup	: topic_collection_setup
        }

        jQuery.post(ajaxurl,data,function(r){
            var updated_topic = jQuery.parseJSON(r);
            LP_update_localStorage_topic(updated_topic);
            LP_set_drip_button();
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

function LP_get_localStorage_future_drip(drip_id){
    var topic_id = localStorage.getItem("topics_in_collection_setup");
    /*var u_topics = jQuery.parseJSON(localStorage.getItem("user_topics"));
     var the_topic = u_topics[topic_id];*/
    var the_topic = LP_get_localStorage_topic(topic_id);
    var the_drip = the_topic.future_drips[drip_id];
    return the_drip;
}

function LP_get_localStorage_history_drip(drip_id){
    var topic_id = localStorage.getItem("topics_in_collection_setup");
    /*var u_topics = jQuery.parseJSON(localStorage.getItem("user_topics"));
     var the_topic = u_topics[topic_id];*/
    var the_topic = LP_get_localStorage_topic(topic_id);
    var the_drip = the_topic.history_drips[drip_id];
    return the_drip;
}

function LP_update_localStorage_drip(drip_obj){
    var u_topics = jQuery.parseJSON(localStorage.getItem("user_topics"));
    for (var topic_id in drip_obj){
        var topic = drip_obj[topic_id];
        for (var drip_id in topic){
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
        sub+"wp-content/themes/linkedpost/images/topic_tabra.png",
        sub+"wp-content/themes/linkedpost/images/navicon-dir-med.png",
        sub+"wp-content/themes/linkedpost/images/item-icons-18px.png",
        sub+"wp-content/themes/linkedpost/images/item-icons-21px.png",
        sub+"wp-content/themes/linkedpost/images/head-icons-18px.png",
        sub+"wp-content/themes/linkedpost/images/head-icons-21px.png",
        sub+"wp-content/themes/linkedpost/images/meter_icons.png"
    ];
    var c_images = images.length;
    for (var a=0; a< c_images; a++){
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

function LP_trim(myString)
{
    return myString.replace(/^s+/g,'').replace(/s+$/g,'')
}

function getOrdinal(n) {
    if((parseFloat(n) == parseInt(n)) && !isNaN(n)){
        var s=["th","st","nd","rd"],
            v=n%100;
        return n+(s[(v-20)%10]||s[v]||s[0]);
    }
    return n;
}

function get_textualMonth(mon){
    var months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    var month_names_short = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return {
        month		: months[mon],
        short_name	: month_names_short[mon]
    };
}

function LP_get_age(the_date){
    if(typeof the_date != 'number'){
        var bdate = new Date(the_date);
        bdate.getTime();
    }else{
        var bdate = the_date;
    }
    var today = new Date();
    var age   = today.getTime() - bdate

    return LP_convert_milliseconds_to_age(age)+" ago";
}

function LP_convert_milliseconds_to_age(the_date){

    var mons  = Math.floor(the_date / 2592000000);
    var days  = Math.floor(the_date / 86400000);
    var hrs	  = Math.floor(the_date / 3600000);
    var mins  = Math.floor(the_date / 60000);
    var secs  = Math.floor(the_date / 6000);
    var plural = "";
    if(mons >= 1){
        if(mons > 1) plural="s";
        return mons+" month"+plural;
    }else if(days >= 1){
        if(days > 1) plural="s";
        return days+" day"+plural;
    }else if(hrs >= 1){
        if(hrs > 1) plural="s";
        return hrs+" hour"+plural;
    }else if(mins >= 1){
        if(mins > 1) plural="s";
        return mins+" minute"+plural;
    }else if(secs >= 1){
        if(secs > 1) plural="s";
        return secs+" second"+plural;
    }
}

function LP_is_URL(url){

    var expression = /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi;

    var regex = new RegExp(expression);

    if (url.match(regex))
        return true;
    else return false;
}

function LP_update_top_scrolls(){
    var cont_height = jQuery(".dripgrpDIV").height();
    var cont_top    = jQuery(".dripgrpDIV").offset().top;
    var cont_sctop  = jQuery(".dripgrpDIV").scrollTop();
    var curr_page   = jQuery("li.liDRIPbtn .contbtndrip.active").attr("id");

    if(curr_page == "drip_ripple"){
        jQuery("#ripple.drips_con .mCustomScrollbar").each(function(){
            var this_top  = jQuery(this).offset().top;
            var this_ctop = this_top - cont_top - cont_sctop;
            var the_height =  cont_height - 10 - this_ctop;
            jQuery(this).height(the_height);
            jQuery(this).mCustomScrollbar("update");
        });
    }
}

function GetUrlValue(url ,VarSearch){
//    var SearchString = window.location.search.substring(1);
    var SearchString = url;
    var VariableArray = SearchString.split('&');
    for(var i = 0; i < VariableArray.length; i++){
        var KeyValuePair = VariableArray[i].split('=');
        if(KeyValuePair[0] == VarSearch){
            return KeyValuePair[1];
        }
    }
}

/* RIPPLE */
jQuery(document).ready(function(){
    // jQuery(".ripple_col[id='linkedin'] .scroll_cont .items_cont > .items > .item > img, .ripple_col[id='othersocials'] .scroll_cont .items_cont > .items > .item > img, .ripple_col[id='activated'] .scroll_cont .items_cont > .items > .item > img").live("click",LP_social_activate);
    // jQuery(".ripple_col[id='linkedin'] .scroll_cont .items_cont > .items .container .item .ico, .ripple_col[id='activated'] .scroll_cont .items_cont > .items .container .item .ico").live("click",LP_lg_activate);
    // jQuery(".ripple_col .scroll_cont").mCustomScrollbar({
    // theme:"LP_thick",
    // scrollInertia:10
    // });
    jQuery(".ripple_head #update_linkedin_list").live("click", LP_fetch_user_lin_groups);

    jQuery("#linkedin.ripple_col .scroll_cont .items_cont > .items, #othersocials.ripple_col .scroll_cont .items_cont > .items").sortable({
        connectWith : "#activated.ripple_col .scroll_cont .items_cont > .items",
        placeholder: "dabarkads",
        forcePlaceholderSize :true,
        over:function(event,ui){
            var obj = jQuery(this);

            if((jQuery(obj).hasClass("li") && (jQuery(ui.item).hasClass("li") || jQuery(ui.item).hasClass("child_scroll"))) || (jQuery(obj).hasClass("others") && jQuery(ui.item).hasClass("others"))){
                jQuery(obj).parent().css("border","1px dotted #FFFFFF");
            }else{
                jQuery(obj).parent().find(".dabarkads").css("display","none");
            }
        },
        out:function(){
            jQuery(this).parent().css("border","");
        },
        receive:function(event, ui){
            var obj = jQuery(this);

            if((jQuery(obj).hasClass("li") && (jQuery(ui.item).hasClass("li") || jQuery(ui.item).hasClass("child_scroll"))) || (jQuery(obj).hasClass("others") && jQuery(ui.item).hasClass("others"))){
                if(jQuery(ui.item).hasClass("child_scroll")){
                    jQuery("#linkedin.ripple_col #li_groups.item .container").append(ui.item);
                }
                if(jQuery(ui.item).attr("id") == "li_groups"){
                    var items = jQuery(".container .item",ui.item);
                    jQuery("#linkedin.ripple_col #li_groups.item .container").append(items);
                    if(jQuery("#linkedin.ripple_col #li_groups.item").length > 1){
                        jQuery(ui.item).remove();
                    }
                }
                jQuery(ui.item).removeClass("active");
                LP_delay_update_topic_ripple();
            }else{
                jQuery(ui.sender).append(ui.item);
            }
        }
    });

    jQuery("#activated.ripple_col .scroll_cont .items_cont > .items").sortable({
        connectWith : "#linkedin.ripple_col .scroll_cont .items_cont > .items, #othersocials.ripple_col .scroll_cont .items_cont > .items",
        placeholder: "dabarkads",
        forcePlaceholderSize :true,
        over:function(){
            jQuery(this).parent().css("border","1px dotted #FFFFFF");
        },
        out:function(){
            jQuery(this).parent().css("border","");
        },
        receive:function(event, ui){
            jQuery(ui.item).addClass("active");
            if(jQuery(ui.item).hasClass("child_scroll")){
                if(jQuery("#activated.ripple_col #li_groups.item").length > 0){
                    jQuery("#activated.ripple_col #li_groups.item .container").append(ui.item);
                }else{
                    var tpl = jQuery("#linkedin.ripple_col #li_groups.item").clone();
                    jQuery(".container",tpl).empty().append(ui.item);
                    jQuery("#activated.ripple_col .scroll_cont .items_cont > .items").append(tpl);
                    jQuery("#activated.ripple_col .scroll_cont .items_cont > .items > .item > .container").sortable({
                        connectWith : "#linkedin.ripple_col .scroll_cont .items_cont > .items",
                        placeholder: "dabarkads",
                        forcePlaceholderSize :true
                    });
                }
            }

            if(jQuery(ui.item).attr("id") == "li_groups"){
                var items = jQuery(".container .item",ui.item);
                jQuery("#activated.ripple_col #li_groups.item .container").append(items);
                if(jQuery("#activated.ripple_col #li_groups.item").length > 1){
                    jQuery(".container",ui.item).empty();
                    jQuery(ui.sender).append(ui.item);
                }else{
                    var c = jQuery(ui.item).clone();
                    jQuery(".container",c).empty();
                    jQuery(ui.sender).append(c);
                }
            }
            LP_delay_update_topic_ripple();
        }
    });
    jQuery(".add_soc").live("click",LP_soc_Oauth_iframe);
});

function LP_lg_activate(){
    var obj = jQuery(this).parent();
    if(jQuery(obj).hasClass("active") == false){
        jQuery(obj).addClass("active");
        if(jQuery(".ripple_col[id='activated'] .scroll_cont .items_cont > .items > .item[id='li_groups']").length == 0){
            var lg = jQuery(".ripple_col[id='linkedin'] .scroll_cont .items_cont > .items > .item[id='li_groups']").clone();
            jQuery(lg).addClass("active");
            jQuery(".container",lg).empty().append(obj);
            jQuery(".ripple_col[id='activated'] .scroll_cont .items_cont > .items").append(lg);
        }else{
            jQuery(".ripple_col[id='activated'] .scroll_cont .items_cont > .items > .item[id='li_groups'] .container").append(obj);
        }
    }else{
        jQuery(obj).removeClass("active");
        jQuery(".ripple_col[id='linkedin'] .scroll_cont .items_cont > .items .item[id='li_groups'] .container").append(obj);
    }
    LP_delay_update_topic_ripple();
}

function LP_social_activate(){
    var obj = jQuery(this).parent();
    if(jQuery(obj).hasClass("active") == false){
        if(jQuery(obj).attr("id") == "li_groups"){
            if(jQuery(".ripple_col[id='activated'] .scroll_cont .items_cont > .items > .item[id='li_groups']").length == 0){
                obj = jQuery(obj).clone();
                jQuery(".container",obj).empty();
            }else{
                return false;
            }
        }
        jQuery(obj).addClass("active");
        jQuery(".ripple_col[id='activated'] .scroll_cont .items_cont > .items").append(obj);
    }else{
        jQuery(obj).removeClass("active");
        if(jQuery(obj).attr("id") == "li_activity"){
            jQuery(".ripple_col[id='linkedin'] .scroll_cont .items_cont > .items").append(obj);
        }else if(jQuery(obj).attr("id") == "li_groups"){
            var lig_items = jQuery(".container .item",obj);
            jQuery(".ripple_col[id='linkedin'] .scroll_cont .items_cont > .items .item[id='li_groups'] .container").append(lig_items);
            jQuery(obj).remove();
        }else{
            jQuery(".ripple_col[id='othersocials'] .scroll_cont .items_cont > .items").append(obj);
        }
    }
    LP_delay_update_topic_ripple();
}

var ripple_args;
var LP_delay_update_topic_ripple_timeout;
function LP_delay_update_topic_ripple(){
    ripple_args = {};
    jQuery(".ripple_col[id='activated'] .scroll_cont .items_cont > .items > .item").each(function(){
        var social = jQuery(this).attr("id");
        var id = jQuery(this).attr("param");
        var label =  jQuery(".soc_label",this).text().trim();

        if(jQuery(this).hasClass("others")){
            if(ripple_args.hasOwnProperty(social)){
                ripple_args[social]["account"]["t_"+id] = {id : id, label : label};
            }else{
                ripple_args[social] = {};
                ripple_args[social]["smallLogoUrl"] = jQuery(".smallLogoUrl",this).attr("src");
                ripple_args[social]["name"] = social;
                ripple_args[social]["account"] = {};
                ripple_args[social]["account"]["t_"+id] = {id : id, label : label};
            }
        }else{
            ripple_args[social] = {id : id,name : jQuery(".soc_btn",this).text().trim(), smallLogoUrl : jQuery(".smallLogoUrl",this).attr("src")};
        }
        if(social == "li_groups"){
            ripple_args[social]["groups"] = [];
            jQuery(".container > .item",this).each(function(i,v){
                ripple_args[social]["groups"][i] = {id : jQuery(this).attr("param"), name : jQuery(this).text().trim(), smallLogoUrl : jQuery(".smallLogoUrl",this).attr("src")};
            });
        }
    });
    clearTimeout(LP_delay_update_topic_ripple_timeout);
    LP_delay_update_topic_ripple_timeout = setTimeout("LP_update_topic_ripple(ripple_args)", 2000);
}

function LP_update_topic_ripple(ripple_args){
    clearTimeout(LP_delay_update_topic_ripple_timeout);
    var data = {
        action      : "LP_update_topic_ripple",
        ripple_args : ripple_args,
        topic       : localStorage.getItem("topics_in_collection_setup")
    };
    jQuery.post(ajaxurl,data,function(r){
        if(r!="0"){
            var updated_topic = jQuery.parseJSON(r);
            LP_update_localStorage_topic(updated_topic);
        }
    });
}

function LP_fetch_ripple_socials(){
    var data = {
        action : "LP_fetch_ripple_socials"
    };

    jQuery.post(ajaxurl,data,function(r){
        localStorage.setItem("user_socials",r);
    });
}

function LP_fetch_user_lin_groups(){
    var data = {
        action : "LP_fetch_user_lin_groups"
    };

    jQuery.post(ajaxurl,data,function(r){
        localStorage.setItem("user_socials",r);
        LP_set_ripple_page()
    });
}

function LP_set_ripple_page(){
    var user_socials = jQuery.parseJSON(localStorage.getItem("user_socials"));
    if(user_socials != null){
        jQuery("div#ripple.drips_con div#linkedin.ripple_col .scroll_cont .items_cont > div.items").empty();
        for (var k in user_socials.linkedin){
            var linkedin = user_socials.linkedin[k];
            var tpl = jQuery("<div class=\"item li\" id=\""+k+"\" param=\""+linkedin.id+"\">\
                            <img src=\""+linkedin.smallLogoUrl+"\" class=\"smallLogoUrl\">\
                            <span class=\"soc_btn\">"+linkedin.name+"</span>\
                        </div>");
            if(k == "li_groups"){
                jQuery(tpl).append("<div class=\"container\"></div>");
            }
            jQuery("div#ripple.drips_con div#linkedin.ripple_col .scroll_cont .items_cont > div.items").append(tpl);
        }

        jQuery("div#ripple.drips_con div#othersocials.ripple_col .scroll_cont .items_cont > div.items").empty();
        for (var k in user_socials.others){
            var others = user_socials.others[k];
            if(others.hasOwnProperty("accounts")){
                for (var a in others.accounts){
                    var account = others.accounts[a];
                    var tpl = "<div class=\"item others\" id=\""+k+"\" param=\""+account.id+"\">\
								<img src=\""+others.smallLogoUrl+"\" class=\"smallLogoUrl\">\
								<span class=\"soc_btn\">"+others.soc_name+"</span>\
								<span class=\"soc_label\">"+account.label+"</span>\
								<span class=\"add_soc\">+</span>\
							</div>";
                    jQuery("div#ripple.drips_con div#othersocials.ripple_col .scroll_cont .items_cont > div.items").append(tpl);
                }
            }else{
                var tpl = "<div class=\"item others\" id=\""+k+"\" param=\""+others.id+"\">\
								<img src=\""+others.smallLogoUrl+"\" class=\"smallLogoUrl\">\
								<span class=\"soc_btn inactive\">"+others.name+"</span>\
								<span class=\"soc_label\"></span>\
								<span class=\"add_soc\">+</span>\
							</div>";
                jQuery("div#ripple.drips_con div#othersocials.ripple_col .scroll_cont .items_cont > div.items").append(tpl);
            }
        }

        for (var key in user_socials.linkedin.li_groups.groups){
            var group = user_socials.linkedin.li_groups.groups[key];
            var thumb = "";

            if(group.group.hasOwnProperty("smallLogoUrl") ==  true){
                thumb = "<img src=\""+group.group.smallLogoUrl+"\" class=\"smallLogoUrl\">";
            }
            var tpl = "<div class=\"item child_scroll\" param=\""+group.group.id+"\">\
                            <div class=\"ico\">\
                               "+thumb+"\
                            </div>\
                            <span class=\"sub_btn\" title=\""+group.group.name+"\">"+group.group.name+"</span>\
                        </div>";
            jQuery("div#ripple.drips_con div#linkedin.ripple_col .scroll_cont .items_cont > div.items > div#li_groups.item > div.container").append(tpl);
        }

        jQuery("div#ripple.drips_con div#activated.ripple_col .scroll_cont .items_cont > div.items").empty();
        var the_topic = LP_get_localStorage_topic();
        if(the_topic.post_fields.hasOwnProperty("_LP_ripple")){
            var LP_ripple = JSON.parse(the_topic.post_fields._LP_ripple);

            for (var k in LP_ripple){
                var ripple = LP_ripple[k];

                var add_soc = "";
                var soc_label = "";
                if(k == "twitter" || k == "facebook"){
                    var accounts = ripple.account;
                    var tpl = "";
                    for (var b in accounts){
                        var account = accounts[b];
                        add_soc = "<span class=\"add_soc\">+</span>";
                        soc_label = "<span class=\"soc_label\">"+account.label+"</span>";
                        tpl+= "<div class=\"item others active\" id=\""+k+"\" param=\""+account.id+"\">\
											<img src=\""+ripple.smallLogoUrl+"\" class=\"smallLogoUrl\">\
											<span class=\"soc_btn\">"+ripple.name+"</span>\
											"+soc_label+"\
											"+add_soc+"\
										</div>";
                        jQuery("div#othersocials.ripple_col .scroll_cont .items_cont > div.items > div#"+k+".item[param='"+account.id+"']").remove();
                    }
                }else{
                    if(k != "li_groups" && k != "li_activity"){
                        add_soc = "<span class=\"add_soc\">+</span>";
                        soc_label = "<span class=\"soc_label\">label</span>";
                    }
                    var tpl = jQuery("<div class=\"item active\" id=\""+k+"\" param=\""+ripple.id+"\">\
										<img src=\""+ripple.smallLogoUrl+"\" class=\"smallLogoUrl\">\
										<span class=\"soc_btn\">"+ripple.name+"</span>\
										"+soc_label+"\
										"+add_soc+"\
									</div>");
                    if(k == "li_groups" || k == "li_activity"){
                        jQuery(tpl).addClass("li").append("<div class=\"container\"></div>");
                    }else{
                        jQuery(tpl).addClass("others");
                        jQuery("div#linkedin.ripple_col .scroll_cont .items_cont > div.items > div#"+k+".item[param='"+ripple.id+"']").remove();
                        jQuery("div#othersocials.ripple_col .scroll_cont .items_cont > div.items > div#"+k+".item[param='"+ripple.id+"']").remove();
                    }
                }
                jQuery("div#activated.ripple_col .scroll_cont .items_cont > div.items").append(tpl);
            }
            if(LP_ripple){
                if(LP_ripple.hasOwnProperty("li_groups")){
                    for (var key in LP_ripple.li_groups.groups){
                        var group = LP_ripple.li_groups.groups[key];
                        var thumb = "";

                        if(group.hasOwnProperty("smallLogoUrl") ==  true){
                            thumb = "<img src=\""+group.smallLogoUrl+"\" class=\"smallLogoUrl\">";
                        }
                        var tpl = "<div class=\"item child_scroll active\" param=\""+group.id+"\">\
										<div class=\"ico\">\
										   "+thumb+"\
										</div>\
										<span class=\"sub_btn\" title=\""+group.name+"\">"+group.name+"</span>\
									</div>";
                        jQuery("div#ripple.drips_con div#activated.ripple_col .scroll_cont .items_cont > div.items > div#li_groups.item > div.container").append(tpl);
                        jQuery("div#ripple.drips_con div#linkedin.ripple_col .scroll_cont .items_cont > div.items > div#li_groups.item > div.container > .item[param='"+group.id+"']").remove();
                    }
                }
            }
        }

        // jQuery(".ripple_col[id='othersocials'] .scroll_cont .items_cont > .items > .item").draggable({
        // connectToSortable: ".ripple_col[id='activated'] .scroll_cont .items_cont > .items",
        // revert : true
        // });
        jQuery("#activated.ripple_col .scroll_cont .items_cont > .items > .item > .container").sortable({
            connectWith : "#linkedin.ripple_col .scroll_cont .items_cont > .items",
            placeholder: "dabarkads",
            forcePlaceholderSize :true
        });
        jQuery("#linkedin.ripple_col .scroll_cont .items_cont > .items > .item > .container").sortable({
            connectWith : "#activated.ripple_col .scroll_cont .items_cont > .items",
            placeholder: "dabarkads",
            forcePlaceholderSize :true
        });
    }
}

var socWindow;
function LP_soc_Oauth_iframe(){
    var obj 		= jQuery(this);
    var topic_id 	= localStorage.getItem("topics_in_collection_setup");
    if(jQuery(obj).parent().attr("id") == "twitter"){
        socWindow = window.open(sub+"twitter/ripple_oauth/"+topic_id,"twitter",'width=400,height=400,addressbar=no');
    }else if(jQuery(obj).parent().attr("id") == "facebook"){
        socWindow = window.open(sub+"facebook/ripple_oauth/"+topic_id,"facebook",'width=400,height=400,addressbar=no');
    }
}

function LP_ripple_insert_social(soc_id, label, soc_name){
    var smallLogoUrl = "";
    if(soc_name == "twitter") smallLogoUrl = "http://www.drippost.com/wp-content/themes/linkedpost/images/twitter-icon.png";
    else if(soc_name == "facebook") smallLogoUrl = "https://www.google.com/s2/favicons?domain=http://newsroom.fb.com/";
    var tpl = "<div class=\"item others\" id=\""+soc_name+"\" param=\""+soc_id+"\">\
                            <img src=\""+smallLogoUrl+"\" class=\"smallLogoUrl\">\
                            <span class=\"soc_btn\">"+soc_name+"</span>\
                            <span class=\"soc_label\">"+label+"</span>\
							<span class=\"add_soc\">+</span>\
                        </div>";
    jQuery("#othersocials.ripple_col .scroll_cont .items_cont > div.items").append(tpl);
    socWindow.close();
}
/* END OF RIPPLE */

/* THEME Scripts*/
var current_shown = '';
var astart = 0;
var sub = linkedIn_AJAX.site_url;

jQuery(document).ready(function(){

    jQuery(".creatacctheadlog, .signinhed, .meter_drips, .adjust_drips").live("click",function(){
        show_dropdown_loginuser(this);
        return false;
    });

    jQuery(".arrowup").live("click",close_dropdown_loginuser);
    jQuery("#comnt_show").live("click",show_cmnt);
    jQuery("#closespansynin").live("click",close_popup_signin);

    //start floating
    jQuery("#f_cont").ready(function(){
        if(jQuery("#ajoinfreeimg_frame").length>=1){
            jQuery("#f_cont").height(307);
        }else{
            jQuery("#f_cont").height(209);
        }
        // var ini_w = jQuery("#f_cont").width();

        jQuery("#floating_menu").width(306);
        jQuery("#f_cont").width(326);

        // astart = jQuery("#f_cont").position().top;
        astart = 100;
        jQuery(document).scroll(function(){
            // if(jQuery("#grpinddivlog").css("display")=="none"){
            jQuery("#f_cont").css("position","").css("left","18px");
            var doc_s = jQuery(document).scrollTop();
            if(doc_s > 25){
                jQuery("#floating_menu").css("top","75px").css("position","fixed");
            }else{
                jQuery("#floating_menu").css("position","absolute").css("top",(astart+10)+"px");
            }
            // }
        });
    });
    //end floating

    jQuery(".see_more").live("click",summary_show);

    /* toggle on/off tips */
    jQuery("#qmark").live("click", toggle_onoff);
    /* show type a city */
    jQuery("#chckdwn").live("click", type_city);

    jQuery(".add_topic_btn").live("click",function(){
        slide_down_header_form(".managetopicsdiv");
    });
    jQuery(".manage_linkedin_message").live("click",LP_fetch_linkedin_message);
    jQuery("#grab_linkedin_connections").live("click",LP_grab_linkedin_connections);
    jQuery("#dripview, #topicview").live("click",toggle_member_view);
    jQuery("#sel_all").live("click",LP_select_all_connections);
    jQuery("#sel_none").live("click",LP_select_no_connections);
    jQuery("input.connection").live("click",LP_select_custom_connections);
    jQuery("#accept_linkedin_connections").live("click",LP_accept_linkedin_connections);
    jQuery("#manage_message_template #pagination > span").live("click",LP_goto_linkedin_connections_page);
});

function LP_grab_linkedin_connections(){
    jQuery.post(ajaxurl,{action:'LP_grab_all_linkedin_connections'},function(r){
        var res = jQuery.parseJSON(r);
        if(res.hasOwnProperty("total")){
            LP_blog_has_messaging = true;
            LP_fetch_linkedin_message("skip");
        }
    });
}

function LP_goto_prev_conn(){
    var curr = jQuery("#manage_message_template #pagination > span.active").text();
    var prev = parseInt(curr)-1;
    if(prev < 1 ) prev = 1;
    jQuery("#manage_message_template #pagination > span:eq("+prev+")").click();
}

function LP_goto_next_conn(){
    var curr = jQuery("#manage_message_template #pagination > span.active").text();
    var next = parseInt(curr)+1;
    var tot = parseInt(jQuery("#manage_message_template #pagination > span").length);
    if(next > (tot-2) ) next = tot-2;
    jQuery("#manage_message_template #pagination > span:eq("+next+")").click();
}

function LP_goto_linkedin_connections_page(){
    if(jQuery(this).hasClass("left")){
        LP_goto_prev_conn();
        return;
    }else if(jQuery(this).hasClass("right")){
        LP_goto_next_conn();
        return;
    }
    var to_page = parseInt(jQuery(this).text())-1;
    var cstart = to_page * 100;
    var cend = cstart+100;
    jQuery("#manage_message_template #pagination > span.active").removeClass("active");
    jQuery(this).addClass("active");
    jQuery("#manage_message_template table.messages_table tbody tr.showing").removeClass("showing");
    jQuery("#manage_message_template table.messages_table tbody tr").slice(cstart, cend).addClass("showing");
    jQuery("#manage_message_template #pagination > span").addClass("disabled");
    jQuery("#manage_message_template #pagination > span.left").removeClass("disabled");
    jQuery("#manage_message_template #pagination > span.right").removeClass("disabled");
    var s_paging = to_page - 1;
    if(s_paging < 1) s_paging = 1;
    for(var a = s_paging; a < (s_paging+5); a++){
        jQuery("#manage_message_template #pagination > span:eq("+a+")").removeClass("disabled");
    }
    if(parseInt(jQuery(this).text()) > 1){
        jQuery("#manage_message_template #pagination span.left").removeClass("disabled");
    }else{
        jQuery("#manage_message_template #pagination span.left").addClass("disabled");
    }
    jQuery("#manage_message_template #pagination span.right").removeClass("disabled");
}

function LP_accept_linkedin_connections(){
    var tpl = "Saving Message settings...";
    jQuery("#notif_bar").empty().append(tpl).fadeIn(100);
    var connections;
    if(selected_connections == "all"){
        connections = "all";
    }else if(selected_connections == "none"){
        connections = "none";
    }else if(selected_connections == "custom"){
        connections = [];
        jQuery("#manage_message_template table.messages_table input.connection:checked").each(function(i,v){
            connections[i]= (jQuery(this).attr("id")).substring(2);
        });
    }

    if(selected_connections == ""){
        connections = false;
    }
    var data = {
        action : "LP_update_toggled_connections",
        connections :connections,
        subject: jQuery("#linkedin_message_forms #message_subject").val(),
        body   : jQuery("#linkedin_message_forms #message_body").val()
    };
    jQuery.post(ajaxurl,data,function(r){
        var tpl = "Message settings saved...";
        jQuery("#notif_bar").empty().append(tpl).delay(8000).fadeOut(1000);
    });

}

//global
var selected_connections = "";
function LP_select_all_connections(){
    selected_connections = "all";
    jQuery("#manage_message_template table.messages_table input.connection").attr('checked','checked');
}

function LP_select_no_connections(){
    selected_connections = "none";
    jQuery("#manage_message_template table.messages_table input.connection").removeAttr("checked");
}

function LP_select_custom_connections(){
    selected_connections = "custom";
}

function LP_fetch_linkedin_message(arg){
    linkedin_conn_count = 0;
    if(arg != "skip"){
        if(jQuery("#manage_message_template").is(":visible")){
            jQuery("#manage_message_template").animate({height:0},100,function(){jQuery(this).hide();});
            jQuery("#t_splash_cont").css("padding-bottom",0);
            return;
        }

        jQuery("#grpinddivlog > div").hide();
        jQuery("#t_splash_topic_tabs").hide();
        var wh= jQuery(window).height();
        jQuery("#manage_message_template").css({height:0,display:"block"});
        jQuery("#manage_message_template").animate({height:wh},100,function(){
            if(jQuery("#manage_message_template #messages_table_cont").hasClass("mCustomScrollbar") == false){
                jQuery("#manage_message_template #messages_table_cont").css("height",(wh-195)).mCustomScrollbar({
                    theme:"dark-thick",
                    mouseWheelPixels: 200,
                    scrollInertia:10,
                    advanced:{updateOnContentResize : true}
                });
            }else{
                jQuery("#manage_message_template #messages_table_cont").css("height",(wh-195)).mCustomScrollbar("update");
            }
        });
    }else{
        jQuery("#grab_linkedin_connections").remove();
        jQuery("#manage_message_template .no_messaging").removeClass("no_messaging");
    }
    if(!LP_blog_has_messaging || LP_blog_has_messaging == ""){return;}
    var data = {
        action : "LP_fetch_linkedin_message"
    };

    jQuery(".manage_linkedin_message,body").css("cursor","progress");
    jQuery.post(ajaxurl,data,function(r){
        var res = jQuery.parseJSON(r);
        var message = jQuery.parseJSON(res.message_tpl);
        jQuery("#linkedin_message_forms #message_subject").val(message["subject"]);
        jQuery("#linkedin_message_forms #message_body").val(message["body"]);
        jQuery("#manage_message_template table.messages_table tbody").empty();
        LP_append_connections_tr(res, false);
        jQuery("#manage_message_template #pagination").empty();
        jQuery("#manage_message_template #pagination").prepend("<span class=\"left disabled\">&lt;</span>");
        jQuery("#manage_message_template #pagination").append("<span class=\"active\">1</span>");
        total_lin_connections = res.total;
        setTimeout('LP_refetch_all_linkedin_connections(2)', 100);
    });
}

//global
var total_lin_connections = 0;
function LP_refetch_all_linkedin_connections(page){
    jQuery.post(ajaxurl,{action:"LP_fetch_linkedin_message",get_all:"all",page:page},function(x){
        var res_all = jQuery.parseJSON(x);
        var total = total_lin_connections;
        var proccessed = parseInt(res_all.page) * 100;
        var next_page = parseInt(res_all.page) + 1;
        if(parseInt(res_all.linkedin_connections.contacts.length) > 0){
            LP_append_connections_tr(res_all, false);
            if(jQuery("#manage_message_template #pagination span.right").length == 0){
                jQuery("#manage_message_template #pagination").append("<span class=\"right\">&gt;</span>");
            }
            var sh="";
            if(parseInt(page)>5) sh = "disabled";
            jQuery("<span class=\""+sh+"\">"+page+"</span>").insertBefore("#manage_message_template #pagination span.right");
            if(parseInt(total) > proccessed){
                LP_refetch_all_linkedin_connections(next_page);
            }
        }
    });
}
//global
var linkedin_conn_count = 0;
function LP_append_connections_tr(conns, is_all){
    var res = conns;
    var counter = linkedin_conn_count;
    for(var ind in res.linkedin_connections.contacts){
        var contact = res.linkedin_connections.contacts[ind];
        if(contact.id == "private") continue;
        counter++;

        var shown = "";
        if(counter <= 100)shown = "showing";

        var checked = "";
        if(contact.messaging == "1"){
            checked = "checked=\"checked\"";
        }

        var Lname = LP_clean_name(contact.lastName);
        jQuery("#manage_message_template table.messages_table tbody").append("<tr class=\""+shown+"\">\
                                                                                    <td style=\"width:55px;\"><input type=\"checkbox\" "+checked+" id=\"c_"+ contact.id+"\" class=\"connection\"></td>\
                                                                                    <td style=\"width:100px;\">"+ contact.firstName +"</td>\
                                                                                    <td style=\"width:125px;\">"+ Lname +"</td>\
                                                                                    <td>"+ contact.industry+"</td>\
                                                                                </tr>");
    }
    linkedin_conn_count = counter;
//    if(is_all == true){
//        var con_pages = Math.ceil(counter/100);
//        var active = "active";
//        jQuery("#manage_message_template #pagination").empty();
//        for(var p=1; p<=con_pages;p++){
//            jQuery("#manage_message_template #pagination").append("<span class=\""+active+"\">"+p+"</span>");
//            active = "";
//        }
//    }
    jQuery(".manage_linkedin_message,body").css("cursor","");
}

function LP_clean_name(name){
    var cname = name.replace(/[| \(\)\.,\[\]]/ig,"");
    var n = cname.slice(0,1);
    if((!isNaN(parseFloat(n)) && isFinite(n)) || cname.length <= 1 || (cname.indexOf('@') >= 0) || (cname.indexOf('*') >= 0)){
        return "";
    }else{
        return name;
    }
}

function toggle_member_view(){
    var obj = jQuery(this);
    if(!jQuery(obj).hasClass("active")){
        var to_view = jQuery(obj).attr("id");
        if(to_view == "dripview"){
            jQuery("#topics_mode").fadeOut(10);
            jQuery("#drip_mode").fadeIn(100);
            jQuery("#topicview").removeClass("active");
        }else{
            jQuery("#drip_mode").fadeOut(10);
            jQuery("#topics_mode").fadeIn(100);
            jQuery("#dripview").removeClass("active");
        }
        jQuery(obj).addClass("active");
    }
}

function slide_down_header_form(cont){
    jQuery("#grpinddivlog > div").hide();
    jQuery(cont).animate({height:"toggle"},200);
}

var obj_top;
LP_make_sticky("mash_trend_drips_div");
LP_make_sticky("mash_hot_drips_div");
LP_make_sticky("mash_new_drips_div");

function LP_make_sticky(className){
    if(jQuery("."+className).length > 0){
        jQuery("."+className).ready(function(){
            var xtop = jQuery("."+className).offset();
            obj_top = xtop.top;
            jQuery(document).live("scroll",function(){
                LP_mash_sticky_head(className);
            });
        });
    }
}

function LP_mash_sticky_head(className){
    var scrollt = jQuery(document).scrollTop();
    if(obj_top <= scrollt){
        jQuery("."+className).css("position","fixed");
    }else{
        jQuery("."+className).css("position","absolute");
    }
}

function show_dropdown_loginuser(obj){
    var t_this = jQuery(obj).attr("class");
    var speed = 200;
    if(jQuery("#grpinddivlog").css("display")=="none"){
        if(t_this == "signinhed"){
            hide_all_head_forms_but("signdiv");
            speed = 250;
            jQuery(document).scrollTop(0);
            jQuery("#floating_menu").css("position","absolute").css("top","0px");
            jQuery("#f_cont").css("position","relative").css("left","0px");
        }else if(t_this == "creatacctheadlog"){
            hide_all_head_forms_but("signlogindivhead");
            jQuery(document).scrollTop(0);
            jQuery("#floating_menu").css("position","absolute").css("top","0px");
            jQuery("#f_cont").css("position","relative").css("left","0px");
        }else if(t_this == "meter_drips"){
            t_this = "meter_drips";
            jQuery("#grpinddivlog > .dripgrpDIV .DRIPdivcrtacc_inf").show();
            jQuery("#grpinddivlog > .dripgrpDIV .latest_post").hide();
            hide_all_head_forms_but("dripgrpDIV");
            jQuery(document).scrollTop(0);
            jQuery("#floating_menu").css("position","absolute").css("top","0px");
            jQuery("#f_cont").css("position","relative").css("left","0px");
        }else if(t_this == "adjust_drips"){
            t_this = "meter_drips";
            jQuery("#grpinddivlog > .dripgrpDIV .DRIPdivcrtacc_inf").hide();
            jQuery("#grpinddivlog > .dripgrpDIV .latest_post").show();
            hide_all_head_forms_but("dripgrpDIV");
            jQuery(document).scrollTop(0);
            jQuery("#floating_menu").css("position","absolute").css("top","0px");
            jQuery("#f_cont").css("position","relative").css("left","0px");
        }
    }

    jQuery("#grpinddivlog").animate({height:'toggle'},speed,function(){
        if(t_this != current_shown && current_shown!=""){
            show_dropdown_loginuser(obj);
        }
        if(jQuery("#grpinddivlog").css("display")=="block"){
            current_shown = t_this;
            jQuery("#underheadDIV").hide();
        }else{
            current_shown = "";
            jQuery("#underheadDIV").show();
            jQuery(document).scrollTop(0);
        }
    });
    return false;
}

function hide_all_head_forms_but(forms){
    jQuery("#grpinddivlog > div").hide();
    jQuery("#grpinddivlog > div.DRIPbtn_head").show();
    jQuery("#grpinddivlog > ."+forms).show();
    jQuery("#grpinddivlog > #whiteclosediv").show();
}
function show_popup_signin(){
    jQuery("#signindiv").show();
}

function close_dropdown_loginuser(){
    jQuery("#grpinddivlog").slideUp();
    jQuery("#underheadDIV").show();
}
function close_popup_signin(){
    jQuery("#signindiv").hide();
}
function show_cmnt(){
    jQuery("#cmnt_divcont").show();
}

function summary_show(){
    jQuery(".prof_summary").animate({height:'toggle'},300,function(){
        if(jQuery(this).css("display")=="none"){
            jQuery(".see_more #c_more").text("See More");
            jQuery(".see_more #left_arr_c_more").html("&or;");
            jQuery(".see_more #right_arr_c_more").html("&or;");
        }else{
            jQuery(".see_more #c_more").text("Show Less");
            jQuery(".see_more #left_arr_c_more").hide();
            jQuery(".see_more #right_arr_c_more").hide();
            jQuery(".see_more #left_arr_c_more2").show();
            jQuery(".see_more #right_arr_c_more2").show();
        }
    });
}

function toggle_onoff(){
    var isHidden = jQuery("#toggle_default").css("display");

    if (isHidden == "none"){
        jQuery("#toggle_default").addClass("toggle_show");
    }
    else jQuery("#toggle_default").removeClass("toggle_show");
}

function type_city(){
    var isHidden = jQuery(".type_city").css("display");

    if (isHidden == "none"){
        jQuery(".type_city").show();
    }
    else jQuery(".type_city").hide();
}
/* END THEME Scripts*/

/* Custom Select */
var cs2_active = false;
jQuery(document).ready(function(){
    LP_generate_custom_select1();
    jQuery(".custom_select1 .handle, .custom_select1 .mid_drop").live("click",LP_show_custom_select1_options);
    jQuery("body").live("click",LP_hide_custom_select1_options);
    jQuery(".custom_select1.options li.item").live("click",LP_setval_custom_select1_options);

    // LP_generate_custom_select2();
    jQuery(".custom_select2 .handle, .custom_select2 .mid_drop").live("click",LP_show_custom_select2_options);
    jQuery("body").live("click",LP_hide_custom_select2_options);
    jQuery(".custom_select2 .options li.item").live("click",LP_setval_custom_select2_options);
    jQuery(".custom_select2 .mCSB_dragger_bar,.custom_select1 .mCSB_dragger_bar").live("click",function(){
        cs_active = true;
        setTimeout(function(){
            cs_active = false;
        },50);
    });
    LP_generate_custom_select2();
    LP_custom_checkbox();
    jQuery(".CS_M").live("change",LP_trigger_changed);
});

var CS1_shown = false;
function LP_show_custom_select1_options(){
    var obj = jQuery(this);
    var the_pid = jQuery(obj).parent().attr("id");
    var the_offset =  jQuery(obj).parent().offset();
    var the_height  =jQuery(obj).parent().height();
    var the_width  =jQuery(obj).parent().width();
    var the_options = jQuery(obj).siblings(".options");
    setTimeout(function(){
        jQuery(the_options).attr("param",the_pid)
            .width(the_width)
            .css("margin-top",(the_height+3)+"px")
            .show();
        CS1_shown = true;
        if(jQuery(the_options).hasClass("mCustomScrollbar") == false){
            jQuery(the_options).mCustomScrollbar({
                theme:"dark-thick",
                mouseWheelPixels: 50,
                scrollInertia:10,
                autoHideScrollbar:true
            });
        }
    },50);
    cs_active = false;
}

function LP_hide_custom_select1_options(){
    if(CS1_shown == true && cs_active==false){
        jQuery(".custom_select1.options").hide();
        CS1_shown = false;
    }
}

function LP_setval_custom_select1_options(){
    var obj = jQuery(this);
    var the_val = jQuery(obj).attr("param");
    var the_text = jQuery(obj).text();
    var the_pid = jQuery(obj).parent().parent().parent().parent().attr("param");
    jQuery("#"+the_pid+" .mid_drop .value").text(the_text).attr("param",the_val);
    var select_class = jQuery("#"+the_pid).attr("param");
    jQuery("."+select_class).val(the_val).change();
}

var custom_select1_i = 0;
function LP_generate_custom_select1(){
    jQuery("select.custom_select1").each(function(i){
        var obj = jQuery(this);
        jQuery(obj).hide();
        var new_class = "custom_select1_"+custom_select1_i;
        jQuery(obj).removeClass("custom_select1").addClass(new_class).addClass("CS_M");
        var extra = "";
        if(jQuery(obj).hasClass("extra")){
            extra = '<div class="handle del_drip_time">\
				<i class="clock_trash-l-18">\
					<i class="clock-l-18"></i>\
					<i class="trash-d-18"></i>\
				</i>\
			</div>';
        }
        var options = "";
        var children = jQuery(obj).children();
        var the_default_val = jQuery(obj).val();
        var the_default_text = "";
        jQuery(children).each(function(){
            if(jQuery(this).is("optgroup")){
                options+= "<li class=\"label\">"+jQuery(this).attr("label")+"</li>";

                jQuery(jQuery(this).children()).each(function(){
                    options+= "<li class=\"item dblIndent\" param=\""+jQuery(this).attr("value")+"\">"+jQuery(this).text()+"</li>";
                    if(jQuery(this).attr("value") == the_default_val){
                        the_default_text = jQuery(this).text();
                    }
                });
            }else if(jQuery(this).is("option")){
                options+= "<li class=\"item\" param=\""+jQuery(this).attr("value")+"\">"+jQuery(this).text()+"</li>";
                if(jQuery(this).attr("value") == the_default_val){
                    the_default_text = jQuery(this).text();
                }
            }
        });

        var tpl = '<div class="custom_select1" id="CS1_'+custom_select1_i+'" param="'+new_class+'">\
			'+extra+'\
			<div class="mid_drop">\
				<span class="value" param="'+the_default_val+'">'+the_default_text+'</span>\
			</div>\
			<div class="handle">\
				<i class="drop_arr"></i>\
			</div>\
			<div class="custom_select1 options">\
			<ul>\
				'+options+'\
			</ul>\
			</div>\
		</div>';
        jQuery(tpl).insertAfter(obj);
        custom_select1_i++;
    });

}

////////////Custom 2//////////////

var CS2_shown = false;
function LP_show_custom_select2_options(){
    var obj = jQuery(this);
    var the_cs = jQuery(obj).parent();
    var the_pid = jQuery(the_cs).attr("id");
    var the_offset =  jQuery(the_cs).offset();
    var the_height  =jQuery(the_cs).height();
    var the_width  =jQuery(the_cs).width();
    var the_options = jQuery(obj).siblings(".options");
    setTimeout(function(){
        jQuery(".items",the_options).attr("param",the_pid)
            .width(the_width)
            .show();
        jQuery(".items_pointer",the_cs)
            .css("margin-left",(the_width-25)+"px")
            .show();
        CS2_shown = true;
        if(jQuery(".items",the_options).hasClass("mCustomScrollbar") == false){
            jQuery(".items",the_options).mCustomScrollbar({
                theme:"dark-thick",
                mouseWheelPixels: 50,
                scrollInertia:10,
                autoHideScrollbar:true
            });
        }
    },50);
    cs_active = false;
}

function LP_hide_custom_select2_options(){
    if(CS2_shown == true && cs_active==false){
        jQuery(".custom_select2 .options .items").hide();
        jQuery(".custom_select2 .items_pointer").hide();
        CS2_shown = false;
    }
}

function LP_setval_custom_select2_options(){
    var obj = jQuery(this);
    var the_val = jQuery(obj).attr("param");
    var the_text = jQuery(obj).text();
    var the_pid = jQuery(obj).parent().parent().parent().parent().attr("param");
    jQuery("#"+the_pid+" .mid_drop .value").text(the_text).attr("param",the_val);
    var select_class = jQuery("#"+the_pid).attr("param");
    jQuery("."+select_class).val(the_val).change();
}

var custom_select2_i = 0;
function LP_generate_custom_select2(){
    jQuery("select.custom_select2").each(function(i){
        var obj = jQuery(this);
        var id  = jQuery(obj).attr("id")+"_cs2";
        var the_label = jQuery("#"+id).val();

        jQuery(obj).hide();
        var new_class = "custom_select2_"+custom_select2_i;
        jQuery(obj).removeClass("custom_select2").addClass(new_class).addClass("CS_M").attr("title","CS2_"+custom_select2_i);

        var options = "";
        var children = jQuery(obj).children();
        var the_default_val = jQuery(obj).val();
        var the_default_text = "";
        var input_label = "";

        if(typeof the_label !="undefined") input_label = "<label>"+the_label+"</label>";
        jQuery(children).each(function(){
            if(jQuery(this).is("optgroup")){
                options+= "<li class=\"label\">"+jQuery(this).attr("label")+"</li>";
                jQuery(jQuery(this).children()).each(function(){
                    options+= "<li class=\"item dblIndent\" param=\""+jQuery(this).attr("value")+"\">"+jQuery(this).text()+"</li>";
                    if(jQuery(this).attr("value") == the_default_val){
                        the_default_text = jQuery(this).text();
                    }
                });
            }else if(jQuery(this).is("option")){
                options+= "<li class=\"item dblIndent\" param=\""+jQuery(this).attr("value")+"\">"+jQuery(this).text()+"</li>";
                if(jQuery(this).attr("value") == the_default_val){
                    the_default_text = jQuery(this).text();
                }
            }
        });

        var tpl = '<div class="custom_select2"  id="CS2_'+custom_select2_i+'" param="'+new_class+'">\
			<div class="mid_drop">\
				<label>'+input_label+'</label>\
				<span class="value" param="'+the_default_val+'">'+the_default_text+'</span>\
			</div>\
			<div class="handle">\
				<i class="drop_arr"></i>\
			</div>\
			<div class="options">\
				<span class="items_pointer"></span>\
				<div class="items">\
				<ul>\
					'+options+'\
				</ul>\
				</div>\
			</div>\
	</div>';

        jQuery(tpl).insertAfter(obj);
        custom_select2_i++;
    });
}

function LP_trigger_changed(){
    var who = jQuery(this).attr("title");
    var val = jQuery(this).val();
    var the_val = jQuery("#"+who+" li.item[param='"+val+"']").html();
    jQuery("#"+who+" .mid_drop .value").html(the_val).attr("param",val);
}

/* CUSTOM Scroll */

function csb(){
    var meter_height = jQuery(".tymdripDIV #meter_times").height();
    var handle_height = (320/(meter_height))*320;
    if(handle_height>=320){
        jQuery(".csb_main .csb_rail").hide();
        jQuery(".csb_main .csb_rail .csb_handle").hide();
        jQuery(".tymdripDIV #meter_times").css("margin-top","0px");
        jQuery(".csb_main").css("height","auto");
    }else{
        jQuery(".csb_main").css("height","320px");
        jQuery(".csb_main .csb_rail").show();
        jQuery(".csb_main .csb_rail .csb_handle").show();
        jQuery(".csb_main .csb_rail .csb_handle").css("height",handle_height+"px").draggable({
            axis:'y',
            delay : 0,
            containment : ".csb_rail",
            scroll: false,
            start : function(){
            },
            drag : function(event,ui){
                var top = jQuery(this).css("top");
                var diff_percentage = (parseInt(top)/320)*meter_height;
                jQuery(".tymdripDIV #meter_times").css("margin-top","-"+diff_percentage+"px");
            }
        });
    }
}
/* END CUSTOM Scroll */

/* CUSTOM RADIO */

function LP_custom_checkbox(){
    jQuery("input.custom_checkbox").each(function(i){
        var obj = jQuery(this);
        if(jQuery(obj).hasClass("CR") == false){
            jQuery(obj).hide().removeClass("custom_checkbox");
            var the_classes = jQuery(obj).attr("class");
            jQuery(obj).addClass("custom_checkbox_"+i).addClass("CR");
            var on    = jQuery(obj).val();
            var id    = jQuery(obj).attr("id");
            var off   = jQuery("#"+id+"_cb").val();
            var is_on = "";
            var is_on_h = "";
            if(jQuery(obj).is(":checked")==false){
                is_on = "style=\"margin-left:-30px;\"";
                is_on_h = "style=\"left:0px;\"";
            }
            var tpl = "<div class=\"custom_checkbox "+the_classes+"\">\
				<div class=\"options\" param=\""+"custom_checkbox_"+i+"\">\
					<div class=\"slider\" "+is_on+">\
						<div class=\"on\">"+on+"</div>\
						<div class=\"off\">"+off+"</div>\
					</div>\
					<div class=\"handle\" "+is_on_h+"><div></div></div>\
				</div>\
				<div class=\"val_labels\">\
					<span>"+on+"</span>\
					<span>"+off+"</span>\
				</div>\
			</div>";

            jQuery(tpl).insertAfter(obj);
        }
    });
}

jQuery(".custom_checkbox .slider, .custom_checkbox .handle").live("click",function(){
    var the_custom_checkbox = jQuery(this).parent();
    var the_checkbox = jQuery(this).parent().attr("param");
    if(parseInt(jQuery(".handle",the_custom_checkbox).css("left")) == 0){
        //toggle On the checkbox
        jQuery("."+the_checkbox).attr("checked",true).trigger("change");
        jQuery(".handle",the_custom_checkbox).stop().animate({left:"36px"},100);
        jQuery(".slider",the_custom_checkbox).stop().animate({"margin-left": 0},100);
    }else{
        //toggle OFF the checkbox
        jQuery("."+the_checkbox).attr("checked",false).trigger("change");
        jQuery(".handle",the_custom_checkbox).stop().animate({left:0},100);
        jQuery(".slider",the_custom_checkbox).stop().animate({"margin-left": "-30px"},100);
    }

});
/* END CUSTOM RADIO */
/* END Custom Select */

/* Dynamic js/css loader*/

function loadjscssfile(filename, filetype){
    if (filetype=="js"){ //if filename is a external JavaScript file
        var fileref=document.createElement('script')
        fileref.setAttribute("type","text/javascript")
        fileref.setAttribute("src", filename)
    }
    else if (filetype=="css"){ //if filename is an external CSS file
        var fileref=document.createElement("link")
        fileref.setAttribute("rel", "stylesheet")
        fileref.setAttribute("type", "text/css")
        fileref.setAttribute("href", filename)
    }
    if (typeof fileref!="undefined")
        document.getElementsByTagName("head")[0].appendChild(fileref)
}