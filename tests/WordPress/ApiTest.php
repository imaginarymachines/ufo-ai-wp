<?php

use ImaginaryMachines\UfoAi\Client;
use ImaginaryMachines\UfoAi\UfoAi;
use ImaginaryMachines\UfoAi\Endpoints\Prompt;
use ImaginaryMachines\UfoAi\PromptRequest;

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
