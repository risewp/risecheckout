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
function risecheckout_get_defaults( $step ) {
	$step = (object) $step;
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

	return (array) $step;
}

function risecheckout_wc_checkout_maybe_skip_fieldset( $fieldset_key, $data ) {
	$checkout = WC()->checkout();
	if ( 'shipping' === $fieldset_key && ( ! $data['ship_to_different_address'] || ! WC()->cart->needs_shipping_address() ) ) {
		return true;
	}

	if ( 'account' === $fieldset_key && ( is_user_logged_in() || ( ! $checkout->is_registration_required() && empty( $data['createaccount'] ) ) ) ) {
		return true;
	}

	return false;
}

function risecheckout_wc_checkout_validate_posted_data( &$data, &$errors ) {
	$checkout = WC()->checkout();
	foreach ( $checkout->get_checkout_fields() as $fieldset_key => $fieldset ) {
		$validate_fieldset = true;
		if ( risecheckout_wc_checkout_maybe_skip_fieldset( $fieldset_key, $data ) ) {
			$validate_fieldset = false;
		}

		$step_key = isset( $data['step'] ) ? $data['step'] : false;
		$fieldset = risecheckout_get_fields( $step_key );
		foreach ( $fieldset as $key => $field ) {
			if ( ! isset( $data[ $key ] ) ) {
				continue;
			}
			if ( preg_match( '/^billing_/', $key ) ) {
				$fieldset_key = 'billing';
			} elseif ( preg_match( '/^shipping_/', $key ) ) {
				$fieldset_key = 'shipping';
			}

			$required    = ! empty( $field['required'] );
			$format      = array_filter( isset( $field['validate'] ) ? (array) $field['validate'] : array() );
			$field_label = isset( $field['label'] ) ? $field['label'] : '';

			// phpcs:disable WordPress.WP.I18n.TextDomainMismatch,WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			if ( $validate_fieldset &&
				( isset( $field['type'] ) && 'country' === $field['type'] && '' !== $data[ $key ] ) &&
				! WC()->countries->country_exists( $data[ $key ] ) ) {
					/* translators: ISO 3166-1 alpha-2 country code */
					$errors->add( $key . '_validation', sprintf( __( "'%s' is not a valid country code.", 'woocommerce' ), $data[ $key ] ) );
			}

			switch ( $fieldset_key ) {
				case 'shipping':
					/* translators: %s: field name */
					$field_label = sprintf( _x( 'Shipping %s', 'checkout-validation', 'woocommerce' ), $field_label );
					break;
				case 'billing':
					/* translators: %s: field name */
					$field_label = sprintf( _x( 'Billing %s', 'checkout-validation', 'woocommerce' ), $field_label );
					break;
			}

			if ( in_array( 'postcode', $format, true ) ) {
				$country      = isset( $data[ $fieldset_key . '_country' ] ) ? $data[ $fieldset_key . '_country' ] : WC()->customer->{"get_{$fieldset_key}_country"}();
				$data[ $key ] = wc_format_postcode( $data[ $key ], $country );

				if ( $validate_fieldset && '' !== $data[ $key ] && ! WC_Validation::is_postcode( $data[ $key ], $country ) ) {
					switch ( $country ) {
						case 'IE':
							/* translators: %1$s: field name, %2$s finder.eircode.ie URL */
							$postcode_validation_notice = sprintf( __( '%1$s is not valid. You can look up the correct Eircode <a target="_blank" href="%2$s">here</a>.', 'woocommerce' ), '<strong>' . esc_html( $field_label ) . '</strong>', 'https://finder.eircode.ie' );
							break;
						default:
							/* translators: %s: field name */
							$postcode_validation_notice = sprintf( __( '%s is not a valid postcode / ZIP.', 'woocommerce' ), '<strong>' . esc_html( $field_label ) . '</strong>' );
					}
					$errors->add( $key . '_validation', apply_filters( 'woocommerce_checkout_postcode_validation_notice', $postcode_validation_notice, $country, $data[ $key ] ), array( 'id' => $key ) );
				}
			}

			if ( in_array( 'phone', $format, true ) ) {
				$data[ $key ] = wc_sanitize_phone_number( $data[ $key ] );

				if ( $validate_fieldset && '' !== $data[ $key ] && ! WC_Validation::is_phone( $data[ $key ] ) ) {
					/* translators: %s: phone number */
					$errors->add( $key . '_validation', sprintf( __( '%s is not a valid phone number.', 'woocommerce' ), '<strong>' . esc_html( $field_label ) . '</strong>' ), array( 'id' => $key ) );
				}
			}

			if ( in_array( 'email', $format, true ) && '' !== $data[ $key ] ) {
				$email_is_valid = is_email( $data[ $key ] );
				$data[ $key ]   = sanitize_email( $data[ $key ] );

				if ( $validate_fieldset && ! $email_is_valid ) {
					/* translators: %s: email address */
					$errors->add( $key . '_validation', sprintf( __( '%s is not a valid email address.', 'woocommerce' ), '<strong>' . esc_html( $field_label ) . '</strong>' ), array( 'id' => $key ) );
					continue;
				}
			}

			if ( '' !== $data[ $key ] && in_array( 'state', $format, true ) ) {
				$country      = isset( $data[ $fieldset_key . '_country' ] ) ? $data[ $fieldset_key . '_country' ] : WC()->customer->{"get_{$fieldset_key}_country"}();
				$valid_states = WC()->countries->get_states( $country );

				if ( ! empty( $valid_states ) && is_array( $valid_states ) && count( $valid_states ) > 0 ) {
					$valid_state_values = array_map( 'wc_strtoupper', array_flip( array_map( 'wc_strtoupper', $valid_states ) ) );
					$data[ $key ]       = wc_strtoupper( $data[ $key ] );

					if ( isset( $valid_state_values[ $data[ $key ] ] ) ) {
						// With this part we consider state value to be valid as well, convert it to the state key for the valid_states check below.
						$data[ $key ] = $valid_state_values[ $data[ $key ] ];
					}

					if ( $validate_fieldset && ! in_array( $data[ $key ], $valid_state_values, true ) ) {
						/* translators: 1: state field 2: valid states */
						$errors->add( $key . '_validation', sprintf( __( '%1$s is not valid. Please enter one of the following: %2$s', 'woocommerce' ), '<strong>' . esc_html( $field_label ) . '</strong>', implode( ', ', $valid_states ) ), array( 'id' => $key ) );
					}
				}
			}

			if ( $validate_fieldset && $required && '' === $data[ $key ] ) {
				/* translators: %s: field name */
				$errors->add( $key . '_required', apply_filters( 'woocommerce_checkout_required_field_notice', sprintf( __( '%s is a required field.', 'woocommerce' ), '<strong>' . esc_html( $field_label ) . '</strong>' ), $field_label, $key ), array( 'id' => $key ) );
			}
			// phpcs:enable WordPress.WP.I18n.TextDomainMismatch,WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		}
	}
}

function risecheckout_step_done( $step ) {
	$errors = new WP_Error();
	if ( isset( $step->fields ) && $step->fields ) {
		$checkout = WC()->checkout();

		$data = array();
		foreach ( $step->fields as $key ) {
			$value = $checkout->get_value( $key );
			if ( is_null( $value ) ) {
				$value = '';
			}
			$data[ $key ] = $value;
		}
		if ( ! isset( $data['ship_to_different_address'] ) ) {
			$data['ship_to_different_address'] = false;
		}
		$data['step'] = $step->key;

		risecheckout_wc_checkout_validate_posted_data( $data, $errors );
	}
	return empty( $errors->errors );
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
	foreach ( $checkout_fields as $fieldset => $fields ) {
		foreach ($fields as $key => $field) {
			$fields[$key]['fieldset'] = $fieldset;
		}
		$merged = array_merge( $merged, $fields );
	}
	$checkout_fields = $merged;

	$fields = array();

	$steps = risecheckout_get_steps();

	$step = (object) $steps[ $step_key ];
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
 * @return void
 */
function risecheckout_checkout_form_step( $step_key ) {
	$steps  = risecheckout_get_steps();
	$active = false;
	foreach ( $steps as $key => $step ) {
		$step = (object) $step;

		if ( risecheckout_step_done( $step ) ) {
			$step->classes[] = 'done';
		} elseif ( ! $active ) {
			$active          = true;
			$step->classes[] = 'active';
		}

		$steps[ $key ] = $step;
	}

	$step = (object) risecheckout_get_defaults( $steps[ $step_key ] );

	// Retrieve and assign the fields for the current step.
	$step->fields = risecheckout_get_fields( $step_key );

	// Load the checkout step template.
	wc_get_template(
		'checkout/form-step.php',
		array(
			'checkout' => WC()->checkout(),
			'step'     => $step,
		)
	);
}
add_action( 'risecheckout_step', 'risecheckout_checkout_form_step' );

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
		'address'  => array(
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

function risecheckout_steps_defaults( $steps ) {
	foreach ( $steps as $step_key => $step ) {
		$step = (object) $step;

		$id = 'risecheckout_step_' . str_replace( '-', '_', sanitize_title( $step_key ) );

		$step->key = $step_key;
		$step->id  = $id;
		if ( isset( $step->classes ) ) {
			$step->classes = array();
		}
		$step->classes[] = str_replace( '_', '-', $id );

		$steps[ $step_key ] = (array) $step;
	}
	return $steps;
}
add_filter( 'risecheckout_steps', 'risecheckout_steps_defaults' );

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
	if ( isset( $step->fields ) && $step->fields ) :
		$checkout = WC()->checkout();
		?>
		<div class="step-fields">
			<?php
			// Loop through and output each field.
			foreach ( $step->fields as $key => $field ) {
				woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
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
	// Only display the button for 'customer' or 'address' steps.
	if ( 'payment' === $step->key ) {
		return;
	}

	// Use "Save" for the address step; "Continue" otherwise.
	$text = 'address' === $step->key ? __( 'Save', 'risecheckout' ) : __( 'Continue', 'risecheckout' );

	// Output a hidden submit button for the step.
	printf(
		'<button type="submit" class="button" name="risecheckout_place_step" value="%s" style="display:none">%s</button>',
		esc_attr( $step->key ),
		esc_html( $text )
	);

	wp_nonce_field( "risecheckout-process_{$step->key}", "risecheckout-process-{$step->key}-nonce" );
}
add_action( 'risecheckout_step_content', 'risecheckout_step_button', 50 );

function risecheckout_step_payment( $step ) {
	if ( 'payment' === $step->key ) {
		woocommerce_checkout_payment();
	}
}
add_action( 'risecheckout_step_content', 'risecheckout_step_payment', 60 );

function risecheckout_step_action() {
	if ( isset( $_POST['risecheckout_place_step'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		wc_nocache_headers();

		if ( WC()->cart->is_empty() ) {
			wp_safe_redirect( wc_get_cart_url() );
			exit;
		}

		wc_maybe_define_constant( 'RISECHECKOUT_STEP', true );

		risecheckout_process();
	}
}
add_action( 'wp_loaded', 'risecheckout_step_action', 20 );

function risecheckout_get_posted_data() {
	$data = [
		'step' => isset( $_POST['risecheckout_place_step'] ) ? wc_clean( wp_unslash( $_POST['risecheckout_place_step'] ) ) : ''
	];
	foreach ( risecheckout_get_fields( $data['step'] ) as $key => $field ) {
		$fieldset_key = isset($field['fieldset']) ? $field['fieldset'] : '';

		$type = sanitize_title( isset( $field['type'] ) ? $field['type'] : 'text' );

		if ( isset( $_POST[ $key ] ) && '' !== $_POST[ $key ] ) {
			$value = wp_unslash( $_POST[ $key ] );
		} else {
			$value = '';
		}

		if ( '' !== $value ) {
			switch ( $type ) {
				case 'checkbox':
					$value = 1;
					break;
				case 'multiselect':
					$value = implode( ', ', wc_clean( $value ) );
					break;
				case 'textarea':
					$value = wc_sanitize_textarea( $value );
					break;
				case 'password':
					if ( $data['createaccount'] && 'account_password' === $key ) {
						$value = wp_slash( $value );
					}
					break;
				default:
					$value = wc_clean( $value );
					break;
			}
		}

		$data[ $key ] = apply_filters( 'risecheckout_process_checkout_' . $type . '_field', apply_filters( 'risecheckout_process_checkout_field_' . $key, $value ) );
	}
	return apply_filters( 'risecheckout_checkout_posted_data', $data );
}

function risecheckout_process() {
	$checkout = WC()->checkout();
	try {
		$step_key = isset( $_POST['risecheckout_place_step'] ) ? sanitize_text_field( $_POST['risecheckout_place_step'] ) : '';

		$nonce_value    = wc_get_var( $_REQUEST[ "risecheckout-process-{$step_key}-nonce" ], wc_get_var( $_REQUEST['_wpnonce'], '' ) );
		$expiry_message = sprintf(
			/* translators: %s: shop cart url */
			__( 'Sorry, your session has expired. <a href="%s" class="wc-backward">Return to shop</a>', 'woocommerce' ),
			esc_url( wc_get_page_permalink( 'shop' ) )
		);

		if ( empty( $nonce_value ) || ! wp_verify_nonce( $nonce_value, "risecheckout-process_{$step_key}" ) ) {
			// If the cart is empty, the nonce check failed because of session expiry.
			if ( WC()->cart->is_empty() ) {
				throw new Exception( $expiry_message );
			}

			WC()->session->set( 'refresh_totals', true );
			throw new Exception( __( 'We were unable to process your order, please try again.', 'woocommerce' ) );
		}

		$errors      = new WP_Error();
		$posted_data = risecheckout_get_posted_data();

		// risecheckout_wc_checkout_validate_posted_data( $posted_data, $errors );

		if ( ! wp_doing_ajax() ) {
			return;
		}

		wp_send_json(
			array(
				'result'      => 'success',
				'posted_data' => $posted_data,
				'fragments'   => [],
			)
		);
	} catch ( Exception $e ) {
		wc_add_notice( $e->getMessage(), 'error' );
	}
	risecheckout_send_ajax_failure_response();
}

function risecheckout_send_ajax_failure_response() {
	if ( wp_doing_ajax() ) {
		// Only print notices if not reloading the checkout, otherwise they're lost in the page reload.
		if ( ! isset( WC()->session->reload_checkout ) ) {
			$messages = wc_print_notices( true );
		}

		$response = array(
			'result'   => 'failure',
			'messages' => isset( $messages ) ? $messages : '',
			'refresh'  => isset( WC()->session->refresh_totals ),
			'reload'   => isset( WC()->session->reload_checkout ),
		);

		unset( WC()->session->refresh_totals, WC()->session->reload_checkout );

		wp_send_json( $response );
	}
}
