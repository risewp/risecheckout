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
					'wrapper_class' => 'col-6',
					'label'         => __( 'First name', 'risecheckout' ),
					'placeholder'   => sprintf(
						/* translators: %s: Example */
						__( 'e.g.: %s', 'risecheckout' ),
						__( 'Mary', 'risecheckout' )
					),
					'minlength'     => 2,
					'pattern'       => '([A-Za-zÀ-ÖØ-öø-ÿ]{2,}(?: [A-Za-zÀ-ÖØ-öø-ÿ]+)*)',
					'invalid'       => sprintf(
						/* translators: %s: Field label */
						__( 'Enter your %s', 'risecheckout' ),
						mb_strtolower( __( 'First name', 'risecheckout' ) )
					),
					'required'      => true,
					'value'         => __( 'Mary', 'risecheckout' ),
					'step'          => 'customer',
					'priority'      => 5,
					'info'          => 'name',
					'info_label'    => __( 'Full name', 'risecheckout' ),
				),
				'lastname'  => array(
					'wrapper_class' => 'col-6',
					'label'         => __( 'Last name', 'risecheckout' ),
					'placeholder'   => sprintf(
						/* translators: %s: Example */
						__( 'e.g.: %s', 'risecheckout' ),
						__( 'Johnson', 'risecheckout' )
					),
					'minlength'     => 2,
					'pattern'       => '([A-Za-zÀ-ÖØ-öø-ÿ]{2,}(?: [A-Za-zÀ-ÖØ-öø-ÿ]+)*)',
					'invalid'       => sprintf(
						/* translators: %s: Field label */
						__( 'Enter your %s', 'risecheckout' ),
						mb_strtolower( __( 'Last name', 'risecheckout' ) )
					),
					'required'      => true,
					'value'         => __( 'Johnson', 'risecheckout' ),
					'step'          => 'customer',
					'priority'      => 10,
					'info'          => 'name',
					'info_label'    => __( 'Full name', 'risecheckout' ),
				),
			)
		);
	}
	$fields = array_merge(
		$fields,
		array(
			'email'  => array(
				'label'       => __( 'Email', 'risecheckout' ),
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
			'cpf'    => array(
				'label'       => 'CPF',
				'placeholder' => '000.000.000-00',
				'minlength'   => 11,
				'maxlength'   => 14,
				'pattern'     => '\d{3}\.?\d{3}\.?\d{3}-?\d{2}',
				'validation'  => 'cpf',
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
			'mobile' => array(
				'label'       => __( 'Mobile', 'risecheckout' ),
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
			'zip'    => array(
				'wrapper_class' => 'col-7 zip',
				'label'         => __( 'Zip', 'risecheckout' ),
				'minlength'     => 8,
				'maxlength'     => 9,
				'pattern'       => '\d{5}-?\d{3}',
				'mask'          => 'zip-br',
				'invalid'       => sprintf(
					/* translators: %s: Field label */
					__( 'Enter a valid %s', 'risecheckout' ),
					mb_strtolower( __( 'Zip', 'risecheckout' ) )
				),
				'required'      => true,
				'step'          => 'delivery',
				'priority'      => 50,
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
		'delivery' => array(
			'title'       => __( 'Delivery', 'risecheckout' ),
			'description' => __(
				'Register or select an address',
				'risecheckout'
			),
			'placeholder' => __(
				'Fill in your personal information to continue',
				'risecheckout'
			),
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

require __DIR__ . '/includes/conditionals.php';
require __DIR__ . '/includes/performance.php';
require __DIR__ . '/includes/theme-support.php';
require __DIR__ . '/includes/template.php';
require __DIR__ . '/includes/frontend-scripts.php';
require __DIR__ . '/includes/ajax.php';
require __DIR__ . '/includes/svg.php';
