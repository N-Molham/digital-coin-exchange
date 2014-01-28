<?php
/**
 * Users offers ( public )
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

// enqueues
wp_enqueue_style( 'dce-public-style' );
wp_enqueue_script( 'dce-public-offers', DCE_URL .'js/public-offers.js', array( 'dce-shared-script' ), false, true );

// shortcode output
$output = '';

// get confirmed offers
$offers = DCE_User::query_offers( array( 'post_status' => 'publish' ) );

// offers table start
$output .= dce_table_start( 'open-offers' ) .'<thead><tr>';
$output .= '<th>'. __( 'Owner', 'dce' ) .'</th>';
$output .= '<th>'. __( 'Original', 'dce' ) .'</th>';
$output .= '<th>'. __( 'Target', 'dce' ) .'</th>';
$output .= '<th>'. __( 'Date &amp; Time', 'dce' ) .'</th>';
$output .= '<th>'. __( 'Details', 'dce' ) .'</th>';
$output .= '</tr></thead><tbody>';

// content
foreach ( $offers as $offer )
{
	// if there are details for the offer
	$has_details = !empty( $offer['details'] );

	// data display
	$output .= '<tr><td><a href="'. $offer['user']->profile_url() .'">'. $offer['user']->display_name .'</a></td>';
	$output .= '<td>'. $offer['from_display'] .'</td>';
	$output .= '<td>'. $offer['to_display'] .'</td>';
	$output .= '<td>'. $offer['datetime'] .'</td>';
	$output .= '<td><a href="#offer-details-'. $offer['ID'] .'" class="button small darkgray'. ( $has_details ? '' : ' disabled' ) .'">'. __( 'Details', 'offer' ) .'</a></td></tr>';

	// offer details
	if ( $has_details )
		$output .= '<tr id="offer-details-'. $offer['ID'] .'" class="offer-details"><td colspan="5"><div class="content"><strong>'. __( 'Offer Details', 'dce' ).':</strong> '. $offer['details'] .'</div></td></tr>';
}

// table end
$output .= '</tbody>'. dce_table_end();

return $output;
















