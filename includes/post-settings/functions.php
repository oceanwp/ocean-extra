<?php
/**
 * OceanWP Post settings functions
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
 * Get all meta.
 *
 * @return array
 */
function oe_get_all_meta() {

	global $post;

	// Return if post is not object
	if ( ! is_object( $post ) ) {
		return;
	}

	$defaults = array();
	$data     = ocean_post_setting_data();

	if ( ! empty( $data  ) ) {
		foreach ( $data as $key => $value ) {
			$count = 0;
			$defaults[$key][$count] = $value['value'];
			$count++;
		}
	}

	if ( ! isset( $defaults ) ) {
		return;
	}

	$options = wp_parse_args(
		get_post_meta( $post->ID ),
		$defaults
	);

	return apply_filters( 'oe_get_all_meta', $options );
}

/**
 * Get all keys in an array.
 */
function get_all_meta_key() {
	$keys = ocean_post_setting_data();

	$result = array();

	foreach( $keys as $key => $value ) {
		$result[] = $key;
	}

	return $result;
}

/**
 * Helpers
 *
 * @since 1.0.0
 */
function oe_get_choices() {

	$data = array();

	// Menu.
	$default_menus = array( array( 'label' => 'Default', 'value' => '' ) );
	$added_menus   = array();
	$get_menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) );

	foreach ( $get_menus as $menu) {
		$menus[$menu->term_id] = $menu->name;
		$added_menus[] = array(
			'label' => $menu->name,
			'value' => $menu->term_id
		);
	}

	$menus = array_merge( $default_menus, $added_menus );

	$data['menu'] = $menus;

	// Ocean library template.
	$default_templates = array( array( 'label' => 'Select a Template', 'value' => '' ) );
	$added_templates   = array();
	$get_templates = get_posts( array( 'post_type' => 'oceanwp_library', 'numberposts' => -1, 'post_status' => 'publish' ) );

	if ( ! empty ( $get_templates ) ) {
		foreach ( $get_templates as $template ) {
			$templates[ $template->ID ] = $template->post_title;
			$added_templates[] = array(
				'label' => $template->post_title,
				'value' => $template->ID
			);
		}
	}

	$templates = array_merge( $default_templates, $added_templates );

	$data['templates'] = $templates;

	// Widget area.
	global $wp_registered_sidebars;
	$default_widget_areas = array( array( 'label' => 'Default', 'value' => '' ) );
	$added_widget_areas = array();
	$get_widget_areas = $wp_registered_sidebars;
	if ( ! empty( $get_widget_areas ) ) {
		foreach ( $get_widget_areas as $widget_area ) {
			$name = isset ( $widget_area['name'] ) ? $widget_area['name'] : '';
			$id = isset ( $widget_area['id'] ) ? $widget_area['id'] : '';
			if ( $name && $id ) {
				$added_widget_areas[] = array(
					'label' => $name,
					'value' => $id
				);
			}
		}
	}

	$widget_areas = array_merge( $default_widget_areas, $added_widget_areas );

	$data['widget_area'] = $widget_areas;

	// Pages.
	$added_page_list = array();
	$get_page_list = oe_get_page_template_list();
	if ( ! empty( $get_page_list ) ) {
		$temp_page_list = array();

		if ( isset( $get_page_list['pages'] ) && ! empty( $get_page_list['pages'] ) ) {
			foreach ( $get_page_list['pages'] as $pg_funcs => $pg_template ) {

				$temp_page_list[] = array(
					'label' => $pg_template,
					'value' => $pg_funcs
				);
			}

			$added_page_list[] = array(
				'label' => esc_html__( 'Pages', 'ocean-extra' ),
				'options' => $temp_page_list
			);
		}

		$temp_page_list = array();

		if ( isset( $get_page_list['categories'] ) && ! empty( $get_page_list['categories'] ) ) {
			foreach ( $get_page_list['categories'] as $pg_funcs => $pg_template ) {

				$temp_page_list[] = array(
					'label' => $pg_template,
					'value' => $pg_funcs
				);
			}

			$added_page_list[] = array(
				'label' => esc_html__( 'Categories', 'ocean-extra' ),
				'options' => $temp_page_list
			);
		}

		$temp_page_list = array();

		if ( isset( $get_page_list['shop'] ) && ! empty( $get_page_list['shop'] ) ) {
			foreach ( $get_page_list['shop'] as $pg_funcs => $pg_template ) {

				$temp_page_list[] = array(
					'label' => $pg_template,
					'value' => $pg_funcs
				);
			}

			$added_page_list[] = array(
				'label' => esc_html__( 'Shop', 'ocean-extra' ),
				'options' => $temp_page_list
			);
		}

		$temp_page_list = array();

		if ( isset( $get_page_list['shop_categories'] ) && ! empty( $get_page_list['shop_categories'] ) ) {
			foreach ( $get_page_list['shop_categories'] as $pg_funcs => $pg_template ) {

				$temp_page_list[] = array(
					'label' => $pg_template,
					'value' => $pg_funcs
				);
			}

			$added_page_list[] = array(
				'label'   => esc_html__( 'Product Categories', 'ocean-extra' ),
				'options' => $temp_page_list
			);
		}

		$temp_page_list = array();

		if ( isset( $get_page_list['others'] ) && ! empty( $get_page_list['others'] ) ) {
			foreach ( $get_page_list['others'] as $pg_funcs => $pg_template ) {

				$temp_page_list[] = array(
					'label' => $pg_template,
					'value' => $pg_funcs
				);
			}

			$added_page_list[] = array(
				'label'   => esc_html__( 'others', 'ocean-extra' ),
				'options' => $temp_page_list
			);
		}

		$temp_page_list = array();
	}

	$page_list = $added_page_list;

	$data['page_list'] = $page_list;

	// User roles.
	$default_user_roles = array( array( 'label' => 'Select', 'value' => '' ) );
	$added_user_roles = array();
	$get_user_roles = array_reverse( get_editable_roles() );
	if ( ! empty( $get_user_roles ) ) {
		foreach ( $get_user_roles as $roles => $role_details ) {
			$name = translate_user_role( $role_details['name'] );
			$added_user_roles[] = array(
				'label' => $name,
				'value' => $roles
			);
		}
	}

	$user_roles = array_merge( $default_user_roles, $added_user_roles );

	$data['user_roles'] = $user_roles;

	// Return data.
	return apply_filters( 'ocean_post_settings_data_choices', $data );

}

/**
 * Check for post types.
 */
function oe_metabox_support_post_types() {

	// Post types to add the metabox to.
	$post_types = apply_filters( 'ocean_main_metaboxes_post_types', array(
		'post',
		'page',
		'product',
		'elementor_library',
		'ae_global_templates',
	) );

	return $post_types;
}

/**
 * Check for post types.
 */
function oe_check_post_types_settings() {

	global $post;

	$status = true;

	// Return if post is not object
	if ( ! is_object( $post ) ) {
		return;
	}

	// Post types to add the metabox to.
	$post_types = oe_metabox_support_post_types();

	// Post types scripts
	$post_types_scripts = apply_filters( 'ocean_metaboxes_post_types_scripts', $post_types );

	if ( ! in_array( $post->post_type, $post_types_scripts ) ) {
		$status = false;
	}

	return apply_filters( 'ocean_check_metabox_post_types_settings', $status );
}

/**
 * Check if block editor
 */
function oe_is_block_editor() {

	global $current_screen;

	if ( isset( $current_screen ) ) {
		if ( property_exists( $current_screen, 'is_block_editor') ) {
			return $current_screen->is_block_editor;
		}
	}

	return false;
}

/**
 * Get Templates
 *
 * @since  2.1.0
 */
function oe_get_page_template_list() {
	$pg_templates['pages'] = array(
		'is_page()'       => esc_html__( 'All Pages', 'ocean-extra' ),
		'is_home()'       => esc_html__( 'Home Page ( is_home() )', 'ocean-extra' ),
		'is_front_page()' => esc_html__( 'Front Page ( is_front_page() )', 'ocean-extra' ),
	);

	$pages = get_pages();

	if ( ! empty( $pages ) ) {
		foreach ( $pages as $page ) {
			$pg_templates['pages'][ 'is_page(' . $page->ID . ')' ] = $page->post_title;
		}
	}

	// Add WordPress categories
	$categories = get_categories();
	$category_options = array();
	if (!empty($categories)) {
		foreach ($categories as $category) {
			$category_options['is_category(' . $category->term_id . ')'] = $category->name;
		}
	}

	$pg_templates['categories'] = $category_options;

	$pg_templates['others'] = array(
		'is_single()'          => esc_html__( 'Single Post', 'ocean-extra' ),
		'is_category()'        => esc_html__( 'Category Page', 'ocean-extra' ),
		'is_archive()'         => esc_html__( 'Archive Page', 'ocean-extra' ),
		'is_user_logged_in()'  => esc_html__( 'Logged In User', 'ocean-extra' ),
		'!is_user_logged_in()' => esc_html__( 'Logged Out User', 'ocean-extra' ),
	);

	// Getting Wocommerce specidic pages
	if ( class_exists( 'WooCommerce' ) ) {
		$pg_templates['shop'] = oe_get_woocommerce_page_list();

		// Add WooCommerce product categories
		$product_categories = get_terms( array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
		) );

		$category_options = array();

		if ( !empty( $product_categories ) ) {
			foreach ( $product_categories as $category ) {
				$category_options[ 'is_product_category(' . $category->term_id . ')' ] = $category->name;
			}
		}

		$pg_templates['shop_categories'] = $category_options;
	}

	return $pg_templates;
}

/**
 * Return WooCommerce specific pages
 *
 * @since  2.1.0
 */
function oe_get_woocommerce_page_list() {

	$shop_page_id = get_option( 'woocommerce_shop_page_id' );
	if ( $shop_page_id ) {
		$pg_templates['is_shop()'] = get_the_title( $shop_page_id );
	}

	$pg_templates['is_product_category()'] = esc_html__( 'Product Category', 'ocean-modal-window' );

	$pg_templates['is_product_tag()'] = esc_html__( 'Product Tag', 'ocean-modal-window' );

	$pg_templates['is_product()'] = esc_html__( 'Single Product', 'ocean-modal-window' );

	$shop_page_id = get_option( 'woocommerce_cart_page_id' );
	if ( $shop_page_id ) {
		$pg_templates[ 'is_page(' . $shop_page_id . ')' ] = get_the_title( $shop_page_id );
	}

	$shop_page_id = get_option( 'woocommerce_checkout_page_id' );
	if ( $shop_page_id ) {
		$pg_templates[ 'is_page(' . $shop_page_id . ')' ] = get_the_title( $shop_page_id );
	}

	$shop_page_id = get_option( 'woocommerce_pay_page_id' );
	if ( $shop_page_id ) {
		$pg_templates[ 'is_page(' . $shop_page_id . ')' ] = get_the_title( $shop_page_id );
	}

	$shop_page_id = get_option( 'woocommerce_thanks_page_id' );
	if ( $shop_page_id ) {
		$pg_templates[ 'is_page(' . $shop_page_id . ')' ] = get_the_title( $shop_page_id );
	}

	$shop_page_id = get_option( 'woocommerce_myaccount_page_id' );
	if ( $shop_page_id ) {
		$pg_templates[ 'is_page(' . $shop_page_id . ')' ] = get_the_title( $shop_page_id );
	}

	$shop_page_id = get_option( 'woocommerce_edit_address_page_id' );
	if ( $shop_page_id ) {
		$pg_templates[ 'is_page(' . $shop_page_id . ')' ] = get_the_title( $shop_page_id );
	}

	$shop_page_id = get_option( 'woocommerce_view_order_page_id' );
	if ( $shop_page_id ) {
		$pg_templates[ 'is_page(' . $shop_page_id . ')' ] = get_the_title( $shop_page_id );
	}

	$shop_page_id = get_option( 'woocommerce_terms_page_id' );
	if ( $shop_page_id ) {
		$pg_templates[ 'is_page(' . $shop_page_id . ')' ] = get_the_title( $shop_page_id );
	}

	return $pg_templates;
}


/**
 * Check if user need to upgrade.
 *
 * @return bool
 */
function ocean_check_pro_license() {
	global $owp_fs;
	$status = false;
	if ( ! empty( $owp_fs ) ) {
		$status = $owp_fs->is_pricing_page_visible();
	} else {
		$status = false;
	}

	return $status;
}
