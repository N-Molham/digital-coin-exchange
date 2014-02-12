<?php
/**
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */
/*
 * Plugin Name: Digital Coins Exchanging Store
 * Description: Exchange Digital coins like bitcoin, dogecoin etc.
 * Version: 1.0
 * Text Domain: dce
*/

if( '' == session_id() )
	session_start();

/**
 * Plugin Base Constants
 */
define( 'DCE_PLUGIN_FILE', __FILE__ );
define( 'DCE_URL', plugin_dir_url( DCE_PLUGIN_FILE ) );
define( 'DCE_PATH', plugin_dir_path( DCE_PLUGIN_FILE ) );

/**
 * Logical Constants
 */
define( 'DCE_CLIENT_ROLE', 'dce_client' );
define( 'DCE_POST_TYPE_OFFER', 'dce_offer' );
define( 'DCE_POST_TYPE_ESCROW', 'dce_escrow' );

/**
 * Includes
 */
// utils and setups
require DCE_PATH . 'functions.php';
require DCE_PATH . 'setup.php';

// template tags for easy theming
require DCE_PATH . 'template_tags.php';

// classes
require DCE_PATH . 'classes/class-base.php';
require DCE_PATH . 'classes/class-offer.php';
require DCE_PATH . 'classes/class-escrow.php';

// ajax
require DCE_PATH . 'ajax/ajax.php';
require DCE_PATH . 'ajax/ajax-offers.php';
require DCE_PATH . 'ajax/ajax-escrows.php';

// Admin pages
require DCE_PATH . 'admin/admin_init.php';
require DCE_PATH . 'admin/components.php';
require DCE_PATH . 'admin/settings.php';

// Users pages
require DCE_PATH . 'users.php';
require DCE_PATH . 'users-shortcode.php';


add_action( 'plugins_loaded', 'dce_plugins_loaded' );
/**
 * Load language file
 */
function dce_plugins_loaded() 
{
	load_plugin_textdomain( 'dce', false, basename( dirname( DCE_PLUGIN_FILE ) ) . '/languages/' );
}
