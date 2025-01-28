<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Ocean_Extenstion_Panel {

	const EXT_URL = 'https://domain.com/extensions.json';

	private $apps       = array();
	private $categories = array();

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_ajax_ocean_install_plugin', array( $this, 'ajax_install_plugin' ) );
		add_action( 'wp_ajax_ocean_activate_plugin', array( $this, 'ajax_activate_plugin' ) );
	}

	public function add_menu_page() {
		add_submenu_page(
			'oceanwp',
			esc_html__( 'Ocean Extenstion Panel', 'ocean-extra' ),
			esc_html__( 'Ocean Extenstions', 'ocean-extra' ),
			'install_plugins',
			'ocean-ext-panel',
			array( $this, 'render_page' )
		);
	}

	public function enqueue_assets( $hook ) {
		if ( isset( $_GET['page'] ) && 'ocean-ext-panel' === $_GET['page'] ) {
			wp_enqueue_style( 'ocean-ext-panel-css', OE_URL . 'includes/extension-panel/assets/css/ocean-ext-panel.css', array(), OE_VERSION );
	
			wp_enqueue_script( 'ocean-ext-panel-js', OE_URL . 'includes/extension-panel/assets/js/ocean-ext-panel.js', array( 'jquery' ), OE_VERSION, true );
			
			$strings = array(
				'installing'       => __( 'Installing...', 'ocean-extra' ),
				'activate'         => __( 'Activate', 'ocean-extra' ),
				'activating'       => __( 'Activating...', 'ocean-extra' ),
				'active'           => __( 'Active', 'ocean-extra' ),
				'install_error'    => __( 'An error occurred while installing the plugin.', 'ocean-extra' ),
				'activate_error'   => __( 'An error occurred while activating the plugin.', 'ocean-extra' ),
			);
	
			wp_localize_script( 'ocean-ext-panel-js', 'OCEAN_EXT_PANEL', array(
				'ajax_url'  => admin_url('admin-ajax.php'),
				'nonce'     => wp_create_nonce( 'ocean_ext_panel_nonce' ),
				'strings'   => $strings,
			));
		}
	}
	

	public function render_page() {
		$this->fetch_apps();
		$this->extract_categories();

		?>
		<div class="wrap ocean-ext-panel-wrapper">
            <div class="ocean-ext-panel-page-title">
                <h2><?php _e( 'Available Plugins & Tools', 'ocean-extra' ); ?></h2>
                <p><?php _e( 'Browse and install plugins directly from here or explore external tools.', 'ocean-extra' ); ?></p>
			</div>

			
			<?php $this->render_category_filters(); ?>
			<div class="ocean-ext-panel-grid">
				<?php $this->render_plugins_list(); ?>
			</div>
		</div>
		<?php
	}

	private function fetch_apps() {
		$response = wp_remote_get( self::EXT_URL );
		if ( is_wp_error( $response ) ) {
			$this->apps = array();
			return;
		}
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );
		if ( ! empty( $data['apps'] ) && is_array( $data['apps'] ) ) {
			$this->apps = $data['apps'];
		} else {
			$this->apps = array();
		}
	}

	private function extract_categories() {
		$all_cats = array();
		foreach ( $this->apps as $app ) {
			if ( isset( $app['categories'] ) && is_array( $app['categories'] ) ) {
				foreach ( $app['categories'] as $cat ) {
					$all_cats[] = $cat;
				}
			}
		}
		$all_cats = array_unique( $all_cats );

		if ( ! in_array( 'All', $all_cats ) ) {
			array_unshift( $all_cats, 'All' );
		}
		$this->categories = $all_cats;
	}

	private function render_category_filters() {
		if ( empty( $this->categories ) ) {
			return;
		}
		echo '<div class="ocean-ext-panel-filters">';
		foreach ( $this->categories as $cat ) {
			echo '<button class="ocean-ext-panel-filter-btn" data-filter="' . esc_attr( $cat ) . '">' . esc_html( $cat ) . '</button>';
		}
		echo '</div>';
	}

	private function render_plugins_list() {
		if ( empty( $this->apps ) ) {
			echo '<p>' . __( 'No apps available at the moment.', 'ocean-extra' ) . '</p>';
			return;
		}
		foreach ( $this->apps as $app ) {
			$this->render_plugin_item( $app );
		}
	}

	private function render_plugin_item( $app ) {
		$name        = isset( $app['name'] ) ? $app['name'] : '';
		$author      = isset( $app['author'] ) ? $app['author'] : '';
		$author_url  = isset( $app['author_url'] ) ? $app['author_url'] : '';
		$description = isset( $app['description'] ) ? $app['description'] : '';
		$image       = isset( $app['image'] ) ? $app['image'] : '';
		$badge       = isset( $app['badge'] ) ? $app['badge'] : '';
		$categories  = isset( $app['categories'] ) ? $app['categories'] : array();

		$action_label = isset( $app['action_label'] ) ? $app['action_label'] : __( 'Learn More', 'ocean-extra' );
		$action_url   = isset( $app['action_url'] ) ? $app['action_url'] : '#';
		$target       = isset( $app['target'] ) ? $app['target'] : '_blank';
		$type         = isset( $app['type'] ) ? $app['type'] : 'link';
		$file_path    = isset( $app['file_path'] ) ? $app['file_path'] : '';

		// Determine action for wporg plugins via AJAX
		if ( $type === 'wporg' && ! empty( $file_path ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
			$installed_plugins = get_plugins();
			if ( isset( $installed_plugins[ $file_path ] ) ) {
				if ( is_plugin_active( $file_path ) ) {
					$action_label = __( 'Active', 'ocean-extra' );
					$action_url   = '#';
					$action_type  = 'none';
				} else {
					$action_label = __( 'Activate', 'ocean-extra' );
					$action_url   = '#';
					$action_type  = 'activate';
				}
			} else {
				$action_label = __( 'Install', 'ocean-extra' );
				$action_url   = '#';
				$action_type  = 'install';
			}
		} else {
			$action_type = 'external'; // For non-wporg
		}

		// Determine CSS class based on action type
		$action_class = '';
		if ( $action_type === 'install' ) {
			$action_class = 'install-button';
		} elseif ( $action_type === 'activate' ) {
			$action_class = 'activate-button';
		} elseif ( $action_type === 'none' ) {
			// Active state
			$action_class = 'active-button';
		} else {
			// external link
			$action_class = 'external-button';
		}

		$cat_data = json_encode($categories);
        
		echo '<div class="ocean-ext-panel-item" data-categories="' . esc_attr( $cat_data ) . '">';
		echo '<div class="ocean-ext-panel-heading">';
		if ( $image ) {
			echo '<div class="ocean-ext-panel-img-wrapper">';
			echo '<img class="ocean-ext-panel-img" src="' . esc_url( $image ) . '" alt="' . esc_attr( $name ) . '">';
			echo '</div>';
		}
		if ( $badge ) {
			echo '<span class="ocean-ext-panel-badge">' . esc_html( $badge ) . '</span>';
		}
		echo '</div>';
		echo '<h3 class="ocean-ext-panel-title">' . esc_html( $name ) . '</h3>';
		if ( $author && $author_url ) {
			echo '<p class="ocean-ext-panel-author">' . __( 'Author', 'ocean-extra' ) . ' <a href="' . esc_url( $author_url ) . '" target="_blank">' . esc_html( $author ) . '</a></p>';
		}
		echo '<div class="ocean-ext-panel-desc"><p>' . esc_html( $description ) . '</p>';
        
		echo '</div>';
		echo '<p class="ocean-ext-panel-actions">';


		if ( $type === 'wporg' ) {
			if ( $action_type === 'none' ) {
				// Active state (no action).
				echo '<button 
						class="ocean-ext-panel-action-btn ' . esc_attr( $action_class ) . '"
						disabled
					  >' . esc_html( $action_label ) . '</button>';
			} elseif ( $action_type === 'install' || $action_type === 'activate' ) {
				// Install or Activate via AJAX.
				echo '<button 
						class="ocean-ext-panel-action-btn ' . esc_attr( $action_class ) . '" 
						data-plugin-file_path="' . esc_attr( $file_path ) . '"
						data-plugin-action="' . esc_attr( $action_type ) . '"
					  >' . esc_html( $action_label ) . '</button>';
			}
		} else {
			// External link.
			echo '<a href="' . esc_url( $action_url ) . '" class="ocean-ext-panel-action-btn ' . esc_attr( $action_class ) . '" target="' . esc_attr( $target ) . '">' . esc_html( $action_label ) . '</a>';
		}

		echo '</p>';
		echo '</div>';
	}

	/**
	 * Handle AJAX plugin installation from WordPress.org
	 */
	public function ajax_install_plugin() {
		check_ajax_referer( 'ocean_ext_panel_nonce', 'security' );

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to install plugins.', 'ocean-extra' ) ) );
		}

		$slug = sanitize_text_field( $_POST['slug'] );

		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		$api = plugins_api( 'plugin_information', array( 'slug' => $slug, 'fields' => array( 'sections' => false ) ) );

		if ( is_wp_error( $api ) ) {
			wp_send_json_error( array( 'message' => $api->get_error_message() ) );
		}

		$upgrader = new Plugin_Upgrader( new WP_Ajax_Upgrader_Skin() );
		$result   = $upgrader->install( $api->download_link );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		// Activate after install?
		$plugin_files = get_plugins();
		foreach ( $plugin_files as $file => $plugin ) {
			if ( dirname( $file ) === $slug ) {
				wp_send_json_success( array( 'message' => __( 'Plugin installed successfully.', 'ocean-extra' ), 'file_path' => $file ) );
			}
		}

		wp_send_json_error( array( 'message' => __( 'Plugin installed but could not determine the file path.', 'ocean-extra' ) ) );
	}

	/**
	 * Handle AJAX plugin activation
	 */
	public function ajax_activate_plugin() {
		check_ajax_referer( 'ocean_ext_panel_nonce', 'security' );

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to activate plugins.', 'ocean-extra' ) ) );
		}

		$file_path = sanitize_text_field( $_POST['file_path'] );
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		$result = activate_plugin( $file_path );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success( array( 'message' => __( 'Plugin activated successfully.', 'ocean-extra' ) ) );
	}
}

new Ocean_Extenstion_Panel();
