<?php
/**
 * Cron: Escrows
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

add_action( 'dce_cron_hourly', 'dce_cron_escrows_transactions_check' );
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

	// escrow expire days
	$expire_days = dce_admin_get_settings( 'escrow_expire' );

	// escrows loop
	for ( $i = 0; $i < $len; $i++ )
	{
		/* @var $escrow DCE_Escrow */
		$escrow =& $open_escrows[$i];

		// marked as failure ?
		if ( 'yes' == $escrow->is_failure )
			continue;

		// check from coin
		$from_rpc_client = dce_coins_rpc_connections( $escrow->from_coin );
		$from_amount_received = $from_rpc_client->getreceivedbyaddress( $escrow->owner_address );
		if ( is_wp_error( $from_amount_received ) )
			continue;

		// check to coin
		$to_rpc_client = dce_coins_rpc_connections( $escrow->to_coin );
		$to_amount_received = $to_rpc_client->getreceivedbyaddress( $escrow->target_address );
		if ( is_wp_error( $to_amount_received ) )
			continue;

		// save results
		$escrow->set_meta( 'from_amount_received', $from_amount_received );
		$escrow->set_meta( 'to_amount_received', $to_amount_received );

		// expires on
		$is_expired = current_time( 'timestamp' ) > strtotime( '+'. $expire_days .'days', strtotime( $escrow->datetime ) );

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

			// skip to next escrow
			continue;
		}

		// check received amounts
		if ( $from_amount_received >= $escrow->from_amount || $to_amount_received >= $escrow->to_amount )
		{
			// all amounts received
			dump_data( 'convert and send' );
		}
	}
}
























