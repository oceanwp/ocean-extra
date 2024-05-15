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
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'assets' ) );
		}

        public function assets() {

            $uri   = OE_URL . 'includes/customizer/assets/';

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
                array(),
                OE_VERSION
            );
        }
    }

    return new OE_Customizer_Init();

endif;
