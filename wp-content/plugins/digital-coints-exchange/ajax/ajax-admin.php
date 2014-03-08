<?php
/**
 * Ajax: Admin
 * 
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

add_action( 'wp_ajax_dce_api_explor', 'dce_ajax_dce_api_explor' );
/**
 * Coin RPC API Explorer
 */
function dce_ajax_dce_api_explor()
{
	if ( !current_user_can( 'manage_options' ) )
		dce_ajax_error( 'permission', __( 'You do not have permission to access here.', 'dce' ) );

	// command
	preg_match_all( '/("[^"]+"|[^\s"]+)/', dce_get_value( 'api_command' ), $command_parts );
	if ( empty( $command_parts ) )
		die( __( 'Unknown command', 'dce' ) );

	// coin
	$coin_type = sanitize_key( dce_get_value( 'api_coin' ) );
	$coin_data = dce_get_coin_types( $coin_type );
	if ( !$coin_data )
		die( __( 'Unknown coin type', 'dce' ) );

	// get rpc connection
	$rpc_client = dce_coins_rpc_connections( $coin_type, $coin_data );

	// method
	$method = $command_parts[0][0];

	// method params
	array_shift( $command_parts[0] );

	// execute command
	$result = call_user_func_array( array( $rpc_client, sanitize_key( $method ) ), $command_parts[0] );

	// is error
	if ( is_wp_error( $result ) )
		$output = 'Error => code: '. $result->get_error_code() .', message: '. $result->get_error_message();
	else
		$output = is_array( $result ) || is_object( $result ) ? DCE_Utiles::pretty_json( $rpc_client->raw_response ) : $result;

	// set header
	header( 'Content-type: text/plain; charset=utf-8' );
	die( $output );
}

add_action( 'wp_ajax_test_rpc_connection', 'dce_ajax_test_rpc_connection' );
/**
 * Test Coin RPC connection
 */
function dce_ajax_test_rpc_connection()
{
	if ( !current_user_can( 'manage_options' ) )
		dce_ajax_error( 'permission', __( 'You do not have permission to access here.', 'dce' ) );

	// get basic coins info
	dump_data( dce_coins_rpc_connections( 'test', array ( 
			'rpc_user' => dce_get_value( 'rpc_user' ), 
			'rpc_pass' => dce_get_value( 'rpc_pass' ), 
			'rpc_host' => dce_get_value( 'rpc_host' ), 
			'rpc_port' => dce_get_value( 'rpc_port' ), 
			'rpc_uri' => dce_get_value( 'rpc_uri' ), 
	) )->getinfo() );
	die();
}

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







