<?php
namespace ImaginaryMachines\ContentMachine;

class SettingsPage {

	const SCREEN = 'content-machine-settings';
	/**
	 * Adds the settings page to the Settings menu.
	 *
	 * @since 0.0.1
	 */
	public static function add_page() {

		// Add the page
		$hook_suffix = add_options_page(
			__( 'Content Machine', 'content-machine-plugin' ),
			__( 'Settings', 'content-machine-plugin' ),
			'manage_options',
			self::SCREEN,
			array( __CLASS__, 'render_page' )
		);

		// This adds a link in the plugins list table
		add_action(
			'plugin_action_links_' . plugin_basename( CONTENT_MACHINE_MAIN_FILE ),
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
			esc_html__( 'Settings', 'content-machine-plugin' )
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
		wp_enqueue_script(SettingsPage::SCREEN);
		$settings = Settings::getAll();
		wp_localize_script(SettingsPage::SCREEN,'CONTENT_MACHINE',[
			'apiUrl' => rest_url('content-machine/v1/settings'),
			'settings' => $settings,
		]);

		?>
			<div class="content-machine-plugin-wrap">
				<h1>
					<?php esc_html_e( 'Content Machine', 'content-machine-plugin' ); ?>
				</h1>
				<div id="content-machine-settings"></div>
			</div>
		<?php
	}

}
