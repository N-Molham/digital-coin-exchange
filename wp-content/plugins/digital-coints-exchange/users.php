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
	if ( isset( $_POST['register_user'], $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'] ,  'dce_user_register' ) )
	{
		//var_dump($_POST);exit();
		$register_fields = DCE_User::data_fields();
		foreach ( $register_fields as $field_name => &$field_args )
		{
			// parse field value
			$field_args['value'] = DCE_Utiles::parse_input( $field_name, $field_args );
		}

		// check form error
		if ( DCE_Utiles::has_form_errors() )
			dce_redirect();

		// lower email chars
		$register_fields['email']['value'] = strtolower( $register_fields['email']['value'] );

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
		if(isset($_POST['redirect_to']))
			dce_redirect($_POST['redirect_to']);
		else		
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
	 * Get user's messages
	 * 
	 * @param array $messages_args
	 * @return number|boolean|array
	 */
	public function get_messages( $messages_args = '' )
	{
		return self::query_messages( wp_parse_args( $messages_args, array( 'user_id' => $this->ID ) ) );
	}

	/**
	 * Query users messages/comments
	 * 
	 * @param array $messages_args
	 * @return number|boolean|array
	 */
	public static function query_messages( $messages_args = '' )
	{
		// parse defaults
		$messages_args = wp_parse_args( $messages_args, array ( 
				'user_id' => '',
				'object_id' => '',
				'target' => 'received',
				'meta_query' => array(),
		) );

		// comments query args
		$query_args = array ( 
				'status' => 'approve',
				'number' => '',
				'post_id' => $messages_args['object_id'],
				'user_id' => '',
				'meta_query' => $messages_args['meta_query'],
				'orderby' => 'comment_date',
		);

		// which way
		if ( 'sent' == $messages_args['target'] )
		{
			// sent messages
			$query_args['user_id'] = $messages_args['user_id'];
		}
		elseif ( 'received' == $messages_args['target'] )
		{
			// received messages
			$query_args['meta_query'][] = array( 'key' => '_target_user', 'value' => $messages_args['user_id'], 'compare' => '=' );
		}
		elseif ( 'both' == $messages_args['target'] )
		{
			// both all users
		}
		else
		{
			// invalid
			return false;
		}

		// query comments -> messages
		$comments = get_comments( $query_args );
		$messages = array();
		if ( is_array( $comments ) )
		{
			$len = count( $comments );
			for ( $i = 0; $i < $len; $i++ )
			{
				$messages[] = array (
						'ID' => $comments[$i]->comment_ID, // message id
						'object_id' => $comments[$i]->comment_post_ID, // related object
						'type' => $comments[$i]->comment_type, // object type
						'from' => new DCE_User( $comments[$i]->user_id ), // sender
						'to' => new DCE_User( get_comment_meta( $comments[$i]->comment_ID, '_target_user', true ) ), // receiver
						'message' => $comments[$i]->comment_content, // message
						'date_time' => $comments[$i]->comment_date, // message
						'replay' => $comments[$i]->comment_parent,
				);
			}
		}

		// return filtered messages
		return apply_filters( 'dce_user_messages', $messages, $query_args, $messages_args );
	}

	/**
	 * Send message to user
	 * 
	 * @param integer $user
	 * @param string $message
	 * @param integer $object_id
	 * @param string $type
	 * @param integer $reply_id
	 * @return integer|boolean
	 */
	public function send_message( $user_id, $message, $object_id, $type, $reply_id = 0 )
	{
		// check if to himself
		if ( $user_id == $this->ID )
			return false;

		// build message data
		$message_data = array (
				'comment_post_ID' => $object_id,
				'comment_author' => $this->display_name(),
				'comment_author_email' => $this->data->user_email,
				'comment_content' => $message,
				'user_id' => $this->ID,
				'comment_type' => $type,
				'comment_parent' => $reply_id,
				'comment_approved' => 1,
		);

		// insert & return msg id
		$message_id = wp_insert_comment( $message_data );
		if ( !$message_id )
			return false;

		// set target user
		update_comment_meta( $message_id, '_target_user', $user_id );

		// action
		do_action( 'dce_message_sent', array ( 
				'message_id' => $message_id,
				'from' => $this->ID,
				'to' => $user_id,
				'message' => $message,
				'object' => $object_id,
				'type' => $type,
				'reply' => $reply_id,
		) );

		return $message_id;
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
		// pass data to offers handler
		return DCE_Offer::save_offer( $this->ID, $from_amount, $from_coin, $to_amount, $to_coin, $offer_args );
	}

	/**
	 * Get user's offers
	 * 
	 * @param array $args
	 * @return array
	 */
	public function get_offers( $args = '' )
	{
		return apply_filters( 'dce_user_offers', DCE_Offer::query_offers( array( 'author' => $this->ID ) ), $this->ID );
	}

	/**
	 * Insert/Update user escrow 
	 * 
	 * @param int $from_amount
	 * @param string $from_coin
	 * @param int $to_amount
	 * @param string $to_coin
	 * @param array $escrow_args
	 * @return DCE_Escrow|WP_Error
	 */
	public function save_escrow( $from_amount, $from_coin, $to_amount, $to_coin, $escrow_args = '' )
	{
		// pass data to escrow handler
		return DCE_Escrow::save_escrow( $this->ID, $from_amount, $from_coin, $to_amount, $to_coin, $escrow_args );
	}

	/**
	 * Get user's escrows
	 * 
	 * @param array $args
	 * @return array
	 */
	public function get_escrows( $args = '' )
	{
		return apply_filters( 'dce_user_escrows', DCE_Escrow::query_escrows( array ( 'author' => $this->ID, 'party_email' => $this->data->user_email ) ), $this->ID );
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
					'required' => false,
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























