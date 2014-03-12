/**
 * Feedback rating
 */
( function ( window ) {
	jQuery( function( $ ) {

		// feedback
		window.user_feedback_callback = function( response, $form, $errors_holder ) {
			if ( response.status ) {
				// display message
				$errors_holder.html( response.data );

				// hide inputs
				$form.find( 'label, .rateit, .form-input' ).slideUp();

				// scroll
				window.$viewport.animate( {
					scrollTop: $errors_holder.offset().top - 100
				}, 500 );
			}
		};

	});
} )( window );