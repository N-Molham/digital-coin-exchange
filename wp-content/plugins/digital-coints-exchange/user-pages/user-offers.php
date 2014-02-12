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
	case 'view_offers':
		$output .= dce_section_title( __( 'Your Offers', 'dce' ) );

		// get user offers
		$offers = $dce_user->get_offers();

		// convert base url
		$convert_url = add_query_arg( 'view', 'create_escrow', dce_get_pages( 'escrow-manager' )->url );

		// offers table start
		$output .= dce_table_start( 'user-offers' ) .'<thead><tr>';
		$output .= '<th>'. __( 'Original', 'dce' ) .'</th>';
		$output .= '<th>'. __( 'Target', 'dce' ) .'</th>';
		$output .= '<th>'. __( 'Commission Agreement', 'dce' ) .'</th>';
		$output .= '<th>'. __( 'Date &amp; Time', 'dce' ) .'</th>';
		$output .= '<th>'. __( 'Status', 'dce' ) .'</th>';
		$output .= '<th>'. __( 'Actions', 'dce' ) .'</th>';
		$output .= '</tr></thead><tbody>';

		// content
		foreach ( $offers as $offer )
		{
			// data display
			$output .= '<tr><td>'. $offer['from_display'] .'</td>';
			$output .= '<td>'. $offer['to_display'] .'</td>';
			$output .= '<td>'. $offer['comm_method'] .'</td>';
			$output .= '<td>'. $offer['datetime'] .'</td>';
			$output .= '<td>'. $offer['status'] .'</td>';

			// actions
			$output .= '<td align="center" width="280">';

			// cancel offer
			$output .= '<a href="#" class="button small red cancel-offer" data-action="cancel_offer" ';
			$output .= 'data-offer="'. $offer['ID'] .'" data-nonce="'. wp_create_nonce( 'dce_cancel_nonce_'. $offer['ID'] ) .'">';
			$output .= __( 'Cancel', 'offer' ) .'</a>&nbsp;';

			// convert offer
			if ( 'confirmed' == $offer['status'] )
				$output .= '<a href="'. add_query_arg( 'convert', $offer['ID'], $convert_url ) .'" class="button small green">'. __( 'Convert To Escrow', 'dce' ) .'</a>';

			// actions end
			$output .= '</td></tr>';

			// offer details
			if ( !empty( $offer['details'] ) )
				$output .= '<tr><td colspan="6"><strong>'. __( 'Offer Details', 'dce' ).':</strong> '. $offer['details'] .'</td></tr>';
		}

		// table end
		$output .= '</tbody>'. dce_table_end();

		// new offer link
		$output .= '<a href="'. add_query_arg( 'view', 'create_offer' ) .'" class="button small green">'. __( 'Create New Offer', 'dce' ) .'</a>';
		break;

	case 'create_offer':
		$coin_types = dce_get_coin_types();

		// title
		$output .= dce_section_title( __( 'Create new offer', 'dce' ) );

		// form start
		$output .= '<form action="" method="post" id="new-offer-form" class="ajax-form" data-callback="new_offer_callback">';

		// input fields
		$form_fields = DCE_Offer::form_fields( $coin_types );
		foreach ( $form_fields as $field_name => $field_args )
		{
			$output .= dce_form_input( $field_name, $field_args );
		}

		// hidden inputs
		$output .= '<input type="hidden" name="action" value="create_offer" />';
		$output .= wp_nonce_field( 'dce_save_offer', 'nonce', false, false );

		// submit
		$output .= '<p class="form-input"><input type="submit" value="'. __( 'Save', 'dce' ) .'" class="button small green" /></p>';

		// form end
		$output .= '</form>';
		break;
}

return $output;
















