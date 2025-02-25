<?php

if ( ! function_exists( 'risecheckout_dequeue_style' ) ) {
	function risecheckout_dequeue_style( $style, $deregister = false, $prefix = '' ) {
		if ( ! risecheckout_is_checkout() || risecheckout_is_order_received_page() ) {
			return;
		}
		$style = $prefix . $style;
		wp_dequeue_style( $style );
		if ( $deregister ) {
			wp_deregister_style( $style );
		}
	}
}

if ( ! function_exists( 'risecheckout_dequeue_styles' ) ) {
	function risecheckout_dequeue_styles( $styles, $prefix = '' ) {
		foreach ( $styles as $style => $deregister ) {
			risecheckout_dequeue_style( $style, $deregister, $prefix );
		}
	}
}

if ( ! function_exists( 'risecheckout_dequeue_script' ) ) {
	function risecheckout_dequeue_script( $script, $prefix = '' ) {
		if ( ! risecheckout_is_checkout() || risecheckout_is_order_received_page() ) {
			return;
		}
		$script = $prefix . $script;
		wp_dequeue_script( $script );
	}
}

if ( ! function_exists( 'risecheckout_dequeue_scripts' ) ) {
	function risecheckout_dequeue_scripts( $scripts, $prefix = '' ) {
		foreach ( $scripts as $script ) {
			risecheckout_dequeue_script( $script, $prefix );
		}
	}
}

function risecheckout_wc_load_scripts() {
	$scripts = array(
		'wc-add-to-cart',
		'selectWoo',
		'select2',
		'wc-checkout',
		'woocommerce',
	);
	risecheckout_dequeue_scripts( $scripts );

	$styles = array(
		'select2'                 => true,
		'woocommerce-layout'      => true,
		'woocommerce-smallscreen' => true,
		'woocommerce-general'     => true,
	);
	risecheckout_dequeue_styles( $styles );
}
add_action( 'wp_enqueue_scripts', 'risecheckout_wc_load_scripts', 11 );

function risecheckout_wp_common_block_scripts_and_styles() {
	risecheckout_dequeue_style( 'block-library', false, 'wp-' );
}
add_action( 'wp_enqueue_scripts', 'risecheckout_wp_common_block_scripts_and_styles', 11 );
