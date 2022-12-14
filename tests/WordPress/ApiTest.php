<?php

use ImaginaryMachines\ContentMachine\Client;
use ImaginaryMachines\ContentMachine\ContentMachine;
use ImaginaryMachines\ContentMachine\Endpoints\Prompt;
use ImaginaryMachines\ContentMachine\PromptRequest;

class ApiTest extends WP_UnitTestCase {


	/**
	 * Test that we can use the client to make a prompt request
	 */
	public function test_prompt_POST_endpoint_with_fake() {


		//@todo recreate this test
		$fakeClient = new FakeClient();
		$this->assertIsObject( $fakeClient );
	}


}
