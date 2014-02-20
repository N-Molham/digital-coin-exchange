/**
 * Public Offers
 */
( function ( window ) {
	jQuery( function( $ ) {

		var $offers = $( '#open-offers' );

		// toggle offer details
		$offers.find( '.button[href*=offer-details]' ).on( 'click', function( e ) {
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

		// send message data fill
		$offers.find( '.contact' ).on( 'click', function( e ) {
			e.preventDefault();

			// check form
			if ( window.send_form ) {
				var data = e.currentTarget.dataset;

				// fill data
				window.send_form.find( '.send-to' ).text( data.userDisplay );
				window.send_form.find( 'input[name=user]' ).val( data.user );
				window.send_form.find( 'input[name=target]' ).val( data.offer );
				window.send_form.find( 'input[name=type]' ).val( 'offer' );
			}
			
			// display form
			$.prettyPhoto.open( '#contact-from-lightbox' );

		} );

	});
} )( window );