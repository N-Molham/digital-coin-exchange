<?php
/**
 * Ajax: Escrows
 * 
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

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
											'target_email' => $form_fields['target_email']['value'], 
											'comm_method' => $form_fields['comm_method']['value'], 
											'details' => $form_fields['details']['value'],
									) );
	if ( is_wp_error( $escrow ) )
		dce_ajax_error( 'save', __( 'Error saving offer, please try again later', 'dce' ) );

	// success
	dce_ajax_response( $escrow );
}





















