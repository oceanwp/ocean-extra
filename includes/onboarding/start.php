<?php
/**
 * OceanWP Setup Wizard
 *
 * @package Ocean_Extra
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// The Setup Wizard class
if ( ! class_exists( 'OE_Onboarding_Manager' ) ) {

	/**
	 * Main class - OE_Onboarding_Manager.
	 *
	 * @since  2.4.8
	 * @access public
	 */
	class OE_Onboarding_Manager {

		/**
		 * Class instance.
		 *
		 * @var     object
		 * @access  private
		 */
		private static $_instance = null;

		/**
		 * Main OE_Onboarding_Wizard Instance
		 *
		 * @static
		 * @see OE_Onboarding_Wizard()
		 * @return Main OE_Onboarding_Wizard instance
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

			add_action( 'admin_enqueue_scripts', [$this, 'onboarding_scripts'] );
		}


		/**
		 * Load required files
		 */
		public function includes() {

			$dir = OE_PATH . 'includes/onboarding/';

			require_once $dir . 'options.php';

			require_once $dir . 'class/child-theme.php';
			require_once $dir . 'class/plugin-manager.php';
			require_once $dir . 'class/site-template.php';
			require_once $dir . 'class/install-template.php';
			require_once $dir . 'class/parser.php';
			require_once $dir . 'class/importer/wp-importer.php';
			require_once $dir . 'class/importer/theme-settings.php';
			require_once $dir . 'class/importer/widgets.php';
			require_once $dir . 'class/importer/wpforms.php';
			require_once $dir . 'class/import-data.php';
			require_once $dir . 'class/rest.php';

			require_once $dir . 'install-demo/start.php';
			require_once $dir . 'setup-wizard/start.php';

		}

		/**
		 * Check if user need to upgrade.
		 *
		 * @return bool
		 */
		public function validate_license() {
			global $owp_fs;
			$status = false;
			if ( ! empty( $owp_fs ) ) {
				$status = $owp_fs->is_pricing_page_visible();
			} else {
				$status = false;
			}

			return $status;
		}

		/**
		 * Enqueque Scripts
		 */
		public function onboarding_scripts() {

			$uri   = OE_URL . 'includes/onboarding/assets/dist/';
			$asset = require OE_PATH . 'includes/onboarding/assets/dist/index.asset.php';
			$deps  = $asset['dependencies'];
			array_push($deps, 'wp-edit-post');

			wp_enqueue_media();

			wp_register_script(
				'oe-onboarding',
				$uri . 'index.js',
				$deps,
				filemtime( OE_PATH . 'includes/onboarding/assets/dist/index.js' ),
				true
			);

			wp_enqueue_style(
				'oe-onboarding',
				$uri . 'style-index.css',
				[],
				filemtime( OE_PATH . 'includes/onboarding/assets/dist/style-index.css' )
			);

			wp_enqueue_style(
				'oe-onboarding-component',
				$uri . 'style-setup-wizard.css',
				[],
				filemtime( OE_PATH . 'includes/onboarding/assets/dist/style-setup-wizard.css' )
			);

			wp_enqueue_script( 'oe-onboarding' );

			if ( function_exists( 'wp_set_script_translations' ) ) {
				wp_set_script_translations( 'oe-onboarding', 'ocean-extra' );
			}

			$loc_data = $this->localize_script();
			if ( is_array( $loc_data ) ) {
				wp_localize_script(
					'oe-onboarding',
					'oeOnboardingLoc',
					$loc_data
				);
			}
		}

		/**
		 * Localize Script.
		 *
		 * @return mixed|void
		 */
		public function localize_script() {

			$theme_slug = 'oceanwp-child-theme-master';
			$child_theme_status = [
				'installed' => wp_get_theme($theme_slug)->exists(),
				'active'    => get_option('stylesheet') === $theme_slug,
			];

			$colorMode = get_transient('oe_onboarding_color_mode');
			$colorMode = $colorMode ? $colorMode : 'light';

			return apply_filters(
				'ocean_onboarding_localize',
				[
					'options' => oe_onboarding_wizard_options(),
					'childThemeStatus' => $child_theme_status,
					'siteUrl' => esc_url(site_url()),
					'homeUrl' => esc_url(home_url()),
					'adminUrl' => esc_url(admin_url()),
					'nonce' => wp_create_nonce( 'owp-onboarding' ),
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'isPremium' => $this->validate_license(),
					'admin_email' => wp_get_current_user()->user_email,
					'colorMode' => $colorMode,
					'upgradeImage' => esc_url(OE_URL . 'includes/onboarding/assets/img/onboarding-upgrade-banner.jpg'),
				]
			);
		}
	}
}

return OE_Onboarding_Manager::instance();
