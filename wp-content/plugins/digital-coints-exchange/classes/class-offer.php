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
	 * Post type
	 *
	 * @var string
	 */
	static $post_type = DCE_POST_TYPE_OFFER;

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
	 * Commission payment method
	 * 
	 * @var string
	 */
	var $comm_method;

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

		// check existence
		if ( !$this->exists() )
			return false;

		// initialize properties
		$this->user = new DCE_User( $this->post_author );

		// fill in fields 
		$fields = array_keys( self::form_fields() );
		foreach ( $fields as $field_name )
		{
			switch ( $field_name )
			{
				case 'details':
					$this->$field_name =& $this->post_content;
					break;

				default:
					$this->$field_name = $this->post_object->$field_name;
			}
		}
	}

	/**
	 * Commission payment method display label
	 * 
	 * @return string
	 */
	public function commission_method_display()
	{
		$display = @self::form_fields()['comm_method']['source'][$this->comm_method];
		return empty( $display ) ? __( 'Not Set', 'dce' ) : $display;
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
	 * Offer Status
	 * 
	 * @return string
	 */
	public function get_status()
	{
		return 'publish' == $this->status ? 'confirmed' : $this->status;
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
				'comm_method' => '',
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
		update_post_meta( $offer_id, 'comm_method', $offer_args['comm_method'] );

		// wp action
		do_action( 'dce_save_user_offer', $offer_id );

		return $offer_id;
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
				'comm_method' => $offer->commission_method_display(),
				'details' => $offer->post_content,
				'datetime' => $offer->datetime,
				'status' => $offer->get_status(),
				'url' => $offer->url(),
		);
	}

	/**
	 * Form data fields
	 * 
	 * @param array $coin_types
	 * @return array
	 */
	public static function form_fields( &$coin_types = '' )
	{
		if ( empty( $coin_types ) )
			$coin_types = dce_get_coin_types();

		return array (
				'from_amount' => array ( 
						'label' => __( 'From Amount', 'dce' ), 
						'input' => 'text',
						'data_type' => 'int',
						'required' => true,
				),
				'from_coin' => array ( 
						'label' => __( 'From Coin', 'dce' ), 
						'input' => 'select', 
						'required' => true,
						'source' => $coin_types, 
				),
				'to_amount' => array ( 
						'label' => __( 'To Amount', 'dce' ), 
						'input' => 'text', 
						'data_type' => 'int',
						'required' => true,
				),
				'to_coin' => array ( 
						'label' => __( 'To Coin', 'dce' ), 
						'input' => 'select', 
						'required' => true,
						'source' => $coin_types, 
				),
				'comm_method' => array ( 
						'label' => __( 'Commission Agreement', 'dce' ), 
						'input' => 'select', 
						'default_value' => 'none',
						'required' => true,
						'source' => array ( 
								'by_user' => __( 'I Will pay 100% of the commission', 'dce' ), 
								'by_target' => __( 'The other party will pay 100% of the commission', 'dce' ), 
								'50_50' => __( 'Both parties will split commission fees by 50% 50%', 'dce' ),
						),
				),
				'details' => array ( 
						'label' => __( 'Deal Details', 'dce' ), 
						'input' => 'textarea', 
						'cols' => 42, 
						'rows' => 8,
						'data_type' => 'text',
						'required' => false,
						'max_length' => 500,
				),
		);
	}
}





















