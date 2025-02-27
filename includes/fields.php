<?php

function risecheckout_wc__default_address_fields( $fields ) {
	$fields['first_name']['placeholder'] = sprintf(
		/* translators: %s: Example */
		__( 'e.g.: %s', 'risecheckout' ),
		__( 'Mary', 'risecheckout' )
	);

	$fields['last_name']['placeholder'] = sprintf(
		/* translators: %s: Example */
		__( 'e.g.: %s', 'risecheckout' ),
		__( 'Johnson', 'risecheckout' )
	);

	$fields['postcode']['class'][] = 'postcode-field';

	$fields['state']['class'][] = 'state-field';

	$fields['city']['class'][] = 'city-field';

	unset( $fields['address_1']['placeholder'] );

	$fields['address_2']['label'] = __( 'Address line 2', 'risecheckout' );
	unset( $fields['address_2']['label_class'] );
	unset( $fields['address_2']['placeholder'] );
	unset( $fields['address_2']['required'] );

	return $fields;
}
add_filter( 'woocommerce_default_address_fields', 'risecheckout_wc__default_address_fields', 11 );

function risecheckout_wc_billing_fields( $fields ) {
	$fields['billing_email']['label'] = __( 'Email', 'risecheckout' );

	if ( isset( $fields['billing_phone'] ) ) {
		$fields['billing_phone']['label'] = __( 'Mobile', 'risecheckout' );
	}

	return $fields;
}
add_filter( 'woocommerce_billing_fields', 'risecheckout_wc_billing_fields' );

function risecheckout_wc_fields( $fields ) {

	$fields['billing']['billing_email']['priority'] = 21;

	$fields['billing']['billing_cpf']['class'][]     = 'cpf-field';
	$fields['billing']['billing_cpf']['required']    = true;
	$fields['billing']['billing_cpf']['placeholder'] = '000.000.000-00';

	$fields['billing']['billing_phone']['priority']    = 24;
	$fields['billing']['billing_phone']['placeholder'] = '(00) 00000-0000';

	$fields['billing']['billing_postcode']['class'][] = 'postcode-field';

	$fields['billing']['billing_state']['class'][] = 'state-field';
	$fields['billing']['billing_state']['break']   = true;

	$fields['billing']['billing_city']['class'][] = 'city-field';

	$fields['billing']['billing_number']['class'][] = 'number-field';

	$fields['billing']['billing_neighborhood']['class'][]  = 'neighborhood-field';
	$fields['billing']['billing_neighborhood']['priority'] = 56;

	$fields['shipping']['shipping_postcode']['class'][] = 'postcode-field';

	$fields['shipping']['shipping_state']['class'][] = 'state-field';
	$fields['shipping']['shipping_state']['break']   = true;

	$fields['shipping']['shipping_city']['class'][] = 'city-field';

	$fields['shipping']['shipping_address_1']['class']   = array_diff(
		$fields['shipping']['shipping_address_1']['class'],
		array( 'form-row-last' )
	);
	$fields['shipping']['shipping_address_1']['class'][] = 'form-row-wide';

	$fields['shipping']['shipping_number']['class'][] = 'number-field';

	$fields['shipping']['shipping_neighborhood']['class'][] = 'neighborhood-field';

	$fields['shipping']['shipping_address_2']['class']   = array_diff(
		$fields['shipping']['shipping_address_2']['class'],
		array( 'form-row-last' )
	);
	$fields['shipping']['shipping_address_2']['class'][] = 'form-row-wide';

	foreach ( $fields as $type => $type_fields ) {
		uasort( $fields[ $type ], 'wc_checkout_fields_uasort_comparison' );
	}

	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'risecheckout_wc_fields' );

function risecheckout_wc_get_country_locale( $locale ) {
	foreach ( $locale as $county_code => $fields ) {
		if ( 'BR' !== $county_code ) {
			$locale[ $county_code ]['phone']['placeholder'] = '';
			$locale[ $county_code ]['cpf']['placeholder']   = '';
			$locale[ $county_code ]['cpf']['required']      = false;
			$locale[ $county_code ]['cpf']['hidden']        = true;
		}
	}
	if ( ! isset( $locale['BR'] ) ) {
		$locale['BR'] = array();
	}
	$locale['BR'] = array_merge(
		$locale['BR'],
		array(
			'state' => array(
				'priority' => 70,
			),
			'city'  => array(
				'priority' => 80,
			),
		)
	);
	return $locale;
}
add_filter( 'woocommerce_get_country_locale', 'risecheckout_wc_get_country_locale', 11 );
