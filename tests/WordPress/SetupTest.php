<?php

use ImaginaryMachines\ContentMachine\Admin;
use ImaginaryMachines\ContentMachine\ContentMachine;
use ImaginaryMachines\ContentMachine\Settings;

class SetupTest extends WP_UnitTestCase {
	/**
	 * Check that the TWO_FACTOR_DIR constant is defined.
	 */
	public function test_constant_defined() {

		$this->assertTrue( defined( 'CONTENT_MACHINE_PLUGIN_DIR' ) );

	}

	/**
	 * Check that the files were included.
	 */
	public function test_classes_exist() {

		$this->assertTrue( class_exists( \ImaginaryMachines\ContentMachine\ContentMachine::class ) );
	}


	/**
	 * Verify adding hooks.
	 *
	 */
	public function test_add_hooks() {
		ContentMachine::addHooks();

		$this->assertGreaterThan(
			0,
			has_action(
				'plugins_loaded',
				[ContentMachine::class,'load_textdomain']
			)
		);

		$this->assertGreaterThan(
			0,
			has_action(
				'rest_api_init',
				[ContentMachine::class,'rest_api_init']
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
		//Test this worked
		//add_action( 'init', 'content_machine_plugin_content_machine_plugin_block_init' );

		$this->assertGreaterThan(
			0,
			has_action(
				'init',
				'content_machine_plugin_content_machine_plugin_block_init'
			)
		);

	}
}
