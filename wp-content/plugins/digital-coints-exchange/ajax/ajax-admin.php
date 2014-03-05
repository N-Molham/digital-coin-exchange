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
