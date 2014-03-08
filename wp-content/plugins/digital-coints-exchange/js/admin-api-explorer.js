/**
 * WP-Admin API Explorer
 */
( function ( window ) {
	jQuery( function( $ ) {

		// init vars
		var command_index = -1,
			history_index = -1,
			$results = $( '#api-result' ),
			$form = $( '#api-from' );
			$command = $form.find( '#api-command' ).trigger( 'focus' ),
			$coins = $form.find( '#api-coin' );

		// form submit
		$form.on( 'submit', function ( e ) {
			e.preventDefault();

			$command.addClass( 'disabled' ).trigger( 'blur' );
			$.post( ajaxurl, $form.serialize(), function ( response ) {
				// output results
				$results.append( '<p class="command">&gt; '+ $coins.find( 'option:selected' ).text() +' =&gt; <a href="#" data-coin="'+ $coins.val() +'">'+ $command.val() +'</a></p><p class="output">'+ response +'</p>' )
						.scrollTop( $results[0].scrollHeight );
				// enable command
				$command.removeClass( 'disabled' ).trigger( 'focus' ).val( '' );
				// set index
				command_index++;
				history_index = command_index;
			} );
		} );

		// command input history browsing
		$command.on( 'keydown', function( e ) {
			if ( e.keyCode == 38 ) {
				// up 38
				history_index--;
			} else if ( e.keyCode == 40 ) {
				// down 40
				history_index++;
			} else {
				return;
			}

			// range
			if ( history_index > command_index )
				history_index = command_index;
			else if ( history_index < 0 )
				history_index = 0;

			// trigger click
			trace( $results.find( '.command:eq('+ history_index +') a' ).trigger( 'history-click' ) );
		} );

		// history click
		$results.on( 'click history-click', '.command a', function ( e ) {
			e.preventDefault();

			// copy history in command input
			$command.val( e.currentTarget.innerText ).trigger( 'focus' );
			$coins.val( e.currentTarget.dataset.coin );
		} );

	});
} )( window );