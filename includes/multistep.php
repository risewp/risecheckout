<?php

function risecheckout_checkout_steps_remove_default_start() {
	ob_start();
}

function risecheckout_checkout_steps_remove_default_clean() {
	$output = ob_get_clean();
	$output = preg_replace( '/(class)(=")customer-details(")/', '$1$2checkout-steps-col$3', $output );
	$output = str_replace( ' id="customer_details"', '', $output );
	echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

function risecheckout_remove_billing_shipping_forms() {
	if ( ! risecheckout_get_steps() ) {
		return;
	}
	add_action( 'woocommerce_checkout_before_customer_details', 'risecheckout_checkout_steps_remove_default_start' );
	add_action( 'woocommerce_checkout_after_customer_details', 'risecheckout_checkout_steps_remove_default_clean' );

	remove_action( 'woocommerce_checkout_billing', array( WC()->checkout(), 'checkout_form_billing' ) );
	remove_action( 'woocommerce_checkout_shipping', array( WC()->checkout(), 'checkout_form_shipping' ) );

	add_action( 'woocommerce_checkout_billing', 'risecheckout_checkout_form_steps', 11 );

	remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
	add_action( 'risecheckout_step_payment', 'woocommerce_checkout_payment' );
}
add_action( 'woocommerce_init', 'risecheckout_remove_billing_shipping_forms' );

function risecheckout_checkout_form_steps() {
	wc_get_template( 'checkout/form-steps.php', array( 'checkout' => WC()->checkout() ) );
}


function risecheckout_get_step( $step_key ) {
	$step  = array();
	$steps = risecheckout_get_steps();
	if ( isset( $steps[ $step_key ] ) ) {
		$step = (object) $steps[ $step_key ];
	}
	if ( ! isset( $step->title ) ) {
		$step->title = false;
	}
	if ( ! isset( $step->desc ) ) {
		$step->desc = false;
	}
	if ( ! isset( $step->placeholder ) ) {
		$step->placeholder = false;
	}

	$id = 'risecheckout_step_' . str_replace( '-', '_', sanitize_title( $step_key ) );

	$step->key     = $step_key;
	$step->id      = $id;
	$step->classes = array( str_replace( '_', '-', $id ) );
	return (array) $step;
}

function risecheckout_get_fields( $step_key ) {
	$checkout_fields = WC()->checkout()->get_checkout_fields();

	$merged = array();
	foreach ( $checkout_fields as $type => $fields ) {
		$merged = array_merge( $merged, $fields );
	}

	$checkout_fields = $merged;

	$fields = array();

	$step = (object) risecheckout_get_step( $step_key );

	if ( isset( $step->fields ) ) {
		foreach ( $step->fields as $key ) {
			if ( ! isset( $checkout_fields[ $key ] ) ) {
				continue;
			}
			$fields[ $key ] = $checkout_fields[ $key ];
		}
	}

	return $fields;
}

function risecheckout_checkout_form_step( $step_key, $index ) {
	$step = (object) risecheckout_get_step( $step_key );

	$step->fields = risecheckout_get_fields( $step_key );

	if ( 0 === $index ) {
		$step->classes[] = 'active';
	}

	wc_get_template(
		'checkout/form-step.php',
		array(
			'checkout' => WC()->checkout(),
			'step'     => $step,
		)
	);
}
add_action( 'risecheckout_step', 'risecheckout_checkout_form_step', 10, 2 );

function risecheckout_get_steps() {
	$steps = array(
		'customer' => array(
			'title'  => __( 'Identify yourself', 'risecheckout' ),
			'desc'   => __(
				'We will use your email to: Identify your profile, purchase history, order notification and shopping cart.',
				'risecheckout'
			),
			'fields' => array(
				'billing_first_name',
				'billing_last_name',
				'billing_email',
				'billing_cpf',
				'billing_phone',
			),
		),
		'shipping' => array(
			'title'       => __( 'Shipping', 'risecheckout' ),
			'desc'        => __(
				'Register an address',
				'risecheckout'
			),
			'placeholder' => __(
				'Fill in your personal information to continue',
				'risecheckout'
			),
			'fields'      => array(
				'billing_country',
				'billing_postcode',
				'billing_city',
				'billing_state',
				'billing_address_1',
				'billing_number',
				'billing_neighborhood',
				'billing_address_2',
			),
		),
		'payment'  => array(
			'title'       => __( 'Payment', 'risecheckout' ),
			'placeholder' => __( 'Fill in your shipping information to continue', 'risecheckout' ),
		),
	);

	$steps = apply_filters( 'risecheckout_steps', $steps );

	return $steps;
}

function risecheckout_multistep_body_class( $classes ) {
	if ( risecheckout_option( 'multistep', 'no' ) && risecheckout_get_steps() ) {
		$classes[] = 'risecheckout-multistep';
	}

	$form_columns = risecheckout_option( 'form_columns', 'no' );
	if ( risecheckout_is_checkout() && ! risecheckout_is_order_received_page() && $form_columns ) {
		$classes[] = 'checkout-form-columns';
	}

	return $classes;
}
add_action( 'body_class', 'risecheckout_multistep_body_class' );

function risecheckout_order_review_inner() {
	echo '<div class="order-review-inner">';
}
add_action( 'woocommerce_checkout_before_order_review_heading', 'risecheckout_order_review_inner' );

function risecheckout_order_review_inner_close() {
	echo '</div><!-- /.order-review-inner -->';
}
add_action( 'woocommerce_checkout_after_order_review', 'risecheckout_order_review_inner_close' );

function risecheckout_order_review_heading( $translation, $text ) {
	$multistep = risecheckout_option( 'multistep', 'no' ) && risecheckout_get_steps();

	if ( $multistep && 'Your order' === $text ) {
		$translation = _x( 'Resume', 'order', 'risecheckout' );
	}

	return $translation;
}
add_filter( 'gettext_woocommerce', 'risecheckout_order_review_heading', 10, 2 );

function risecheckout_step_next_button( $step_key ) {
	$text = 'shipping' === $step_key ? __( 'Save', 'risecheckout' ) : __( 'Continue', 'risecheckout' );
	printf(
		'<button type="submit" class="button" name="step" value="%s" style="display:none">%s</button>',
		esc_attr( $step_key ),
		esc_html( $text )
	);
}
add_action( 'risecheckout_after_step_customer', 'risecheckout_step_next_button' );
add_action( 'risecheckout_after_step_shipping', 'risecheckout_step_next_button' );
