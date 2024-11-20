<?php
/**
 * Preloader
 *
 * @package Ocean_Extra
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
class Ocean_Preloader_Customizer {

	/**
	 * Status
	 */
	public $active = false;

	/**
	 * Type
	 */
	public $type = 'default';

	/**
	 * Icon Type
	 */
	public $icon_type = 'css';

	/**
	 * Icon
	 */
	public $icon = 'roller';

	/**
	 * Elementor template id
	 */
	public $template_id = '';

	/**
	 * Initialize
	 */
	public function __construct() {

		add_filter( 'ocean_customize_options_data', array( $this, 'register_customize_options' ) );

		$this->active    = get_theme_mod( 'ocean_preloader_enable', false );
		$this->type      = get_theme_mod( 'ocean_preloader_type', 'default' );
		$this->icon_type = get_theme_mod( 'ocean_preloader_icon_type', 'css' );
		$this->icon      = get_theme_mod( 'ocean_preloader_default_icon', 'roller' );

		if ( $this->active ) {
			add_filter( 'ocean_head_css', array( $this, 'head_css' ), 15 );
			add_action( 'customize_preview_init', array( $this, 'customize_preview_js' ) );
		}
	}

	/**
	 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
	 */
	public function customize_preview_js() {
		wp_enqueue_script(
			'preloader-customizer',
			OE_URL . 'includes/preloader/assets/js/customize-preview.min.js',
			array( 'customize-preview' ),
			OE_VERSION,
			true
		);
	}

	/**
	 * Register customizer options
	 */
	public function register_customize_options($options) {

		$options['ocean_preloader'] = [
			'title' => esc_html__( 'Site Preloader', 'ocean-extra' ),
			'priority' => 7,
			'options' => [
				'ocean_preloader_enable' => [
					'type' => 'ocean-switch',
					'label' => esc_html__( 'Enable Site Preloader', 'ocean-extra' ),
					'section' => 'ocean_preloader',
					'default'  => false,
					'transport' => 'refresh',
					'priority' => 10,
					'hideLabel' => false,
					'sanitize_callback' => 'oceanwp_sanitize_checkbox',
				],

				'oe_divider_after_preloader_enable' => [
					'type' => 'ocean-divider',
					'section' => 'ocean_preloader',
					'transport' => 'postMessage',
					'priority' => 10,
					'top' => 10,
					'bottom' => 10,
					'active_callback' => 'oe_cac_has_preloader',
				],

				'ocean_preloader_type' => [
					'type' => 'ocean-buttons',
					'label' => esc_html__( 'Preloader Type', 'ocean-extra' ),
					'section' => 'ocean_preloader',
					'default'  => 'default',
					'transport' => 'refresh',
					'priority' => 10,
					'hideLabel' => false,
					'wrap'    => false,
					'active_callback' => 'oe_cac_has_preloader',
					'sanitize_callback' => 'sanitize_key',
					'choices' => [
						'default' => [
							'id'     => 'default',
							'label'   => esc_html__( 'Default', 'ocean-extra' ),
							'content' => esc_html__( 'Default', 'ocean-extra' ),
						],
						'custom'  => [
							'id'     => 'custom',
							'label'   => esc_html__( 'Custom', 'ocean-extra' ),
							'content' => esc_html__( 'Custom', 'ocean-extra' ),
						]
					]
				],

				'oe_divider_after_preloader_type' => [
					'type' => 'ocean-divider',
					'section' => 'ocean_preloader',
					'transport' => 'postMessage',
					'priority' => 10,
					'top' => 1,
					'active_callback' => 'oe_cac_has_preloader',
				],

				'ocean_preloader_custom_settings' => [
					'type' => 'section',
					'title' => esc_html__( 'Custom Preloader Settings ', 'ocean-extra' ),
					'section' => 'ocean_preloader',
					'after' => 'oe_divider_after_preloader_type',
					'class' => 'section-site-layout',
					'priority' => 10,
					'options' => [
						'ocean_desc_for_preloader_custom_settings' => [
							'type' => 'ocean-content',
							'isContent' => esc_html__( 'Custom preloader settings are available for the Custom Preloader type.', 'ocean-extra' ),
							'section' => 'ocean_preloader_custom_settings',
							'class' => 'description',
							'transport' => 'postMessage',
							'priority' => 10,
							'active_callback' => 'oe_cac_has_preloader',
						],

						'ocean_preloader_template' => [
							'type' => 'ocean-select',
							'label' => esc_html__( 'Select Template', 'ocean-extra' ),
							'desc' => esc_html__( 'Choose a template you created in OceanWP > My Library', 'ocean-extra' ),
							'section' => 'ocean_preloader_custom_settings',
							'transport' => 'refresh',
							'default' => '0',
							'priority' => 10,
							'hideLabel' => false,
							'multiple' => false,
							'active_callback' => 'oe_cac_has_preloader_custom',
							'choices' => oceanwp_library_template_choices(),
							'sanitize_callback' => 'sanitize_key',
						],

						'oe_divider_after_preloader_template' => [
							'type' => 'ocean-divider',
							'section' => 'ocean_preloader_custom_settings',
							'transport' => 'postMessage',
							'priority' => 10,
							'top' => 10,
							'bottom' => 10,
							'active_callback' => 'oe_cac_has_preloader_custom',
						],

						'ocean_preloader_elementor_fouc' => [
							'type' => 'ocean-switch',
							'label' => esc_html__( 'Elementor Flickers/FOUC', 'ocean-extra' ),
							'desc' => esc_html__( 'Experimental (beta) feature which could potentially help resolve Elementor flicker / FOUC issues. No guarantee on resolution at this point.', 'ocean-extra' ),
							'section' => 'ocean_preloader_custom_settings',
							'default'  => true,
							'transport' => 'postMessage',
							'priority' => 10,
							'hideLabel' => false,
							'sanitize_callback' => 'oceanwp_sanitize_checkbox',
							'active_callback' => 'oe_cac_has_preloader_custom',
						],

						'ocean_preloader_custom_settings_need_help' => [
							'type'            => 'ocean-content',
							'isContent'       => sprintf( esc_html__( '%1$s Need Help? %2$s', 'oceanwp' ), '<a href="https://docs.oceanwp.org/article/908-customizer-site-preloader#Custom-Prealoder-Settings-xf0dI/" target="_blank">', '</a>' ),
							'class'           => 'need-help',
							'section'         => 'ocean_preloader_custom_settings',
							'transport'       => 'postMessage',
							'priority'        => 10,
							'active_callback' => 'oe_cac_has_preloader',
						]
					]
				],

				'oe_title_for_preloader_default_settings' => [
					'type'            => 'ocean-title',
					'label'           => esc_html__( 'Default Preloader Settings', 'ocean-extra' ),
					'section'         => 'ocean_preloader',
					'transport'       => 'postMessage',
					'priority'        => 10,
					'top'             => 20,
					'padding'         => 20,
					'active_callback' => 'oe_cac_has_preloader_default',
				],

				'ocean_preloader_icon_type' => [
					'type'              => 'ocean-select',
					'label'             => esc_html__( 'Icon Type', 'ocean-extra' ),
					'section'           => 'ocean_preloader',
					'transport'         => 'refresh',
					'default'           => 'css',
					'priority'          => 10,
					'hideLabel'         => false,
					'multiple'          => false,
					'active_callback'   => 'oe_cac_has_preloader_default',
					'sanitize_callback' => 'sanitize_key',
					'choices'           => [
						'css'   => esc_html__( 'CSS', 'ocean-extra' ),
						'image' => esc_html__( 'Image', 'ocean-extra' ),
						'logo'  => esc_html__( 'Logo', 'ocean-extra' ),
						'svg'   => esc_html__( 'SVG', 'ocean-extra' ),
					]
				],

				'ocean_preloader_default_icon' => [
					'type' => 'ocean-select',
					'label' => esc_html__( 'Preloader Icon', 'ocean-extra' ),
					'section' => 'ocean_preloader',
					'transport' => 'refresh',
					'default' => 'roller',
					'priority' => 10,
					'hideLabel' => false,
					'multiple' => false,
					'active_callback' => 'oe_cac_has_preloader_icon_css',
					'sanitize_callback' => 'sanitize_key',
					'choices' => [
						'roller'        => esc_html__( 'Roller', 'ocean-extra' ),
						'circle'        => esc_html__( 'Circle', 'ocean-extra' ),
						'ring'          => esc_html__( 'Ring', 'ocean-extra' ),
						'dual-ring'     => esc_html__( 'Dual Ring', 'ocean-extra' ),
						'ripple-plain'  => esc_html__( 'Ripple Plain', 'ocean-extra' ),
						'ripple-circle' => esc_html__( 'Ripple Circle', 'ocean-extra' ),
						'heart'         => esc_html__( 'Heart', 'ocean-extra' ),
						'ellipsis'      => esc_html__( 'Ellipsis', 'ocean-extra' ),
						'spinner-line'  => esc_html__( 'Spinner Line', 'ocean-extra' ),
						'spinner-dot'   => esc_html__( 'Spinner Dot', 'ocean-extra' ),
					]
				],

				'ocean_preloader_icon_image' => [
					'label' => esc_html__( 'Image', 'ocean-extra' ),
					'description' => esc_html__( 'Upload svg, gif, png, jpg.', 'ocean-extra' ),
					'type' => 'ocean-image',
					'section'  => 'ocean_preloader',
					'transport' => 'refresh',
					'priority' => 10,
					'hideLabel' => false,
					'mediaType' => 'image',
					'savetype' => 'url',
					'active_callback' => 'oe_cac_has_preloader_icon_image',
					'sanitize_callback' => 'ocean_sanitize_image_control'
				],

				'ocean_preloader_icon_svg' => [
					'label' => esc_html__( 'Upload SVG', 'ocean-extra' ),
					'description' => esc_html__( 'Upload svg file here.', 'ocean-extra' ),
					'type' => 'ocean-image',
					'section'  => 'ocean_preloader',
					'transport' => 'refresh',
					'priority' => 10,
					'hideLabel' => false,
					'mediaType' => 'image',
					'savetype' => 'url',
					'active_callback' => 'oe_cac_has_preloader_icon_svg',
					'sanitize_callback' => 'ocean_sanitize_image_control'
				],

				'oe_divider_after_preloader_icon_svg' => [
					'type' => 'ocean-divider',
					'section' => 'ocean_preloader',
					'transport' => 'postMessage',
					'priority' => 10,
					'top' => 10,
					'bottom' => 10,
					'active_callback' => 'oe_cac_has_preloader_icon_svg',
				],

				'ocean_preloader_image_size' => [
					'id' => 'ocean_preloader_image_size',
					'label'    => esc_html__( 'Size (px)', 'ocean-extra' ),
					'type'     => 'ocean-range-slider',
					'section'  => 'ocean_preloader',
					'transport' => 'postMessage',
					'priority' => 10,
					'hideLabel'    => false,
					'isUnit'       => false,
					'isResponsive' => false,
					'min'          => 0,
					'max'          => 100,
					'step'         => 1,
					'sanitize_callback' => 'oceanwp_sanitize_number_blank',
					'active_callback' => 'oe_cac_has_not_preloader_icon_css',
					'setting_args' => [
						'desktop' => [
							'id' => 'ocean_preloader_image_size',
							'label' => esc_html__( 'Desktop', 'ocean-extra' ),
							'attr' => [
								'transport' => 'postMessage',
								'default' => 100,
							],
						]
					],
					'preview' => 'queryWithType',
					'css' => [
						'.ocean-preloader--active .preloader-image, .ocean-preloader--active .preloader-logo' => ['max-width'],
						'.ocean-preloader--active .preloader-svg svg' => ['width']
					]
				],

				'oe_divider_after_preloader_image_size' => [
					'type'            => 'ocean-divider',
					'section'         => 'ocean_preloader',
					'transport'       => 'postMessage',
					'priority'        => 10,
					'top'             => 10,
					'bottom'          => 10,
					'active_callback' => 'oe_cac_has_preloader_default',
				],

				'ocean_preloader_content' => [
					'type'              => 'ocean-textarea',
					'label'             => esc_html__( 'Content', 'ocean-extra' ),
					'section'           => 'ocean_preloader',
					'transport'         => 'postMessage',
					'default'           => esc_html__( 'Site is Loading, Please wait...', 'ocean-extra' ),
					'priority'          => 10,
					'hideLabel'         => false,
					'sanitize_callback' => 'wp_kses_post',
					'active_callback'   => 'oe_cac_has_preloader_default',
				],

				'oe_divider_after_preloader_content' => [
					'type'            => 'ocean-divider',
					'section'         => 'ocean_preloader',
					'transport'       => 'postMessage',
					'priority'        => 10,
					'top'             => 10,
					'bottom'          => 10,
					'active_callback' => 'oe_cac_has_preloader_default',
				],

				'ocean_preloader_container_width' => [
					'id'                => 'ocean_preloader_container_width',
					'label'             => esc_html__( 'Container Width (px)', 'ocean-extra' ),
					'desc'              => esc_html__( 'Enter a value "0" to unset Container Width, or add custom Container Width value.', 'ocean-extra' ),
					'type'              => 'ocean-range-slider',
					'section'           => 'ocean_preloader',
					'transport'         => 'postMessage',
					'priority'          => 10,
					'hideLabel'         => false,
					'isUnit'            => false,
					'isResponsive'      => true,
					'min'               => 0,
					'max'               => 2000,
					'step'              => 1,
					'active_callback'   => 'oe_cac_has_preloader_default',
					'sanitize_callback' => 'oceanwp_sanitize_number_blank',
					'setting_args'      => [
						'desktop' => [
							'id'    => 'ocean_preloader_container_width',
							'label' => esc_html__( 'Desktop', 'ocean-extra' ),
							'attr'  => [
								'transport' => 'postMessage',
							],
						],
						'tablet' => [
							'id'    => 'ocean_preloader_container_width_tablet',
							'label' => esc_html__( 'Tablet', 'ocean-extra' ),
							'attr'  => [
								'transport' => 'postMessage',
							],
						],
						'mobile' => [
							'id'    => 'ocean_preloader_container_width_mobile',
							'label' => esc_html__( 'Mobile', 'ocean-extra' ),
							'attr'  => [
								'transport' => 'postMessage',
							],
						]
					],
					'preview' => 'queryWithType',
					'css'     => [
						'.ocean-preloader--active .preloader-inner' => ['width']
					]
				],

				'oe_title_for_preloader_typography_and_colors_settings' => [
					'type'            => 'ocean-title',
					'label'           => esc_html__( 'Typography and Colors ', 'ocean-extra' ),
					'section'         => 'ocean_preloader',
					'transport'       => 'postMessage',
					'priority'        => 10,
					'top'             => 20,
					'padding'         => 20,
					'active_callback' => 'oe_cac_has_preloader',
				],

				'ocean_preloader_after_content_typography' => [
					'id'              => 'ocean_preloader_after_content_typography',
					'type'            => 'ocean-typography',
					'label'           => esc_html__( 'Content Text', 'ocean-extra' ),
					'section'         => 'ocean_preloader',
					'transport'       => 'postMessage',
					'priority'        => 10,
					'hideLabel'       => false,
					'selector'        => '.ocean-preloader--active .preloader-after-content',
					'active_callback' => 'oe_cac_has_preloader',
					'setting_args'    => [
						'fontFamily' => [
							'id'    => 'preloader_after_content_typography[font-family]',
							'label' => esc_html__(esc_html__( 'Font Family', 'ocean-extra' ), 'ocean-extra' ),
							'attr'  => [
								'transport' => 'postMessage',
							],
						],
						'fontWeight' => [
							'id'    => 'preloader_after_content_typography[font-weight]',
							'label' => esc_html__( 'Font Weight', 'ocean-extra' ),
							'attr'  => [
								'transport' => 'postMessage',
							],
						],
						'fontWeightTablet' => [
							'id'    => 'preloader_after_content_tablet_typography[font-weight]',
							'label' => esc_html__( 'Font Weight', 'ocean-extra' ),
							'attr'  => [
								'transport' => 'postMessage',
							],
						],
						'fontWeightMobile' => [
							'id'    => 'preloader_after_content_mobile_typography[font-weight]',
							'label' => esc_html__( 'Font Weight', 'ocean-extra' ),
							'attr'  => [
								'transport' => 'postMessage',
							],
						],
						'fontSubset' => [
							'id'    => 'preloader_after_content_typography[font-subset]',
							'label' => esc_html__( 'Font Subset', 'ocean-extra' ),
							'attr'  => [
								'transport' => 'postMessage',
							],
						],
						'fontSize' => [
							'id'    => 'preloader_after_content_typography[font-size]',
							'label' => esc_html__( 'Font Size', 'ocean-extra' ),
							'attr'  => [
								'transport' => 'postMessage',
								'default' => 20,
							],
						],
						'fontSizeTablet' => [
							'id'    => 'preloader_after_content_tablet_typography[font-size]',
							'label' => esc_html__( 'Font Size', 'ocean-extra' ),
							'attr'  => [
								'transport' => 'postMessage',
							],
						],
						'fontSizeMobile' => [
							'id'    => 'preloader_after_content_mobile_typography[font-size]',
							'label' => esc_html__( 'Font Size', 'ocean-extra' ),
							'attr'  => [
								'transport' => 'postMessage',
							],
						],
						'fontSizeUnit' => [
							'id'    => 'preloader_after_content_typography[font-size-unit]',
							'label' => esc_html__( 'Unit', 'ocean-extra' ),
							'attr'  => [
								'transport' => 'postMessage',
								'default' => 'px',
							],
						],
						'letterSpacing' => [
							'id'    => 'preloader_after_content_typography[letter-spacing]',
							'label' => esc_html__( 'Letter Spacing', 'ocean-extra' ),
							'attr'  => [
								'transport' => 'postMessage',
								'default' => 0.6,
							],
						],
						'letterSpacingTablet' => [
							'id'    => 'preloader_after_content_tablet_typography[letter-spacing]',
							'label' => esc_html__( 'Letter Spacing', 'ocean-extra' ),
							'attr'  => [
								'transport' => 'postMessage',
							],
						],
						'letterSpacingMobile' => [
							'id'    => 'preloader_after_content_mobile_typography[letter-spacing]',
							'label' => esc_html__( 'Letter Spacing', 'ocean-extra' ),
							'attr'  => [
								'transport' => 'postMessage',
							],
						],
						'letterSpacingUnit' => [
							'id'    => 'preloader_after_content_typography[letter-spacing-unit]',
							'label' => esc_html__( 'Unit', 'ocean-extra' ),
							'attr'  => [
								'transport' => 'postMessage',
							],
						],
						'lineHeight' => [
							'id'    => 'preloader_after_content_typography[line-height]',
							'label' => esc_html__( 'Line Height', 'ocean-extra' ),
							'attr'  => [
								'transport' => 'postMessage',
								'default' => 1.8,
							],
						],
						'lineHeightTablet' => [
							'id'    => 'preloader_after_content_tablet_typography[line-height]',
							'label' => esc_html__( 'Line Height', 'ocean-extra' ),
							'attr'  => [
								'transport' => 'postMessage',
							],
						],
						'lineHeightMobile' => [
							'id'    => 'preloader_after_content_mobile_typography[line-height]',
							'label' => esc_html__( 'Line Height', 'ocean-extra' ),
							'attr'  => [
								'transport' => 'postMessage',
							],
						],
						'lineHeightUnit' => [
							'id'    => 'preloader_after_content_typography[line-height-unit]',
							'label' => esc_html__( 'Unit', 'ocean-extra' ),
							'attr'  => [
								'transport' => 'postMessage',
							],
						],
						'textTransform' => [
							'id'    => 'preloader_after_content_typography[text-transform]',
							'label' => esc_html__( 'Text Transform', 'ocean-extra' ),
							'attr'  => [
								'transport' => 'postMessage',
							],
						],
						'textTransformTablet' => [
							'id'    => 'preloader_after_content_tablet_typography[text-transform]',
							'label' => esc_html__( 'Text Transform', 'ocean-extra' ),
							'attr'  => [
								'transport' => 'postMessage',
							],
						],
						'textTransformMobile' => [
							'id'    => 'preloader_after_content_mobile_typography[text-transform]',
							'label' => esc_html__( 'Text Transform', 'ocean-extra' ),
							'attr'  => [
								'transport' => 'postMessage',
							],
						],
						'textDecoration' => [
							'id'    => 'preloader_after_content_typography[text-decoration]',
							'label' => esc_html__( 'Text Decoration', 'ocean-extra' ),
							'attr'  => [
								'transport' => 'postMessage',
							],
						],
					]
				],

				'oe_divider_after_preloader_typography_setting' => [
					'type'            => 'ocean-divider',
					'section'         => 'ocean_preloader',
					'transport'       => 'postMessage',
					'priority'        => 10,
					'top'             => 1,
					'bottom'          => 10,
					'active_callback' => 'oe_cac_has_preloader',
				],

				'preloader_after_content_typography_color' => [
					'type'              => 'ocean-color',
					'label'             => esc_html__( 'Content Text', 'ocean-extra' ),
					'section'           => 'ocean_preloader',
					'transport'         => 'postMessage',
					'priority'          => 10,
					'hideLabel'         => false,
					'showAlpha'         => true,
					'sanitize_callback' => 'wp_kses_post',
					'active_callback'   => 'oe_cac_has_preloader',
					'setting_args'      => [
						'normal' => [
							'id'       => 'preloader_after_content_typography[color]',
							'key'      => 'normal',
							'label'    => esc_html__( 'Select Color', 'ocean-extra' ),
							'selector' => [
								'.ocean-preloader--active .preloader-after-content' => 'color'
							],
							'attr'     => [
								'transport' => 'postMessage',
								'default'   => '#333333',
							],
						]
					]
				],

				'ocean_preloader_overlay_color' => [
					'type'              => 'ocean-color',
					'label'             => esc_html__( 'Overlay', 'ocean-extra' ),
					'section'           => 'ocean_preloader',
					'transport'         => 'postMessage',
					'priority'          => 10,
					'hideLabel'         => false,
					'showAlpha'         => true,
					'sanitize_callback' => 'wp_kses_post',
					'active_callback'   => 'oe_cac_has_preloader',
					'setting_args'      => [
						'normal' => [
							'id'       => 'ocean_preloader_overlay_color',
							'key'      => 'normal',
							'label'    => esc_html__( 'Select Color', 'ocean-extra' ),
							'selector' => [
								'.sidebar-box .widget-title' => 'color'
							],
							'attr'     => [
								'transport' => 'postMessage',
								'default'   => '#000000',
							],
						]
					]
				],

				'ocean_preloader_icon_color' => [
					'type'              => 'ocean-color',
					'label'             => esc_html__( 'Icon', 'ocean-extra' ),
					'section'           => 'ocean_preloader',
					'transport'         => 'postMessage',
					'priority'          => 10,
					'hideLabel'         => false,
					'showAlpha'         => true,
					'sanitize_callback' => 'wp_kses_post',
					'active_callback'   => 'oe_cac_has_preloader_icon_css',
					'setting_args'      => [
						'normal' => [
							'id' => 'ocean_preloader_icon_color',
							'key' => 'normal',
							'label' => esc_html__( 'Select Color', 'ocean-extra' ),
							'selector' => [
								'.ocean-preloader--active .preloader-roller div:after' => 'background',
								'.ocean-preloader--active .preloader-circle > div' => 'background',
								'.ocean-preloader--active .preloader-ripple-plain div' => 'background',
								'.ocean-preloader--active .preloader-ripple-circle div' => 'border-color',
								'.ocean-preloader--active .preloader-ring div' => 'border-top-color',
								'.ocean-preloader--active .preloader-dual-ring:after' => 'border-top-color',
								'.ocean-preloader--active .preloader-dual-ring:after' => 'border-bottom-color',
								'.ocean-preloader--active .preloader-heart div, .ocean-preloader--active .preloader-heart div::after, .ocean-preloader--active .preloader-heart div::before' => 'background',
								'.ocean-preloader--active .preloader-ellipsis div' => 'background',
								'.ocean-preloader--active .preloader-spinner-dot div' => 'background',
								'.ocean-preloader--active .preloader-spinner-line div:after' => 'background',
							],
							'attr' => [
								'transport' => 'postMessage',
								'default' => '#ffffff',
							],
						]
					]
				],

				'ocean_preloader_section_need_help' => [
					'type'      => 'ocean-content',
					'isContent' => sprintf( esc_html__( '%1$s Need Help? %2$s', 'oceanwp' ), '<a href="https://docs.oceanwp.org/article/908-customizer-site-preloader/" target="_blank">', '</a>' ),
					'class'     => 'need-help',
					'section'   => 'ocean_preloader',
					'transport' => 'postMessage',
					'priority'  => 10,
				]
			]
		];

		return $options;
	}

	/**
	 * Get CSS
	 *
	 * @param obj $output CSS Output.
	 */
	public function head_css( $output ) {

		$container_width          = get_theme_mod( 'ocean_preloader_container_width' );
		$container_width_tablet   = get_theme_mod( 'ocean_preloader_container_width_tablet' );
		$container_width_mobile   = get_theme_mod( 'ocean_preloader_container_width_mobile' );
		$overlay_color            = get_theme_mod( 'ocean_preloader_overlay_color', '#000000' );
		$icon_color               = get_theme_mod( 'ocean_preloader_icon_color', '#fff' );
		$image_size               = get_theme_mod( 'ocean_preloader_image_size', 100 );

		$content_typography       = get_theme_mod( 'preloader_after_content_typography' );
		$content_typography       = isset($content_typography['color']) ? $content_typography['color'] : '#333333';


		$css = '';

		if ( ! empty( $content_typography ) ) {
			$css .= '.ocean-preloader--active .preloader-after-content{color:'. $content_typography .';}';
		}

		if ( ! empty( $overlay_color ) && '#000000' !== $overlay_color ) {
			$css .= '.ocean-preloader--active #ocean-preloader{background-color:' . $overlay_color . ';}';
		}

		if ( 'default' === $this->type ) {

			if ( ! empty( $container_width ) ) {
				$css .= '.ocean-preloader--active .preloader-inner{width:' . $container_width . 'px;}';
			}
			if ( ! empty( $container_width_tablet ) ) {
				$css .= '@media (max-width: 768px){.ocean-preloader--active .preloader-inner{width:' . $container_width_tablet . 'px;}}';
			}
			if ( ! empty( $container_width_mobile ) ) {
				$css .= '@media (max-width: 480px){.ocean-preloader--active .preloader-inner{width:' . $container_width_mobile . 'px;}}';
			}


			if ( 'css' === $this->icon_type ) {
				if ( ! empty( $icon_color ) && '#fff' !== $icon_color ) {
					if ( 'roller' === $this->icon ) {
						$css .= '.ocean-preloader--active .preloader-roller div:after{background:' . $icon_color . ';}';
					}
					if ( 'circle' === $this->icon ) {
						$css .= '.ocean-preloader--active .preloader-circle > div{background:' . $icon_color . ';}';
					}
					if ( 'ripple-plain' === $this->icon ) {
						$css .= '.ocean-preloader--active .preloader-ripple-plain div{background:' . $icon_color . ';}';
					}
					if ( 'ripple-circle' === $this->icon ) {
						$css .= '.ocean-preloader--active .preloader-ripple-circle div{border-color:' . $icon_color . ';}';
					}
					if ( 'ring' === $this->icon ) {
						$css .= '.ocean-preloader--active .preloader-ring div{border-top-color:' . $icon_color . ';}';
					}
					if ( 'dual-ring' === $this->icon ) {
						$css .= '.ocean-preloader--active .preloader-dual-ring:after{border-top-color:' . $icon_color . ';}';
						$css .= '.ocean-preloader--active .preloader-dual-ring:after{border-bottom-color:' . $icon_color . ';}';
					}
					if ( 'heart' === $this->icon ) {
						$css .= '.ocean-preloader--active .preloader-heart div, .ocean-preloader--active .preloader-heart div::after, .ocean-preloader--active .preloader-heart div::before{background:' . $icon_color . ';}';
					}
					if ( 'ellipsis' === $this->icon ) {
						$css .= '.ocean-preloader--active .preloader-ellipsis div{background:' . $icon_color . ';}';
					}
					if ( 'spinner-dot' === $this->icon ) {
						$css .= '.ocean-preloader--active .preloader-spinner-dot div{background:' . $icon_color . ';}';
					}
					if ( 'spinner-line' === $this->icon ) {
						$css .= '.ocean-preloader--active .preloader-spinner-line div:after{background:' . $icon_color . ';}';
					}
				}
			}

			if ( ! empty( $image_size ) && 100 !== $image_size ) {
				if ( 'image' === $this->icon_type ) {
					$css .= '.ocean-preloader--active .preloader-image {max-width:' . $image_size . 'px;}';
				}
				if ( 'logo' === $this->icon_type ) {
					$css .= '.ocean-preloader--active .preloader-logo {max-width:' . $image_size . 'px;}';
				}
				if ( 'svg' === $this->icon_type ) {
					$css .= '.ocean-preloader--active .preloader-svg svg {width:' . $image_size . 'px; height:' . $image_size . 'px}';
				}
			}
		}



		// Return CSS.
		if ( ! empty( $css ) ) {
			$output .= '/* OceanWP Preloader CSS */' . $css;
		}

		// Return output css.
		return $output;
	}



}

new Ocean_Preloader_Customizer();

/**
 * Callback function
 */
function oe_cac_has_preloader() {
	if ( true === get_theme_mod( 'ocean_preloader_enable', false ) ) {
		return true;
	} else {
		return false;
	}

	return false;
}

/**
 * Callback function
 */
function oe_cac_has_preloader_default() {
	if ( true === get_theme_mod( 'ocean_preloader_enable', false ) && 'default' === get_theme_mod( 'ocean_preloader_type', 'default' ) ) {
		return true;
	} else {
		return false;
	}

	return false;
}

/**
 * Callback function
 */
function oe_cac_has_preloader_custom() {
	if ( true === get_theme_mod( 'ocean_preloader_enable', false ) && 'custom' === get_theme_mod( 'ocean_preloader_type', 'default' ) ) {
		return true;
	} else {
		return false;
	}

	return false;
}

/**
 * Callback function
 */
function oe_cac_has_preloader_icon_css() {
	if ( true === get_theme_mod( 'ocean_preloader_enable', false )
		&& 'default' === get_theme_mod( 'ocean_preloader_type', 'default' )
		&& 'css' === get_theme_mod( 'ocean_preloader_icon_type', 'css' ) )
	{
		return true;
	} else {
		return false;
	}

	return false;
}

/**
 * Callback function
 */
function oe_cac_has_preloader_icon_image() {
	if ( true === get_theme_mod( 'ocean_preloader_enable', false )
		&& 'default' === get_theme_mod( 'ocean_preloader_type', 'default' )
		&& 'image' === get_theme_mod( 'ocean_preloader_icon_type', 'css' ) )
	{
		return true;
	} else {
		return false;
	}

	return false;
}

/**
 * Callback function
 */
function oe_cac_has_not_preloader_icon_css() {
	if ( true === get_theme_mod( 'ocean_preloader_enable', false )
		&& 'default' === get_theme_mod( 'ocean_preloader_type', 'default' )
		&& 'css' !== get_theme_mod( 'ocean_preloader_icon_type', 'css' ) )
	{
		return true;
	} else {
		return false;
	}

	return false;
}

/**
 * Callback function
 */
function oe_cac_has_preloader_icon_svg() {
	if ( true === get_theme_mod( 'ocean_preloader_enable', false )
		&& 'default' === get_theme_mod( 'ocean_preloader_type', 'default' )
		&& 'svg' === get_theme_mod( 'ocean_preloader_icon_type', 'css' ) )
	{
		return true;
	} else {
		return false;
	}

	return false;
}
