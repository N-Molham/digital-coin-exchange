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
	wp_register_style( 'dce-shared-style', DCE_URL .'css/shared.css' );
	wp_register_style( 'dce-public-style', DCE_URL .'css/public.css' );
	wp_enqueue_style( 'dce-shared-style' );

	// js
	wp_register_script( 'dce-shared-script', DCE_URL .'js/shared.js', array( 'jquery' ), false, true );
	// localized data
	wp_localize_script( 'dce-shared-script', 'dce', array (
			'ajax_url' => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
	) );

	// restrict access to wp register form
	if ( strpos( $_SERVER['REQUEST_URI'], 'wp-login.php' ) !== false && isset( $_REQUEST['action'] ) && 'register' == $_REQUEST['action'] )
		dce_redirect( home_url() );

	// register post status
	register_post_status( 'denied', array (
			'label' => _x( 'Denied', 'post', 'dce' ),
			'public' => true,
			'internal' => true,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'label_count' => _n_noop( 'Denied <span class="count">(%s)</span>', 'Denied <span class="count">(%s)</span>', 'dce' ),
	) );

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
					'menu_name' => _x( 'Offers', 'dce_offer', 'dce' ),
			),
			'hierarchical' => false,
			'description' => __( 'Clients coins exchange offer', 'dce' ),
			'supports' => array( 'author' ),
			'show_in_menu' => true,
			'public' => true,
			'show_ui' => true,
			'query_var' => 'offer',
			'rewrite' => array (
					'slug' => 'offer',
					'with_front' => false,
			),
			'can_export' => true,
	);
	register_post_type( DCE_POST_TYPE_OFFER, $args );

	// escrows
	$args = array (
			'labels' => array (
					'name' => _x( 'Escrows', 'dce_offer', 'dce' ),
					'singular_name' => _x( 'Escrow', 'dce_offer', 'dce' ),
					'add_new' => _x( 'Add New Escrow', 'dce_offer', 'dce' ),
					'add_new_item' => _x( 'Add New Escrow', 'dce_offer', 'dce' ),
					'edit_item' => _x( 'Edit Escrow', 'dce_offer', 'dce' ),
					'new_item' => _x( 'New Escrow', 'dce_offer', 'dce' ),
					'view_item' => _x( 'View Escrow', 'dce_offer', 'dce' ),
					'search_items' => _x( 'Search Escrows', 'dce_offer', 'dce' ),
					'not_found' => _x( 'No Escrows found', 'dce_offer', 'dce' ),
					'not_found_in_trash' => _x( 'No Escrows found in Trash', 'dce_offer', 'dce' ),
					'menu_name' => _x( 'Escrows', 'dce_offer', 'dce' ),
			),
			'hierarchical' => false,
			'description' => __( 'Clients escrows', 'dce' ),
			'supports' => array( 'author' ),
			'show_in_menu' => true,
			'public' => true,
			'show_ui' => true,
			'query_var' => 'escrow',
			'rewrite' => array (
					'slug' => 'escrow',
					'with_front' => false,
			),
			'can_export' => true,
	);
	register_post_type( DCE_POST_TYPE_ESCROW, $args );
}

add_action( 'template_redirect', 'dce_public_template_redirect' );
/**
 * Frontend templates filter
 */
function dce_public_template_redirect()
{
	// enqueues
	wp_enqueue_style( 'dce-public-style' );
}

/**
 * Registered digital coin types
 * 
 * @param string $type ( Optional )
 * @return array|object|boolean
 */
function dce_get_coin_types( $type = null )
{
	// list of coin types
	$coin_types = array ( 
			'bitcoin' => array ( 
					'label' => __( 'Bitcoin', 'dce' ),
					'single' => '%d bitcoin',
					'plural' => '%d bitcoins',
					'command' => 'bitcoind',
			),
			'litecoin' => array ( 
					'label' => __( 'Litecoin', 'dce' ),
					'single' => '%d litecoin',
					'plural' => '%d litecoins',
					'command' => 'litecoind',
			),
			'dogecoin' => array ( 
					'label' => __( 'Dogecoin', 'dce' ),
					'single' => '%d dogecoin',
					'plural' => '%d dogecoins',
					'command' => 'dogecoind',
			),
	);

	// get specific coin type
	if ( $type )
		return isset( $coin_types[$type] ) ? apply_filters( 'dce_coin_type', (object) $coin_types[$type], $type ) : false;

	// return all coin types
	return apply_filters( 'dce_coin_types', $coin_types );
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


































