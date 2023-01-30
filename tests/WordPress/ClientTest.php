<?php

use ImaginaryMachines\UfoAi\Client;
use ImaginaryMachines\UfoAi\UfoAi;
use ImaginaryMachines\UfoAi\Settings;
class ClientTest extends WP_UnitTestCase {

	//test create client from settings
	public function test_create_client_from_settings() {
		$settings = Settings::getDefaults();
		$client = Client::fromSettings($settings);
		$this->assertEquals(
			$settings[Settings::KEY],
			$client->getKey(),
		);
		$this->assertEquals(
			$settings[Settings::URL],
			$client->getUrl(),
		);
	}




	/**
	 * Test that we can use the client to make a prompt request
	 *
	 * @group realApi
	 * @group edits
	 */
	public function test_prompt_edit_real() {
		if( ! defined('UFO_AI_WPAPI_KEY')|| empty(UFO_AI_WPAPI_KEY) ){
			$this->markTestSkipped('No API key found');
		}
		$client = new Client(
			Settings::getDefault(Settings::URL),
			UFO_AI_WPAPI_KEY,
		);

		$input = 'There are fOre dogs';
		$instruction = 'Fix spelling';
		$response = $client->edit($input,$instruction);
		$this->assertIsArray($response);

		$this->assertCount(1,$response);
		$this->assertEquals('There are four dogs',
			substr($response[0],0, strlen('There are four dogs'))
		);

	}

	/**
	 * @return Client
	 */
	protected function getRealClient(){
		return new Client(
			Settings::getDefault(Settings::URL),
			UFO_AI_WPAPI_KEY,
		);
	}

	/**
	 * Test isConnected
	 *
	 * @group realApi
	 * @group now
	 */
	public function test_is_connected_real() {
		if( ! defined('UFO_AI_WPAPI_KEY')|| empty(UFO_AI_WPAPI_KEY) ){
			$this->markTestSkipped('No API key found');
		}
		$client = $this->getRealClient();
		$this->assertTrue($client->isConnected());

	}

}
