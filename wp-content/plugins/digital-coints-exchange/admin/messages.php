<?php
/**
 * WP-Admin: Messages
 * 
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

add_action( 'admin_menu', 'dce_admin_add_messages_page' );
/**
 * Add Users messages page
 *
 * @since Digital Coins Exchanging Store 1.0
*/
function dce_admin_add_messages_page()
{
	global $dce_admin_pages_slugs;

	// add messages page
	$dce_admin_pages_slugs[] = add_menu_page( __( 'Messages', 'dce' ), __( 'Users Messages', 'dce' ), 'manage_options', 'dce_admin_messages', 'dce_admin_messages_page_ui', 'dashicons-email-alt', 28 );
}

/**
 * WP-Admin: Users' Messages 
 */
function dce_admin_messages_page_ui()
{
	// query users messages
	$users_messages = DCE_User::query_messages( array ( 
			'object_id' => '', 
			'target' => 'both', 
	) );

	// date & time format
	$date_format = get_option( 'date_format' );
	$time_format = get_option( 'time_format' );

	// edit url
	$edit_url = self_admin_url( 'user-edit.php' );

	?>
	<div class="wrap" dir="ltr">
		<h2><?php _e( 'Users Messages', 'dce' ); ?></h2>

		<table id="messages" class="widefat fixed comments" cellspacing="0">
			<thead>
				<tr>
					<th scope="col" class="column-author"><?php _e( 'From', 'dce' ); ?></th>
					<th scope="col" class="column-author"><?php _e( 'To', 'dce' ); ?></th>
					<th scope="col"><?php _e( 'Message', 'dce' ); ?></th>
					<th scope="col" class="column-response"><?php _e( 'In Response To', 'dce' ); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th scope="col" class="column-author"><?php _e( 'From', 'dce' ); ?></th>
					<th scope="col" class="column-author"><?php _e( 'To', 'dce' ); ?></th>
					<th scope="col"><?php _e( 'Message', 'dce' ); ?></th>
					<th scope="col" class="column-response"><?php _e( 'In Response To', 'dce' ); ?></th>
				</tr>
			</tfoot>
			<tbody>
				<?php 
				$len = count( $users_messages );
				for ( $i = 0; $i < $len; $i++ )
				{
					$message =& $users_messages[$i];
					$message['date_time'] = strtotime( $message['date_time'] );
					?>
				<tr class="comment <?php echo $i % 2 ? 'alt' : ''; ?>">
					<td class="author"><a href="<?php echo add_query_arg( 'user_id', $message['from']->ID, $edit_url ); ?>"><?php echo $message['from']->display_name(); ?></a></td>
					<td class="author"><a href="<?php echo add_query_arg( 'user_id', $message['to']->ID, $edit_url ); ?>"><?php echo $message['to']->display_name(); ?></a></td>
					<td class="comment column-comment">
						<div class="submitted-on"><?php printf( __( 'Sent on <strong>%s</strong> at <strong>%s</strong>', 'dce' ), date( $date_format, $message['date_time'] ), date( $time_format, $message['date_time'] ) ); ?></div>
						<p><?php echo $message['message']; ?></p>
					</td>
					<td class="response column-response">
						<div class="response-links">
							<a target="_blank" href="<?php echo get_permalink( $message['object_id'] ); ?>"><?php echo 'offer' == $message['type'] ? __( 'Offer', 'dce' ) : __( 'Escrow', 'dce' ); ?></a>
						</div>
					</td>
				</tr>
					<?php
				}
				?>
			</tbody>
		</table>
	</div>
	<?php
}

add_filter( 'comments_clauses', 'dce_comments_clauses_filter' );
/**
 * Comments query filter to hide messages from global
 *
 * @param array $clauses
 * @return array
*/
function dce_comments_clauses_filter( $clauses )
{
	// check if in the right page
	if ( !is_admin() || 'edit-comments' != get_current_screen()->id )
		return $clauses;

	// check where conditions
	if ( !empty( $clauses['where'] ) )
		$clauses['where'] .= ' AND';

	// exclude messages
	$clauses['where'] .= " ( comment_type NOT IN ( 'offer', 'escrow' ) )";

	// return filtered data
	return $clauses;
}
