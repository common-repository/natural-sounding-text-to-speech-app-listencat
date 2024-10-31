<?php 

// 
// ADD COLUMNS
// 

// ENABLE
if ( get_option('listencat_key') ) {

	add_filter( 'manage_posts_columns', 'listencat_add_enable_column' );
	function listencat_add_enable_column( $columns ) {
		$columns_merged = array_merge( $columns, 
		array( 'listencat_enable' => __( 'Listencat', 'listencat' ) ) );

		$columns_ordered = array();
		foreach($columns_merged as $key => $value) {
			if ($key == 'date') {
				$columns_ordered['listencat_enable'] = 'listencat_enable';
			}
				$columns_ordered[$key] = $value;
		}

		return $columns_ordered;
	}

	add_action( 'manage_posts_custom_column' , 'listencat_display_enable_column', 10, 2 );
	function listencat_display_enable_column( $column, $post_id ) {
	    if ($column == 'listencat_enable'){
	    	$checked = boolval( get_post_meta($post_id, 'listencat_enabled', true) );
	        echo '<label class="listencat-switch">
					<input type="checkbox"' . ($checked ? 'checked' : '') . ' onclick="lictencat_handle_enable_toggle(event, this, ' . $post_id . '); " class="listen_button" id="listen_button-97" name="listen_button">
					<span class="listencat-slider round"></span>
				</label>';
	    }
	}

}

// PLAY TIME
if ( get_option('listencat_key') && get_option('listencat_time') ) {

	add_filter( 'manage_posts_columns', 'listencat_add_time_column' );
	function listencat_add_time_column( $columns ) {
		$columns_merged = array_merge( $columns, 
		array( 'listencat_time' => __( 'Play minutes', 'listencat' ) ) );

		$columns_ordered = array();
		$flag = FALSE;
		foreach($columns_merged as $key => $value) {
			if ($flag == TRUE) {
				$columns_ordered['listencat_time'] = 'listencat_time';
				$flag = FALSE;
			}
			if ($key == 'listencat_enable') {
				$flag = TRUE;
			}
				$columns_ordered[$key] = $value;
		}

		return $columns_ordered;
	}

	add_action( 'manage_posts_custom_column' , 'listencat_display_time_column', 10, 2 );
	function listencat_display_time_column( $column, $post_id ) {
	    if ($column == 'listencat_time'){
	    	echo get_post_meta($post_id, 'listencat_time', true);
	    }
	}

}

// PLAY COUNT
if ( get_option('listencat_key') && get_option('listencat_count') ) {

	add_filter( 'manage_posts_columns', 'listencat_add_count_column' );
	function listencat_add_count_column( $columns ) {
		$columns_merged = array_merge( $columns, 
		array( 'listencat_count' => __( 'Play count', 'listencat' ) ) );

		$columns_ordered = array();
		$flag = FALSE;
		foreach($columns_merged as $key => $value) {
			if ($flag == TRUE) {
				$columns_ordered['listencat_count'] = 'listencat_count';
				$flag = FALSE;
			}
			if ($key == 'listencat_enable') {
				$flag = TRUE;
			}
				$columns_ordered[$key] = $value;
		}

		return $columns_ordered;
	}

	add_action( 'manage_posts_custom_column' , 'listencat_display_count_column', 10, 2 );
	function listencat_display_count_column( $column, $post_id ) {
	    if ($column == 'listencat_count'){
	    	echo get_post_meta($post_id, 'listencat_count', true);
	    }
	}

}

// 
// BULK ACTIONS
// 
if ( get_option('listencat_key') ) {

	// REGISTER BULK ACTIONS
	add_filter( 'bulk_actions-edit-post', 'listencat_register_bulk_actions' );
	function listencat_register_bulk_actions($bulk_actions) {
		$bulk_actions['listencat_add_audio'] = __( 'Add Audio', 'listencat');
		$bulk_actions['listencat_remove_audio'] = __( 'Remove Audio', 'listencat');
		return $bulk_actions;
	}

	// HANDLE BULK ACTIONS
	add_filter( 'handle_bulk_actions-edit-post', 'listencat_bulk_actions_handler', 10, 3 );
	function listencat_bulk_actions_handler( $redirect_to, $doaction, $post_ids ) {

		if ( $doaction == 'listencat_add_audio' ) {
			$count = 0;

			foreach ( $post_ids as $post_id ) {
				$enabled = boolval( get_post_meta($post_id, 'listencat_enabled', true) );

				if (!$enabled) {

					$res = lictencat_convert_post($post_id);

					if ($res) {
						$audioFile = esc_url_raw($res['audioFile']);
						$playCount = intval($res['playCount']);
						$playMinutes = intval($res['playMinutes']);

						update_post_meta($post_id, 'listencat_enabled', true);
						update_post_meta($post_id, 'listencat_audioFile', $audioFile);
						update_post_meta($post_id, 'listencat_count', $playCount);
						update_post_meta($post_id, 'listencat_time', $playMinutes);

						$count++;
					} else {
						$redirect_to = preg_replace('/\?.*/', '', $redirect_to);
						$redirect_to = add_query_arg( 'listencat_unable_to_convert', true, $redirect_to );
						return $redirect_to;
					}
				}
			}

			$redirect_to = preg_replace('/\?.*/', '', $redirect_to);
			$redirect_to = add_query_arg( 'bulk_listencat_audio_added', true, $redirect_to );
			return $redirect_to;
		}

		if ( $doaction == 'listencat_remove_audio' ) {
			$count = 0;

			foreach ( $post_ids as $post_id ) {
				$enabled = boolval( get_post_meta($post_id, 'listencat_enabled', true) );

				if ($enabled) {
					$res = lictencat_disable_post($post_id);

					if ($res) {
						update_post_meta($post_id, 'listencat_enabled', false);
						$count++;
					}
				}
			}

			$redirect_to = preg_replace('/\?.*/', '', $redirect_to);
			$redirect_to = add_query_arg( 'bulk_listencat_audio_removed', $count, $redirect_to );
			return $redirect_to;
		}

	}

}

?>