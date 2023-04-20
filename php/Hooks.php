<?php

namespace ImaginaryMachines\UfoAi;

class Hooks {

	/**
	 * @var UfoAi
	 */
	protected $plugin;

	/**
	 * @var SettingsPage
	 */
	protected $settingsPage;


	public function __construct( UfoAi $plugin ) {
		$this->plugin       = $plugin;
		$this->settingsPage = new SettingsPage( $plugin );
	}

	/**
	 * Register all hooks
	 */
	public function addHooks() {
		add_action(
			'admin_init',
			array( $this->plugin->getSettings(), 'registerSettings' )
		);
		add_action( 'plugins_loaded', array( $this->plugin, 'load_textdomain' ) );
		add_action( 'rest_api_init', array( $this->plugin, 'rest_api_init' ) );
		add_action( 'admin_menu', array( $this->settingsPage, 'addPage' ) );

	}

	/**
	 * Remove Hooks
	 */
	public function removeHooks() {
		remove_action(
			'admin_init',
			array( $this->plugin->getSettings(), 'registerSettings' )
		);
		remove_action( 'plugins_loaded', array( $this->plugin, 'load_textdomain' ) );
		remove_action( 'rest_api_init', array( $this->plugin, 'rest_api_init' ) );
		remove_action( 'admin_menu', array( $this->settingsPage, 'addPage' ) );
	}

}
