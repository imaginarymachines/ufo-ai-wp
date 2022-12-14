<?php

use ImaginaryMachines\ContentMachine\Client;
use ImaginaryMachines\ContentMachine\ContentMachine;
use ImaginaryMachines\ContentMachine\PromptRequest;
use ImaginaryMachines\ContentMachine\Settings;
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
	 */
	public function test_prompt_request_real() {
		$this->markTestSkipped('Real API test');
		$client = ContentMachine::getClient();
		$prompt = new PromptRequest(
			'words',
			[
				'type' => 'blog post',
				'title' => 'Spatuals of the Future'
			],
			[
				'about' => 'Space travelers'
			],
			5
		);
		$response = $client->prompt($prompt);
		$this->assertIsArray($response);
	}

}
