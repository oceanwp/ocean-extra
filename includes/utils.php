<?php
/**
 * Ocean Extra: Utils
 *
 * @package OceanWP WordPress theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (!function_exists('oe_get_page_by_title')) {
    /**
	 * Get Page by title.
	 */
	function oe_get_page_by_title( $page_title, $output = OBJECT, $post_type = 'page' ) {

		$query = new WP_Query(
			array(
				'post_type'              => $post_type,
				'title'                  => $page_title,
				'post_status'            => 'all',
				'posts_per_page'         => 1,
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'orderby'                => 'date',
				'order'                  => 'ASC',
			)
		);

		if ( ! empty( $query->post ) ) {
			$_post = $query->post;

			if ( ARRAY_A === $output ) {
				return $_post->to_array();
			} elseif ( ARRAY_N === $output ) {
				return array_values( $_post->to_array() );
			}

			return $_post;
		}

		return null;
	}
}

if (! function_exists('oe_get_remote')) {

    function oe_get_remote( $url ) {

        // Get data
        $response = wp_remote_get( $url );

        // Check for errors
        if ( is_wp_error( $response ) or ( wp_remote_retrieve_response_code( $response ) != 200 ) ) {
            return false;
        }

        // Get remote body val
        $body = wp_remote_retrieve_body( $response );

        // Return data
        if ( ! empty( $body ) ) {
            return $body;
        } else {
            return false;
        }
    }
}
