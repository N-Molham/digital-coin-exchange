<?php
/**
 * Single: Offer
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */
/* @var $dce_user DCE_User */
global $dce_user;

// current offer
$offer = new DCE_offer( get_post() );
if ( !$offer->exists() )
	return dce_alert_message( __( 'Unknown offer', 'dce' ), 'error' );

// output holder
$output = '';

$coin_types = dce_get_coin_types();
$form_display = $offer->convert_from_display( $coin_types );
$to_display = $offer->convert_to_display( $coin_types );

// data table start
$output .= dce_table_start( 'single-offer' );

// form fields for data display
$fields = DCE_offer::form_fields( $coin_types );

// convert from
$output .= '<tr><th>'. __( 'Convert From', 'dce' ) .'</th><td>'. $offer->convert_from_display( $coin_types ) .'</td></tr>';

// convert to
$output .= '<tr><th>'. __( 'Convert To', 'dce' ) .'</th><td>'. $offer->convert_to_display( $coin_types ) .'</td></tr>';

// Commission payment
$output .= '<tr><th>'. $fields['comm_method']['label'] .'</th><td>'. $offer->commission_method_display() .'</td></tr>';

// details
$output .= '<tr><th>'. $fields['details']['label'] .'</th><td>'. $offer->details .'</td></tr>';

// table end
$output .= dce_table_end();

return $output;














