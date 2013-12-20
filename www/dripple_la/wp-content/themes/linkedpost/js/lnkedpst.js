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
	
	// jQuery(".signinhed").live("click",show_popup_signin);
    // jQuery(".signinhed").live("click",function(){
        // show_dropdown_loginuser(this);
        // return false;
    // });
    
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
	
	// jQuery(".custom_select").live("click", function(){
		// jQuery("select",this).simulate('mousedown');
	// });
    
    jQuery(".add_topic_btn").live("click",function(){
        slide_down_header_form(".managetopicsdiv");
    });
    jQuery(".manage_linkedin_message").live("click",function(){
        LP_fetch_linkedin_message();
    });    
	jQuery("#dripview, #topicview").live("click",toggle_member_view);
});

function LP_fetch_linkedin_message(){
    var data = {
        action : "LP_fetch_linkedin_message"
    };
    
    jQuery.post(ajaxurl,data,function(r){
        var message = jQuery.parseJSON(r);
        jQuery("#manage_message_template #message_subject").val(message["subject"]);
        jQuery("#manage_message_template #message_body").val(message["body"]);
        slide_down_header_form("#manage_message_template");
    });
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
	// jQuery(".lnkrotateadz").text(obj_top+ " <= "+ scrollt);
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