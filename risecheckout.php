<?php
/**
 * Plugin Name: Rise Checkout
 * Description: Enhanced WooCommerce checkout with a seamless single-page multi-step with tracking for abandoned carts.
 * Version: 1.0.0-dev
 * Author: RiseWP
 * Author URI: https://risewp.github.io
 * Text Domain: risecheckout
 * Domain Path: /languages/
 * Requires at least: 6.7
 * Requires PHP: 7.4
 */

defined( 'ABSPATH' ) || exit;



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

function risecheckout_plugin_action_links( $links ) {
	if ( class_exists( 'WooCommerce' ) ) {
		$plugin_links   = array();
		$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=risecheckout' ) ) . '">' . __( 'Settings', 'risecheckout' ) . '</a>';

		$links = array_merge( $plugin_links, $links );
	}
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( RISECHECKOUT_PLUGIN_FILE ), 'risecheckout_plugin_action_links' );

if ( ! function_exists( 'root_class' ) ) {
	function root_class( $classes = array() ) {
		$classes = apply_filters('root_class', $classes);
		echo esc_attr( 'class' ) . '="' . implode( ' ', $classes ) . '"';
	}
}

require __DIR__ . '/includes/fields.php';
require __DIR__ . '/includes/conditionals.php';
require __DIR__ . '/includes/theme-support.php';
require __DIR__ . '/includes/template.php';
require __DIR__ . '/includes/frontend-scripts.php';
require __DIR__ . '/includes/ajax.php';

if ( is_admin() ) {
	require __DIR__ . '/includes/settings.php';
}
