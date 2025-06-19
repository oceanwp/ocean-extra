<?php
/**
 * OceanWP WooCommerce JSON Schema
 *
 * @package   Ocean_Extra
 * @category  Core
 * @link      https://oceanwp.org/
 * @author    OceanWP
 * @since     2.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (
	! class_exists( 'OceanWP_WooCommerce_Schema') &&
	class_exists( 'OceanWP_JsonLD_Schema' ) &&
	OCEANWP_WOOCOMMERCE_ACTIVE
) {

	class OceanWP_WooCommerce_Schema {

		/**
		 * Constructor.
		 */
		public function __construct() {
			// Register Customizer schema options.
			add_filter( 'ocean_customize_options_data', [ $this, 'register_customize_options' ] );
		}

		/**
		 * Get schema for single WooCommerce product.
		 */
		public static function get_single_product_schema() {
			if ( ! function_exists( 'is_product' ) || ! is_product() ) {
				return null;
			}

			global $product;
			if ( ! $product instanceof WC_Product ) {
				$product = wc_get_product();
			}

			if ( ! $product ) {
				return null;
			}

			$data = [
				'@context'        => 'https://schema.org',
				'@type'           => 'Product',
				'name'            => $product->get_name(),
				'description'     => wp_strip_all_tags( $product->get_short_description() ?: $product->get_description() ),
				'sku'             => $product->get_sku(),
				'url'             => get_permalink( $product->get_id() ),
				'image'           => wp_get_attachment_url( $product->get_image_id() ),
				'brand'           => [
					'@type' => 'Brand',
					'name'  => get_bloginfo( 'name' ),
				],
			];

			// Offer
			if ( $product->get_price() ) {
				$data['offers'] = [
					'@type'         => 'Offer',
					'priceCurrency' => get_woocommerce_currency(),
					'price'         => $product->get_price(),
					'availability'  => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
					'url'           => get_permalink( $product->get_id() ),
				];
			}

			// Reviews
			if ( wc_review_ratings_enabled() && $product->get_review_count() ) {
				$data['aggregateRating'] = [
					'@type'       => 'AggregateRating',
					'ratingValue'=> $product->get_average_rating(),
					'reviewCount'=> $product->get_review_count(),
				];
			}

			return $data;
		}

		/**
		 * Get schema for shop main page.
		 */
		public static function get_shop_page_schema() {
			if ( ! function_exists( 'is_shop' ) || ! is_shop() ) {
				return null;
			}

			$page_id = wc_get_page_id( 'shop' );
			$title   = get_the_title( $page_id );
			$desc    = get_the_excerpt( $page_id ) ?: get_bloginfo( 'description' );
			$url     = get_permalink( $page_id );

			return [
				'@context'    => 'https://schema.org',
				'@type'       => 'CollectionPage',
				'name'        => esc_html( $title ),
				'description' => esc_html( wp_strip_all_tags( $desc ) ),
				'url'         => esc_url( $url ),
			];
		}

		/**
		 * Get schema for product categories or tags.
		 */
		public static function get_product_taxonomy_schema() {
			if ( ! is_product_category() || ! is_product_tag() ) {
				return null;
			}

			$term = get_queried_object();
			if ( ! $term ) {
				return null;
			}

			return [
				'@context'    => 'https://schema.org',
				'@type'       => 'CollectionPage',
				'name'        => esc_html( $term->name ),
				'description' => esc_html( wp_strip_all_tags( term_description( $term ) ) ),
				'url'         => esc_url( get_term_link( $term ) ),
			];
		}

		/**
		 * Customizer Settings
		 */
		public function register_customize_options($options) {

			$options['ocean_seo_settings']['options'] = [

				'ocean_schema_woocommerce_enable' => [
					'type'              => 'ocean-switch',
					'label'             => esc_html__( 'Enable WooCommerce Schema', 'ocean-extra' ),
					'section'           => 'ocean_seo_settings',
					'after'             => 'oe_schema_caching',
					'default'           => false,
					'transport'         => 'postMessage',
					'priority'          => 10,
					'hideLabel'         => false,
					'active_callback'   => function() {
						return function_exists( 'oceanwp_cac_is_schema_manager_enabled' )
							? oceanwp_cac_is_schema_manager_enabled()
							: false;
					},
					'sanitize_callback' => 'oceanwp_sanitize_checkbox',
				],
			];

			return $options;
		}
	}
}
