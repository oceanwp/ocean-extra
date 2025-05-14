<?php
/**
 * OceanWP Setup Wizard: Plugin Manager
 *
 * @package Ocean_Extra
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin manager
 */
if (!class_exists('OE_Onboarding_Plugin_Manager')) {

    /**
     * OE_Onboarding_Plugin_Manager.
     *
     * @since  2.4.6
     * @access public
     */
    final class OE_Onboarding_Plugin_Manager {

        /**
         * Class instance.
         *
         * @var object
         * @access private
         */
        private static $_instance = null;

        /**
         * OE_Onboarding_Plugin_Manager Instance
         *
         * @static
         * @return OE_Onboarding_Plugin_Manager instance
         */
        public static function instance() {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * Install a plugin from WordPress.org
         */
        public function install_plugin(WP_REST_Request $request) {
            $this->include_wp_files();

            $plugin_slug = sanitize_text_field($request->get_param('plugin_slug'));

            if (!$plugin_slug) {
                return new WP_REST_Response(['message' => __('Plugin slug is required', 'ocean-extra')], 400);
            }

            if ($this->get_plugin_status($plugin_slug) !== 'uninstalled') {
                return new WP_REST_Response(['message' => __('Plugin is already installed', 'ocean-extra')], 200);
            }

            $api = plugins_api('plugin_information', ['slug' => $plugin_slug, 'fields' => ['sections' => false]]);
            if (is_wp_error($api)) {
                return new WP_REST_Response(['message' => __('Invalid plugin slug or API error', 'ocean-extra')], 400);
            }

            $upgrader = new Plugin_Upgrader(new WP_Ajax_Upgrader_Skin());
            $result = $upgrader->install($api->download_link);

            if (is_wp_error($result)) {
                return new WP_REST_Response(['message' => __('Failed to install plugin. ', 'ocean-extra') . $result->get_error_message()], 400);
            }

            return new WP_REST_Response(['message' => __('Plugin installed successfully', 'ocean-extra')], 200);
        }

        /**
         * Activate an installed plugin
         */
        public function activate_plugin(WP_REST_Request $request) {
            $this->include_wp_files();

            $plugin_slug = sanitize_text_field($request->get_param('plugin_slug'));

            if (!$plugin_slug) {
                return new WP_REST_Response(['message' => __('Plugin slug is required', 'ocean-extra')], 400);
            }

            $plugin_path = $this->is_plugin_installed($plugin_slug);
            if (!$plugin_path) {
                return new WP_REST_Response(['message' => __('Plugin not found', 'ocean-extra')], 400);
            }

            $result = activate_plugin($plugin_path);

            if (is_wp_error($result)) {
                return new WP_REST_Response([
                    'message' => sprintf(__('Failed to activate plugin. %s.', 'ocean-extra'), $result->get_error_message()),
                    400
                ]);
            }

            return new WP_REST_Response(['message' => 'Plugin activated successfully'], 200);
        }

        /**
         * Check if a plugin is installed
         */
        private function is_plugin_installed($slug) {
            $this->include_wp_files();

            $installed_plugins = get_plugins();

            foreach ($installed_plugins as $plugin_file => $data) {
                $plugin_slug_parts = explode('/', $plugin_file);
                $main_file         = $plugin_slug_parts[0];

                if (strtolower($slug) === strtolower($main_file)) {
                    return $plugin_file;
                }
            }
            return false;
        }

        /**
         * Get the status of a plugin
         */
        public function get_plugin_status($slug) {
            $plugin_file = $this->is_plugin_installed($slug);
            if (!$plugin_file) return 'uninstalled';
            return is_plugin_active($plugin_file) ? 'active' : 'installed';
        }

        /**
         * Include necessary WordPress files
         */
        private function include_wp_files() {
            if (!function_exists('get_plugins')) {
                include_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
            if (!class_exists('Plugin_Upgrader', false)) {
                require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
            }
            if (!function_exists('activate_plugin')) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
            if (!function_exists('plugins_api')) {
                require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
            }
            if (!function_exists('WP_Filesystem')) {
                include_once ABSPATH . 'wp-admin/includes/file.php';
            }
        }

        /**
         * Check if user has permission
         *
         * @param string $capability Capability name.
         * @return bool
         */
        public function can($capability = 'install_plugins') {
            if (defined('WP_CLI') && WP_CLI) {
                return true;
            }

            if (is_multisite()) {
                $can = current_user_can_for_blog(get_current_blog_id(), $capability);
            } else {
                $can = current_user_can($capability);
            }

            if ($can) {
                $can = $capability;
            }

            return $can;
        }

        /**
         * Check if direct access to filesystem is possible (without FTP)
         */
        public function has_direct_access($context = null) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            WP_Filesystem();

            global $wp_filesystem;

            if ($wp_filesystem) {
                if (is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code()) {
                    return false;
                } else {
                    return $wp_filesystem->method === 'direct';
                }
            }

            if (get_filesystem_method([], $context) === 'direct') {
                ob_start();
                $creds = request_filesystem_credentials(admin_url(), '', false, $context, null);
                ob_end_clean();

                if (WP_Filesystem($creds)) {
                    return true;
                }
            }

            return false;
        }
    }
}

OE_Onboarding_Plugin_Manager::instance();
