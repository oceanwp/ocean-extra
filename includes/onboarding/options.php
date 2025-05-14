<?php
/**
 * OceanWP Setup Wizard: Options
 *
 * @package Ocean_Extra
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the options for the onboarding wizard.
 *
 * @since 2.4.8
 * @return array
 */
function oe_onboarding_wizard_options() {

    $headings_typo         = get_theme_mod('headings_typography');
	$headings_font_familiy = isset($headings_typo['font-family']) ? $headings_typo['font-family'] : '';

    $body_typo             = get_theme_mod('body_typography');
	$body_font_familiy     = isset($body_typo['font-family']) ? $body_typo['font-family'] : '';

    $options = [
        'siteTitle'         => get_option( 'blogname' ),
        'siteTagline'       => get_option( 'blogdescription' ),
        'siteIcon'          => get_option( 'site_icon' ),
        'siteLogo'          => get_option( 'site_logo' ),
        'siteRetinaLogo'    => get_theme_mod( 'ocean_retina_logo' ),
        'siteMobileLogo'    => get_theme_mod( 'ocean_responsive_logo' ),
        'backgroundColor'   => get_theme_mod( 'ocean_background_color', '#ffffff' ),
        'primaryColor'      => get_theme_mod( 'ocean_primary_color', '#13aff0' ),
        'primaryHoverColor' => get_theme_mod( 'ocean_hover_primary_color', '#0b7cac' ),
        'borderColor'       => get_theme_mod( 'ocean_main_border_color', '#e9e9e9' ),
        'linkColor'         => get_theme_mod( 'ocean_links_color', '#333333' ),
        'linkHoverColor'    => get_theme_mod( 'ocean_links_color_hover', '#13aff0' ),
        'headingsFont'      => $headings_font_familiy,
        'bodyFont'          => $body_font_familiy,
    ];

    return apply_filters( 'ocean_onboarding_wizard_options', $options );
}
