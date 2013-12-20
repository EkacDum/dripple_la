jQuery("#topic_define_focal_point .cell").live("click",LP_set_focal_point);

function LP_new_topic_define_focal_point(url){
	jQuery("<img>").attr("src",url).load(function(){
		jQuery("#topic_define_focal_point #img").attr("src",url).load(function(){
			var obj = jQuery(this);
			jQuery("#topic_define_focal_point").css("margin-top","-10000px").show();
			setTimeout(function(){
				var ch = jQuery(obj).height()/12;
				var cw = jQuery(obj).width()/12;
				LP_console(jQuery(obj).width());
				jQuery("#topic_define_focal_point .cell").css("height",ch).css("width",cw);
				jQuery("#topic_define_focal_point").css("left","calc(50% - "+(jQuery(obj).width()/2 + 30)+"px)").css("margin-top","0");
			},1000);
			
		});
	});
}

function LP_set_focal_point(){
	var who = (jQuery(this).index())+1;
	var who_f = Math.floor(who/12);
	var who_d = who/12;
	var from_top = Math.ceil(who_d);
	var from_left = Math.round((who_d - who_f) * 12);
	if(from_left == 0 )from_left = 12;
	var focal_point = from_left+"/"+from_top;
	jQuery("#LP_new_topic_uploader #focal_point").val(focal_point);
	var the_img = jQuery(this).parent().parent().find("img#img").attr("src");
	jQuery(".adding_new_topic img#LP_feat_img_ul").attr("src",the_img).addClass("left-"+from_left).addClass("top-"+from_top);
	var to_margin = 0;
	jQuery(".adding_new_topic img#LP_feat_img_ul").load(function(){
		var ih = jQuery(this).height();
		LP_console("ih : "+ih);
		var ch = parseFloat(ih/12);
		LP_console("ch : "+ch);
		var ph = parseFloat(jQuery(this).parent().height());
		LP_console("ph : "+ph);
		var excess = 0;
		LP_console("from_top : "+from_top);
		if(from_top > 6){
			to_margin = 6 * ch;
			LP_console("to_margin : "+to_margin);
			excess = from_top - 6;
		}else{
			to_margin = 0;
			LP_console("to_margin : "+to_margin);
			excess = excess;
		}
		LP_console("excess : "+excess);
		var ec = 0;
		LP_console("excess * ec : "+excess * ch);
		if((excess * ch) > ph){
			ec = (excess * ch) - ph;
		}else{
			ec = (ph - (excess * ch)) * -1;
		}
		
		to_margin+=ec;
		LP_console("to_margin + ec : "+to_margin);
		jQuery(this).css("margin-top","-"+to_margin+"px");
	});
	jQuery("#topic_define_focal_point").hide();
	add_topic_busy = false;
}

/* <div id="topic_define_focal_point">
								<img id="img" src=""/>
								<div class="cellscont">
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
									<div class="cell"><div></div></div>
								</div>
							</div> */
							
/* TOPIC FOCAL POINT 
#topic_define_focal_point{
	position: absolute;
	z-index: 10;
	top: 0;
	padding: 15px;
	background-color: #2d2d2d;
	min-width: 626px;
	max-width: 1000px;
	border-radius: 5px;
	display:none;
}

#topic_define_focal_point  #img{
	margin: 0 auto;
	display: block;
	width: 100%;
}

#topic_define_focal_point .cellscont{
	top: 15px;
	width: calc(100% - 30px);
	height: calc(100% - 30px);
	position:absolute;
}

#topic_define_focal_point .cellscont .cell{
	float: left;
	width: calc(100% / 12);
	height: calc(100% / 12);
	background-color: rgba(1,1,1,.3);
	cursor: pointer;
}
#topic_define_focal_point .cellscont .cell:hover{
	background-color: rgba(1,1,1,0);
}
#topic_define_focal_point .cellscont .cell:hover div{
	background-color: rgba(1,1,1,0);
	border:1px dashed;
	width: calc(100% - 2px);
	height: calc(100% - 2px);
}
END TOPIC FOCAL POINT */