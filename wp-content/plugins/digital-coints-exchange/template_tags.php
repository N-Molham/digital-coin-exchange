<?php
/**
 * Template tags
 * 
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

/**
 * Section title layout
 * 
 * @param string $title
 * @return string
 */
function dce_section_title( $title )
{
	return '<div class="title"><h2>'. $title .'</h2><div class="title-sep-container"><div class="title-sep"></div></div></div>';
}

/**
 * Alert messages layout
 * 
 * @param string $message
 * @param string $type
 * @return string
 */
function dce_alert_message( $message, $type = 'general' )
{
	return '<div class="alert '. $type .'"><div class="msg">'. $message .'</div></div>';
}