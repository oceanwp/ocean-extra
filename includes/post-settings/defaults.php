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
		'ocean_post_layout' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'wp_kses_post'
		),
		'ocean_both_sidebars_style' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_key'
		),
		'ocean_both_sidebars_content_width' => array(
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 0,
			'sanitize' => 'ops_sanitize_absint',
		),
		'ocean_both_sidebars_sidebars_width' => array(
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 0,
			'sanitize' => 'ops_sanitize_absint',
		),
		'ocean_sidebar' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_key',
		),
		'ocean_second_sidebar' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_key',
		),
		'ocean_disable_margins' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 'enable',
			'sanitize' => 'sanitize_key',
		),
		'ocean_add_body_class' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'wp_kses_post'
		),

		// Shortcode.
		'ocean_shortcode_before_top_bar' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_text_field'
		),
		'ocean_shortcode_after_top_bar' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_text_field'
		),
		'ocean_shortcode_before_header' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_text_field'
		),
		'ocean_shortcode_after_header' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_text_field'
		),
		'ocean_has_shortcode' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_text_field'
		),
		'ocean_shortcode_after_title' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_text_field'
		),
		'ocean_shortcode_before_footer_widgets' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_text_field'
		),
		'ocean_shortcode_after_footer_widgets' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_text_field'
		),
		'ocean_shortcode_before_footer_bottom' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_text_field'
		),
		'ocean_shortcode_after_footer_bottom' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_text_field'
		),

		// Header.
		'ocean_display_top_bar' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 'default',
			'sanitize' => 'sanitize_key',
		),
		'ocean_display_header' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 'default',
			'sanitize' => 'sanitize_key',
		),
		'ocean_header_style' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_key',
		),
		'ocean_center_header_left_menu' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_key',
		),
		'ocean_custom_header_template' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_key',
		),

		// Logo.
		'ocean_custom_logo' => array(
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 0,
			'sanitize' => 'sanitize_key',
		),
		'ocean_custom_retina_logo' => array(
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 0,
			'sanitize' => 'sanitize_key',
		),
		'ocean_custom_logo_max_width' => array(
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 0,
			'sanitize' => 'ops_sanitize_absint',
		),
		'ocean_custom_logo_tablet_max_width' => array(
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 0,
			'sanitize' => 'ops_sanitize_absint',
		),
		'ocean_custom_logo_mobile_max_width' => array(
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 0,
			'sanitize' => 'ops_sanitize_absint',
		),
		'ocean_custom_logo_max_height' => array(
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 0,
			'sanitize' => 'ops_sanitize_absint',
		),
		'ocean_custom_logo_tablet_max_height' => array(
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 0,
			'sanitize' => 'ops_sanitize_absint',
		),
		'ocean_custom_logo_mobile_max_height' => array(
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 0,
			'sanitize' => 'ops_sanitize_absint',
		),

		// Menu.
		'ocean_header_custom_menu' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_key',
		),

		'ocean_menu_typo_font_family' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_text_field',
		),
		'ocean_menu_typo_font_subset' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_text_field',
		),
		'ocean_menu_typo_font_size' => array(
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 0,
			'sanitize' => 'ops_sanitize_absint',
		),
		'ocean_menu_typo_font_size_tablet' => array(
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 0,
			'sanitize' => 'ops_sanitize_absint',
		),
		'ocean_menu_typo_font_size_mobile' => array(
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 0,
			'sanitize' => 'ops_sanitize_absint',
		),
		'ocean_menu_typo_font_size_unit' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 'px',
			'sanitize' => 'sanitize_key',
		),
		'ocean_menu_typo_font_weight' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_text_field',
		),
		'ocean_menu_typo_font_weight_tablet' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_text_field',
		),
		'ocean_menu_typo_font_weight_mobile' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_text_field',
		),
		'ocean_menu_typo_transform' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_key',
		),
		'ocean_menu_typo_transform_tablet' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_key',
		),
		'ocean_menu_typo_transform_mobile' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_key',
		),
		'ocean_menu_typo_line_height' => array(
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 0,
			'sanitize' => 'ops_sanitize_absint',
		),
		'ocean_menu_typo_line_height_tablet' => array(
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 0,
			'sanitize' => 'ops_sanitize_absint',
		),
		'ocean_menu_typo_line_height_mobile' => array(
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 0,
			'sanitize' => 'ops_sanitize_absint',
		),
		'ocean_menu_typo_line_height_unit' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_key',
		),
		'ocean_menu_typo_spacing' => array(
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 0,
			'sanitize' => 'ops_sanitize_absint',
		),
		'ocean_menu_typo_spacing_tablet' => array(
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 0,
			'sanitize' => 'ops_sanitize_absint',
		),
		'ocean_menu_typo_spacing_mobile' => array(
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 0,
			'sanitize' => 'ops_sanitize_absint',
		),
		'ocean_menu_typo_spacing_unit' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_key',
		),
		'ocean_menu_link_color' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'wp_kses_post',
		),
		'ocean_menu_link_color_hover' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'wp_kses_post',
		),
		'ocean_menu_link_color_active' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'wp_kses_post',
		),
		'ocean_menu_link_background' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'wp_kses_post',
		),
		'ocean_menu_link_hover_background' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'wp_kses_post',
		),
		'ocean_menu_link_active_background' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'wp_kses_post',
		),
		'ocean_menu_social_links_bg' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'wp_kses_post',
		),
		'ocean_menu_social_hover_links_bg' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'wp_kses_post',
		),
		'ocean_menu_social_links_color' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'wp_kses_post',
		),
		'ocean_menu_social_hover_links_color' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'wp_kses_post',
		),

		// Title.
		'ocean_disable_title' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 'default',
			'sanitize' => 'sanitize_key',
		),
		'ocean_disable_heading' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 'default',
			'sanitize' => 'sanitize_key',
		),
		'ocean_post_title' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'wp_kses_post',
		),
		'ocean_post_subheading' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'wp_kses_post',
		),
		'ocean_post_title_style' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_key',
		),
		'ocean_post_title_background_color' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'wp_kses_post',
		),
		'ocean_post_title_background' => array(
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 0,
			'sanitize' => 'ops_sanitize_absint',
		),
		'ocean_post_title_bg_image_position' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_text_field',
		),
		'ocean_post_title_bg_image_attachment' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_key',
		),
		'ocean_post_title_bg_image_repeat' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_text_field',
		),
		'ocean_post_title_bg_image_size' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_key',
		),
		'ocean_post_title_height' => array(
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 0,
			'sanitize' => 'ops_sanitize_absint',
		),
		'ocean_post_title_bg_overlay' => array(
			'type'   => 'number',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 0.5,
			'sanitize' => 'ops_sanitize_decimal',
		),
		'ocean_post_title_bg_overlay_color' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_text_field',
		),

		// Breadcrumbs.
		'ocean_disable_breadcrumbs' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 'default',
			'sanitize' => 'sanitize_key',
		),
		'ocean_breadcrumbs_color' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'wp_kses_post',
		),
		'ocean_breadcrumbs_separator_color' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'wp_kses_post',
		),
		'ocean_breadcrumbs_links_color' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'wp_kses_post',
		),
		'ocean_breadcrumbs_links_hover_color' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'wp_kses_post',
		),

		// Footer.
		'ocean_display_footer_widgets' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 'default',
			'sanitize' => 'sanitize_key',
		),
		'ocean_display_footer_bottom' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => 'default',
			'sanitize' => 'sanitize_key',
		),
		'ocean_custom_footer_template' => array(
			'type'   => 'string',
			'single' => true,
			'rest'   => true,
			'subType' => '',
			'value' => '',
			'sanitize' => 'sanitize_key',
		)
	);

	return apply_filters( 'ocean_post_setting_meta', $defaults );
}

function oe_post_meta_args( $defaults ) {

	$defaults['ocean_post_oembed'] = array(
		'type'   => 'string',
		'single' => true,
		'rest'   => true,
		'subType' => 'post',
		'value'  => '',
		'sanitize' => 'sanitize_text_field',
	);

	$defaults['ocean_post_self_hosted_media'] = array(
		'type'   => 'string',
		'single' => true,
		'rest'   => true,
		'subType' => 'post',
		'value'  => '',
		'sanitize' => 'sanitize_text_field',
	);

	$defaults['ocean_post_video_embed'] = array(
		'type'   => 'string',
		'single' => true,
		'rest'   => true,
		'subType' => 'post',
		'value'  => '',
		'sanitize' => 'sanitize_text_field',
	);

	$defaults['ocean_link_format'] = array(
		'type'   => 'string',
		'single' => true,
		'rest'   => true,
		'subType' => 'post',
		'value'  => '',
		'sanitize' => 'sanitize_text_field',
	);

	$defaults['ocean_link_format_target'] = array(
		'type'   => 'string',
		'single' => true,
		'rest'   => true,
		'subType' => 'post',
		'value'  => 'self',
		'sanitize' => 'sanitize_text_field',
	);

	$defaults['ocean_quote_format'] = array(
		'type'   => 'string',
		'single' => true,
		'rest'   => true,
		'subType' => 'post',
		'value'  => '',
		'sanitize' => 'sanitize_text_field',
	);

	$defaults['ocean_quote_format_link'] = array(
		'type'   => 'string',
		'single' => true,
		'rest'   => true,
		'subType' => 'post',
		'value'  => 'post',
		'sanitize' => 'sanitize_text_field',
	);

	$defaults['ocean_gallery_link_images'] = array(
		'type'   => 'string',
		'single' => true,
		'rest'   => true,
		'subType' => 'post',
		'value'  => 'on',
		'sanitize' => 'sanitize_key',
	);

	$defaults['ocean_gallery_id'] = array(
		'type'   => 'array',
		'single' => true,
		'rest'   => array(
			'schema' => array(
				'type'  => 'array',
				'items' => array(
					'type' => 'integer'
				)
			)
		),
		'subType' => 'post',
		'value'  => '',
		'sanitize' => 'ops_sanitize_array',
	);

	return apply_filters( 'ocean_post_meta_args', $defaults );

}
add_filter( 'ocean_post_setting_meta', 'oe_post_meta_args' );
