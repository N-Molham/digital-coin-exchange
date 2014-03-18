/**
 * Users' Escrows
 */
( function ( window ) {
	jQuery( function( $ ) {

		// auto-focus
		var $escrow_form = $( '#new-escrow-form' );

		if ( $escrow_form.length && window.$viewport && window.$viewport.length ) {
			// escrow wizard
			$( '#escrow-wizard' ).steps( {
				headerTag: 'h3',
				transitionEffect: 'slideLeft',
				transitionEffectSpeed: 200,
				labels: {
					finish: dce_escrow.wizard_finish_label,
					next: dce_escrow.wizard_next_label,
					previous: dce_escrow.wizard_previous_label
				},
				onStepChanging: wizard_step_chaning,
				onFinishing: wizard_step_chaning
			} );

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

		// wizard change
		function wizard_step_chaning( e, current_index, new_index ) {
			var no_errors = true;

			// skip previous step
			if ( current_index > new_index )
				return true;

			$( '#escrow-wizard-p-'+ current_index +' :input' ).each( function( index, input ) {
				var $input = $( input ),
					value = trim( $input.val() ),
					is_checkbox = $input.is( ':checkbox' );

				// check empty values
				if ( value == '' || value == '-1' || value == 'none' || ( is_checkbox && !$input.is( ':checked' ) ) ) {
					if ( is_checkbox ) {
						$input.parent().addClass( 'error' );
					} else {
						$input.addClass( 'error' );
					}
					no_errors = false;
				} else {
					// clear errors
					if ( is_checkbox ) {
						$input.parent().removeClass( 'error' );
					} else {
						$input.removeClass( 'error' );
					}
				}
			} );

			// finishing step
			if ( typeof new_index == 'undefined' ) {
				// submit form
				$escrow_form.trigger( 'submit' );
			}

			return no_errors;
		}

		// new escrow
		window.new_escrow_callback = function( response ) {
			if ( response.status ) {
				// success
				location.href = response.data;
			}
		};
		
		// receive address save
		window.receive_address_callback = function( response, $form, $messages ) {
			if ( response.status ) {
				$messages.html( response.data );
			}
		};

	});
} )( window );