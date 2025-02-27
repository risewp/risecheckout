<?php
/**
 * Theme Support Functions
 *
 * @package RiseCheckout
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'risecheckout_dequeue_style' ) ) {
	/**
	 * Dequeue and optionally deregister a style on checkout pages.
	 *
	 * @param string $style Style handle.
	 * @param bool   $deregister Whether to deregister the style.
	 * @param string $prefix Optional prefix for the style handle.
	 */
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
	/**
	 * Dequeue multiple styles on checkout pages.
	 *
	 * @param array  $styles Array of style handles with deregister flags.
	 * @param string $prefix Optional prefix for the style handles.
	 */
	function risecheckout_dequeue_styles( $styles, $prefix = '' ) {
		foreach ( $styles as $style => $deregister ) {
			risecheckout_dequeue_style( $style, $deregister, $prefix );
		}
	}
}

if ( ! function_exists( 'risecheckout_dequeue_script' ) ) {
	/**
	 * Dequeue a script on checkout pages.
	 *
	 * @param string $script Script handle.
	 * @param string $prefix Optional prefix for the script handle.
	 */
	function risecheckout_dequeue_script( $script, $prefix = '' ) {
		if ( ! risecheckout_is_checkout() || risecheckout_is_order_received_page() ) {
			return;
		}
		$script = $prefix . $script;
		wp_dequeue_script( $script );
	}
}

if ( ! function_exists( 'risecheckout_dequeue_scripts' ) ) {
	/**
	 * Dequeue multiple scripts on checkout pages.
	 *
	 * @param array  $scripts Array of script handles.
	 * @param string $prefix Optional prefix for the script handles.
	 */
	function risecheckout_dequeue_scripts( $scripts, $prefix = '' ) {
		foreach ( $scripts as $script ) {
			risecheckout_dequeue_script( $script, $prefix );
		}
	}
}

/**
 * Check if the active theme matches the provided theme name(s).
 *
 * @param string|array $theme Theme name or array of theme names.
 * @return bool
 */
function risecheckout_is_active_theme( $theme ) {
	return is_array( $theme ) ? in_array( get_template(), $theme, true ) : get_template() === $theme;
}

/**
 * Check if a known compatible theme is active.
 *
 * @return bool
 */
function risecheckout_is_know_theme_active() {
	return risecheckout_is_active_theme(
		array(
			'storefront',
			'Impreza',
		)
	);
}

/**
 * Include theme-specific support files.
 */
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

/**
 * Modify body classes on checkout pages.
 *
 * @param array $classes Array of body class names.
 * @return array Modified array of body class names.
 */
function risecheckout_theme_support_body_class( $classes ) {
	if ( risecheckout_is_checkout() && ! risecheckout_is_order_received_page() ) {
		$classes   = array_diff(
			$classes,
			array(
				sprintf( 'theme-%s', get_template() ),
			)
		);
		$classes[] = 'themed-risecheckout';
	}
	return $classes;
}
add_action( 'body_class', 'risecheckout_theme_support_body_class' );
