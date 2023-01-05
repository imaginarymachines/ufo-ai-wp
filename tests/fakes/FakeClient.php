<?php

use ImaginaryMachines\UfoAi\Contracts\ClientContract;
use ImaginaryMachines\UfoAi\PromptRequest;

/**
 * Fake client for testing
 */
class FakeClient implements ClientContract {

	public array $nextData = array();

	public bool $isConnected = true;
	public function prompt( PromptRequest $promptRequest ):array {
		return $this->nextData;
	}

	public function isConnected(): bool {
		return $this->isConnected;
	}



	public function getKey(): string {
		return 'fake';
	}

	public function getUrl(): string {
		return 'fake';
	}


}
