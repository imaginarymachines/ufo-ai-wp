<?php
namespace ImaginaryMachines\UfoAi;

class SettingsPage {

	const SCREEN = 'ufo-ai-settings';
	/**
	 * Adds the settings page to the Settings menu.
	 *
	 * @since 0.0.1
	 */
	public static function add_page() {

		// Add the page
		$hook_suffix = add_options_page(
			__( 'Upcycled Found Objects', 'ufo-ai-wp' ),
			__( 'Upcycled Found Objects', 'ufo-ai-wp' ),
			'manage_options',
			self::SCREEN,
			array( __CLASS__, 'render_page' )
		);

		// This adds a link in the plugins list table
		add_action(
			'plugin_action_links_' . plugin_basename( UFO_AI_WPMAIN_FILE ),
			array(
				__CLASS__,
				'plugin_action_links_add_settings',
			)
		);

		return $hook_suffix;
	}

	/**
	 * Adds a link to the setting page to the plugin's entry in the plugins list table.
	 *
	 * @since 1.0.0
	 *
	 * @param array $links List of plugin action links HTML.
	 * @return array Modified list of plugin action links HTML.
	 */
	public static function plugin_action_links_add_settings( $links ) {
		// Add link as the first plugin action link.
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( add_query_arg( 'page', self::SCREEN, admin_url( 'options-general.php' ) ) ),
			esc_html__( 'Settings', 'ufo-ai-wp' )
		);
		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Renders the settings page.
	 *
	 * @since 0.0.1
	 */
	public static function render_page() {
		wp_enqueue_script( self::SCREEN );
		$settings = Settings::getAll();
		wp_localize_script(
			self::SCREEN,
			'CONTENT_MACHINE',
			array(
				'apiUrl'   => rest_url( 'ufo-ai/v1/settings' ),
				'settings' => $settings,
			)
		);

		?>
			<div class="ufo-ai-wp-wrap">
				<h1>
					<?php esc_html_e( 'Upcycled Found Objects', 'ufo-ai-wp' ); ?>
				</h1>
				<div id="ufo-ai-settings"></div>
			</div>
		<?php
	}

}
