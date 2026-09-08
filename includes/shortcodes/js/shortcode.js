/**
 * Apply ajax to get values of fragments when update cart
 * @param {type} $
 * @returns {undefined}
 */

jQuery( function( $ ) {

	$( document.body ).on( 'added_to_cart removed_from_cart', function( event, fragments, cart_hash ) {
		var e = $.Event( 'storage' );

		e.originalEvent = {
			key: wc_cart_fragments_params.cart_hash_key,
		};

		$( '.oceanwp-woo-free-shipping' ).each( function( i, obj ) {
			var spanSelect = $( obj ),
				content = spanSelect.attr( 'data-content' ),
				rechData = spanSelect.attr( 'data-reach' ),
				multiplyBy = spanSelect.attr( 'data-multiply-by' );

			$.ajax( {
				type: 'post',
				dataType: 'json',
				url: woocommerce_params.ajax_url,
				data: {
					action: 'update_oceanwp_woo_free_shipping_left_shortcode',
					content: content,
					content_rech_data: rechData,
					multiply_by: multiplyBy
				},

				success: function( response ) {
					spanSelect.html( '' );
					spanSelect.html( response );
				}
			} );
		} );

		$( window ).trigger( e );
	} );

} );