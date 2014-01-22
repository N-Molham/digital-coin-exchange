<?php
/**
 * User's offers
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

/* @var $dce_user DCE_User */
global $dce_user;

// shortcode output
$output = '';

// current view
$current_view = dce_get_value( 'view' );
if ( !in_array( $current_view, array( 'view_offers', 'create_offer' ) ) )
	$current_view = 'view_offers';

// views switch
switch ( $current_view )
{
	case 'view_offers':
		$output .= '<a href="'. add_query_arg( 'view', 'create_offer' ) .'">'. __( 'Create New Offer', 'dce' ) .'</a>';
		break;

	case 'create_offer':
		// title
		$output .= dce_section_title( __( 'Create new offer', 'dce' ) );

		// form start
		$output .= '<form action="" method="post" id="new-offer-form" class="ajax-form">';

		// form end
		$output .= '</form>';
		break;
}

return $output;