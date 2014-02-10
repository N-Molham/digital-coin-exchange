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

		// offer to convert
		$convert_offer = (int) dce_get_value( 'convert' );
		if ( $convert_offer )
		{
			// get instance
			$convert_offer = new DCE_Offer( $convert_offer );
		}

		// title
		$output .= dce_section_title( __( 'New Escrow', 'dce' ) );

		// form start
		$output .= '<form action="" method="post" id="new-escrow-form" class="ajax-form" data-callback="new_escrow_callback">';

		// input fields
		$form_fields = DCE_Escrow::form_fields( $coin_types );
		foreach ( $form_fields as $field_name => $field_args )
		{
			$output .= dce_form_input( $field_name, $field_args );
		}

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
















