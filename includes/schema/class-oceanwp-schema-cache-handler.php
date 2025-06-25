<?php
/**
 * OceanWP Schema Cache Handler Class
 *
 * @package   Ocean_Extra
 * @category  Core
 * @link      https://oceanwp.org/
 * @author    OceanWP
 * @since     2.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (
	! class_exists( 'OceanWP_Schema_Cache_Handler' ) &&
	class_exists( 'OceanWP_JsonLD_Schema' ) // Ensure theme provides the required schema class.
) {

	class OceanWP_Schema_Cache_Handler {

		/**
		 * Constructor.
		 */
		public function __construct() {
			// Register Customizer schema options.
			add_filter( 'ocean_customize_options_data', [ $this, 'register_customize_options' ] );

			// Check if caching is enabled before registering handlers.
			if ( get_theme_mod( 'ocean_schema_cache_enable', false ) ) {

				// JSON-LD output hook.
				add_action( 'oceanwp_schema_output_json_cached', [ $this, 'output_cached_schema' ] );

				// Post save invalidation.
				add_action( 'save_post', [ $this, 'invalidate_post_cache' ] );

				// Add admin notice.
				add_action( 'admin_notices', [ $this, 'admin_notice_schema_cache_cleared' ] );
			}

			// Show debug node for debug purposes. Uncomment the line below, and see 'add_debug_node()' at the bottom of the class.
			//add_action( 'admin_bar_menu', [ $this, 'add_debug_node' ], 100 );

			// Add Clear Cache button if user opted in.
			if ( get_theme_mod( 'ocean_schema_clear_cache_button', false ) ) {
				add_action( 'admin_bar_menu', [ $this, 'add_clear_schema_admin_bar_button' ], 101 );
				add_action( 'admin_post_oceanwp_clear_all_schema_cache', [ $this, 'handle_clear_cache_request' ] );
				add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_bar_script' ] );
				add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_admin_bar_script' ] );
			}
		}

		/**
		 * Output cached Schema.
		 */
		public function output_cached_schema() {
			if ( is_preview() || is_customize_preview() || is_admin() || wp_doing_ajax() ) {
				return;
			}

			if ( ! function_exists( 'oceanwp_is_json_schema_enabled' ) || ! oceanwp_is_json_schema_enabled() || ! get_theme_mod( 'ocean_schema_cache_enable', false ) ) {
				return;
			}

			if ( ! class_exists( 'OceanWP_JsonLD_Schema' ) ) {
				return;
			}

			$schema = OceanWP_JsonLD_Schema::instance();
			if ( ! $schema ) {
				//error_log( '[Schema Cache] Schema instance is null, skipping output.' );
				return;
			}
			$cache_key = apply_filters( 'oceanwp_schema_cache_key', $this->get_cache_key() );

			$json = get_transient( $cache_key );

			if ( false === $json || ! is_string( $json ) || empty( $json ) ) {
				$data = $schema->generate_schema();

				// Apply filters for developers to include their own schema before caching.
				$data = apply_filters( 'oceanwp_schema_cache_data', $data, $cache_key );

				if ( empty( $data ) ) {
					//error_log( '[Schema Cache] Schema generated but empty.' );
					return;
				}

				$expiration = $this->get_cache_duration();

				$json = wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
				set_transient( $cache_key, $json, $expiration );
				//error_log( '[Schema Cache] Cached schema with key: ' . $cache_key );
			}

			echo "\n<script type=\"application/ld+json\">\n$json\n</script>\n";

			//error_log( '[Schema Cache] Checking key: ' . $cache_key );
		}

		/**
		 * Schema caching expiration.
		 */
		protected function get_cache_duration() {
			if ( ! get_theme_mod( 'ocean_schema_cache_enable', false ) ) {
				return;
			}

			$duration = get_theme_mod( 'ocean_schema_cache_duration', '2' );
			$expire   = 2 * DAY_IN_SECONDS;

			if ( '7' === $duration ) {
				$expire = WEEK_IN_SECONDS;
			} elseif ( '14' === $duration ) {
				$expire = 2 * WEEK_IN_SECONDS;
			} elseif ( '30' === $duration ) {
				$expire = MONTH_IN_SECONDS;
			}

			return $expire;
		}

		/**
		 * Generate cache keys.
		 */
		protected function get_cache_key() {
			// Portfolio: main portfolio page
			if (
				function_exists( 'oceanwp_is_main_portfolio_page' )
				&& oceanwp_is_main_portfolio_page()
			) {
				return 'oceanwp_jsonld_portfolio_main';
			}

			// Portfolio: single
			if (
				post_type_exists( 'ocean_portfolio' )
				&& is_singular( 'ocean_portfolio' )
			) {
				return 'oceanwp_jsonld_portfolio_' . get_the_ID();
			}

			// Portfolio: taxonomies
			if (
				taxonomy_exists( 'ocean_portfolio_cat' ) || taxonomy_exists( 'ocean_portfolio_tag' )
			) {
				if ( is_tax( [ 'ocean_portfolio_cat', 'ocean_portfolio_tag' ] ) ) {
					$term = get_queried_object();
					return 'oceanwp_jsonld_portfolio_term_' . ( $term->term_id ?? 0 );
				}
			}

			// WooCommerce: single product
			if ( function_exists( 'is_product' ) && is_product() ) {
				return 'oceanwp_jsonld_product_' . get_the_ID();
			}

			// WooCommerce: shop page
			if ( function_exists( 'is_shop' ) && is_shop() ) {
				return 'oceanwp_jsonld_shop';
			}

			// WooCommerce: product categories and tags
			if ( function_exists( 'is_product_category' ) && ( is_product_category() || is_product_tag() ) ) {
				$term = get_queried_object();
				return 'oceanwp_jsonld_product_term_' . ( $term->term_id ?? 0 );
			}

			// Regular theme-based schema types
			if ( is_front_page() ) {
				return 'oceanwp_jsonld_front';
			}

			if ( is_home() ) {
				return 'oceanwp_jsonld_home';
			}

			if ( is_singular() ) {
				return 'oceanwp_jsonld_' . get_the_ID();
			}

			if ( is_post_type_archive() ) {
				return 'oceanwp_jsonld_posttype_' . get_post_type();
			}

			if ( is_category() || is_tag() || is_tax() ) {
				$term = get_queried_object();
				return 'oceanwp_jsonld_term_' . ( $term->term_id ?? 0 );
			}

			if ( is_author() ) {
				return 'oceanwp_jsonld_author_' . get_queried_object_id();
			}

			if ( is_search() ) {
				return 'oceanwp_jsonld_search';
			}

			if ( is_404() ) {
				return 'oceanwp_jsonld_404';
			}

			return 'oceanwp_jsonld_generic';
		}

		/**
		 * Delete existing Schema cache on post save.
		 */
		public function invalidate_post_cache( $post_id ) {
			if ( ! $post_id || 'auto-draft' === get_post_status( $post_id ) ) {
				return;
			}

			// General singular schema invalidation
			delete_transient( 'oceanwp_jsonld_' . $post_id );

			// Ocean Portfolio
			if ( post_type_exists( 'ocean_portfolio' ) && get_post_type( $post_id ) === 'ocean_portfolio' ) {
				delete_transient( 'oceanwp_jsonld_portfolio_' . $post_id );
			}

			// WooCommerce Product
			if ( function_exists( 'is_product' ) && get_post_type( $post_id ) === 'product' ) {
				delete_transient( 'oceanwp_jsonld_product_' . $post_id );
			}
		}

		/**
		 * Schema Caching Customizer options.
		 */
		public function register_customize_options( $options ) {
			$options['ocean_seo_settings']['options']['oe_schema_caching'] = [
				'type'     => 'section',
				'title'    => esc_html__( 'JSON Schema Caching', 'ocean-extra' ),
				'section'  => 'ocean_seo_settings',
				'after'    => 'ocean_divider_before_schema_caching',
				'class'    => 'section-site-layout',
				'priority' => 15,
				'options'  => [
					'ocean_schema_cache_enable' => [
						'type'              => 'ocean-switch',
						'label'             => esc_html__( 'Enable Schema Caching', 'ocean-extra' ),
						'section'           => 'oe_schema_caching',
						'default'           => false,
						'transport'         => 'postMessage',
						'priority'          => 16,
						'hideLabel'         => false,
						'active_callback'   => function() {
							return function_exists( 'oceanwp_cac_is_schema_manager_enabled' )
								? oceanwp_cac_is_schema_manager_enabled()
								: false;
						},
						'sanitize_callback' => 'oceanwp_sanitize_checkbox',
					],

					'ocean_schema_cache_duration' => [
						'type'              => 'ocean-select',
						'label'             => esc_html__( 'Schema Cache Duration', 'ocean-extra' ),
						'section'           => 'oe_schema_caching',
						'transport'         => 'postMessage',
						'default'           => '2',
						'priority'          => 17,
						'hideLabel'         => false,
						'multiple'          => false,
						'active_callback'   => function() {
							return function_exists( 'oceanwp_cac_is_schema_manager_enabled' )
								? oceanwp_cac_is_schema_manager_enabled()
								: false;
						},
						'sanitize_callback' => 'sanitize_key',
						'choices' => [
							'2'  => esc_html__( '2 Days', 'ocean-extra' ),
							'7'  => esc_html__( '7 Days', 'ocean-extra' ),
							'14' => esc_html__( '14 Days', 'ocean-extra' ),
							'30' => esc_html__( '30 Days', 'ocean-extra' ),
						],
					],

					'ocean_schema_clear_cache_button' => [
						'type'              => 'ocean-switch',
						'label'             => esc_html__( 'Add Clear Cache in Admin Bar', 'ocean-extra' ),
						'section'           => 'oe_schema_caching',
						'default'           => false,
						'transport'         => 'postMessage',
						'priority'          => 18,
						'hideLabel'         => false,
						'active_callback'   => function() {
							return function_exists( 'oceanwp_cac_is_schema_manager_enabled' )
								? oceanwp_cac_is_schema_manager_enabled()
								: false;
						},
						'sanitize_callback' => 'oceanwp_sanitize_checkbox',
					],
				]
			];

			return $options;
		}

		/**
		 * Adds a Clear Schema Cache button to the admin bar.
		 *
		 * @param WP_Admin_Bar $wp_admin_bar The admin bar instance.
		 */
		public function add_clear_schema_admin_bar_button( $wp_admin_bar ) {
			if ( ! get_theme_mod( 'ocean_schema_cache_enable', false ) ) {
				return;
			}

			if ( ! is_admin_bar_showing() ) {
				return;
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$show_clear_all = apply_filters(
				'oceanwp_show_clear_schema_cache_button',
				get_theme_mod( 'ocean_schema_clear_cache_button', false )
			);

			if ( ! $show_clear_all ) {
				return;
			}

			$url = wp_nonce_url(
				admin_url( 'admin-post.php?action=oceanwp_clear_all_schema_cache' ),
				'oceanwp_clear_all_schema_cache'
			);

			$wp_admin_bar->add_node( [
				'id'     => 'schema-cache-clear-all',
				'parent' => null,
				'title'  => esc_html__( 'Clear JSON Schema Cache', 'ocean-extra' ),
				'href'   => esc_url( $url ),
				'meta'   => [
					'class' => 'oceanwp-clear-schema-cache',
				],
			] );
		}

		/**
		 * Handle the Clear Schema Cache request.
		 */
		public function handle_clear_cache_request() {
			if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'oceanwp_clear_all_schema_cache' ) ) {
				wp_die( esc_html__( 'Permission denied.', 'ocean-extra' ) );
			}

			global $wpdb;

			$prefix = '_transient_oceanwp_jsonld_%';

			if ( is_multisite() && function_exists( 'is_plugin_active_for_network' ) && is_plugin_active_for_network( 'ocean-extra/ocean-extra.php' ) ) {
				$sites = get_sites( [ 'fields' => 'ids' ] );

				foreach ( $sites as $site_id ) {
					switch_to_blog( $site_id );
					$this->delete_schema_transients( $wpdb, $prefix );
					restore_current_blog();
				}
			} else {
				$this->delete_schema_transients( $wpdb, $prefix );
			}

			// Show notice after redirect
			set_transient( 'oceanwp_schema_cache_cleared', true, 30 );

			wp_safe_redirect( wp_get_referer() ?: admin_url() );
			exit;
		}

		/**
		 * Deletes all schema-related transients for the current site.
		 *
		 * @param wpdb $wpdb
		 * @param string $like_prefix SQL LIKE pattern to match transient names.
		 */
		protected function delete_schema_transients( $wpdb, $like_prefix ) {
			// Get all matching option names
			$transients = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
					$like_prefix
				)
			);

			if ( ! empty( $transients ) ) {
				foreach ( $transients as $option_name ) {
					$transient_key = str_replace( '_transient_', '', $option_name );
					delete_transient( $transient_key );
				}
			}
		}

		/**
		 * Admin notice after schema cache is cleared.
		 */
		public function admin_notice_schema_cache_cleared() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			if ( get_transient( 'oceanwp_schema_cache_cleared' ) ) {
				delete_transient( 'oceanwp_schema_cache_cleared' );

				echo '<div class="notice notice-success is-dismissible" role="alert" aria-live="polite">';
				echo '<p><strong>' . esc_html__( 'JSON Schema cache cleared successfully.', 'ocean-extra' ) . '</strong></p>';
				echo '</div>';
			}
		}

		public function enqueue_admin_bar_script() {
			if ( ! is_admin_bar_showing() || ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$uri = OE_URL . 'assets/js/';
			wp_register_script(
				'oceanwp-schema-cache-bar',
				$uri . 'schema-cache-bar.js',
				[ 'jquery' ],
				filemtime( OE_PATH . 'assets/js/schema-cache-bar.js' ), // Cache-busting version
				true
			);

			//error_log( plugins_url( 'assets/js/schema-cache-bar.js', __FILE__ ) );

			wp_localize_script( 'oceanwp-schema-cache-bar', 'OceanSchemaCacheBar', [
				'confirm_message' => esc_html__( "⚠️ You are about to clear all cached JSON Schema data.\n\nThis may affect structured data visibility in search engines temporarily.\n\nWe recommend taking a full website backup before continuing.\n\nDo you want to proceed?", 'ocean-extra' ),
			] );

			wp_enqueue_script( 'oceanwp-schema-cache-bar' );
		}

		/**
		 * Schema Cache Debugger tool. Displays in the admin bar.
		 * Uncomment the entire function.
		 * Also see related action hook in __construct()
		 */
		/* public function add_debug_node( $wp_admin_bar ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$key = $this->get_cache_key();
			$cached = get_transient( $key );
			$ttl = get_option( '_transient_timeout_' . $key );

			$wp_admin_bar->add_node([
				'id'    => 'schema-cache-debug',
				'parent'=> null,
				'title' => 'Schema Cache',
				'href'  => false,
			]);

			$wp_admin_bar->add_node([
				'id'    => 'schema-cache-key',
				'parent'=> 'schema-cache-debug',
				'title' => 'Key: ' . esc_html( $key ),
			]);

			$wp_admin_bar->add_node([
				'id'    => 'schema-cache-status',
				'parent'=> 'schema-cache-debug',
				'title' => 'Cached: ' . ( $cached ? 'Yes' : 'No' ),
			]);

			$wp_admin_bar->add_node([
				'id'    => 'schema-cache-ttl',
				'parent'=> 'schema-cache-debug',
				'title' => 'Expires: ' . ( $ttl ? date( 'Y-m-d H:i:s', $ttl ) : 'N/A' ),
			]);
		} */
	}
}
