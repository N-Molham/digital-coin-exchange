<?php
/**
 * Offer Class
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

/**
 * Offer Class
 */
class DCE_Offer extends DCE_Component
{
	/**
	 * Offer's owner
	 * 
	 * @var DCE_User
	 */
	var $user;

	/**
	 * Original currency amount
	 * 
	 * @var int
	 */
	var $from_amount;

	/**
	 * Original currency type
	 * 
	 * @var string
	 */
	var $from_coin; 

	/**
	 * Target currency amount
	 * 
	 * @var int
	 */
	var $to_amount;

	/**
	 * Target currency type
	 * 
	 * @var string
	 */
	var $to_coin;

	/**
	 * Offer deal details
	 * 
	 * @var string
	 */
	var $details;

	/**
	 * Constructor ( override )
	 *
	 * @param number|WP_Post|object $post_id
	 */
	public function __construct( $post_id )
	{
		parent::__construct( $post_id );

		// initialize properties
		$this->user = new DCE_User( $this->post_author );
		$this->from_amount = $this->post_object->from_amount;
		$this->from_coin = $this->post_object->from_coin;
		$this->to_amount = $this->post_object->to_amount;
		$this->to_coin = $this->post_object->to_coin;
		$this->details = $this->post_object->details;
	}

	/**
	 * Convert from display
	 * 
	 * @param array $coin_types
	 * @return string
	 */
	public function convert_from_display( &$coin_types = '' )
	{
		if ( empty( $coin_types ) )
			$coin_types = dce_get_coin_types();

		return _n( sprintf( $coin_types[$this->from_coin]['single'], $this->from_amount ), sprintf( $coin_types[$this->from_coin]['plural'], $this->from_amount ), $this->from_amount );
	}

	/**
	 * Convert to display
	 * 
	 * @param array $coin_types
	 * @return string
	 */
	public function convert_to_display( &$coin_types = '' )
	{
		if ( empty( $coin_types ) )
			$coin_types = dce_get_coin_types();

		return _n( sprintf( $coin_types[$this->to_coin]['single'], $this->to_amount ), sprintf( $coin_types[$this->to_coin]['plural'], $this->to_amount ), $this->to_amount );
	}

	/**
	 * Insert/Update user offer
	 *
	 * @param int $user_id
	 * @param int $from_amount
	 * @param string $from_coin
	 * @param int $to_amount
	 * @param string $to_coin
	 * @param array $offer_args
	 * 
	 * @return int|WP_Error
	 */
	static public function save_offer( $user_id, $from_amount, $from_coin, $to_amount, $to_coin, $offer_args = '' )
	{
		$offer_args = wp_parse_args( $offer_args, array (
				'details' => '',
				'id' => '',
		) );

		// post args
		$post_args = array (
				'ID' => is_numeric( $offer_args['id'] ) ? $offer_args['id'] : '',
				'post_status' => 'pending',
				'post_type' => DCE_POST_TYPE_OFFER,
				'post_author' => $user_id,
				'post_content' => $offer_args['details'],
		);

		// save post
		$offer_id = wp_insert_post( $post_args, true );
		if ( is_wp_error( $offer_id ) )
			return $offer_id;

		// save offer data/meta
		update_post_meta( $offer_id, 'to_amount', $to_amount );
		update_post_meta( $offer_id, 'to_coin', $to_coin );
		update_post_meta( $offer_id, 'from_amount', $from_amount );
		update_post_meta( $offer_id, 'from_coin', $from_coin );

		return apply_filters( 'dce_save_user_offer', $offer_id );
	}

	/**
	 * Query users' offers
	 * 
	 * @param array $args
	 * @return mixed
	 */
	public static function query_offers( $args = '' )
	{
		global $wpdb;

		// default args
		$args = wp_parse_args( $args, array (
				'ID' => '',
				'post_type' => DCE_POST_TYPE_OFFER,
				'author' => '',
				'nopaging' => true,
				'post_status' => array( 'publish', 'pending' ),
		) );

		// query offers
		$single = !empty( $args['ID'] );
		if ( $single )
		{
			// single offer
			$offers = array( get_post( $args['ID'] ) );
		}
		else
		{
			// all offers
			$offers = get_posts( $args );
		}

		// holders
		$offer = null;
		$return = array();
		$len = count( $offers );

		if ( $len )
		{
			$coin_types = dce_get_coin_types();

			// wrapper loop
			for ( $i = 0; $i < $len; $i++ )
			{
				/* @var $offer WP_Post */
				$offer =& $offers[$i];

				// wrap offer data
				$return[] = self::wrap_offer( $offer, $coin_types );
			}
		}

		return apply_filters( 'dce_query_offers', $single ? $return[0] : $return );
	}

	/**
	 * Get array wrapper of the instance
	 * 
	 * @return array|boolean
	 */
	public function to_array()
	{
		return self::wrap_offer( $this );
	}

	/**
	 * Wrap offer data
	 *
	 * meta data, offer details, etc...
	 *
	 * @param DCE_Offer $offer
	 * @param array $coin_types
	 * @return array|boolean
	 */
	public static function wrap_offer( $offer, &$coin_types = '' )
	{
		if ( !$offer )
			return false;

		// offer instance
		if ( !is_a( $offer, 'DCE_Offer' ) )
			$offer = new DCE_Offer( $offer );

		// data wrapper
		return array (
				'ID' => $offer->ID,
				'user' => $offer->user,
				'from_amount' => $offer->from_amount,
				'from_coin' => $offer->from_coin,
				'from_display' => $offer->convert_from_display( $coin_types ),
				'to_amount' => $offer->to_amount,
				'to_coin' => $offer->to_coin,
				'to_display' => $offer->convert_to_display( $coin_types ),
				'details' => $offer->details,
				'datetime' => $offer->datetime,
				'status' => $offer->status,
				'url' => $offer->url(),
		);
	}
}

