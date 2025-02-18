<?php

defined( 'ABSPATH' ) || exit;

function risecheckout_template_loader( $template ) {
	$current_theme_supports_woocommerce = risecheckout_current_theme_supports_woocommerce();
	$is_checkout                        = risecheckout_is_checkout();
	$is_order_received_page             = risecheckout_is_order_received_page();
	if ( $current_theme_supports_woocommerce && $is_checkout && ! $is_order_received_page ) {
		$template = risecheckout_plugin_path() . '/templates/checkout.php';
	}
	return $template;
}
add_filter( 'template_include', 'risecheckout_template_loader' );

function risecheckout_get_header( $name = null, $args = array() ) {
	$templates = array();
	$name      = (string) $name;
	if ( '' !== $name ) {
		$templates[] = "header-{$name}.php";
	}

	$templates[] = 'header.php';

	if ( ! risecheckout_locate_template( $templates, true, true, $args ) ) {
		return false;
	}
}

function risecheckout_get_footer( $name = null, $args = array() ) {
	$templates = array();
	$name      = (string) $name;
	if ( '' !== $name ) {
		$templates[] = "footer-{$name}.php";
	}

	$templates[] = 'footer.php';

	if ( ! risecheckout_locate_template( $templates, true, true, $args ) ) {
		return false;
	}
}

function risecheckout_locate_template( $template_names, $load = false, $load_once = true, $args = array() ) {
	global $wp_stylesheet_path, $wp_template_path;

	if ( ! isset( $wp_stylesheet_path ) || ! isset( $wp_template_path ) ) {
		wp_set_template_globals();
	}

	$templates_path = risecheckout_plugin_path() . '/templates';

	$is_child_theme = is_child_theme();

	$located = '';
	foreach ( (array) $template_names as $template_name ) {
		if ( ! $template_name ) {
			continue;
		}
		if ( file_exists( $templates_path . '/' . $template_name ) ) {
			$located = $templates_path . '/' . $template_name;
			break;
		} elseif ( file_exists( $wp_stylesheet_path . '/' . $template_name ) ) {
			$located = $wp_stylesheet_path . '/' . $template_name;
			break;
		} elseif ( $is_child_theme && file_exists( $wp_template_path . '/' . $template_name ) ) {
			$located = $wp_template_path . '/' . $template_name;
			break;
		} elseif ( file_exists( ABSPATH . WPINC . '/theme-compat/' . $template_name ) ) {
			$located = ABSPATH . WPINC . '/theme-compat/' . $template_name;
			break;
		}
	}

	if ( $load && '' !== $located ) {
		load_template( $located, $load_once, $args );
	}

	return $located;
}
