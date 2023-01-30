<?php

namespace ImaginaryMachines\UfoAi;

use ImaginaryMachines\UfoAi\Api\Proxy;
use ImaginaryMachines\UfoAi\Api\SettingsEndpoint;
use ImaginaryMachines\UfoAi\Contracts\ClientContract;
use ImaginaryMachines\UfoAi\Client;


class UfoAi {

	/**
	 * The API client.
	 *
	 * @var Client
	 */
	protected $client;

	/**
	 * Plugin settings.
	 *
	 * @var Settings
	 */
	protected $settings;

	/**
	 * Hooks
	 *
	 * @var Hooks
	 */
	protected $hooks;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;

	}

	/**
	 * Set up the plugin.
	 *
	 * @return UfoAi
	 */
	public function init() {
		$this->hooks = new Hooks( $this );
		$this->hooks->addHooks();
		return $this;
	}


	/**
	 * Get plugin settings.
	 *
	 * @return Settings
	 */
	public function getSettings() {
		return $this->settings;
	}

	/**
	 * Get API client.
	 *
	 * @return Client
	 */
	public function getClient(): Client {
		if ( ! isset( $this->client ) ) {
			$this->client = new Client( $this );
		}
		return $this->client;
	}

	/**
	 * Set API client.
	 *
	 * @param ClientContract $client
	 *
	 * @return UfoAi
	 */
	public function setClient( Client $client ) {
		$this->client = $client;
		return $this;
	}

	/**
	 * Loads the plugin's text domain.
	 *
	 * @uses "plugins_loaded" action
	 *
	 * Sites on WordPress 4.6+ benefit from just-in-time loading of translations.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'ufo-ai' );
	}

	/**
	 * Register the REST API endpoints
	 *
	 * @uses "rest_api_init" action
	 *
	 * @return void
	 */
	public function rest_api_init() {
		// Routes that proxy content machine api
		( new Proxy( $this ) )->registerRoutes();
		// Routes for settings
		( new SettingsEndpoint( $this ) )->registerRoutes();
	}
}
