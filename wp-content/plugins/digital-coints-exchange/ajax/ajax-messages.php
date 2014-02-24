<?php
/**
 * Messages
 * 
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

add_action( 'wp_ajax_send_message', 'dce_ajax_send_message' );
/**
 * Send Message
*/
function dce_ajax_send_message()
{
	check_ajax_referer( 'dce_send_message', 'nonce' );

	// message
	$message = sanitize_text_field( dce_get_value( 'message' ) );
	if ( '' == $message || empty( $message ) || !DCE_Utiles::is_str_length_between( $message, 4, 1000 ) )
		dce_ajax_error( 'message', dce_alert_message( sprintf( __( '%s character length must be between %d and %d', 'dce' ), __( 'Message', 'dce' ), 4, 1000 ), 'error' ) );

	// type
	$type = sanitize_key( dce_get_value( 'type' ) );
	if ( !in_array( $type, array( 'offer', 'escrow' ) ) )
		dce_ajax_error( 'type', dce_alert_message( __( 'Error Happened, please try again later', 'dce' ), 'error' ) );

	// target user
	$target_user = new DCE_User( (int) dce_get_value( 'user' ) );
	if ( !$target_user->exists() )
		dce_ajax_error( 'user', dce_alert_message( __( 'Unknown user', 'dce' ), 'error' ) );

	// target object
	$object_id = (int) dce_get_value( 'target' );
	if ( 'offer' == $type )
	{
		// target: offer
		$object = new DCE_Offer( $object_id );

		// check
		if ( !$object->exists() )
			$object_id = null;
	}
	else
	{
		// target: escrow
		$object = new DCE_Escrow( $object_id );

		if ( !$object->exists() || !$object->check_user( $target_user->data->user_email ) )
			$object_id = null;
	}

	if ( !$object_id )
		dce_ajax_error( 'target', dce_alert_message( __( 'Unknown target', 'dce' ), 'error' ) );

	$current_user = DCE_User::get_current_user();

	// send message
	$message_id = $current_user->send_message( $target_user->ID, $message, $object_id, $type );
	if ( !$message_id )
		dce_ajax_error( 'send', dce_alert_message( __( 'Error sending message, please try again later', 'dce' ), 'error' ) );

	// success
	dce_ajax_response( dce_alert_message( __( 'Message sent successfully.', 'dce' ), 'success' ) );
}



























