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
if ( ! class_exists( 'OE_Install_Demo' ) ) {

	/**
	 * Main class - OE_Install_Demo.
	 *
	 * @since  2.4.8
	 * @access public
	 */
	final class OE_Install_Demo extends OE_Onboarding_Manager {

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

			// $this->includes();

            add_action( 'admin_footer', [$this, 'render_install_demo'] );
			add_action( 'admin_menu', [$this, 'add_page'], 999 );
            add_action( 'admin_enqueue_scripts', [$this, 'onboarding_scripts'], 120 );
		}

		/**
		 * Add submenu page
		 */
		public function add_page() {

			$title = esc_html__( 'Install Demo', 'ocean-extra' );

			add_submenu_page(
				'oceanwp',
				$title,
				$title,
				'manage_options',
				'#install-demo',
				''
			);
		}

        /**
         * Onboarding wizard
         */
        public function render_install_demo() {

            ?>
            <div id="oe-install-demo-app"></div>
            <?php
        }

		/**
		 * Enqueque Scripts
		 */
		public function onboarding_scripts() {

			$uri   = OE_URL . 'includes/onboarding/assets/dist/';
			$asset = require OE_PATH . 'includes/onboarding/assets/dist/install-demo.asset.php';
			$deps  = $asset['dependencies'];
			array_push($deps, 'wp-edit-post');

			wp_enqueue_media();

			wp_register_script(
				'oe-install-demo',
				$uri . 'install-demo.js',
				$deps,
				filemtime( OE_PATH . 'includes/onboarding/assets/dist/install-demo.js' ),
				true
			);

			wp_enqueue_script( 'oe-install-demo' );

			if ( function_exists( 'wp_set_script_translations' ) ) {
				wp_set_script_translations( 'oe-install-demo', 'ocean-extra' );
			}

			$loc_data = $this->localize_script();
			if ( is_array( $loc_data ) ) {
				wp_localize_script(
					'oe-install-demo',
					'oeOnboardingLoc',
					$loc_data
				);
			}
		}
	}
}

/**
 * Returns the main instance of OE_Install_Demo to prevent the need to use globals.
 *
 * @return object OE_Install_Demo
 */
function oe_install_demo() {

	if ( ! defined( 'OE_REGULAR_IMPORT_DEMO' ) ) {
		define( 'OE_REGULAR_IMPORT_DEMO', true );

		return OE_Install_Demo::instance();
	}
}

// Run the regular import.
oe_install_demo();
