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

// creator
$output .= '<tr><th>'. __( 'Creator', 'dce' ) .'</th><td><a href="'. $offer->user->profile_url() .'">'. $offer->user->display_name() .'</a></td></tr>';

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

// contact form
$output .= '<a href="#contact-from-lightbox" rel="sendform" class="button small green contact" data-user-display="'. esc_attr( $offer->user->display_name() ) .'" data-user="'. $offer->user->ID .'" data-target="'. $offer->ID .'" data-type="offer">'. __( 'Contact', 'offer' ) .'</a>';
$output .= '<div id="contact-from-lightbox"><div class="send-message post-content">'. do_shortcode( '[dce-send-message lightbox="yes"]' ) .'</div></div>';

return $output;














