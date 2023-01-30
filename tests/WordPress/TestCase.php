<?php
namespace ImaginaryMachines\UfoAi\Tests;
use ImaginaryMachines\UfoAi\Settings;
use ImaginaryMachines\UfoAi\UfoAi;

abstract class TestCase extends \WP_UnitTestCase {

	protected function makePlugin(){
		$settings = new Settings();
		return new UfoAi($settings);
	}
}
