<?php
/**
 * Ajax: Escrows
 * 
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

add_action( 'wp_ajax_user_feedback', 'dce_ajax_user_feedback' );
/**
 * Escrow users' feedback
 */
function dce_ajax_user_feedback()
{
	check_ajax_referer( 'dce_user_feedback', 'nonce' );

	// target escrow
	$escrow = new DCE_Escrow( (int) dce_get_value( 'escrow' ) );

	// current user
	$user = DCE_User::get_current_user();

	// check escrow status, only allow completed and failed ones
	if ( !$escrow->exists() || !in_array( $escrow->get_status(), array( 'completed', 'failed' ) ) || !$escrow->check_user( $user->user_email ) )
		dce_ajax_error( 'escrow', dce_alert_message( __( 'Unknown Escrow', 'dce' ), 'error', true ) );

	// check if he gave feedback before
	if ( 'yes' == $escrow->get_meta( $user->ID .'_gave_feedback' ) )
		dce_ajax_error( 'rating', dce_alert_message( __( 'You already gave a feedback about this escrow', 'dce' ), 'error', true ) );

	// rating
	$rating = (int) dce_get_value( 'rating' );
	if ( !$rating || $rating < 1 || $rating > 5 )
		dce_ajax_error( 'rating', dce_alert_message( __( 'Invalid rating', 'dce' ), 'error', true ) );

	// feedback
	$feedback = sanitize_text_field( dce_get_value( 'feedback' ) );
	if ( !DCE_Utiles::is_str_length_between( $feedback, 10, 500 ) )
		dce_ajax_error( 'feedback', dce_alert_message( sprintf( __( '%s character length must be between %d and %d', 'dce' ), __( 'Feddback', 'dce' ), 10, 240 ), 'error', true ) );

	$escrow->set_feedback( $user, $escrow->other_party( $user )->ID, $rating, $feedback );
	dce_ajax_response( dce_alert_message( __( 'Thanks for your feedback', 'dce' ), 'success' ) );
}

add_action( 'wp_ajax_save_receive_address', 'dce_ajax_save_receive_address' );
/**
 * Save escrow user receive address
 */
function dce_ajax_save_receive_address()
{
	check_ajax_referer( 'dce_receive_address', 'nonce' );

	// current logged in user
	$user_email = DCE_User::get_current_user()->data->user_email;

	// check escrow && access
	$escrow = new DCE_Escrow( (int) dce_get_value( 'escrow' ) );
	if ( !$escrow->exists() || !$escrow->check_user( $user_email ) )
		dce_ajax_error( 'escrow', dce_alert_message( __( 'Unknown escrow !!!', 'dce' ), 'error' ) );

	$address = dce_get_value( 'receive_address' );
	if ( !DCE_Escrow::verify_address( $address ) )
		dce_ajax_error( 'address', dce_alert_message( __( 'Invalid receive address', 'dce' ), 'error' ) );

	// save address
	$escrow->set_receive_address( $address, $escrow->is_user_owner( $user_email ) );

	// success
	dce_ajax_response( dce_alert_message( __( 'Address Saved', 'dce' ), 'success', true ) );
}

add_action( 'wp_ajax_create_escrow', 'dce_ajax_create_escrow' );
/**
 * Create new escrow
 */
function dce_ajax_create_escrow()
{
	check_ajax_referer( 'dce_save_escrow', 'nonce' );

	// init data
	$coin_types = dce_get_coin_types();
	$form_fields = DCE_Escrow::form_fields( $coin_types );

	// clear old errors
	DCE_Utiles::clear_form_errors();

	// validate form data
	foreach ( $form_fields as $field_name => &$field_args )
	{
		// set amount minimums
		if ( 'from_amount' == $field_name )
			$field_args['min_number'] = @$coin_types[ $_REQUEST['from_coin'] ]['min_amount'];

		if ( 'to_amount' == $field_name )
			$field_args['min_number'] = @$coin_types[ $_REQUEST['to_coin'] ]['min_amount'];

		$field_args['value'] = dce_parse_input( $field_name, $field_args );
	}

	// lower-case email address
	$form_fields['target_email']['value'] = strtolower( $form_fields['target_email']['value'] );

	// current user
	$user = DCE_User::get_current_user();

	// escrow with himself !!!!
	if ( $form_fields['target_email']['value'] == $user->user_email )
		DCE_Utiles::form_error( 'wtf', __( 'WTF, REALLY !!!!', 'dce' ) );

	// error messages
	if ( DCE_Utiles::has_form_errors() )
	{
		$error_messages = '';
		$errors = DCE_Utiles::show_form_errors( false, true );

		foreach ( $errors as $error_message )
		{
			$error_messages .= dce_alert_message( $error_message, 'error' );
		}

		dce_ajax_error( 'form-errors', $error_messages );
	}

	// save offer
	$escrow = $user->save_escrow( $form_fields['from_amount']['value'], 
									$form_fields['from_coin']['value'], 
									$form_fields['to_amount']['value'], 
									$form_fields['to_coin']['value'], 
									array ( 
											'target_email' => $form_fields['target_email']['value'], 
											'comm_method' => $form_fields['comm_method']['value'], 
											'details' => $form_fields['details']['value'],
											'owner_receive_address' => $form_fields['owner_receive_address']['value'], 
											'owner_refund_address' => $form_fields['owner_refund_address']['value'], 
									) );
	if ( is_wp_error( $escrow ) )
		dce_ajax_error( $escrow->get_error_code(), dce_alert_message( __( 'Error saving offer, please try again later', 'dce' ), 'error' ) );

	// success
	dce_ajax_response( $escrow->url() );
}






















