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
	$default_user_roles = array(
		array(
			'label' => 'Select',
			'value' => ''
		)
	);

	$added_user_roles = array();

	global $wp_roles;

	if ( ! isset( $wp_roles ) ) {
		$wp_roles = wp_roles();
	}

	$get_user_roles = array_reverse( $wp_roles->roles, true );

	if ( ! empty( $get_user_roles ) ) {
		foreach ( $get_user_roles as $role_key => $role_details ) {
			$name = translate_user_role( $role_details['name'] );

			$added_user_roles[] = array(
				'label' => $name,
				'value' => $role_key
			);
		}
	}

	$role_options = array_merge( $default_user_roles, $added_user_roles );

	$data['user_roles'] = $role_options;

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

/**
 * Check if user requires upgrading
 *
 * @return bool
 */
if ( ! function_exists( 'oe_pro_license_check' ) ) {

	function oe_pro_license_check() {
		global $owp_fs;
		$need_to_upgrade = ! empty( $owp_fs ) ? $owp_fs->is_pricing_page_visible() : false;

		if ( ! $need_to_upgrade ) {
			return false;
		}

		return true;
	}
}

/**
 * Get allowed condition values
 */
function oe_get_allowed_condition_values() {

    $choices = oe_get_choices();
    $allowed = [];

    // Menu
    if ( ! empty( $choices['menu'] ) ) {
        foreach ( $choices['menu'] as $m ) {
            if ( isset( $m['value'] ) ) {
                $allowed[] = (string) $m['value'];
            }
        }
    }

    // Templates
    if ( ! empty( $choices['templates'] ) ) {
        foreach ( $choices['templates'] as $t ) {
            if ( isset( $t['value'] ) ) {
                $allowed[] = (string) $t['value'];
            }
        }
    }

    // Widget areas
    if ( ! empty( $choices['widget_area'] ) ) {
        foreach ( $choices['widget_area'] as $w ) {
            if ( isset( $w['value'] ) ) {
                $allowed[] = (string) $w['value'];
            }
        }
    }

    // Page list (nested)
    if ( ! empty( $choices['page_list'] ) ) {
        foreach ( $choices['page_list'] as $group ) {
            if ( ! empty( $group['options'] ) ) {
                foreach ( $group['options'] as $opt ) {
                    if ( isset( $opt['value'] ) ) {
                        $allowed[] = (string) $opt['value'];
                    }
                }
            }
        }
    }

    // User roles
    if ( ! empty( $choices['user_roles'] ) ) {
        foreach ( $choices['user_roles'] as $r ) {
            if ( isset( $r['value'] ) ) {
                $allowed[] = (string) $r['value'];
            }
        }
    }

    return array_unique( $allowed );
}

if ( ! function_exists('oe_match_conditions') ) {
	/**
	 * Safe evaluator for display/hide conditions.
	 *
	 * Handles:
	 * - Page conditions (functions like is_front_page, is_home, is_page, etc.)
	 * - WooCommerce conditions (is_shop, is_product_category, etc.)
	 * - Menu conditions (menu IDs)
	 * - Widget area conditions (sidebar IDs)
	 * - Template conditions (post IDs)
	 * - User roles
	 *
	 * Returns TRUE if **any** condition in the array matches.
	 */
	function oe_match_conditions( $values ) {

		if ( empty( $values ) ) {
			return false;
		}

		$allowed_values = oe_get_allowed_condition_values();

		$conds = oe_parse_condition_string($values);

		$valid_conds = [];

		foreach ( $conds as $cond ) {

			if ( strpos( $cond, ':' ) !== false ) {
				list( $fn, $arg ) = explode( ':', $cond, 2 );

				// If argument is not part of the allowed choices, skip it entirely
				if ( ! in_array( $arg, $allowed_values, true ) ) {
					continue;
				}
			}

			$valid_conds[] = $cond;
		}

		// No valid conditions after filtering
		if ( empty( $valid_conds ) ) {
			return false;
		}

		foreach ( $valid_conds as $cond ) {

			if ( ! is_string( $cond ) ) {
				continue;
			}

			// Basic sanitation
			$cond = trim( $cond );
			if ( preg_match( '/[;`$<>{}]/', $cond ) ) {
				continue;
			}

			// ----------------------------
			// 1) Page template conditions
			// ----------------------------
			// Formats:
			//   is_front_page
			//   is_home
			//   is_page:123
			//   is_page:about
			//   is_single:45
			//   is_category
			//   is_search, is_404, is_tag, etc.
			//   is_product, is_shop, is_cart (Woo)
			// ----------------------------

			if ( preg_match( '/^(!?[a-z_]+)(?:[:\(]([a-zA-Z0-9_-]*)\)?)?$/', $cond, $m ) ) {

				$token = $m[1];
				$arg = isset( $m[2] ) ? $m[2] : null;

				switch ( $token ) {

					case 'is_front_page':
						if ( is_front_page() ) return true;
						break;

					case 'is_home':
						if ( is_home() ) return true;
						break;

					case 'is_page':
						if ( $arg ) {
							if ( is_numeric( $arg ) && is_page( intval( $arg ) ) ) return true;
							if ( is_page( sanitize_text_field( $arg ) ) ) return true;
						} else {
							if ( is_page() ) return true;
						}
						break;

					case 'is_single':
						if ( $arg ) {
							if ( is_numeric( $arg ) && is_single( intval( $arg ) ) ) return true;
							if ( is_single( sanitize_text_field( $arg ) ) ) return true;
						} else {
							if ( is_single() ) return true;
						}
						break;

					case 'is_category':
						if ( is_category() ) return true;
						break;

					case 'is_tag':
						if ( is_tag() ) return true;
						break;

					case 'is_search':
						if ( is_search() ) return true;
						break;

					case 'is_404':
						if ( is_404() ) return true;
						break;

					// WooCommerce
					case 'is_shop':
						if ( function_exists( 'is_shop' ) && is_shop() ) return true;
						break;

					case 'is_product':
						if ( function_exists( 'is_product' ) && is_product() ) return true;
						break;

					case 'is_cart':
						if ( function_exists( 'is_cart' ) && is_cart() ) return true;
						break;

					case 'is_checkout':
						if ( function_exists( 'is_checkout' ) && is_checkout() ) return true;
						break;

					// Product category: is_product_category:slug
					case 'is_product_category':
						if ( function_exists( 'is_product_category' ) ) {
							if ( $arg && is_product_category( sanitize_text_field( $arg ) ) ) return true;
							if ( is_product_category() ) return true;
						}
						break;

					case 'is_user_logged_in':
						if ( function_exists( 'is_user_logged_in' ) && is_user_logged_in() ) return true;
						break;

					case '!is_user_logged_in':
						if ( function_exists( 'is_user_logged_in' ) && !is_user_logged_in() ) return true;
						break;

				}
			}

			// ----------------------------
			// 2) User roles
			// ----------------------------
			// value == role slug (editor, author, subscriber, etc.)
			// ----------------------------
			if ( taxonomy_exists( 'role' ) ) { /* ignore — not used */ }

			$user = wp_get_current_user();
			if ( $user && in_array( $cond, (array) $user->roles, true ) ) {
				return true;
			}

			// ----------------------------
			// 3) Menu ID condition
			// ----------------------------
			// If condition equals menu term_id
			// ----------------------------
			if ( is_numeric( $cond ) ) {
				$menus = wp_get_nav_menu_items( intval( $cond ) );
				if ( ! empty( $menus ) ) {
					// Match if current page matches any menu item
					foreach ( $menus as $item ) {
						if ( get_the_ID() == $item->object_id ) {
							return true;
						}
					}
				}
			}

			// ----------------------------
			// 4) Widget area condition
			// ----------------------------
			global $wp_registered_sidebars;
			if ( isset( $wp_registered_sidebars[ $cond ] ) ) {
				// If you want to check that this page uses this sidebar, implement here.
				// For now, just allow true (same as current OceanWP behavior).
				return true;
			}

			// ----------------------------
			// 5) OceanWP Library template ID
			// ----------------------------
			if ( is_numeric( $cond ) && get_post_type( intval( $cond ) ) === 'oceanwp_library' ) {
				// Match if user selected this template ID
				if ( intval( $cond ) === get_the_ID() ) {
					return true;
				}
			}
		}

		return false;
	}
}

/**
 * Parse condition string into array of conditions
 */
function oe_parse_condition_string( $str ) {

    if ( empty( $str ) ) {
        return [];
    }

    // Split by || or &&
    $parts = preg_split( '/\s*(\|\||&&)\s*/', $str );

    $final = [];

    foreach ( $parts as $p ) {
        $p = trim($p);

        // 1. Match: is_page(123), is_single(about)
        if ( preg_match('/^(!?[a-z_]+)\(([\w-]*)\)$/i', $p, $m ) ) {

            // function with no args: is_user_logged_in()
            if ( $m[2] === '' ) {
                $final[] = $m[1] . '()';
            } else {
                $final[] = $m[1] . '(' . $m[2] . ')';
            }
            continue;
        }

        // 2. Match: is_page:123 → convert to is_page(123)
        if ( preg_match('/^(!?[a-z_]+):([\w-]+)$/i', $p, $m ) ) {
            $final[] = $m[1] . '(' . $m[2] . ')';
            continue;
        }

        // 3. Match: is_user_logged_in OR !is_user_logged_in
        if ( preg_match('/^!?[a-z_]+$/i', $p ) ) {
            $final[] = $p . '()';
            continue;
        }
    }

    return $final;
}
