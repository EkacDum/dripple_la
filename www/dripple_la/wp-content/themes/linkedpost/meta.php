<?php
global $wpdb, $shardb_prefix, $post;
$global_db = $shardb_prefix."global";

$current_blog_id = get_current_blog_id();

$sql = "SELECT * FROM `$global_db`.`wp_channels`";
$channels = $wpdb->get_results($sql,ARRAY_A);

$post_channels = get_the_channel($post->ID, $current_blog_id);

?>
<div class="my_meta_control">     
    <label>Channel</label>
    <p>
        <select type="text" name="LP_channel[]"  style="height:100px; width:200px;" multiple>
            <?php foreach($channels as $channel):
                $selected = "";
                foreach($post_channels as $key => $postchan){
                    if($postchan["channel_id"] == $channel["id"]){
                        $selected = "selected";
                        unset($post_channels[$key]);
                        break;
                    }
                }
            ?>
            <option value="<?php echo $channel["id"];?>" <?php echo $selected; ?>><?php echo $channel["name"];?></option>
            <?php endforeach; ?>
        </select>
        <span>This is the URL of the article.</span>
    </p>
    <label>Article URL</label>
    <p>
        <input type="text" name="story_URL" value="<?php if(!empty($meta['story_URL'])) echo $meta['story_URL']; ?>"/>
        <span>This is the URL of the article.</span>
    </p>
</div>