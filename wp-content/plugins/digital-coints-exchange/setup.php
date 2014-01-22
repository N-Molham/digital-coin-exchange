<?php
/**
 * Setups
 * 
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

add_action( 'init', 'dce_setup_init' );
/**
 * WP Initialize
 */
function dce_setup_init()
{
	/**
	 * Register Styles & Scripts
	 */
	// styles
	wp_register_style( 'dce-shared-style', DCE_URL .'/css/shared.css' );

	// js
	wp_register_script( 'dce-shared-script', DCE_URL .'/js/shared.js', array( 'jquery' ), false, true );

	// restrict access to wp register form
	if ( strpos( $_SERVER['REQUEST_URI'], 'wp-login.php' ) !== false && isset( $_REQUEST['action'] ) && 'register' == $_REQUEST['action'] )
		dce_redirect( home_url() );

	/**
	 * Register post types
	 */

	// offers
	$args = array (
			'labels' => array (
					'name' => _x( 'Offers', 'dce_offer', 'dce' ),
					'singular_name' => _x( 'Offer', 'dce_offer', 'dce' ),
					'add_new' => _x( 'Add New Offer', 'dce_offer', 'dce' ),
					'add_new_item' => _x( 'Add New Offer', 'dce_offer', 'dce' ),
					'edit_item' => _x( 'Edit Offer', 'dce_offer', 'dce' ),
					'new_item' => _x( 'New Offer', 'dce_offer', 'dce' ),
					'view_item' => _x( 'View Offer', 'dce_offer', 'dce' ),
					'search_items' => _x( 'Search Offers', 'dce_offer', 'dce' ),
					'not_found' => _x( 'No offers found', 'dce_offer', 'dce' ),
					'not_found_in_trash' => _x( 'No offers found in Trash', 'dce_offer', 'dce' ),
					'parent_item_colon' => _x( 'Parent Offer:', 'dce_offer', 'dce' ),
					'menu_name' => _x( 'Offers', 'dce_offer', 'dce' ),
			),
			'hierarchical' => false,
			'description' => __( 'Clients coins exchange offer', 'dce' ),
			'supports' => array( 'author' ),
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => false,
			'publicly_queryable' => true,
			'exclude_from_search' => true,
			'has_archive' => false,
			'query_var' => 'offer',
			'can_export' => true,
			'rewrite' => true,
	);
	register_post_type( DCE_POST_TYPE_OFFER, $args );
}

add_filter( 'show_admin_bar', 'dce_admin_bar_visibility' );
/**
 * Hide Admin bar from clients
 * 
 * @param boolean $show
 * @return boolean
 */
function dce_admin_bar_visibility( $show )
{
	// if not admin hide bar
	if ( !dce_is_user_admin() )
		return false;

	return $show;
}

/**
 * Get all pages or specific page
 * 
 * @param string $page_name ( optional )
 * @return boolean|StdClass|array if $page_name is set and page found will return stdClass or boolean on failure otherwise an array of all pages
 */
function dce_get_pages( $page_name = '' )
{
	// get pages from db or cache
	$pages = isset( $GLOBALS['dce_pages'] ) ? $GLOBALS['dce_pages'] : get_option( 'dce_pages', array() );

	// all needed pages
	$default_pages = array (
			'home' => array ( 
					'title' => __( 'Home', 'dce' ),
					'content' => '',
					'id' => 0,
					'url' => false,
			),
			'How-it-works' => array ( 
					'title' => __( 'How it works?', 'dce' ),
					'content' => '[dce-contact-form]',
					'id' => 0,
					'url' => false,
			),
			'offers' => array ( 
					'title' => __( 'Offers', 'dce' ),
					'content' => '[dce-offers]',
					'id' => 0,
					'url' => false,
			),
			'register' => array ( 
					'title' => __( 'Register', 'dce' ),
					'content' => '[dce-register-form]',
					'id' => 0,
					'url' => false,
			),
			'login' => array ( 
					'title' => __( 'login', 'dce' ),
					'content' => '[dce-login-form]',
					'id' => 0,
					'url' => false,
			),
			'quick-tour' => array ( 
					'title' => __( 'Quick Tour', 'dce' ),
					'content' => '',
					'id' => 0,
					'url' => false,
			),
			'about' => array ( 
					'title' => __( 'About', 'dce' ),
					'content' => '',
					'id' => 0,
					'url' => false,
			),
			'contact-us' => array ( 
					'title' => __( 'Contact Us', 'dce' ),
					'content' => '[dce-contact-form]',
					'id' => 0,
					'url' => false,
			),
			'dashboard' => array ( 
					'title' => __( 'Dashboard', 'dce' ),
					'content' => '[dce-user-dashboard]',
					'id' => 0,
					'url' => false,
			),
			'escrow-manager' => array ( 
					'title' => __( 'Escrow Manager', 'dce' ),
					'content' => '[dce-escrow-manager]',
					'id' => 0,
					'url' => false,
			),
			'user-offers' => array ( 
					'title' => __( 'Offers', 'dce' ),
					'content' => '[dce-user-offers]',
					'id' => 0,
					'url' => false,
			),
			'profile' => array ( 
					'title' => __( 'Profile', 'dce' ),
					'content' => '[dce-user-profile]',
					'id' => 0,
					'url' => false,
			),
			'messages' => array ( 
					'title' => __( 'Messages', 'dce' ),
					'content' => '[dce-user-messages]',
					'id' => 0,
					'url' => false,
			),
	);

	$pages = wp_parse_args( $pages, $default_pages );

	// return specific page
	if ( '' != $page_name )
		return isset( $pages[$page_name] ) ? (object) $pages[$page_name] : false;

	// cache in global
	if ( !isset( $GLOBALS['dce_pages'] ) )
		$GLOBALS['dce_pages'] = $pages;

	return $pages;
}

register_activation_hook( DCE_PLUGIN_FILE, 'dce_plugin_activation' );
/**
 * Plugin Activation Hook
 */
function dce_plugin_activation()
{
	// register client role
	$client_role = get_role( DCE_CLIENT_ROLE );
	if ( !$client_role )
	{
		// role doesn't exist, get base role caps
		$caps = get_role( 'subscriber' )->capabilities;

		// register new role
		add_role( DCE_CLIENT_ROLE, __( 'Exchange Client', 'dce' ), apply_filters( 'dce_client_role_caps', $caps ) );
	}

	// update registration options
	update_option( 'users_can_register', 1 );
	update_option( 'default_role', DCE_CLIENT_ROLE );

	// rewrite flush for custom post types
	flush_rewrite_rules();
}




































