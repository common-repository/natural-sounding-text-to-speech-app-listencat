<?php
/**
 * Plugin Name:       Natural sounding text to speech app - Listencat
 * Plugin URI:        #
 * Description:       Listencat is a text to speech tool for audience growth and engagement that lets bloggers and publishers convert all your articles into natural, human sounding speech in minutes.
 * Version:           1.1.2
 * Author:            Listencat
 * Author URI:        https://listencat.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       listencat
 */

require_once plugin_dir_path( __FILE__ ) . 'admin/admin.php';
require_once plugin_dir_path( __FILE__ ) . 'public/public.php';


register_activation_hook( __FILE__, 'listencat_activate_plugin' );

function listencat_activate_plugin() {

	// ENABLE OPTIONS BY DEFAULT
	update_option( 'listencat_auto', true );
	update_option( 'listencat_count', true );

}

register_deactivation_hook( __FILE__, 'listencat_deactivate_plugin' );

function listencat_deactivate_plugin() {
}


//
// ADMIN STYLES & SCRIPTS
//
function listencat_admin_style() {
	wp_enqueue_style('listencat-admin-style', plugin_dir_url(__FILE__) . 'admin/css/admin-style.css');
}
add_action('admin_enqueue_scripts', 'listencat_admin_style');

function listencat_admin_scripts() {
	wp_enqueue_script( 'listencat-admin-scripts', plugin_dir_url(__FILE__) . 'admin/js/admin-js.js');
}
add_action('admin_enqueue_scripts', 'listencat_admin_scripts');


//
// FRONT-END STYLES & SCRIPTS
//
function listencat_style() {
	wp_enqueue_style('listencat-style', plugin_dir_url(__FILE__) . 'public/css/player.css' );
}
add_action( 'wp_enqueue_scripts', 'listencat_style' );

function listencat_scripts() {
	wp_enqueue_script('listencat-scripts', plugin_dir_url(__FILE__) . 'public/js/audio-player.js', array( 'jquery' ) );
}
add_action( 'wp_enqueue_scripts', 'listencat_scripts' );


//
// UPDATE POSTS DATA
// hourly & and on every loging in
//

add_action('wp_login', 'listencat_update_posts_data');

add_action( 'listencat_update_posts_data_cron_hook', 'listencat_update_posts_data' );

if ( ! wp_next_scheduled( 'listencat_update_posts_data_cron_hook' ) ) {
    wp_schedule_event( time(), 'hourly', 'listencat_update_posts_data_cron_hook' );
}

function listencat_update_posts_data() {
	$args = array(
		'fields'	 => 'ids',
		'nopaging'	 => true,
		'meta_query' => array(
			array(
				'key' => 'listencat_enabled',
				'value' => true,
				'compare' => '=',
			)
		)
	);
	$listencatEnabledPosts = get_posts($args);

	if ( get_option('listencat_key') && count($listencatEnabledPosts) > 0 ) {
		$posts = lictencat_api_get_posts_info($listencatEnabledPosts);

		foreach ($posts as $i => $post) {

			$post_id = $post['id'];
			$playCount = $post['playCount'];
			$playMinutes = $post['playMinutes'];
			update_post_meta($post_id, 'listencat_count', $playCount);
			update_post_meta($post_id, 'listencat_time', $playMinutes);

		}
	}

}

?>
