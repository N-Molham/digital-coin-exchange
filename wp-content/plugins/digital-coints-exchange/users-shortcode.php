<?php
/**
 * Users forms shortcodes
 * 
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

global $dce_user;

add_shortcode( 'dce-user-offers', 'dce_user_page_loader' );
add_shortcode( 'dce-offers', 'dce_user_page_loader' );
add_shortcode( 'dce-contact-form', 'dce_user_page_loader' );
add_shortcode( 'dce-user-dashboard', 'dce_user_page_loader' );
add_shortcode( 'dce-escrow-manager', 'dce_user_page_loader' );
add_shortcode( 'dce-user-profile', 'dce_user_page_loader' );
add_shortcode( 'dce-user-messages', 'dce_user_page_loader' );
/**
 * User's page loader
 * 
 * @param array $attrs
 * @param string $content
 * @param string $shortcode
 * @return string
 */
function dce_user_page_loader( $attrs, $content, $shortcode )
{
	global $dce_user;

	$public_tags = array( 'dce-offers', 'dce-contact-form', 'dce-user-profile' );

	// check current user
	$dce_user = DCE_User::get_current_user();
	if ( !$dce_user->exists() && !in_array( $shortcode, $public_tags ) )
		return dce_alert_message( __( 'This is a client access only.', 'dce' ), 'error' );

	// determine page path
	$user_page = DCE_PATH .'user-pages'. DIRECTORY_SEPARATOR . str_replace( 'dce-', '', $shortcode ) .'.php';

	// load file if exists
	if( file_exists( $user_page ) )
	{
		// found
		return include $user_page;
	}

	// not found
	return dce_alert_message( __( 'Page not found.', 'dce' ), 'error' );
}

add_shortcode( 'dce-login-form', 'dce_user_login_form' );
/**
 * User's login form layout
 *
 * @return string
 */
function dce_user_login_form()
{
	// logged-in user
	if ( is_user_logged_in() )
		return dce_alert_message( __( 'You are already logged-in.', 'dce' ), 'info' );

	// style
	wp_enqueue_style( 'dce-shared-style' );

	// before form start
	$out = apply_filters( 'dce_before_login_form', '' );

	// error messages
	if ( DCE_Utiles::has_form_errors() )
		$out .= DCE_Utiles::show_form_errors();

	// login form
	$out .= wp_login_form( array (
			'echo' => true,
			'redirect' => dce_get_pages( 'user-offers' )->url,
			'form_id' => 'login-form',
			'label_username' => __( 'E-mail', 'dce' ),
			'label_password' => __( 'Password', 'dce' ),
			'label_remember' => __( 'Remember Me', 'dce' ),
			'label_log_in' => __( 'Log In', 'dce' ),
	) );

	// before form start
	$out .= apply_filters( 'dce_after_login_form', '' );

	return apply_filters( 'dce_login_form', $out );
}


add_shortcode( 'dce-register-form', 'dce_user_register_form' );
/**
 * User's register form layout
 * 
 * @return string
 */
function dce_user_register_form()
{
	// whether registration is open or not
	if ( '0' == get_option( 'users_can_register' ) )
		return dce_alert_message( __( 'Registration is closed right now, try again later.', 'dce' ), 'error' );

	// logged-in user
	if ( is_user_logged_in() )
	{
		// success message
		if ( isset( $_GET['register'] ) && 'success' == $_GET['register'] )
			return dce_alert_message( __( 'Registration successful', 'dce' ), 'success' );

		return dce_alert_message( __( 'Your already registered.', 'dce' ), 'error' );
	}

	// style
	wp_enqueue_style( 'dce-shared-style' );

	// before form start
	$out = apply_filters( 'dce_before_register_form', '' );
 
	// error messages
	if ( DCE_Utiles::has_form_errors() )
		$out .= DCE_Utiles::show_form_errors();

	// form start
	$out .= '<form id="register-form" action="" method="post">';

	// form inputs loop
	$register_fields = '';
	foreach ( DCE_User::data_fields() as $field_name => $field_args )
	{
		// form input layout
		$field_args['value'] = dce_get_value( $field_name, '', true );
		$register_fields .= dce_form_input( $field_name, $field_args );
	}

	// hidden fields
	$register_fields .= wp_nonce_field( 'dce_user_register', 'nonce', true, false );

	// submit
	$register_fields .= '<p class="form-input"><input type="submit" name="register_user" value="'. __( 'Register', 'dce' ) .'" class="button small green" /></p>';

	// filtered inputs
	$out .= apply_filters( 'dce_register_form_inputs', $register_fields );

	// form end
	$out .= '</form>';

	// before form start
	$out .= apply_filters( 'dce_after_register_form', '' );

	return apply_filters( 'dce_register_form', $out );
}



add_shortcode( 'dce-home-register-form', 'dce_user_home_register_form' );
/**
 * User's register form layout
 * 
 * @return string
 */
function dce_user_home_register_form()
{
	// whether registration is open or not
	if ( '0' == get_option( 'users_can_register' ) )
		return dce_alert_message( __( 'Registration is closed right now, try again later.', 'dce' ), 'error' );

	// logged-in user
	if ( is_user_logged_in() )
	{
		$user = DCE_User::get_current_user();
		$out = "<br><br><h1> Welcome ".$user->first_name."</h1>";
		$out .= "<ul style='text-align: left;font-size: 3.2em;'>";
		$out .="<li><a href='#'>Manage Profile</a></li>";
		$out .="<li><a href='#'>Messages</a></li>";
		$out .="<li><a href='#'>Offers</a></li>";
		$out .="<li><a href='#'>New Escrow</a></li>";
		$out .="<li><a href='#'>Search Offers</a></li></ul>";
		return $out ;
	}


	// before form start
	$out = apply_filters( 'dce_before_home_register_form', '' );

 	if ( DCE_Utiles::has_form_errors() )
	{
		//var_dump(DCE_Utiles::show_form_errors());exit();
		DCE_Utiles::clear_values();
		if(strpos( DCE_Utiles::show_form_errors(), 'username already exists') !== false)	
			$out .= "<h4 style='color:red !important; margin-bottom:0'>This email already Exists!</h4>";
		else
			$out .= "<h4 style='color:red !important; margin-bottom:0'>Please enter all fields</h4>";

	}
	$out .= '<form method="post" action="" id="home_reg" >';
	$out .= '<h2>Create Escrow - Free !</h2>';

	$out .= '<p class="form-row form-row-first validate-required" id="name"><input type="text" class="input-text" name="first_name" d="billing_first_name" placeholder="Name" value=""></p>';

	$out .='<p class="form-row form-row-first validate-required validate-email" id="email_field"><input type="text" class="input-text" name="email" id="email" placeholder="Email" value=""></p>';

	$out .='<p class="form-row form-row-first validate-required validate-password" id="password_field"><input type="password" class="input-text" name="password" id="password" placeholder="Select a password" value=""></p>';
	$out .='<p><a class="button small default" title="" href="Javascript:document.forms[0].submit()" id="home_reg_start" target="_self">Start Escrow</a></p>';
	$out .= wp_nonce_field( 'dce_user_register', 'nonce', true, false );
	$out .= '<input type="hidden" name="register_user" value="Register">';
$out .='</form>';
	
	$out .= apply_filters( 'dce_after_home_register_form', '' );

	return apply_filters( 'dce_home_register_form', $out );
}


