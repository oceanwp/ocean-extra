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

/**
 * Get all meta.
 *
 * @return array
 */
function oe_get_all_meta() {

	global $post;

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
 * Get meta settings.
 *
 * @var string $option Option name.
 *
 * @return string|array
 */
function oe_get_meta( $option ) {

	global $post;

	$result       = '';
	$options      = oe_get_all_meta();
	$defaults     = ocean_post_setting_data();
	$migrated     = get_option( 'ocean_metabox_migration_status' );
	$block_editor = get_post_meta( $post->ID, 'ocean_is_block_editor', true );


	if ( ! empty( $defaults  ) ) {
		foreach ( $defaults as $key => $value ) {
			if ( $option === $key ) {
				if ( 'true' === $migrated && 'yes' === $block_editor ) {
					$result = $options[ $option ][0];
				} else {
					if ( $value['map'] && in_array( $value['map'], $options, true ) ) {
						$result = $options[ $value['map'] ][0];
					}
				}
			}
		}
	}

	return apply_filters( 'oe_get_meta', $result );
}

/**
 * Check if old meta fields exist
 *
 * @return boolean
 */
function oe_check_old_meta() {

	$status = false;

	$posts = get_posts( array(
		'post_type'   => get_post_types(),
		'numberposts' => -1,
		'post_status' => 'any'
	) );

	$newMetaData = ocean_post_setting_data();

	foreach ( $posts as $post ) {
		$oldMetaData = get_post_meta( $post->ID );
		foreach ( $newMetaData as $key => $value ) {
			foreach ( $oldMetaData as $keyname => $option ) {
				if (  $value['map'] === $keyname ) {
					$status = true;
					break;
				}
			}
		}
	}

	return apply_filters( 'oe_check_old_meta', $status );
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

	// Return data.
	return apply_filters( 'ocean_post_settings_data_choices', $data );

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
	$post_types = apply_filters( 'ocean_main_metaboxes_post_types', array(
		'post',
		'page',
		'product',
		'elementor_library',
		'ae_global_templates',
	) );

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

    $result = '';

	if ( function_exists( 'get_current_screen' )  ) {
		$current_screen = get_current_screen();

		if ( isset( $current_screen ) ) {
			$result = $current_screen->is_block_editor;
		}
	}

    return $result;
}
