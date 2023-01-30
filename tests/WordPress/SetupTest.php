<?php
namespace ImaginaryMachines\UfoAi\Tests;

use ImaginaryMachines\UfoAi\Hooks;
use ImaginaryMachines\UfoAi\UfoAi;
use ImaginaryMachines\UfoAi\Settings;

class SetupTest extends TestCase {
	/**
	 * Check that the TWO_FACTOR_DIR constant is defined.
	 */
	public function test_constant_defined() {

		$this->assertTrue( defined( 'UFO_AI_WPPLUGIN_DIR' ) );
		$this->assertTrue( defined( 'UFO_AI_WPMAIN_FILE' ) );
		$this->assertTrue( defined( 'UFO_AI_WPVERSION' ) );
	}

	/**
	 * Check that the files were included.
	 */
	public function test_classes_exist() {

		$this->assertTrue( class_exists( \ImaginaryMachines\UfoAi\UfoAi::class ) );
	}


	/**
	 * Verify adding hooks.
	 *
	 */
	public function test_add_hooks() {
		$plugin = $this->makePlugin();
		$plugin->init();
		$this->assertGreaterThan(
			0,
			has_action(
				'plugins_loaded',
				[$plugin,'load_textdomain']
			)
		);

		$this->assertGreaterThan(
			0,
			has_action(
				'rest_api_init',
				[$plugin,'rest_api_init']
			)
		);

	}

	/**
	 * Test adding admin hooks.
	 */
	public function test_add_admin_hooks() {
		$plugin = $this->makePlugin();
		$plugin->init();

		$this->assertGreaterThan(
			0,
			has_action(
				'admin_init',
				[$plugin->getSettings(),'registerSettings']
			)
		);

	}
}
