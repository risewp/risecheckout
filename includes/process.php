<?php

function risecheckout_sanitize_numbers( $text ) {
	return preg_replace( '/\D/', '', $text );
}
// add_filter( 'woocommerce_process_checkout_field_billing_cpf', 'risecheckout_sanitize_numbers' );
// add_filter( 'woocommerce_process_checkout_field_billing_phone', 'risecheckout_sanitize_numbers' );
// add_filter( 'woocommerce_process_checkout_field_billing_postcode', 'risecheckout_sanitize_numbers' );
// add_filter( 'woocommerce_process_checkout_field_billing_shipping', 'risecheckout_sanitize_numbers' );

function risecheckout_pay_with_different_address( $data ) {
	$new_data = array();
	foreach ( $data as $key => $value ) {
		if ( 'ship_to_different_address' === $key ) {
			$new_data['pay_with_different_address'] = false;
		}
		$new_data[ $key ] = $value;
	}
	return $new_data;
}
add_filter( 'woocommerce_checkout_posted_data', 'risecheckout_pay_with_different_address' );

/**
 * Validate a Brazilian CPF number.
 *
 * @param string $cpf The CPF number to validate.
 * @return bool True if the CPF is valid, false otherwise.
 */
function risecheckout_is_valid_cpf( $cpf ) {
	// Remove non-numeric characters.
	$cpf = preg_replace( '/\D/', '', $cpf );

	// CPF must be 11 digits and not a sequence of the same number.
	if ( strlen( $cpf ) !== 11 || preg_match( '/^(\d)\1{10}$/', $cpf ) ) {
		return false;
	}

	// Loop through both check digits.
	for ( $t = 9; $t < 11; $t++ ) {
		$sum = 0;
		for ( $i = 0; $i < $t; $i++ ) {
			$sum += intval( $cpf[ $i ] ) * ( ( $t + 1 ) - $i );
		}
		$remainder = ( $sum * 10 ) % 11;
		$remainder = ( 10 === $remainder ? 0 : $remainder );
		if ( intval( $cpf[ $t ] ) !== $remainder ) {
			return false;
		}
	}

	return true;
}

/**
 * Format a Brazilian CPF number.
 *
 * This function removes all non-digit characters, limits the input to 11 digits,
 * and applies CPF formatting (e.g., 123.456.789-01).
 *
 * @param string $value The CPF value to format.
 * @return string Formatted CPF.
 */
function risecheckout_format_cpf( $value ) {
	// Remove non-numeric characters.
	$value = preg_replace( '/\D/', '', $value );

	// Limit to 11 digits.
	if ( strlen( $value ) > 11 ) {
		$value = substr( $value, 0, 11 );
	}

	$length    = strlen( $value );
	$formatted = $value;

	// Insert dot after the first 3 digits if applicable.
	if ( $length > 3 ) {
		$formatted = substr( $value, 0, 3 ) . '.' . substr( $value, 3 );
	}

	// Insert second dot after the first 6 digits if applicable.
	if ( $length > 6 ) {
		$formatted = substr( $formatted, 0, 7 ) . '.' . substr( $formatted, 7 );
	}

	// Insert hyphen after the first 9 digits if applicable.
	if ( $length > 9 ) {
		$formatted = substr( $formatted, 0, 11 ) . '-' . substr( $formatted, 11 );
	}

	return $formatted;
}

/*
 * Returns custom meta keys for customer session
 *
 * @return array List of custom meta keys
 */
function risecheckout_customer_custom_meta_keys() {
	return array(
		'billing_cpf',
		'billing_number',
		'billing_neighborhood',
		'shipping_number',
		'shipping_neighborhood',
	);
}

/*
 * Adds custom meta keys to the allowed session meta keys in WooCommerce
 *
 * @param array $keys Existing allowed session meta keys
 * @return array Merged array of allowed session meta keys
 */
function risecheckout_customer_allowed_session_meta_keys( $keys ) {
	return array_merge( $keys, risecheckout_customer_custom_meta_keys() );
}
add_filter( 'woocommerce_customer_allowed_session_meta_keys', 'risecheckout_customer_allowed_session_meta_keys' );

/*
 * Updates session data with custom meta fields
 *
 * @param array $data Checkout posted data
 * @return array Modified posted data
 */
function risecheckout_update_session( $data ) {
	$address_fields = array_unique(
		array_map(
			function ( $field ) {
				return preg_replace( '/^(billing_|shipping_)/', '', $field );
			},
			risecheckout_customer_custom_meta_keys()
		)
	);

	foreach ( $address_fields as $field ) {
		risecheckout_set_customer_address_fields( $field, $data );
	}

	WC()->customer->save();

	return $data;
}
add_filter( 'woocommerce_checkout_posted_data', 'risecheckout_update_session', 11 );

/*
 * Sets customer meta fields for both billing and shipping
 *
 * @param string $field Field name without prefix
 * @param array $data Checkout posted data
 */
function risecheckout_set_customer_address_fields( $field, $data ) {
	$billing_value  = null;
	$shipping_value = null;

	$meta_keys = risecheckout_customer_custom_meta_keys();

	if ( isset( $data[ "billing_{$field}" ] ) && in_array( "billing_{$field}", $meta_keys, true ) ) {
		$billing_value  = $data[ "billing_{$field}" ];
		$shipping_value = $data[ "billing_{$field}" ];
	}

	if ( isset( $data[ "shipping_{$field}" ] ) && is_callable( "risecheckout_customer_set_shipping_{$field}" ) ) {
		$shipping_value = $data[ "shipping_{$field}" ];
	}

	if ( ! is_null( $billing_value ) && in_array( "billing_{$field}", $meta_keys, true ) ) {
		risecheckout_customer_set_meta( "billing_{$field}", $billing_value );
	}

	if ( ! is_null( $shipping_value ) && in_array( "shipping_{$field}", $meta_keys, true ) ) {
		risecheckout_customer_set_meta( "shipping_{$field}", $shipping_value );
	}
}

/*
 * Sets meta data for the customer with optional filtering
 *
 * @param string $meta Meta key
 * @param mixed $value Meta value
 */
function risecheckout_customer_set_meta( $meta, $value ) {
	if ( ! in_array( $meta, risecheckout_customer_custom_meta_keys(), true ) ) {
		return;
	}

	$value = apply_filters( "risecheckout_set_meta_{$meta}", $value );

	if ( $value ) {
		WC()->customer->update_meta_data( $meta, sanitize_text_field( $value ) );
	}
}

/*
 * Sanitizes CPF field with validation
 *
 * @param string $cpf CPF value
 * @return string Sanitized CPF or empty string if invalid
 */
function risecheckout_sanitize_cpf( $cpf ) {
	return risecheckout_is_valid_cpf( $cpf ) ? risecheckout_format_cpf( $cpf ) : '';
}
add_filter( 'risecheckout_set_meta_billing_cpf', 'risecheckout_sanitize_cpf' );
