<?php

namespace ImaginaryMachines\UfoAi\Api;

use ImaginaryMachines\UfoAi\Client;
use ImaginaryMachines\UfoAi\UfoAi;
use ImaginaryMachines\UfoAi\PromptRequest;
use ImaginaryMachines\UfoAi\Settings;

/**
 * REST API endpoints for proxying requests to the Upcycled Found Objects API
 */
class Proxy {


	/**
	 * Client instance
	 *
	 * @var Client
	 */
	protected $client;

	const NAMESPACE = 'ufo-ai/v1';

	/**
	 * Factory
	 *
	 * @return self
	 */
	public static function factory() {
		$obj = new static( UfoAi::getClient() );
		\register_rest_route(
			self::NAMESPACE,
			'/post',
			array(
				'methods'             => 'POST',
				'callback'            => array( $obj, 'forPost' ),
				'permission_callback' => array( $obj, 'authorize' ),
				'args'                => array(
					'title'      => array(
						'required' => true,
						'type'     => 'string',
					),
					'categories' => array(
						'required' => false,
						'type'     => 'array',
					),
					'tags'       => array(
						'required' => false,
						'type'     => 'array',
					),
					'post'       => array(
						'required' => true,
						'type'     => 'integer',
					),
					'length'     => array(
						'required' => false,
						'type'     => 'integer',
					),
					'what'       => array(
						'required' => false,
						'type'     => 'string',
						'default'  => PromptRequest::getDefaultWhat(),
					),
				),
			)
		);

		\register_rest_route(
			self::NAMESPACE,
			'/connected',
			array(
				'methods'             => 'GET',
				'callback'            => array( $obj, 'checkConnection' ),
				'permission_callback' => array( $obj, 'authorize' ),

			)
		);

		\register_rest_route(
			self::NAMESPACE,
			'/edit',
			array(
				'methods'             => 'POST',
				'callback'            => array( $obj, 'handleEdit' ),
				'permission_callback' => array( $obj, 'authorize' ),
				'args'                => array(
					'input'       => array(
						'required' => true,
						'type'     => 'string',
					),
					'instruction' => array(
						'required' => true,
						'type'     => 'string',
					),
				),

			)
		);
	}

	/**
	 * Constructor
	 *
	 * @param Client $client
	 */
	public function __construct( Client $client ) {
		$this->client = $client;
	}

	/**
	 * Check if account connected
	 */
	public function checkConnection() {
		$key = Settings::get( Settings::KEY );
		if ( empty( $key ) ) {
			return new \WP_Error(
				'no_api_key',
				'No Saved API Key',
			);
		}
		$connected = $this->client->isConnected();
		return array(
			'connected' => $connected,
		);
	}
	/**
	 * Create a prompt from a post
	 */
	public function forPost( $request ) {

		$what = $request->get_param( 'what' );
		//reset to default if invalid
		if ( ! in_array( $what, PromptRequest::getValidWhats() ) ) {
			$what = PromptRequest::getDefaultWhat();
		}

		$post_id      = $request->get_param( 'post' );
		$categories   = $request->get_param( 'categories' );
		$tags         = $request->get_param( 'tags' );
		$title        = $request->get_param( 'title' );
		$length       = (int) $request->get_param( 'length', 1 );
		$that         = array(
			'about' => '',
		);
		$for          = array(
			'title' => $title,
			'type'  => 'blog post',
		);
		$aboutPattern = ' %s,';

		if ( empty( $categories ) ) {
			$categories = \get_the_category( $post_id );
		} else {
			$categories = array();
			foreach ( $categories as $id ) {
				$categories[] = get_category( $id );
			}
		}
		if ( ! empty( $categories ) ) {
			foreach ( $categories as $category ) {
				if ( $category ) {
					$that['about'] .= sprintf(
						$aboutPattern,
						$category->name
					);
				}
			}
		}
		if ( empty( $tags ) ) {
			$tags = get_the_tags( $post_id );
		} else {
			$tags = array();
			foreach ( $tags as $id ) {
				$tags[] = get_tag( $id );
			}
		}
		if ( ! empty( $tags ) ) {
			foreach ( $tags as $tag ) {
				if ( $tag ) {
					$that['about'] .= sprintf(
						$aboutPattern,
						$tag->name
					);
				}
			}
		}
		if ( empty( $that['about'] ) ) {
			$that['about'] = get_the_excerpt( $post_id );
		}
		$that['about'] = trim( $that['about'], ',' );
		if ( empty( $that['about'] ) ) {
			$that['about'] = $title ? $title : 'Something';
		}

		$promptRequest = new PromptRequest(
			$what,
			$for,
			$that,
			$length
		);
		try {
			$texts = $this->client->prompt( $promptRequest );
			return array( 'texts' => $texts );
		} catch ( \Throwable $th ) {
			return new \WP_Error( 'ufo-ai-error', $th->getMessage() );
		}
	}

	/**
	 * Handle edit request
	 * @param \WP_REST_Request $request
	 * @return array
	 */
	public function handleEdit( $request ) {
		$input       = $request->get_param( 'input' );
		$instruction = $request->get_param( 'instruction' );
		wp_send_json_error([
			'input' => $input,
			'instruction' => $instruction,
		]
		);
		exit;
		try {
			$texts = $this->client->edit( $input, $instruction );
			return array( 'texts' => $texts );
		} catch ( \Throwable $th ) {
			return new \WP_Error( 'ufo-ai-error', $th->getMessage() );
		}
	}

	/**
	 * Default permission_callback
	 *
	 * @param \WP_REST_Request $request
	 * @return bool
	 */
	public function authorize( $request ) {
		$capability = is_multisite() ? 'delete_sites' : 'manage_options';
		return current_user_can( $capability );
	}


}
