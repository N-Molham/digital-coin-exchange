<?php
/**
 * Offers/Escrows
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

add_action( 'pre_get_posts', 'dce_components_admin_query_sorting' );
/**
 * Components sorting
 * 
 * @param WP_Query $query
 */
function dce_components_admin_query_sorting( $query )
{
	if ( !is_admin() || !in_array( $query->get( 'post_type' ), array( DCE_POST_TYPE_OFFER ) ) )
		return;

	$orderby = $query->get( 'orderby' );
	if ( in_array( $orderby, array( 'from_amount', 'from_coin', 'to_amount', 'to_coin' ) ) )
	{
		// set meta info
		$query->set( 'meta_key', $orderby );
		$query->set( 'orderby', strpos( $orderby, 'amount') !== false ? 'meta_value_num' : 'meta_value' ); 
	}
}

add_filter( 'manage_edit-'. DCE_POST_TYPE_OFFER .'_columns', 'dce_components_admin_custom_columns_filter' );
add_filter( 'manage_edit-'. DCE_POST_TYPE_ESCROW .'_columns', 'dce_components_admin_custom_columns_filter' );
/**
 * Components columns
 * 
 * @param array $columns
 * @return array
 */
function dce_components_admin_custom_columns_filter( $columns )
{
	global $wp_query;

	$new_columns = array (
			'cb' => $columns['cb'],
			'owner' => __( 'Owner', 'dce' ),
	);

	// escrow target user
	if ( DCE_POST_TYPE_ESCROW == $wp_query->get( 'post_type' ) )
		$new_columns['target_user'] = __( 'Target User', 'dce' );

	$new_columns = array_merge( $new_columns, array (
			'from_amount' => __( 'From Amount', 'dce' ),
			'from_coin' => __( 'From Coin', 'dce' ),
			'to_amount' => __( 'To Amount', 'dce' ),
			'to_coin' => __( 'To Coin', 'dce' ),
			'date' => $columns['date'],
			'actions' => __( 'Actions', 'dce' ),
	) );

	// return component data
	return $new_columns;
}

add_filter( 'manage_edit-'. DCE_POST_TYPE_OFFER .'_sortable_columns', 'dce_components_admin_sortable_custom_columns_filter' );
add_filter( 'manage_edit-'. DCE_POST_TYPE_ESCROW .'_sortable_columns', 'dce_components_admin_sortable_custom_columns_filter' );
/**
 * Components sortable columns
 * 
 * @param array $columns
 * @return array
 */
function dce_components_admin_sortable_custom_columns_filter( $columns )
{
	return array (
			'from_amount' => 'from_amount',
			'from_coin' => 'from_coin',
			'to_amount' => 'to_amount',
			'to_coin' => 'to_coin',
			'date' => $columns['date'],
	);
}

add_action( 'manage_'. DCE_POST_TYPE_OFFER .'_posts_custom_column', 'dce_components_admin_columns_content', 10, 2 );
add_action( 'manage_'. DCE_POST_TYPE_ESCROW .'_posts_custom_column', 'dce_components_admin_columns_content', 10, 2 );
/**
 * Components columns content
 */
function dce_components_admin_columns_content( $column, $post_id )
{
	// coin types
	$coin_types = dce_get_coin_types();

	// get item data
	$post = get_post( $post_id );
	$item = DCE_POST_TYPE_OFFER == $post->post_type ? new DCE_Offer( $post ) : new DCE_Escrow( $post );
	if ( !$item->exists() )
		return;

	// columns switch
	switch ( $column )
	{
		// amounts
		case 'from_amount';
		case 'to_amount';
			echo $item->$column;
			break;

		// coins
		case 'from_coin':
		case 'to_coin':
			echo isset( $coin_types[ $item->$column ] ) ? $coin_types[ $item->$column ]['label'] : '-';
			break;

		case 'owner':
			echo '<a href="', self_admin_url( 'user-edit.php?user_id=' . $item->user->ID ) ,'">', $item->user->data->display_name ,'</a>';
			break;

		case 'target_user':
			$wp_user = get_user_by( 'email', $item->target_email );
			if ( $wp_user )
				echo '<a href="', self_admin_url( 'user-edit.php?user_id=' . $wp_user->ID ) ,'">', $wp_user->data->display_name ,'</a>';
			else 
				echo $item->target_email;
			break;

		// actions
		case 'actions':
			// offer details
			if ( !empty( $item->details ) )
			{
				// trigger
				echo '<a href="#TB_inline?width=600&height=320&inlineId=com-details-', $post_id ,'" class="thickbox button">', __( 'Details', 'dce' ) ,'</a>';

				// details content
				echo '<div id="com-details-', $post_id ,'" style="display:none;"><p>', $item->details ,'</p></div>';
			}
			else
			{
				// disabled
				echo '<button disabled="disabled" class="button disabled">', __( 'No Details', 'dce' ) ,'</button>';
			}

			// confirm/deny offers
			echo '<div class="offer-actions">';
			if ( isset( $item->target_email ) )
			{
				if ( 'completed' == $item->get_status() )
				{
					// status
					_e( 'Completed', 'dce' );
				}
				elseif ( 'denied' == $item->get_status() )
				{
					// status
					_e( 'Denied', 'dce' );
				}
				else
				{
					// Close
					echo '<a href="#" class="button button-delete" data-escrow="', $post_id ,'" data-action="dce_close_escrow">', __( 'Close', 'dce' ) ,'</a>';
				}
			}
			else
			{
				if ( 'confirmed' == $item->get_status() )
				{
					// status
					_e( 'Confirmed', 'dce' );
				}
				elseif ( 'denied' == $item->get_status() )
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
			}
			echo '</div>';
			break;
	}
}

















