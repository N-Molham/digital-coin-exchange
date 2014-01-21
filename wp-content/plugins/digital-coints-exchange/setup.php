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
	wp_register_style( 'dce-shared-style', DCE_URL .'/css/shared.css' );

	// restrict access to wp register form
	if ( strpos( $_SERVER['REQUEST_URI'], 'wp-login.php' ) !== false && isset( $_REQUEST['action'] ) && 'register' == $_REQUEST['action'] )
		dce_redirect( home_url() );
}

function dce_get_pages( $page_name = '' )
{
	// get pages from db
	$pages = get_option( 'dce_pages', 'none' );

	if ( 'none' == $pages )
	{
		// all needed pages
		$pages = array (
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
		);
	}

	// return specific page
	if ( '' != $page_name )
		return isset( $pages[$page_name] ) ? $pages[$page_name] : false;

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
}




































