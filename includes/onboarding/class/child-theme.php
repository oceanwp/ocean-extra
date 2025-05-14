<?php
/**
 * OceanWP Setup Wizard: Child theme manager
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
 * Child theme manager class
 */
if ( ! class_exists( 'OE_Onboarding_Child_Theme' ) ) {

	/**
	 * OE_Onboarding_Child_Theme.
	 *
	 * @since  2.4.6
	 * @access public
	 */
	final class OE_Onboarding_Child_Theme {

		/**
		 * Class instance.
		 *
		 * @var     object
		 * @access  private
		 */
		private static $_instance = null;

		/**
		 * OE_Onboarding_Child_Theme Instance
		 *
		 * @static
		 * @see OE_Onboarding_Child_Theme()
		 * @return Main OE_Onboarding_Child_Theme instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

        /**
		 * Handles the installation and activation of the child theme.
		 *
		 * @return void|WP_REST_Response Returns a REST response in case of failure.
		 */
        public function child_theme_manager() {

            $theme_slug = 'oceanwp-child-theme-master';
            $download_url = 'https://downloads.oceanwp.org/oceanwp/oceanwp-child-theme.zip';

            if ($this->is_child_theme_installed($theme_slug)) {
                if (!$this->is_child_theme_active($theme_slug)) {
                   switch_theme($theme_slug);
                }

                return new WP_REST_Response(['success' => true, 'message' => 'Child theme activated.'], 200);
    		}

            $install_result = $this->download_and_install_child_theme($theme_slug, esc_url_raw($download_url));

            if (is_wp_error($install_result)) {

				$fallback_install = $this->generate_child_theme_manually();

				if (is_wp_error($fallback_install)) {
					return new WP_REST_Response(['error' => __('Failed to install child theme. Please install it manually.', 'ocean-extra')], 500);
				}

				switch_theme($theme_slug);

				return new WP_REST_Response(['success' => true, 'message' => __('Child theme manually generated and activated.', 'ocean-extra')], 200);
			}

           	switch_theme($theme_slug);
			return new WP_REST_Response(['success' => true, 'message' => __('Child theme installed and activated.', 'ocean-extra')], 200);
        }

        /**
		 * Checks if the child theme is already installed.
		 *
		 * @param string $theme_slug Child theme slug.
		 * @return bool True if installed
		 */
        public function is_child_theme_installed($theme_slug) {
            $theme = wp_get_theme($theme_slug);
            return $theme->exists();
        }

        /**
		 * Checks if the child theme is currently active.
		 *
		 * @param string $theme_slug The slug of the child theme.
		 * @return bool True if the child theme is active, false otherwise.
		 */
        public function is_child_theme_active($theme_slug) {
            return get_option('stylesheet') === $theme_slug;
        }

        /**
		 * Downloads and installs the child theme.
		 *
		 * @param string $theme_slug Child theme slug.
		 * @param string $download_url Child theme ZIP file url.
		 * @return bool|WP_Error True on success.
		 */
        public function download_and_install_child_theme($theme_slug, $download_url) {

			if ($this->is_child_theme_installed($theme_slug)) {
				return new WP_REST_Response([
					'error' => __('Child theme already installed.', 'ocean-extra')
				], 400);
			}

			$upload_dir = wp_upload_dir();
			$zip_file = $upload_dir['basedir'] . '/' . $theme_slug . '.zip';
			$theme_directory = get_theme_root();

			$response = wp_remote_get($download_url, array('timeout' => 30));

			if (is_wp_error($response)) {
				return new WP_REST_Response([
					'error' => __('Failed to download child theme.', 'ocean-extra')
				], 500);
			}

			file_put_contents($zip_file, wp_remote_retrieve_body($response));

			if (!file_exists($zip_file)) {
				return new WP_REST_Response([
					'error' => __('Downloaded file not found.', 'ocean-extra')
				], 400);
			}

			$zip = new ZipArchive();
			$zip_open_result = $zip->open($zip_file);

			if ($zip_open_result !== true) {
				unlink($zip_file);
				return new WP_REST_Response([
					'error' => sprintf(__('Zip failed to open: %s.', 'ocean-extra'), $zip->getStatusString()),
				], 500);
			}

			$zip->extractTo($theme_directory);
			$zip->close();

			if (!file_exists($theme_directory . $theme_slug)) {
				unlink($zip_file);
				return new WP_REST_Response([
					'error' => __('Failed to extract child theme.', 'ocean-extra')
				], 500);
			}

			unlink($zip_file);

			return new WP_REST_Response([
				'success' => __('Child theme installed and activated successfully.', 'ocean-extra')
			], 200);
		}

		/**
		 *  Fallback Method: Generate Child Theme Manually
		 */
		public function generate_child_theme_manually() {
			$theme = wp_get_theme();
			$name = $theme->get('Name') . ' Child Theme';
			$slug = sanitize_title($name) . '-master';
			$path = get_theme_root() . '/' . $slug;
			$version = $theme->get( 'Version' );

			WP_Filesystem();
			global $wp_filesystem;

			if (!$wp_filesystem->exists($path)) {
				$wp_filesystem->mkdir($path);

				$wp_filesystem->put_contents($path . '/style.css', "
				/*
				Theme Name: $name
				Description: Child theme for {$theme->get('Name')}
				Theme URI: https://oceanwp.org/
				Author: {$theme->get('Author')}
				Template: {$theme->get('Template')}
				Author URI: https://oceanwp.org/
				Version: 1.0
				*/
				");

				$wp_filesystem->put_contents($path . '/functions.php', "<?php
				add_action('wp_enqueue_scripts', function() {
					wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'oceanwp-style' ), $version );
				});

				");

				$this->make_screenshot($path);
				$allowed_themes = get_option('allowedthemes');
				$allowed_themes[$slug] = true;

				update_option('allowedthemes', $allowed_themes);
			}
		}

		/**
		 * Generate screenshot on the go.
		 */
		private function make_screenshot($path) {
			$base_path = get_parent_theme_file_path();

			global $wp_filesystem;

			if ($wp_filesystem->exists($base_path . '/screenshot.png')) {
				$screenshot = $base_path . '/screenshot.png';
				$screenshot_ext = 'png';
			} elseif ($wp_filesystem->exists($base_path . '/screenshot.jpg')) {
				$screenshot = $base_path . '/screenshot.jpg';
				$screenshot_ext = 'jpg';
			}

			if (! empty($screenshot) && $wp_filesystem->exists($screenshot)) {
				$copied = $wp_filesystem->copy(
					$screenshot,
					$path . '/screenshot.' . $screenshot_ext
				);
			}
		}
	}
}

OE_Onboarding_Child_Theme::instance();
