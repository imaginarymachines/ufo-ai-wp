<?php

namespace ImaginaryMachines\ContentMachine;

use ImaginaryMachines\ContentMachine\Api\Proxy;
use ImaginaryMachines\ContentMachine\Api\SettingsEndpoint;
use ImaginaryMachines\ContentMachine\Contracts\ClientContract;


class ContentMachine {

	/**
	 * @var array
	 */
	protected static $container = array();

	/**
	 * Set up filters and actions.
	 *
	 * @since 0.1-dev
	 */
	public static function addHooks() {
		add_action( 'plugins_loaded', array( __CLASS__, 'load_textdomain' ) );
		add_action( 'rest_api_init', array( __CLASS__, 'rest_api_init' ) );
		add_action( 'admin_init', array( Settings::class, 'registerSettings' ) );
		add_action( 'admin_menu', array( SettingsPage::class, 'add_page' ) );
	}

	/**
	 * Loads the plugin's text domain.
	 *
	 * Sites on WordPress 4.6+ benefit from just-in-time loading of translations.
	 */
	public static function load_textdomain() {
		load_plugin_textdomain( 'content-machine' );
	}

	/**
	 * Register the REST API endpoints
	 *
	 * @return void
	 */
	public static function rest_api_init() {
		// .
		// Routes that proxy content machine api
		Proxy::factory();
		// Routes for settings
		SettingsEndpoint::factory();
	}

	public static function getClient(): ClientContract {
		if ( ! isset( self::$container['client'] ) ) {
			// self::$container['client'] = Client::fromSettings();

			self::$container['client'] = new Client(
				CONTENT_MACHINE_URL,
				CONTENT_MACHINE_API_KEY,
			);
		}
		return self::$container['client'];
	}

	public static function setClient( ClientContract $client ) {
		self::$container['client'] = $client;
	}
}
