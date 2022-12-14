<?php

namespace ImaginaryMachines\ContentMachine;

// PHP class that uses WordPress' options API to store and retrieve settings
class Settings {

	const URL   = 'url';
	const KEY   = 'key';
	const GROUP = 'cmwp-settings';

	public static function registerSettings() {

		register_setting(
			static::GROUP,
			static::settingName( static::KEY ),
			array(
				'type'              => 'string',
				'sanitize_callback' => array( __CLASS__, 'sanitizeSettingKey' ),
				'default'           => self::getDefault( static::KEY ),
			)
		);
		register_setting(
			static::GROUP,
			static::settingName( static::URL ),
			array(
				'type'              => 'string',
				'sanitize_callback' => array( __CLASS__, 'sanitizeSettingUrl' ),
				'default'           => self::getDefault( static::URL ),
			)
		);
	}

	// Delete all settings
	public static function deleteAll() {
		foreach ( self::getDefaults() as $key => $value ) {
			delete_option( static::settingName( $key ) );
		}
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

	public static function sanitizeSettingKey( $value ) {

		if ( empty( $value ) ) {
			$value = $_POST['content_machine_api_key'];
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
			throw new \Exception(
				sprintf( 'Invalid key %s', $key )
			);
		}
		$setting = get_option( static::settingName( $key ), null );
		if ( ! $setting ) {
			$setting = $defaults[ $key ];
		}
		return $setting;
	}
	// set a setting
	public static function set( $key, $value ) {
		update_option( static::settingName( $key ), $value );
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

	public static function settingName( $key ) {
		return 'content_machine_' . $key;
	}
}
