<?php
/**
 * Template tags
 * 
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

/**
 * Divider
 *  
 * @param string $type ( shadow | double | single | dashed | dotted )
 * @param string $tag
 * @return string
 */
function dce_divider( $type = 'shadow', $tag = 'div' )
{
	return '<'. $tag .' class="sep-'. $type .'" style="margin-bottom:20px;"></'. $tag .'>';
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
function dce_table_start( $id = '' )
{
	return '<div'. ( '' == $id ? '' : ' id='. $id ) .' class="table-1"><table width="100%">';
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
 * @param string $message
 * @param string $type
 * @return string
 */
function dce_alert_message( $message, $type = 'general', $close_link = false )
{
	return '<div class="alert '. $type .'"><div class="msg">'. $message .'</div>'. ( $close_link ? '<a href="#" class="toggle-alert">'. __( 'Toggle', 'dce' ) .'</a>' : '' ) .'</div><div class="demo-sep sep-none" style="margin-top:20px;"></div>';
}



