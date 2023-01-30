<?php

namespace ImaginaryMachines\UfoAi\Api;

use ImaginaryMachines\UfoAi\Client;
use ImaginaryMachines\UfoAi\UfoAi;
use ImaginaryMachines\UfoAi\Settings;

/**
 * REST API endpoints for proxying requests to the Upcycled Found Objects API
 */
class Proxy extends Endpoint{




	/**
	 * Register endpoints
	 */
	public  function registerRoutes() {

		\register_rest_route(
			self::NAMESPACE,
			'/connected',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'checkConnection' ),
				'permission_callback' => array( $this, 'authorize' ),

			)
		);

		\register_rest_route(
			self::NAMESPACE,
			'/text',
			array(
				'methods'             => array( 'POST', 'GET' ),
				'callback'            => array( $this, 'handleText' ),
				'permission_callback' => array( $this, 'authorize' ),
				'args'                => array(
					'prompt'      => array(
						'required' => true,
						'type'     => 'string',
					),
					'temperature' => array(
						'required' => false,
						'type'     => 'float',
						'default'  => 0.8,
					),
				),

			)
		);
	}

	/**
	 * Check if account connected
	 */
	public function checkConnection() {
		$key = $this
			->plugin
			->getSettings()
			->get( Settings::KEY );
		if ( empty( $key ) ) {
			return new \WP_Error(
				'no_api_key',
				'No Saved API Key',
			);
		}
		$connected = $this->client->isConnected();
		return array(
			'connected' => $connected,
		);
	}

	/**
	 * Handle text request
	 */
	public function handleText( $request ) {
		$prompt      = $request->get_param( 'prompt' );
		$temperature = $request->get_param( 'temperature', 0.8 );

		return $this->client->text( $prompt, $temperature );
	}


}
