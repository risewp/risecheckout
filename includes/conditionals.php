<?php

defined( 'ABSPATH' ) || exit;

function risecheckout_is_checkout() {
	return function_exists( 'is_checkout' ) && is_checkout();
}

function risecheckout_is_order_received_page() {
	return function_exists( 'is_order_received_page' ) && is_order_received_page();
}

function risecheckout_current_theme_supports_woocommerce() {
	return (bool) current_theme_supports( 'woocommerce' );
}
