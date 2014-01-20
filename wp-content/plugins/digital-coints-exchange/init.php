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

/**
 * Includes
 */
require DCE_PATH . 'functions.php';
require DCE_PATH . 'setup.php';

add_action( 'plugins_loaded', 'dce_plugins_loaded' );
/**
 * Load language file
 */
function dce_plugins_loaded() 
{
	load_plugin_textdomain( 'dce', false, basename( dirname( DCE_PLUGIN_FILE ) ) . '/languages/' );
}
