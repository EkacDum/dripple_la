<?php
global $post;
$post_id = $post->ID;
$blog_id = LP_get_user_blog_id();
$user_topics = LP_get_user_topics();
$post_topic = LP_get_post_topic($post_id, $blog_id);
$story_URL = LP_get_post_story_URL($post_id, $blog_id);
?>
<div class="my_meta_control">     
    <label>Topic</label>
    <p>
        <select type="text" name="LP_topic"  style="width:200px;">
            <?php 
            $selected = "";
            foreach($user_topics as $key => $u_topic):
                if($post_topic["ID"] == $u_topic["ID"]){
                    $selected = "selected";
                }else{
                    $selected = "";
                }
            ?>
            <option value="<?php echo $u_topic["ID"];?>" <?php echo $selected; ?>><?php echo $u_topic["post_title"];?></option>
            <?php endforeach; ?>
        </select>
        <span>Specify the topic of this Drip.</span>
    </p>
    <label>Article URL</label>
    <p>
        <input type="text" name="story_URL" value="<?php if(!empty($story_URL['story_URL'])) echo $story_URL['story_URL']; ?>"/>
        <span>This is the URL of the article.</span>
    </p>
</div>