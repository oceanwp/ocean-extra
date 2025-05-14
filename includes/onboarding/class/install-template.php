<?php
/**
 * OceanWP Setup Wizard: Install site templates
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
if (!class_exists('OE_Onboarding_Site_Templates_Install')) {

    /**
     * OE_Onboarding_Site_Templates_Install.
     *
     * @since  2.4.6
     * @access public
     */
    final class OE_Onboarding_Site_Templates_Install {

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

        public function get_template_data($request) {

            $params = $request->get_json_params();

            if (empty($params['selected_template'])) {
                return new WP_REST_Response(['success' => false, 'message' => __('No template selected', 'ocean-extra')], 400);
            }

            $selected_template = sanitize_text_field($params['selected_template']);

            $template_array = json_decode($selected_template, true);

            update_option('ocean_installing_template_data', $template_array);

            return new WP_REST_Response([
                'success' => true,
            ], 200);
        }
    }

}

OE_Onboarding_Site_Templates_Install::instance();
