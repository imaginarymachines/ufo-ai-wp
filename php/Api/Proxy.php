<?php

namespace ImaginaryMachines\ContentMachine\Api;

use ImaginaryMachines\ContentMachine\Client;
use ImaginaryMachines\ContentMachine\ContentMachine;
use ImaginaryMachines\ContentMachine\PromptRequest;

/**
 * REST API endpoints for proxying requests to the Content Machine API
 */
class Proxy {


	/**
	 * @var Client
	 */
	protected $client;

	const NAMESPACE = 'content-machine/v1';

	public static function factory() {
		$obj = new static( ContentMachine::getClient() );
		\register_rest_route(
			self::NAMESPACE,
			'/post',
			array(
				'methods'             => 'POST',
				'callback'            => array( $obj, 'forPost' ),
				'permission_callback' => '__return_true',
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
	 * Create a prompt from a post
	 */
	public function forPost( $request ) {
		$what = 'paragraphs';

		$post_id      = $request->get_param( 'post' );
		$categories   = $request->get_param( 'categories' );
		$tags         = $request->get_param( 'tags' );
		$title        = $request->get_param( 'title' );
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
			// @todo deal with this
			$that['about'] = '  ';
		}
		$client = ContentMachine::getClient();


		$promptRequest = new PromptRequest(
			$what,
			$for,
			$that,
			1
		);
		try {
			$texts = $client->prompt( $promptRequest );
			return array( 'texts' => $texts );
		} catch ( \Throwable $th ) {
			wp_die( $th->getMessage() );
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