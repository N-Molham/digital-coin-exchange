<?php
/**
 * Transactions Class
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */
class DCE_Transactions
{
	static $table = 'transactions';
 
	/**
	 * Save transaction
	 * 
	 * @param mixed $data
	 * @param string $action
	 * @param int $user_id
	 * @param int $escrow_id
	 * @param string $txid
	 * @param string $datetime
	 * @return Ambigous <boolean, number>
	 */
	public static function save( $data, $action, $user_id, $escrow_id, $txid = '', $datetime = '' )
	{
		global $wpdb;

		// serialize data if needed
		$data = maybe_serialize( $data );

		// row fields
		$insert_fields = array ( 
				'user_id' => $user_id,
				'escrow_id' => $escrow_id,
				'trans_action' => $action,
				'trans_data' => $data,
				'trans_txid' => $txid,
				'trans_datetime' => empty( $datetime ) ? current_time( 'mysql' ) : $datetime,
		);

		$insert = $wpdb->insert( $wpdb->prefix . self::$table, $insert_fields, array( '%d', '%d', '%s', '%s', '%s', '%s' ) );
		return $insert ? $wpdb->insert_id : false;
	}
}
