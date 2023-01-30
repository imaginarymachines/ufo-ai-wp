<?php

namespace ImaginaryMachines\UfoAi\Api;

use ImaginaryMachines\UfoAi\Settings;

/**
 * REST API endpoints for plugin settings
 */
class SettingsEndpoint extends Endpoint{


	public function registerRoutes()
	{
		\register_rest_route(
			$this->namespace,
			'/settings',
			[
				'methods'             => 'POST',
				'callback'            => [$this, 'updateSettings'],
				'permission_callback' => [$this, 'authorize'],
				'args'                => [
					'key' => [
						'required' => true,
						'type'     => 'string',
					],

				],
			]
		);
	}


	/**
	 * Update settings
	 */
	public function updateSettings( $request ) {
		$key = $request->get_param( 'key' );
		$this->plugin
			->getSettings()
			->set( Settings::KEY, $key );
		return $this
			->plugin
			->getSettings()
			->getAll();
	}

}
