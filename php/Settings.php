<?php

namespace ImaginaryMachines\UfoAi;

// PHP class that uses WordPress' options API to store and retrieve settings
class Settings {

	const URL          = 'url';
	const KEY          = 'key';
	const GROUP        = 'cm-settings';
	const API_SETTINGS = 'cm_api_settings';
	public static function registerSettings() {

		register_setting(
			static::GROUP,
			static::API_SETTINGS,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitizeSettings' ),
				'default'           => self::getDefaults(),
			)
		);

	}

	// Delete all settings
	public static function deleteAll() {
		delete_option( self::API_SETTINGS );
	}
	// static method to get default settings
	public static function getDefaults() {
		return array(
			self::URL => 'https://cma-pclz7.ondigitalocean.app/',
			self::KEY => '',
		);
	}

	// static method to get default
	public static function getDefault( string $key ) {
		return static::getDefaults()[ $key ];
	}

	/**
	 * Sanitize API Key
	 *
	 * @param mixed $value
	 * @return string
	 */
	public static function sanitizeSettingKey( $value ) {

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

	public static function sanitizeSettingUrl( $value ) {
		if ( ! is_string( $value ) ) {
			return '';
		}
		return esc_url_raw( $value );
	}

	// Is this an allowed key?
	public static function isAllowedKey( $key ) {
		return in_array( $key, array_keys( self::getDefaults() ) );
	}
	// get a setting
	public static function get( $key ) {
		$defaults = self::getDefaults();
		// throw if not allowed key
		if ( ! self::isAllowedKey( $key ) ) {
			throw new \Exception( sprintf( 'Invalid key %s', $key ) );
		}
		$settings = get_option( self::API_SETTINGS, array() );
		// return if in array
		if ( is_array( $settings ) && array_key_exists( $key, $settings ) ) {
			return $settings[ $key ];
		}
		// return default
		$setting = $defaults[ $key ];
		return $setting;
	}
	// set a setting
	public static function set( $key, $value ) {
		$current = static::getAll();
		if ( ! static::isAllowedKey( $key ) ) {
			throw new \Exception(
				sprintf( 'Invalid key %s', $key )
			);
		}
		// Sanitize the value
		$fnName = 'sanitizeSetting' . ucfirst( $key );
		$value  = static::$fnName( $value );

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
	public static function getAll() {
		$defaults = self::getDefaults();
		$settings = array();
		foreach ( $defaults as $key => $default ) {
			$settings[ $key ] = self::get( $key );
		}
		return $settings;
	}


}
