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
	 * rewrite rules
	 */ 

	// public profile
	$page_id = dce_get_pages( 'profile' )->id;
	add_rewrite_rule( 'user/([^/]+)/?$', 'index.php?page_id='. $page_id .'&user_id=$matches[1]', 'top' );

	// feedback form
	$page_id = dce_get_pages( 'feedback' )->id;
	add_rewrite_rule( 'feedback/([^/]+)/?$', 'index.php?page_id='. $page_id .'&feedback_escrow=$matches[1]', 'top' );

	/**
	 * Register Styles
	 */
	// Shared styles
	wp_register_style( 'dce-shared-style', DCE_URL .'css/shared.css' );
	wp_enqueue_style( 'dce-shared-style' );

	// Front-end style
	wp_register_style( 'dce-public-style', DCE_URL .'css/public.css' );

	// rateit plug-in style
	wp_register_style( 'dce-rateit-style', DCE_URL .'css/rateit.css' );

	// wizard steps plug-in style
	wp_register_style( 'dce-jquery-wizard-style', DCE_URL .'css/jquery.steps.css' );

	/**
	 * Register Scripts
	 */
	// shared js
	wp_register_script( 'dce-shared-script', DCE_URL .'js/shared.js', array( 'jquery' ), false, true );

	// jQuery Wizard steps plug-in
	wp_register_script( 'dce-jquery-wizard-script', DCE_URL .'js/jquery.steps.min.js', array( 'jquery' ), false, true );

	// jQuery rateit plug-in
	wp_register_script( 'dce-rateit-script', DCE_URL .'js/jquery.rateit.min.js', array( 'dce-shared-script' ), false, true );

	// escrow page
	wp_register_script( 'dce-escrows', DCE_URL .'js/escrows.js', array( 'dce-shared-script', 'dce-jquery-wizard-script' ), false, true );

	// send message
	wp_register_script( 'dce-messages', DCE_URL .'js/messages.js', array( 'dce-shared-script' ), false, true );

	// global localized data
	wp_localize_script( 'dce-shared-script', 'dce', array (
			'ajax_url' => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
			'login_first' => is_user_logged_in() ? false : dce_alert_message( __( 'Please, login ot register first', 'dce' ), 'error', true ),
	) );

	// escrow localized data
	wp_localize_script( 'dce-shared-script', 'dce_escrow', array (
			'wizard_next_label' => __( 'Next', 'dce' ),
			'wizard_previous_label' => __( 'Previous', 'dce' ),
			'wizard_finish_label' => __( 'Start Escrow', 'dce' ),
	) );

	// restrict access to wp register form
	if ( strpos( $_SERVER['REQUEST_URI'], 'wp-login.php' ) !== false && isset( $_REQUEST['action'] ) && 'register' == $_REQUEST['action'] )
		dce_redirect( home_url() );

	/**
	 * register post status
	 */ 
	// Denied
	register_post_status( 'denied', array (
			'label' => _x( 'Denied', 'post', 'dce' ),
			'public' => true,
			'internal' => true,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'label_count' => _n_noop( 'Denied <span class="count">(%s)</span>', 'Denied <span class="count">(%s)</span>', 'dce' ),
	) );

	// Completed
	register_post_status( 'completed', array (
			'label' => _x( 'Completed', DCE_POST_TYPE_ESCROW, 'dce' ),
			'public' => true,
			'internal' => true,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'label_count' => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'dce' ),
	) );

	// Failed
	register_post_status( 'failed', array (
			'label' => _x( 'Failed', DCE_POST_TYPE_ESCROW, 'dce' ),
			'public' => true,
			'internal' => true,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'label_count' => _n_noop( 'Failed <span class="count">(%s)</span>', 'Failed <span class="count">(%s)</span>', 'dce' ),
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
			'menu_icon' => 'dashicons-info',
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
			'menu_icon' => 'dashicons-groups',
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

add_filter( 'query_vars', 'dce_query_vars_filter' );
/**
 * Query Variables filter
 *
 * @param array $query_vars
 * @return array
 */
function dce_query_vars_filter( $query_vars )
{
	$query_vars[] = 'user_id';
	$query_vars[] = 'feedback_escrow';

	return $query_vars;
}

/**
 * Set sending mails content type to HTML
 * 
 * @return string
 */
function dce_set_mail_html_content_type()
{
	return 'text/html';
}

add_action( 'template_redirect', 'dce_public_template_redirect' );
/**
 * Frontend templates filter
 */
function dce_public_template_redirect()
{
	// enqueues
	wp_enqueue_style( 'dce-jquery-wizard-style' );
	wp_enqueue_style( 'dce-rateit-style' );
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
	$coin_types = dce_admin_get_settings( 'coin_types' );

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
			'feedback' => array ( 
					'title' => __( 'Your Feedback', 'dce' ),
					'content' => '[dce-user-feedback]',
					'id' => 0,
					'url' => false,
			),
			'trans-history' => array ( 
					'title' => __( 'Transactions History', 'dce' ),
					'content' => '[dce-user-trans-history]',
					'id' => 0,
					'url' => false,
			),
	);

	$pages = wp_parse_args( $pages, $default_pages );

	// return specific page
	if ( '' != $page_name )
	{
		// if not found
		if ( !isset( $pages[$page_name] ) )
			return false;

		// check permalink
		if ( strpos( $pages[$page_name]['url'], 'page_id=' ) !== false )
			$pages[$page_name]['url'] = get_permalink( $pages[$page_name]['id'] );

		// return page data
		return (object) $pages[$page_name];
	}

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
	global $wpdb;

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

	// create old schedule 
	wp_clear_scheduled_hook( 'dce_cron_interval' );

	// cron jobs register
	wp_schedule_event( time(), 'dce_15_min', 'dce_cron_interval' );

	// db tables
	$sql = "CREATE TABLE {$wpdb->prefix}transactions (
  trans_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  user_id bigint(20) unsigned NOT NULL,
  escrow_id bigint(20) unsigned NOT NULL,
  trans_action varchar(24) CHARACTER SET utf8 NOT NULL,
  trans_data text CHARACTER SET utf8 NOT NULL,
  trans_txid varchar(128) CHARACTER SET utf8 NOT NULL DEFAULT '',
  trans_datetime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY  (trans_id),
  KEY trans_user_key (user_id),
  KEY trans_escrow_key (escrow_id)
);";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );

	// rewrite flush for custom post types
	flush_rewrite_rules();
}

add_filter( 'cron_schedules', 'dce_cron_schedules' );
/**
 * Cron jobs schedule timing
 *
 * @param array $schedules
 * @return array
 */
function dce_cron_schedules( $schedules )
{
	$schedules['dce_15_min'] = array ( 
			'interval' => MINUTE_IN_SECONDS * 15,
			'display' => __( 'Every 15 Minutes', 'dce' ), 
	);

	return $schedules;
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


































