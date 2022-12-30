<?php

use ImaginaryMachines\UfoAi\UfoAi;
use ImaginaryMachines\UfoAi\Settings;
class SettingsTest extends WP_UnitTestCase {
	//Test get and save a setting that is allowed
	public function test_get_save_allowed_setting() {
		$setting = Settings::KEY;
		$value = 'value';
		Settings::set($setting, $value);
		$this->assertEquals(
			$value,
			Settings::get($setting),
		);
	}

	//Test throws for invalid key
	public function test_throws_for_get_invalid_key() {
		$this->expectException(\Exception::class);
		Settings::get('invalid_key');
	}

	//Test get all settings
	public function test_get_all_settings() {
		Settings::deleteAll();
		$settings = Settings::getAll();
		$this->assertEquals(
			Settings::getDefaults(),
			$settings,
		);
	}
}
