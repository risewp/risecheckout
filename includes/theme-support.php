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

function risecheckout_is_active_theme( $theme ) {
	return is_array( $theme ) ? in_array( get_template(), $theme, true ) : get_template() === $theme;
}

function risecheckout_is_know_theme_active() {
	return risecheckout_is_active_theme(
		array(
			'storefront',
			'Impreza',
		)
	);
}

function risecheckout_theme_support_includes() {
	if ( risecheckout_is_know_theme_active() ) {
		switch ( get_template() ) {
			case 'storefront':
				include_once RISECHECKOUT_ABSPATH . 'includes/theme-support/storefront.php';
				break;
			case 'Impreza':
				include_once RISECHECKOUT_ABSPATH . 'includes/theme-support/Impreza.php';
				break;
		}
	}
}
risecheckout_theme_support_includes();

function risecheckout_theme_support_body_class( $classes ) {
	if ( risecheckout_is_checkout() && ! risecheckout_is_order_received_page() ) {
		$classes = array_diff(
			$classes,
			array(
				sprintf( 'theme-%s', get_template() ),
			)
		);
		$classes = 'themed-risecheckout';
	}
	return $classes;
}
add_action( 'body_class', 'risecheckout_theme_support_body_class' );
