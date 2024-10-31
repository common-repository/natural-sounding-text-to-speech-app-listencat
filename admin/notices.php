<?php

function listencat_notices(){
    global $pagenow;

	//
	// CTA WHEN NO POST IS ENABLED
	//
	$args = array(
		'fields'	 => 'ids',
		'meta_query' => array(
			array(
				'key' => 'listencat_enabled',
				'value' => true,
				'compare' => '=',
			)
		)
	);
	$posts = get_posts($args);
	$listencatEnabledPosts = count($posts);
	if ( get_option('listencat_key') && $listencatEnabledPosts == 0 ) {

		$args = array(
			'numberposts' => '1',
			'fields'	  => 'id'
		);
		$newest_post = wp_get_recent_posts( $args );
		if ( count($newest_post) > 0 ) {

			$id = $newest_post[0]['ID'];
			echo '<div class="notice notice-error is-dismissible listencat-notice listencat-notice--add-first">
					<p>' . __('Try adding audio to your first post. Click “Add audio” to convert your latest post into natural sounding speech and make it available for seamless playback. Or go to “Posts” section to add manualy.', 'listencat') . '</p>
					<a href="#" class="button button-primary" id="listencat_add_first_audio" data-id="' . $id . '">' . __('Add audio', 'listencat') . '</a>
				</div>';

		}

	}

	//
	// UPDATE AVAILABLE
	//
	if ( get_option('listencat_key') ) {
		$update_plugins = get_site_transient( 'update_plugins' );
		if( isset( $update_plugins->response[ 'listencat/listencat.php' ] ) ) {
			echo '<div class="notice notice-success is-dismissible">
					<p>' . __('Text to Speech by Listencat has a new version available. Click') . ' <a href="/wp-admin/plugins.php" >' . __('here', 'listencat') . '</a> ' . __('to update it.') . '</p>
				</div>';
		}
	}

	//
	// UNABLE TO CONVERT
	//
	if ( get_option('listencat_key') ) {
		$queries = array();
		parse_str($_SERVER['QUERY_STRING'], $queries);
		if ( isset($queries['listencat_unable_to_convert']) && $queries['listencat_unable_to_convert'] === '1') {
			echo '<div class="notice notice-warning is-dismissible">
					<p>' . __('Listencat was unable to convert the post. Please visit the Listencat account to make sure you have credits available. If the problem persists please reach out in the', 'listencat') . ' <a href="https://listencat.freshdesk.com/" >' . __('help center', 'listencat') . '</a>.</p>
				</div>';
		}
	}

	//
	// CONVERTED ONE POST
	//
	if ( get_option('listencat_key') ) {
		$queries = array();
		parse_str($_SERVER['QUERY_STRING'], $queries);
		if ( isset($queries['listencat_successfuly_converted']) && $queries['listencat_successfuly_converted'] === '1') {
			echo '<div class="notice notice-success is-dismissible">
					<p>' . __('Listencat has added audio for your post.') . '</p>
				</div>';
		}
	}

	//
	// POSTS LIST - BULK POSTS CONVERTED
	//
	if ( get_option('listencat_key') ) {
		$queries = array();
		parse_str($_SERVER['QUERY_STRING'], $queries);
		if ( isset($queries['bulk_listencat_audio_added']) && $queries['bulk_listencat_audio_added'] === '1') {
			echo '<div class="notice notice-success is-dismissible">
					<p>' . __('Listencat has added audio for your posts.') . '</p>
				</div>';
		}
	}

    //
    // ADMIN NOTICES
    //
    if ( $pagenow == 'admin.php' ) {

		$queries = array();
		parse_str($_SERVER['QUERY_STRING'], $queries);

		if ( isset($queries['page']) && isset($queries['status']) ) {
			if ( $queries['page'] === 'listencat' && $queries['status'] === 'success') {
				echo '<div class="notice notice-success is-dismissible">
						<p>' . __('Congratulations! Your Listencat Text to Speech plugin is enabled.', 'listencat') . '</p>
					</div>';
			} else if ($queries['page'] === 'listencat' && $queries['status'] === 'failure') {
				echo '<div class="notice notice-warning is-dismissible">
						<p>' . __('Could not connect your Listencat account using this API key. Please check the API key and try again. If the problem persists, please visit our help section', 'listencat') . ' <a href="https://listencat.freshdesk.com/" target="_blank">' . __('here', 'listencat') . '</a>' . '.</p>
					</div>';
			} else if ($queries['page'] === 'listencat' && $queries['status'] === 'settingschanged') {
				echo '<div class="notice notice-success is-dismissible">
						<p>' . __('Settings successfully changed.', 'listencat') . '</p>
					</div>';
			}
		}

    } else {

    	//
		// CONNECT WITH APP
		//
    	if (!get_option('listencat_key')) {

    		echo '<div class="notice notice-error is-dismissible listencat-notice listencat-notice--connect">
					<p>' . __('Connect with your Listencat.com account to enable the plugin.', 'listencat') . '</p>
					<div class="listencat-notice--connect__button-wrapper">
						<a href="' . get_bloginfo("url") . '/wp-admin/admin.php?page=listencat" class="button button-primary">Connect your account</a>
						<a href="https://app.listencat.com" class="button button-secondary">Try Us Free</a>
						<p><a href="https://www.listencat.com/wordpress" target="_blank">' . __('Learn more', 'listencat') . '</a> ' . __('about text to speech tool for audience growth and engagement.', 'listencat') . '</p>
					</div>
				</div>';

    	}

    }
}

add_action('admin_notices', 'listencat_notices');

 ?>
