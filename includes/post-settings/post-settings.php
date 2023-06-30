<?php
/**
 * OceanWP Post Metabox
 *
 * @package Ocean_Extra
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// The Metabox class
if ( ! class_exists( 'OceanWP_Post_Settings' ) ) {

	/**
	 * Main Post Settings class.
	 *
	 * @since  2.1.8
	 * @access public
	 */
	final class OceanWP_Post_Settings {

		/**
		 * Namespace.
		 *
		 * @var string
		 */
		protected $namespace = 'oceanwp/v';

		/**
		 * Version.
		 *
		 * @var string
		 */
		protected $version = '1';

		/**
		 * Ocean_Extra The single instance of Ocean_Extra.
		 *
		 * @var     object
		 * @access  private
		 */
		private static $_instance = null;

		/**
		 * Main OceanWP_Post_Settings Instance
		 *
		 * @static
		 * @see OceanWP_Post_Settings()
		 * @return Main OceanWP_Post_Settings instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {

			$this->includes();

			add_action( 'init',  array( $this, 'register_meta_settings' ), 15 );
			add_action( 'enqueue_block_editor_assets', array( $this, 'editor_enqueue_script' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_script' ) );
			add_action( 'rest_api_init', array( $this, 'register_routes' ) );
			add_action( 'updated_post_meta', array( $this, 'add_check_editor' ), 10, 4 );
			add_action( 'admin_init',  array( $this, 'remove_butterbean_metabox' ), 20 );

		}

		/**
		 * Load required files
		 */
		public function includes() {
			require_once OE_PATH . 'includes/post-settings/defaults.php';
			require_once OE_PATH . 'includes/post-settings/functions.php';
			require_once OE_PATH . 'includes/post-settings/apply-settings.php';

			if (is_admin()) {
				require_once OE_PATH . 'includes/post-settings/admin-notice.php';
			}
		}

		/**
		 * Use value of post meta for something when the post
		 * meta changes
		 *
		 * @param  integer $meta_id    ID of the meta data field
		 * @param  integer $post_id    Post ID
		 * @param  string $meta_key    Name of meta field
		 * @param  string $meta_value  Value of meta field
		 */
		public function add_check_editor( $meta_id, $post_id, $meta_key='', $meta_value='') {

			if ( oe_is_block_editor() ) {
				update_post_meta( $post_id, 'ocean_is_block_editor', 'yes' );
			} else {
				update_post_meta( $post_id, 'ocean_is_block_editor', 'no' );
			}
		}

		/**
		 * Register Post Meta options.
		 *
		 * @return void
		 */
		public function register_meta_settings() {

			$settings = ocean_post_setting_data();

			foreach ( $settings as $key => $value ) {

				$args = array(
					'single'        => $value['single'],
					'type'          => $value['type'],
					'value'         => $value['value'],
					'show_in_rest'  => $value['rest'],
					'auth_callback' => '__return_true',
				);

				// Register meta.
				register_meta( 'post', $key, $args );
			}
		}

		/**
		 * Admin script
		 */
		public function admin_script() {

			if ( false === oe_check_post_types_settings() ) {
				return;
			}

			$uri   = OE_URL . 'includes/post-settings/assets/';
			$asset = require OE_PATH . 'includes/post-settings/assets/migrate.asset.php';
			$deps  = $asset['dependencies'];

			wp_register_script(
				'oe-metabox-migrate-action',
				$uri . 'migrate.js',
				$deps,
				filemtime( OE_PATH . 'includes/post-settings/assets/migrate.js' ),
				true
			);

			wp_enqueue_style(
				'oe-metabox-migrate-action',
				$uri . 'style-migrate.css',
				array( 'wp-components' ),
				filemtime( OE_PATH . 'includes/post-settings/assets/style-migrate.css' )
			);

			wp_enqueue_script( 'oe-metabox-migrate-action' );

			if ( function_exists( 'wp_set_script_translations' ) ) {
				wp_set_script_translations( 'oe-metabox-migrate-action', 'ocean-extra' );
			}

		}

		/**
		 * Enqueque Editor Scripts
		 */
		public function editor_enqueue_script() {

			if ( false === oe_check_post_types_settings() ) {
				return;
			}

			$uri   = OE_URL . 'includes/post-settings/assets/';
			$asset = require OE_PATH . 'includes/post-settings/assets/index.asset.php';
			$deps  = $asset['dependencies'];
			array_push( $deps, 'updates' );

			wp_register_script(
				'owp-post-settings',
				$uri . 'index.js',
				$deps,
				filemtime( OE_PATH . 'includes/post-settings/assets/index.js' ),
				true
			);

			wp_enqueue_style(
				'owp-post-settings',
				$uri . 'style-index.css',
				array(),
				filemtime( OE_PATH . 'includes/post-settings/assets/style-index.css' )
			);

			wp_enqueue_script( 'owp-post-settings' );

			if ( function_exists( 'wp_set_script_translations' ) ) {
				wp_set_script_translations( 'owp-post-settings', 'ocean-extra' );
			}

			$editor_loc_data = $this->localize_editor_script();
			if ( is_array( $editor_loc_data ) ) {
				wp_localize_script(
					'owp-post-settings',
					'owpPostSettings',
					$editor_loc_data
				);
			}
		}

		/**
		 * Localize Script.
		 *
		 * @return mixed|void
		 */
		public function localize_editor_script() {

			return apply_filters(
				'ocean_post_settings_localize',
				array(
					'choices' => oe_get_choices()

				)
			);
		}

		/**
		 * Remove Butterbean Metabox
		 */
		public function remove_butterbean_metabox() {

			$post_id = '';

			if ( isset( $_GET['post'] ) ) {
				$post_id = $_GET['post'];
			}

			$migrated     = get_option( 'ocean_metabox_migration_status' );
			$block_editor = get_post_meta( $post_id, 'ocean_is_block_editor', true );

			if ( 'yes' === $block_editor && 'true' === $migrated  ) {
				remove_all_actions( 'butterbean_register' );
			}
		}

		/**
		 * Register rest routes.
		 */
		public function register_routes() {

			// Update Settings.
			register_rest_route(
				$this->namespace . $this->version,
				'/metabox-migrate/',
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'settings_callback' ),
					'permission_callback' => array( $this, 'settings_permission' ),
				)
			);
		}

		/**
		 * Get edit options permissions.
		 *
		 * @return bool
		 */
		public function settings_permission() {
			return current_user_can( 'manage_options' );
		}

		/**
		 * Ocean Features.
		 *
		 * @param WP_REST_Request $request  Request object.
		 *
		 * @return mixed
		 */
		public function settings_callback( WP_REST_Request $request ) {

			$status = false;

			$posts = get_posts( array(
				'post_type'   => get_post_types(),
				'numberposts' => -1,
				'post_status' => 'any'
			) );

			$newMetaData = ocean_post_setting_data();

			foreach ( $posts as $post ) {
				$oldMetaData = get_post_meta( $post->ID );
				foreach ( $newMetaData as $key => $value ) {
					foreach ( $oldMetaData as $keyname => $option ) {
						if (  $value['map'] === $keyname ) {
							update_post_meta( $post->ID, $key, $option[0] );
							$status = true;
						}
					}
				}
			}

			update_option( 'ocean_metabox_migration_status', 'true' );

			return $this->success( '<span class="dashicons dashicons-yes"></span>' );
		}

		/**
		 * Success
		 *
		 * @param mixed $response response data.
		 * @return mixed
		 */
		public function success( $response ) {
			return new WP_REST_Response(
				array(
					'success'  => true,
					'response' => $response,
				),
				200
			);
		}

	}
}

/**
 * Returns the main instance of OceanWP_Post_Settings to prevent the need to use globals.
 *
 * @return object OceanWP_Post_Settings
 */
function OceanWP_Post_Settings() {
	return OceanWP_Post_Settings::instance();
}

OceanWP_Post_Settings();
