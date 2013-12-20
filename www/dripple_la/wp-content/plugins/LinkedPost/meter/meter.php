<?php
/* 
 * Save/update topic meter
 */
function LP_save_topic_meter()
{
    if(is_user_logged_in()){
        global $switched;
        $user_id = get_current_user_id();
        $topic_meter = $_POST["topic_meter"];
        $the_meter["drip_day"] = $topic_meter["drip_day"];
        $d_time = array();
        foreach($topic_meter["drip_time"] as $time){
            if(LP_validate_time($time)){
                $d_time[$time] = $time;
            }
        }
        usort($d_time,"LP_sort_meter_time");
        foreach($d_time as $stime){
            $the_meter["drip_time"][$stime] = $stime;
        }
        // $the_meter["drip_time"] = $d_time;
        $topic_id = $_POST["topic_id"];
        $blog_id = LP_get_user_blog_id();
        switch_to_blog($blog_id);
            update_post_meta( $topic_id, "_LP_topic_meter", json_encode($the_meter));
        restore_current_blog();


        LP_reschedule_all_drips($topic_id);

        $timezone = $_POST["timezone"];

        update_blog_option ($blog_id, "timezone_string", $timezone);
        update_user_meta($user_id, "timezone_string", $timezone);
        $new_topic = LP_get_user_topics($topic_id, true);
        echo json_encode($new_topic[0]);
    }
	die();
}
add_action('wp_ajax_LP_save_topic_meter', 'LP_save_topic_meter');

function LP_validate_time($time){
	if(strtotime($time)===false) return false;
	return true;
}

/* 
 * This will return topic meter
 */
function LP_get_topic_meter($topic_id, $blog_id = false)
{
	if($blog_id===false){
		$blog_id = LP_get_user_blog_id();
	}
	
	switch_to_blog($blog_id);
		$topic_meter =  json_decode(get_post_meta($topic_id,"_LP_topic_meter",true),true);
		if($topic_meter == "")
			$topic_meter = json_decode(DEFAULT_METER,true);
	restore_current_blog();
	return $topic_meter;	
}

function LP_sort_meter_time($a, $b)
{
	return strtotime("2013-08-10 ".$a) - strtotime("2013-08-10 ".$b);
}
?>