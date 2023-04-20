<?php

namespace ImaginaryMachines\UfoAi\Actions;

use ImaginaryMachines\UfoAi\Client;
use ImaginaryMachines\UfoAi\Settings;

class CheckConnection {

	protected Client $client;
	protected Settings $settings;

	public function __construct(
		Client $client,
		Settings $settings
	) {
		$this->client   = $client;
		$this->settings = $settings;
	}

	/**
	 * Check if client is connected with a valid API key
	 */
	public function handle() {
		$key = $this->settings
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

}
