<?php

use ImaginaryMachines\UfoAi\Admin;
use ImaginaryMachines\UfoAi\UfoAi;
use ImaginaryMachines\UfoAi\Settings;

class SetupTest extends WP_UnitTestCase {
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
		UfoAi::addHooks();

		$this->assertGreaterThan(
			0,
			has_action(
				'plugins_loaded',
				[UfoAi::class,'load_textdomain']
			)
		);

		$this->assertGreaterThan(
			0,
			has_action(
				'rest_api_init',
				[UfoAi::class,'rest_api_init']
			)
		);

	}

	/**
	 * Test adding admin hooks.
	 */
	public function test_add_admin_hooks() {
		$this->assertGreaterThan(
			0,
			has_action(
				'admin_init',
				[Settings::class,'registerSettings']
			)
		);

	}
}
