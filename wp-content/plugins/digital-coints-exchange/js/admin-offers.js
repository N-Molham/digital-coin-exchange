/**
 * Offers
 */
( function ( window ) {
	jQuery( function( $ ) {

		// new offer submit
		$( '.offer-actions .button' ).on( 'click', function( e ) {
			e.preventDefault();

			// post data
			$.post( ajaxurl, $(this).data(), function( response ) {
				if ( response.status ) {
					// success
					location.href = location.href;
				} else {
					// error
					alert( response.error.message );
				}
			}, 'json' );
		} );

	});
} )( window );