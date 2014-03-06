/**
 * WP-Admin Settings
 */
( function ( window ) {
	jQuery( function( $ ) {

		// init vars
		var $coins_list = $( '.coin-types' ),
			new_index = 1;

		// remove button
		$coins_list.on( 'click', '.button-delete', function( e ) {
			e.preventDefault();

			if ( confirm( dce_settings.delete_msg ) )
				$( this ).parent().parent().fadeOut( function( e ) {
					$( this ).remove();
				} );
		} )
		// RPC test
		.on( 'click', '.rpc-test', function( e ) {
			e.preventDefault();

			var $parent = $( this ).parent(),
				data = { 
					action: 'test_rpc_connection' 
				};

			$parent.find( 'input[name*=rpc]' ).each( function( index, item ) {
				var obj = {};
				data[item.name.replace( /\w+\[\w+\]\[\w+\]/, '' ).replace( '[', '' ).replace( ']', '' )] = item.value;
			} );

			$.post( ajaxurl, data, function( response ) {
				$parent.find( '.rpc-test-res' ).html( response );
			} );
		} );

		// add new coin
		$( '.add-coin-type' ).on( 'click', function( e ) {
			e.preventDefault();

			$.post( ajaxurl, { action: 'new_coin_type_item', 'new_index': new_index  }, function ( response ) {
				if ( response.status ) {
					// add new item
					$coins_list.append( response.data );
					new_index++;
				}
			}, 'json' );
		} );

	});
} )( window );