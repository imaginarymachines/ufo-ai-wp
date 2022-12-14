<?php

namespace ImaginaryMachines\ContentMachine;

use ImaginaryMachines\ContentMachine\Contracts\ClientContract;

/**
 * Interfact with API
 */
class Client  implements ClientContract {

	// url for the api
	protected string $url;
	// api key
	protected string $key;
	// api version
	protected string $version;

	const ROUTE_PROMPT  = '/from-prompt';
	const METHOD_PROMPT = 'POST';
	/**
	 * Constructor
	 *
	 * @param string $url
	 * @param string $key
	 * @param string $version
	 */
	public function __construct( string $url, string $key, string $version = 'v1' ) {
		$this->url     = $url;
		$this->key     = $key;
		$this->version = $version;
	}

	public static function latestApiVersion():string {
		return 'v1';
	}

	// Create from saved settings
	public static function fromSettings(): Client {
		return new Client(
			Settings::get( Settings::URL, ),
			Settings::get( Settings::KEY ),
			self::latestApiVersion()
		);
	}

	// Get api key
	public function getKey(): string {
		return $this->key;
	}

	// Get api url
	public function getUrl(): string {
		return $this->url;
	}

	// Make prompt request
	public function prompt( PromptRequest $promptRequest ):array {

		$response = wp_remote_post(
			$this->makeUrl( self::ROUTE_PROMPT ),
			array(
				'method'  => self::METHOD_PROMPT,
				'timeout' => 15,
				'body'    => json_encode( $promptRequest->toArray() ),
				'headers' => array(
					// content type json
					'Content-Type'  => 'application/json; charset=utf-8',
					// api key as bearer token
					'Authorization' => 'Bearer ' . $this->key,
					'Accept'        => 'application/json',
				),
			)
		);
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
			throw new \Exception( 'Invalid content-machine response body' );
		}
		// Ensure each key of "texts" is a string
		foreach ( $body['texts'] as $key => $text ) {
			if ( ! is_string( $text ) ) {
				throw new \Exception(
					sprintf( 'Invalid content-machine response body, key of texts %s is not a string', $key )
				);

			}
		}
		// return the texts
		return $body['texts'];
	}

	protected function makeUrl( string $endpoint ):string {
		return $this->url . 'api/' . $this->version . $endpoint;
	}

}
