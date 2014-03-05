<?php
/**
 * Ajax: Admin
 * 
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

add_action( 'wp_ajax_test_rpc_connection', 'dce_ajax_test_rpc_connection' );
/**
 * Test Coin RPC connection
 */
function dce_ajax_test_rpc_connection()
{
	if ( !current_user_can( 'manage_options' ) )
		dce_ajax_error( 'permission', __( 'You do not have permission to access here.', 'dce' ) );

	// get basic coins info
	dump_data( ( new DCE_RPC_Client( dce_get_value( 'url' ) ) )->getinfo() );
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







