/**
 * Users' Escrows
 */
( function ( window ) {
	jQuery( function( $ ) {

		// auto-focus
		var $escrow_form = $( '#new-escrow-form' );
		if ( $escrow_form.length && window.$viewport && window.$viewport.length ) {
			// get input to focus
			var $target_focus = $escrow_form.find( ':input[name='+ $escrow_form.data( 'focus' ) +']' );
			if ( $target_focus.length ) {
				// focus on input
				$target_focus.focus();
				// scroll to it
				window.$viewport.animate( {
					scrollTop: $target_focus.offset().top - 100
				}, 500 );
			}
		}

		// new escrow
		window.new_escrow_callback = function( response ) {
			if ( response.status ) {
				// success
				location.href = response.data;
			}
		};

	});
} )( window );