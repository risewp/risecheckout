<?php

defined( 'ABSPATH' ) || exit;

function risecheckout_permitir_svg_upload( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter( 'upload_mimes', 'risecheckout_permitir_svg_upload' );

function risecheckout_svg_allowed_tags() {
	return array(
		'svg'      => array(
			'xmlns'   => array(),
			'width'   => array(),
			'height'  => array(),
			'viewbox' => array(),
			'fill'    => array(),
			'stroke'  => array(),
		),
		'g'        => array(
			'fill'   => array(),
			'stroke' => array(),
		),
		'path'     => array(
			'd'      => array(),
			'fill'   => array(),
			'stroke' => array(),
		),
		'rect'     => array(
			'x'      => array(),
			'y'      => array(),
			'width'  => array(),
			'height' => array(),
			'fill'   => array(),
			'stroke' => array(),
		),
		'circle'   => array(
			'cx'     => array(),
			'cy'     => array(),
			'r'      => array(),
			'fill'   => array(),
			'stroke' => array(),
		),
		'ellipse'  => array(
			'cx'     => array(),
			'cy'     => array(),
			'rx'     => array(),
			'ry'     => array(),
			'fill'   => array(),
			'stroke' => array(),
		),
		'line'     => array(
			'x1'     => array(),
			'y1'     => array(),
			'x2'     => array(),
			'y2'     => array(),
			'stroke' => array(),
		),
		'polyline' => array(
			'points' => array(),
			'fill'   => array(),
			'stroke' => array(),
		),
		'polygon'  => array(
			'points' => array(),
			'fill'   => array(),
			'stroke' => array(),
		),
		'text'     => array(
			'x'         => array(),
			'y'         => array(),
			'font-size' => array(),
			'fill'      => array(),
		),
		'tspan'    => array(
			'x'  => array(),
			'y'  => array(),
			'dx' => array(),
			'dy' => array(),
		),
		'defs'     => array(),
		'use'      => array(
			'xlink:href' => array(),
		),
		'symbol'   => array(),
		'title'    => array(),
		'desc'     => array(),
		'style'    => array(),
	);
}

function risecheckout_sanitizar_svg_antes_upload( $file ) {
	if ( 'image/svg+xml' === $file['type'] ) {
		$svg = file_get_contents( $file['tmp_name'] );

		$allowed_tags  = risecheckout_svg_allowed_tags();
		$sanitized_svg = wp_kses( $svg, $allowed_tags );

		file_put_contents( $file['tmp_name'], $sanitized_svg );
	}
	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'risecheckout_sanitizar_svg_antes_upload' );


function risecheckout_svg_preview_fixer() {
	echo '<style>
        td.media-icon img[src$=".svg"], img[src$=".svg"] {
            width: 100px !important;
            height: auto !important;
        }
    </style>';
}
add_action( 'admin_head', 'risecheckout_svg_preview_fixer' );
