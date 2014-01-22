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


