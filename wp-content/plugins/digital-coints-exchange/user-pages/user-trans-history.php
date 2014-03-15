<?php
/**
 * User's Transactions History
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

/* @var $dce_user DCE_User */
global $dce_user;

// shortcode output
$output = '';

// get transactions
$user_transactions = $dce_user->get_transactions_history();

// table start
$output .= dce_table_start( 'user-trans' ) .'<thead><tr>';
$output .= '<th>'. __( 'Escrow', 'dce' ) .'</th>';
$output .= '<th>'. __( 'Amount', 'dce' ) .'</th>';
$output .= '<th>'. __( 'Target', 'dce' ) .'</th>';
$output .= '<th>'. __( 'Status', 'dce' ) .'</th>';
$output .= '<th>'. __( 'Date &amp; Time', 'dce' ) .'</th>';
$output .= '</tr></thead><tbody>';

// date & time format
$date_format = get_option( 'date_format' );
$time_format = get_option( 'time_format' );

$len = count( $user_transactions );
for ( $i = 0; $i < $len; $i++ )
{
	// ref
	$trans =& $user_transactions[$i];

	// datetime stamp
	$trans->trans_datetime = strtotime( $trans->trans_datetime );

	$output .= '<tr><td><a href="'. get_permalink( $trans->escrow_id ) .'" target="_blank">'. __( 'View Escrow', 'dce' ) .'</a></td>';
	$output .= '<td>'. $trans->trans_data['amount'] .'</td>';
	$output .= '<td>'. ( 'sent' == $trans->trans_action ? __( 'Sent', 'dce' ) : __( 'Received', 'dce' ) ) .'</td>';
	$output .= '<td>'. ( isset( $trans->trans_data['error'] ) && $trans->trans_data['error'] ? __( 'Failed', 'dce' ) : __( 'Success', 'dce' ) ) .'</td>';
	$output .= '<td>'. sprintf( __( '%s at %s', 'dce' ), date( $date_format, $trans->trans_datetime ), date( $time_format, $trans->trans_datetime ) ) .'</td></tr>';
}

$output .= dce_table_end();

return $output;
















