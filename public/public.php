<?php

if (get_option('listencat_key')) {

	function listencat_display_player ( $content ) {
		$id = get_the_ID();
		$enabled = boolval( get_post_meta($id, 'listencat_enabled', true) );
		$audio_path = get_post_meta($id, 'listencat_audioFile', true);

		if ( $enabled && is_single($id) ) {

			$custom_content = '
			<div class="listencat-audio-player">
				<input type="hidden" name="wp_post_id" value="' . $id . '">
				<div id="listencat-initial-player-screen" class="initial-player-screen " onclick="removeInitialScreen()">
					<div class="play-button"></div>
					<div class="welcome-message">
						Listen to this article
					</div>
					<div class="welcome-message sm">
						Listen to<br/>this article
					</div>
					<div class="equalizer"></div>
					<div class="powered-by">
						Text to speech by <a href="https://listencat.com">Listencat</a>
					</div>
					<div class="powered-by sm">
						Text to speech<br/>by <a href="https://listencat.com">Listencat</a>
					</div>
				</div>
				<div id="listencat-control-buttons" class="control-buttons hidden">
					<div id="seek-bwd" onclick="seekBackward()">
						<img src="' . plugin_dir_url( __FILE__ ) . 'images/player/replay-button.svg">
					</div>
					<div id="play-btn"></div>
					<div id="seek-fwd" onclick="seekForward()">
						<img src="' . plugin_dir_url( __FILE__ ) . 'images/player/forward-button.svg">
					</div>
				</div>
				<div id="listencat-player-controls" class="player-controls scrubber hidden">
				<span id="seek-obj-container">
					<progress id="seek-obj" value="0" max="1"></progress>
				</span>
					<small id="start-time">0:00</small>
					<small id="end-time">0:00</small>

					<span class="powered-by">
					Text to speech by <a href="https://listencat.com">Listencat</a>
				</span>
				</div>
				<div style="clear: both;"></div>
				<div class="audio-wrapper" id="player-container" href="javascript:;">
					<audio id="player" ontimeupdate="initProgressBar()" preload="auto">
						<source src="' . $audio_path . '" type="audio/mp3">
					</audio>
				</div>
			</div>';
			$custom_content = $content . $custom_content;
			return $custom_content;
		} else {
			return $content;
		}

	}

	add_filter('the_content', 'listencat_display_player');

}

?>
