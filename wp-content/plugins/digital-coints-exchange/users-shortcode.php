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
add_shortcode( 'dce-single-escrow', 'dce_user_page_loader' );
add_shortcode( 'dce-send-message', 'dce_user_page_loader' );
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

	$public_tags = array ( 
			'dce-offers', 
			'dce-contact-form',
			'dce-user-profile',  
	);

	// check current user
	$dce_user = DCE_User::get_current_user();
	if ( !$dce_user->exists() && !in_array( $shortcode, $public_tags ) )
		return dce_alert_message( sprintf ( 
					__( 'This is a client access only. please <a href="%s">login</a> or <a href="%s">register</a>', 'dce' ), 
					add_query_arg( 'ref', $_SERVER['REQUEST_URI'], dce_get_pages( 'login' )->url ), 
					add_query_arg( 'ref', $_SERVER['REQUEST_URI'], dce_get_pages( 'register' )->url ) 
		), 'error' );

	// determine page path
	$user_page = DCE_PATH .'user-pages'. DIRECTORY_SEPARATOR . str_replace( 'dce-', '', $shortcode ) .'.php';

	// load file if exists
	if( file_exists( $user_page ) )
	{
		// globals
		$GLOBALS[$shortcode] = array( 'attrs' => wp_parse_args( $attrs, array() ), 'content' => $content );

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
		return dce_alert_message( __( 'You are already logged-in.', 'dce' ), 'general' );

	// style
	wp_enqueue_style( 'dce-shared-style' );

	// before form start
	$out = apply_filters( 'dce_before_login_form', '' );

	// error messages
	if ( DCE_Utiles::has_form_errors() )
		$out .= DCE_Utiles::show_form_errors();

	// where to redirect after login
	$redirect_to = isset( $_GET['ref'] ) ? $_GET['ref'] : dce_get_pages( 'user-offers' )->url;

	// login form
	$out .= '<div id="customer_login"><div id="customer_login_box">';
	$out .= '<h2>'. __( 'Login', 'dce' ) .'</h2><div class="sep-double"></div>';
	$out .= '<form action="'. wp_login_url() .'" method="post" name="login-form" id="login-form" class="login">';

	// Username/email
	$out .= '<p class="form-row form-row-first"><input style="width:85% !important" type="text" class="input-text" name="log" id="user_login" placeholder="'. __( 'E-mail', 'dce' ) .'"></p>';

	// password
	$out .= '<p class="form-row form-row-last"><input class="input-text" type="password" name="pwd" id="user_pass" placeholder="'. __( 'Password', 'dce' ) .'"></p>';
	$out .= '<div class="clear"></div>';

	// hidden fields
	$out .= '<p class="form-row"><input type="submit" class="button comment-submit small" name="wp-submit" value="'. __( 'Log In', 'dce' ) .'">';
	$out .= '<input type="hidden" name="redirect_to" value="'. $redirect_to .'">';

	// rememeber login
	$out .= '<span class="remember-box">';
	$out .= '<label for="rememberme"><input name="rememberme" type="checkbox" id="rememberme" value="forever">'. __( 'Remember Me', 'dce' ) .'</label></span></p>';

	// form end
	$out .= '</form></div></div>';

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

	// where to redirect after register
	$redirect_to = isset( $_GET['ref'] ) ? $_GET['ref'] : dce_get_pages( 'escrow-manager' )->url;

	$out .= '<form method="post" action="" id="home_reg" >';
	$out .= '<h2>Create Escrow - Free !</h2>';

	$out .= '<p class="form-row form-row-first validate-required" id="name"><input type="text" class="input-text" name="first_name" d="billing_first_name" placeholder="Name" value=""></p>';

	$out .='<p class="form-row form-row-first validate-required validate-email" id="email_field"><input type="text" class="input-text" name="email" id="email" placeholder="Email" value=""></p>';

	$out .='<p class="form-row form-row-first validate-required validate-password" id="password_field"><input type="password" class="input-text" name="password" id="password" placeholder="Select a password" value=""></p>';
	$out .='<p><a class="button small default" title="" href="Javascript:document.forms[0].submit()" id="home_reg_start" target="_self">Start Escrow</a></p>';
	$out .= '<input type="hidden" name="redirect_to" value="'. $redirect_to .'">';
	$out .= wp_nonce_field( 'dce_user_register', 'nonce', true, false );
	$out .= '<input type="hidden" name="register_user" value="Register">';
	$out .='</form>';
	
	$out .= apply_filters( 'dce_after_home_register_form', '' );

	return apply_filters( 'dce_home_register_form', $out );
}


