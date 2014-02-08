<?php
/**
 * Escrow Class
 *
 * @package Digital Coins Exchanging Store
 * @since 1.0
 */

/**
 * Escrow Class
 */
class DCE_Escrow extends DCE_Offer
{
	/**
	 * Constructor ( override )
	 *
	 * @param number|WP_Post|object $post_id
	 */
	public function __construct( $post_id )
	{
		parent::__construct( $post_id );
	}

	/**
	 * Form data fields
	 * 
	 * @param array $coin_types
	 * @return array
	 */
	public static function form_fields( &$coin_types = '' )
	{
		if ( empty( $coin_types ) )
			$coin_types = dce_get_coin_types();

		// original fields
		$fields = parent::form_fields( $coin_types );

		// change details label
		$fields['details']['label'] = __( 'Escrow Terms & Agreements', 'dce' );

		// new fields
		$fields['target_email'] = array ( 
				'label' => __( 'Target User Email', 'dce' ), 
				'input' => 'text',
				'data_type' => 'email',
				'required' => true,
		);

		return $fields;
	}
}





















