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

/**
 * Function to automatically convert emails, URLs, and phone numbers into clickable links.
 *
 * This function uses WordPress' native make_clickable() to auto-link emails and URLs.
 * It then processes phone numbers by removing any characters that are not digits,
 * except for a leading '+' (for international country codes), and wraps them in a clickable tel: link.
 *
 * @param string $content The content to process.
 * @return string The processed content with clickable links.
 */
function risecheckout_make_clickable( $content ) {
	// First, use WordPress' built-in function to link emails and URLs
	$content = make_clickable( $content );

	// Regex pattern to match phone numbers with various formatting (spaces, dashes, parentheses)
	// This pattern matches a phone number that starts optionally with '+' followed by digits and allowed characters.
	$pattern = '/(\+?[0-9\-\s\(\)]{7,}[0-9])/';

	// Process each matched phone number using a callback function
	$content = preg_replace_callback(
		$pattern,
		function ( $matches ) {
			$phone = $matches[0];
			// Clean the phone number: remove all characters except digits,
			// while preserving a '+' if it's at the beginning.
			$cleaned_phone = preg_replace( '/(?!^\+)[^\d]/', '', $phone );
			// Return the original phone number wrapped in a clickable tel: link with the cleaned number
			return '<a href="tel:' . $cleaned_phone . '">' . $phone . '</a>';
		},
		$content
	);

	return $content;
}

function risecheckout_header_text() {
	$text = get_option( 'risecheckout_header_text' );
	if ( 'yes' === get_option( 'risecheckout_text_make_clickable', 'no' ) ) {
		$text = risecheckout_make_clickable( $text );
	}
	$text = risecheckout_replace_emojis_with_font_awesome( wpautop( $text ) );

	$text = preg_replace(
		'/<p>\s*(<i class="fal fa-[^"]+"><\/i>)\s*<a href="([^"]+)">([^<]+)<\/a>\s*<\/p>/',
		'<p><a href="$2">$1 $3</a></p>',
		$text
	);

	return $text;
}

function risecheckout_header_toggler_icon() {
	$icon = '<span class="navbar-toggler-icon"></span>';
	preg_match_all( '/<i\s+class="fal\s+fa-[^"]+"><\/i>/', risecheckout_header_text(), $icons );
	if ( ! empty( current( $icons ) ) ) {
		$icons = array_unique( current( $icons ) );
		$icon  = implode( ' ', $icons );
	}
	return $icon;
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

if ( ! function_exists( 'root_class' ) ) {
	function root_class( $classes = array() ) {
		$classes = apply_filters('root_class', $classes);
		echo esc_attr( 'class' ) . '="' . implode( ' ', $classes ) . '"';
	}
}

require __DIR__ . '/includes/fields.php';
require __DIR__ . '/includes/conditionals.php';
require __DIR__ . '/includes/performance.php';
require __DIR__ . '/includes/theme-support.php';
require __DIR__ . '/includes/template.php';
require __DIR__ . '/includes/frontend-scripts.php';
require __DIR__ . '/includes/ajax.php';
require __DIR__ . '/includes/svg.php';

if ( is_admin() ) {
	require __DIR__ . '/includes/settings.php';
}
