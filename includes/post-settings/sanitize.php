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
* Sanitize function for integer
*/
function ops_sanitize_absint( $value ) {
	return $value && is_numeric( $value ) ? absint( $value ) : '';
}


/**
* Sanitize function for decimal
*/
function ops_sanitize_decimal( $value ) {
	if (is_numeric($value)) {
		return round((float) $value, 2);
	} else {
		return '';
	}
}

/**
* Sanitize function for array
*/
function ops_sanitize_array($meta_value) {
	if (!is_array($meta_value)) {
		return array();
	}

	foreach ($meta_value as $key => $value) {
		$meta_value[$key] = wp_kses_post($value);
	}

	return $meta_value;
}
