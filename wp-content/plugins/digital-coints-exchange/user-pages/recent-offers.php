<?php
/**
 * Recent Offers created
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

// enqueues
wp_enqueue_style( 'dce-public-style' );

// shortcode output
$output = '';

// attributes
$attrs = wp_parse_args( $GLOBALS['dce-recent-offers']['attrs'], array ( 
		'count' => '4',
		'display' => 'box', // box or list
) );

// display
$display_box = 'box' == $attrs['display'];

// date format
$date_format = get_option( 'date_format' );
$datetime = null;

// wrapper
if ( $display_box )
	$output .= '<div class="avada-container layout-date-on-side layout-columns-2"><section class="columns columns-2"><div class="holder">';
else
	$output .= '<ul>';

// recent offers
$recent_offers = DCE_Offer::query_offers( array ( 
		'post_status' => 'publish', 
		'numberposts' => $attrs['count'], 
		'nopaging' => false, 
		'list_output' => 'class', 
) );

/* @var $offer DCE_Offer */
foreach ( $recent_offers as $offer )
{
	if ( $display_box )
	{
		$datetime = new DateTime( $offer->datetime );

		$output .= '<article class="col clearfix"><div class="date-and-formats"><div class="date-box">';
		$output .= '<span class="date">'. $datetime->format( 'd' ) .'</span>';
		$output .= '<span class="month-year">'. $datetime->format( 'm, Y' ) .'</span></div>';
		$output .= '<div class="format-box"><i class="icon-comments-alt"></i></div></div><div class="recent-posts-content">';
		$output .= '<h4><a href="'. $offer->url() .'">'. sprintf( __( '%s For %s', 'dce' ), $offer->convert_from_display(), $offer->convert_to_display() ) .'</a></h4>';
		$output .= '<ul class="meta"><li>'. $datetime->format( $date_format ) .'</li></ul>';
		$output .= '<div class="excerpt-container"><p>'. DCE_Utiles::substr_words( wp_strip_all_tags( $offer->details, true ), 24, '' ) .'</p></div></div></article>';
	}
	else
	{
		$output .= '<li><a href="'. $offer->url() .'">'. sprintf( __( '%s For %s', 'dce' ), $offer->convert_from_display(), $offer->convert_to_display() ) .'</a></li>';
	}
}

// wrapper end
$output .= $display_box ? '</div></section></div>' : '</ul>';

return $output;
















