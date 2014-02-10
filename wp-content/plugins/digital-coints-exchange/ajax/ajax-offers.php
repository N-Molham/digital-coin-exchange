<?php
/**
 * Ajax: Offers
 * 
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

add_action( 'wp_ajax_dce_confirm_offer', 'dce_ajax_admin_offer_actions' );
add_action( 'wp_ajax_dce_deny_offer', 'dce_ajax_admin_offer_actions' );
/**
 * Confirm/Deny user's offer
 */
function dce_ajax_admin_offer_actions()
{
	if ( !current_user_can( 'manage_options' ) )
		dce_ajax_error( 'permission', __( 'You do not have permission to access here.', 'dce' ) );

	// offer
	$offer = new DCE_Offer( (int) dce_get_value( 'offer' ) );
	if ( !$offer->exists() )
		dce_ajax_error( 'offer', __( 'Invalid offer ID', 'dce' ) );

	// target action/status
	$status = str_replace( array( 'dce_', '_offer' ), '', dce_get_value( 'action' ) );

	$update = $offer->change_status( $status );
	if ( is_wp_error( $update ) )
		dce_ajax_error( $update->get_error_code(), $update->get_error_message() );

	// success
	dce_ajax_response( $status );
}

add_action( 'wp_ajax_cancel_offer', 'dce_ajax_cancel_offer' );
/**
 * Cancel offer
 */
function dce_ajax_cancel_offer()
{
	// offer ID
	$offer_id = (int) dce_get_value( 'offer' );
	if ( !$offer_id || !check_ajax_referer( 'dce_cancel_nonce_'. $offer_id, 'nonce', false ) )
		dce_ajax_error( 'offer', __( 'Unknown offer!!!', 'dce' ) );

	// check owner
	$offer = new DCE_Offer( $offer_id );
	if ( !$offer->exists() || wp_get_current_user()->ID != $offer->user->ID )
		dce_ajax_error( 'permission', __( 'Unknown offer!!!', 'dce' ) );

	// cancel/delete offer
	$offer->delete();

	// success
	dce_ajax_response( 'done' );
}

add_action( 'wp_ajax_create_offer', 'dce_ajax_create_offer' );
/**
 * Create new offer
 */
function dce_ajax_create_offer()
{
	check_ajax_referer( 'dce_save_offer', 'nonce' );

	// init data
	$coin_types = dce_get_coin_types();
	$form_fields = DCE_Offer::form_fields( $coin_types );

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
	$offer_id = $user->save_offer( $form_fields['from_amount']['value'], 
									$form_fields['from_coin']['value'], 
									$form_fields['to_amount']['value'], 
									$form_fields['to_coin']['value'], 
									array ( 
											'comm_method' => $form_fields['comm_method']['value'], 
											'details' => $form_fields['details']['value'] 
									) );
	if ( is_wp_error( $offer_id ) )
		dce_ajax_error( 'save', __( 'Error saving offer, please try again later', 'dce' ) );

	// success
	dce_ajax_response( $offer_id );
}






















