<?php

use ImaginaryMachines\UfoAi\Client;
use ImaginaryMachines\UfoAi\UfoAi;
use ImaginaryMachines\UfoAi\PromptRequest;
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
	 * @group now
	 */
	public function test_prompt_request_real() {
		if( ! defined('UFO_AI_WPAPI_KEY')|| empty(UFO_AI_WPAPI_KEY) ){
			$this->markTestSkipped('No API key found');
		}
		$client = new Client(
			Settings::getDefault(Settings::URL),
			UFO_AI_WPAPI_KEY,
		);
		$prompt = new PromptRequest(
			'words',
			[
				'type' => 'blog post',
				'title' => 'Spatuals of the Future'
			],
			[
				'about' => 'Space travelers'
			],
			1
		);
		$response = $client->prompt($prompt);
		$this->assertIsArray($response);

		$this->assertCount(1,$response);
		//set length to 2
		$prompt->setN(2);
		$response = $client->prompt($prompt);
		$this->assertIsArray($response);
		$this->assertCount(2, $response);

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

}
