<?php
/**
 * User's offers
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

/* @var $dce_user DCE_User */
global $dce_user;

// js & css
wp_enqueue_script( 'dce-offers', DCE_URL .'js/offers.js', array( 'dce-shared-script' ), false, true );

// shortcode output
$output = '';

// current view
$current_view = dce_get_value( 'view' );
if ( !in_array( $current_view, array( 'view_offers', 'create_offer' ) ) )
	$current_view = 'view_offers';

// views switch
switch ( $current_view )
{


	default:
		$coin_types = dce_get_coin_types();

		// title
		$output .= dce_section_title( __( 'New Escrow', 'dce' ) );

		// form start
		$output .= '<form action="" method="post" id="new-offer-form" class="ajax-form">';

		// exchange from amount
		$output .= dce_form_input( 'from_amount', array( 'label' => __( 'From Amount', 'dce' ), 'input' => 'text' ) );

		// exchange from coin type
		$output .= dce_form_input( 'from_coin', array( 'label' => __( 'From Coin', 'dce' ), 'input' => 'select', 'source' => $coin_types ) );

		// exchange to amount
		$output .= dce_form_input( 'to_amount', array( 'label' => __( 'To Amount', 'dce' ), 'input' => 'text' ) );

		// exchange to coin type
		$output .= dce_form_input( 'to_coin', array( 'label' => __( 'To Coin', 'dce' ), 'input' => 'select', 'source' => $coin_types ) );

		// exchange to coin type
		$output .= dce_form_input( 'to_coin', array( 'label' => __( 'Commission Agreement', 'dce' ), 'input' => 'select', 'source' => array("0"=>"I Will pay 100% of the commission" , "1"=>"The other party will pay 100% of the commission" , "2"=>"Both parties will split commission fees by 50% 50%") ) );
		// exchange deal details
		$output .= dce_form_input( 'Escrow Terms', array( 'label' => __( 'Escrow Terms & Agreements', 'dce' ), 'input' => 'textarea', 'cols' => 42, 'rows' => 8 ) );

		$output .= dce_form_input( 'user_email', array( 'label' => __( 'User Email', 'dce' ), 'input' => 'text' ) );

		// hidden inputs
		$output .= '<input type="hidden" name="action" value="create_offer" />';
		$output .= wp_nonce_field( 'dce_save_offer', 'nonce', false, false );

		// submit
		$output .= '<p class="form-input"><input type="submit" value="'. __( 'Start', 'dce' ) .'" class="button small green" /></p>';

		// form end
		$output .= '</form>';
		break;
}

return $output;
















