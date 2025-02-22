<?php

function risecheckout_storefront_scripts() {
	$styles = array(
		'style' => true,
		'icons' => true,
		'fonts' => false,
	);
	risecheckout_storefront_dequeue_styles( $styles );

	wp_dequeue_script( 'storefront-navigation' );
}
add_action( 'wp_enqueue_scripts', 'risecheckout_storefront_scripts', 11 );

function risecheckout_storefront_dequeue_styles( $styles ) {
	risecheckout_dequeue_styles( $styles, $prefix = 'storefront-' );
}

function risecheckout_storefront_dequeue_scripts( $scripts ) {
	risecheckout_dequeue_scripts( $scripts, $prefix = 'storefront-' );
}

function risecheckout_storefront_block_assets() {
	$styles = array(
		'gutenberg-blocks' => true,
	);
	risecheckout_storefront_dequeue_styles( $styles );
}
add_action( 'enqueue_block_assets', 'risecheckout_storefront_block_assets', 11 );

function risecheckout_storefront_wc_integrations_scripts() {
	$styles = array(
		'woocommerce-brands-style' => true,
	);
	risecheckout_storefront_dequeue_styles( $styles );

	$scripts = array(
		'woocommerce-brands',
	);
	risecheckout_storefront_dequeue_scripts( $scripts );
}

function risecheckout_storefront_wc_scripts() {
	$styles = array(
		'woocommerce-style' => true,
	);
	risecheckout_storefront_dequeue_styles( $styles );

	$scripts = array(
		'header-cart',
		'handheld-footer-bar',
	);
	risecheckout_storefront_dequeue_scripts( $scripts );
}

function risecheckout_storefront_wc() {
	add_action( 'wp_enqueue_scripts', 'risecheckout_storefront_wc_integrations_scripts', 100 );
	add_action( 'wp_enqueue_scripts', 'risecheckout_storefront_wc_scripts', 21 );
}
risecheckout_storefront_wc();
