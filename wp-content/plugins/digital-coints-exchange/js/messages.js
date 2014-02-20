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
			trace( response );
		};

	});
} )( window );