<?php
/**
 * Recent Offers widget
 *
 * @package Digital Coins Exchanging Store
 * @since 1.2
 */

class DCE_Widget_Recent_Offers extends WP_Widget 
{
	public function __construct() 
	{
		parent::__construct( 'recent-offers', __( 'Recent Offers', 'dce' ), array ( 
				'classname' => 'widget_recent_entries', 
				'description' => __( 'Most recent Offers', 'dce' ),
		) );

		$this->alt_option_name = 'widget_recent_offers';

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	public function widget( $args, $instance ) 
	{
		$cache = wp_cache_get( 'widget_recent_offers', 'widget' );

		if ( !is_array( $cache ) )
			$cache = array();

		if ( !isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();

		$title = empty( $instance['title'] ) ?  __( 'Recent Offers', 'dce' ) : $instance['title'];
		$number = empty( $instance['number'] ) ? 4 : absint( $instance['number'] );
		if ( !$number )
 			$number = 4;

		// widget start
		echo $args['before_widget'];

		if ( $title )
			echo $args['before_title'], $title, $args['after_title'];

		// list
		echo do_shortcode( '[dce-recent-offers count="'. $number .'" display="list"]' );

		// widget end
		echo $args['after_widget'];

		$cache[ $args['widget_id'] ] = ob_get_flush();
		wp_cache_set( 'widget_recent_offers', $cache, 'widget' );
	}

	public function update( $new_instance, $old_instance ) 
	{
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = (int) $new_instance['number'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['widget_recent_offers'] ) )
			delete_option( 'widget_recent_offers' );

		return $instance;
	}

	/**
	 * Flush/clear cache
	 */
	public function flush_widget_cache() 
	{
		wp_cache_delete( 'widget_recent_offers', 'widget' );
	}

	public function form( $instance ) 
	{
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 4;

		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
		<?php
	}
}




