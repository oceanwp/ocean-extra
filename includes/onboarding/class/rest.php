<?php
/**
 * OceanWP Setup Wizard: Rest
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
 * Rest Controller class
 */
class OE_Onboarding_Rest_Controller extends WP_REST_Controller {

    /**
     * Instance.
     *
     * @access private
     * @var object Instance
     */
    private static $_instance;

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
     * Initiator
     *
     * @return object
     */
    public static function instance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Class constructor.
     */
    public function __construct() {

        add_action('rest_api_init', array($this, 'rest_api_init'));
    }

    /**
     * Register rest routes.
     */
    public function rest_api_init() {

        // Namespace.
        $namespace = $this->namespace . $this->version;

        register_rest_route(
            $namespace,
            '/onboarding/options',
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array($this, 'update_options'),
                'permission_callback' => array($this, 'update_permission'),
            )
        );

        register_rest_route(
            $namespace,
            '/onboarding/reset-site',
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array($this, 'reset_existing_site'),
                'permission_callback' => array($this, 'update_permission'),
            )
        );

        register_rest_route(
            $namespace,
            '/onboarding/reset-progress',
            array(
                'methods'  => 'GET',
                'callback' => array($this, 'get_progress'),
                'permission_callback' => array($this, 'update_permission'),
            )
        );

        register_rest_route(
            $namespace,
            '/onboarding/install-plugin',
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array($this, 'install_plugin_callback'),
                'permission_callback' => array($this, 'plugin_install_permission'),
            )
        );

        register_rest_route(
            $namespace,
            '/onboarding/activate-plugin',
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array($this, 'activate_plugin_callback'),
                'permission_callback' => array($this, 'plugin_install_permission'),
            )
        );

        register_rest_route(
            $namespace,
            '/onboarding/plugin-status',
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array($this, 'get_plugin_status_callback'),
                'permission_callback' => array($this, 'plugin_install_permission'),
            )
        );

        // register_rest_route(
        //     $namespace,
        //     '/onboarding/subscribe',
        //     array(
        //         'methods'             => WP_REST_Server::EDITABLE,
        //         'callback'            => array($this, 'get_newsletter_subscribe'),
        //         'permission_callback' => '__return_true',
        //     )
        // );

        register_rest_route(
            $namespace,
            '/onboarding/get-templates',
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'get_ocean_templates'),
                'permission_callback' => array($this, 'update_permission'),
            )
        );

        register_rest_route(
            $namespace,
            '/onboarding/sync-templates',
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array($this, 'sync_ocean_templates'),
                'permission_callback' => array($this, 'update_permission'),
            )
        );

        register_rest_route(
            $namespace,
            '/onboarding/select-template',
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array($this, 'select_ocean_template'),
                'permission_callback' => array($this, 'update_permission'),
            )
        );

        register_rest_route(
            $namespace,
            '/onboarding/get-plugins',
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'get_ocean_plugins'),
                'permission_callback' => array($this, 'update_permission'),
            )
        );

        // register_rest_route(
        //     $namespace,
        //     '/onboarding/template-data',
        //     array(
        //         'methods'             => WP_REST_Server::EDITABLE,
        //         'callback'            => array($this, 'get_template_data'),
        //         'permission_callback' => array($this, 'update_permission'),
        //     )
        // );

        // register_rest_route(
        //     $namespace,
        //     '/onboarding/import-content',
        //     array(
        //         'methods'             => WP_REST_Server::EDITABLE,
        //         'callback'            => array($this, 'process_import_content'),
        //         'permission_callback' => array($this, 'update_permission'),
        //     )
        // );

        register_rest_route(
            $namespace,
            '/onboarding/finish-setup/',
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array($this, 'update_finish_setup_flag'),
                'permission_callback' => array($this, 'update_permission'),
            )
        );
    }

    public function get_progress() {
        return new WP_REST_Response(['progress' => get_option('onboarding_wizard_cleanup_progress', 0)], 200);
    }

    /**
     * Get the status of requested plugins (from React)
     */
    public function get_plugin_status_callback(WP_REST_Request $request) {

        $plugin_manager = OE_Onboarding_Plugin_Manager::instance();

        $requested_plugins = $request->get_json_params()['plugins'] ?? [];

        if (!is_array($requested_plugins) || empty($requested_plugins)) {
            return new WP_REST_Response(['error' => 'No plugins provided'], 400);
        }

        $plugin_statuses = [];

        foreach ($requested_plugins as $plugin_slug) {
            $plugin_statuses[$plugin_slug] = $plugin_manager->get_plugin_status($plugin_slug);
        }

        return new WP_REST_Response($plugin_statuses, 200);
    }

    /**
     * Get edit options permission.
     *
     * @return bool
     */
    public function update_permission() {
        global $current_user;
        return current_user_can('manage_options');
    }

    /**
     * Manage Blocks.
     *
     * @param WP_REST_Request $request  Request object.
     *
     * @return mixed
     */
    public function update_options(WP_REST_Request $request) {
        $new_options = $request->get_param('options');

        if (is_array($new_options)) {

            if (isset($new_options['siteTitle']) && !empty($new_options['siteTitle'])) {
                update_option('blogname', $new_options['siteTitle'] );
            }

            if (isset($new_options['siteTagline']) && !empty($new_options['siteTagline'])) {
                update_option('blogdescription', $new_options['siteTagline'] );
            }

            if (isset($new_options['siteIcon']) && !empty($new_options['siteIcon'])) {
                update_option('site_icon', $new_options['siteIcon'] );
            }

            if (isset($new_options['siteLogo']) && !empty($new_options['siteLogo'])) {
                update_option('site_logo', $new_options['siteLogo'] );
            }

            if (isset($new_options['siteRetinaLogo']) && !empty($new_options['siteRetinaLogo'])) {
                set_theme_mod('ocean_retina_logo', $new_options['siteRetinaLogo'] );
            }

            if (isset($new_options['siteMobileLogo']) && !empty($new_options['siteMobileLogo'])) {
                set_theme_mod('ocean_responsive_logo', $new_options['siteMobileLogo'] );
            }

            // Colors.
            if (isset($new_options['backgroundColor']) && !empty($new_options['backgroundColor'])) {
                set_theme_mod('ocean_background_color', sanitize_hex_color($new_options['backgroundColor']) );
            }

            if (isset($new_options['primaryColor']) && !empty($new_options['primaryColor'])) {
                set_theme_mod('ocean_primary_color', sanitize_hex_color($new_options['primaryColor']) );
            }

            if (isset($new_options['primaryHoverColor']) && !empty($new_options['primaryHoverColor'])) {
                set_theme_mod('ocean_hover_primary_color', sanitize_hex_color($new_options['primaryHoverColor']) );
            }

            if (isset($new_options['borderColor']) && !empty($new_options['borderColor'])) {
                set_theme_mod('ocean_main_border_color', sanitize_hex_color($new_options['borderColor']) );
            }

            if (isset($new_options['linkColor']) && !empty($new_options['linkColor'])) {
                set_theme_mod('ocean_links_color', sanitize_hex_color($new_options['linkHoverColor']) );
            }

            if (isset($new_options['linkHoverColor']) && !empty($new_options['linkHoverColor'])) {
                set_theme_mod('ocean_links_color_hover', sanitize_hex_color($new_options['linkHoverColor']) );
            }

            if (isset($new_options['headingsFont']) && !empty($new_options['headingsFont'])) {

                $typography_headings = get_theme_mod('headings_typography', []);

                if (!is_array($typography_headings)) {
                    $typography_headings = [];
                }

                $typography_headings['font-family'] = $new_options['headingsFont'];
                set_theme_mod('headings_typography', $typography_headings);
            }

            if (isset($new_options['bodyFont']) && !empty($new_options['bodyFont'])) {

                $typography_body = get_theme_mod('body_typography', []);

                if (!is_array($typography_body)) {
                    $typography_body = [];
                }

                $typography_body['font-family'] = $new_options['bodyFont'];
                set_theme_mod('body_typography', $typography_body);
            }

        }

        return $this->success([
            'message'  => __('Settings updated successfully', 'ocean-extra'),
            'options'  => $new_options,
        ]);
    }

    /**
     * Reset Existing site.
     */
    public function reset_existing_site(WP_REST_Request $request) {

        $params = $request->get_json_params();
        $cleanup_options = isset($params['resetOptions']) ? $params['resetOptions'] : [];

        $total_tasks = count($cleanup_options);
        $completed_tasks = 0;

        if (in_array('pages', $cleanup_options)) {
            $this->delete_pages();
            $completed_tasks++;
            $this->send_progress_update($completed_tasks, $total_tasks);
        }

        if (in_array('menus', $cleanup_options)) {
            $this->delete_menus();
            $completed_tasks++;
            $this->send_progress_update($completed_tasks, $total_tasks);
        }

        if (in_array('customizer-settings', $cleanup_options)) {
            $this->reset_customizer_settings();
            $completed_tasks++;
            $this->send_progress_update($completed_tasks, $total_tasks);
        }

        if (in_array('disable-plugins', $cleanup_options)) {
            $this->deactivate_plugins();
            $completed_tasks++;
            $this->send_progress_update($completed_tasks, $total_tasks);
        }

        if (in_array('posts', $cleanup_options)) {
            $this->delete_posts();
            $completed_tasks++;
            $this->send_progress_update($completed_tasks, $total_tasks);
        }

        if (in_array('media', $cleanup_options)) {
            $this->delete_media();
            delete_option('site_icon');
            $completed_tasks++;
            $this->send_progress_update($completed_tasks, $total_tasks);
        }

        if (in_array('child-theme', $cleanup_options)) {
            if (class_exists('OE_Onboarding_Child_Theme')) {
                OE_Onboarding_Child_Theme::instance()->child_theme_manager();

                $completed_tasks++;
                $this->send_progress_update($completed_tasks, $total_tasks);
            }
        }

        return new WP_REST_Response(['success' => true, 'message' =>  'Cleanup Completed!'], 200);
    }

    /**
     * Send progress update
     */
    public function send_progress_update($completed, $total) {
        $progress = (int) (($completed / $total) * 100);
        update_option('onboarding_wizard_cleanup_progress', $progress);
    }

    /**
     * Delete pages
     */
    public function delete_pages() {
        $pages = get_posts(array(
            'post_type' => 'page',
            'numberposts' => -1
        ));

        foreach ($pages as $page) {
            wp_delete_post($page->ID, true);
        }
    }

    /**
     * Delete Menus
     */
    public function delete_menus() {
        $menu_locations = get_nav_menu_locations();

        foreach ($menu_locations as $location => $menu_id) {
            wp_delete_nav_menu($menu_id);
        }
    }

    /**
     * Delete customizer settings
     */
    public function reset_customizer_settings() {
        remove_theme_mods();
    }

    /**
     * Deactivate plugins
     */
    public function deactivate_plugins() {
        $active_plugins = get_option('active_plugins', []);

        foreach ($active_plugins as $plugin) {
            if ($plugin !== 'ocean-extra/ocean-extra.php') {
                deactivate_plugins($plugin);
            }
        }
    }

    /**
     * Delete posts
     */
    public function delete_posts() {
        $posts = get_posts(array(
            'post_type' => 'post',
            'numberposts' => -1
        ));

        foreach ($posts as $post) {
            wp_delete_post($post->ID, true);
        }
    }

    /**
     * Delete media
     */
    public function delete_media() {
        $media_files = get_posts(array(
            'post_type' => 'attachment',
            'numberposts' => -1
        ));

        foreach ($media_files as $media) {
            wp_delete_attachment($media->ID, true);
        }
    }

    /**
     * Install plugin
     */
    public function install_plugin_callback(WP_REST_Request $request) {
        $plugin_manager = OE_Onboarding_Plugin_Manager::instance();
        return $plugin_manager->install_plugin($request);
    }

    /**
     * Activate plugin.
     */
    public function activate_plugin_callback(WP_REST_Request $request) {
        $plugin_manager = OE_Onboarding_Plugin_Manager::instance();
        return $plugin_manager->activate_plugin($request);
    }

    /**
     * Permission check
     */
    public function plugin_install_permission() {
        $plugin_manager = OE_Onboarding_Plugin_Manager::instance();

        if (! $plugin_manager->can()) {
            return false;
        }

        return true;
    }

    /**
     * Newsletter subscribe
     */
    // public function get_newsletter_subscribe(WP_REST_Request $request) {
    //     $newsletter = OE_Onboarding_NewsLetter::instance();
    //     return $newsletter->onboarding_mailerlite_subscribe($request);
    // }

    /**
     * Select template
     */
    public function select_ocean_template(WP_REST_Request $request) {
        $install_template = OE_Onboarding_Site_Templates_Install::instance();
        return $install_template->get_template_data($request);
    }

    /**
     * Get template data
     */
    // public function get_template_data(WP_REST_Request $request) {
    //     $install_template = OE_Onboarding_Site_Templates_Install::instance();
    //     return $install_template->get_selected_template_data($request);
    // }

    /**
     * Get template data
     */
    // public function process_import_content(WP_REST_Request $request) {
    //     $install_template = OE_Onboarding_Site_Templates_Install::instance();
    //     return $install_template->import_content($request);
    // }

    /**
     * Finish setup flag
     */
    public function update_finish_setup_flag(WP_REST_Request $request) {
        update_option('owp_onboarding_completed', true);

        return rest_ensure_response(array(
            'success' => true,
            'message' => __('Setup completed successfully.', 'ocean-extra'),
        ));
    }

    public function get_ocean_templates(WP_REST_Request $request) {
        $site_templates = OE_Onboarding_Site_Templates::instance();

        $cached_demos = $site_templates->fetch_ocean_template_data();

        return new WP_REST_Response([
            'success' => !empty($cached_demos),
            'data'    => $cached_demos
        ], 200);
    }


    public function sync_ocean_templates(WP_REST_Request $request) {

        $data = OE_Onboarding_Site_Templates::instance()->fetch_ocean_template_data(true);

        return new WP_REST_Response([
            'success' => !empty($data),
            'data'    => $data
        ], 200);
    }

    public function get_ocean_plugins(WP_REST_Request $request) {
        $site_templates = OE_Onboarding_Site_Templates::instance();

        $cached_plugins = $site_templates->fetch_ocean_plugin_data();

        return new WP_REST_Response([
            'success' => !empty($cached_plugins),
            'data'    => $cached_plugins
        ], 200);
    }


    /**
     * Success rest.
     *
     * @param mixed $response response data.
     * @return mixed
     */
    public function success($response = array()) {
        return new WP_REST_Response(
            array(
                'success'  => true,
                'response' => $response,
            ),
            200
        );
    }

    /**
     * Error rest.
     *
     * @param mixed $code     error code.
     * @param mixed $response response data.
     * @return mixed
     */
    public function error($code, $response) {
        return new WP_REST_Response(
            array(
                'error'      => true,
                'success'    => false,
                'error_code' => $code,
                'response'   => $response,
            ),
            401
        );
    }
}

OE_Onboarding_Rest_Controller::instance();
