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
	final class OE_Onboarding_Wizard extends OE_Onboarding_Manager {

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

			// $this->includes();

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
			$asset = require OE_PATH . 'includes/onboarding/assets/dist/setup-wizard.asset.php';
			$deps  = $asset['dependencies'];

			wp_register_script(
				'oe-setup-wizard',
				$uri . 'setup-wizard.js',
				$deps,
				filemtime( OE_PATH . 'includes/onboarding/assets/dist/setup-wizard.js' ),
				true
			);

			wp_enqueue_script( 'oe-setup-wizard' );

			if ( function_exists( 'wp_set_script_translations' ) ) {
				wp_set_script_translations( 'oe-setup-wizard', 'ocean-extra' );
			}

			$loc_data = $this->localize_script();
			if ( is_array( $loc_data ) ) {
				wp_localize_script(
					'oe-setup-wizard',
					'oeOnboardingLoc',
					$loc_data
				);
			}
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
