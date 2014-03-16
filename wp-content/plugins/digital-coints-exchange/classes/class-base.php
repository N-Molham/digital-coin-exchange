<?php
/**
 * Post Objects Base Class
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

add_action( 'admin_bar_menu', 'dce_admin_bar_menu', 120 );
/**
 * WP-Admin bar menu
 */
function dce_admin_bar_menu()
{
	/* @var $wp_admin_bar WP_Admin_Bar */
	global $wp_admin_bar;

	// remove unwanted links
	$wp_admin_bar->remove_menu( 'edit' );
	$wp_admin_bar->remove_menu( 'new-'. DCE_POST_TYPE_OFFER );
	$wp_admin_bar->remove_menu( 'new-'. DCE_POST_TYPE_ESCROW );
}

add_action( 'template_redirect', 'dce_single_view_check' );
/**
 * Check if user who sees this escrow/offer is allowed to
*/
function dce_single_view_check()
{
	global $wp_query;

	// if the target post types
	$post_type = get_post_type();
	if ( !in_array( $post_type, array( DCE_POST_TYPE_ESCROW, DCE_POST_TYPE_OFFER ) ) )
		return;

	// specific post type
	if ( DCE_POST_TYPE_ESCROW == $post_type )
	{
		// target escrow
		$escrow = new DCE_Escrow( get_post() );

		// get current user
		$user = DCE_User::get_current_user();

		// check if admin
		if ( dce_is_user_admin( $user ) )
			return ;

		// check login
		if ( !$user->exists() || !$escrow->check_user( $user->data->user_email ) )
		{
			// clicked from mail
			if ( 'mail' != dce_get_value( 'ref' ) )
			{
				// load 404
				$wp_query->set_404();
				status_header( 404 );
			}
		}
	}
}

add_filter( 'the_content', 'dce_single_public_content_handler' );
/**
 * Handle escrow/offer post view/content
 *
 * @param string $template
 * @return string
*/
function dce_single_public_content_handler( $content )
{
	// escrow single
	if ( is_singular( DCE_POST_TYPE_ESCROW ) )
		return '[dce-single-escrow]';

	// offer single
	if ( is_singular( DCE_POST_TYPE_OFFER ) )
		return '[dce-single-offer]';

	return $content;
}

add_filter( 'the_title', 'dce_single_public_title_handler', 10, 2 );
/**
 * Handle escrow/offer post title
 *
 * @param string $title
 * @param int $post_id
 * @return string
*/
function dce_single_public_title_handler( $title, $post_id )
{
	$post_type = get_post_type( $post_id );

	// escrow title
	if ( DCE_POST_TYPE_ESCROW == $post_type )
		return __( 'Escrow Details', 'dce' );

	// offer title
	if ( DCE_POST_TYPE_OFFER == $post_type )
		return __( 'Offer Details', 'dce' );

	return $title;
}

/**
 * Base Class
 */
class DCE_Component
{

	/**
	 * Post type
	 *
	 * @var string
	 */
	static $post_type = '';

	/**
	* Component ID
	*
	* @var int
	*/
	var $ID;

	/**
	 * WP_Post object retrieved
	 *
	 * @var WP_Post
	 */
	var $post_object;

	/**
	 * Date & time
	 *
	 * @var string
	 */
	var $datetime;

	/**
	 * Component status
	 *
	 * @var string
	 */
	protected $status;

	/**
	 * WP_Post object properties
	 *
	 * @var array
	 */
	protected $post_vars;

	/**
	 * Constructor
	 *
	 * @param number|WP_Post|object $post_id
	 */
	public function __construct( $post_id )
	{
		// check if construce with id or object
		if ( is_object( $post_id ) && isset( $post_id->ID ) )
		{
			// post object
			if( !is_a( $post_id, 'WP_Post' ) )
				$post_id = new WP_Post($post_id);

			$this->post_object = $post_id;
			$this->ID = $post_id->ID;
		}
		else
		{
			// post id
			$this->ID = $post_id;
			$this->post_object = get_post( $this->ID );
		}

		// check existence
		if ( !$this->post_object )
			return false;

		// raw data filter
		$this->post_object->filter = 'raw';

		// post object vars
		$this->post_vars = get_object_vars( $this->post_object );

		// other properties
		$this->datetime = $this->post_date;
		$this->status = $this->post_status;
	}

	/**
	 * Change/update item status
	 * 
	 * @param string $new_status
	 * @return boolean|WP_Error
	 */
	public function change_status( $new_status )
	{
		// parse status
		switch ( $new_status )
		{
			case 'confirm':
				$new_status = 'publish';
				break;
			case 'deny':
				$new_status = 'denied';
				break;
		}

		// update status
		$update = wp_update_post( array( 'ID' => $this->ID, 'post_status' => $new_status ), true );
		if ( is_wp_error( $update ) )
			return $update;

		// set new status
		$this->status = $new_status;
		return true;
	}

	/**
	 * Delete/cancel component item
	 * 
	 * @param boolean $force_delete
	 * @return boolean
	 */
	public function delete( $force_delete = true )
	{
		return wp_delete_post( $this->ID, $force_delete );
	}

	/**
	 * Get Object Permalink URL
	 *
	 * @return string
	 */
	public function url()
	{
		return get_permalink( $this->ID );
	}

	/**
	 * Check if object exists in db or not
	 *
	 * @return boolean
	 */
	public function exists()
	{
		return $this->post_object && static::$post_type == $this->post_object->post_type;
	}

	/**
	 * Set component meta
	 * 
	 * @param string $meta_key
	 * @param mixed $meta_value
	 */
	public function set_meta( $meta_key, $meta_value )
	{
		update_post_meta( $this->ID, $meta_key, $meta_value );
	}

	/**
	 * Get component meta
	 * 
	 * @param string $meta_key
	 * @param boolean $single
	 * @return mixed
	 */
	public function get_meta( $meta_key, $single = true )
	{
		return get_post_meta( $this->ID, $meta_key, $single );
	}

	/**
	 * Magic Isset method for meta values or post object
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function __isset( $key )
	{
		// check in meta value
		return isset( $this->post_object->$key );
	}

	/**
	 * Magic Get Method to get meta values if doesn't exist in post object
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function __get( $key )
	{
		// get it from the post object
		return $this->post_object->$key;
	}

	/**
	 * Display coins amount formated
	 * 
	 * @param float $amount
	 * @param string $type
	 * @param array $coin_types
	 * @return string
	 */
	public static function display_amount_formated( $amount, $type, &$coin_types = '' )
	{
		if ( empty( $coin_types ) )
			$coin_types = dce_get_coin_types();

		return _n( sprintf( $coin_types[$type]['single'], $amount ), sprintf( $coin_types[$type]['plural'], $amount ), $amount );
	}
}

