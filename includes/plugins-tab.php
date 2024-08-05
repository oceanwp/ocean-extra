<?php
/**
 * OceanWP Plugins Tab
 *
 * @package OceanWP WordPress theme
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class OceanWP_Plugins_Tab
 *
 * Adds a custom tab to the plugin install screen to display OceanWP plugins.
 */
class OceanWP_Plugins_Tab {

	/**
	 * OceanWP_Plugins_Tab constructor.
	 *
	 * Hooks the methods to the appropriate actions and filters.
	 */
	public function __construct() {
		add_filter( 'install_plugins_tabs', array( $this, 'add_oceanwp_plugin_tab' ) );
		add_action( 'install_plugins_oceanwp_plugins_tab', array( $this, 'display_oceanwp_plugins_tab_content' ) );
	}

	/**
	 * Adds a custom tab to the plugin install screen.
	 *
	 * @param array $tabs The existing tabs.
	 * @return array The modified tabs.
	 */
	public function add_oceanwp_plugin_tab( $tabs ) {
		// Check if the current user has the capability to install plugins.
		if ( apply_filters( 'oceanwp_show_plugin_tab', current_user_can( 'install_plugins' ) ) ) {
			$tabs['oceanwp_plugins_tab'] = __( 'OceanWP Plugins', 'ocean-extra' ); // Add new tab.
		}
		return $tabs;
	}

	/**
	 * Displays the content for the custom OceanWP plugins tab.
	 */
	public function display_oceanwp_plugins_tab_content() {
		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'ocean-extra' ) );
		}

		?>
		<div class="wrap">
			<?php
			// Query Plugins by Author.
			$api = plugins_api(
				'query_plugins',
				array(
					'author'   => 'oceanwp',
					'per_page' => 20,
				)
			);

			if ( is_wp_error( $api ) ) {
				echo '<div class="error"><p>' . $api->get_error_message() . '</p></div>';
			} else {
				$this->ocean_display_plugins_table( $api->plugins );
			}
			?>
		</div>
		<?php
	}

	/**
	 * Displays the plugins using the default WordPress layout.
	 *
	 * @param array $plugins The plugins to display.
	 */
	private function ocean_display_plugins_table( $plugins ) {
		global $wp_list_table;

		if ( ! class_exists( 'WP_Plugin_Install_List_Table' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-plugin-install-list-table.php';
		}

		$wp_list_table = new WP_Plugin_Install_List_Table(
			array(
				'screen' => 'plugin-install',
			)
		);

		$wp_list_table->items = $plugins;
		$wp_list_table->display();
	}
}

// Initialize the class.
new OceanWP_Plugins_Tab();
