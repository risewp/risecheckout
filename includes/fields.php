<?php
/**
 * WooCommerce Fields Customization.
 *
 * This file customizes the default WooCommerce billing, shipping, and checkout fields.
 *
 * @package RiseCheckout
 */

defined( 'ABSPATH' ) || exit;

/**
 * Customize default WooCommerce address fields.
 *
 * @param array $fields Address fields.
 * @return array Modified address fields.
 */
function risecheckout_wc_default_address_fields( $fields ) {
	$placeholders = array(
		'first_name' => array(
			/* translators: %s: Example */
			'placeholder' => sprintf( __( 'e.g.: %s', 'risecheckout' ), __( 'Mary', 'risecheckout' ) ),
		),
		'last_name'  => array(
			/* translators: %s: Example */
			'placeholder' => sprintf( __( 'e.g.: %s', 'risecheckout' ), __( 'Johnson', 'risecheckout' ) ),
		),
	);

	$classes = array(
		'postcode' => 'postcode-field',
		'state'    => 'state-field',
		'city'     => 'city-field',
	);

	foreach ( $placeholders as $key => $attributes ) {
		$fields[ $key ] = array_merge( $fields[ $key ], $attributes );
	}

	foreach ( $classes as $key => $class ) {
		$fields[ $key ]['class'][] = $class;
	}

	unset( $fields['address_1']['placeholder'] );

	$fields['address_2']['label'] = __( 'Address line 2', 'risecheckout' );
	unset( $fields['address_2']['label_class'], $fields['address_2']['placeholder'], $fields['address_2']['required'] );

	return $fields;
}
add_filter( 'woocommerce_default_address_fields', 'risecheckout_wc_default_address_fields', 11 );

/**
 * Customize WooCommerce billing fields.
 *
 * @param array $fields Billing fields.
 * @return array Modified billing fields.
 */
function risecheckout_wc_billing_fields( $fields ) {
	$fields['billing_email']['label'] = __( 'Email', 'risecheckout' );

	if ( isset( $fields['billing_phone'] ) ) {
		$fields['billing_phone']['label'] = __( 'Mobile', 'risecheckout' );
	}

	return $fields;
}
add_filter( 'woocommerce_billing_fields', 'risecheckout_wc_billing_fields' );

/**
 * Customize WooCommerce checkout fields.
 *
 * @param array $fields Checkout fields.
 * @return array Modified checkout fields.
 */
function risecheckout_wc_fields( $fields ) {
	$billing_fields = array(
		'billing_email'        => array( 'priority' => 21 ),
		'billing_cpf'          => array(
			// 'class'       => array( 'cpf-field' ),
			// 'required'    => true,
			'placeholder' => '000.000.000-00',
		),
		'billing_phone'        => array(
			'priority'    => 24,
			'placeholder' => '(00) 00000-0000',
		),
		// 'billing_postcode'     => array( 'class' => array( 'postcode-field' ) ),
		// 'billing_state'        => array(
		// 	'class' => array( 'state-field' ),
		// 	'break' => true,
		// ),
		// 'billing_city'         => array( 'class' => array( 'city-field' ) ),
		// 'billing_number'       => array( 'class' => array( 'number-field' ) ),
		'billing_neighborhood' => array(
			// 'class'    => array( 'neighborhood-field' ),
			'priority' => 56,
		),
	);

	$shipping_fields = array(
		// 'shipping_postcode'     => array( 'class' => array( 'postcode-field' ) ),
		// 'shipping_state'        => array(
		// 	'class' => array( 'state-field' ),
		// 	'break' => true,
		// ),
		// 'shipping_city'         => array( 'class' => array( 'city-field' ) ),
		// 'shipping_address_1'    => array(
		// 	'class' => array_merge(
		// 		array_diff( $fields['shipping']['shipping_address_1']['class'], array( 'form-row-last' ) ),
		// 		array( 'form-row-wide' )
		// 	),
		// ),
		// 'shipping_number'       => array( 'class' => array( 'number-field' ) ),
		// 'shipping_neighborhood' => array( 'class' => array( 'neighborhood-field' ) ),
		// 'shipping_address_2'    => array(
		// 	'class' => array_merge(
		// 		array_diff( $fields['shipping']['shipping_address_2']['class'], array( 'form-row-last' ) ),
		// 		array( 'form-row-wide' )
		// 	),
		// ),
	);

	foreach ( $billing_fields as $key => $attributes ) {
		$fields['billing'][ $key ] = array_merge( $fields['billing'][ $key ], $attributes );
	}

	foreach ( $shipping_fields as $key => $attributes ) {
		$fields['shipping'][ $key ] = array_merge( $fields['shipping'][ $key ], $attributes );
	}

	foreach ( array_keys( $fields ) as $field_type ) {
		uasort( $fields[ $field_type ], 'wc_checkout_fields_uasort_comparison' );
	}

	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'risecheckout_wc_fields', 11 );

function risecheckout_wc_fields_grid( $fields ) {
	$billing_fields = array(
		'billing_cpf'       => array(
			'class' => array_merge(
				$fields['billing']['billing_cpf']['class'],
				array( 'cpf-field' )
			),
		),
		'billing_number'       => array(
			'class' => array_merge(
				array_diff( $fields['billing']['billing_number']['class'], array( 'form-row-wide' ) ),
				array( 'form-row-first', 'number-field' )
			),
		),
		'billing_neighborhood' => array(
			'class' => array_merge(
				array_diff( $fields['billing']['billing_neighborhood']['class'], array( 'form-row-wide' ) ),
				array( 'form-row-last', 'neighborhood-field' )
			),
		),
		'billing_city'         => array(
			'class' => array_merge(
				array_diff( $fields['billing']['billing_city']['class'], array( 'form-row-wide' ) ),
				array( 'form-row-first' )
			),
		),
		'billing_state'        => array(
			'class' => array_merge(
				array_diff( $fields['billing']['billing_state']['class'], array( 'form-row-wide' ) ),
				array( 'form-row-last' )
			),
		),
	);

	$shipping_fields = array(
		'shipping_number'       => array(
			'class' => array_merge(
				array_diff( $fields['shipping']['shipping_number']['class'], array( 'form-row-wide' ) ),
				array( 'form-row-first', 'number-field' )
			),
		),
		'shipping_neighborhood' => array(
			'class' => array_merge(
				array_diff( $fields['shipping']['shipping_neighborhood']['class'], array( 'form-row-wide' ) ),
				array( 'form-row-last', 'neighborhood-field' )
			),
		),
		'shipping_city'         => array(
			'class' => array_merge(
				array_diff( $fields['shipping']['shipping_number']['class'], array( 'form-row-wide' ) ),
				array( 'form-row-first' )
			),
		),
		'shipping_state'        => array(
			'class' => array_merge(
				array_diff( $fields['shipping']['shipping_neighborhood']['class'], array( 'form-row-wide' ) ),
				array( 'form-row-last' )
			),
		),
	);

	foreach ( $billing_fields as $key => $attributes ) {
		$fields['billing'][ $key ] = array_merge( $fields['billing'][ $key ], $attributes );
	}

	foreach ( $shipping_fields as $key => $attributes ) {
		$fields['shipping'][ $key ] = array_merge( $fields['shipping'][ $key ], $attributes );
	}

	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'risecheckout_wc_fields_grid', 12 );

function risecheckout_wc_fields_priority( $fields ) {
	// echo '<pre>';
	// print_r($fields);
	// die;
	$billing_fields = array(
		// 'billing_email'        => array( 'priority' => 21 ),
		'billing_cpf'          => array(
			// 'class'       => array( 'cpf-field' ),
			// 'required'    => true,
			// 'placeholder' => '000.000.000-00',
		),
		'billing_phone'        => array(
			// 'priority'    => 24,
			// 'placeholder' => '(00) 00000-0000',
		),
		'billing_postcode'     => array(
			// 'class' => array( 'postcode-field' ),
		),
		'billing_city'         => array(
			'priority'    => 70,
			// 'class' => array( 'city-field' ),
		),
		'billing_state'        => array(
			'priority'    => 80,
			// 'class' => array( 'state-field' ),
			// 'break' => true,
		),
		'billing_number'       => array(
			// 'class' => array( 'number-field' ),
		),
		'billing_neighborhood' => array(
			// 'class'    => array( 'neighborhood-field' ),
			// 'priority' => 56,
		),
	);

	$shipping_fields = array(
		'shipping_postcode'     => array(
			// 'class' => array( 'postcode-field' ),
		),
		'shipping_city'         => array(
			'priority'    => 70,
			// 'class' => array( 'city-field' )
		),
		'shipping_state'        => array(
			'priority'    => 80,
			// 'class' => array( 'state-field' ),
			// 'break' => true,
		),
		'shipping_address_1'    => array(
			// 'class' => array_merge(
			// 	array_diff( $fields['shipping']['shipping_address_1']['class'], array( 'form-row-last' ) ),
			// 	array( 'form-row-wide' )
			// ),
		),
		'shipping_number'       => array(
			// 'class' => array( 'number-field' ),
		),
		'shipping_neighborhood' => array(
			// 'class' => array( 'neighborhood-field' ),
		),
		'shipping_address_2'    => array(
			// 'class' => array_merge(
			// 	array_diff( $fields['shipping']['shipping_address_2']['class'], array( 'form-row-last' ) ),
			// 	array( 'form-row-wide' )
			// ),
		),
	);

	foreach ( $billing_fields as $key => $attributes ) {
		$fields['billing'][ $key ] = array_merge( $fields['billing'][ $key ], $attributes );
	}

	foreach ( $shipping_fields as $key => $attributes ) {
		$fields['shipping'][ $key ] = array_merge( $fields['shipping'][ $key ], $attributes );
	}

	foreach ( array_keys( $fields ) as $field_type ) {
		uasort( $fields[ $field_type ], 'wc_checkout_fields_uasort_comparison' );
	}

	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'risecheckout_wc_fields_priority', 13 );

/**
 * Customize WooCommerce country locale.
 *
 * @param array $locale Country locale.
 * @return array Modified country locale.
 */
function risecheckout_wc_get_country_locale( $locale ) {
	foreach ( $locale as $country_code => $fields ) {
		if ( 'BR' !== $country_code ) {
			$locale[ $country_code ] = array_merge(
				$locale[ $country_code ],
				array(
					'phone' => array( 'placeholder' => '' ),
					'cpf'   => array(
						'placeholder' => '',
						'required'    => false,
						'hidden'      => true,
					),
				)
			);
		}
	}

	if ( ! isset( $locale['BR'] ) ) {
		$locale['BR'] = array();
	}

	$locale['BR'] = array_merge(
		$locale['BR'],
		array(
			'city'  => array( 'priority' => 70 ),
			'state' => array( 'priority' => 80 ),
		)
	);

	return $locale;
}
add_filter( 'woocommerce_get_country_locale', 'risecheckout_wc_get_country_locale', 11 );
