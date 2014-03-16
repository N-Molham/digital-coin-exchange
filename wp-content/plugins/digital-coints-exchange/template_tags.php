<?php
/**
 * Template tags
 * 
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

/**
 * Image Lightbox 
 * 
 * @param string $thum_url
 * @param string $full_size_url
 * @param string $lightbox_title
 * @param string $link_title
 * @param string $rel
 * @return string
 */
function dce_image_lightbox( $thum_url, $full_size_url, $lightbox_title = '', $link_title = '', $rel = 'prettyPhoto' )
{
	return '<a title="'. $link_title .'" href="'. $full_size_url .'" rel="'. $rel .'"><img alt="'. $lightbox_title .'" src="'. $thum_url .'" /></a>';
}

/**
 * Promotion Box
 * 
 * @param string $content
 * @return string
 */
function dce_promotion_box( $content )
{
	return '<div class="reading-box-container clearfix"><section class="reading-box tagline-shadow"><h2>'. $content .'</h2></section></div>';
}

/**
 * Divider
 *  
 * @param string $type ( shadow | double | single | dashed | dotted )
 * @param string $tag
 * @return string
 */
function dce_divider( $type = 'shadow', $tag = 'div' )
{
	return '<'. $tag .' class="divider sep-'. $type .'"></'. $tag .'>';
}

/**
 * Button element
 * 
 * @param array $args
 * @return string
 */
function dce_button( $args )
{
	// defaults
	$args = wp_parse_args( $args, array ( 
			'tag' => 'a', 
			'content' => '', 
			'class' => array(), 
	) );

	// button class
	$args['class'] = array_merge( array( 'button' ), $args['class'] );

	// element tag
	$tag = $args['tag'];
	unset( $args['tag'] );

	// element content
	$content = $args['content'];
	unset( $args['content'] );

	// attributes
	$attrs = '';
	foreach ( $args as $attr_name => $attr_value )
	{
		$attrs .= $attr_name .'="';

		if ( is_array( $attr_value ) )
			$attrs .= implode( ' ', $attr_value );
		else
			$attrs .= $attr_value;

		$attrs .= '" ';
	}

	// tag
	if ( 'input' == $tag )
		return '<input '. $attrs .'/>';

	return '<'. $tag .' '. $attrs .'>'. $content .'</'. $tag .'>';
}

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
 * Table Start
 * 
 * @param string $id
 * @return string
 */
function dce_table_start( $id = '', $class = 'table-1' )
{
	return '<div'. ( '' == $id ? '' : ' id='. $id ) .' class="'. $class .'"><table width="100%">';
}

/**
 * Table end
 * 
 * @return string
 */
function dce_table_end()
{
	return '</table></div>'. dce_divider();
}

/**
 * Alert messages layout
 * 
 * @param string $message ( general | success | error | notice )
 * @param string $type
 * @param boolean $close_link
 * @return string
 */
function dce_alert_message( $message, $type = 'general', $close_link = false )
{
	return '<div class="alert '. $type .'"><div class="msg">'. $message .'</div>'. ( $close_link ? '<a href="#" class="toggle-alert">'. __( 'Toggle', 'dce' ) .'</a>' : '' ) .'</div><div class="demo-sep sep-none" style="margin-top:20px;"></div>';
}



