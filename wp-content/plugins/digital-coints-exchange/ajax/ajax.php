<?php
/**
 * Ajax
 * 
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

add_action( 'wp_ajax_new_coin_type_item', 'dce_ajax_new_coin_item_layout' );
/**
 * New coin type html layout
 */
function dce_ajax_new_coin_item_layout()
{
	if ( !current_user_can( 'manage_options' ) )
		dce_ajax_error( 'permission', __( 'You do not have permission to access here.', 'dce' ) );

	// new item layout
	dce_ajax_response( dce_admin_settings_coin_item_template( 'new-'. (int) dce_get_value( 'new_index' ), '', 'dce_admin_options[coin_types]' ) );
}

/**
 * AJAX Debug response
 *
 * @param mixed $data
 */
function dce_ajax_debug( $data )
{
	// return dump
	dce_ajax_error( 'debug', dump_data_export( $data ) );
}

/**
 * AJAX Error response
 *
 * @param string $error_key
 * @param mixed $error_message
 */
function dce_ajax_error( $error_key, $error_message )
{
	// error obj
	$error = array( 'key' => $error_key, 'message' => $error_message );

	// send response
	dce_ajax_response( $error, false );
}

/**
 * AJAX JSON Response
 *
 * @param mixed $data
 * @param boolean $status
 */
function dce_ajax_response( $data, $status = true )
{
	// set response header content type
	header( 'Content-Type:application/json' );

	// response body
	$response = array ( 'status' => $status );

	// response type
	if ( $status )
	{
		// success response
		$response['data'] = $data;
	}
	else
	{
		// failure/error response
		$response['error'] = $data;
	}

	// send response
	die( json_encode( $response ) );
}