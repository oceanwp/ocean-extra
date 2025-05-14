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
if ( ! class_exists( 'OE_Onboarding_Wizard' ) ) {

	/**
	 * Main class - OE_Onboarding_Wizard.
	 *
	 * @since  2.4.8
	 * @access public
	 */
	final class OE_Onboarding_Wizard {

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

			$installed_version = get_option( 'ocean-extra-installed-version' );

			if ( !empty($installed_version) && defined('OE_VERSION') && version_compare( $installed_version, OE_VERSION, '<' ) ) {
				add_action( 'plugin_loaded', [$this, 'owp_onboarding_existing_site_flags']);
				return;
			}

			$this->includes();

			add_action( 'admin_menu', [$this, 'add_page'], 999 );
            add_action( 'admin_footer', [$this, 'onboarding_app'] );
            add_action( 'admin_enqueue_scripts', [$this, 'onboarding_scripts'], 120 );
		}

		/**
		 * Onboarding flags for existing sites
		 *
		 * @return void
		 */
		public function owp_onboarding_existing_site_flags() {
			if ( ! get_option('owp_onboarding_completed') ) {
				update_option('owp_onboarding_completed', true);
			}

			if ( ! get_option('oceanwp_plugin_notice_permanently_dismissed') ) {
				update_option('oceanwp_plugin_notice_permanently_dismissed', true);
			}
		}

		/**
		 * Add submenu page
		 */
		public function add_page() {

			if ( get_option( 'owp_onboarding_completed' ) || get_option( 'oceanwp_plugin_notice_permanently_dismissed' )) {
				return;
			}

			$title = esc_html__( 'Setup Wizard', 'ocean-extra' );

			add_submenu_page(
				'oceanwp',
				$title,
				$title,
				'manage_options',
				'#setup-wizard',
				''
			);
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

		}

        /**
         * Onboarding wizard
         */
        public function onboarding_app() {

			if ( get_option('owp_onboarding_completed') || get_option('oceanwp_plugin_notice_permanently_dismissed') ) {
				return;
			}

            ?>
            <div id="oe-onboarding-app"></div>
            <?php
        }

		/**
		 * Enqueque Scripts
		 */
		public function onboarding_scripts() {

			if ( get_option('owp_onboarding_completed') || get_option('oceanwp_plugin_notice_permanently_dismissed') ) {
				return;
			}

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
		 * Localize Script.
		 *
		 * @return mixed|void
		 */
		public function localize_script() {

			if ( get_option('owp_onboarding_completed')|| get_option('oceanwp_plugin_notice_permanently_dismissed') ) {
				return;
			}

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

/**
 * Returns the main instance of OE_Onboarding_Wizard to prevent the need to use globals.
 *
 * @return object OE_Onboarding_Wizard
 */
function oe_onboarding_wizard() {

	if ( ! defined( 'OE_ONBOARDING_WIZARD' ) ) {
		define( 'OE_ONBOARDING_WIZARD', true );

		if ( ! defined( 'OE_ONBOARDING_WIZARD_VERSION' ) ) {
			define( 'OE_ONBOARDING_WIZARD_VERSION', '2.4.8' );
		}

		return OE_Onboarding_Wizard::instance();
	}
}

// Run the setup wizard.
oe_onboarding_wizard();
