<?php
/**
 * Single: Escrow
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */
/* @var $dce_user DCE_User */
global $dce_user;

// enqueues
wp_enqueue_script( 'dce-escrows' );

// current escrow
$escrow = new DCE_Escrow( get_post() );
if ( !$escrow->exists() )
	return dce_alert_message( __( 'Unknown escrow', 'dce' ), 'error' );

// is the current logged in user is the owner/creator
$is_owner = $escrow->is_user_owner( $dce_user->data->user_email );

// output holder
$output = '';

$coin_types = dce_get_coin_types();
$form_display = $escrow->convert_from_display( $coin_types );
$to_display = $escrow->convert_to_display( $coin_types );
$exchange_addresses = '';

// exchange addresses
// send address
if ( $is_owner )
{
	$exchange_addresses .= '<p>'. sprintf( __( 'Send <strong>%s</strong> to us on this Address', 'dce' ), $form_display ) .' : ';
	$exchange_addresses .= '<code>'. $escrow->owner_address .'</code></p>';
	$exchange_addresses .= '<p>'. sprintf( __( 'You will be notified once the other party sends <strong>%s</strong> to us, if for any reason they did not send it on time, you will get your coins back with no commissions.', 'dce' ), $to_display ) .'</p>';
}
else
{
	$exchange_addresses .= '<p>'. sprintf( __( 'Send <strong>%s</strong> to us on this Address', 'dce' ), $to_display ) .' : ';
	$exchange_addresses .= '<code>'. $escrow->target_address .'</code></p>';
	$exchange_addresses .= '<p>'. sprintf( __( 'You will be notified once the other party sends <strong>%s</strong> to us, if for any reason they did not send it on time, you will get your coins back with no commissions.', 'dce' ), $form_display ) .'</p>';
}

// receive addresses
$exchange_addresses .= dce_divider( 'double' );
$exchange_addresses .= '<p>'. __( 'Set below the address you will receive the exchanged coins on', 'dce' ) .'</p>';
$exchange_addresses .= '<div class="receive-address"><form action="" method="post" class="ajax-form" data-callback="receive_address_callback">';
$exchange_addresses .= '<input type="text" class="input-text input-code" name="receive_address" value="'. ( $is_owner ? $escrow->owner_receive_address : $escrow->target_receive_address ) .'" />';
$exchange_addresses .= '<input type="submit" value="'. __( 'Save', 'dce' ) .'" class="button small green" />';
$exchange_addresses .= '<input type="hidden" name="action" value="save_receive_address" />';
$exchange_addresses .= '<input type="hidden" name="escrow" value="'. $escrow->ID .'" />';
$exchange_addresses .= wp_nonce_field( 'dce_receive_address', 'nonce', false, false ) .'</form></div>'; 

// display
$output .= dce_promotion_box( $exchange_addresses );

// data table start
$output .= dce_table_start( 'single-escrow' );

// form fields for data display
$fields = DCE_Escrow::form_fields( $coin_types );

// Creator
$output .= '<tr><th>'. __( 'Creator', 'dce' ) .'</th><td>'. $escrow->user->display_name() .'</td></tr>';

// other party
$output .= '<tr><th>'. __( 'Other Party', 'dce' ) .'</th><td>'. $escrow->target_user->display_name() .'</td></tr>';

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














