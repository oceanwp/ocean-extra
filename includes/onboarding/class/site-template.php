<?php
/**
 * OceanWP Setup Wizard: Get site templates
 *
 * @package Ocean_Extra
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly.
if ( ! defined('ABSPATH') ) {
    exit;
}

// The Setup Wizard site templates class
if ( ! class_exists('OE_Onboarding_Site_Templates') ) {

    /**
     * OE_Onboarding_Site_Templates.
     *
     * @since  2.4.8
     * @access public
     */
    final class OE_Onboarding_Site_Templates {

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
         * Fetch OceanWP template data.
         *
         * @param bool $force_update Force update the transient.
         * @return string|WP_Error JSON encoded template data or WP_Error on failure.
         */
        public function fetch_ocean_template_data($force_update = false) {

            $cached_demos = get_transient('ocean_onboarding_template_data');

            if (!$cached_demos || $force_update) {

                $primary_api_url = 'https://demos.oceanwp.org/1wizard-installation/demos.json';
                $fallback_api_url = 'https://vx4nrbekbe5yqsuv52.pages.dev/demos.json';

                $response = wp_remote_get(esc_url_raw($primary_api_url), ['timeout' => 15]);

                if (is_wp_error($response)) {

                    $response = wp_remote_get(esc_url_raw($fallback_api_url), ['timeout' => 15]);

                    if (is_wp_error($response)) {
                        return new WP_Error('demo_api_error', __('Unable to fetch demo data from source.', 'ocean-extra'));
                    }
                }

                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);

                if (isset($data['templates']) && is_array($data['templates']) && count($data['templates']) > 0) {

                    $json_data = json_encode($data['templates']);

                    set_transient('ocean_onboarding_template_data', $json_data, DAY_IN_SECONDS);

                    return $json_data;
                }

                return json_encode([]);
            }

            return $cached_demos;
        }

        /**
         * Fetch OceanWP plugin data.
         *
         * @return string|WP_Error JSON encoded plugin data or WP_Error on failure.
         */
        public function fetch_ocean_plugin_data() {
            $transient_key = 'ocean_onboarding_plugin_data';
            $cached_plugins = get_transient($transient_key);


            // Return cached data if exists
            if ($cached_plugins) {
                return json_decode($cached_plugins, true);
            }

            // External sources
            $primary_api_url = 'https://demos.oceanwp.org/1wizard-installation/demo-plugins.json';
            $fallback_api_url = 'https://vx4nrbekbe5yqsuv52.pages.dev/demo-plugins.json';

            $response = wp_remote_get(esc_url_raw($primary_api_url), ['timeout' => 15]);

            // If the primary fails, try fallback
            if (is_wp_error($response)) {
                $response = wp_remote_get(esc_url_raw($fallback_api_url), ['timeout' => 15]);

                if (is_wp_error($response)) {
                    return new WP_Error('plugin_api_error', __('Unable to fetch plugin data from source.', 'ocean-extra'));
                }
            }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if (!empty($data) && is_array($data)) {
                set_transient($transient_key, json_encode($data), DAY_IN_SECONDS);
                return $data;
            }

            return json_encode([]);
        }
    }
}

OE_Onboarding_Site_Templates::instance();
