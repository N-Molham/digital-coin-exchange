<?php
/**
 * Users offers ( public )
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

// enqueues
wp_enqueue_style( 'dce-public-style' );
wp_enqueue_script( 'dce-public-offers', DCE_URL .'js/public-offers.js', array( 'dce-shared-script' ), false, true );

// shortcode output
$output = '';

$coin_types = dce_get_coin_types();

// search params
$search_params = isset( $_GET['search'] ) && is_array( $_GET['search'] ) ? array_map( 'DCE_Utiles::clean_string', $_GET['search'] ) : array();
$search_params = wp_parse_args( $search_params, array ( 
		'user' => '',
		'org_min' => '',
		'org_max' => '',
		'org_coin' => '',
		'target_min' => '',
		'target_max' => '',
		'target_coin' => '',
) );

// query args
$query_args = array( 'post_status' => 'publish', 'meta_query' => array() );

// search params loop
foreach ( $search_params as $search_key => $search_value )
{
	switch ( $search_key )
	{
		// amounts
		case 'org_min':
		case 'org_max':
		case 'target_min':
		case 'target_max':
			$search_value = (int) $search_value;
			if ( $search_value < 1 )
				continue;

			$query_args['meta_query'][] = array (
					'key' => 'org_min' == $search_key || 'org_max' == $search_key ? 'from_amount' : 'to_amount',
					'value' => $search_value,
					'compare' => 'org_min' == $search_key || 'target_min' == $search_key ? '>=' : '<=',
					'type' => 'NUMERIC',
			);
			break;

		// coin types
		case 'org_coin':
		case 'target_coin':
			if ( !isset( $coin_types[$search_value] ) )
				continue;

			$query_args['meta_query'][] = array ( 
					'key' => 'org_coin' == $search_key ? 'from_coin' : 'to_coin',
					'value' => $search_value,
					'compare' => '=',
			);
			break;
	}
}

// get confirmed offers
$offers = DCE_User::query_offers( $query_args );

// offers search/filters
$output .= dce_section_title( __( 'Offers Search', 'dce' ) );
$output .= '<div id="offer-search"><form action="" method="get">';

// Username
$output .= '<div class="search-holder"><input type="text" class="input-text" name="search[user]" placeholder="'. __( 'Username', 'dce' ) .'" value="'. $search_params['user'] .'"></div>';

// Original Min & Max amounts
$output .= '<div class="search-holder one_half"><input type="text" class="input-text" name="search[org_min]" placeholder="'. __( 'Original Amount Min.', 'dce' ) .'" value="'. $search_params['org_min'] .'"></div>';
$output .= '<div class="search-holder one_half last"><input type="text" class="input-text" name="search[org_max]" placeholder="'. __( 'Original Amount Max.', 'dce' ) .'" value="'. $search_params['org_max'] .'"></div>';

// Original coin
$output .= '<div class="search-holder"><select name="search[org_coin]" class="input-text"><option value="">'. __( 'Original Coin', 'dce' ) .'</option>';
foreach ( $coin_types as $coin_name => $coin_attrs )
{
	$output .= '<option value="'. $coin_name .'"'. ( $coin_name == $search_params['org_coin'] ? ' selected' : '' ) .'>'. $coin_attrs['label'] .'</option>';
}
$output .= '</select></div>';

// Target Min & Max amounts
$output .= '<div class="search-holder one_half"><input type="text" class="input-text" name="search[target_min]" placeholder="'. __( 'Target Amount Min.', 'dce' ) .'" value="'. $search_params['target_min'] .'"></div>';
$output .= '<div class="search-holder one_half last"><input type="text" class="input-text" name="search[target_max]" placeholder="'. __( 'Target Amount Max.', 'dce' ) .'" value="'. $search_params['target_max'] .'"></div>';

// Target coin
$output .= '<div class="search-holder"><select name="search[target_coin]" class="input-text"><option value="">'. __( 'Target Coin', 'dce' ) .'</option>';
foreach ( $coin_types as $coin_name => $coin_attrs )
{
	$output .= '<option value="'. $coin_name .'"'. ( $coin_name == $search_params['target_coin'] ? ' selected' : '' ) .'>'. $coin_attrs['label'] .'</option>';
}
$output .= '</select></div>';

// submit & clear
$output .= '<div class="search-holder">';
$output .= dce_button( array( 'tag' => 'input', 'class' => array( 'small', 'comment-submit' ), 'value' => __( 'Search', 'dce' ), 'type' => 'submit' ) );
$output .= '&nbsp;&nbsp;<a href="'. get_permalink() .'" class="button small lightgray">'. __( 'Clear', 'dce' ) .'</a>';
$output .= '</div>';

// search end
$output .= '</form></div>';

// offers table start
$output .= dce_section_title( __( 'Open Offers', 'dce' ) );
$output .= dce_table_start( 'open-offers' ) .'<thead><tr>';
$output .= '<th>'. __( 'Owner', 'dce' ) .'</th>';
$output .= '<th>'. __( 'Original', 'dce' ) .'</th>';
$output .= '<th>'. __( 'Target', 'dce' ) .'</th>';
$output .= '<th>'. __( 'Date &amp; Time', 'dce' ) .'</th>';
$output .= '<th>'. __( 'Details', 'dce' ) .'</th>';
$output .= '</tr></thead><tbody>';


if ( count( $offers ) )
{
	foreach ( $offers as $offer )
	{
		// if there are details for the offer
		$has_details = !empty( $offer['details'] );
	
		// data display
		$output .= '<tr><td><a href="'. $offer['user']->profile_url() .'">'. $offer['user']->display_name .'</a></td>';
		$output .= '<td>'. $offer['from_display'] .'</td>';
		$output .= '<td>'. $offer['to_display'] .'</td>';
		$output .= '<td>'. $offer['datetime'] .'</td>';
		$output .= '<td><a href="#offer-details-'. $offer['ID'] .'" class="button small darkgray'. ( $has_details ? '' : ' disabled' ) .'">'. __( 'Details', 'offer' ) .'</a></td></tr>';
	
		// offer details
		if ( $has_details )
			$output .= '<tr id="offer-details-'. $offer['ID'] .'" class="offer-details"><td colspan="5"><div class="content"><strong>'. __( 'Offer Details', 'dce' ).':</strong> '. $offer['details'] .'</div></td></tr>';
	}
}
else 
{
	// empty result
	$output .= '<tr><td colspan="5"><strong>'. __( 'No offers found', 'dce' ).'</strong></td></tr>';
}

// table end
$output .= '</tbody>'. dce_table_end();

return $output;
















