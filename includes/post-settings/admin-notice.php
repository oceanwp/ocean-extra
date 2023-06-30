<?php
/**
 * OceanWP Notice
 *
 * @package Ocean_Extra
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Check if legacy metabox data exist.
$meta_exist = oe_check_old_meta();

// Getmigration status.
$meta_migrated = get_option( 'ocean_metabox_migration_status' );

/**
 * Display migration notice if legacy meta exist but not migrated.
 */
if ( $meta_exist && 'true' !== $meta_migrated ) {
	add_action( 'admin_notices', 'oe_migrate_metabox_notice' );
}

/**
 *  Admin notice for migrating metabox database
 *
 * @return void
 */
function oe_migrate_metabox_notice() {

	$message = '<div id="ocean-migrate-action"></div>';

	$html_message = sprintf( '<div class="notice notice-warning notice-migrate-metabox is-dismissible">%s</div>', wpautop( $message ) );

	echo wp_kses_post( $html_message );
}
