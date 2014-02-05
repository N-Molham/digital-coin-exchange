/**
 * Users' Escrows
 */
( function ( window ) {
	jQuery( function( $ ) {

		// new escrow
		window.new_escrow_callback = function( response ) {
			trace( response );
		};
		/*$( '#new-escrow-form' ).on( 'submit', function( e ) {
			e.preventDefault();
			var $form = $( this );

			// post data
			$.post( dce.ajax_url, $form.serialize(), function( response ) {
				if ( response.status ) {
					// success
					location.href = update_query_value( location.href, 'view', 'view_escrows' );
				} else {
					// error
					alert( response.error.message );
				}
			}, 'json' );
		} );*/

	});
} )( window );