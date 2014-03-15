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
	 * Query transactions
	 * 
	 * @param string $args
	 * @return array
	 */
	public static function query_transactions( $args = '' )
	{
		global $wpdb;

		// defaults
		$args = wp_parse_args( $args, array (
				'fields' => '*',
				'user' => null, 
				'escrow' => null, 
				'limit' => 0,
				'page' => 0,
				'output' => OBJECT,
		) );

		// query SQL
		$query_sql = 'SELECT ';

		// query selected fields
		$query_sql .= is_array( $args['fields'] ) ? implode( ', ', array_map( 'sanitize_key', $args['fields'] ) ) : $args['fields'];

		// query conditions
		$query_sql .= ' FROM '. $wpdb->prefix . self::$table .' WHERE 1=1';

		// query values
		$query_values = array();

		// specific user
		if ( $args['user'] )
		{
			$query_sql .= ' AND user_id = %d';
			$query_values[] = $args['user'];
		}

		// specific escrow
		if ( $args['escrow'] )
		{
			$query_sql .= ' AND escrow_id = %d';
			$query_values[] = $args['escrow'];
		}

		// limit
		if( $args['limit'] )
		{
			$query_sql .= ' LIMIT ';

			// paging
			if( $args['page'] )
				$query_sql .= ( ( absint( $args['offset'] ) - 1 ) * $args['limit'] ) . ', ';

			$query_sql .= $args['limit'];
		}

		// query results
		$transactions = $wpdb->get_results( $wpdb->prepare( $query_sql, $query_values ), $args['output'] );
		$len = count( $transactions );

		if ( $len )
		{
			for ( $i = 0; $i < $len; $i++ )
			{
				// unserialize data if needed
				$transactions[$i]->trans_data = maybe_unserialize( $transactions[$i]->trans_data );
			}
		}

		// return filtered data
		return apply_filters( 'dce', $transactions, $args );
	}

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
