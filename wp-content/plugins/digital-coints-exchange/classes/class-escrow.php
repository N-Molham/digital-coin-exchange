<?php
/**
 * Escrow Class
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

add_action( 'template_redirect', 'dce_escrow_view_check' );
/**
 * Check if user who sees this escrow is allowed to
 */
function dce_escrow_view_check()
{
	global $wp_query, $data;

	if ( !is_singular( DCE_POST_TYPE_ESCROW ) )
		return;

	// Avada theme
	if ( !empty( $data ) )
	{
		// full width layout
		$data['single_post_full_width'] = true;

		// hide post navigation
		$data['blog_pn_nav'] = true;

		// hide sharing box
		$data['social_sharing_box'] = false;

		// hide comments
		$data['blog_comments'] = false;

		// hide author
		$data['author_info'] = false;

		// hide post meta
		$data['post_meta'] = false;
	}

	// target escrow
	$escrow = new DCE_Escrow( get_post() ); 

	// check login
	$user = DCE_User::get_current_user();
	if ( !$user->exists() || !in_array( $user->data->user_email, array( $escrow->target_email, $escrow->user->user_email ) ) )
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

add_filter( 'the_content', 'dce_escrow_public_content_handler' );
/**
 * Handle escrow post view/content
 * 
 * @param string $template
 * @return string
 */
function dce_escrow_public_content_handler( $content )
{
	if ( is_singular( DCE_POST_TYPE_ESCROW ) )
		return '[dce-single-escrow]';

	return $content;
}

add_filter( 'the_title', 'dce_escrow_public_title_handler', 10, 2 );
/**
 * Handle escrow post title
 * 
 * @param string $title
 * @param int $post_id
 * @return string
 */
function dce_escrow_public_title_handler( $title, $post_id )
{
	// check single post type
	if ( DCE_POST_TYPE_ESCROW == get_post_type( $post_id ) )
		return __( 'Escrow Details', 'dce' );

	return $title;
}

add_action( 'dce_save_user_escrow', 'dce_new_escrow_mail_notification' );
/**
 * Notify escrow related users
 * 
 * @param DCE_Escrow $escrow
 */
function dce_new_escrow_mail_notification( $escrow )
{
	// body message
	$message = 'New escrow started, open link below for details'. "\n\r";
	$message .= add_query_arg( 'ref', 'mail', $escrow->url() );

	// sent to parties
	wp_mail( array( $escrow->user->data->user_email, $escrow->target_email ), __( 'New Escrow Started', 'dce' ), $message );
}

/**
 * Escrow Class
 */
class DCE_Escrow extends DCE_Offer
{
	/**
	 * Post type
	 *
	 * @var string
	 */
	static $post_type = DCE_POST_TYPE_ESCROW;

	/**
	 * Targeted user to deal with
	 * 
	 * @var string
	 */
	var $target_email;

	/**
	 * Escrow owner receive address
	 * 
	 * @var string
	 */
	var $owner_address;

	/**
	 * Escrow target receive address
	 * 
	 * @var string
	 */
	var $target_address;

	/**
	 * Constructor ( override )
	 *
	 * @param number|WP_Post|object $post_id
	 */
	public function __construct( $post_id )
	{
		parent::__construct( $post_id );

		// check existence
		if ( !$this->exists() )
			return false;

		// additional fields
		$this->target_email = $this->post_object->target_email;

		// receive addresses
		$this->owner_address = $this->post_object->owner_address;
		$this->target_address = $this->post_object->target_address;
	}

	/**
	 * Escrow Status
	 *
	 * @return string
	 */
	public function get_status()
	{
		return 'publish' == $this->status ? 'open' : $this->status;
	}

	/**
	 * Insert/Update user escrow
	 *
	 * @param int $user_id
	 * @param int $from_amount
	 * @param string $from_coin
	 * @param int $to_amount
	 * @param string $to_coin
	 * @param array $escrow_args
	 *
	 * @return DCE_Escrow|WP_Error
	 */
	static public function save_escrow( $user_id, $from_amount, $from_coin, $to_amount, $to_coin, $escrow_args = '' )
	{
		$escrow_args = wp_parse_args( $escrow_args, array (
				'target_email' => '',
				'details' => '',
				'comm_method' => '',
				'id' => '',
		) );

		// post args
		$post_args = array (
				'ID' => is_numeric( $escrow_args['id'] ) ? $escrow_args['id'] : '',
				'post_status' => 'publish',
				'post_type' => self::$post_type,
				'post_author' => $user_id,
				'post_content' => $escrow_args['details'],
		);

		// save post
		$escrow_id = wp_insert_post( $post_args, true );
		if ( is_wp_error( $escrow_id ) )
			return $escrow_id;

		// save escrow data/meta
		update_post_meta( $escrow_id, 'to_amount', $to_amount );
		update_post_meta( $escrow_id, 'to_coin', $to_coin );
		update_post_meta( $escrow_id, 'from_amount', $from_amount );
		update_post_meta( $escrow_id, 'from_coin', $from_coin );
		update_post_meta( $escrow_id, 'comm_method', $escrow_args['comm_method'] );
		update_post_meta( $escrow_id, 'target_email', $escrow_args['target_email'] );

		// receive addresses
		update_post_meta( $escrow_id, 'owner_address', DCE_Escrow::generate_address() );
		update_post_meta( $escrow_id, 'target_address', DCE_Escrow::generate_address() );

		$escrow = new DCE_Escrow( $escrow_id );

		// wp action
		do_action( 'dce_save_user_escrow', $escrow );

		return $escrow;
	}

	/**
	 * Query users' escrows
	 *
	 * @param array $args
	 * @return mixed
	 */
	public static function query_escrows( $args = '' )
	{
		global $wpdb;
	
		// default args
		$args = wp_parse_args( $args, array (
				'ID' => '',
				'post_type' => self::$post_type,
				'author' => '',
				'nopaging' => true,
				'post_status' => array( 'publish', 'pending' ),
		) );

		// query escrow
		$single = !empty( $args['ID'] );
		if ( $single )
		{
			// single escrow
			$escrows = array( get_post( $args['ID'] ) );
		}
		else
		{
			// all escrows
			$escrows = get_posts( $args );
		}

		// class wrap
		$escrows = array_map( function ( $escow ) {
			return new DCE_Escrow( $escow );
		}, $escrows );

		return apply_filters( 'dce_query_escrows', $single ? $escrows[0] : $escrows );
	}

	/**
	 * Form data fields
	 * 
	 * @param array $coin_types
	 * @return array
	 */
	public static function form_fields( &$coin_types = '' )
	{
		if ( empty( $coin_types ) )
			$coin_types = dce_get_coin_types();

		// original fields
		$fields = parent::form_fields( $coin_types );

		// change details label
		$fields['details']['label'] = __( 'Terms & Agreements', 'dce' );

		// new fields
		$fields['target_email'] = array ( 
				'label' => __( 'Target User Email', 'dce' ), 
				'input' => 'text',
				'data_type' => 'email',
				'required' => true,
		);

		return $fields;
	}

	/**
	 * Generate receive address
	 * 
	 * @return string
	 */
	public static function generate_address()
	{
		return wp_generate_password( 64, false );
	}
}





















