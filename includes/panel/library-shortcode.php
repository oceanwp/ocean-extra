<?php
/**
 * My Library Shortcode
 *
 * @package   Ocean_Extra
 * @category  Core
 * @author    OceanWP
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'OceanWP_Library_Shortcode' ) ) {

	class OceanWP_Library_Shortcode {

		/**
		 * Start things up
		 */
		public function __construct() {
			add_shortcode( 'oceanwp_library', array( $this, 'library_shortcode' ) );
		}

		/**
		 * Registers the function as a shortcode
		 */
		public function library_shortcode( $atts, $content = null ) {

			// Attributes
			$atts = shortcode_atts( array(
				'id' => '',
			), $atts, 'oceanwp_library' );

			$id = absint( $atts['id'] );

			if ( ! $id ) {
				return '';
			}

			$owp_post_type   = get_post_type( $id );
			$owp_post_status = get_post_status( $id );

			if ( $owp_post_type !== 'oceanwp_library' || $owp_post_status !== 'publish' ) {
				return '';
			}

			ob_start();

			// Check if the template is created with Elementor
			$elementor = get_post_meta( $id, '_elementor_edit_mode', true );

			// If Elementor
			if ( class_exists( 'Elementor\Plugin' ) && $elementor ) {

				echo Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $id );

			}

			// If Beaver Builder.
			else if ( class_exists( 'FLBuilder' ) && ! empty( $id ) ) {

				echo do_shortcode( '[fl_builder_insert_layout id="' . esc_attr( $id )  . '"]' );

			}

			// if SiteOrigin.
			else if ( class_exists( 'SiteOrigin_Panels' ) && get_post_meta( $id, 'panels_data', true ) ) {

				echo SiteOrigin_Panels::renderer()->render( $id );

			}

			// Else
			else {

				// Get template content
				$content = '';

				if ( ! empty( $id ) ) {

					$template = get_post( $id );

					if ( is_object( $template ) && ! is_wp_error( $template ) ) {
						$content = $template->post_content;
					}

					// If Gutenberg.
					if ( function_exists( 'ocean_is_block_template' ) && ocean_is_block_template( $id ) ) {
						$content = apply_filters( 'oe_library_shortcode_template_content', do_blocks( $content ) );
					}
				}

				// Display template content.
				if ( ! empty( $content ) ) {
					echo do_shortcode( $content );
				}

			}

			return ob_get_clean();

		}

	}

}
new OceanWP_Library_Shortcode();