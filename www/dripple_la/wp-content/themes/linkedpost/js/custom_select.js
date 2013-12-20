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
		LP_console(the_label);
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
			// LP_console(jQuery(obj).is(":checked") == false);
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