<?php
/**
 * Ajax: Escrows
 * 
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

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

add_action( 'wp_ajax_dce_close_escrow', 'dce_ajax_admin_escrow_actions' );
/**
 * Close user's escrow
 */
function dce_ajax_admin_escrow_actions()
{
	if ( !current_user_can( 'manage_options' ) )
		dce_ajax_error( 'permission', __( 'You do not have permission to access here.', 'dce' ) );

	// offer
	$escrow = new DCE_Escrow( (int) dce_get_value( 'escrow' ) );
	if ( !$escrow->exists() )
		dce_ajax_error( 'escrow', __( 'Invalid escow ID', 'dce' ) );

	$update = $escrow->change_status( 'closed' );
	if ( is_wp_error( $update ) )
		dce_ajax_error( $update->get_error_code(), $update->get_error_message() );

	// success
	dce_ajax_response( 'closed' );
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
		$field_args['value'] = dce_parse_input( $field_name, $field_args );
	}

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

	// current user
	$user = DCE_User::get_current_user();

	// save offer
	$escrow = $user->save_escrow( $form_fields['from_amount']['value'], 
									$form_fields['from_coin']['value'], 
									$form_fields['to_amount']['value'], 
									$form_fields['to_coin']['value'], 
									array ( 
											'target_email' => strtolower( $form_fields['target_email']['value'] ), 
											'comm_method' => $form_fields['comm_method']['value'], 
											'details' => $form_fields['details']['value'],
									) );
	if ( is_wp_error( $escrow ) )
		dce_ajax_error( 'save', __( 'Error saving offer, please try again later', 'dce' ) );

	// success
	dce_ajax_response( $escrow->url() );
}






















