<?php
/* 
 * Toggles the day Active/Inactive
 */
function LP_meter_toggle_day()
{
    if(isset($_POST["day"]) && $_POST["day"]!="" && isset($_POST["toggle"]) && $_POST["toggle"]!="" && $_POST["topic_id"]!=""){
	gloval $switched;
        $day = $_POST["day"];
        $LP_user_drip_settings["drip_day"][$day] = ($_POST["toggle"] == 'true' ? 0:1);
        $user_ID = get_current_user_id(); 
        update_usermeta( $user_ID, "LP_drip_settings", $LP_user_drip_settings );
		
		$blog_id = LP_get_user_blog_id();
		switch_to_blog($blog_id);
			$topic_meter = get_post_meta( $_POST["topic_id"], "_LP_topic_meter", true );
		restore_current_blog();
        
		$settings = LP_meter_fetch_settings_f();
        $to_return["LP_user_drip_settings"] = $LP_user_drip_settings;
        $to_return["post"] = $_POST;
        echo json_encode($to_return);
    }
    LP_reschedule_drips();
    die();
}
add_action('wp_ajax_LP_meter_toggle_day', 'LP_meter_toggle_day');

/* 
 * Add a time
 */
function LP_meter_add_time()
{
    // if(isset($_POST["time"]) && $_POST["time"]!=""){
        global $LP_user_drip_settings;
        
        $LP_user_drip_settings["drip_time"]["00:00 pm"] = "00:00 pm";
        $LP_user_drip_settings["drip_time"] = sort_LP_drip_time($LP_user_drip_settings["drip_time"]);
        
        $user_ID = get_current_user_id(); 
        update_usermeta( $user_ID, "LP_drip_settings", $LP_user_drip_settings );
        $settings = LP_meter_fetch_settings_f();
        $to_return["LP_user_drip_settings"] = $LP_user_drip_settings;
        $to_return["post"] = $_POST;
        echo json_encode($to_return);
    // }
    // LP_reschedule_drips();
    die();
}
add_action('wp_ajax_LP_meter_add_time', 'LP_meter_add_time');

/* 
 * Remove a time
 */
function LP_meter_remove_time()
{
    if(isset($_POST["time_i"]) && $_POST["time_i"]!=""){
        global $LP_user_drip_settings;
        $time_i = $_POST["time_i"];
        unset($LP_user_drip_settings["drip_time"][$time_i]);
        $user_ID = get_current_user_id(); 
        update_usermeta( $user_ID, "LP_drip_settings", $LP_user_drip_settings );
        $to_return["LP_user_drip_settings"] = LP_meter_fetch_settings_f();
        $to_return["post"] = $_POST;
        echo json_encode($to_return);
    }
    LP_reschedule_drips();
    die();
}
add_action('wp_ajax_LP_meter_remove_time', 'LP_meter_remove_time');

/* 
 * Update a time
 */
function LP_meter_update_time()
{
    if(isset($_POST["time_i"]) && $_POST["time_i"]!="" && isset($_POST["time"]) && $_POST["time"]!=""){
        global $LP_user_drip_settings;
        $time_i = $_POST["time_i"];
        $new_arr = array();
        foreach($LP_user_drip_settings["drip_time"] as $key => $value){
            if($key == $time_i){
                $new_arr[$_POST["time"]] = $_POST["time"];
            }else{
                if($key != $_POST["time"]){
                    $new_arr[$key] = $value;
                }
            }
        }
        $LP_user_drip_settings["drip_time"] = $new_arr;
        $LP_user_drip_settings["drip_time"] = sort_LP_drip_time($LP_user_drip_settings["drip_time"]);
        
        $user_ID = get_current_user_id(); 
        update_usermeta( $user_ID, "LP_drip_settings", $LP_user_drip_settings );
        $to_return["LP_user_drip_settings"] = $LP_user_drip_settings;
        $to_return["post"] = $_POST;
        echo json_encode($to_return);
    }
    LP_reschedule_drips();
    die();
}
add_action('wp_ajax_LP_meter_update_time', 'LP_meter_update_time');

/* 
 * Fetch Settings
 */
 
function LP_meter_fetch_settings_f()
{
    return LP_drip_settings($user_meta);
}

function LP_meter_fetch_settings()
{
    $s = LP_meter_fetch_settings_f();
    echo json_encode($s);
    die();
}
add_action('wp_ajax_LP_meter_fetch_settings', 'LP_meter_fetch_settings');

/* 
 * Set settings
 */
function LP_drip_settings($user_meta)
{
    global $LP_user_drip_settings;
	$LP_user_drip_settings = json_decode(LP_get_user_blog_option("DEFAULT_METER"),true);
	return $LP_user_drip_settings;
}
////////////////////////////////////// NEW METER /////////////////////////////////
?>