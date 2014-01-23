<?php
/**
 * Ajax: Offers
 * 
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

add_action( 'wp_ajax_create_offer', 'dce_ajax_create_offer' );
/**
 * Create new offer
 */
function dce_ajax_create_offer()
{
	check_ajax_referer( 'dce_save_offer', 'nonce' );

	// from value
	$from_amount = (int) dce_get_value( 'from_amount' );
	if ( !$from_amount )
		dce_ajax_error( 'from_amount', __( 'From Amount Invalid value', 'dce' ) );

	// to value
	$to_amount = (int) dce_get_value( 'to_amount' );
	if ( !$to_amount )
		dce_ajax_error( 'to_amount', __( 'To Amount Invalid value', 'dce' ) );

	$coin_types = dce_get_coin_types();

	// from coin type
	$from_coin = dce_get_value( 'from_coin' );
	if ( !isset( $coin_types[$from_coin] ) )
		dce_ajax_error( 'from_coin', __( 'From Coin Invalid value', 'dce' ) );

	// to coin type
	$to_coin = dce_get_value( 'to_coin' );
	if ( !isset( $coin_types[$from_coin] ) )
		dce_ajax_error( 'to_coin', __( 'To Coin Invalid value', 'dce' ) );

	// details
	$deal_details = dce_get_value( 'details' );

	// current user
	$user = DCE_User::get_current_user();

	// save offer
	$offer_id = $user->save_offer( $from_amount, $from_coin, $to_amount, $to_coin, array( 'details' => $deal_details ) );
	if ( is_wp_error( $offer_id ) )
		dce_ajax_error( 'save', __( 'Error saving offer, please try again later', 'dce' ) );

	// success
	dce_ajax_response( $offer_id );
}






















