<?php
/**
 * OceanWP Setup Wizard: Newsletter
 *
 * @package Ocean_Extra
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// The Setup Wizard newsletter class
if (!class_exists('OE_Onboarding_NewsLetter')) {

    /**
     * OE_Onboarding_NewsLetter.
     *
     * @since  2.4.6
     * @access public
     */
    final class OE_Onboarding_NewsLetter {

        /**
         * Class instance.
         *
         * @var object
         * @access private
         */
        private static $_instance = null;

        /**
         * OE_Onboarding_NewsLetter Instance
         *
         * @static
         * @return OE_Onboarding_NewsLetter instance
         */
        public static function instance() {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function onboarding_mailerlite_subscribe(WP_REST_Request $request) {

            $email = sanitize_email($request->get_param('email'));

            if (!$email) {
                return new WP_REST_Response(['message' => 'Invalid email'], 400);
            }

            $api_key = 'YOUR_MAILERLITE_API_KEY'; // Replace with your API Key
            $group_id = 'YOUR_GROUP_ID'; // Replace with your MailerLite group ID

            $data = [
                'email' => $email,
                'groups' => [$group_id],
            ];

            $response = wp_remote_post('https://connect.mailerlite.com/api/subscribers', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                ],
                'body'    => json_encode($data),
                'method'  => 'POST',
            ]);

            if (is_wp_error($response)) {
                return new WP_REST_Response(['message' => 'Failed to subscribe'], 500);
            }

            $response_code = wp_remote_retrieve_response_code($response);
            if ($response_code !== 200) {
                return new WP_REST_Response(['message' => 'MailerLite API Error'], $response_code);
            }

            return new WP_REST_Response(['message' => 'Successfully subscribed'], 200);
        }


    }
}

OE_Onboarding_NewsLetter::instance();
