<?php
/**
 * Single: Escrow
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */
/* @var $dce_user DCE_User */
global $dce_user;

// enqueues
wp_enqueue_script( 'dce-escrows' );

// current escrow
$escrow = new DCE_Escrow( get_post() );
if ( !$escrow->exists() )
	return dce_alert_message( __( 'Unknown escrow', 'dce' ), 'error' );

// is the current logged in user is the owner/creator
$is_owner = $escrow->is_user_owner( $dce_user->data->user_email );

$is_admin = dce_is_user_admin( $dce_user );

// output holder
$output = '';

$plugin_settings = dce_admin_get_settings();
$coin_types = dce_get_coin_types();
$form_display = $escrow->convert_from_display( $coin_types );
$to_display = $escrow->convert_to_display( $coin_types );
$exchange_addresses = '';

// exchange addresses
if ( !$is_admin )
{
	$status = $escrow->get_status();

	// message depending on escrow status
	switch ( $status )
	{
		case 'pending':
		case 'started':
			if ( $is_owner )
			{
				// owner/creator user
				$exchange_addresses .= '<p>'. sprintf( $plugin_settings['escrow_start_top_msg'], $form_display, $escrow->owner_address ) .'</p>';
				$exchange_addresses .= '<p>'. sprintf( $plugin_settings['escrow_waiting_party_msg'], $to_display ) .'</p>';
			}
			else
			{
				// target user
				$exchange_addresses .= '<p>'. sprintf( $plugin_settings['escrow_start_top_msg'], $to_display, $escrow->target_address ) .'</p>';
				$exchange_addresses .= '<p>'. sprintf( $plugin_settings['escrow_waiting_party_msg'], $form_display ) .'</p>';
			}
			break;
		case 'in_progress':
			// received coins
			$owner_received_coins = (float) $escrow->from_amount_received;
			$target_received_coins = (float) $escrow->to_amount_received;

			if ( $is_owner )
			{
				// owner part
				if ( $owner_received_coins )
				{
					// owner sent the coins
					$exchange_addresses .= '<p>'. $plugin_settings['escrow_progress_top_msg'] .'</p>';
				}
				else 
				{
					// owner didn't send the coins yet
					$exchange_addresses .= '<p>'. sprintf( $plugin_settings['escrow_start_top_msg'], $form_display, $escrow->owner_address ) .'</p>';
				}

				// target part
				if ( $target_received_coins )
				{
					$receive_datetime = $escrow->get_transactions( array ( 
							'fields' => 'trans_datetime',
							'user' => $escrow->target_user->ID,
							'limit' => 1,
					) );

					// target sent the coins
					$exchange_addresses .= '<p>'. sprintf( $plugin_settings['escrow_party_sent_msg'], $to_display, DCE_Utiles::wp_datetime_format( @$receive_datetime[0]->trans_datetime ) ) .'</p>';
				}
				else
				{
					// target didn't send the coins yet
					$exchange_addresses .= '<p>'. sprintf( $plugin_settings['escrow_waiting_party_msg'], $to_display ) .'</p>';
				}
			}
			else
			{
				// target part
				if ( $target_received_coins )
				{
					// target sent the coins
					$exchange_addresses .= '<p>'. $plugin_settings['escrow_progress_top_msg'] .'</p>';
				}
				else 
				{
					// target didn't send the coins yet
					$exchange_addresses .= '<p>'. sprintf( $plugin_settings['escrow_start_top_msg'], $to_display, $escrow->target_address ) .'</p>';
				}

				// owner part
				if ( $owner_received_coins )
				{
					$receive_datetime = $escrow->get_transactions( array ( 
							'fields' => 'trans_datetime',
							'user' => $escrow->user->ID,
							'limit' => 1,
					) );

					// target sent the coins
					$exchange_addresses .= '<p>'. sprintf( $plugin_settings['escrow_party_sent_msg'], $form_display, DCE_Utiles::wp_datetime_format( @$receive_datetime[0]->trans_datetime ) ) .'</p>';
				}
				else
				{
					// target didn't send the coins yet
					$exchange_addresses .= '<p>'. sprintf( $plugin_settings['escrow_waiting_party_msg'], $form_display ) .'</p>';
				}
			}
			break;
		case 'completed':
			$exchange_addresses .= '<p>'. __( 'Escrow Completed Successfully', 'dce' ) .'</p>';
			break;
		case 'failed':
			$exchange_addresses .= '<p>'. __( 'Escrow Failed', 'dce' ) .'</p>';
			break;
	}

	// coins received message
	if ( !in_array( $status, array( 'completed', 'failed' ) ) )
	{
		// receive addresses
		$exchange_addresses .= dce_divider( 'double' );

		// form nonce
		$coins_address_nonce = wp_nonce_field( 'dce_coins_address', 'nonce', false, false );

		// receive address
		$receive_address = $is_owner ? $escrow->owner_receive_address : $escrow->target_receive_address;
		if ( '' == $receive_address || empty( $receive_address ) )
		{
			// set form
			$exchange_addresses .= '<p>'. $plugin_settings['escrow_receive_msg'] .'</p>';
			$exchange_addresses .= '<div class="receive-address"><form action="" method="post" class="ajax-form" data-callback="coins_address_callback">';
			$exchange_addresses .= '<input type="text" class="input-text input-code" name="coin_address" value="'. $receive_address .'" />';
			$exchange_addresses .= '<input type="submit" value="'. __( 'Save', 'dce' ) .'" class="button small green" />';
			$exchange_addresses .= '<input type="hidden" name="action" value="save_coins_address" />';
			$exchange_addresses .= '<input type="hidden" name="type" value="receive" />';
			$exchange_addresses .= '<input type="hidden" name="escrow" value="'. $escrow->ID .'" />';
			$exchange_addresses .= $coins_address_nonce .'</form></div>'; 
		}
		else
		{
			// display address
			$exchange_addresses .= '<p>'. __( 'Your Receive Address', 'dce' ) .': <code>'. $receive_address .'</code></p>';
		}

		// refund address
		$refund_address = $is_owner ? $escrow->owner_refund_address : $escrow->target_refund_address;
		if ( '' == $refund_address || empty( $refund_address ) )
		{
			// set form
			$exchange_addresses .= '<p>'. $plugin_settings['escrow_refund_msg'] .'</p>';
			$exchange_addresses .= '<div class="receive-address"><form action="" method="post" class="ajax-form" data-callback="coins_address_callback">';
			$exchange_addresses .= '<input type="text" class="input-text input-code" name="coin_address" value="'. $refund_address .'" />';
			$exchange_addresses .= '<input type="submit" value="'. __( 'Save', 'dce' ) .'" class="button small green" />';
			$exchange_addresses .= '<input type="hidden" name="action" value="save_coins_address" />';
			$exchange_addresses .= '<input type="hidden" name="type" value="refund" />';
			$exchange_addresses .= '<input type="hidden" name="escrow" value="'. $escrow->ID .'" />';
			$exchange_addresses .= $coins_address_nonce .'</form></div>'; 
		}
		else
		{
			// display address
			$exchange_addresses .= '<p>'. __( 'Your Re-fund Address', 'dce' ) .': <code>'. $refund_address .'</code></p>';
		}
	}

	// display box
	$output .= dce_promotion_box( $exchange_addresses );
}

// data table start
$output .= dce_table_start( 'single-escrow' );

// form fields for data display
$fields = DCE_Escrow::form_fields( $coin_types );

// Status
$output .= '<tr><th>'. __( 'Status', 'dce' ) .'</th><td>'. $escrow->get_status( true ) .'</td></tr>';

// Creator
$output .= '<tr><th>'. __( 'Creator', 'dce' ) .'</th><td><a href="'. $escrow->owner_user()->profile_url() .'">'. $escrow->user->display_name() .'</a></td></tr>';

// other party
$output .= '<tr><th>'. __( 'Other Party', 'dce' ) .'</th><td><a href="'. $escrow->target_user->profile_url() .'">'. $escrow->target_user->display_name() .'</a></td></tr>';

// convert from
$output .= '<tr><th>'. __( 'Convert From', 'dce' ) .'</th><td>'. $escrow->convert_from_display( $coin_types ) .'</td></tr>';

// convert to
$output .= '<tr><th>'. __( 'Convert To', 'dce' ) .'</th><td>'. $escrow->convert_to_display( $coin_types ) .'</td></tr>';

// Commission payment
$output .= '<tr><th>'. $fields['comm_method']['label'] .'</th><td>'. $escrow->commission_method_display() .'</td></tr>';

// details
$output .= '<tr><th>'. $fields['details']['label'] .'</th><td>'. $escrow->details .'</td></tr>';

// show addresses for admin
if ( $is_admin )
{
	// owner/creator addresses
	$output .= '<tr><th>'. __( 'Creator Send Address', 'dce' ) .'</th><td><code>'. $escrow->owner_address .'</code></td></tr>';
	$output .= '<tr><th>'. __( 'Creator Receive Address', 'dce' ) .'</th><td><code>'. $escrow->owner_receive_address .'</code></td></tr>';

	// target addresses
	$output .= '<tr><th>'. __( 'Target User Send Address', 'dce' ) .'</th><td><code>'. $escrow->target_address .'</code></td></tr>';
	$output .= '<tr><th>'. __( 'Target User Receive Address', 'dce' ) .'</th><td><code>'. $escrow->target_receive_address .'</code></td></tr>';
}

// table end
$output .= dce_table_end();

return $output;














