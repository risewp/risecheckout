<?php

defined( 'ABSPATH' ) || exit;

function risecheckout_frontend_scripts() {
	$current_theme_supports_woocommerce = risecheckout_current_theme_supports_woocommerce();
	$is_checkout                        = risecheckout_is_checkout();
	$is_order_received_page             = risecheckout_is_order_received_page();
	$version                            = RISECHECKOUT_VERSION;
	if ( $current_theme_supports_woocommerce && $is_checkout && ! $is_order_received_page ) {
		wp_register_style( 'risecheckout-twbs', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', array(), '5.3.3' );
		wp_enqueue_style( 'risecheckout', risecheckout_plugin_url() . '/assets/css/risecheckout.css', array( 'risecheckout-twbs' ), $version );
	}
}
add_action( 'wp_enqueue_scripts', 'risecheckout_frontend_scripts' );
