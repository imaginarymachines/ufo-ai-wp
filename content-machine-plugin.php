<?php
/**
 * Plugin Name:       Generate Words
 * Description:       Example block scaffolded with Create Block tool.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       content-machine-plugin
 *
 * @package           content-machine-plugin
 */
if ( file_exists( __DIR__ . '/keys.php' ) ) {
	require_once __DIR__ . '/keys.php';
}

/**
 * Shortcut constant to the path of this file.
 */
define( 'CONTENT_MACHINE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Version of the plugin.
 */
define( 'CONTENT_MACHINE_VERSION', '0.1.0' );

/**
 * Main file of plugin
 */
define( 'CONTENT_MACHINE_MAIN_FILE', __FILE__ );


 // include autoloader from composer
require_once __DIR__ . '/vendor/autoload.php';
\ImaginaryMachines\ContentMachine\ContentMachine::addHooks();

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
add_action( 'init', 'content_machine_plugin_content_machine_plugin_block_init' );
