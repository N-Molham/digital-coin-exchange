/**
 * Messages
 */
( function ( window ) {
	jQuery( function( $ ) {

		// message form
		window.send_form = $( '#send-message-form' );
		if ( !window.send_form.length )
			window.send_form = null;

		// response handlers
		window.new_message_sent = function ( response, $form, $errors_holder ) {
			if ( response.status ) {
				// display success message
				$errors_holder.html( response.data );

				// hide inputs
				$form.find( 'label, .form-input' ).hide();

				// close form
				if( typeof window.is_form_open == 'boolean' ) {
					setTimeout( function () {
						$.prettyPhoto.close();
					}, 2000 );
				}
			}
		};

		// send message data fill
		$( 'a[rel^=sendform]' ).on( 'click', function( e ) {
			e.preventDefault();
			// check form
			if ( window.send_form ) {
				var data = e.currentTarget.dataset;

				// fill data
				window.send_form.find( '.send-to' ).text( data.userDisplay );
				window.send_form.find( 'input[name=user]' ).val( data.user );
				window.send_form.find( 'input[name=target]' ).val( data.target );
				window.send_form.find( 'input[name=type]' ).val( data.type );
			}

			// display form
			//$.prettyPhoto.open( '#contact-from-lightbox' );
			window.is_form_open = true;
		} );

	});
} )( window );