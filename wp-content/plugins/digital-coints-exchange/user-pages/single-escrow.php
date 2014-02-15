<?php
/**
 * Single: Escrow
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */
/* @var $dce_user DCE_User */
global $dce_user;

// current escrow
$escrow = new DCE_Escrow( get_post() );
if ( !$escrow->exists() )
	return dce_alert_message( __( 'Unknown escrow', 'dce' ), 'error' );

// output holder
$output = '';

$coin_types = dce_get_coin_types();

//check if the current viewer is the target user ?
$target = strtolower($dce_user->data->user_email) == strtolower($escrow->target_email);
//show what to send
$send_text = $target ?$escrow->convert_to_display( $coin_types ):$escrow->convert_from_display( $coin_types );

// output address
$receive_address = __( 'Please send', 'dce' ) .' '.$send_text.' to this address : ';
$receive_address .= '<code>'. ( $target ? $escrow->target_address : $escrow->owner_address ) .'</code>';

//show what will be received
$receive_text = $target ?$escrow->convert_from_display( $coin_types ):$escrow->convert_to_display( $coin_types );
$receive_address .='<p style="text-align:center; margin-top:10px !important;">you will be notified once the other party sends <strong>'.$receive_text.'</strong> to us , if for any reason they did not send it on time , you will get your coins back with no commissions.</p>';

// display
$output .= dce_promotion_box( $receive_address );

// data table start
$output .= dce_table_start( 'single-escrow' );

// form fields for data display
$fields = DCE_Escrow::form_fields( $coin_types );

//escrow status
$output .= '<tr><th>'. __( 'Escrow Status', 'dce' ) .'</th><td>'. $escrow->get_status() .'</td></tr>';

//escrow status
$output .= '<tr><th>'. __( 'Target User', 'dce' ) .'</th><td>'. $escrow->target_email .'</td></tr>';

// convert from
$output .= '<tr><th>'. __( 'Convert From', 'dce' ) .'</th><td>'. $escrow->convert_from_display( $coin_types ) .'</td></tr>';

// convert to
$output .= '<tr><th>'. __( 'Convert To', 'dce' ) .'</th><td>'. $escrow->convert_to_display( $coin_types ) .'</td></tr>';

// Commission payment
$output .= '<tr><th>'. $fields['comm_method']['label'] .'</th><td>'. $escrow->commission_method_display() .'</td></tr>';

// details
$output .= '<tr><th>'. $fields['details']['label'] .'</th><td>'. $escrow->details .'</td></tr>';

// table end
$output .= dce_table_end();

return $output;
















