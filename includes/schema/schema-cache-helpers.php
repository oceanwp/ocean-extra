<?php
/**
 * OceanWP Schema Cache Helper Functions
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

/**
 * Conditionally clears all JSON-LD schema cache transients on Customizer save.
 *
 * This function attaches a 'customize_save_after' action hook that will clear
 * all cached JSON-LD schema data from the database if:
 * - The filter 'oceanwp_schema_auto_clear_on_customize_save' returns true.
 * - And any of the following are disabled during the Customizer session:
 *   - Schema Markup ('ocean_schema_markup')
 *   - Schema Manager ('ocean_schema_manager')
 *   - Schema Cache ('ocean_schema_cache_enable')
 *
 * This helps prevent stale or orphaned schema cache when schema features are turned off.
 * The feature is opt-in via a filter to avoid performance penalties during normal saves.
 * 
 * Example usage via child theme: add_filter( 'oceanwp_schema_auto_clear_on_customize_save', '__return_true' );
 *
 * @since 2.6.0
 *
 * @return void
 */
if ( ! function_exists( 'oceanwp_auto_clear_schema_cache_on_customize_save' ) ) {
	function oceanwp_auto_clear_schema_cache_on_customize_save() {
		add_action( 'customize_save_after', function ( $wp_customize ) {

			// Allow users to enable this logic via filter.
			if ( ! apply_filters( 'oceanwp_schema_auto_clear_on_customize_save', false ) ) {
				return;
			}

			// Check if cache was previously enabled
			$cache_setting     = $wp_customize->get_setting( 'ocean_schema_cache_enable' );
			$old_cache_enabled = $cache_setting && isset( $cache_setting->previous ) ? (bool) $cache_setting->previous : false;

			if ( ! $old_cache_enabled ) {
				return;
			}

			// Get previous values.
			$markup_setting     = $wp_customize->get_setting( 'ocean_schema_markup' );
			$manager_setting    = $wp_customize->get_setting( 'ocean_schema_manager' );

			$old_markup_enabled  = $markup_setting && isset( $markup_setting->previous ) ? (bool) $markup_setting->previous : true;
			$old_manager_enabled = $manager_setting && isset( $manager_setting->previous ) ? (bool) $manager_setting->previous : false;

			// Get current values.
			$new_markup_enabled  = get_theme_mod( 'ocean_schema_markup', true );
			$new_manager_enabled = get_theme_mod( 'ocean_schema_manager', false );
			$new_cache_enabled   = get_theme_mod( 'ocean_schema_cache_enable', false );

			// If any relevant settings were just disabled.
			$markup_disabled_now  = $old_markup_enabled  && ! $new_markup_enabled;
			$manager_disabled_now = $old_manager_enabled && ! $new_manager_enabled;
			$cache_disabled_now   = $old_cache_enabled   && ! $new_cache_enabled;

			if ( $markup_disabled_now || $manager_disabled_now || $cache_disabled_now ) {
				global $wpdb;
				$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_oceanwp_jsonld_%'" );
				$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_oceanwp_jsonld_%'" );
			}
		});
	}
	add_action( 'init', 'oceanwp_auto_clear_schema_cache_on_customize_save' );
}
