/**
 * Offers
 */
( function ( window ) {
	jQuery( function( $ ) {

		// new offer submit
		$( '#new-offer-form' ).on( 'submit', function( e ) {
			e.preventDefault();
			var $form = $( this );

			// post data
			$.post( dce.ajax_url, $form.serialize(), function( response ) {
				trace( response );
				if ( response.status ) {
					// success
					location.href = update_query_value( location.href, 'view', 'view_offers' );
				} else {
					// error
					alert( response.error.message );
				}
			}, 'json' );
		} );

	});
} )( window );