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