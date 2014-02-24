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

		if( $().prettyPhoto ) {
			var pp_args = {
					animation_speed: 'fast',
					overlay_gallery: true,
					keyboard_shortcuts: false,
					autoplay_slideshow: false,
					opacity: 0.8,
					show_title: false,
					show_desc: false,
					social_tools: ''
			};
			$( 'a[rel^=sendform]').prettyPhoto( pp_args );

			var media_query = 'desk';
			if ( Modernizr.mq( 'only screen and (max-width: 600px)' ) || Modernizr.mq( 'only screen and (max-height: 520px)' ) ) {
				media_query = 'mobile';
				$( 'a[rel=sendform]' ).unbind( 'click' );
			}

			// Disables prettyPhoto if screen small
			$( window ).on( 'resize', function() {
				if ( ( Modernizr.mq( 'only screen and (max-width: 600px)' ) || Modernizr.mq( 'only screen and (max-height: 520px)' ) ) && media_query == 'desk' ) {
					$( 'a[rel=sendform]' ).unbind( 'click.prettyphoto' );
					media_query = 'mobile';
				} else if ( !Modernizr.mq( 'only screen and (max-width: 600px)' ) && !Modernizr.mq( 'only screen and (max-height: 520px)' ) && media_query == 'mobile' ) {
					$( 'a[rel=sendform]' ).prettyPhoto( pp_args );
					media_query = 'desk';
				}
			} );
		}

	});
} )( window );