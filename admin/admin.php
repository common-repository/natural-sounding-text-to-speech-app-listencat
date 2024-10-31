<?php

require_once plugin_dir_path( __FILE__ ) . 'api.php';
require_once plugin_dir_path( __FILE__ ) . 'ajax.php';
require_once plugin_dir_path( __FILE__ ) . 'notices.php';
require_once plugin_dir_path( __FILE__ ) . 'posts-list.php';
require_once plugin_dir_path( __FILE__ ) . 'single-post.php';

//
// SETUP ADMIN MENU
//
add_action( 'admin_menu', 'listencat_add_menu_page' );

function listencat_add_menu_page() {
	add_menu_page(
		'Listencat',
		'Listencat',
		'manage_options',
		'listencat',
		'listencat_options_page_html',
		plugin_dir_url(__FILE__) . 'images/logo.png',
		100
	);
}

function listencat_options_page_html() {
?>
	<div class="wrap">
		<h1 class="listencat__header"><?php _e( 'Listencat Text to Speech Settings', 'listencat' ); ?></h1>
		<p class="listencat__enable-title">
			<?php _e( 'Enable the plugin', 'listencat' ); ?>
			<?php if (get_option('listencat_key')): ?>
				<span class="green"> - <?php _e( 'Enabled', 'listencat' ); ?></span>
			<?php endif; ?>
		</p>
		<p class="listencat__enable-text">
			<?php _e( 'Connect your Listencat account to start converting your posts into natural sounding audio articles. ', 'listencat' ); ?>
			<a href="http://app.listencat.com" target="_blank"><?php _e( 'Sign up for free', 'listencat' ); ?></a>
			<?php _e( ' in case you don’t have an account yet.', 'listencat' ); ?>
		</p>
		<form action="<?php echo admin_url( 'admin-post.php'); ?>" method="post" class="listencat__form" >

			<?php if (get_option('listencat_key')): ?>
				<input type="hidden" name="action" value="disable_listencat_plugin" />
			<?php else: ?>
				<input type="hidden" name="action" value="enable_listencat_plugin" />
			<?php endif; ?>

			<label><?php _e('API key', 'listencat'); ?></label>
			<div class="listencat__input-holder">
				<input type="text" name="listencat_key" value="<?php echo get_option('listencat_key'); ?>" placeholder="Enter your API key here" />
				<p><?php _e( 'You can find the API key in the Settings section of your Listencat account.', 'listencat' ); ?></p>
			</div>

			<?php if (get_option('listencat_key')): ?>
				<?php submit_button( __( 'Disconnect account', 'listencat' ), 'secondary' ); ?>
			<?php else: ?>
				<?php submit_button( __( 'Connect account', 'listencat' ) ); ?>
			<?php endif; ?>

		</form>
		<?php if (!get_option('listencat_key')): ?>
			<p class="listencat__cta-text"><?php _e( 'Don’t have an account?', 'listencat' ); ?> <a href="http://app.listencat.com" target="_blank"><?php _e( 'Sign up for free', 'listencat' ); ?></a>.</p>
		<?php endif; ?>

		<?php if (get_option('listencat_key')): ?>
			<div class="listencat__options">
				<p class="options-title"><?php _e( 'Other settings', 'listencat' ); ?></p>
				<form action="<?php echo admin_url( 'admin-post.php'); ?>" method="post" class="options-form" >
					<input type="hidden" name="action" value="change_listencat_settings" />
					<div class="options-input-holder">
						<input type="checkbox" id="auto" name="auto" value="auto" <?php echo get_option('listencat_auto') ? 'checked' : '' ?> ><label for="auto"><?php _e( 'Add audio automatically as new posts are created', 'listencat' ); ?></label>
					</div>
					<div class="options-input-holder">
						<input type="checkbox" id="count" name="count" value="count" <?php echo get_option('listencat_count') ? 'checked' : '' ?> ><label for="count"><?php _e( 'Display “Play count” statistics in Posts section', 'listencat' ); ?></label>
					</div>
					<div class="options-input-holder">
						<input type="checkbox" id="time" name="time" value="time" <?php echo get_option('listencat_time') ? 'checked' : '' ?> ><label for="time"><?php _e( 'Display “Play time” statistics in Posts section', 'listencat' ); ?></label>
					</div>
					<p class="options-disclaimer"><?php _e( 'You can see detailed statistics and edit other settings in your Listencat account.', 'listencat' ); ?></p>
					<?php submit_button( __( 'Save Changes', 'listencat' ) ); ?>
				</form>
				<p class="options-cta"><?php _e( 'Need help?', 'listencat' ); ?> <?php _e( 'Click', 'listencat' ); ?> <a href="https://listencat.freshdesk.com/" target="_blank"><?php _e( 'here', 'listencat' ); ?></a> <?php _e( 'for support.', 'listencat' ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}

//
// HANDLE API KEY FORM SUBMIT
//
add_action( 'admin_post_enable_listencat_plugin', 'listencat_enable_plugin' );

function listencat_enable_plugin() {
	$listencat_key = sanitize_text_field($_POST["listencat_key"]);
	$key = ( !empty($listencat_key) ) ? $listencat_key : NULL;

	$token_valid = lictencat_api_verify_token($key);

	if ($token_valid) {

		update_option( 'listencat_key', $key );

		$redirect_url = get_bloginfo("url") . "/wp-admin/admin.php?page=listencat&status=success";
		header("Location: ".$redirect_url);
		exit;

	} else {

		$redirect_url = get_bloginfo("url") . "/wp-admin/admin.php?page=listencat&status=failure";
		header("Location: ".$redirect_url);
		exit;

	}

}

add_action( 'admin_post_disable_listencat_plugin', 'listencat_disable_plugin' );

function listencat_disable_plugin() {
	delete_option( 'listencat_key' );

	$redirect_url = get_bloginfo("url") . "/wp-admin/admin.php?page=listencat";
	header("Location: ".$redirect_url);

	exit;

}


//
// HANDLE CHANGE SETTINGS FORM SUBMIT
//
add_action( 'admin_post_change_listencat_settings', 'listencat_change_settings' );

function listencat_change_settings() {
	$listencat_auto = sanitize_key($_POST["auto"]);
	$listencat_count = sanitize_key($_POST["count"]);
	$listencat_time = sanitize_key($_POST["time"]);

	$auto = (!empty($listencat_auto)) ? $listencat_auto : NULL;
	$count = (!empty($listencat_count)) ? $listencat_count : NULL;
	$time = (!empty($listencat_time)) ? $listencat_time : NULL;

	update_option( 'listencat_auto', $auto === NULL ? false : true );
	update_option( 'listencat_count', $count === NULL ? false : true );
	update_option( 'listencat_time', $time === NULL ? false : true );

	$redirect_url = get_bloginfo("url") . "/wp-admin/admin.php?page=listencat&status=settingschanged";
	header("Location: ".$redirect_url);
	exit;

}

//
// HANDLE CONTENT CHANGE & ADDING NEW POST
//
add_action('transition_post_status', 'listencat_post_transition', 10, 3);

function listencat_post_transition($new_status, $old_status, $post) {
	if('publish' === $new_status && 'publish' !== $old_status && $post->post_type === 'post') {

		if ( get_option('listencat_auto') ) {

			$post_id = $post->ID;

			$res = lictencat_convert_post($post_id);

			if ($res) {
				$audioFile = esc_url_raw($res['audioFile']);
				$playCount = intval($res['playCount']);
				$playMinutes = intval($res['playMinutes']);

				update_post_meta($post_id, 'listencat_enabled', true);
				update_post_meta($post_id, 'listencat_audioFile', $audioFile);
				update_post_meta($post_id, 'listencat_count', $playCount);
				update_post_meta($post_id, 'listencat_time', $playMinutes);
			}

		}

		return;

	} else {

	  	$post_id = $post->ID;
	  	$enabled = boolval( get_post_meta($post_id, 'listencat_enabled', true) );

	  	if ($enabled) {

			$res = lictencat_convert_post($post_id);

			if ($res) {
				$audioFile = esc_url_raw($res['audioFile']);
				$playCount = intval($res['playCount']);
				$playMinutes = intval($res['playMinutes']);

				update_post_meta($post_id, 'listencat_enabled', true);
				update_post_meta($post_id, 'listencat_audioFile', $audioFile);
				update_post_meta($post_id, 'listencat_count', $playCount);
				update_post_meta($post_id, 'listencat_time', $playMinutes);
			}
		}

	}
}

?>
