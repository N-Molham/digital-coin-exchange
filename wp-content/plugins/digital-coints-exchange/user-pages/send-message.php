<?php
/**
 * Send message form
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

/* @var $dce_user DCE_User */
global $dce_user, $attrs;

// enqueues
wp_enqueue_script( 'dce-messages', DCE_URL .'js/messages.js', array( 'dce-shared-script' ), false, true );

// shortcode output
$output = '';

// title
$output .= dce_section_title( __( 'Send Message', 'dce' ) );

// form start
$output .= '<form action="" id="send-message-form" method="post" class="ajax-form" data-callback="new_message_sent">';

// send to
$output .= '<label class="inline">'. __( 'Send to :', 'dce' ) .'</label>&nbsp;';
$output .= '<p class="form-input inline"><strong class="send-to"></strong></p>';

// message 
$output .= dce_form_input( 'message', array (
		'label' => __( 'Message', 'dce' ), 
		'input' => 'textarea', 
		'cols' => 42, 
		'rows' => 8,
) );

// hidden inputs
$output .= '<input type="hidden" name="action" value="send_message" />';
$output .= '<input type="hidden" name="user" value="" />';
$output .= '<input type="hidden" name="target" value="" />';
$output .= '<input type="hidden" name="type" value="" />';
$output .= wp_nonce_field( 'dce_send_message', 'nonce', false, false );

// submit
$output .= '<p class="form-input"><input type="submit" value="'. __( 'Send', 'dce' ) .'" class="button small green" /></p>';

// form end
$output .= '</form>';

return $output;
















