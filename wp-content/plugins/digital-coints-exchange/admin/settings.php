<?php
/**
 * Settings
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

global $dce_admin_settings_fields, $dce_admin_pages_slugs;

// settings fields
$dce_admin_settings_fields = array();

// settings page slug
$dce_admin_pages_slugs = array();

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
 * @since Digital Coins Exchanging Store 1.0
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
						$key_gen = sanitize_key( $coin_data['label'] );
						if ( empty( $key_gen ) )
							$key_gen = 'coin-'. uniqid();

						$value[$key_gen] = array_map( 'sanitize_text_field', $coin_data );

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
	$input_value = dce_admin_get_settings( $args['name'] );

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
			'rpc_user' => '',
			'rpc_pass' => '',
			'rpc_host' => '',
			'rpc_port' => '',
			'rpc_uri' => '',
	) );

	// item start
	$out = '<li class="coin-item">';

	// label
	$out .= '<p class="item-field"><input type="text" name="'. $input_name .'[label]" class="regular-text" value="'. esc_attr( $coin_data['label'] ) .'" placeholder="'. __( 'Coin Label', 'dce' ) .'" /></p>';

	// singular format
	$out .= '<p class="item-field"><input type="text" name="'. $input_name .'[single]" class="regular-text code" value="'. esc_attr( $coin_data['single'] ) .'" placeholder="'. __( 'Singular Display Format', 'dce' ) .'" />';
	$out .= '&nbsp;<span class="description">'. __( 'Formated string, ex: <strong>%s coin</strong>', 'dce' ) .'</span></p>';

	// plural format
	$out .= '<p class="item-field"><input type="text" name="'. $input_name .'[plural]" class="regular-text code" value="'. esc_attr( $coin_data['plural'] ) .'" placeholder="'. __( 'Plural Display Format', 'dce' ) .'" />';
	$out .= '&nbsp;<span class="description">'. __( 'Formated string, ex: <strong>%s coins</strong>', 'dce' ) .'</span></p>';

	// rpc user
	$out .= '<p class="item-field"><input type="text" name="'. $input_name .'[rpc_user]" class="regular-text code" value="'. esc_attr( $coin_data['rpc_user'] ) .'" placeholder="'. __( 'RPC Username', 'dce' ) .'" /></p>';

	// rpc pass
	$out .= '<p class="item-field"><input type="text" name="'. $input_name .'[rpc_pass]" class="regular-text code" value="'. esc_attr( $coin_data['rpc_pass'] ) .'" placeholder="'. __( 'RPC Password', 'dce' ) .'" /></p>';

	// rpc host
	$out .= '<p class="item-field"><input type="text" name="'. $input_name .'[rpc_host]" class="regular-text code" value="'. esc_attr( $coin_data['rpc_host'] ) .'" placeholder="'. __( 'RPC Host', 'dce' ) .'" />';
	$out .= '&nbsp;<span class="description">'. __( 'Default is <strong>127.0.0.1</strong> or <strong>localhost</strong>', 'dce' ) .'</span></p>';

	// rpc port
	$out .= '<p class="item-field"><input type="text" name="'. $input_name .'[rpc_port]" class="regular-text code" value="'. esc_attr( $coin_data['rpc_port'] ) .'" placeholder="'. __( 'RPC Port', 'dce' ) .'" /></p>';

	// rpc uri
	$out .= '<p class="item-field"><input type="text" name="'. $input_name .'[rpc_uri]" class="regular-text code" value="'. esc_attr( $coin_data['rpc_uri'] ) .'" placeholder="'. __( 'RPC URL/URI', 'dce' ) .'" />';
	$out .= '&nbsp;<span class="description">'. __( 'Default is empty', 'dce' ) .'</span></p>';

	// rpc description
	$out .= '<p class="item-field description">'. __( 'You can find those data in *coin.conf file', 'dce' ) .'</p>';

	// rpc test
	$out .= '<a href="#" class="button rpc-test">'. __( 'Test RPC Connection', 'dce' ) .'</a><div class="rpc-test-res"></div>';

	// remove button / item end
	$out .= '<p><a href="#" title="'. __( 'Delete this Coin', 'dce' ) .'" class="button button-delete">'. __( 'Delete', 'dce' ) .'</a></p></li>';

	return $out;
}

add_action( 'admin_menu', 'dce_admin_settgins_add_page' );
/**
 * Add our options page to the admin menu.
 *
 * @since Digital Coins Exchanging Store 1.0
 */
function dce_admin_settgins_add_page()
{
	global $dce_admin_pages_slugs;

	// add settings page
	$dce_admin_pages_slugs[] = add_options_page( __( 'Digital Coins Exchanging Store Settings', 'dce' ), __( 'DCE Store Settings', 'dce' ), 'manage_options', 'dce_admin_settings_page', 'dce_admin_settings_page_ui' );

	// coins API explorer
	$dce_admin_pages_slugs[] = add_options_page( __( 'Digital Coins API Explorer', 'dce' ), __( 'DCE API Explorer', 'dce' ), 'manage_options', 'dce_admin_api_page', 'dce_admin_api_page_ui' );
}

/**
 * Returns the options array for Digital Coins Exchanging Store.
 *
 * @param string $option_name
 * @return mixed
 *
 * @since Digital Coins Exchanging Store 1.0
 */
function dce_admin_get_settings( $option_name = null )
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
 * @since Digital Coins Exchanging Store 1.0
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

/**
 * Coins API Explorer page
 * 
 */
function dce_admin_api_page_ui()
{
	$coin_types = dce_get_coin_types();
	?>
	<div class="wrap">
		<h2><?php _e( 'Digital Coins API Explorer', 'dce' ); ?></h2>
<div id="ajax-loading"></div>
		<div id="api-result" class="large-text code"></div>

		<form action="" method="post" id="api-from">
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><label for="api-command"><?php _e( 'API Command', 'dce' ) ?></label></th>
						<td>
							<input type="text" name="api_command" id="api-command" class="large-text code" />
							<span class="description"><?php _e( 'Press <strong>Enter</strong> to execute, <strong>Up</strong> or <strong>Down</strong> to navigate through previous commands', 'dce' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="api-coin"><?php _e( 'Digital Coin', 'dce' ) ?></label></th>
						<td>
							<select name="api_coin" id="api-coin">
								<?php 
								foreach ( $coin_types as $coin_name => $coin_data )
								{
									echo '<option value="', $coin_name ,'">', $coin_data['label'] ,'</option>';
								}
								?>
							</select>
						</td>
					</tr>
				</tbody>
			</table><!-- .form-table -->
			<input type="hidden" name="action" value="dce_api_explor"  />
		</form>
	</div>
	<?php
}













