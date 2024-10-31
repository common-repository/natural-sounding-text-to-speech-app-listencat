<?php 

if ( get_option('listencat_key') ) {

	function listencat_add_metabox()
	{
	    $screens = ['post'];
	    foreach ($screens as $screen) {
	        add_meta_box(
	            'listencat_metabox_id',
	             __( 'Text to speech by Listencat', 'listencat' ),
	            'listencat_metabox_html',
	            $screen,
	            'side',
	            'high'
	        );
	    }
	}
	add_action('add_meta_boxes', 'listencat_add_metabox');

	function listencat_metabox_html($post)
	{	

		$post_id = $post->ID;
		$checked = boolval( get_post_meta($post_id, 'listencat_enabled', true) );
	    ?>
	    <div class="listencat-metabox <?php echo $checked ? 'checked' : '';  ?>">
		    <label class="listencat-switch">
				<input type="checkbox" <?php echo $checked ? 'checked' : '';  ?> onclick="lictencat_handle_enable_toggle_single_post( event, this, <?php echo $post_id; ?> )" class="listen_button" id="listen_button-97" name="listen_button">
				<span class="listencat-slider round"></span>
			</label>
			<p class="listencat-metabox__enabled-text"><?php _e('This post has audio enabled', 'listencat'); ?></p>
			<p class="listencat-metabox__disabled-text"><?php _e('This post does not have audio', 'listencat'); ?></p>
		</div>
	    <?php
	}

}

?>