<?php
/**
 * Setups
 * 
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

add_action( 'admin_init', 'dce_admin_init' );
/*
 * WP-Admin initialization
 */
function dce_admin_init()
{
	// action actions ( setups )
	if ( current_user_can( 'manage_options' ) && isset( $_GET['dce_setup'] ) )
	{
		switch ( $_GET['dce_setup'] )
		{
			// setup pages
			case 'pages':
				// page attrs
				$page_attrs = array ( 
						'post_status' => 'publish',  
						'post_type' => 'page',
						'$comment_status' => 'closed',
				);

				$pages = dce_pages();
				foreach ( $pages as $page_name => &$page_info )
				{
					// check page existence 
					$page_id = dce_get_page_by_slug( $page_name, 'id' );
					if ( !$page_id )
					{
						// fill the attrs
						$page_attrs['post_name'] = $page_name;
						$page_attrs['post_title'] = $page_info['title'];
						$page_attrs['post_content'] = $page_info['content'];

						// insert the page
						$page_id = wp_insert_post( $page_attrs );
						if ( $page_id )
						{
							$page_info['id'] = $page_id;
							$page_info['url'] = get_permalink( $page_id );
						}
					}
				}

				// save changes
				update_option( 'dce_pages', $pages );

				break;
		}
	}
}

add_action( 'admin_notices', 'dce_admin_notices' );
/**
 * WP-Admin Notification messages
 */
function dce_admin_notices()
{
	// check pages
	$login_page = dce_pages( 'login' );
	if ( !$login_page['url'] )
		echo '<div class="error"><p>', sprintf( __( 'In order to <strong>Digital Coins Exchanging Store</strong> plugin to work it needs to setup the needed pages, <a class="button button-primary" href="%s">Click here to run setup</a>', 'dce' ), add_query_arg( 'dce_setup', 'pages' ) ) ,'</p></div>';
}

function dce_pages( $page_name = '' )
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

register_activation_hook( DCE_PLUGIN_FILE, 'dec_plugin_activation' );
/**
 * Plugin Activation Hook
 */
function dec_plugin_activation()
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




































