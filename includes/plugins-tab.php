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
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_oceanwp_install_plugin', array( $this, 'ajax_install_plugin' ) );
		add_action( 'wp_ajax_oceanwp_activate_plugin', array( $this, 'ajax_activate_plugin' ) );
	}

	/**
	 * Enqueues the necessary scripts for handling AJAX plugin installation.
	 */
	public function enqueue_scripts( $hook_suffix ) {
		// Only enqueue the scripts on the plugin installation page and our custom tab.
		if ( 'plugin-install.php' === $hook_suffix && isset( $_GET['tab'] ) && 'oceanwp_plugins_tab' === $_GET['tab'] ) {
			wp_enqueue_script( 'plugin-install' );
			wp_enqueue_script( 'updates' );
			wp_enqueue_script( 'oceanwp-plugin-install', plugin_dir_url( __FILE__ ) . '../assets/js/oceanwp-plugin-install.js', array( 'jquery' ), OE_VERSION, true );

			wp_localize_script(
				'oceanwp-plugin-install',
				'oceanwpPluginInstall',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( 'plugin_install_nonce' ),
				)
			);
		}
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
			$tabs['oceanwp_plugins_tab'] = __( 'For OceanWP', 'ocean-extra' ); // Add new tab.
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
			<h2><?php _e( 'For OceanWP', 'ocean-extra' ); ?></h2>
			<div id="oceanwp-plugin-list">
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

	/**
	 * Handles the AJAX request to install a plugin.
	 */
	public function ajax_install_plugin() {
		check_ajax_referer( 'plugin_install_nonce', '_ajax_nonce' );

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( __( 'You do not have sufficient permissions to install plugins.', 'ocean-extra' ) );
		}

		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

		$slug = sanitize_text_field( $_POST['slug'] );
		$api  = plugins_api(
			'plugin_information',
			array(
				'slug'   => $slug,
				'fields' => array(
					'sections' => false,
				),
			)
		);

		if ( is_wp_error( $api ) ) {
			wp_send_json_error( $api->get_error_message() );
		}

		$skin     = new Automatic_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );
		$result   = $upgrader->install( $api->download_link );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() );
		}

		wp_send_json_success();
	}
	/**
	 * Handles the AJAX request to activate a plugin.
	 */
	public function ajax_activate_plugin() {
		check_ajax_referer( 'plugin_install_nonce', '_ajax_nonce' );

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( __( 'You do not have sufficient permissions to activate plugins.', 'ocean-extra' ) );
		}

		$slug        = sanitize_text_field( $_POST['slug'] );
		$plugin_file = $this->get_plugin_file_path( $slug );

		if ( ! $plugin_file || ! file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) ) {
			wp_send_json_error( __( 'Plugin file does not exist.', 'ocean-extra' ) );
		}

		$result = activate_plugin( $plugin_file );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() );
		}

		wp_send_json_success();
	}

	/**
	 * Get the plugin file path based on the plugin slug.
	 *
	 * @param string $slug The plugin slug.
	 * @return string|false The plugin file path or false if not found.
	 */
	private function get_plugin_file_path( $slug ) {
		$plugins = get_plugins();

		foreach ( $plugins as $plugin_file => $plugin_data ) {
			if ( strpos( $plugin_file, $slug . '/' ) !== false || strpos( $plugin_file, $slug . '.php' ) !== false ) {
				return $plugin_file;
			}
		}

		return false;
	}
}

// Initialize the class
new OceanWP_Plugins_Tab();

