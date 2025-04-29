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
	 * @since  2.4.6
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
		 * Main OE_Setup_Wizard Instance
		 *
		 * @static
		 * @see OE_Setup_Wizard()
		 * @return Main OE_Setup_Wizard instance
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
			add_action( 'owp_onboarding_add_second_notice', [$this, 'install'] );
			//add_action( 'admin_notices', [$this, 'add_notice_launch_wizard'] );
			//add_action( 'wp_ajax_oceanwp_onboarding_wizard_dismiss_notice', [$this, 'onboarding_wizard_dismiss_notice'] );

		}

		public function owp_onboarding_existing_site_flags() {
			if ( ! get_option('owp_onboarding_completed') ) {
				update_option('owp_onboarding_completed', true);
			}

			if ( ! get_option('oceanwp_plugin_notice_permanently_dismissed') ) {
				update_option('oceanwp_plugin_notice_permanently_dismissed', true);
			}

			// delete_option('owp_onboarding_completed');
			// delete_option('owp_onboarding_skipped_finally');


			// error_log(print_r(get_option('owp_onboarding_completed'), true));
		}

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
		 * Install.
		 *
		 * @return void
		 */
		public static function install() {

			if ( ! get_option( 'owp_onboarding_wizard' ) ) {
				update_option( 'owp_onboarding_wizard', 'skip' );
			} else {
				// first run for automatic message after first 24 hour.
				if ( ! get_option( 'owp_onboarding_2nd_notice' ) ) {
					update_option( 'owp_onboarding_2nd_notice', 'final-skip' );
					delete_option( 'owp_onboarding_wizard_dismiss' );
				} else {
					// clear cronjob after second 24 hour.
					wp_clear_scheduled_hook( 'owp_onboarding_add_second_notice' );
					// delete_option( 'owp_onboarding_2nd_notice' );
					// delete_option( 'owp_onboarding_wizard' );
					// delete_option( 'owp_onboarding_wizard_dismiss' );
					exit;
				}
			}
		}

		/**
		 * Clear cronjob when deactivate plugin.
		 *
		 * @return void
		 */
		public static function uninstall() {
			wp_clear_scheduled_hook( 'owp_onboarding_add_second_notice' );
			delete_option( 'owp_onboarding_2nd_notice' );
			delete_option( 'owp_onboarding_wizard' );
			delete_option( 'owp_onboarding_wizard_dismiss' );
			delete_option( 'owp_onboarding_completed' );
		}

		/**
		 * Define cronjob
		 */
		public static function cronjob_activation() {
			$timezone_string = get_option( 'timezone_string' );
			if ( ! $timezone_string ) {
				return false;
			}
			date_default_timezone_set( $timezone_string );
			$new_time_format = time() + ( 24 * 60 * 60 );
			if ( ! wp_next_scheduled( 'owp_onboarding_add_second_notice' ) ) {
				wp_schedule_event( $new_time_format, 'daily', 'owp_onboarding_add_second_notice' );
			}
		}

		/**
		 * Delete cronjob
		 */
		public static function cronjob_deactivation() {
			wp_clear_scheduled_hook( 'owp_onboarding_add_second_notice' );

		}

		public static function add_notice_launch_wizard() {

			if ( get_option('owp_onboarding_completed') || get_option( 'oceanwp_plugin_notice_permanently_dismissed' ) ) {
				return;
			}

			if ( ! get_option( 'owp_onboarding_wizard_dismiss' ) ) {
				?>
				<div class="notice notice-success ocean-extra-notice owp-sticky-notice onboarding-dismiss">
					<div class="notice-inner">
						<span class="icon-side">
							<span class="owp-notification-icon">
								<img src="<?php echo esc_url(OE_URL . 'includes/themepanel/assets/img/themepanel-icon.svg'); ?>">
							</span>
						</span>
						<div class="notice-content">
							<div class="notice-content__area">
								<h2><?php echo esc_html__( 'Woohoo! Your website is nearly there — let\'s make it perfect!','ocean-extra' ); ?></h2>
								<h3 class="notice-subheading">
									<?php echo esc_html__( 'From Blank Page to Stunning Website—Follow Simple Steps to Create Something Amazing!', 'ocean-extra' ) ?>
								</h3>
								<p>
									<?php
									echo sprintf(
										esc_html__( 'The Onboarding Wizard is designed to make your website setup quick, easy, and hassle-free. With intuitive steps and smart suggestions tailored to your needs, this guide ensures you can create a stunning, fully-functional site in no time. Whether you\'re starting from scratch or fine-tuning an existing design, we\'ve got you covered. With clear instructions and helpful tips along the way, you\'ll be able to focus on what matters most—building a site that\'s perfect for you, without the stress! - %1$sLearn more%2$s', 'ocean-extra' ),
										'<a href="' . esc_url('https://oceanwp.org/core-extensions-bundle/') . '" target="_blank">',
										'</a>'
									);
									?>
								</p>
								<p>
									<a href="#" class="btn button-primary launch-onboarding-wizard">
										<span class="dashicons dashicons-airplane"></span>
										<span><?php _e( 'Launch Onboarding Wizard', 'ocean-extra' ); ?></span>
									</a>
								</p>
							</div>
							<div class="notice-content__image">
								<img src="<?php echo esc_url(OE_URL . 'includes/onboarding/assets/img/launch-wizard.png'); ?>">
							</div>
						</div>
						<a href="#" class="dismiss"><span class="dashicons dashicons-dismiss"></span></a>
					</div>
				</div>
				<?php
			}
		}

		/**
		 * Dismiss notice
		 */
		public function onboarding_wizard_dismiss_notice() {

			if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'owp-onboarding')) {
                wp_send_json_error(array(
                    'message' => esc_html__('Nonce verification failed.', 'ocean-extra')
                ));
            }

			$onboarding_wizard = get_option('owp_onboarding_wizard');
			$onboarding_2nd_notice = get_option('owp_onboarding_2nd_notice');

			if ($onboarding_wizard === 'skip') {
				update_option('owp_onboarding_wizard_dismiss', true);

				if (!$onboarding_2nd_notice) {
					// First time skipping
					$timezone_string = get_option('timezone_string');

					if ($timezone_string) {
						date_default_timezone_set($timezone_string);

						// Schedule event for the next day
						$next_run = time() + (24 * 60 * 60);

						if (!wp_next_scheduled('owp_onboarding_add_second_notice')) {
							wp_schedule_event($next_run, 'daily', 'owp_onboarding_add_second_notice');
						}
					}
				} elseif ($onboarding_2nd_notice === 'final-skip') {
					// If skipped for the second time, clear cronjob and remove notices
					delete_option('owp_onboarding_wizard');
					delete_option('owp_onboarding_2nd_notice');
					update_option('owp_onboarding_skipped_finally', true);
					wp_clear_scheduled_hook('owp_onboarding_add_second_notice');
				}
			}

			wp_send_json_success(array('message' => esc_html__('Notice dismissed successfully.', 'ocean-extra')));
		}

		/**
		 * Load required files
		 */
		public function includes() {

			$dir = OE_PATH . 'includes/onboarding/';

			require_once $dir . 'options.php';

			require_once $dir . 'class/child-theme.php';
			require_once $dir . 'class/plugin-manager.php';
			//require_once $dir . 'class/newsletter.php';
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
				array(),
				filemtime( OE_PATH . 'includes/onboarding/assets/dist/style-index.css' )
			);

			// wp_enqueue_style(
			// 	'oe-admin-notice',
			// 	OE_URL . 'includes/panel/assets/css/notice.min.css',
			// 	array(),
			// 	OE_VERSION
			// );

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

			return apply_filters(
				'ocean_onboarding_localize',
				array(
					'options' => oe_onboarding_wizard_options(),
					'childThemeStatus' => $child_theme_status,
					'siteUrl' => site_url(),
					'adminUrl' => admin_url(),
					'nonce' => wp_create_nonce( 'owp-onboarding' ),
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'isPremium' => $this->validate_license(),
					'admin_email' => wp_get_current_user()->user_email,
				)
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

		return OE_Onboarding_Wizard::instance();
	}
}

// Run the setup wizard.
oe_onboarding_wizard();

// register_activation_hook( OE_FILE_PATH, 'OE_Onboarding_Wizard::install' );
// // when deactivate plugin.
// //register_deactivation_hook( OE_FILE_PATH, 'OE_Onboarding_Wizard::uninstall' );
// // when activate plugin for automatic second notice.
// register_activation_hook( OE_FILE_PATH, array( 'OE_Onboarding_Wizard', 'cronjob_activation' ) );
// register_deactivation_hook( OE_FILE_PATH, array( 'OE_Onboarding_Wizard', 'cronjob_deactivation' ) );

// function dffs() {
// 	delete_option('oceanwp_plugin_notice_first_dismissed');
// 	delete_option('oceanwp_plugin_notice_permanently_dismissed');
// 	delete_option('owp_onboarding_completed');
// 	//delete_option('oceanwp_theme_installed_version');
// }
// add_action('init', 'dffs' );

