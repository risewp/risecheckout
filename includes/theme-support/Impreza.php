<?php

function risecheckout_us_theme_styles_scripts() {
	$styles = array(
		'style'  => false,
		'header' => false,
		'theme'  => false,
	);
	risecheckout_us_fallback_dequeue_styles( $styles );

	$scripts = array(
		'core',
	);
	risecheckout_us_fallback_dequeue_scripts( $scripts );
}
add_action( 'wp_enqueue_scripts', 'risecheckout_us_theme_styles_scripts', 13 );

function risecheckout_us_fallback_dequeue_styles( $styles ) {
	risecheckout_dequeue_styles( $styles, $prefix = 'us-fallback-' );
}

function risecheckout_us_fallback_dequeue_scripts( $scripts ) {
	risecheckout_dequeue_scripts( $scripts, $prefix = 'us-fallback-' );
}
