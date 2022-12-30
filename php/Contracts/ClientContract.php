<?php

namespace ImaginaryMachines\UfoAi\Contracts;

use ImaginaryMachines\UfoAi\PromptRequest;

/**
 * Defines the interface for the API client
 */
interface ClientContract {
	/**
	 * Get api key
	 *
	 * @return string
	 */
	public function getKey(): string;

	/**
	 * Get api url
	 *
	 * @return string
	 */
	public function getUrl(): string;

	/**
	 * Make prompt request
	 *
	 * @param PromptRequest $promptRequest
	 * @return array
	 */
	public function prompt( PromptRequest $promptRequest ):array;
}
