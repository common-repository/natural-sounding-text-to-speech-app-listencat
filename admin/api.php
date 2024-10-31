<?php 

$LISTENCAT_BASE_URL = "https://app.listencat.com/api/v1";

// 
// VERIFY TOKEN
// 
function lictencat_api_verify_token($token) {

	global $LISTENCAT_BASE_URL;

	$args = array(
		'timeout'     => 45,
		'sslverify'   => false,
		'headers'     => array(
			'Authorization' => 'Bearer '. $token,
			'Content-Type'  => 'application/json',
		),
	);

	$request = wp_remote_get( $LISTENCAT_BASE_URL . '/token', $args );
	$response = wp_remote_retrieve_body( $request );

	$response_decoded = json_decode($response);

	$res = false;

	if ($response_decoded) {
		if ($response_decoded->status === 'success') {
			$res = true;
		}
	}

	return $res;
}

// 
// CONVERT / UPDATE POST
// 
function lictencat_convert_post($id) {

	global $LISTENCAT_BASE_URL;

	$content = get_post_field('post_content', $id);
	$title = get_the_title($id);
	$url = get_permalink($id);
	$author_id = get_post_field('post_author', $id);
	$author = get_the_author_meta('display_name' , $author_id);
	$thumbnail_url = get_the_post_thumbnail_url($id);

	$body = array(
		'id' 			=> $id,
		'audioEnabled'	=> true,
		'content' 		=> $content,
		'title' 		=> $title,
		'author'		=> $author,
		'url' 			=> $url,
		'image'			=> $thumbnail_url ? $thumbnail_url : null,
	);

	$args = array(
		'method'      => 'POST',
		'timeout'     => 45,
		'sslverify'   => false,
		'headers'     => array(
			'Authorization' => 'Bearer '. get_option('listencat_key'),
			'Content-Type'  => 'application/json',
		),
		'body'        => json_encode($body)
	);

	$request = wp_remote_post( $LISTENCAT_BASE_URL . '/post' , $args );
	$response = wp_remote_retrieve_body( $request );
	
	$response_decoded = json_decode( $response, true );

	return $response_decoded['post'];
}

// 
// DISABLE POST
// 
function lictencat_disable_post($id) {

	global $LISTENCAT_BASE_URL;

	$body = array(
		'id' 			=> $id,
		'audioEnabled'	=> false,
	);

	$args = array(
		'method'      => 'POST',
		'timeout'     => 45,
		'sslverify'   => false,
		'headers'     => array(
			'Authorization' => 'Bearer '. get_option('listencat_key'),
			'Content-Type'  => 'application/json',
		),
		'body'        => json_encode($body)
	);

	$request = wp_remote_post( $LISTENCAT_BASE_URL . '/post' , $args );
	$response = wp_remote_retrieve_body( $request );
	
	$response_decoded = json_decode( $response, true );

	return $response_decoded['post'];
}

// 
// GET POST
// 
function lictencat_api_get_posts_info($ids) {

	global $LISTENCAT_BASE_URL;

	$args = array(
		'timeout'     => 45,
		'sslverify'   => false,
		'headers'     => array(
			'Authorization' => 'Bearer '. get_option('listencat_key'),
			'Content-Type'  => 'application/json',
		),
	);

	$query = '?';

	foreach ( $ids as $i => $id ) {
		$query .= 'id%5B%5D=' . $id;
		if ( $i < count($ids) - 1 ) $query .= '&';
	}

	$request = wp_remote_get( $LISTENCAT_BASE_URL . '/post' . $query , $args );
	$response = wp_remote_retrieve_body( $request );

	$response_decoded = json_decode( $response, true );

	return $response_decoded['posts'];
}

?>