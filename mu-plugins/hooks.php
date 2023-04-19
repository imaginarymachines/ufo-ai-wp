<?php
/**
 * This file is hooks for testing the plugin.
 *
 * Should be loaded as mu-plugin
 */

use ImaginaryMachines\UfoAi\Settings;
use ImaginaryMachines\UfoAi\UfoAi;

/**
 * Change the URL and API Key
 */
add_filter('ufoaiwp_get_setting', function(
	$setting, $key
){
	//Use local dev of new API
	if( Settings::URL === $key ){
		return 'http://localhost:3000/';
	}
	//Any API key
	if( Settings::KEY === $key ){
		return 'aaa';
	}
	return $setting;

},10,2);

/**
 * Test the client
 */
add_action('ufoaiwp', function(
	UfoAi $plugin
) {
	$plugin->getClient()
		->text(
			'Say hello in Spanish'
		);
});

/**
 * Verify false in request
 */
add_filter('ufoaiwp_request_args', function(
	$args
){
	$args['sslverify'] = false;
	$args['verify'] = false;
	return $args;
});
/**
 * Change version to match new API
 */
add_filter('ufoai_api_version', function(
	$version
){
	return 'ufoai';
});

add_filter('ufoai_client_url', function(
	$url,
	$endpoint,
	$client
){
	return $url;
	var_dump(
		$url,
		$endpoint,
	);
	exit;
},10,3);
