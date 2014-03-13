<?php
/**
 * User's Feddback about escrow result
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

/* @var $dce_user DCE_User */
global $dce_user;

// enqueues
wp_enqueue_script( 'dce-feedback', DCE_URL .'js/feedback.js', array( 'dce-rateit-script' ), false, true );

// shortcode output
$output = '';

// target escrow
$escrow = new DCE_Escrow( (int) get_query_var( 'feedback_escrow' ) );

// check escrow status, only allow completed and failed ones
if ( !$escrow->exists() || !in_array( $escrow->get_status(), array( 'completed', 'failed' ) ) || !$escrow->check_user( $dce_user->user_email ) )
	return dce_alert_message( __( 'Unknown Escrow', 'dce' ), 'error' );

// check if he gave feedback before
if ( 'yes' == $escrow->get_meta( $dce_user->ID .'_gave_feedback' ) )
	return dce_alert_message( __( 'You already gave a feedback about this escrow', 'dce' ), 'error' );

// feedback message
$output .= '<p>'. dce_admin_get_settings( 'escrow_feedback_msg' ) .'</p>';

// form start
$output .= '<form action="" id="user-feedback-form" method="post" class="ajax-form" data-callback="user_feedback_callback">';

// feedback rating
$output .= '<label for="rating-input">'. __( 'Escrow Rate', 'dce' ) .' :</label>&nbsp;';
$output .= '<input type="range" min="0" max="5" value="0" step="1" id="rating-input" name="rating">';
$output .= '<div class="rateit" data-rateit-backingfld="#rating-input"></div>';

// feedback message
$output .= dce_form_input( 'feedback', array (
		'label' => __( 'Feedback', 'dce' ), 
		'input' => 'textarea', 
		'cols' => 42, 
		'rows' => 8,
) );

// hidden inputs
$output .= '<input type="hidden" name="action" value="user_feedback" />';
$output .= '<input type="hidden" name="escrow" value="'. $escrow->ID .'" />';
$output .= wp_nonce_field( 'dce_user_feedback', 'nonce', false, false );

// submit
$output .= '<p class="form-input"><input type="submit" value="'. __( 'Send', 'dce' ) .'" class="button small green" /></p>';

// form end
$output .= '</form>';

return $output;
















