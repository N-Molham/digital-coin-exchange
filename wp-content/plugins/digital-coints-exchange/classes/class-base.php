<?php
/**
 * Post Objects Base Class
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

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
	var $status;

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
		$new_status = 'confirm' == $new_status ? 'publish' : 'denied';

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
}

