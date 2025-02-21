<?php

defined( 'ABSPATH' ) || exit;

function risecheckout_frontend_scripts() {
	$scripts = array(
		array(
			'handle' => 'twbs',
			'src'    => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
			'ver'    => '5.3.3',
		),
		array(
			'handle' => 'validation',
			'deps'   => array(
				'twbs',
			),
		),
		array(
			'handle' => 'mask',
		),
		array(
			'deps' => array(
				'twbs',
				'mask',
				'validation',
			),
		),
	);

	$styles = array(
		array(
			'handle' => 'twbs',
			'src'    => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
			'ver'    => '5.3.3',
		),
		array(
			'deps' => array(
				'twbs',
			),
		),
	);

	if ( ! risecheckout_is_checkout() || risecheckout_is_order_received_page() ) {
		return;
	}

	foreach ( $styles as $style ) {
		risecheckout_register_style( $style );
	}
	wp_enqueue_style( 'risecheckout' );

	foreach ( $scripts as $script ) {
		risecheckout_register_script( $script );
	}
	wp_enqueue_script( 'risecheckout' );
}
add_action( 'wp_enqueue_scripts', 'risecheckout_frontend_scripts' );

function risecheckout_register_asset( $asset, $type ) {
	if ( ! in_array( $type, array( 'script', 'style' ), true ) ) {
		return;
	}
	$slug      = 'risecheckout';
	$asset     = (object) $asset;
	$handle    = isset( $asset->handle ) ? $asset->handle : $slug;
	$extension = 'style' === $type ? 'css' : 'js';
	$src       = isset($asset->src) ? $asset->src : ( $handle ? $handle . ".{$extension}" : '' );
	$handle    = $handle !== $slug ? "{$slug}-{$handle}" : $handle;
	$deps      = isset( $asset->deps ) ? $asset->deps : array();
	$version   = isset( $asset->ver ) ? $asset->ver : RISECHECKOUT_VERSION;
	if ( ! filter_var( $src, FILTER_VALIDATE_URL ) ) {
		$src = risecheckout_plugin_url() . "/assets/{$extension}/" . $src;
	}
	foreach ( $deps as &$dep ) {
		$dep = 'risecheckout-' . $dep;
	}
	if ( 'style' === $type ) {
		wp_register_style( $handle, $src, $deps, $version );
	} else {
		wp_register_script( $handle, $src, $deps, $version, true );
	}
}

function risecheckout_register_style( $style ) {
	risecheckout_register_asset( $style, 'style');
}

function risecheckout_register_script( $script ) {
	risecheckout_register_asset( $script, 'script');
}
