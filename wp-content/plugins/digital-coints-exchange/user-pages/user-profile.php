<?php
/**
 * User's Profile
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

/* @var $dce_user DCE_User */
global $dce_user, $wp_query;

// shortcode output
$output = '';

// target user
$public_profile = (int) get_query_var( 'user_id' );

// edit profile
if ( $dce_user->exists() && !$public_profile && 'yes' == dce_get_value( 'edit_profile' ) )
{
	$output .= dce_section_title( __( 'Edit Profile', 'dce' ) );

	$output .= do_shortcode( '[dce-register-form edit_user="yes"]' );
}
else
{
	if ( $public_profile )
	{
		// view other user profile
		$dce_user = new DCE_User( $public_profile );
		if ( !$dce_user->exists() )
			return dce_alert_message( __( 'Unknown user !!!', 'dce' ), 'error' );
	}
	else
	{
		// user see his own profile
		if ( !$dce_user->exists() )
			return dce_alert_message( __( 'This is a client access only.', 'dce' ), 'error' );
	}

	$output .= dce_section_title( $public_profile ? __( 'User Profile', 'dce' ) : __( 'Your Profile', 'dce' ) );

	// profile fields start
	$output .= dce_table_start( 'user-profile' );

	// profile fields loop
	$profile_fields = DCE_User::data_fields();
	foreach ( $profile_fields as $field_name => $field_attrs )
	{
		// check public data
		if ( !$field_attrs['public'] )
			continue;

		// data label
		$output .= '<tr><th>'. $field_attrs['label'] .'</th>';

		// data display
		$output .= '<td>'. $dce_user->get_profile_field( $field_name ) .'</td></tr>';
	}

	// profile fields end
	$output .= dce_table_end();

	// Edit profile link
	$output .= $public_profile ? '' : '<a href="'. add_query_arg( 'edit_profile', 'yes' ) .'" class="button small green">'. __( 'Edit Profile', 'dce' ) .'</a>';
}


return $output;
















