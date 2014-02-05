<?php
/**
 * Offers
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

add_action( 'pre_get_posts', 'dce_offers_admin_query_sorting' );
/**
 * Offers sorting
 * 
 * @param WP_Query $query
 */
function dce_offers_admin_query_sorting( $query )
{
	if ( !is_admin() || DCE_POST_TYPE_OFFER != $query->get( 'post_type' ) )
		return;

	$orderby = $query->get( 'orderby' );
	if ( in_array( $orderby, array( 'from_amount', 'from_coin', 'to_amount', 'to_coin' ) ) )
	{
		// set meta info
		$query->set( 'meta_key', $orderby );
		$query->set( 'orderby', strpos( $orderby, 'amount') !== false ? 'meta_value_num' : 'meta_value' ); 
	}
}

add_filter( 'manage_edit-'. DCE_POST_TYPE_OFFER .'_columns', 'dce_offers_admin_custom_columns_filter' );
/**
 * Offers columns
 * 
 * @param array $columns
 * @return array
 */
function dce_offers_admin_custom_columns_filter( $columns )
{
	// return offer data
	return array (
			'cb' => $columns['cb'],
			'author' => __( 'Owner', 'dce' ),
			'from_amount' => __( 'From Amount', 'dce' ),
			'from_coin' => __( 'From Coin', 'dce' ),
			'to_amount' => __( 'To Amount', 'dce' ),
			'to_coin' => __( 'To Coin', 'dce' ),
			'date' => $columns['date'],
			'actions' => __( 'Actions', 'dce' ),
	);
}

add_filter( 'manage_edit-'. DCE_POST_TYPE_OFFER .'_sortable_columns', 'dce_offers_admin_sortable_custom_columns_filter' );
/**
 * Offer sortable columns
 * 
 * @param array $columns
 * @return array
 */
function dce_offers_admin_sortable_custom_columns_filter( $columns )
{
	// return offer data
	return array (
			'author' => 'author',
			'from_amount' => 'from_amount',
			'from_coin' => 'from_coin',
			'to_amount' => 'to_amount',
			'to_coin' => 'to_coin',
			'date' => $columns['date'],
	);
}

add_action( 'manage_'. DCE_POST_TYPE_OFFER .'_posts_custom_column', 'dce_offers_admin_columns_content', 10, 3 );
/**
 * Offers columns content
 */
function dce_offers_admin_columns_content( $column, $post_id )
{
	// coin types
	$coin_types = dce_get_coin_types();

	// get offer data
	$offer = DCE_Offer::wrap_offer( get_post( $post_id ), $coin_types );
	if ( !$offer )
		return;

	// columns switch
	switch ( $column )
	{
		// amounts
		case 'from_amount';
		case 'to_amount';
			echo isset( $offer[$column] ) ? number_format( $offer[$column] ) : '-';
			break;

		// coins
		case 'from_coin':
		case 'to_coin':
			echo isset( $coin_types[ $offer[$column] ] ) ? $coin_types[ $offer[$column] ]['label'] : '-';
			break;

		// actions
		case 'actions':
			// offer details
			if ( !empty( $offer['details'] ) )
			{
				// trigger
				echo '<a href="#TB_inline?width=600&height=320&inlineId=offer-details-', $post_id ,'" class="thickbox button">', __( 'Details' ) ,'</a>';

				// details content
				echo '<div id="offer-details-', $post_id ,'" style="display:none;"><p>', $offer['details'] ,'</p></div>';
			}
			else
			{
				// disabled
				echo '<button disabled="disabled" class="button disabled">', __( 'No Details' ) ,'</button>';
			}

			// confirm/deny
			echo '<div class="offer-actions">';
			if ( 'publish' == $offer['status'] )
			{
				// status
				_e( 'Confirmed', 'dce' );
			}
			elseif ( 'denied' == $offer['status'] )
			{
				// status
				_e( 'Denied', 'dce' );
			}
			else 
			{
				// confirm
				echo '<a href="#" class="button button-primary" data-offer="', $post_id ,'" data-action="dce_confirm_offer">', __( 'Confirm', 'dce' ) ,'</a>&nbsp;';
				// deny
				echo '<a href="#" class="button button-delete" data-offer="', $post_id ,'" data-action="dce_deny_offer">', __( 'Deny', 'dce' ) ,'</a>';
			}
			echo '</div>';
			break;
	}
}

















