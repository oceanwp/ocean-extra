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
	 * @since  2.2.0
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

			$capabilities = apply_filters('ocean_main_metaboxes_capabilities', 'manage_options');

			add_action( 'init',  array( $this, 'register_meta_settings' ), 15 );

			if ( current_user_can($capabilities) ) {

				add_action( 'enqueue_block_editor_assets', array( $this, 'editor_enqueue_script' ), 21 );
				add_filter('update_post_metadata', array( $this, 'handle_updating_post_meta' ), 20, 5);
				add_action( 'rest_api_init', array( $this, 'register_routes' ) );
				add_filter('register_post_type_args', array( $this, 'post_args' ), 10, 2 );
			}

			add_action( 'current_screen', array( $this, 'butterbean_loader' ), 20 );
		}

		/**
		 * Load required files
		 */
		public function includes() {
			require_once OE_PATH . 'includes/post-settings/defaults.php';
			require_once OE_PATH . 'includes/post-settings/functions.php';
			require_once OE_PATH . 'includes/post-settings/sanitize.php';
		}

		/**
		 * Register Post Meta options.
		 *
		 * @return void
		 */
		public function register_meta_settings() {

			$settings = ocean_post_setting_data();

			foreach ( $settings as $key => $value ) {

				$sanitize_callback = isset($value['sanitize']) ? $value['sanitize'] : null;

				$args = array(
					'object_subtype' => $value['subType'],
					'single'         => $value['single'],
					'type'           => $value['type'],
					'default'        => $value['value'],
					'show_in_rest'   => $value['rest'],
					'sanitize_callback' => $sanitize_callback,
					'auth_callback'  => '__return_true',
				);

				// Register meta.
				register_meta( 'post', $key, $args );
			}
		}

		/**
		 * Modify post type arguments to add 'custom-fields' support for specific post types.
		 *
		 * This function hooks into the 'register_post_type_args' filter to check if the current
		 * post type is in the list of post types that should receive 'custom-fields' support. If
		 * the support is not already present, it's added to the post type's arguments.
		 *
		 * @param array  $args      The original post type arguments.
		 * @param string $post_type The slug of the current post type.
		 *
		 * @return array Modified post type arguments.
		 */
		public function post_args( $args, $post_type ) {

			// Array of post types to check for 'custom-fields' support.
			$post_types_to_check = oe_metabox_support_post_types();

			if ( ! is_array( $post_types_to_check ) ) {
				$post_types_to_check = [];
			}

			// Check if the current post type is in the list to check.
			if ( in_array( $post_type, $post_types_to_check ) ) {

				// Check if 'custom-fields' support already exists.
				if ( ! isset( $args['supports'] ) || ! in_array( 'custom-fields', $args['supports'] ) ) {
					$args['supports'][] = 'custom-fields';
				}
			}

			return $args;
		}

		/**
		 * Filter callback to fix the WP REST API meta error when sending updated encoded JSON with no change.
		 *
		 * @param mixed  $value       The new value of the user metadata to be updated.
		 * @param int    $object_id   The ID of the user object whose metadata is being updated.
		 * @param mixed  $meta_value  The new meta value to be stored.
		 * @param mixed  $prev_value  The previous meta value before the update.
		 * @param string $meta_key    Optional. The meta key for which the value is being updated. Defaults to false.
		 *
		 * @return mixed The filtered value. If the function returns true, it prevents the update from occurring.
		 */
		public function handle_updating_post_meta( $value, $object_id, $meta_key, $meta_value, $prev_value ) {

			$meta_type = 'post';
			$serialized_meta_keys = get_all_meta_key();

			// Check if it's a REST API request and the meta key is in the serialized meta keys array.
			if ( defined( 'REST_REQUEST' ) && REST_REQUEST && in_array( $meta_key, $serialized_meta_keys ) ) {

				// Get the meta cache for the user.
				$meta_cache = wp_cache_get( $object_id, $meta_type . '_meta' );

				// If meta cache doesn't exist, update the meta cache for the user.
				if ( ! $meta_cache ) {
					$meta_cache = update_meta_cache( $meta_type, array( $object_id ) );
					$meta_cache = $meta_cache[$object_id];
				}

				// Check if the meta key exists in the meta cache.
				if ( isset( $meta_cache[$meta_key] ) ) {
					// If the new meta value is the same as the one in the meta cache, return true to prevent update.
					if ( $meta_value === $meta_cache[$meta_key][0] ) {
						return true;
					}
				}
			}

			// If not a REST API request or the meta key is not in the serialized meta keys array, proceed with the update.
			return $value;
		}

		/**
		 * Enqueque Editor Scripts
		 */
		public function editor_enqueue_script() {

			if ( false === oe_check_post_types_settings() ) {
				return;
			}

			if ( get_current_screen()->base === 'widgets' ) {
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
					'choices'   => oe_get_choices(),
					'postTypes' => oe_metabox_support_post_types(),
					'isPremium' => ocean_check_pro_license()
				)
			);
		}

		/**
		 * Remove Butterbean metabox when block editor.
		 */
		public function butterbean_loader() {

			if ( false === oe_is_block_editor() ) {
				add_action( 'current_screen', 'butterbean_loader_100', 9999 );
				require_once OE_PATH . '/includes/metabox/gallery-metabox/gallery-metabox.php';
			}
		}

		/**
		 * Register rest routes.
		 */
		public function register_routes() {

			register_rest_route(
				$this->namespace . $this->version,
				'/option-reset-current/(?P<post_id>\d+)',
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'current_reset_options_callback' ),
					'permission_callback' => array( $this, 'settings_permission' ),
				)
			);
		}

		/**
		 * Ocean Migration metabox for current page/post only.
		 *
		 * @param WP_REST_Request $request  Request object.
		 *
		 * @return mixed
		 */
		public function current_reset_options_callback( WP_REST_Request $request ) {

			$newMetaData = ocean_post_setting_data();

			$post_id = $request->get_param('post_id');

			if ( $post_id > 0 ) {
				$post = get_post( $post_id );

				if ( $post && in_array( $post->post_type, get_post_types() ) ) {
					$oldMetaData = get_post_custom( $post_id );

					foreach ( $newMetaData as $key => $value ) {
						if ( isset( $oldMetaData[ $key ][0] ) && $oldMetaData[ $key ][0] !== $value['value'] ) {
							update_post_meta( $post_id, $key, $value['value'] );
						}
					}

					return $this->success( '<span class="dashicons dashicons-yes"></span>' );
				}
			}
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

	if ( ! defined( 'OCEAN_METABOX_LOADER' ) ) {
		define( 'OCEAN_METABOX_LOADER', true );

		return OceanWP_Post_Settings::instance();
	}
}

OceanWP_Post_Settings();
