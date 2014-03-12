<?php
/**
 * User's Profile
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

/* @var $dce_user DCE_User */
global $dce_user, $wp_query;

// enqueues
wp_enqueue_script( 'dce-rateit-script' );

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
	$output .= $public_profile ? '' : '<p><a href="'. add_query_arg( 'edit_profile', 'yes' ) .'" class="button small green">'. __( 'Edit Profile', 'dce' ) .'</a></p>';

	// list feedbacks
	$output .= dce_section_title( __( 'Users Feedbacks', 'dce' ) );

	$user_feedbacks = $dce_user->get_feedbacks();
	$len = count( $user_feedbacks );

	if ( $len )
	{
		// feedback loop
		for ( $i = 0; $i < $len; $i++ )
		{
			$feedback =& $user_feedbacks[$i];

			// review start
			$output .= '<div class="feedback review male">';

			// feedback
			$output .= '<blockquote><q>'. $feedback['feedback'] .'</q><div class="clearfix">';

			// name
			$output .= '<span class="company-name">';
			$output .= '<strong>'. $feedback['by'] .'</strong>,&nbsp;';

			// rating
			$output .= '<span class="rateit" data-rateit-value="'. $feedback['rating'] .'" data-rateit-ispreset="true" data-rateit-readonly="true"></span>';

			// name end
			$output .= '</span></div>';

			// feedback end
			$output .= '</blockquote>';

			// review end
			$output .= '</div>';
		}
	}
	else
	{
		$output .= dce_promotion_box( __( 'No Feedbacks yet', 'dce' ) );
	}
}


return $output;
















