<?php

namespace ImaginaryMachines\UfoAi\Api;

use ImaginaryMachines\UfoAi\Client;
use ImaginaryMachines\UfoAi\UfoAi;
use ImaginaryMachines\UfoAi\PromptRequest;
use ImaginaryMachines\UfoAi\Settings;

/**
 * REST API endpoints for proxying requests to the Upcycled Found Objects API
 */
class Proxy {


	/**
	 * Client instance
	 *
	 * @var Client
	 */
	protected $client;

	const NAMESPACE = 'ufo-ai/v1';

	/**
	 * Factory
	 *
	 * @return self
	 */
	public static function factory() {
		$obj = new static( UfoAi::getClient() )

		\register_rest_route(
			self::NAMESPACE,
			'/connected',
			array(
				'methods'             => 'GET',
				'callback'            => array( $obj, 'checkConnection' ),
				'permission_callback' => array( $obj, 'authorize' ),

			)
		);

		\register_rest_route(
			self::NAMESPACE,
			'/text',
			array(
				'methods'             => array( 'POST', 'GET' ),
				'callback'            => array( $obj, 'handleText' ),
				'permission_callback' => array( $obj, 'authorize' ),
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
	 * Constructor
	 *
	 * @param Client $client
	 */
	public function __construct( Client $client ) {
		$this->client = $client;
	}

	/**
	 * Check if account connected
	 */
	public function checkConnection() {
		$key = Settings::get( Settings::KEY );
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

	public function handleText( $request ) {
		$prompt      = $request->get_param( 'prompt' );
		$temperature = $request->get_param( 'temperature', 0.8 );

		return $this->client->text( $prompt, $temperature );
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
