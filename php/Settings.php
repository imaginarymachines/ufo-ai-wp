<?php

namespace ImaginaryMachines\UfoAi;

// PHP class that uses WordPress' options API to store and retrieve settings
class Settings {


	const URL          = 'url';
	const KEY          = 'key';
	const GROUP        = 'cm-settings';
	const API_SETTINGS = 'cm_api_settings';
	public function registerSettings() { 
		register_setting(
			static::GROUP,
			static::API_SETTINGS,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitizeSettings' ),
				'default'           => $this->getDefaults(),
			)
		);
	}

	// Delete all settings
	public function deleteAll() {
		delete_option( self::API_SETTINGS );
	}
	// static method to get default settings
	public function getDefaults() {
		 return array(
			 self::URL => 'https://upcycledfoundobjects.com/',
			 self::KEY => '',
		 );
	}

	// static method to get default
	public function getDefault( string $key ) {
		return $this->getDefaults()[ $key ];
	}

	/**
	 * Sanitize API Key
	 *
	 * @param mixed $value
	 * @return string
	 */
	public function sanitizeSettingKey( $value ) {

		if ( empty( $value ) ) {
			if ( isset( $_POST['content_machine_api_key'] ) && is_string( $_POST['content_machine_api_key'] ) ) {
				return sanitize_text_field(
					$_POST['content_machine_api_key']
				);
			}
		}
		if ( ! is_string( $value ) ) {
			return '';
		}
		return sanitize_text_field( $value );
	}

	public function sanitizeSettingUrl( $value ) {
		if ( ! is_string( $value ) ) {
			return '';
		}
		return esc_url_raw( $value );
	}

	// Is this an allowed key?
	public function isAllowedKey( $key ) {
		return in_array( $key, array_keys( $this->getDefaults() ) );
	}
	// get a setting
	public function get( $key ) {
		$defaults = $this->getDefaults();
		// throw if not allowed key
		if ( ! $this->isAllowedKey( $key ) ) {
			throw new \Exception( sprintf( 'Invalid key %s', $key ) );
		}
		$settings = get_option( static::API_SETTINGS, array() );
		// return if in array
		if ( is_array( $settings ) && array_key_exists( $key, $settings ) ) {
			$setting = $settings[ $key ];
		} else {
			// return default
			$setting = $defaults[ $key ];
		}

		/**
		 * Filter the setting
		 *
		 * @param mixed $setting Setting value
		 * @param string $key Setting key
		 * @param array $settings All settings
		 *
		 * @return mixed
		 * @since 1.0.0
		 */
		return apply_filters( 'ufoaiwp_get_setting', $setting, $key, $settings );
	}
	// set a setting
	public function set( $key, $value ) {
		$current = $this->getAll();
		if ( ! $this->isAllowedKey( $key ) ) {
			throw new \Exception(
				sprintf( 'Invalid key %s', $key )
			);
		}
		// Sanitize the value
		$fnName = 'sanitizeSetting' . ucfirst( $key );
		$value  = $this->$fnName( $value );

		update_option(
			self::API_SETTINGS,
			array_merge(
				$current,
				array(
					$key => $value,
				)
			)
		);
	}

	// get all settings
	public function getAll() {
		$defaults = $this->getDefaults();
		$settings = array();
		foreach ( $defaults as $key => $default ) {
			$settings[ $key ] = $this->get( $key );
		}
		return $settings;
	}
}
