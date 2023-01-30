<?php
namespace ImaginaryMachines\UfoAi;

/**
 * Remote updater for plugin
 */
class Updater {

	/**
	 * Slug with full file path
	 *
	 * @string
	 */
	public $pluginSlug;

	/**
	 * Plugin file
	 *
	 * @string
	 */
	public $pluginFile;

	/**
	 * Current version
	 *
	 * @string
	 */
	public $version;

	/**
	 * Tranisent name
	 *
	 * @string
	 */
	public $cacheKey;

	/**
	 * API Key
	 * @string
	 */
	public $apiKey;

	/**
	 * API URL
	 * @string
	 */
	public $apiUrl;

	/**
	 * Is caching enbled
	 *
	 * @bool
	 */
	public $cacheAllowed;

	public function __construct( $version, $pluginSlug, $pluginFile, $apiKey, $apiUrl ) {

		$this->pluginSlug   = $pluginSlug;
		$this->version      = $version;
		$this->pluginFile   = $pluginFile;
		$this->apiKey       = $apiKey;
		$this->apiUrl       = $apiUrl;
		$this->cacheKey     = $pluginSlug . '_update-check';
		$this->cacheAllowed = false;
	}

	/**
	 * Add all the hooks and return instance
	 *
	 * Use instance to remove hooks later if needed
	 *
	 * @return static
	 */
	public static function addHooks( $instance ) {
		add_filter( 'plugins_api', array( $instance, 'info' ), 20, 3 );
		add_filter( 'site_transient_update_plugins', array( $instance, 'update' ) );
		add_action( 'upgrader_process_complete', array( $instance, 'purge' ), 10, 2 );
		return $instance;
	}

	/**
	 * Remove all the hooks
	 *
	 * @return static
	 */
	public static function removeHooks( $instance ) {
		remove_filter( 'plugins_api', array( $instance, 'info' ), 20, 3 );
		remove_filter( 'site_transient_update_plugins', array( $instance, 'update' ) );
		remove_action( 'upgrader_process_complete', array( $instance, 'purge' ), 10, 2 );
		return $instance;
	}

	public static function factory() {
		$key = Settings::get( Settings::KEY );
		if ( $key ) {
			//create updater
			$updater = new Updater(
				UFO_AI_WPVERSION,
				'ufo-ai-wp',
				UFO_AI_WPMAIN_FILE,
				$key,
				Settings::getDefault( Settings::URL )
			);
			return $updater;

		}
		return false;
	}

	/**
	 * Get plugin info from updater
	 */
	public function request() {

		$remote = get_transient( $this->cacheKey );
		if ( false === $remote || ! $this->cacheAllowed ) {

			$url    = $this->apiUrl . 'api/v1/plugin/info';
			$remote = wp_remote_get(
				$url,
				array(
					'timeout' => 10,
					'headers' => array(
						'Accept'        => 'application/json',
						// api key as bearer token
						'Authorization' => 'Bearer ' . $this->apiKey,
						'verify'        => false,
					),
				)
			);



			if (
				is_wp_error( $remote )
				|| 200 !== wp_remote_retrieve_response_code( $remote )
				|| empty( wp_remote_retrieve_body( $remote ) )
			) {
				return false;
			}

			set_transient( $this->cacheKey, $remote, DAY_IN_SECONDS );

		}

		$remote = json_decode( wp_remote_retrieve_body( $remote ) );

		return $remote;

	}


	/**
	 * Provide data to WordPress Plugins API
	 *
	 * @uses "plugins_api" filter
	 */
	public function info( $res, $action, $args ) {

		// do nothing if you're not getting plugin information right now
		if ( 'plugin_information' !== $action ) {
			return false;
		}

		// do nothing if it is not our plugin
		if ( $this->pluginSlug !== $args->slug ) {
			return false;
		}


		// get updates
		$remote = $this->request();

		if ( ! $remote ) {
			return false;
		}

		$res = new \stdClass();

		$res->name           = $remote->name;
		$res->slug           = $remote->slug;
		$res->version        = $remote->version;
		$res->tested         = $remote->tested;
		$res->requires       = $remote->requires;
		$res->author         = $remote->author;
		$res->author_profile = $remote->author_profile;
		$res->download_link  = $remote->download_url;
		$res->trunk          = $remote->download_url;
		$res->requires_php   = $remote->requires_php;
		$res->last_updated   = $remote->last_updated;

		$res->sections = array(
			'description'  => $remote->sections->description,
			'installation' => $remote->sections->installation,
			'changelog'    => $remote->sections->changelog,
		);

		if ( ! empty( $remote->banners ) ) {
			$res->banners = array(
				'low'  => $remote->banners->low,
				'high' => $remote->banners->high,
			);
		}

		return $res;

	}

	/**
	 * Handle update of plugin
	 *
	 *  @uses "site_transient_update_plugins" filter
	 */
	public function update( $transient ) {

		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$remote = $this->request();

		if (
			$remote
			&& version_compare( $this->version, $remote->version, '<' )
			&& version_compare( $remote->requires, get_bloginfo( 'version' ), '<' )
			&& version_compare( $remote->requires_php, PHP_VERSION, '<' )
		) {
			$res              = new \stdClass();
			$res->slug        = $this->pluginSlug;
			$res->plugin      = $this->pluginFile;
			$res->new_version = $remote->version;
			$res->tested      = $remote->tested;
			$res->package     = $remote->download_url;

			$transient->response[ $res->plugin ] = $res;

		}

		return $transient;

	}

	/**
	 * @uses "upgrader_process_complete" filter
	 */
	public function upgraderProcessComplete( $options ) {

		if (
			$this->cacheAllowed
			&& 'update' === $options['action']
			&& 'plugin' === $options['type']
		) {
			// just clean the cache when new plugin version is installed
			delete_transient( $this->cacheKey );
		}

	}


}
