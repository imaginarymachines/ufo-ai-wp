<?php

namespace ImaginaryMachines\UfoAi;

use ImaginaryMachines\UfoAi\Contracts\ClientContract;

/**
 * Interact with API
 */
class Client implements ClientContract {


	/**
	 * Url for the api
	 * @var string
	 */
	protected string $url;
	/**
	 * API key
	 * @var string
	 */
	protected string $key;
	/**
	 * API version
	 * @var string
	 */
	protected string $version;

	/**
	 * @var UfoAi
	 */
	protected UfoAi $plugin;

	/**
	 *
	 *
	 * @param string $url
	 * @param string $key
	 * @param string $version
	 */
	public function __construct( UfoAi $plugin ) {
		$this->plugin  = $plugin;
		$this->url     = $plugin->getSettings()->get( Settings::URL );
		$this->key     = $plugin->getSettings()->get( Settings::KEY );
		$this->version = self::latestApiVersion();
	}


	/**
	 * Check if client is connected with a valid API key
	 */
	public function isConnected(): bool {

		$response = wp_remote_request(
			$this->makeUrl( '/user', false ),
			array(
				'method'  => 'GET',
				'timeout' => 10,
				'headers' => $this->getHeaders(),

			)
		);
		if ( is_wp_error( $response ) ) {
			return false;
		}
		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			return false;
		}
		return true;
	}

	public function text( string $prompt, float $temperature = 0.8 ): array {

		$response = wp_remote_request(
			$this->makeUrl( '/text', true ),
			array(
				'method'  => 'POST',
				'timeout' => 15,
				'headers' => $this->getHeaders(),
				'body'    => json_encode(
					array(
						'prompt'      => $prompt,
						'temperature' => $temperature,
					)
				),

			)
		);
		if ( is_wp_error( $response ) ) {
			throw new \Exception(
				$response->get_error_message()
			);

			return $response;
		}
		if ( wp_remote_retrieve_response_code( $response ) !== 201 ) {
			return array();
		}
		return $this->handleResponse( $response );
	}

	/**
	 * Arguments for wp_remote_request
	 *
	 * @since 1.1.0
	 *
	 * @param array $args
	 * @param string $url
	 * @return array
	 */
	protected function requestArgs( array $args, string $url ) {
		$args = array_merge(
			array(
				'timeout' => 15,
				'headers' => $this->getHeaders(),

			),
			$args,
		);
		/**
		 * Change wp_remote_request API request arguments
		 *
		 * @since 1.1.0
		 * @param array $args Request arguments
		 * @param string $url Request url
		 * @return array
		 */
		return apply_filters( 'ufoaiwp_request_args', $args, $url );
	}


	public function edit( string $input, string $instruction ) {
		$data     = array(
			'input'       => $input,
			'instruction' => $instruction,
		);
		$response = wp_remote_request(
			$this->makeUrl( '/text/edit' ),
			array(
				'method'  => 'POST',
				'timeout' => 15,
				'body'    => json_encode( $data ),
				'headers' => $this->getHeaders(),
			)
		);
		return $this->handleResponse( $response );
	}


	protected function handleResponse( $response ) {
		// check if is_wp_error
		if ( is_wp_error( $response ) ) {
			throw new \Exception( $response->get_error_message() );
		}
		// Check if status is 201 with wp_remote_retrieve_response_code
		if ( wp_remote_retrieve_response_code( $response ) !== 201 ) {
			$message  = 'Response failed with ' . wp_remote_retrieve_response_code( $response );
			$message .= '. ' . wp_remote_retrieve_response_message( $response );
			throw new \Exception( $message );
		}
		// decode the body with json_decode
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		// check if body is array with key "texts"
		if ( ! is_array( $body ) || ! array_key_exists( 'texts', $body ) ) {
			throw new \Exception( 'Invalid ufo-ai response body' );
		}
		// Ensure each key of "texts" is a string
		foreach ( $body['texts'] as $key => $text ) {
			if ( ! is_string( $text ) ) {
				throw new \Exception(
					sprintf( 'Invalid ufo-ai response body, key of texts %s is not a string', $key )
				);
			}
		}
		// return the texts
		return $body['texts'];
	}

	/**
	 * Get latest api version
	 *
	 * @return string
	 */
	public static function latestApiVersion(): string {
		/**
		 * Filter the api version
		 *
		 * @param string $version Current version
		 * @return string
		 * @since 1.1.0
		 */
		return apply_filters(
			'ufoai_api_version',
			'v2',
		);
	}


	/**
	 * Get API key
	 *
	 * @return string
	 */
	public function getKey(): string {
		return $this->key;
	}

	/**
	 *  Get api url
	 *
	 * @return string
	 */
	public function getUrl(): string {
		return $this->url;
	}


	/**
	 * Make a url
	 *
	 * @param string $endpoint
	 * @param bool $withVersion
	 * @return string
	 */
	protected function makeUrl( string $endpoint, bool $withVersion = true ): string {
		//Ensure $endpoint starts with a slash
		if ( substr( $endpoint, 0, 1 ) !== '/' ) {
			$endpoint = '/' . $endpoint;
		}
		if ( $withVersion ) {
			$endpoint = 'api/' . $this->version . $endpoint;
		} else {
			$endpoint = 'api' . $endpoint;
		}

		/**
		 * Filter the url for API request
		 *
		 * @param string $url URL for request
		 * @param string $endpoint Endpoint for the request
		 * @param Client $client API client
		 * @return string
		 * @since 1.1.0
		 */
		return apply_filters(
			'ufoai_client_url',
			$this->url . $endpoint,
			$endpoint,
			$this,
		);
	}

	/**
	 * Array of headers for requests
	 *
	 * @return array
	 */
	protected function getHeaders(): array {
		return array(
			// content type json
			'Content-Type'  => 'application/json; charset=utf-8',
			'Accept'        => 'application/json',
			// api key as bearer token
			'Authorization' => 'Bearer ' . $this->key,
			'Accept'        => 'application/json',
		);
	}
}
