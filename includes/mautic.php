<?php
/**
 * OceanWP Mautic Integration
 *
 * @package Ocean_Extra
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ocean Extra Mautic Integration
 */
class Ocean_Extra_Mautic {

    /**
     * Class instance.
     *
     * @var     object
     * @access  private
     */
    private static $_instance = null;

    /**
     * Main Ocean_Extra_Mautic Instance
     *
     * @static
     * @return Main Ocean_Extra_Mautic instance
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

        add_action('wp_ajax_oe_mautic_subscribe', [$this, 'ajax_subscribe']);
        add_action('wp_ajax_nopriv_oe_mautic_subscribe', [$this, 'ajax_subscribe']);
    }

    /**
     * AJAX handler
     */
    public function ajax_subscribe() {

        check_ajax_referer('owp-onboarding', 'security');

        $email = isset($_POST['email'])
            ? sanitize_email($_POST['email'])
            : '';

        $user_type = isset($_POST['user_type'])
            ? sanitize_text_field($_POST['user_type'])
            : 'free';

        if (empty($email) || !is_email($email)) {
            wp_send_json_error('Invalid email address.');
        }

        $result = $this->subscribe_to_mautic($email, $user_type);

        if ($result === true) {

            wp_send_json_success('Successfully subscribed!');
        }

        wp_send_json_error($result);
    }

    /**
     * Send data to Mautic
     */
    private function subscribe_to_mautic($email, $user_type) {

        $mautic_base_url = 'http://mautic.oceanwp.org';

        $forms = [
            'free' => 12,
            'pro'  => 13
        ];

        $form_id = isset($forms[$user_type]) ? $forms[$user_type] : $forms['free'];

        $submit_url = "{$mautic_base_url}/form/submit";

        $body = [
            "mauticform[email]"  => $email,
            "mauticform[formId]" => $form_id,
            "mauticform[return]" => '',
        ];

        $response = wp_remote_post($submit_url, [
            'method'  => 'POST',
            'timeout' => 15,
            'headers' => [
                'User-Agent' => 'OceanExtra-Onboarding/1.0'
            ],
            'body' => $body
        ]);

        if (is_wp_error($response)) {
            return esc_html__('Connection to mailing server failed.', 'ocean-extra');
        }

        $code = wp_remote_retrieve_response_code($response);

        if ($code !== 200) {
            return esc_html__('Subscription failed. Please try again.', 'ocean-extra');
        }

        return true;
    }
}

/**
 * Initialize
 */
return Ocean_Extra_Mautic::instance();
