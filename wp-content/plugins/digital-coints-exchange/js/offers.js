/**
 * Users' Offers
 */
( function ( window ) {
	jQuery( function( $ ) {

		// offers cancel
		$( '#user-offers .cancel-offer' ).on( 'click', function( e ) {
			e.preventDefault();

			// post data
			$.post( dce.ajax_url, $( this ).data(), function( response ) {
				if ( response.status ) {
					// success
					location.href = location.href;
				} else {
					// error
					alert( response.error.message );
				}
			}, 'json' );
		} );

		// new offer submit
		$( '#new-offer-form' ).on( 'submit', function( e ) {
			e.preventDefault();
			var $form = $( this );

			// post data
			$.post( dce.ajax_url, $form.serialize(), function( response ) {
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