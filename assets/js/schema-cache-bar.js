jQuery( document ).ready( function( $ ) {
	$( '#wp-admin-bar-schema-cache-clear-all a' ).on( 'click', function( e ) {
		const message = OceanSchemaCacheBar.confirm_message;

		if ( message && ! window.confirm( message ) ) {
			e.preventDefault();
		}
	} );
} );