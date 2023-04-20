<?php
namespace ImaginaryMachines\UfoAi\Tests;

use ImaginaryMachines\UfoAi\Actions\CheckConnection;
use ImaginaryMachines\UfoAi\Client;
use ImaginaryMachines\UfoAi\Settings;

class CheckConnectionTest extends TestCase {


	/**
	 * Test action returns an array with true, when connected.
	 *
	 * @group actions
	 */
	public function testCheckConnection(){
		$client = new class($this->makePlugin()) extends Client {
			public function isConnected(): bool {
				return true;
			}
		};
		$settings = new class extends Settings {
			public function get(  $key ){
				return 'test';
			}
		};
		$checkConnection = new CheckConnection( $client, $settings );
		$this->assertEquals( array( 'connected' => true ), $checkConnection->handle() );
	}

	/**
	 * Test returns array with false, when connection test fails
	 *
	 * @group actions
	 */
	public function testCheckConnectionReturnsFalse(){
		$client = new class($this->makePlugin()) extends Client {
			public function isConnected(): bool {
				return false;
			}
		};
		$settings = new class extends Settings {
			public function get(  $key ){
				return 'test';
			}
		};
		$checkConnection = new CheckConnection( $client, $settings );
		$this->assertEquals( array( 'connected' => false ), $checkConnection->handle() );
	}

	/**
	 * Test returns WP_Error when no key is set.
	 *
	 * @group actions
	 */
	public function testCheckConnectionRequiresKey(){
		$client = new class($this->makePlugin()) extends Client {
			public function isConnected(): bool {
				return true;
			}
		};
		$settings = new class extends Settings {
			public function get(  $key ){
				return '';
			}
		};
		$checkConnection = new CheckConnection( $client, $settings );
		$this->assertTrue( \is_wp_error(
			$checkConnection->handle()
		));
	}

}
