<?php

namespace ImaginaryMachines\UfoAi\Api;

use ImaginaryMachines\UfoAi\Client;
use ImaginaryMachines\UfoAi\UfoAi;

/**
 * Base class for REST API endpoints
 */
abstract class Endpoint {

	/**
	 * Client instance
	 *
	 * @var Client
	 */
	protected $client;

	/**
	 * Plugin instance
	 *
	 * @var UfoAi
	 */
	protected $plugin;

	/**
	 * REST API namespace
	 *
	 * @var string
	 */
	protected $namespace = 'ufo-ai/v1';


	/**
	 * Constructor
	 *
	 * @param Plugin $plugin
	 */
	public function __construct( UfoAi $plugin ) {
		$this->plugin = $plugin;
		$this->client = $this->plugin->getClient();
	}

	/**
	 * Register endpoints
	 */
	abstract public function registerRoutes();

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
