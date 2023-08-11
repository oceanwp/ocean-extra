<?php
/**
 * OceanWP Post Settings Output
 *
 * @package Ocean_Extra
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// The Metabox class
if ( ! class_exists( 'OceanWP_Post_Settings_Output' ) ) {

	/**
	 * Main Post Settings class.
	 *
	 * @since  2.1.8
	 * @access public
	 */
	final class OceanWP_Post_Settings_Output {

		/**
		 * Ocean_Extra The single instance of Ocean_Extra.
		 *
		 * @var     object
		 * @access  private
		 */
		private static $_instance = null;

		/**
		 * Main OceanWP_Post_Settings_Output Instance
		 *
		 * @static
		 * @see OceanWP_Post_Settings_Output()
		 * @return Main OceanWP_Post_Settings_Output instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {

			// Load fonts.
			add_action( 'wp_enqueue_scripts', array( $this, 'load_fonts' ) );

			// Body classes.
			add_filter( 'body_class', array( $this, 'body_class' ) );

			// Default sidebar.
			add_filter( 'ocean_get_sidebar', array( $this, 'get_sidebar' ) );

			// Left sidebar.
			add_filter( 'ocean_get_second_sidebar', array( $this, 'get_second_sidebar' ) );

			// Display top bar.
			add_filter( 'ocean_display_top_bar', array( $this, 'display_top_bar' ) );

			// Display header.
			add_filter( 'ocean_display_header', array( $this, 'display_header' ) );

			// Custom menu.
			add_filter( 'ocean_custom_menu', array( $this, 'custom_menu' ) );

			// Header style.
			add_filter( 'ocean_header_style', array( $this, 'header_style' ) );

			// Left custom menu for center geader style.
			add_filter( 'ocean_center_header_left_menu', array( $this, 'left_custom_menu' ) );

			// Custom header template.
			add_filter( 'ocean_custom_header_template', array( $this, 'custom_header_template' ) );

			// Custom logo.
			add_filter( 'get_custom_logo', array( $this, 'custom_logo' ) );

			// getustom logo ID for the retina function.
			add_filter( 'ocean_custom_logo', array( $this, 'custom_logo_id' ) );

			// Custom retina logo.
			add_filter( 'ocean_retina_logo', array( $this, 'custom_retina_logo' ) );

			// Custom logo max width.
			add_filter( 'ocean_logo_max_width', array( $this, 'custom_logo_max_width' ) );

			// Custom logo max width tablet.
			add_filter( 'ocean_logo_max_width_tablet', array( $this, 'custom_logo_max_width_tablet' ) );

			// Custom logo max width mobile.
			add_filter( 'ocean_logo_max_width_mobile', array( $this, 'custom_logo_max_width_mobile' ) );

			// Custom logo max height.
			add_filter( 'ocean_logo_max_height', array( $this, 'custom_logo_max_height' ) );

			// Custom logo max height tablet.
			add_filter( 'ocean_logo_max_height_tablet', array( $this, 'custom_logo_max_height_tablet' ) );

			// Custom logo max height mobile.
			add_filter( 'ocean_logo_max_height_mobile', array( $this, 'custom_logo_max_height_mobile' ) );

			// Menu colors.
			add_filter( 'ocean_menu_link_color', array( $this, 'menu_link_color' ) );
			add_filter( 'ocean_menu_link_color_hover', array( $this, 'menu_link_color_hover' ) );
			add_filter( 'ocean_menu_link_color_active', array( $this, 'menu_link_color_active' ) );
			add_filter( 'ocean_menu_link_background', array( $this, 'menu_link_background' ) );
			add_filter( 'ocean_menu_link_hover_background', array( $this, 'menu_link_hover_background' ) );
			add_filter( 'ocean_menu_link_active_background', array( $this, 'menu_link_active_background' ) );
			add_filter( 'ocean_menu_social_links_bg', array( $this, 'menu_social_links_bg' ) );
			add_filter( 'ocean_menu_social_hover_links_bg', array( $this, 'menu_social_hover_links_bg' ) );
			add_filter( 'ocean_menu_social_links_color', array( $this, 'menu_social_links_color' ) );
			add_filter( 'ocean_menu_social_hover_links_color', array( $this, 'menu_social_hover_links_color' ) );

			// Display page header.
			add_filter( 'ocean_display_page_header', array( $this, 'display_page_header' ) );

			// Display page header heading.
			add_filter( 'ocean_display_page_header_heading', array( $this, 'display_page_header_heading' ) );

			// Page header style.
			add_filter( 'ocean_page_header_style', array( $this, 'page_header_style' ) );

			// Page header title.
			add_filter( 'ocean_title', array( $this, 'page_header_title' ) );

			// Page header subheading.
			add_filter( 'ocean_post_subheading', array( $this, 'page_header_subheading' ) );

			// Page header background image.
			add_filter( 'ocean_page_header_background_image', array( $this, 'page_header_bg_image' ) );

			// Page header background color.
			add_filter( 'ocean_post_title_background_color', array( $this, 'page_header_bg_color' ) );

			// Page header background image position.
			add_filter( 'ocean_post_title_bg_image_position', array( $this, 'page_header_bg_image_position' ) );
			add_filter( 'ocean_post_title_bg_image_attachment', array( $this, 'page_header_bg_image_attachment' ) );
			add_filter( 'ocean_post_title_bg_image_repeat', array( $this, 'page_header_bg_image_repeat' ) );
			add_filter( 'ocean_post_title_bg_image_size', array( $this, 'page_header_bg_image_size' ) );

			// Page header height.
			add_filter( 'ocean_post_title_height', array( $this, 'page_header_height' ) );

			// Page header background opacity.
			add_filter( 'ocean_post_title_bg_overlay', array( $this, 'page_header_bg_opacity' ) );

			// Page header background overlay color.
			add_filter( 'ocean_post_title_bg_overlay_color', array( $this, 'page_header_bg_overlay_color' ) );

			// Display breadcrumbs.
			add_filter( 'ocean_display_breadcrumbs', array( $this, 'display_breadcrumbs' ) );

			// Display footer widgets.
			add_filter( 'ocean_display_footer_widgets', array( $this, 'display_footer_widgets' ) );

			// Display footer bottom.
			add_filter( 'ocean_display_footer_bottom', array( $this, 'display_footer_bottom' ) );

			// Custom footer template.
			add_filter( 'ocean_custom_footer_template', array( $this, 'custom_footer_template' ) );

			// Custom CSS
			add_filter( 'ocean_head_css', array( $this, 'head_css' ), 99 );

		}

		/**
		 * Load google fonts.
		 */
		public function load_fonts() {

			$fonts = array();

			// Menu font.
			$menu_typo_font = oceanwp_post_id() ? get_post_meta( oceanwp_post_id(), 'ocean_menu_typo_font_family', true ) : '';

			if ( $menu_typo_font ) {
				$fonts[] = $menu_typo_font;
			}

			// Loop through and enqueue fonts
			if ( ! empty( $fonts ) && is_array( $fonts ) ) {
				foreach ( $fonts as $font ) {
					oceanwp_enqueue_google_font( $font );
				}
			}
		}

		/**
		 * Body classes
		 *
		 * @since  1.2.10
		 */
		public function body_class( $classes ) {

			// Disabled margins
			if ( 'on' == get_post_meta( oceanwp_post_id(), 'ocean_disable_margins', true )
				&& ! is_search() ) {
				$classes[] = 'no-margins';
			}

			$body_class = get_post_meta( oceanwp_post_id(), 'ocean_add_body_class', true );

			if ( ! empty( $body_class ) ) {
				$classes[] = $body_class;
			}

			return $classes;

		}

		/**
		 * Returns the correct second sidebar ID
		 *
		 * @since  1.3.3
		 */
		public function get_second_sidebar( $sidebar ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_second_sidebar', true ) ) {
				$sidebar = $meta;
			}

			return $sidebar;

		}

		/**
		 * Returns the correct sidebar ID
		 *
		 * @since  1.2.10
		 */
		public function get_sidebar( $sidebar ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_sidebar', true ) ) {
				$sidebar = $meta;
			}

			return $sidebar;

		}

		/**
		 * Display top bar
		 *
		 * @since  1.2.10
		 */
		public function display_top_bar( $return ) {

			// Check meta
			$meta = oceanwp_post_id() ? get_post_meta( oceanwp_post_id(), 'ocean_display_top_bar', true ) : '';

			// Check if disabled
			if ( 'on' == $meta ) {
				$return = true;
			} elseif ( 'off' == $meta ) {
				$return = false;
			}

			return $return;

		}

		/**
		 * Display header
		 *
		 * @since  1.2.10
		 */
		public function display_header( $return ) {

			// Check meta
			$meta = oceanwp_post_id() ? get_post_meta( oceanwp_post_id(), 'ocean_display_header', true ) : '';

			// Check if disabled
			if ( 'on' == $meta ) {
				$return = true;
			} elseif ( 'off' == $meta ) {
				$return = false;
			}

			return $return;

		}

		/**
		 * Custom menu
		 *
		 * @since  1.2.10
		 */
		public function custom_menu( $menu ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_header_custom_menu', true ) ) {
				$menu = $meta;
			}

			return $menu;

		}

		/**
		 * Header style
		 *
		 * @since  1.3.3
		 */
		public function header_style( $style ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_header_style', true ) ) {
				$style = $meta;
			}

			return $style;

		}

		/**
		 * Left custom menu for center geader style
		 *
		 * @since  1.3.3
		 */
		public function left_custom_menu( $menu ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_center_header_left_menu', true ) ) {
				$menu = $meta;
			}

			return $menu;

		}

		/**
		 * Custom header template
		 *
		 * @since  1.3.3
		 */
		public function custom_header_template( $template ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_custom_header_template', true ) ) {
				$template = $meta;
			}

			return $template;

		}

		/**
		 * Custom logo
		 *
		 * @since  1.3.3
		 */
		public function custom_logo( $html ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_custom_logo', true ) ) {

				$html = '';

				// We have a logo. Logo is go.
				if ( $meta ) {

					$custom_logo_attr = array(
						'class'    => 'custom-logo',
						'itemprop' => 'logo',
					);

					/*
					 * If the logo alt attribute is empty, get the site title and explicitly
					 * pass it to the attributes used by wp_get_attachment_image().
					 */
					$image_alt = get_post_meta( $meta, '_wp_attachment_image_alt', true );
					if ( empty( $image_alt ) ) {
						$custom_logo_attr['alt'] = get_bloginfo( 'name', 'display' );
					}

					/*
					 * If the alt attribute is not empty, there's no need to explicitly pass
					 * it because wp_get_attachment_image() already adds the alt attribute.
					 */
					$html = sprintf( '<a href="%1$s" class="custom-logo-link" rel="home" itemprop="url">%2$s</a>',
						esc_url( home_url( '/' ) ),
						wp_get_attachment_image( $meta, 'full', false, $custom_logo_attr )
					);

				}

			}

			return $html;

		}

		/**
		 * Custom logo ID
		 *
		 * @since  1.3.3
		 */
		public function custom_logo_id( $logo_url ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_custom_logo', true ) ) {
				$logo_url = $meta;
			}

			return $logo_url;

		}

		/**
		 * Custom retina logo
		 *
		 * @since  1.3.3
		 */
		public function custom_retina_logo( $logo_url ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_custom_retina_logo', true ) ) {
				$logo_url = $meta;

				// Generate image URL if using ID
				if ( is_numeric( $logo_url ) ) {
					$logo_url = wp_get_attachment_image_src( $logo_url, 'full' );
					$logo_url = $logo_url[0];
				}
			}

			return $logo_url;

		}

		/**
		 * Custom logo max width
		 *
		 * @since  1.3.3
		 */
		public function custom_logo_max_width( $width ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_custom_logo_max_width', true ) ) {
				$width = $meta;
			}

			return $width;

		}

		/**
		 * Custom logo max width tablet
		 *
		 * @since  1.3.3
		 */
		public function custom_logo_max_width_tablet( $width ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_custom_logo_tablet_max_width', true ) ) {
				$width = $meta;
			}

			return $width;

		}

		/**
		 * Custom logo max width mobile
		 *
		 * @since  1.3.3
		 */
		public function custom_logo_max_width_mobile( $width ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_custom_logo_mobile_max_width', true ) ) {
				$width = $meta;
			}

			return $width;

		}

		/**
		 * Custom logo max height
		 *
		 * @since  1.3.3
		 */
		public function custom_logo_max_height( $height ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_custom_logo_max_height', true ) ) {
				$height = $meta;
			}

			return $height;

		}

		/**
		 * Custom logo max height tablet
		 *
		 * @since  1.3.3
		 */
		public function custom_logo_max_height_tablet( $height ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_custom_logo_tablet_max_height', true ) ) {
				$height = $meta;
			}

			return $height;

		}

		/**
		 * Custom logo max height mobile
		 *
		 * @since  1.3.3
		 */
		public function custom_logo_max_height_mobile( $height ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_custom_logo_mobile_max_height', true ) ) {
				$height = $meta;
			}

			return $height;

		}

		/**
		 * Menu links color
		 *
		 * @since  1.3.3
		 */
		public function menu_link_color( $color ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_menu_link_color', true ) ) {
				$color = $meta;
			}

			return $color;

		}

		/**
		 * Menu links color: hover
		 *
		 * @since  1.3.3
		 */
		public function menu_link_color_hover( $color ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_menu_link_color_hover', true ) ) {
				$color = $meta;
			}

			return $color;

		}

		/**
		 * Menu links color: current menu item
		 *
		 * @since  1.3.3
		 */
		public function menu_link_color_active( $color ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_menu_link_color_active', true ) ) {
				$color = $meta;
			}

			return $color;

		}

		/**
		 * Menu links background
		 *
		 * @since  1.3.3
		 */
		public function menu_link_background( $color ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_menu_link_background', true ) ) {
				$color = $meta;
			}

			return $color;

		}

		/**
		 * Menu links background: hover
		 *
		 * @since  1.3.3
		 */
		public function menu_link_hover_background( $color ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_menu_link_hover_background', true ) ) {
				$color = $meta;
			}

			return $color;

		}

		/**
		 * Menu links background: current menu item
		 *
		 * @since  1.3.3
		 */
		public function menu_link_active_background( $color ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_menu_link_active_background', true ) ) {
				$color = $meta;
			}

			return $color;

		}

		/**
		 * Social menu links background color
		 *
		 * @since  1.3.3
		 */
		public function menu_social_links_bg( $color ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_menu_social_links_bg', true ) ) {
				$color = $meta;
			}

			return $color;

		}

		/**
		 * Social menu hover links background color
		 *
		 * @since  1.3.3
		 */
		public function menu_social_hover_links_bg( $color ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_menu_social_hover_links_bg', true ) ) {
				$color = $meta;
			}

			return $color;

		}

		/**
		 * Social menu links color
		 *
		 * @since  1.3.3
		 */
		public function menu_social_links_color( $color ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_menu_social_links_color', true ) ) {
				$color = $meta;
			}

			return $color;

		}

		/**
		 * Social menu hover links color
		 *
		 * @since  1.3.3
		 */
		public function menu_social_hover_links_color( $color ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_menu_social_hover_links_color', true ) ) {
				$color = $meta;
			}

			return $color;

		}

		/**
		 * Display page header
		 *
		 * @since  1.2.10
		 */
		public function display_page_header( $return ) {

			// Check meta
			$meta = oceanwp_post_id() ? get_post_meta( oceanwp_post_id(), 'ocean_disable_title', true ) : '';

			// Check if enabled or disabled
			if ( 'enable' == $meta ) {
				$return = true;
			} elseif ( 'on' == $meta ) {
				$return = false;
			}

			return $return;

		}

		/**
		 * Display page header heading
		 *
		 * @since  1.3.3
		 */
		public function display_page_header_heading( $return ) {

			// Check meta
			$meta = oceanwp_post_id() ? get_post_meta( oceanwp_post_id(), 'ocean_disable_heading', true ) : '';

			// Check if enabled or disabled
			if ( 'enable' == $meta ) {
				$return = true;
			} elseif ( 'on' == $meta ) {
				$return = false;
			}

			return $return;

		}

		/**
		 * Page header style
		 *
		 * @since  1.2.10
		 */
		public function page_header_style( $style ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_post_title_style', true ) ) {
				$style = $meta;
			}

			return $style;

		}

		/**
		 * Page header title
		 *
		 * @since  1.2.10
		 */
		public function page_header_title( $title ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_post_title', true ) ) {
				$title = $meta;
			}

			return $title;

		}

		/**
		 * Page header subheading
		 *
		 * @since  1.2.10
		 */
		public function page_header_subheading( $subheading ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_post_subheading', true ) ) {
				$subheading = $meta;
			}

			return $subheading;

		}

		/**
		 * Display breadcrumbs
		 *
		 * @since  1.2.10
		 */
		public function display_breadcrumbs( $return ) {

			// Check meta
			$meta = oceanwp_post_id() ? get_post_meta( oceanwp_post_id(), 'ocean_disable_breadcrumbs', true ) : '';

			// Check if enabled or disabled
			if ( 'on' == $meta ) {
				$return = true;
			} elseif ( 'off' == $meta ) {
				$return = false;
			}

			return $return;

		}

		/**
		 * Title background color
		 *
		 * @since  1.2.10
		 */
		public function page_header_bg_color( $bg_color ) {

			if ( 'solid-color' == get_post_meta( oceanwp_post_id(), 'ocean_post_title_style', true ) ) {
				if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_post_title_background_color', true ) ) {
					$bg_color = $meta;
				}
			}

			return $bg_color;

		}

		/**
		 * Title background image
		 *
		 * @since  1.2.10
		 */
		public function page_header_bg_image( $bg_img ) {

			if ( 'background-image' == get_post_meta( oceanwp_post_id(), 'ocean_post_title_style', true ) ) {
				if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_post_title_background', true ) ) {
					$bg_img = $meta;
				}
			}

			return $bg_img;

		}

		/**
		 * Title background image position
		 *
		 * @since  1.2.10
		 */
		public function page_header_bg_image_position( $bg_img_position ) {

			if ( 'background-image' == get_post_meta( oceanwp_post_id(), 'ocean_post_title_style', true ) ) {
				if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_post_title_bg_image_position', true ) ) {
					$bg_img_position = $meta;
				}
			}

			return $bg_img_position;

		}

		/**
		 * Title background image attachment
		 *
		 * @since  1.2.10
		 */
		public function page_header_bg_image_attachment( $bg_img_attachment ) {

			if ( 'background-image' == get_post_meta( oceanwp_post_id(), 'ocean_post_title_style', true ) ) {
				if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_post_title_bg_image_attachment', true ) ) {
					$bg_img_attachment = $meta;
				}
			}

			return $bg_img_attachment;

		}

		/**
		 * Title background image repeat
		 *
		 * @since  1.2.10
		 */
		public function page_header_bg_image_repeat( $bg_img_repeat ) {

			if ( 'background-image' == get_post_meta( oceanwp_post_id(), 'ocean_post_title_style', true ) ) {
				if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_post_title_bg_image_repeat', true ) ) {
					$bg_img_repeat = $meta;
				}
			}

			return $bg_img_repeat;

		}

		/**
		 * Title background image size
		 *
		 * @since  1.2.10
		 */
		public function page_header_bg_image_size( $bg_img_size ) {

			if ( 'background-image' == get_post_meta( oceanwp_post_id(), 'ocean_post_title_style', true ) ) {
				if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_post_title_bg_image_size', true ) ) {
					$bg_img_size = $meta;
				}
			}

			return $bg_img_size;

		}

		/**
		 * Title height
		 *
		 * @since  1.2.10
		 */
		public function page_header_height( $title_height ) {

			if ( 'background-image' == get_post_meta( oceanwp_post_id(), 'ocean_post_title_style', true ) ) {
				if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_post_title_height', true ) ) {
					$title_height = $meta;
				}
			}

			return $title_height;

		}

		/**
		 * Title background opacity
		 *
		 * @since  1.2.10
		 */
		public function page_header_bg_opacity( $opacity ) {

			if ( 'background-image' == get_post_meta( oceanwp_post_id(), 'ocean_post_title_style', true ) ) {
				if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_post_title_bg_overlay', true ) ) {
					$opacity = $meta;
				}
			}

			return $opacity;

		}

		/**
		 * Title background overlay color
		 *
		 * @since  1.2.10
		 */
		public function page_header_bg_overlay_color( $overlay_color ) {

			if ( 'background-image' == get_post_meta( oceanwp_post_id(), 'ocean_post_title_style', true ) ) {
				if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_post_title_bg_overlay_color', true ) ) {
					$overlay_color = $meta;
				}
			}

			return $overlay_color;

		}

		/**
		 * Display footer widgets
		 *
		 * @since  1.2.10
		 */
		public function display_footer_widgets( $return ) {

			// Check meta
			$meta = oceanwp_post_id() ? get_post_meta( oceanwp_post_id(), 'ocean_display_footer_widgets', true ) : '';

			// Check if disabled
			if ( 'on' == $meta ) {
				$return = true;
			} elseif ( 'off' == $meta ) {
				$return = false;
			}

			return $return;

		}

		/**
		 * Display footer bottom
		 *
		 * @since  1.2.10
		 */
		public function display_footer_bottom( $return ) {

			// Check meta
			$meta = oceanwp_post_id() ? get_post_meta( oceanwp_post_id(), 'ocean_display_footer_bottom', true ) : '';

			// Check if disabled
			if ( 'on' == $meta ) {
				$return = true;
			} elseif ( 'off' == $meta ) {
				$return = false;
			}

			return $return;

		}

		/**
		 * Custom footer template
		 *
		 * @since  1.3.3
		 */
		public function custom_footer_template( $template ) {

			if ( $meta = get_post_meta( oceanwp_post_id(), 'ocean_custom_footer_template', true ) ) {
				$template = $meta;
			}

			return $template;

		}

		/**
		 * Get CSS
		 */
		public static function head_css( $output ) {

			$id = oceanwp_post_id();

			// Layout.
			$layout                     = get_post_meta( $id, 'ocean_post_layout', true );
			$content_width              = get_post_meta( $id, 'ocean_both_sidebars_content_width', true );
			$sidebars_width             = get_post_meta( $id, 'ocean_both_sidebars_sidebars_width', true );

			$menu_font_family           = get_post_meta( $id, 'ocean_menu_typo_font_family', true );
			$menu_font_size             = get_post_meta( $id, 'ocean_menu_typo_font_size', true );
			$menu_font_size_tablet      = get_post_meta( $id, 'ocean_menu_typo_font_size_tablet', true );
			$menu_font_size_mobile      = get_post_meta( $id, 'ocean_menu_typo_font_size_mobile', true );
			$menu_font_size_unit        = get_post_meta( $id, 'ocean_menu_typo_font_size_unit', true );
			$menu_font_weight           = get_post_meta( $id, 'ocean_menu_typo_font_weight', true );
			$menu_font_weight_tablet    = get_post_meta( $id, 'ocean_menu_typo_font_weight_tablet', true );
			$menu_font_weight_mobile    = get_post_meta( $id, 'ocean_menu_typo_font_weight_mobile', true );
			$menu_text_transform        = get_post_meta( $id, 'ocean_menu_typo_transform', true );
			$menu_text_transform_tablet = get_post_meta( $id, 'ocean_menu_typo_transform_tablet', true );
			$menu_text_transform_mobile = get_post_meta( $id, 'ocean_menu_typo_transform_mobile', true );
			$menu_line_height           = get_post_meta( $id, 'ocean_menu_typo_line_height', true );
			$menu_line_height_tablet    = get_post_meta( $id, 'ocean_menu_typo_line_height_tablet', true );
			$menu_line_height_mobile    = get_post_meta( $id, 'ocean_menu_typo_line_height_mobile', true );
			$menu_line_height_unit      = get_post_meta( $id, 'ocean_menu_typo_line_height_unit', true );
			$menu_letter_spacing        = get_post_meta( $id, 'ocean_menu_typo_spacing', true );
			$menu_letter_spacing_tablet = get_post_meta( $id, 'ocean_menu_typo_spacing_tablet', true );
			$menu_letter_spacing_mobile = get_post_meta( $id, 'ocean_menu_typo_spacing_mobile', true );
			$menu_letter_spacing_unit   = get_post_meta( $id, 'ocean_menu_typo_spacing_unit', true );

			$menu_font_size_unit      = $menu_font_size_unit ? $menu_font_size_unit : 'px';
			$menu_line_height_unit    = $menu_line_height_unit ? $menu_line_height_unit : 'px';
			$menu_letter_spacing_unit = $menu_letter_spacing_unit ? $menu_letter_spacing_unit : 'px';

			// Define css var
			$css = '';
			$menu_typo_css = '';
			$menu_typo_tablet_css = '';
			$menu_typo_mobile_css = '';

			// If Both Sidebars layout
			if ( 'both-sidebars' == $layout ) {

				// Both Sidebars layout content width
				if ( ! empty( $content_width ) ) {
					$css .=
						'@media only screen and (min-width: 960px){
							.content-both-sidebars .content-area {width: '. $content_width .'%;}
							.content-both-sidebars.scs-style .widget-area.sidebar-secondary,
							.content-both-sidebars.ssc-style .widget-area {left: -'. $content_width .'%;}
						}';
				}

				// Both Sidebars layout sidebars width
				if ( ! empty( $sidebars_width ) ) {
					$css .=
						'@media only screen and (min-width: 960px){
							.content-both-sidebars .widget-area{width:'. $sidebars_width .'%;}
							.content-both-sidebars.scs-style .content-area{left:'. $sidebars_width .'%;}
							.content-both-sidebars.ssc-style .content-area{left:'. $sidebars_width * 2 .'%;}
						}';
				}

			}

			// Add menu font family
			if ( ! empty( $menu_font_family ) ) {
				$menu_typo_css .= 'font-family:' . $menu_font_family . ';';
			}
			// Add menu font size
			if ( ! empty( $menu_font_size ) ) {
				$menu_typo_css .= 'font-size:' . $menu_font_size . '' . $menu_font_size_unit . ';';
			}
			if ( ! empty( $menu_font_size_tablet ) ) {
				$menu_typo_tablet_css .= 'font-size:' . $menu_font_size_tablet . '' . $menu_font_size_unit . ';';
			}
			if ( ! empty( $menu_font_size_mobile ) ) {
				$menu_typo_mobile_css .= 'font-size:' . $menu_font_size_mobile . '' . $menu_font_size_unit . ';';
			}

			// Add menu font weight
			if ( ! empty( $menu_font_weight ) ) {
				$menu_typo_css .= 'font-weight:' . $menu_font_weight . ';';
			}
			if ( ! empty( $menu_font_weight_tablet ) ) {
				$menu_typo_tablet_css .= 'font-weight:' . $menu_font_weight_tablet . ';';
			}
			if ( ! empty( $menu_font_weight_mobile ) ) {
				$menu_typo_mobile_css .= 'font-weight:' . $menu_font_weight_mobile . ';';
			}

			// Add menu text transform
			if ( ! empty( $menu_text_transform ) ) {
				$menu_typo_css .= 'text-transform:'. $menu_text_transform .';';
			}
			if ( ! empty( $menu_text_transform_tablet ) ) {
				$menu_typo_tablet_css .= 'text-transform:' . $menu_text_transform_tablet . ';';
			}
			if ( ! empty( $menu_text_transform_mobile ) ) {
				$menu_typo_mobile_css .= 'text-transform:' . $menu_text_transform_mobile . ';';
			}

			// Add menu line height
			if ( ! empty( $menu_line_height ) ) {
				$menu_typo_css .= 'line-height:' . $menu_line_height . '' . $menu_line_height_unit . ';';
			}
			if ( ! empty( $menu_line_height_tablet ) ) {
				$menu_typo_tablet_css .= 'line-height:' . $menu_line_height_tablet . '' . $menu_line_height_unit . ';';
			}
			if ( ! empty( $menu_line_height_mobile ) ) {
				$menu_typo_mobile_css .= 'line-height:' . $menu_line_height_mobile . '' . $menu_line_height_unit . ';';
			}

			// Add menu letter spacing
			if ( ! empty( $menu_letter_spacing ) ) {
				$menu_typo_css .= 'letter-spacing:' . $menu_letter_spacing . '' . $menu_letter_spacing_unit . ';';
			}
			if ( ! empty( $menu_letter_spacing_tablet ) ) {
				$menu_typo_tablet_css .= 'letter-spacing:' . $menu_letter_spacing_tablet . '' . $menu_letter_spacing_unit . ';';
			}
			if ( ! empty( $menu_letter_spacing_mobile ) ) {
				$menu_typo_mobile_css .= 'letter-spacing:' . $menu_letter_spacing_mobile . '' . $menu_letter_spacing_unit . ';';
			}

			// Menu typography css
			if ( ! empty( $menu_typo_css ) ) {
				$css .= '#site-navigation-wrap .dropdown-menu > li > a, .oceanwp-mobile-menu-icon a {'. $menu_typo_css .'}';
			}
			if ( ! empty( $menu_typo_tablet_css ) ) {
				$css .= '@media only screen and (max-width: 768x){
					#site-navigation-wrap .dropdown-menu > li > a, .oceanwp-mobile-menu-icon a {'. $menu_typo_tablet_css .'}
				}';
			}
			if ( ! empty( $menu_typo_mobile_css ) ) {
				$css .= '@media only screen and (max-width: 480x){
					#site-navigation-wrap .dropdown-menu > li > a, .oceanwp-mobile-menu-icon a {'. $menu_typo_mobile_css .'}
				}';
			}

			// Return CSS
			if ( ! empty( $css ) ) {
				$output .= $css;
			}

			// Return output css
			return $output;

		}

	}
}

/**
 * Returns the main instance of OceanWP_Post_Settings_Output to prevent the need to use globals.
 *
 * @return object OceanWP_Post_Settings_Output
 */
function OceanWP_Post_Settings_Output() {
	return OceanWP_Post_Settings_Output::instance();
}

OceanWP_Post_Settings_Output();
