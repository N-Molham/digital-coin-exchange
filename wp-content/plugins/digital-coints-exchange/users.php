<?php
/**
 * Users
 * 
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

add_action( 'init', 'dce_users_init');
/**
 * Users initialize
 */
function dce_users_init()
{
	// request data cache
	DCE_Utiles::catch_request_data();

	// register handler
	if ( isset( $_POST['register_user'], $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'dce_user_register' ) )
	{
		$register_fields = DCE_User::data_fields();
		foreach ( $register_fields as $field_name => &$field_args )
		{
			// parse field value
			$field_args['value'] = DCE_Utiles::parse_input( $field_name, $field_args );
		}

		// check form error
		if ( DCE_Utiles::has_form_errors() )
			dce_redirect();

		// register attrs
		$user_attrs = array ( 
				'user_login' => $register_fields['email']['value'],
				'user_pass' => $register_fields['password']['value'],
				'user_email' => $register_fields['email']['value'],
				'display_name' => $register_fields['first_name']['value'] .' '. $register_fields['last_name']['value'],
				'first_name' => $register_fields['first_name']['value'],
				'last_name' => $register_fields['last_name']['value'],
		);

		$user_id = wp_insert_user( $user_attrs );
		if ( is_wp_error( $user_id ) )
		{
			// save error
			DCE_Utiles::form_error( 'general', $user_id->get_error_message() );
			dce_redirect();
		}

		// update user meta
		update_user_meta( $user_id, 'phone', $register_fields['phone']['value'] );
		update_user_meta( $user_id, 'address', $register_fields['address']['value'] );

		// clear data
		DCE_Utiles::clear_values();

		// login user
		wp_signon( array( 'user_login' => $register_fields['email']['value'], 'user_password' => $register_fields['password']['value'] ) );

		// redirect
		dce_redirect( add_query_arg( 'register', 'success' ) );
	}
}

/**
 * Check if the user is admin or not
 * 
 * @param number|WP_User $user_id
 * @return boolean
 */
function dce_is_user_admin( &$user_id = null )
{
	// check if no user data passed
	if ( !$user_id )
		return current_user_can( 'manage_options' );

	// check if id is passed
	if ( !is_object( $user_id ) || is_numeric( $user_id ) )
		$user_id = get_user_by( 'id' , (string) $user_id );

	// check permission
	if ( $user_id && is_object( $user_id ) && $user_id->has_cap( 'manage_options' ) )
		return true;

	// return false
	return false;
}

/**
 * Exchange User Class
 * 
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */
class DCE_User extends WP_User
{
	/**
	 * WP User role
	 * 
	 * @var string
	 */
	static $role = DCE_CLIENT_ROLE;

	/**
	 * Constructor
	 * 
	 * @param number $id
	 * @param string $name
	 * @param string $blog_id
	 */
	public function __construct( $id = 0, $name = '', $blog_id = '' )
	{
		parent::__construct( $id, $name, $blog_id );
	}

	/**
	 * Determine whether the user exists in the database and a client.
	 * 
	 * (non-PHPdoc)
	 * @see WP_User::exists()
	 * @return boolean
	 */
	public function exists()
	{
		return parent::exists() && in_array( self::$role, $this->roles );
	}

	/**
	 * Get user's display name
	 * 
	 * @return string
	 */
	public function display_name()
	{
		return $this->display_name;
	}

	/**
	 * Get user profile page
	 * 
	 * @return string
	 */
	public function profile_url()
	{
		$profile_page = dce_get_pages( 'profile' )->url;
		if( !preg_match( '/\/$/', $profile_page ) )
			$profile_page .= '/';

		return dce_get_pages( 'profile' )->url . $this->ID;
	}

	/**
	 * Insert/Update user offer 
	 * 
	 * @param int $from_amount
	 * @param string $from_coin
	 * @param int $to_amount
	 * @param string $to_coin
	 * @param array $offer_args
	 * @return int|WP_Error
	 */
	public function save_offer( $from_amount, $from_coin, $to_amount, $to_coin, $offer_args = '' )
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
				'post_author' => $this->ID,
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
	 * Get user's offers
	 * 
	 * @param array $args
	 * @return array
	 */
	public function get_offers( $args = '' )
	{
		// default args
		$args = wp_parse_args( $args, array (
				'author' => $this->ID,
		) );

		$results = self::query_offers( $args );

		return apply_filters( 'dce_user_offers', $results, $this->ID );
	}

	public static function query_offers( $args )
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
	 * Wrap offer data 
	 * 
	 * meta data, offer details, etc...
	 * 
	 * @param WP_Post $offer
	 * @param array $coin_types
	 * @return array|boolean
	 */
	public static function wrap_offer( $offer, &$coin_types )
	{
		if ( !$offer )
			return false;

		// data wrapper
		return array (
				'ID' => $offer->ID,
				'user' => isset( $this ) ? $this : new DCE_User( $offer->post_author ),
				'from_amount' => $offer->from_amount,
				'from_coin' => $offer->from_coin,
				'from_display' => _n( sprintf( $coin_types[$offer->from_coin]['single'], $offer->from_amount ), sprintf( $coin_types[$offer->from_coin]['plural'], $offer->from_amount ), $offer->from_amount ),
				'to_amount' => $offer->to_amount,
				'to_coin' => $offer->to_coin,
				'to_display' => _n( sprintf( $coin_types[$offer->to_coin]['single'], $offer->to_amount ), sprintf( $coin_types[$offer->to_coin]['plural'], $offer->to_amount ), $offer->to_amount ),
				'details' => $offer->post_content,
				'datetime' => $offer->post_date,
				'status' => $offer->post_status,
				'url' => get_permalink( $offer->ID ),
		);
	}

	/**
	 * User data fields 
	 * 
	 * @return array
	 */
	public static function data_fields()
	{
		return apply_filters( 'dce_user_data_fields', array ( 
			'first_name' => array (
					'input' => 'text',
					'label' => __( 'First Name', 'dce' ),
					'data_type' => 'text',
					'required' => true,
					'min_length' => 3,
					'max_length' => 32,
			),
			'last_name' => array (
					'input' => 'text',
					'label' => __( 'Last Name', 'dce' ),
					'data_type' => 'text',
					'required' => true,
					'min_length' => 3,
					'max_length' => 32,
			),
			'email' => array (
					'input' => 'text',
					'label' => __( 'E-mail', 'dce' ),
					'data_type' => 'email',
					'required' => true,
			),
			'password' => array (
					'input' => 'password',
					'label' => __( 'Password', 'dce' ),
					'data_type' => 'password',
					'required' => true,
			),
			'phone' => array (
					'input' => 'text',
					'label' => __( 'Phone number', 'dce' ),
					'data_type' => 'text',
					'required' => false,
					'max_length' => 32,
			),
			'address' => array (
					'input' => 'text',
					'label' => __( 'Address', 'dce' ),
					'data_type' => 'text',
					'required' => false,
					'max_length' => 200,
			),
		) );
	}

	/**
	 * Retrieve the current user object.
	 *
	 * @return DCE_User
	 */
	public static function get_current_user()
	{
		return new DCE_User( wp_get_current_user() );
	}
}























