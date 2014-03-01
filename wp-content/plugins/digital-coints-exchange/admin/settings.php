<?php
/**
 * Settings
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

global $dce_admin_settings_fields, $dce_admin_settings_page_slug;

// settings fields
$dce_admin_settings_fields = array();

// settings page slug
$dce_admin_settings_page_slug = '';

add_action( 'init', 'dce_settings_init' );
/**
 * Extra settings init
*/
function dce_settings_init()
{
	global $dce_admin_settings_fields;

	// settings fields list
	$dce_admin_settings_fields = array (
			array (
					'label' => __( 'Commission', 'dce' ),
					'page' => 'dce_settings_page',
					'section' => 'dce_general',
					'args' => array (
							'name' => 'commission',
							'input' => 'number',
							'class' => 'small-text code',
							'default' => 5,
							'attrs' => array( 'step' => '1.00', 'min' => '1.00' ),
							'visible' => true,
							'desc' => '%',
					),
			),
			array (
					'label' => __( 'Escrow Expiration Days', 'dce' ),
					'page' => 'dce_settings_page',
					'section' => 'dce_general',
					'args' => array (
							'name' => 'escrow_expire',
							'input' => 'number',
							'class' => 'small-text code',
							'default' => 10,
							'attrs' => array( 'step' => '1.00', 'min' => '1.00' ),
							'visible' => true,
							'desc' => __( 'Number of days to expire/close escrow if no transfer done', 'dce' ),
					),
			),
			array (
					'label' => __( 'Transactions Confirmation Level', 'dce' ),
					'page' => 'dce_settings_page',
					'section' => 'dce_general',
					'args' => array (
							'name' => 'trans_conf_level',
							'input' => 'number',
							'class' => 'small-text code',
							'default' => 2,
							'attrs' => array( 'step' => '1.00', 'min' => '1.00' ),
							'visible' => true,
							'desc' => __( 'Number of confirmations to check coin transactions against', 'dce' ),
					),
			),
			array (
					'label' => __( 'Coin Types', 'dce' ),
					'page' => 'dce_settings_page',
					'section' => 'dce_general',
					'args' => array (
							'name' => 'coin_types',
							'input' => 'html',
							'class' => 'coin-types',
							'default' => array(),
							'desc' => __( 'List of supported digital coins', 'dce' ),
					),
			),
	);
}

add_action( 'admin_init', 'dce_admin_settings_init' );
/**
 * Register the form setting for our dce_options array.
 *
 * @since Digital Coins Exchanging Store 2.0
 */
function dce_admin_settings_init()
{
	global $dce_admin_settings_fields;

	// register settings group
	register_setting( 'dce_options', 'dce_admin_options', 'dce_admin_settings_sanitize_values' );

	/**
	 * Register our settings field group
	 */

	// general section
	add_settings_section( 'dce_general', __( 'General Settings', 'dce' ), '__return_false', 'dce_settings_page' );

	// Register our individual settings fields
	foreach ( $dce_admin_settings_fields as $field_data )
	{
		// register field
		add_settings_field( $field_data['args']['name'], $field_data['label'], 'dce_admin_settings_field_ui', $field_data['page'], $field_data['section'], $field_data['args'] );
	}
}

/**
 * Admin settings values sanitizing
 * 
 * @param array $settings_values
 * @return array
 */
function dce_admin_settings_sanitize_values( $settings_values )
{
	// loop values
	foreach ( $settings_values as $name => &$value )
	{
		switch ( $name )
		{
			case 'commission':
				$value = (float) $value;
				break;

			case 'escrow_expire':
				$value = (int) $value;
				break;

			case 'coin_types':
				foreach ( $value as $coin_key => $coin_data )
				{
					// if new item
					if ( strpos( $coin_key, 'new-' ) !== false )
					{
						// generate new index key
						$value[ sanitize_key( $coin_data['label'] ) ] = array_map( 'sanitize_text_field', $coin_data );

						// remove old one
						unset( $value[$coin_key] );
					}
				}
				break;
		}
	}

	return $settings_values;
}

/**
 * Renders settings fields UI
 *
 * @param array $args
 */
function dce_admin_settings_field_ui( $args )
{
	// value
	$input_value = dce_admin_get_settgins( $args['name'] );

	// inputs names
	$input_name = 'dce_admin_options['. $args[ 'name' ] .']';

	// additional attributes holder
	$additional_attrs = '';
	if ( isset( $args['attrs'] ) )
	{
		// attributes loop
		foreach ( $args['attrs'] as $attr_name => $attr_value )
		{
			// echo attr
			$additional_attrs .= $attr_name .'="'. $attr_value .'" ';
		}
	}

	// input layout
	switch( $args['input'] )
	{
		// html layout
		case 'html':
			// coin types
			if ( 'coin_types' == $args['name'] )
			{
				// list start
				echo '<ol class="', $args['class'] ,'">';

				// coins loop
				foreach ( $input_value as $coin_key => $coin_data )
				{
					echo dce_admin_settings_coin_item_template( $coin_key, $coin_data, $input_name );
				}

				// list end
				echo '</ol>';

				// add new coin
				echo '<p><a href="#" class="button add-coin-type">+ Add new</a></p>';
			}
			break;

		// text input
		case 'text':
		// numeric input
		case 'number':
		// email address input
		case 'email':
			echo '<input class="regular-text ', @$args['class'] ,'" type="', $args['input'] ,'" name="', $input_name ,'" id="', $args['name'] ,'" ';
			echo 'value="', esc_attr( $input_value ) ,'" ', $additional_attrs ,'/>';
			break;

		// text area
		case 'textarea':
			echo '<textarea class="regular-text ', @$args['class'] ,'" name="', $input_name ,'" id="', $args['name'] ,'" ', $additional_attrs ,'>';
			echo esc_attr( $input_value ) ,'</textarea><br/>';
			break;
	}

	// description
	echo ' <span class="description">', @$args['desc'] ,'</span>';
}

/**
 * Settings input template for coin type
 * 
 * @param string $coin_key
 * @param array $coin_data
 * @param string $input_name
 * @return string
 */
function dce_admin_settings_coin_item_template( $coin_key, $coin_data, $input_name )
{
	$input_name = $input_name .'['. $coin_key .']';

	$coin_data = wp_parse_args( $coin_data, array ( 
			'label' => '',
			'single' => '',
			'plural' => '',
			'command' => '',
	) );

	// item start
	$out = '<li class="coin-item">';

	// label
	$out .= '<p><input type="text" name="'. $input_name .'[label]" class="regular-text" value="'. esc_attr( $coin_data['label'] ) .'" placeholder="'. __( 'Coin Label', 'dce' ) .'" /></p>';

	// singular format
	$out .= '<p><input type="text" name="'. $input_name .'[single]" class="regular-text code" value="'. esc_attr( $coin_data['single'] ) .'" placeholder="'. __( 'Singular Display Format', 'dce' ) .'" />';
	$out .= '&nbsp;<span class="description">'. __( 'Formated string, ex: <strong>%f coin</strong>', 'dce' ) .'</span></p>';

	// plural format
	$out .= '<p><input type="text" name="'. $input_name .'[plural]" class="regular-text code" value="'. esc_attr( $coin_data['plural'] ) .'" placeholder="'. __( 'Plural Display Format', 'dce' ) .'" />';
	$out .= '&nbsp;<span class="description">'. __( 'Formated string, ex: <strong>%f coins</strong>', 'dce' ) .'</span></p>';

	// base command
	$out .= '<p><input type="text" name="'. $input_name .'[command]" class="regular-text code" value="'. esc_attr( $coin_data['command'] ) .'" placeholder="'. __( 'Command Line Base', 'dce' ) .'" /></p>';

	// remove button / item end
	$out .= '<p><a href="#" title="'. __( 'Delete this Coin', 'dce' ) .'" class="button button-delete">'. __( 'Delete', 'dce' ) .'</a></p></li>';

	return $out;
}

add_action( 'admin_menu', 'dce_admin_settgins_add_page' );
/**
 * Add our options page to the admin menu.
 *
 * @since Digital Coins Exchanging Store 2.0
 */
function dce_admin_settgins_add_page()
{
	global $dce_admin_settings_page_slug;

	// add settings page
	$dce_admin_settings_page_slug = add_options_page( __( 'Digital Coins Exchanging Store Settings', 'dce' ), __( 'DCE Store Settings', 'dce' ), 'manage_options', 'dce_admin_settings_page', 'dce_admin_settings_page_ui' );
}

/**
 * Returns the options array for Digital Coins Exchanging Store.
 *
 * @param string $option_name
 * @return mixed
 *
 * @since Digital Coins Exchanging Store 2.0
 */
function dce_admin_get_settgins( $option_name = null )
{
	global $dce_admin_settings_fields;

	// load options
	$saved = (array) get_option( 'dce_admin_options' );

	// default values
	$defaults = array();

	// fields loop
	foreach ( $dce_admin_settings_fields as $field_data )
	{
		// set default values
		$defaults[ $field_data['args']['name'] ] = $field_data['args']['default'];
	}

	// parse defaults
	$options = wp_parse_args( $saved, $defaults );
	$options = array_intersect_key( $options, $defaults );

	// return options values
	return $option_name && isset( $options[ $option_name ] ) ? $options[ $option_name ] : $options;
}

/**
 * Renders the Options administration screen.
 *
 * @since Digital Coins Exchanging Store 2.0
 */
function dce_admin_settings_page_ui()
{
	?>
	<div class="wrap">
		<h2><?php _e( 'Digital Coins Exchanging Store Settings', 'dce' ); ?></h2>

		<form method="post" action="options.php">
			<?php
				settings_fields( 'dce_options' );
				do_settings_sections( 'dce_settings_page' );
				submit_button();
			?>
		</form>
	</div>
	<?php
}














