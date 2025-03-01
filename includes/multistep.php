<?php
/**
 * Start output buffering for checkout steps.
 *
 * @return void
 */
function risecheckout_checkout_steps_remove_default_start() {
	// Begin capturing output.
	ob_start();
}

/**
 * Clean and modify the checkout steps output.
 *
 * This function cleans the output buffer and replaces the default customer details class
 * with a custom one. It also removes the default customer details ID.
 *
 * @return void
 */
function risecheckout_checkout_steps_remove_default_clean() {
	$output = ob_get_clean();

	// Replace "customer-details" with "checkout-steps-col" in the class attribute.
	$output = preg_replace( '/(class)(=")customer-details(")/', '$1$2checkout-steps-col$3', $output );
	// Remove the customer_details ID.
	$output = str_replace( ' id="customer_details"', '', $output );
	// Output the modified HTML. phpcs ignore comment added to avoid warnings about unescaped output.
	echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Remove default billing and shipping forms and add custom checkout steps.
 *
 * Hooks into various WooCommerce checkout actions to remove default billing/shipping
 * templates and insert custom step templates.
 *
 * @return void
 */
function risecheckout_remove_billing_shipping_forms() {
	// If there are no custom steps configured, exit early.
	if ( ! risecheckout_get_steps() ) {
		return;
	}

	// Start output buffering before the customer details section.
	add_action( 'woocommerce_checkout_before_customer_details', 'risecheckout_checkout_steps_remove_default_start' );
	// Clean and modify the output after the customer details section.
	add_action( 'woocommerce_checkout_after_customer_details', 'risecheckout_checkout_steps_remove_default_clean' );

	// Remove the default billing and shipping forms.
	remove_action( 'woocommerce_checkout_billing', array( WC()->checkout(), 'checkout_form_billing' ) );
	remove_action( 'woocommerce_checkout_shipping', array( WC()->checkout(), 'checkout_form_shipping' ) );

	// Add the custom checkout form steps template.
	add_action( 'woocommerce_checkout_billing', 'risecheckout_checkout_form_steps', 11 );

	// Reassign payment section to a custom hook.
	remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
	add_action( 'risecheckout_step_payment', 'woocommerce_checkout_payment' );
}
add_action( 'woocommerce_init', 'risecheckout_remove_billing_shipping_forms' );

/**
 * Load the custom checkout form steps template.
 *
 * @return void
 */
function risecheckout_checkout_form_steps() {
	wc_get_template( 'checkout/form-steps.php', array( 'checkout' => WC()->checkout() ) );
}

/**
 * Retrieve data for a specific checkout step.
 *
 * Fetches the step configuration by its key, setting defaults for missing values,
 * and adds a unique identifier and CSS classes.
 *
 * @param string $step_key The identifier for the checkout step.
 * @return array The checkout step data as an associative array.
 */
function risecheckout_get_step( $step_key ) {
	$step  = array();
	$steps = risecheckout_get_steps();

	if ( isset( $steps[ $step_key ] ) ) {
		$step = (object) $steps[ $step_key ];
	}

	// Set default values if not defined.
	if ( ! isset( $step->title ) ) {
		$step->title = false;
	}
	if ( ! isset( $step->desc ) ) {
		$step->desc = false;
	}
	if ( ! isset( $step->placeholder ) ) {
		$step->placeholder = false;
	}

	// Create a unique ID for the step.
	$id = 'risecheckout_step_' . str_replace( '-', '_', sanitize_title( $step_key ) );

	$step->key     = $step_key;
	$step->id      = $id;
	$step->classes = array( str_replace( '_', '-', $id ) );

	return (array) $step;
}

/**
 * Retrieve the checkout fields for a given step.
 *
 * Merges all WooCommerce checkout fields and returns only those configured for the specific step.
 *
 * @param string $step_key The identifier for the checkout step.
 * @return array Array of checkout fields for the step.
 */
function risecheckout_get_fields( $step_key ) {
	$checkout_fields = WC()->checkout()->get_checkout_fields();

	// Merge all fields from different groups into a single array.
	$merged = array();
	foreach ( $checkout_fields as $type => $fields ) {
		$merged = array_merge( $merged, $fields );
	}
	$checkout_fields = $merged;

	$fields = array();
	$step   = (object) risecheckout_get_step( $step_key );

	// If the step has specific fields defined, loop through and add them.
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

/**
 * Render a single checkout form step.
 *
 * Loads the template for the given checkout step, passing step data and checkout instance.
 *
 * @param string $step_key The identifier for the checkout step.
 * @param int    $index    The index position of the step in the checkout process.
 * @return void
 */
function risecheckout_checkout_form_step( $step_key, $index ) {
	$step = (object) risecheckout_get_step( $step_key );

	// Retrieve and assign the fields for the current step.
	$step->fields = risecheckout_get_fields( $step_key );

	// Mark the first step as active.
	if ( 0 === $index ) {
		$step->classes[] = 'active';
	}

	// Load the checkout step template.
	wc_get_template(
		'checkout/form-step.php',
		array(
			'checkout' => WC()->checkout(),
			'step'     => $step,
		)
	);
}
add_action( 'risecheckout_step', 'risecheckout_checkout_form_step', 10, 2 );

/**
 * Retrieve all checkout steps configuration.
 *
 * Defines the steps for the checkout process and allows modifications via a filter.
 *
 * @return array The checkout steps configuration.
 */
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
			'desc'        => __( 'Register an address', 'risecheckout' ),
			'placeholder' => __( 'Fill in your personal information to continue', 'risecheckout' ),
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

	// Allow custom modifications of the checkout steps.
	$steps = apply_filters( 'risecheckout_steps', $steps );

	return $steps;
}

/**
 * Add custom body classes for multi-step checkout.
 *
 * Appends classes if multi-step checkout or form columns are enabled.
 *
 * @param array $classes Existing body classes.
 * @return array Modified array of body classes.
 */
function risecheckout_multistep_body_class( $classes ) {
	// Add multi-step class if enabled and steps exist.
	if ( risecheckout_option( 'multistep', 'no' ) && risecheckout_get_steps() ) {
		$classes[] = 'risecheckout-multistep';
	}

	// Add class for form columns if applicable.
	$form_columns = risecheckout_option( 'form_columns', 'no' );
	if ( risecheckout_is_checkout() && ! risecheckout_is_order_received_page() && $form_columns ) {
		$classes[] = 'checkout-form-columns';
	}

	return $classes;
}
add_action( 'body_class', 'risecheckout_multistep_body_class' );

/**
 * Output the opening wrapper for the order review section.
 *
 * @return void
 */
function risecheckout_order_review_inner() {
	echo '<div class="order-review-inner">';
}
add_action( 'woocommerce_checkout_before_order_review_heading', 'risecheckout_order_review_inner' );

/**
 * Output the closing wrapper for the order review section.
 *
 * @return void
 */
function risecheckout_order_review_inner_close() {
	echo '</div><!-- /.order-review-inner -->';
}
add_action( 'woocommerce_checkout_after_order_review', 'risecheckout_order_review_inner_close' );

/**
 * Modify the order review heading text for multi-step checkout.
 *
 * Changes "Your order" to "Resume" when multi-step checkout is active.
 *
 * @param string $translation The current translated text.
 * @param string $text        The original text.
 * @return string The modified translation.
 */
function risecheckout_order_review_heading( $translation, $text ) {
	$multistep = risecheckout_option( 'multistep', 'no' ) && risecheckout_get_steps();

	if ( $multistep && 'Your order' === $text ) {
		$translation = _x( 'Resume', 'order', 'risecheckout' );
	}

	return $translation;
}
add_filter( 'gettext_woocommerce', 'risecheckout_order_review_heading', 10, 2 );

/**
 * Output the title for a checkout step.
 *
 * @param object $step The checkout step object.
 * @return void
 */
function risecheckout_step_title( $step ) {
	if ( $step->title ) {
		echo wp_kses_post( '<p class="h3">' . esc_html( $step->title ) . '</p>' );
	}
}
add_action( 'risecheckout_step_content', 'risecheckout_step_title', 10 );

/**
 * Output the description for a checkout step.
 *
 * @param object $step The checkout step object.
 * @return void
 */
function risecheckout_step_desc( $step ) {
	if ( $step->desc ) {
		echo wp_kses_post( '<p class="desc">' . esc_html( $step->desc ) . '</p>' );
	}
}
add_action( 'risecheckout_step_content', 'risecheckout_step_desc', 20 );

/**
 * Output the placeholder for a checkout step.
 *
 * @param object $step The checkout step object.
 * @return void
 */
function risecheckout_step_placeholder( $step ) {
	if ( $step->placeholder ) {
		echo wp_kses_post( '<p class="placeholder">' . esc_html( $step->placeholder ) . '</p>' );
	}
}
add_action( 'risecheckout_step_content', 'risecheckout_step_placeholder', 30 );

/**
 * Render the fields for a checkout step.
 *
 * Loops through each field of the step and renders it using WooCommerce form field function.
 *
 * @param object $step The checkout step object.
 * @return void
 */
function risecheckout_step_fields( $step ) {
	if ( $step->fields ) :
		?>
		<div class="step-fields">
			<?php
			// Loop through and output each field.
			foreach ( $step->fields as $key => $field ) {
				woocommerce_form_field( $key, $field, WC()->checkout()->get_value( $key ) );
			}
			?>
		</div>
		<?php
	endif;
}
add_action( 'risecheckout_step_content', 'risecheckout_step_fields', 40 );

/**
 * Output the navigation button for a checkout step.
 *
 * Renders a hidden button for steps with the key "customer" or "shipping".
 *
 * @param object $step The checkout step object.
 * @return void
 */
function risecheckout_step_button( $step ) {
	// Only display the button for 'customer' or 'shipping' steps.
	if ( ! in_array( $step->key, array( 'customer', 'shipping' ), true ) ) {
		return;
	}

	// Use "Save" for the shipping step; "Continue" otherwise.
	$text = 'shipping' === $step->key ? __( 'Save', 'risecheckout' ) : __( 'Continue', 'risecheckout' );

	// Output a hidden submit button for the step.
	printf(
		'<button type="submit" class="button" name="step" value="%s" style="display:none">%s</button>',
		esc_attr( $step->key ),
		esc_html( $text )
	);
}
add_action( 'risecheckout_step_content', 'risecheckout_step_button', 50 );
