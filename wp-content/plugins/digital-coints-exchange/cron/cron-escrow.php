<?php
/**
 * Cron: Escrows
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

//add_action( 'template_redirect', 'dce_cron_test' );
/**
 * Cron test
 */
function dce_cron_test()
{
	do_action( 'dce_cron_interval' );
}

add_action( 'dce_cron_interval', 'dce_cron_escrows_transactions_check' );
/**
 * Check open escrow transactions
 */
function dce_cron_escrows_transactions_check()
{
	// open up execution time
	set_time_limit( 300 );

	// query open escrows
	$open_escrows = DCE_Escrow::query_escrows( array( 'post_status' => 'publish' ) );

	$len = count( $open_escrows );
	if ( !$len )
		return;

	// system settings
	$settings = dce_admin_get_settings();
	$settings['commission'] = floatval( $settings['commission'] );
	$settings['escrow_expire'] = intval( $settings['escrow_expire'] );

	$coin_types = dce_get_coin_types();

	// escrows loop
	for ( $i = 0; $i < $len; $i++ )
	{
		/* @var $escrow DCE_Escrow */
		$escrow =& $open_escrows[$i];

		// marked as failure ?
		if ( 'yes' == $escrow->is_failure )
			continue;

		// check from coin
		$from_rpc_client = &dce_coins_rpc_connections( $escrow->from_coin );
		$from_amount_received = $from_rpc_client->getreceivedbyaddress( $escrow->owner_address );
		if ( is_wp_error( $from_amount_received ) )
			continue;

		// check to coin
		$to_rpc_client = &dce_coins_rpc_connections( $escrow->to_coin );
		$to_amount_received = $to_rpc_client->getreceivedbyaddress( $escrow->target_address );
		if ( is_wp_error( $to_amount_received ) )
			continue;

		// save results
		$escrow->set_meta( 'from_amount_received', $from_amount_received );
		$escrow->set_meta( 'to_amount_received', $to_amount_received );

		// expires on
		$is_expired = current_time( 'timestamp' ) > strtotime( '+'. intval( $settings['escrow_expire'] ) .'days', strtotime( $escrow->datetime ) );

		// escrow expired
		if ( $is_expired )
		{
			// escrow failed 
			$escrow->set_meta( 'is_failure', 'yes' );

			// no sufficient funds received
			if ( $from_amount_received < $escrow->from_amount || $to_amount_received < $escrow->to_amount )
			{
				$notify = array();

				// notify owner for refund
				if ( ( float ) $from_amount_received )
					$notify[] = $escrow->user->user_email;

				// notify other party to refund
				if ( ( float ) $to_amount_received )
					$notify[] = $escrow->target_email;

				if ( !empty( $notify ) )
					wp_mail( $notify,
							 __( 'Amount Refund notification', 'dce' ), 
							sprintf( __( 'The escrow you participated in expired without fulfilling the necessarily amounts, <a href="%s">Click here</a> to request a refund.', 'dce' ), esc_attr( $escrow->url() ) ) 
					);
			}

			// wp action
			do_action( 'dce_escrow_failed', $escrow );

			// skip to next escrow
			continue;
		}

		// which parties sent amounts right
		$all_received = 0;
		$to_notify = array();
		$notify_mail_msg = __( 'The other party %s of this <a href="%s">escrow</a>, sent the required coins amount %s', 'dce' );

		// owner sent right amount
		if ( $from_amount_received >= $escrow->from_amount )
		{
			// notify target
			if ( 'yes' != $escrow->target_notified )
			{
				// add to notification list
				wp_mail( $escrow->target_email, 
						__( 'Escrow Notification', 'dce' ), 
						sprintf( $notify_mail_msg, $escrow->user->display_name(), $escrow->url(), $escrow->convert_from_display( $coin_types ) ) );

				// mark as notified
				$escrow->set_meta( 'target_notified', 'yes' );
			}

			$all_received++;
		}

		// target sent right amount
		if ( $to_amount_received >= $escrow->to_amount )
		{
			// notify target
			if ( 'yes' != $escrow->owner_notified )
			{
				// add to notification list
				wp_mail( $escrow->user->data->user_email, 
						__( 'Escrow Notification', 'dce' ), 
						sprintf( $notify_mail_msg, $escrow->target_user->display_name(), $escrow->url(), $escrow->convert_to_display( $coin_types ) ) );

				// mark as notified
				$escrow->set_meta( 'owner_notified', 'yes' );
			}

			$all_received++;
		}

		// all amounts received
		if ( 2 == $all_received )
		{
			// check for receive addresses
			if ( empty( $escrow->owner_receive_address ) || empty( $escrow->target_receive_address ) )
				continue;

			// final amounts
			$amount_for_owner = $escrow->to_amount;
			$amount_for_target = $escrow->from_amount;

			// commission divider
			switch ( $escrow->comm_method )
			{
				// all on the owner
				case 'by_user':
					$amount_for_owner *= ( 100 - $settings['commission'] ) / 100;
					break;

				// all on the other paty
				case 'by_target':
					$amount_for_target *= ( 100 - $settings['commission'] ) / 100;
					break;

				// divided on both party equally
				case '50_50':
					// calculate 50% of the commission
					$half_commission = ( 100 - ( $settings['commission'] * 0.5 ) ) / 100;

					// cut from both amounts
					$amount_for_owner *= $half_commission;
					$amount_for_target *= $half_commission;
					break;
				default:
					return;
			}

			// send amounts to parties
			$owner_txid = $to_rpc_client->sendtoaddress( $escrow->owner_receive_address, $amount_for_owner );
			$target_txid = $from_rpc_client->sendtoaddress( $escrow->target_receive_address, $amount_for_target );

			// save results
			$escrow->set_meta( 'owner_txid', $owner_txid );
			$escrow->set_meta( 'target_txid', $target_txid );

			// set as completed
			$escrow->change_status( 'completed' );

			// wp action
			do_action( 'dce_escrow_success', $escrow );
		}
	}
}
























