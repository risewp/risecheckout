<?php
/**
 * Plugin Name: Rise Checkout
 * Description: Enhanced WooCommerce checkout with a seamless single-page multi-step with tracking for abandoned carts.
 * Version: 1.0.0
 * Author: RiseWP
 * Author URI: https://risewp.github.io
 * Requires at least: 6.7.2
 * Requires PHP: 7.4
 */

defined( 'ABSPATH' ) || exit;

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

	// echo '<pre>';
	// print_r($fields);
	// die;
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
	// $fields['billing']['billing_state']['priority'] = 46;
	$fields['billing']['billing_state']['break'] = true;

	$fields['billing']['billing_city']['class'][] = 'city-field';
	// $fields['billing']['billing_city']['priority'] = 47;

	$fields['billing']['billing_number']['class'][] = 'number-field';

	$fields['billing']['billing_neighborhood']['class'][]  = 'neighborhood-field';
	$fields['billing']['billing_neighborhood']['priority'] = 56;

	$fields['shipping']['shipping_postcode']['class'][] = 'postcode-field';

	$fields['shipping']['shipping_state']['class'][] = 'state-field';
	// $fields['shipping']['shipping_state']['priority'] = 46;
	$fields['shipping']['shipping_state']['break'] = true;

	$fields['shipping']['shipping_city']['class'][] = 'city-field';
	// $fields['shipping']['shipping_city']['priority'] = 47;

	$fields['shipping']['shipping_address_1']['class']   = array_diff(
		$fields['shipping']['shipping_address_1']['class'],
		array( 'form-row-last' )
	);
	$fields['shipping']['shipping_address_1']['class'][] = 'form-row-wide';

	$fields['shipping']['shipping_number']['class'][] = 'number-field';

	$fields['shipping']['shipping_neighborhood']['class'][] = 'neighborhood-field';
	// $fields['shipping']['shipping_neighborhood']['priority'] = 56;

	$fields['shipping']['shipping_address_2']['class']   = array_diff(
		$fields['shipping']['shipping_address_2']['class'],
		array( 'form-row-last' )
	);
	$fields['shipping']['shipping_address_2']['class'][] = 'form-row-wide';

	foreach ( $fields as $type => $type_fields ) {
		uasort( $fields[ $type ], 'wc_checkout_fields_uasort_comparison' );
	}

	// unset(
	//  $fields['billing']['billing_first_name'],
	//  $fields['billing']['billing_last_name'],
	//  $fields['billing']['billing_email'],
	//  $fields['billing']['billing_cpf'],
	//  $fields['billing']['billing_phone']
	// );
	// echo '<pre>';
	// print_r($fields);
	// die;
	return $fields;
}

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
	// echo '<pre>';
	// print_r($locale);
	// die;
	return $locale;
}
add_filter( 'woocommerce_get_country_locale', 'risecheckout_wc_get_country_locale', 11 );

function risecheckout_wc_fields_shippingpartial( $fields ) {
	$new_fields = array();

	$renames = array(
		'billing_country'       => 'country',
		'billing_name'          => 'name',
		'billing_first_name'    => 'firstname',
		'billing_last_name'     => 'lastname',
		'billing_email'         => 'email',
		'billing_cpf'           => 'cpf',
		'billing_phone'         => 'phone',
		'shipping_postcode'     => 'postcode',
		'shipping_city'         => 'city',
		'shipping_state'        => 'state',
		'shipping_address_1'    => 'address1',
		'shipping_number'       => 'number',
		'shipping_neighborhood' => 'neighborhood',
		'shipping_address_2'    => 'address2',
		'shipping_name'         => 'receiver',
		'shipping_first_name'   => 'receiver_firstname',
		'shipping_last_name'    => 'receiver_lastname',
	);

	$steps = array(
		'customer' => array(
			'firstname',
			'lastname',
			'email',
			'cpf',
			'phone',
		),
		'shipping' => array(
			'postcode',
			'city',
			'state',
			'address1',
			'number',
			'neighborhood',
			'address2',
			'receiver',
		),
	);

	if ( 'yes' === get_option( 'risecheckout_fullname', 'yes' ) ) {
		$fields['billing']['billing_name'] = array(
			'autocomplete' => 'name',
		);
		unset( $fields['billing']['billing_first_name'], $fields['billing']['billing_last_name'] );
		$fields['shipping']['shipping_name'] = array(
			'autocomplete' => 'name',
		);
		unset( $fields['shipping']['shipping_first_name'], $fields['shipping']['shipping_last_name'] );
	}

	foreach ( $fields as $type => $type_fields ) {
		foreach ( $type_fields as $name => $field ) {
			if ( isset( $renames[ $name ] ) ) {
				$field['renamed'] = $name;
				$name             = $renames[ $name ];
			}
			foreach ( $steps as $step => $step_fields ) {
				if ( in_array( $name, $step_fields, true ) ) {
					$field['step'] = $step;
					break;
				}
			}
			$new_fields[ $name ] = $field;
		}
	}
	$fields = $new_fields;

	$merge_fields = risecheckout_fields();

	foreach ( $fields as $name => &$field ) {
		if ( isset( $merge_fields[ $name ] ) ) {
			$field = array_merge( $field, $merge_fields[ $name ] );
		}
	}

	$new_fields = array();

	foreach ( $fields as $name => $field ) {
		if ( isset( $field['step'] ) ) {
			$field_step = $field['step'];
			unset( $field['step'] );
			$new_fields[ $field_step ][ $name ] = $field;
		}
	}

	$fields = $new_fields;
	unset( $field );

	foreach ( $fields as $field_step => $step_fields ) {
		uasort( $fields[ $field_step ], 'wc_checkout_fields_uasort_comparison' );
	}

	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'risecheckout_wc_fields' );

function risecheckout_fields() {
	$fields = array();
	if ( 'yes' === get_option( 'risecheckout_fullname', 'yes' ) ) {
		$fields['name'] = array(
			'label'       => __( 'Full name', 'risecheckout' ),
			'placeholder' => sprintf(
				/* translators: %s: Example */
				__( 'e.g.: %s', 'risecheckout' ),
				__( 'Mary Anne Johnson', 'risecheckout' )
			),
			'minlength'   => 5,
			'pattern'     => '[A-Za-zÀ-ÖØ-öø-ÿ]{2,}(\s+[A-Za-zÀ-ÖØ-öø-ÿ]{2,})+',
			'invalid'     => sprintf(
				/* translators: %s: Field label */
				__( 'Enter your %s', 'risecheckout' ),
				mb_strtolower( __( 'Full name', 'risecheckout' ) )
			),
			'required'    => true,
			'value'       => __( 'Mary Anne Johnson', 'risecheckout' ),
			'step'        => 'customer',
			'priority'    => 10,
			'info'        => true,
		);
	} else {
		$fields = array_merge(
			$fields,
			array(
				'firstname' => array(
					'class'       => array( 'col-6' ),
					'label'       => __( 'First name', 'risecheckout' ),
					'placeholder' => sprintf(
						/* translators: %s: Example */
						__( 'e.g.: %s', 'risecheckout' ),
						__( 'Mary', 'risecheckout' )
					),
					'minlength'   => 2,
					'pattern'     => '([A-Za-zÀ-ÖØ-öø-ÿ]{2,}(?: [A-Za-zÀ-ÖØ-öø-ÿ]+)*)',
					'invalid'     => sprintf(
						/* translators: %s: Field label */
						__( 'Enter your %s', 'risecheckout' ),
						mb_strtolower( __( 'First name', 'risecheckout' ) )
					),
					'required'    => true,
					'value'       => __( 'Mary', 'risecheckout' ),
					'step'        => 'customer',
					'priority'    => 5,
					'info'        => 'name',
					'info_label'  => __( 'Full name', 'risecheckout' ),
				),
				'lastname'  => array(
					'class'       => array( 'col-6' ),
					'label'       => __( 'Last name', 'risecheckout' ),
					'placeholder' => sprintf(
						/* translators: %s: Example */
						__( 'e.g.: %s', 'risecheckout' ),
						__( 'Johnson', 'risecheckout' )
					),
					'minlength'   => 2,
					'pattern'     => '([A-Za-zÀ-ÖØ-öø-ÿ]{2,}(?: [A-Za-zÀ-ÖØ-öø-ÿ]+)*)',
					'invalid'     => sprintf(
						/* translators: %s: Field label */
						__( 'Enter your %s', 'risecheckout' ),
						mb_strtolower( __( 'Last name', 'risecheckout' ) )
					),
					'required'    => true,
					'value'       => __( 'Johnson', 'risecheckout' ),
					'step'        => 'customer',
					'priority'    => 10,
					'info'        => 'name',
					'info_label'  => __( 'Full name', 'risecheckout' ),
				),
			)
		);
	}
	$fields = array_merge(
		$fields,
		array(
			'email'        => array(
				'label'       => __( 'Email', 'risecheckout' ),
				'class'       => array( 'col-12' ),
				'type'        => 'email',
				'placeholder' => sprintf(
					/* translators: %s: Example */
					__( 'e.g.: %s', 'risecheckout' ),
					sprintf( '%s@gmail.com', sanitize_title( __( 'Mary', 'risecheckout' ) ) )
				),
				'pattern'     => '[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}',
				'invalid'     => sprintf(
					/* translators: %s: Field label */
					__( 'Invalid %s. Please check if you typed it correctly.', 'risecheckout' ),
					__( 'Email', 'risecheckout' )
				),
				'required'    => true,
				'value'       => sprintf(
					'%s@gmail.com',
					sanitize_title( __( 'Mary', 'risecheckout' ) )
				),
				'step'        => 'customer',
				'priority'    => 20,
				'info'        => true,
			),
			'cpf'          => array(
				'label'       => 'CPF',
				'class'       => array( 'col-12' ),
				'placeholder' => '000.000.000-00',
				'minlength'   => 11,
				'maxlength'   => 14,
				'pattern'     => '\d{3}\.?\d{3}\.?\d{3}-?\d{2}',
				'validate'    => array( 'cpf' ),
				'mask'        => 'cpf',
				'clean'       => 'numbers',
				'invalid'     => sprintf(
					/* translators: %s: Field label */
					__( 'Enter a valid %s', 'risecheckout' ),
					'CPF'
				),
				'required'    => true,
				'value'       => '154.505.032-53',
				'step'        => 'customer',
				'priority'    => 30,
				'info_prefix' => true,
			),
			'phone'        => array(
				'label'       => __( 'Mobile', 'risecheckout' ),
				'class'       => array( 'col-12' ),
				'prefix'      => '+55',
				'placeholder' => '(00) 000000-0000',
				'minlength'   => 11,
				'maxlength'   => 15,
				'pattern'     => '\(?\d{2}\)?\s?\d{4,5}-?\d{4}',
				// 'pattern'     => '[\(\d\)\s-]+',
				'mask'        => 'phone-br',
				'clean'       => 'numbers',
				'invalid'     => sprintf(
					/* translators: %s: Field label */
					__( 'Enter a valid %s', 'risecheckout' ),
					mb_strtolower( __( 'Mobile', 'risecheckout' ) )
				),
				'required'    => true,
				'value'       => '(47) 98804-3272',
				'step'        => 'customer',
				'priority'    => 40,
			),
			'postcode'     => array(
				'class'     => array( 'col-7', 'postcode-field' ),
				// 'label'         => __( 'Postcode', 'risecheckout' ),
				'minlength' => 8,
				'maxlength' => 9,
				'pattern'   => '\d{5}-?\d{3}',
				'mask'      => 'postcode-br',
				'invalid'   => sprintf(
					/* translators: %s: Field label */
					__( 'Enter a valid %s', 'risecheckout' ),
					mb_strtolower( __( 'Zip', 'risecheckout' ) )
				),
				'required'  => true,
				'step'      => 'shipping',
				'priority'  => 50,
				'loading'   => true,
				// 'value'         => '89240-000',
				'value'     => '79814-054',
			),
			'city'         => array(
				'class'        => array( 'col-8', 'address-field' ),
				'priority'     => 60,
				'column_break' => true,
				// 'value'    => 'São Francisco do Sul',
				'value'        => 'Dourados',
			),
			'state'        => array(
				'class'    => array( 'col-4', 'address-field' ),
				'priority' => 65,
				// 'value'    => 'SC',
				'value'    => 'MS',
			),
			'address1'     => array(
				'class'       => array( 'col-12', 'address-field' ),
				'placeholder' => '',
				'priority'    => 70,
				'value'       => 'Rua Edgar Xavier de Matos',
			),
			'number'       => array(
				'class'    => array( 'col-4', 'address-field' ),
				'priority' => 80,
				'value'    => 256,
			),
			'neighborhood' => array(
				'class'    => array( 'col-8', 'address-field' ),
				'priority' => 85,
				'value'    => 'Jardim Itália',
			),
			'address2'     => array(
				'class'       => array( 'col-12', 'address-field' ),
				'placeholder' => '',
				'priority'    => 90,

				// 'value'    => 'SC',  'value' => 'Ap101',
			),
			'receiver'     => array(
				'label'       => __( 'Receiver', 'risecheckout' ),
				'class'       => array( 'col-12', 'address-field' ),
				'placeholder' => sprintf(
					/* translators: %s: Example */
					__( 'e.g.: %s', 'risecheckout' ),
					__( 'Mary Anne Johnson', 'risecheckout' )
				),
				'minlength'   => 5,
				'pattern'     => '[A-Za-zÀ-ÖØ-öø-ÿ]{2,}(\s+[A-Za-zÀ-ÖØ-öø-ÿ]{2,})+',
				'invalid'     => sprintf(
					/* translators: %s: Field label */
					__( 'Enter %s', 'risecheckout' ),
					mb_strtolower( __( 'Receiver', 'risecheckout' ) )
				),
				'value'       => __( 'Mary Anne Johnson', 'risecheckout' ),
				'step'        => 'shipping',
				'required'    => true,
				'priority'    => 100,
			),
		)
	);

	uasort(
		$fields,
		function ( $a, $b ) {
			return $a['priority'] <=> $b['priority'];
		}
	);

	return $fields;
}

function risecheckout_steps() {
	return array(
		'customer' => array(
			'title'       => __( 'Identify yourself', 'risecheckout' ),
			'description' => __(
				'We will use your email to: Identify your profile, purchase history, order ' .
				'notification and shopping cart.',
				'risecheckout'
			),
			'continue'    => __( 'Continue', 'risecheckout' ),
			'edit'        => __( 'Edit', 'risecheckout' ),
		),
		'shipping' => array(
			'title'       => __( 'Shipping', 'risecheckout' ),
			// 'description' => __(
			//  'Register or select an address',
			//  'risecheckout'
			// ),
			'description' => __(
				'Register an address',
				'risecheckout'
			),
			'placeholder' => __(
				'Fill in your personal information to continue',
				'risecheckout'
			),
			'save'        => __( 'Save', 'risecheckout' ),
		),
		'payment'  => array(
			'title'       => __( 'Payment', 'risecheckout' ),
			'placeholder' => __( 'Fill in your shipping information to continue', 'risecheckout' ),
		),
	);
}

if ( ! defined( 'RISECHECKOUT_PLUGIN_FILE' ) ) {
	define( 'RISECHECKOUT_PLUGIN_FILE', __FILE__ );
}

function risecheckout_define_version_constant() {
	if ( ! function_exists( 'get_plugin_data' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$risecheckout = get_plugin_data( RISECHECKOUT_PLUGIN_FILE );

	if ( ! defined( 'RISECHECKOUT_VERSION' ) ) {
		define( 'RISECHECKOUT_VERSION', $risecheckout['Version'] );
	}
}

function risecheckout_load_plugin_textdomain() {
	$locale = determine_locale();

	$locale = apply_filters( 'plugin_locale', $locale, 'risecheckout' );

	unload_textdomain( 'risecheckout', true );
	load_textdomain( 'risecheckout', dirname( RISECHECKOUT_PLUGIN_FILE ) . '/languages/' . $locale . '.mo' );
	load_plugin_textdomain( 'risecheckout', false, plugin_basename( dirname( RISECHECKOUT_PLUGIN_FILE ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'risecheckout_load_plugin_textdomain' );

function risecheckout_plugin_url() {
	return untrailingslashit( plugins_url( '/', RISECHECKOUT_PLUGIN_FILE ) );
}

function risecheckout_plugin_path() {
	return untrailingslashit( plugin_dir_path( RISECHECKOUT_PLUGIN_FILE ) );
}

function risecheckout_define_constants() {
	define( 'RISECHECKOUT_ABSPATH', dirname( RISECHECKOUT_PLUGIN_FILE ) . '/' );

	add_action( 'init', 'risecheckout_define_version_constant' );
}
risecheckout_define_constants();

function risecheckout_steps_rewrite_rule() {
	if ( ! function_exists( 'wc_get_page_id' ) ) {
		return;
	}
	$checkout_page_id = wc_get_page_id( 'checkout' );
	$checkout_slug    = get_post_field( 'post_name', $checkout_page_id );

	if ( $checkout_slug ) {
		add_rewrite_rule( "^{$checkout_slug}/delivery/?$", "index.php?pagename={$checkout_slug}&step=delivery", 'top' );
		add_rewrite_rule( "^{$checkout_slug}/payment/?$", "index.php?pagename={$checkout_slug}&step=payment", 'top' );
	}
}
add_action( 'init', 'risecheckout_steps_rewrite_rule' );

function risecheckout_replace_emojis_with_font_awesome( $text ) {
	$replacements = array(
		'🏬'  => '<i class="fal fa-store"></i>',
		'📍'  => '<i class="fal fa-map-marker-alt"></i>',
		'✉️' => '<i class="fal fa-envelope"></i>',
		'📞'  => '<i class="fal fa-phone"></i>',
	);

	foreach ( $replacements as $emoji => $faIcon ) {
		$text = str_replace( $emoji, $faIcon, $text );
	}

	return $text;
}

function risecheckout_header_text() {
	return risecheckout_replace_emojis_with_font_awesome( wpautop( get_option( 'risecheckout_header_text' ) ) );
}

function risecheckout_order_review_text() {
	echo wp_kses_post( '<div class="order-review-text">' . wpautop( get_option( 'risecheckout_order_review_text' ) ) . '</div>' );
}
add_action( 'woocommerce_checkout_order_review', 'risecheckout_order_review_text', 21 );

function risecheckout_footer_text() {
	return risecheckout_replace_emojis_with_font_awesome( wpautop( get_option( 'risecheckout_footer_text' ) ) );
}

function risecheckout_plugin_action_links( $links ) {
	if ( class_exists( 'WooCommerce' ) ) {
		$plugin_links   = array();
		$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=risecheckout' ) ) . '">' . __( 'Settings', 'risecheckout' ) . '</a>';

		$links = array_merge( $plugin_links, $links );
	}
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( RISECHECKOUT_PLUGIN_FILE ), 'risecheckout_plugin_action_links' );

if ( is_admin() ) {
	require __DIR__ . '/includes/settings.php';
}

require __DIR__ . '/includes/conditionals.php';
require __DIR__ . '/includes/performance.php';
require __DIR__ . '/includes/theme-support.php';
require __DIR__ . '/includes/template.php';
require __DIR__ . '/includes/frontend-scripts.php';
require __DIR__ . '/includes/ajax.php';
require __DIR__ . '/includes/svg.php';
