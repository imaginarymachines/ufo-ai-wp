<?php
/**
 * Plugin Name:       Upcylced Found Objects
 * Description:       Generates words.
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Version:           0.3.1
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
define( 'UFO_AI_WPVERSION', '0.3.1' );

/**
 * Main file of plugin
 */
define( 'UFO_AI_WPMAIN_FILE', __FILE__ );


 // include autoloader from composer
require_once __DIR__ . '/vendor/autoload.php';
\ImaginaryMachines\UfoAi\UfoAi::addHooks();

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function content_machine_plugin_content_machine_plugin_block_init() {
	register_block_type( __DIR__ . '/build' );
}
//add_action( 'init', 'content_machine_plugin_content_machine_plugin_block_init' );

// Register script built in build/admin.js

add_action(
	'admin_enqueue_scripts',
	function() {
		$dependencies = array();
		// Use asset file if it exists
		if ( file_exists( __DIR__ . '/build/settings.asset.php' ) ) {
			$asset_file   = include __DIR__ . '/build/settings.asset.php';
			$dependencies = $asset_file['dependencies'];
		}
		wp_register_script(
			SettingsPage::SCREEN,
			plugins_url( 'build/settings.js', __FILE__ ),
			$dependencies,
			UFO_AI_WPVERSION,
		);
	}
);
//Load assets for editor
add_action(
	'enqueue_block_editor_assets',
	function() {
		$dependencies = array();
		// Use asset file if it exists
		if ( file_exists( __DIR__ . '/build/editor.asset.php' ) ) {
			$asset_file   = include __DIR__ . '/build/editor.asset.php';
			$dependencies = $asset_file['dependencies'];
		}
		wp_register_script(
			'ufo-ai-wp-editor',
			plugins_url( 'build/editor.js', __FILE__ ),
			$dependencies,
			UFO_AI_WPVERSION,
		);
		wp_enqueue_script( 'ufo-ai-wp-editor' );
	}
);
