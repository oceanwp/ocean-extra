<?php
/**
 * Customizer
 *
 * @package OceanWP WordPress theme
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'OE_Customizer_Init' ) ) :

	/**
	 * Custom CSS / JS Customizer Class
	 */
	class OE_Customizer_Init {

		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_filter( 'ocean_customize_options_data', array( $this, 'register_customize_options') );
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'assets' ) );
			add_action( 'customize_preview_init', array( $this, 'assets_preloader' ) );
		}

		/**
		 * Register customizer options
		 */
		public function register_customize_options($options) {

			$options['ocean_info'] = [
				'title' => esc_html__('OceanWP Info', 'ocean-extra'),
				'priority' => 19,
				'options' => [
					'ocean_info_content' => [
						'type'      => 'ocean-content',
						'isContent' => $this->oe_render_info_content(),
						'section'   => 'ocean_info',
						'class'     => 'description',
						'transport' => 'postMessage',
						'priority'  => 10,
					]
				]
			];

			return $options;
		}

		public function assets() {

			$uri = OE_URL . 'includes/customizer/assets/';

			wp_enqueue_script(
				'oe-customize-script',
				$uri . 'script.min.js',
				[],
				OE_VERSION,
				false
			);

			wp_enqueue_style(
				'oe-customize-preloader',
				$uri . 'style.min.css',
				[],
				OE_VERSION
			);
		}

		public function assets_preloader() {

			$uri = OE_URL . 'includes/customizer/assets/';

			wp_enqueue_style(
				'oe-customize-preloader',
				$uri . 'style.min.css',
				[],
				OE_VERSION
			);
		}

		public function oe_render_info_content() {
			$check_icon = '<svg height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z"/></svg>';
			ob_start();
			?>

			<div class="ocean-info-container">
				<h3 class="info-heading"><?php echo esc_html__( 'Documentation', 'ocean-extra' ); ?></h3>
				<p><?php echo sprintf( esc_html__( 'OceanWP has detailed documentation and comprehensive user guides available to help you get results fast. %1$s View documentation. %2$s', 'ocean-extra' ), '<a href="https://see.oceanwp.org/tcinfo-preview-demos" target="_blank">', '</a>' ); ?></p>
			</div>

			<span class="info-divider"></span>

			<div class="ocean-info-container">
				<h3 class="info-heading"><?php echo esc_html__( 'Website Templates', 'ocean-extra' ); ?></h3>
				<p><?php echo sprintf( esc_html__( 'OceanWP provides a collection of pre-designed website templates (demos) to help jumpstart your project. %1$s View all available website templates. %2$s', 'ocean-extra' ), '<a href="https://see.oceanwp.org/tcinfo-preview-demos" target="_blank">', '</a>' ); ?></p>
			</div>

			<?php
			if ( function_exists( 'oe_pro_license_check' )
				&& true === oe_pro_license_check() ) {

				?>
					<span class="info-divider"></span>

					<div class="ocean-info-container">
						<h3 class="info-heading"><?php echo esc_html__( 'Dedicated Premium Support', 'ocean-extra' ); ?></h3>
						<p><?php echo sprintf( esc_html__( 'Elevate your experience with faster, expert and personalized email support available exclusively to %1$s OceanWP Pro Bundle %2$s and %3$s Ocean eCommerce Pro %2$s users. Upgrade today and get the best for your website.', 'ocean-extra' ), '<a href="https://see.oceanwp.org/tcinfo-bundle-upgrade" target="_blank">', '</a>', '<a href="https://see.oceanwp.org/tcinfo-ecommerce-upgrade" target="_blank">' ); ?></p>
					</div>
				<?php

			}
			?>

			<span class="info-divider"></span>

			<div class="ocean-info-container">
				<h3 class="info-heading"><?php echo esc_html__( 'Free User Support', 'ocean-extra' ); ?></h3>
				<p><?php echo sprintf( esc_html__( 'Receive free support for your website via  %1$s WordPress community forum  %2$s or  %3$s OceanWP official community on Facebook.  %2$s', 'ocean-extra' ), '<a href="https://wordpress.org/support/theme/oceanwp/" target="_blank">', '</a>', '<a href="https://www.facebook.com/groups/oceanwptheme" target="_blank">' ); ?></p>
			</div>

			<?php
			return ob_get_clean();
		}

	}

	return new OE_Customizer_Init();

endif;
