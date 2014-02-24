<?php
/**
 * User's Messeges
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

/* @var $dce_user DCE_User */
global $dce_user;

$target = dce_get_value( 'mails' );
if ( !in_array( $target, array( 'received', 'sent' ) ) )
	$target = 'received';

$sent = 'sent' == $target;

// shortcode output
$output = '';

$user_messages = $dce_user->get_messages( array( 'target' => $target ) );

// title
$section_title = __( 'Inbox', 'dce' ) .'&nbsp;<small>( ';
$section_title .= !$sent ? '<strong>'. __( 'Received', 'dce' ) .'</strong>' : '<a href="'. add_query_arg( 'mails', 'received' ) .'">'. __( 'Received', 'dce' ) .'</a>';
$section_title .= ' , ';
$section_title .= $sent ? '<strong>'. __( 'Sent', 'dce' ) .'</strong>' : '<a href="'. add_query_arg( 'mails', 'sent' ) .'">'. __( 'Sent', 'dce' ) .'</a>';
$section_title .= ' )</small>';
$output .= dce_section_title( $section_title );

// messages start
$output .= dce_table_start( 'messages' );
$output .= '<thead><tr>';
$output .= '<th>'. ( $sent ? __( 'To', 'dce' ) : __( 'From', 'dce' ) ) .'</th>';
$output .= '<th>'. __( 'About', 'dce' ) .'</th>';
$output .= '<th width="120">'. __( 'Date & Time', 'dce' ) .'</th>';
$output .= '<th width="120">'. __( 'Actions', 'dce' ) .'</th>';
$output .= '</tr></thead><tbody>';

$offers_url = dce_get_pages( 'offers' )->url;

// messages loop
foreach ( $user_messages as $message )
{
	// user display
	$output .= '<tr><td>';
	if ( $sent )
		$output .= '<a href="'. $message['to']->profile_url() .'">'. $message['to']->display_name() .'</a>';
	else
		$output .= '<a href="'. $message['from']->profile_url() .'">'. $message['from']->display_name() .'</a>';
	$output .= '</td>';

	// object link
	$output .= '<td>';
	if ( 'offer' == $message['type'] )
		$output .= '<a href="'. get_permalink( $message['object_id'] ) .'">'. __( 'Offer', 'dce' ) .'</a>';
	else
		$output .= '<a href="'. get_permalink( $message['object_id'] ) .'">'. __( 'Escrow', 'dce' ) .'</a>';
		
	$output .= '</td>';

	// date & time
	$output .= '<td>'. $message['date_time'] .'</td>';

	// body
	$output .= '<td><a href="#message-'. $message['ID'] .'" rel="prettyPhoto" class="button small darkgray">'. __( 'Read', 'dce' ) .'</a>';
	$output .= '<div id="message-'. $message['ID'] .'" class="message-body">'. $message['message'] .'</div></td></tr>';
}

// messages end
$output .= '</tbody>'. dce_table_end();

return $output;
















