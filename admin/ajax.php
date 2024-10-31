<?php 

add_action( 'wp_ajax_listencat_convert_post_ajax', 'listencat_convert_post_ajax' );

function listencat_convert_post_ajax() {

	$id = intval($_POST['id']);
	$enable = sanitize_text_field($_POST['enable']) == 'true' ? true : false;

	if ($enable) {

		$res = lictencat_convert_post($id);

		if ($res) {
			$audioFile = $res['audioFile'];
			$playCount = $res['playCount'];
			$playMinutes = $res['playMinutes'];

			update_post_meta($id, 'listencat_enabled', $enable);
			update_post_meta($id, 'listencat_audioFile', $audioFile);
			update_post_meta($id, 'listencat_count', $playCount);
			update_post_meta($id, 'listencat_time', $playMinutes);

			echo json_encode(array(
				'playCount' => $playCount,
				'playMinutes' => $playMinutes,
			));

			wp_die();
		}

	} else {

		$res = lictencat_disable_post($id);

		if ($res) {

			update_post_meta($id, 'listencat_enabled', $enable);

			echo true;

			wp_die();	
		}

	}

	echo false;

	wp_die();
	
}

?>