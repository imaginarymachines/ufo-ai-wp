<?php
/**
 * Plugin Name:       Upcylced Found Objects
 * Description:       Uses a large lanaguage model to help you write your posts, for your post, based on the post you’re working on.
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Version:           0.5.0
 * Author:            Imaginary Machines
 * Plugin URI:        https://upcycledfoundobjects.com/
 * Author URI:        https://upcycledfoundobjects.com/docs/install
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ufo-ai-wp
 *
 * @package           ufo-ai-wp
 */

use ImaginaryMachines\UfoAi\SettingsPage;

if ( file_exists( __DIR__ . '/keys.php' ) ) {
	require_once __DIR__ . '/keys.php';
}

/**
 * Shortcut constant to the path of this file.
 */
define( 'UFO_AI_WPPLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Version of the plugin.
 */
define( 'UFO_AI_WPVERSION', '0.5.0' );

/**
 * Main file of plugin
 */
define( 'UFO_AI_WPMAIN_FILE', __FILE__ );


 // include autoloader from composer
require_once __DIR__ . '/vendor/autoload.php';
\ImaginaryMachines\UfoAi\UfoAi::addHooks();


add_action(
	'init',
	function() {
		register_block_type( __DIR__ . '/build/block' );
	}
);

// Register script built in build/admin.js
add_action(
	'admin_enqueue_scripts',
	function() {
		$dependencies = array();
		$version      = UFO_AI_WPVERSION;

		// Use asset file if it exists
		if ( file_exists( __DIR__ . '/build/settings.asset.php' ) ) {
			$asset_file   = include __DIR__ . '/build/settings.asset.php';
			$dependencies = $asset_file['dependencies'];
			$version      = $asset_file['version'];

		}
		wp_register_script(
			SettingsPage::SCREEN,
			plugins_url( 'build/settings.js', __FILE__ ),
			$dependencies,
			$version,
		);
	}
);
//Load assets for editor
add_action(
	'enqueue_block_editor_assets',
	function() {
		$dependencies = array();
		$version      = UFO_AI_WPVERSION;
		// Use asset file if it exists
		if ( file_exists( __DIR__ . '/build/editor.asset.php' ) ) {
			$asset_file   = include __DIR__ . '/build/editor.asset.php';
			$dependencies = $asset_file['dependencies'];
			$version      = $asset_file['version'];
		}
		wp_register_script(
			'ufo-ai-wp-editor',
			plugins_url( 'build/editor.js', __FILE__ ),
			$dependencies,
			$version,
		);
		wp_enqueue_script( 'ufo-ai-wp-editor' );
	}
);
