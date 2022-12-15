<?php

namespace ImaginaryMachines\ContentMachine\Api;

use ImaginaryMachines\ContentMachine\Client;
use ImaginaryMachines\ContentMachine\ContentMachine;
use ImaginaryMachines\ContentMachine\PromptRequest;
use ImaginaryMachines\ContentMachine\Settings;

/**
 * REST API endpoints for plugin settings
 */
class SettingsEndpoint {

	const NAMESPACE = 'content-machine/v1';

	public static function factory() {
		$obj = new static();
		\register_rest_route(
			self::NAMESPACE,
			'/settings',
			array(
				'methods'             => 'POST',
				'callback'            => array( $obj, 'updateSettings' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'key' => array(
						'required' => true,
						'type'     => 'string',
					),

				),
			)
		);
	}


	/**
	 * Update settings
	 */
	public function updateSettings( $request ) {
		$key = $request->get_param( 'key' );
		Settings::set( Settings::KEY, $key );
		return Settings::getAll();
	}

	/**
	 * Default permission_callback
	 *
	 * @param \WP_REST_Request $request
	 * @return bool
	 */
	public function authorize( $request ) {
		$capability = is_multisite() ? 'delete_sites' : 'manage_options';
		return current_user_can( $capability );
	}


}
