<?php

use ImaginaryMachines\ContentMachine\Contracts\ClientContract;
use ImaginaryMachines\ContentMachine\PromptRequest;

/**
 * Fake client for testing
 */
class FakeClient implements ClientContract {

	public array $nextData = array();
	public function prompt( PromptRequest $promptRequest ):array {
		return $this->nextData;
	}


	public function getKey(): string {
		return 'fake';
	}

	public function getUrl(): string {
		return 'fake';
	}


}
