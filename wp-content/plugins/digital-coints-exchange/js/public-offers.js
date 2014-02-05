/**
 * Public Offers
 */
( function ( window ) {
	jQuery( function( $ ) {

		// toggle offer details
		$( '#open-offers .button' ).on( 'click', function( e ) {
			e.preventDefault();

			var $details = $( e.currentTarget.hash );
			if ( !$details && !$details.length ) {
				return false;
			}

			// toggle
			if ( $details.is( ':visible' ) ) {
				// hide
				$details.find( '.content' ).slideUp( 'fast', function () {
					$details.hide();
				} );
			} else {
				// show
				$details.show().find( '.content' ).slideDown( 'fast' );
			}
		} );

	});
} )( window );