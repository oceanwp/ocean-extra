<?php
/**
 * Dynamic date shortcode
 *
 * @package Ocean_Extra
 * @author OceanWP
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'OceanWP_Date_Shortcode' ) ) {

	/**
	 * Register OceanWP Date Shortcode Class.
	 */
	class OceanWP_Date_Shortcode {

		/**
		 * Start things up
		 *
		 * @since 1.1.8
		 */
		public function __construct() {
			add_shortcode( 'oceanwp_date', array( $this, 'date_shortcode' ) );
		}

		/**
		 * Registers the function as a shortcode
		 *
		 * @since 1.1.8
		 * @param  array  $atts    Date shortcode attributes.
		 * @param  string $content Date shortcode content.
		 * @return string
		 */
		public function date_shortcode( $atts, $content = null ) {
			$settings = shortcode_atts(
				array(
					'year' => '',
				),
				$atts
			);

			$year = $settings['year'];

			// Var.
			$date = '';

			if ( '' !== $year ) {
				$date .= $year . ' - ';
			}

			$date .= date( 'Y' );

			return esc_attr( $date );
		}

	}

}

new OceanWP_Date_Shortcode();
