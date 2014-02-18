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
wp_enqueue_script( 'dce-escrows' );

// shortcode output
$output = '';

// current view
$current_view = dce_get_value( 'view' );
if ( !in_array( $current_view, array( 'view_escrows', 'create_escrow' ) ) )
	$current_view = 'view_escrows';

$coin_types = dce_get_coin_types();

// views switch
switch ( $current_view )
{
	case 'view_escrows':
		$output .= dce_section_title( __( 'Your Escrows', 'dce' ) );
	
		// get user escrows
		$user_escrows = $dce_user->get_escrows();

		// escrows table start
		$output .= dce_table_start( 'user-escrows' ) .'<thead><tr>';
		$output .= '<th>'. __( 'Creator', 'dce' ) .'</th>';
		$output .= '<th>'. __( 'Original', 'dce' ) .'</th>';
		$output .= '<th>'. __( 'Target', 'dce' ) .'</th>';
		$output .= '<th>'. __( 'Commission Agreement', 'dce' ) .'</th>';
		$output .= '<th>'. __( 'Date &amp; Time', 'dce' ) .'</th>';
		$output .= '<th>'. __( 'Status', 'dce' ) .'</th>';
		$output .= '<th>'. __( 'Actions', 'dce' ) .'</th>';
		$output .= '</tr></thead><tbody>';

		// content
		/* @var $escrow DCE_Escrow */
		foreach ( $user_escrows as $escrow )
		{
			// data display
			$output .= '<tr><td>'. $escrow->user->display_name() .'</td>';
			$output .= '<td>'. $escrow->convert_from_display( $coin_types ) .'</td>';
			$output .= '<td>'. $escrow->convert_to_display( $coin_types ) .'</td>';
			$output .= '<td>'. $escrow->commission_method_display() .'</td>';
			$output .= '<td>'. $escrow->datetime .'</td>';
			$output .= '<td>'. $escrow->get_status() .'</td>';
			$output .= '<td><a href="'. $escrow->url() .'" class="button small green" target="_blank">'. __( 'View', 'dce' ) .'</a></td>';

			// escrow details
			if ( !empty( $escrow->details ) )
				$output .= '<tr><td colspan="7"><strong>'. __( 'Escrow Details', 'dce' ).':</strong> '. $escrow->details .'</td></tr>';
		}

		// table end
		$output .= '</tbody>'. dce_table_end();

		// new offer link
		$output .= '<a href="'. add_query_arg( 'view', 'create_escrow' ) .'" class="button small green">'. __( 'Start New Escrow', 'dce' ) .'</a>';
		break;

	default:
		// offer to convert
		$convert_offer = (int) dce_get_value( 'convert' );
		if ( $convert_offer )
		{
			// get instance
			$convert_offer = new DCE_Offer( $convert_offer );
			if ( !$convert_offer->exists() )
				$convert_offer = null;
		}

		// title
		$output .= dce_section_title( __( 'New Escrow', 'dce' ) );

		// form start
		$output .= '<form action="" method="post" id="new-escrow-form" class="ajax-form" data-callback="new_escrow_callback" data-focus="'. ( $convert_offer ? 'target_email' : '' ) .'">';

		// input fields
		$form_fields = DCE_Escrow::form_fields( $coin_types );
		foreach ( $form_fields as $field_name => $field_args )
		{
			// fill in convert values
			$field_args['value'] = $convert_offer ? $convert_offer->$field_name : '';

			// input
			$output .= dce_form_input( $field_name, $field_args );
		}

		// hidden inputs
		$output .= '<input type="hidden" name="action" value="create_escrow" />';
		$output .= '<input type="hidden" name="convert_base" value="'. ( $convert_offer ? $convert_offer->ID : 'none' ) .'" />';
		$output .= wp_nonce_field( 'dce_save_escrow', 'nonce', false, false );

		// submit
		$output .= '<p class="form-input"><input type="submit" value="'. __( 'Start', 'dce' ) .'" class="button small green" /></p>';

		// form end
		$output .= '</form>';
		break;
}

return $output;
















