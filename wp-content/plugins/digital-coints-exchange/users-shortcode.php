<?php
/**
 * Users forms shortcodes
 * 
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

global $dce_user;

add_action( 'template_redirect', 'dce_avada_theme_settings_override' );
/**
 * Override Avada theme settings for some pages
 */
function dce_avada_theme_settings_override()
{
	global $data;

	// check post type
	$post_type = get_post_type();
	if ( !in_array( $post_type, array( DCE_POST_TYPE_ESCROW, DCE_POST_TYPE_OFFER, 'page' ) ) )
		return;

	// check page
	if ( 'page' == $post_type && !has_shortcode( get_post()->post_content, 'dce-user-dashboard' ) )
		return;

	// Avada theme
	if ( !empty( $data ) )
	{
		// full width layout
		$data['single_post_full_width'] = true;

		// hide post navigation
		$data['blog_pn_nav'] = true;

		// hide sharing box
		$data['social_sharing_box'] = false;

		// hide comments
		$data['blog_comments'] = false;

		// hide author
		$data['author_info'] = false;

		// hide post meta
		$data['post_meta'] = false;
	}
}

add_shortcode( 'dce-user-offers', 'dce_user_page_loader' );
add_shortcode( 'dce-offers', 'dce_user_page_loader' );
add_shortcode( 'dce-recent-offers', 'dce_user_page_loader' );
add_shortcode( 'dce-contact-form', 'dce_user_page_loader' );
add_shortcode( 'dce-user-dashboard', 'dce_user_page_loader' );
add_shortcode( 'dce-escrow-manager', 'dce_user_page_loader' );
add_shortcode( 'dce-user-profile', 'dce_user_page_loader' );
add_shortcode( 'dce-user-messages', 'dce_user_page_loader' );
add_shortcode( 'dce-single-escrow', 'dce_user_page_loader' );
add_shortcode( 'dce-single-offer', 'dce_user_page_loader' );
add_shortcode( 'dce-send-message', 'dce_user_page_loader' );
add_shortcode( 'dce-user-feedback', 'dce_user_page_loader' );
add_shortcode( 'dce-user-trans-history', 'dce_user_page_loader' );
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
			'dce-recent-offers', 
			'dce-contact-form',
			'dce-user-profile',
			'dce-single-offer',
	);

	// check current user
	$dce_user = DCE_User::get_current_user();
	if ( !dce_is_user_admin( $dce_user ) && !$dce_user->exists() && !in_array( $shortcode, $public_tags ) )
		return dce_alert_message( sprintf ( 
					__( 'This is a client access only. please <a href="%s">login</a> or <a href="%s">register</a>', 'dce' ), 
					add_query_arg( 'ref', $_SERVER['REQUEST_URI'], dce_get_pages( 'login' )->url ), 
					add_query_arg( 'ref', $_SERVER['REQUEST_URI'], dce_get_pages( 'register' )->url ) 
		), 'error' );

	// determine page path
	$user_page = DCE_PATH .'user-pages/'. str_replace( 'dce-', '', $shortcode ) .'.php';

	// load file if exists
	if( file_exists( $user_page ) )
	{
		// globals
		$GLOBALS[$shortcode] = array( 'attrs' => wp_parse_args( $attrs, array() ), 'content' => $content );

		// found
		return apply_filters( 'dce_shortcode_output', include $user_page, $content, $shortcode );
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
function dce_user_register_form( $attrs )
{
	global $dce_user;

	// whether registration is open or not
	if ( '0' == get_option( 'users_can_register' ) )
		return dce_alert_message( __( 'Registration is closed right now, try again later.', 'dce' ), 'error' );

	// default attributes
	$attrs = wp_parse_args( $attrs, array ( 
			'edit_user' => 'no',
			'short' => 'no',
			'submit' => '',
			'redirect_to' => '',
	) );

	// checks
	$edit_user = 'yes' == $attrs['edit_user'];
	$short_version = 'yes' == $attrs['short'];

	// register fields data
	$data_fields = DCE_User::data_fields();

	// logged-in user
	if ( is_user_logged_in() )
	{
		if ( $edit_user )
		{
			// user edit's his profile
			$fields_names = array_keys( $data_fields );
			foreach ( $fields_names as $fname )
			{
				if ( 'password' == $fname )
					continue;

				// fill in request data
				$_REQUEST[$fname] = $dce_user->$fname;
			}
		}
		else
		{
			// success message
			if ( isset( $_GET['register'] ) && 'success' == $_GET['register'] )
				return dce_alert_message( __( 'Registration successful', 'dce' ), 'success' );

			// logged-in user access register page directry
			return dce_alert_message( __( 'Your already registered.', 'dce' ), 'error' );
		}
	}

	// style
	wp_enqueue_style( 'dce-shared-style' );

	// before form start
	$output = apply_filters( 'dce_before_register_form', '' );
 
	// error messages
	if ( DCE_Utiles::has_form_errors() )
	{
		if ( $short_version && count( $_SESSION['form_errors'] ) > 1 )
		{
			$output .= dce_alert_message( __( 'Error, Please fill in all fields', 'dce' ), 'error' );
			DCE_Utiles::clear_form_errors();
		}
		else
			$output .= DCE_Utiles::show_form_errors();
	}

	// form start
	$output .= '<form id="register-form" action="" method="post">';

	// form inputs loop
	$register_fields = '';
	foreach ( $data_fields as $field_name => $field_args )
	{
		// form input layout
		if ( $short_version && !$field_args['required'] )
			continue;

		$field_args['value'] = dce_get_value( $field_name, '', true );
		$register_fields .= dce_form_input( $field_name, $field_args );
	}

	// hidden fields
	$register_fields .= wp_nonce_field( 'dce_user_register', 'nonce', true, false );

	// redirect after save
	if ( $edit_user )
	{
		// profile page
		$redirect_to = dce_get_pages( 'profile' )->url;
	}
	elseif( !empty( $attrs['redirect_to'] ) )
	{
		// as the redirect_to attribute say
		$redirect_to = $attrs['redirect_to'];
	}
	else
	{
		// otherwise to "ref" query string or escrow manage page
		$redirect_to = isset( $_GET['ref'] ) ? $_GET['ref'] : dce_get_pages( 'escrow-manager' )->url;
	}

	// redirect field
	$register_fields .= '<input type="hidden" name="redirect_to" value="'. $redirect_to .'">';

	// submit
	$register_fields .= '<p class="form-input"><input type="submit" name="register_user" value="';
	if ( $edit_user )
	{
		// update
		$register_fields .= $edit_user;
	}
	else
	{
		// new
		$register_fields .= '' == $attrs['submit'] || empty( $attrs['submit'] ) ? __( 'Register', 'dce' ) : $attrs['submit'];
	}
	$register_fields .= '" class="button small green" /></p>';

	// filtered inputs
	$output .= apply_filters( 'dce_register_form_inputs', $register_fields );

	// form end
	$output .= '</form>';

	// before form start
	$output .= apply_filters( 'dce_after_register_form', '' );

	return apply_filters( 'dce_register_form', $output );
}



add_shortcode( 'dce-home-register-form', 'dce_user_home_register_form' );
/**
 * User's register form layout
 * 
 * @return string
 */
function dce_user_home_register_form( $attrs )
{
	// whether registration is open or not
	if ( '0' == get_option( 'users_can_register' ) )
		return dce_alert_message( __( 'Registration is closed right now, try again later.', 'dce' ), 'error' );

	// default attributes
	$attrs = wp_parse_args( $attrs, array (
			'submit' => '',
	) );

	$pages = dce_get_pages();
	$escrow_wizard = esc_attr( add_query_arg( 'view', 'create_escrow', $pages['escrow-manager']['url'] ) );

	// logged-in user
	if ( is_user_logged_in() )
	{
		// logged-in user
		$user = DCE_User::get_current_user();

		// list
		$out = '<h1>'. sprintf( __( 'Welcome, <span class="ucword">%s</span>', 'dce' ), $user->first_name ) .'</h1>';
		$out .= '<ul style="text-align: left;font-size: 3.2em;">';
		$out .= '<li><a href="'. esc_attr( $pages['profile']['url'] ) .'">'. __( 'Manage Profile', 'dce' ) .'</a></li>';
		$out .= '<li><a href="'. esc_attr( $pages['messages']['url'] ) .'">'. __( 'Messages', 'dce' ) .'</a></li>';
		$out .= '<li><a href="'. esc_attr( $pages['user-offers']['url'] ) .'">'. __( 'Offers', 'dce' ) .'</a></li>';
		$out .= '<li><a href="'. esc_attr( $escrow_wizard ) .'">'. __( 'New Escrow', 'dce' ) .'</a></li>';
		$out .= '<li><a href="'. esc_attr( $pages['offers']['url'] ) .'">'. __( 'Search Offers', 'dce' ) .'</a></li></ul>';
		return $out ;
	}


	// before form
	$out = apply_filters( 'dce_before_home_register_form', '' );

	// register form
 	$out .= '<div id="home-register">'. do_shortcode( '[dce-register-form short="yes" submit="'. $attrs['submit'] .'" redirect_to="'. $escrow_wizard .'"]' ) .'</div>';

 	// after form
	$out .= apply_filters( 'dce_after_home_register_form', '' );

	return apply_filters( 'dce_home_register_form', $out );
}


