<?php
/**
 * Users forms shortcodes
 * 
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

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
		return '<div class="alert error"><div class="msg">'. __( 'Registration is closed right now, try again later.', 'dce' ) .'</div></div>';

	// logged-in user
	if ( is_user_logged_in() )
	{
		// success message
		if ( isset( $_GET['register'] ) && 'success' == $_GET['register'] )
			return '<div class="alert success"><div class="msg">'. __( 'Registration successful', 'dce' ) .'</div></div>';

		return '<div class="alert error"><div class="msg">'. __( 'Your already registered.', 'dce' ) .'</div></div>';
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
		$register_fields .= DCE_Utiles::form_input( $field_name, $field_args );
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





