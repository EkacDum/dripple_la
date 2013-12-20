<?php
/*
	Template Name: Channels
*/
global $user_id, $post_channel, $the_topic ;

get_header();
$ch = false;
if(count($post_channel) > 0){
	$ch = true;
}
?>
</div>
<div>
<div id="tile_view">
	<div class="topics_head">
		<h2><?php echo $post_channel["name"];?> channel</h2>
	</div>
    <div class="topics_cont">
        <?php
        $topics = LP_get_channel_topics($post_channel["id"]);
		// print_r($topics);
        foreach($topics as $topic):
		$the_topic = $topic;
        ?>
		<?php get_template_part( "custom", "topic_tile" );?>
        <?php endforeach; ?>
    </div>
</div>
<?php
get_footer();
?>