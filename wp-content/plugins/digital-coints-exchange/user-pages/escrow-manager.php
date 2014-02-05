<?php
/**
 * User's Escrows
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

/* @var $dce_user DCE_User */
global $dce_user;

// js & css
wp_enqueue_script( 'dce-escrows', DCE_URL .'js/escrows.js', array( 'dce-shared-script' ), false, true );

// shortcode output
$output = '';

// current view
$current_view = dce_get_value( 'view' );
if ( !in_array( $current_view, array( 'view_escrows', 'create_escrow' ) ) )
	$current_view = 'view_escrows';

// views switch
switch ( $current_view )
{
	default:
		$coin_types = dce_get_coin_types();

		// title
		$output .= dce_section_title( __( 'New Escrow', 'dce' ) );

		// form start
		$output .= '<form action="" method="post" id="new-escrow-form" class="ajax-form" data-callback="new_escrow_callback">';

		// exchange from amount
		$output .= dce_form_input( 'from_amount', array( 'label' => __( 'From Amount', 'dce' ), 'input' => 'text' ) );

		// exchange from coin type
		$output .= dce_form_input( 'from_coin', array( 'label' => __( 'From Coin', 'dce' ), 'input' => 'select', 'source' => $coin_types ) );

		// exchange to amount
		$output .= dce_form_input( 'to_amount', array( 'label' => __( 'To Amount', 'dce' ), 'input' => 'text' ) );

		// exchange to coin type
		$output .= dce_form_input( 'to_coin', array( 'label' => __( 'To Coin', 'dce' ), 'input' => 'select', 'source' => $coin_types ) );

		// commission payment method
		$output .= dce_form_input( 'comm_method', array ( 
				'label' => __( 'Commission Agreement', 'dce' ), 
				'input' => 'select', 
				'default_value' => 'none', 
				'source' => array ( 
						'by_user' => __( 'I Will pay 100% of the commission', 'dce' ), 
						'by_target' => __( 'The other party will pay 100% of the commission', 'dce' ), 
						'50_50' => __( 'Both parties will split commission fees by 50% 50%', 'dce' ),
				),
			) 
		);

		// exchange deal details
		$output .= dce_form_input( 'Escrow Terms', array( 'label' => __( 'Escrow Terms & Agreements', 'dce' ), 'input' => 'textarea', 'cols' => 42, 'rows' => 8 ) );

		// target user email
		$output .= dce_form_input( 'target_email', array( 'label' => __( 'Target User Email', 'dce' ), 'input' => 'text' ) );

		// hidden inputs
		$output .= '<input type="hidden" name="action" value="create_escrow" />';
		$output .= wp_nonce_field( 'dce_save_escrow', 'nonce', false, false );

		// submit
		$output .= '<p class="form-input"><input type="submit" value="'. __( 'Start', 'dce' ) .'" class="button small green" /></p>';

		// form end
		$output .= '</form>';
		break;
}

return $output;
















