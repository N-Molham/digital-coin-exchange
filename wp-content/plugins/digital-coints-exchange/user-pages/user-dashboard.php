<?php
/**
 * User's Dashboard
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

/* @var $dce_user DCE_User */
global $dce_user;

// shortcode output
$output = '';

$coin_types = dce_get_coin_types();

// balance
$output .= '<div class="one_half">'. dce_section_title( __( 'Balance', 'dce' ) ) .'</div>';

// escrows
$output .= '<div class="one_half last">'. dce_section_title( __( 'Latest Esrows', 'dce' ) );

$latest_escrows = $dce_user->get_escrows( array ( 
		'nopaging' => false,
		'numberposts' => 5,
) );

// escrows table
$output .= dce_table_start( 'dashboard-escrows' ) .'<thead><tr>';
$output .= '<th>'. __( 'With', 'dce' ) .'</th>';
$output .= '<th>'. __( 'Original', 'dce' ) .'</th>';
$output .= '<th>'. __( 'Target', 'dce' ) .'</th>';
$output .= '<th>'. __( 'View', 'dce' ) .'</th>';
$output .= '</tr></thead><tbody>';

/* @var $escrow DCE_Escrow */
foreach ( $latest_escrows as $escrow )
{
	$other_party = $escrow->other_party( $dce_user );

	// data display
	$output .= '<tr><td><a href="'. $other_party->profile_url() .'">'. $other_party->display_name() .'</a></td>';
	$output .= '<td>'. $escrow->convert_from_display( $coin_types ) .'</td>';
	$output .= '<td>'. $escrow->convert_to_display( $coin_types ) .'</td>';
	$output .= '<td><a href="'. $escrow->url() .'" class="button small green" target="_blank">'. __( 'View', 'dce' ) .'</a></td></tr>';
}

// table end
$output .= '</tbody>'. dce_table_end();

// all escrows
$output .= '<a href="'. dce_get_pages( 'escrow-manager' )->url .'" class="button small green">'. __( 'View All', 'dce' ) .'</a>';

// escrows end
$output .= '</div>';

// separator
$output .= '<div class="clearboth"></div>';

// messages
$output .= '<div class="one_half">'. dce_section_title( __( 'Messages', 'dce' ) );

$user_messages = $dce_user->get_messages( array( 'target' => 'received', 'number' => 5 ) );

// messages table
$output .= dce_table_start( 'dashboard-messages' ) .'<thead><tr>';
$output .= '<th>'. __( 'From', 'dce' ) .'</th>';
$output .= '<th>'. __( 'About', 'dce' ) .'</th>';
$output .= '<th>'. __( 'View', 'dce' ) .'</th>';
$output .= '</tr></thead><tbody>';

foreach ( $user_messages as $message )
{
	// user display
	$output .= '<tr><td><a href="'. $message['from']->profile_url() .'">'. $message['from']->display_name() .'</a></td>';

	// object link
	$output .= '<td>';
	if ( 'offer' == $message['type'] )
		$output .= '<a href="'. get_permalink( $message['object_id'] ) .'">'. __( 'Offer', 'dce' ) .'</a>';
	else
		$output .= '<a href="'. get_permalink( $message['object_id'] ) .'">'. __( 'Escrow', 'dce' ) .'</a>';
	$output .= '</td>';

	// body
	$output .= '<td><a href="#message-'. $message['ID'] .'" rel="prettyPhoto" class="button small darkgray">'. __( 'Read', 'dce' ) .'</a>';
	$output .= '<div id="message-'. $message['ID'] .'" class="message-body">'. $message['message'] .'</div></td></tr>';
}

// table end
$output .= '</tbody>'. dce_table_end();

// all escrows
$output .= '<a href="'. dce_get_pages( 'messages' )->url .'" class="button small green">'. __( 'View All', 'dce' ) .'</a>';

// messages end
$output .= '</div>';

// offers
$output .= '<div class="one_half last">'. dce_section_title( __( 'Latest Offers', 'dce' ) );

$latest_offers = DCE_Offer::query_offers( array (
		'nopaging' => false,
		'numberposts' => 5,
		'list_output' => 'class',
) );

// offers table
$output .= dce_table_start( 'dashboard-offers' ) .'<thead><tr>';
$output .= '<th>'. __( 'Creator', 'dce' ) .'</th>';
$output .= '<th>'. __( 'Original', 'dce' ) .'</th>';
$output .= '<th>'. __( 'Target', 'dce' ) .'</th>';
$output .= '<th>'. __( 'View', 'dce' ) .'</th>';
$output .= '</tr></thead><tbody>';

/* @var $offer DCE_Offer */
foreach ( $latest_offers as $offer )
{
	$owner = $offer->owner_user();

	// data display
	$output .= '<tr><td><a href="'. $owner->profile_url() .'">'. $owner->display_name() .'</a></td>';
	$output .= '<td>'. $offer->convert_from_display( $coin_types ) .'</td>';
	$output .= '<td>'. $offer->convert_to_display( $coin_types ) .'</td>';
	$output .= '<td><a href="'. $offer->url() .'" class="button small green" target="_blank">'. __( 'View', 'dce' ) .'</a></td></tr>';
}

// table end
$output .= '</tbody>'. dce_table_end();

// all escrows
$output .= '<a href="'. dce_get_pages( 'offers' )->url .'" class="button small green">'. __( 'View All', 'dce' ) .'</a>';

// offers end
$output .= '</div>';

return $output;
















