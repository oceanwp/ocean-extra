<?php
/**
 * OceanWP Setup Wizard: Get site templates
 *
 * @package Ocean_Extra
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// The Setup Wizard site templates class
if (!class_exists('OE_Onboarding_Site_Templates_Import_Data')) {

    /**
     * OE_Onboarding_Site_Templates.
     *
     * @since  2.4.6
     * @access public
     */
    final class OE_Onboarding_Site_Templates_Import_Data {

        /**
         * Class instance.
         *
         * @var object
         * @access private
         */
        private static $_instance = null;

        /**
         * OE_Onboarding_Site_Templates Instance
         *
         * @static
         * @return OE_Onboarding_Site_Templates instance
         */
        public static function instance() {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
		 * Constructor
		 */
		public function __construct() {

            add_action('wp_ajax_download_template_data', array( $this, 'download_selected_template_data' ) );
            add_action( 'wp_ajax_oceanwp_onboarding_import_data', array( $this, 'onboarding_import_data' ) );
            add_action( 'wp_ajax_oceanwp_onboarding_after_import', array( $this, 'ajax_after_import' ) );

		}

        public function get_template_remote_url() {
            $endpoint = 'https://demos.oceanwp.org/';
            return esc_url_raw( $endpoint );
        }

        public function download_selected_template_data() {

            check_ajax_referer('owp-onboarding', 'security');

            $template = get_option('ocean_installing_template_data');

            if (empty($template)) {
                wp_send_json_error(['message' => __('No template selected.', 'ocean-extra')]);
            }

            $this->download_template_files($template);

            if ( is_wp_error( $file_path ) ) {
                wp_send_json_error(array(
                    'message' => __('Failed to download the file.', 'ocean-extra')
                ));
            }

            update_option('ocean_downloaded_demo_path', $file_path);

            wp_send_json_success(['message' => __('Template is downloading in the background.', 'ocean-extra')]);
        }

        public function download_template_files($template) {

            $files_to_download = [
                'content'        => 'sample-data.xml',
                'theme_settings' => 'oceanwp-export.dat',
                'widgets'        => 'widgets.wie',
                'form'           => 'form.json',
            ];

            foreach ($files_to_download as $key => $file_name) {
                if (isset($template[$key]) && $template[$key] === true) {
                    $file_url = sprintf(
                        '%s%s/%s',
                        trailingslashit($this->get_template_remote_url()),
                        $template['slug'],
                        $file_name
                    );

                    $file_path = $this->download_file($file_url);

                    if (is_wp_error($file_path)) {
                        wp_send_json_error(array(
                            'message' => sprintf(__('Failed to download file: %s.', 'ocean-extra'), $file_name),
                        ));
                    }

                    update_option("ocean_import_data_{$file_name}_path", $file_path);
                }
            }

            wp_send_json_success(array(
                'message' => __('All files downloaded successfully.', 'ocean-extra')
            ));
        }

        /**
         * Import data
         *
         * @access public
         * @return void
         */
        public function onboarding_import_data() {

            if ( !isset($_POST['nonce']) || !wp_verify_nonce( sanitize_key($_POST['nonce']), 'owp-onboarding' ) ) {
                wp_send_json_error(array(
                    'message' => __('Nonce verification failed.', 'ocean-extra')
                ));
            }

            $template = get_option('ocean_installing_template_data');

            if (empty($template)) {
                wp_send_json_error(['message' => __('No template selected.', 'ocean-extra')]);
            }

            $import_type = isset($_POST['importType']) ? sanitize_text_field($_POST['importType']) : '';

            if (empty($import_type)) {
                wp_send_json_error(['message' => __('No import type specified.', 'ocean-extra')]);
            }

            $result = null;

            switch ($import_type) {
                case 'content':
                    $result = $this->import_content($template);
                    break;
                case 'customizer':
                    $result = $this->import_theme_settings($template);
                    break;
                case 'widgets':
                    $result = $this->import_widgets($template);
                    break;
                case 'form':
                    $result = $this->import_wpforms($template);
                    break;
                default:
                    wp_send_json_error(['message' => __('Invalid import type.', 'ocean-extra')]);
            }

            if (is_wp_error($result)) {
                wp_send_json_error(['message' => $result->get_error_message()]);
            }

            wp_send_json_success([
                'success' => true,
                'imported' => $import_type,
                'message'  => sprintf(__('Successfully imported %s.', 'ocean-extra'), $import_type),
            ]);
        }

        public function import_content($template) {

            $file_url = trailingslashit($this->get_template_remote_url()) . $template['slug'] . '/sample-data.xml';

            $file_path = get_option( 'ocean_import_data_content_path' );

            if ( ! file_exists( $file_path ) ) {
                $file_path = $this->download_file( $file_url );

                if ( is_wp_error( $file_path ) ) {
                    return new WP_Error('import_error', __('Failed to download the XML file.', 'ocean-extra'));
                }
            }

            $sample_page      = get_page_by_path( 'sample-page', OBJECT, 'page' );
			$hello_world_post = get_page_by_path( 'hello-world', OBJECT, 'post' );

			if ( ! is_null( $sample_page ) ) {
				wp_delete_post( $sample_page->ID, true );
			}

			if ( ! is_null( $hello_world_post ) ) {
				wp_delete_post( $hello_world_post->ID, true );
			}


            if ( ! class_exists('WP_Importer') ) {
                require_once ABSPATH . 'wp-admin/includes/import.php';
            }

            if ( ! class_exists('Ocean_WP_Import') ) {
                require_once plugin_dir_path( __FILE__ ) . 'includes/onboarding/class/wp-importer.php';
            }

            $importer = new Ocean_WP_Import();

            try {
                ob_start();
                $importer->fetch_attachments = true;
                $importer->import( $file_path );
                ob_end_clean();

                if ( file_exists( $file_path ) ) {
                    unlink( $file_path );
                }

                return true;

            } catch ( Exception $e ) {

                if ( file_exists( $file_path ) ) {
                    unlink( $file_path );
                }

                return new WP_Error(
                    'import_failed',
                    sprintf(__('Import failed: %s', 'ocean-extra'), $e->getMessage())
                );
            }
        }

        public function import_theme_settings($template) {

            $file_url = trailingslashit($this->get_template_remote_url()) . $template['slug'] . '/oceanwp-export.dat';

            $file_path = get_option( 'ocean_import_data_theme_settings_path' );

            if ( ! file_exists( $file_path ) ) {
                $file_path = $this->download_file( $file_url );

                if ( is_wp_error( $file_path ) ) {
                    return new WP_Error('theme_import_error', __('Failed to download theme settings.', 'ocean-extra'));
                }
            }

            if ( file_exists( $file_path ) ) {

                $importer = new Ocean_Settings_Importer();
                $result = $importer->process_import_file( $file_path );

                if ( is_wp_error( $result ) ) {

                    return new WP_Error(
                        'theme_import_failed',
                        sprintf(__('Failed to import theme settings: %s', 'ocean-extra'), $result->get_error_message())
                    );
                }

                unlink( $file_path );
                delete_option( 'ocean_import_data_theme_settings_path' );
            }

            return true;
        }

        public function import_widgets($template) {

            $file_url = trailingslashit($this->get_template_remote_url()) . $template['slug'] . '/widgets.wie';

            $file_path = get_option( 'ocean_import_data_widgets_path' );

            if ( ! file_exists( $file_path ) ) {
                $file_path = $this->download_file( $file_url );

                if ( is_wp_error( $file_path ) ) {
                    return new WP_Error('widgets_import_error', __('Failed to download widgets.', 'ocean-extra'));
                }
            }

            if ( file_exists( $file_path ) ) {

                $importer = new Ocean_Widget_Importer();
                $result = $importer->process_import_file( $file_path );

                if ( is_wp_error( $result ) ) {

                    return new WP_Error(
                        'widgets_import_failed',
                        sprintf(__('Failed to import widgets: %s', 'ocean-extra'), $result->get_error_message())
                    );
                }

                unlink( $file_path );
                delete_option( 'ocean_import_data_widgets_path' );
            }

            return true;
        }

        public function import_wpforms($template) {

            $file_url = trailingslashit($this->get_template_remote_url()) . $template['slug'] . '/form.json';

            $file_path = get_option( 'ocean_import_data_form_path' );

            if ( ! file_exists( $file_path ) ) {
                $file_path = $this->download_file( $file_url );

                if ( is_wp_error( $file_path ) ) {
                    return new WP_Error('wpform_import_error', __('Failed to download wpForms.', 'ocean-extra'));
                }
            }

            if ( file_exists( $file_path ) ) {

                $importer = new Ocean_WPForms_Importer();
                $result = $importer->process_import_file( $file_path );

                if ( is_wp_error( $result ) ) {

                    return new WP_Error(
                        'wpform_import_failed',
                        sprintf(__('Failed to import wpForms: %s', 'ocean-extra'), $result->get_error_message())
                    );
                }

                unlink( $file_path );
                delete_option( 'ocean_import_data_form_path' );
            }

            return true;
        }

        private function download_file( $file_url ) {

            $upload_dir  = wp_upload_dir();
            $temp_data_dir = $upload_dir['basedir'] . '/sample-data/';

            if (!file_exists($temp_data_dir)) {
                wp_mkdir_p($temp_data_dir);
            }

            $file_path = $temp_data_dir . basename( $file_url );

            if ( file_exists( $file_path ) ) {
                unlink( $file_path );
            }

            $response = wp_remote_get( $file_url, array( 'timeout' => 60 ) );
            if ( is_wp_error( $response ) ) {
                return new WP_Error( 'download_failed', 'Failed to download the XML file' );
            }

            file_put_contents( $file_path, wp_remote_retrieve_body( $response ) );

            if ( ! file_exists( $file_path ) ) {
                return new WP_Error( 'file_save_failed', 'Failed to save the XML file' );
            }

            return $file_path;
        }

        /**
		 * After import
		 */
		public function ajax_after_import() {
            if ( !isset($_POST['nonce']) || !wp_verify_nonce( $_POST['nonce'], 'owp-onboarding' ) ) {
                wp_send_json_error(array(
                    'message' => __('Nonce verification failed.', 'ocean-extra')
                ));
            }

            $template = get_option('ocean_installing_template_data');

            if ( empty( $template ) ) {
                wp_send_json_error(array(
                    'message' => __('no template selected.', 'ocean-extra')
                ));
            }

			if ( $_POST['xml_import_status'] === 'success' ) {


				// Elementor width setting
				$elementor_width = isset( $template['elementor_width'] ) ? $template['elementor_width'] : '';

				// Reading settings
				$homepage_title = isset( $template['home_title'] ) ? $template['home_title'] : 'Home';
				$blog_title     = isset( $template['blog_title'] ) ? $template['blog_title'] : '';

				// Posts to show on the blog page
				$posts_to_show = isset( $template['posts_to_show'] ) ? $template['posts_to_show'] : '';

				// If shop demo
				$shop_demo = isset( $template['is_shop'] ) ? $template['is_shop'] : false;

				// Product image size
				$image_size     = isset( $template['woo_image_size'] ) ? $template['woo_image_size'] : '';
				$thumbnail_size = isset( $template['woo_thumb_size'] ) ? $template['woo_thumb_size'] : '';
				$crop_width     = isset( $template['woo_crop_width'] ) ? $template['woo_crop_width'] : '';
				$crop_height    = isset( $template['woo_crop_height'] ) ? $template['woo_crop_height'] : '';

				// Assign WooCommerce pages if WooCommerce Exists
				if ( class_exists( 'WooCommerce' ) && true == $shop_demo ) {

					$woopages = array(
						'woocommerce_shop_page_id'            => 'Shop',
						'woocommerce_cart_page_id'            => 'Cart',
						'woocommerce_checkout_page_id'        => 'Checkout',
						'woocommerce_pay_page_id'             => 'Checkout &#8594; Pay',
						'woocommerce_thanks_page_id'          => 'Order Received',
						'woocommerce_myaccount_page_id'       => 'My Account',
						'woocommerce_edit_address_page_id'    => 'Edit My Address',
						'woocommerce_view_order_page_id'      => 'View Order',
						'woocommerce_change_password_page_id' => 'Change Password',
						'woocommerce_logout_page_id'          => 'Logout',
						'woocommerce_lost_password_page_id'   => 'Lost Password'
					);

					foreach ( $woopages as $woo_page_name => $woo_page_title ) {

						$woopage = oe_get_page_by_title( $woo_page_title );
						if ( isset( $woopage ) && $woopage->ID ) {
							update_option( $woo_page_name, $woopage->ID );
						}

					}

					// We no longer need to install pages.
					delete_option( '_wc_needs_pages' );
					delete_transient( '_wc_activation_redirect' );

					// Get products image size.
					update_option( 'woocommerce_single_image_width', $image_size );
					update_option( 'woocommerce_thumbnail_image_width', $thumbnail_size );
					update_option( 'woocommerce_thumbnail_cropping', 'custom' );
					update_option( 'woocommerce_thumbnail_cropping_custom_width', $crop_width );
					update_option( 'woocommerce_thumbnail_cropping_custom_height', $crop_height );

				}

				// Set imported menus to registered theme locations.
				$locations = get_theme_mod( 'nav_menu_locations' );
                $locations = is_array( $locations ) ? $locations : [];
				$menus     = wp_get_nav_menus();

				if ( $menus ) {

					foreach ( $menus as $menu ) {

						if ( $menu->name == 'Main Menu' ) {
							$locations['main_menu'] = $menu->term_id;
						} else if ( $menu->name == 'Top Menu' ) {
							$locations['topbar_menu'] = $menu->term_id;
						} else if ( $menu->name == 'Footer Menu' ) {
							$locations['footer_menu'] = $menu->term_id;
						} else if ( $menu->name == 'Sticky Footer' ) {
							$locations['sticky_footer_menu'] = $menu->term_id;
						}

					}

				}

				// Set menus to locations
				set_theme_mod( 'nav_menu_locations', $locations );

				// Disable Elementor default settings
				update_option( 'elementor_disable_color_schemes', 'yes' );
				update_option( 'elementor_disable_typography_schemes', 'yes' );

                // Disable Elementor Local Google Fonts download.
                update_option( 'elementor_experiment-e_local_google_fonts', 'inactive' );

				if ( ! empty( $elementor_width ) ) {
					update_option( 'elementor_container_width', $elementor_width );
				}

				// Assign front page and posts page (blog page).
				$home_page = oe_get_page_by_title($homepage_title);
                $blog_page = oe_get_page_by_title($blog_title);

                if ( isset($homepage_title) && ! empty($homepage_title) ) {
                    $home_page = oe_get_page_by_title($homepage_title);
                    update_option( 'show_on_front', 'page' );
                    update_option( 'page_on_front', $home_page->ID );
                }

                if ( isset($blog_title) && ! empty($blog_title) ) {
                    $blog_page = oe_get_page_by_title($blog_title);
                    //update_option( 'page_for_posts', $blog_page->ID );

                    if ( $blog_page instanceof WP_Post ) {
                        update_option( 'page_for_posts', $blog_page->ID );
                    }
                }

				// Posts to show on the blog page
				if ( ! empty( $posts_to_show ) ) {
					update_option( 'posts_per_page', $posts_to_show );
				}

				if ( 'elementor' !== $template['builder'] ) {

					$page_ids = get_all_page_ids();

					foreach ( $page_ids as $id ) {
						delete_post_meta( $id, '_elementor_edit_mode', '' );
					}

				}

                delete_option( 'ocean_import_data_content_path' );

                wp_send_json_success(array(
                    'sucess' => true
                ));
			}
		}


    }
}

OE_Onboarding_Site_Templates_Import_Data::instance();
