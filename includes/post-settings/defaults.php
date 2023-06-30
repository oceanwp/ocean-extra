<?php
/**
 * OceanWP Post settings default value
 *
 * @package Ocean_Extra
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ocean_post_setting_data() {

	$defaults = array(

		// General.
		'_ocean_meta_post_layout' => array(
			'map'    => 'ocean_post_layout',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_both_sidebars_style' => array(
			'map'  => 'ocean_both_sidebars_style',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_both_sidebars_content_width' => array(
			'map'  => 'ocean_both_sidebars_content_width',
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'value' => null
		),
		'_ocean_meta_both_sidebars_sidebars_width' => array(
			'map'  => 'ocean_both_sidebars_sidebars_width',
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'value' => null
		),
		'_ocean_meta_sidebar' => array(
			'map'  => 'ocean_sidebar',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_second_sidebar' => array(
			'map'  => 'ocean_second_sidebar',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_disable_margins' => array(
			'map'  => 'ocean_disable_margins',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => 'enable'
		),
		'_ocean_meta_add_body_class' => array(
			'map'  => 'ocean_add_body_class',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),

		// Shortcode.
		'_ocean_meta_shortcode_before_top_bar' => array(
			'map'  => 'ocean_shortcode_before_top_bar',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_shortcode_after_top_bar' => array(
			'map'  => 'ocean_shortcode_after_top_bar',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_shortcode_before_header' => array(
			'map'  => 'ocean_shortcode_before_header',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_shortcode_after_header' => array(
			'map'  => 'ocean_shortcode_after_header',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_shortcode_before_title' => array(
			'map'  => 'ocean_has_shortcode',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_shortcode_after_title' => array(
			'map'  => 'ocean_shortcode_after_title',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_shortcode_before_footer_widgets' => array(
			'map'  => 'ocean_shortcode_before_footer_widgets',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_shortcode_after_footer_widgets' => array(
			'map'  => 'ocean_shortcode_after_footer_widgets',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_shortcode_before_footer_bottom' => array(
			'map'  => 'ocean_shortcode_before_footer_bottom',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_shortcode_after_footer_bottom' => array(
			'map'  => 'ocean_shortcode_after_footer_bottom',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),

		// Header.
		'_ocean_meta_display_top_bar' => array(
			'map'  => 'ocean_display_top_bar',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => 'default'
		),
		'_ocean_meta_display_header' => array(
			'map'  => 'ocean_display_header',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => 'default'
		),
		'_ocean_meta_header_style' => array(
			'map'  => 'ocean_header_style',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_center_header_left_menu' => array(
			'map'  => 'ocean_center_header_left_menu',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_custom_header_template' => array(
			'map'  => 'ocean_custom_header_template',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),

		// Logo.
		'_ocean_meta_custom_logo' => array(
			'map'  => 'ocean_custom_logo',
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_custom_retina_logo' => array(
			'map'  => 'ocean_custom_retina_logo',
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_custom_logo_max_width' => array(
			'map'  => 'ocean_custom_logo_max_width',
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'value' => null
		),
		'_ocean_meta_custom_logo_tablet_max_width' => array(
			'map'  => 'ocean_custom_logo_tablet_max_width',
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'value' => null
		),
		'_ocean_meta_custom_logo_mobile_max_width' => array(
			'map'  => 'ocean_custom_logo_mobile_max_width',
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'value' => null
		),
		'_ocean_meta_custom_logo_max_height' => array(
			'map'  => 'ocean_custom_logo_max_height',
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'value' => null
		),
		'_ocean_meta_custom_logo_tablet_max_height' => array(
			'map'  => 'ocean_custom_logo_tablet_max_height',
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'value' => null
		),
		'_ocean_meta_custom_logo_mobile_max_height' => array(
			'map'  => 'ocean_custom_logo_mobile_max_height',
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'value' => null
		),

		// Menu.
		'_ocean_meta_header_custom_menu' => array(
			'map'  => 'ocean_header_custom_menu',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),

		'_ocean_meta_menu_typo_font_family' => array(
			'map'  => 'ocean_menu_typo_font_family',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_typo_font_subset' => array(
			'map'  => '',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_typo_font_size' => array(
			'map'  => 'ocean_menu_typo_font_size',
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_typo_font_size_tablet' => array(
			'map'  => '',
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_typo_font_size_mobile' => array(
			'map'  => '',
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_typo_font_size_unit' => array(
			'map'  => '',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_typo_font_weight' => array(
			'map'  => 'ocean_menu_typo_font_weight',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_typo_font_weight_tablet' => array(
			'map'  => '',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_typo_font_weight_mobile' => array(
			'map'  => '',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_typo_transform' => array(
			'map'  => 'ocean_menu_typo_transform',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_typo_transform_tablet' => array(
			'map'  => '',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_typo_transform_mobile' => array(
			'map'  => '',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_typo_line_height' => array(
			'map'  => 'ocean_menu_typo_line_height',
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_typo_line_height_tablet' => array(
			'map'  => '',
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_typo_line_height_mobile' => array(
			'map'  => '',
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_typo_line_height_unit' => array(
			'map'  => '',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_typo_spacing' => array(
			'map'  => 'ocean_menu_typo_spacing',
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_typo_spacing_tablet' => array(
			'map'  => '',
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_typo_spacing_mobile' => array(
			'map'  => '',
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_typo_spacing_unit' => array(
			'map'  => '',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_link_color' => array(
			'map'  => 'ocean_menu_link_color',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_link_color_hover' => array(
			'map'  => 'ocean_menu_link_color_hover',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_link_color_active' => array(
			'map'  => 'ocean_menu_link_color_active',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_link_background' => array(
			'map'  => 'ocean_menu_link_background',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_link_hover_background' => array(
			'map'  => 'ocean_menu_link_hover_background',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_link_active_background' => array(
			'map'  => 'ocean_menu_link_active_background',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_social_links_bg' => array(
			'map'  => 'ocean_menu_social_links_bg',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_social_hover_links_bg' => array(
			'map'  => 'ocean_menu_social_hover_links_bg',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_social_links_color' => array(
			'map'  => 'ocean_menu_social_links_color',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_menu_social_hover_links_color' => array(
			'map'  => 'ocean_menu_social_hover_links_color',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),

		// Title.
		'_ocean_meta_disable_title' => array(
			'map'  => 'ocean_disable_title',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => 'default'
		),
		'_ocean_meta_disable_heading' => array(
			'map'  => 'ocean_disable_heading',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => 'default'
		),
		'_ocean_meta_post_title' => array(
			'map'  => 'ocean_post_title',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_post_subheading' => array(
			'map'  => 'ocean_post_subheading',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_post_title_style' => array(
			'map'  => 'ocean_post_title_style',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_post_title_background_color' => array(
			'map'  => 'ocean_post_title_background_color',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_post_title_background' => array(
			'map'  => 'ocean_post_title_background',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_post_title_bg_image_position' => array(
			'map'  => 'ocean_post_title_bg_image_position',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_post_title_bg_image_attachment' => array(
			'map'  => 'ocean_post_title_bg_image_attachment',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_post_title_bg_image_repeat' => array(
			'map'  => 'ocean_post_title_bg_image_repeat',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_post_title_bg_image_size' => array(
			'map'  => 'ocean_post_title_bg_image_size',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_post_title_height' => array(
			'map'  => 'ocean_post_title_height',
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'value' => null
		),
		'_ocean_meta_post_title_bg_overlay' => array(
			'map'  => 'ocean_post_title_bg_overlay',
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'value' => null
		),
		'_ocean_meta_post_title_bg_overlay_color' => array(
			'map'  => 'ocean_post_title_bg_overlay_color',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),

		// Breadcrumbs.
		'_ocean_meta_disable_breadcrumbs' => array(
			'map'  => 'ocean_disable_breadcrumbs',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => 'default'
		),
		'_ocean_meta_breadcrumbs_color' => array(
			'map'  => 'ocean_breadcrumbs_color',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_breadcrumbs_separator_color' => array(
			'map'  => 'ocean_breadcrumbs_separator_color',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_breadcrumbs_links_color' => array(
			'map'  => 'ocean_breadcrumbs_links_color',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),
		'_ocean_meta_breadcrumbs_links_hover_color' => array(
			'map'  => 'ocean_breadcrumbs_links_hover_color',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		),

		// Footer.
		'_ocean_meta_display_footer_widgets' => array(
			'map'  => 'ocean_display_footer_widgets',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => 'default'
		),
		'_ocean_meta_display_footer_bottom' => array(
			'map'  => 'ocean_display_footer_bottom',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => 'default'
		),
		'_ocean_meta_custom_footer_template' => array(
			'map'  => 'ocean_custom_footer_template',
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'value' => ''
		)
	);

	return apply_filters( 'ocean_post_setting_meta', $defaults );
}

function oe_post_meta_args( $defaults ) {

	$defaults['_ocean_meta_post_oembed'] = array(
		'map'    => 'ocean_post_oembed',
		'type'   => 'string',
		'single' => true,
		'rest'   => true,
		'value'  => ''
	);

	$defaults['_ocean_meta_post_self_hosted_media'] = array(
		'map'    => 'ocean_post_self_hosted_media',
		'type'   => 'string',
		'single' => true,
		'rest'   => true,
		'value'  => ''
	);

	$defaults['_ocean_meta_post_video_embed'] = array(
		'map'    => 'ocean_post_video_embed',
		'type'   => 'string',
		'single' => true,
		'rest'   => true,
		'value'  => ''
	);

	$defaults['_ocean_meta_link_format_url'] = array(
		'map'    => 'ocean_link_format',
		'type'   => 'string',
		'single' => true,
		'rest'   => true,
		'value'  => ''
	);

	$defaults['_ocean_meta_link_format_target'] = array(
		'map'    => 'ocean_link_format_target',
		'type'   => 'string',
		'single' => true,
		'rest'   => true,
		'value'  => 'self'
	);

	$defaults['_ocean_meta_quote_format'] = array(
		'map'    => 'ocean_quote_format',
		'type'   => 'string',
		'single' => true,
		'rest'   => true,
		'value'  => ''
	);

	$defaults['_ocean_meta_quote_format_link'] = array(
		'map'    => 'ocean_quote_format_link',
		'type'   => 'string',
		'single' => true,
		'rest'   => true,
		'value'  => 'post'
	);

	return apply_filters( 'ocean_post_meta_args', $defaults );

}
add_filter( 'ocean_post_setting_meta', 'oe_post_meta_args' );
