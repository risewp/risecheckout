<?php

defined( 'ABSPATH' ) || exit;

function risecheckout_frontend_scripts() {
	return array(
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
				'risecheckout-twbs',
			),
		),
	);
}

function risecheckout_frontend_styles() {
	$deps = array(
		'woocommerce-general',
		'risecheckout-icons',
	);
	if ( 'yes' === get_option( 'risecheckout_gfonts', 'yes' ) ) {
		$deps[] = 'risecheckout-fonts';
	}
	return array(
		array(
			'handle' => 'twbs',
			'src'    => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
			'ver'    => '5.3.3',
		),
		array(
			'handle' => 'fonts',
			'src'    => risecheckoutgoogle_fonts(),
		),
		array(
			'handle' => 'icons',
		),
		array(
			'deps' => $deps,
		),
	);
}

function risecheckout_frontend_load_scripts() {
	$scripts = risecheckout_frontend_scripts();

	$styles = risecheckout_frontend_styles();

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

	$l10n = array();
	if ( class_exists( 'WC_AJAX' ) ) {
		$l10n['wcAjaxUrl'] = WC_AJAX::get_endpoint( '%%endpoint%%' );
	}
	$l10n = array_merge(
		$l10n,
		array(
			'customerNonce'   => wp_create_nonce( 'risecheckout-customer' ),
			'postcodeBrNonce' => wp_create_nonce( 'risecheckout-postcode-br' ),
		)
	);
	// wp_localize_script( 'risecheckout', 'risecheckoutParams', $l10n );

	wp_enqueue_script( 'risecheckout' );
}
add_action( 'wp_enqueue_scripts', 'risecheckout_frontend_load_scripts', 11 );

/**
 * Add query arguments to a URL, allowing arrays to be expanded as repeated keys.
 *
 * @param mixed ...$args Query arguments.
 *
 * @return string URL with query arguments.
 */
function risecheckout_add_query_arg( ...$args ) {
	if ( is_array( $args[0] ) ) {
		$arg_data = $args[0];
		$url      = $args[1] ?? $_SERVER['REQUEST_URI'];
	} else {
		$arg_data = array( $args[0] => $args[1] );
		$url      = $args[2] ?? $_SERVER['REQUEST_URI'];
	}

	$has_array   = false;
	$query_parts = array();

	// Check if any argument is an array.
	foreach ( $arg_data as $key => $value ) {
		if ( is_array( $value ) ) {
			$has_array = true;
			break;
		}
	}

	// If no array is found, use the default add_query_arg.
	if ( ! $has_array ) {
		return add_query_arg( $arg_data, $url );
	}

	// Process each argument.
	foreach ( $arg_data as $key => $value ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $sub_value ) {
				$query_parts[] = $key . '=' . urlencode( $sub_value );
			}
		} else {
			$query_parts[] = $key . '=' . urlencode( $value );
		}
	}

	// Build the final URL.
	$separator = ( strpos( $url, '?' ) === false ) ? '?' : '&';
	return $url . $separator . implode( '&', $query_parts );
}

function risecheckoutgoogle_fonts() {
	$families = array(
		// 'Rubik'      => 'Rubik:ital,wght@0,300..900;1,300..900',
		'quicksand'  => 'Quicksand:wght@300..700',
		'montserrat' => 'Montserrat:ital,wght@0,100..900;1,100..900',
	);

	$fonts_url = urldecode(
		risecheckout_add_query_arg(
			array(
				'family'  => array_values( $families ),
				'display' => 'swap',
			),
			'https://fonts.googleapis.com/css2'
		)
	);

	// var_dump($fonts_url);
	// die;
	return $fonts_url;
}

function risecheckout_style_loader_tag( $tag, $handle, $href ) {
	preg_match_all( '/(rel|id|title|href|media)=\'([^\']+)\'\s+/', $tag, $matches );
	$tag = (object) array_combine( $matches[1], $matches[2] );

	if ( 'fonts.googleapis.com' === parse_url( $tag->href, PHP_URL_HOST ) ) {
		$preconnects = array(
			array(
				'href' => 'https://fonts.googleapis.com',
			),
			array(
				'href'        => 'https://fonts.gstatic.com',
				'crossorigin' => true,
			),
		);

		foreach ( $preconnects as $preconnect ) {
			$preconnect = (object) $preconnect;
			printf(
				"<link rel=\"preconnect\" href=\"%s\"%s>\n",
				$preconnect->href,
				isset( $preconnect->crossorigin ) && $preconnect->crossorigin ? ' crossorigin' : ''
			);
		}

		$tag->href = preg_replace( '/&ver=[^&]+/', '', html_entity_decode( urldecode( $tag->href ) ) );
	}

	if ( 'risecheckout-fonts-css' === $tag->id ) {
		$tag->href = risecheckoutgoogle_fonts();
	}

	return sprintf(
		"<link href=\"%s\" rel=\"%s\" id=\"%s\"%s%s>\n",
		$tag->href,
		$tag->rel,
		$tag->id,
		isset( $tag->title ) ? sprintf( ' title="%s"', $tag->title ) : '',
		'all' !== $tag->media ? sprintf( ' media="%s"', $tag->media ) : ''
	);
}
add_filter( 'style_loader_tag', 'risecheckout_style_loader_tag', 10, 3 );

function risecheckout_register_asset( $asset, $type ) {
	if ( ! in_array( $type, array( 'script', 'style' ), true ) ) {
		return;
	}
	$slug      = 'risecheckout';
	$asset     = (object) $asset;
	$handle    = isset( $asset->handle ) ? $asset->handle : $slug;
	$extension = 'style' === $type ? 'css' : 'js';
	$src       = isset( $asset->src ) ? $asset->src : ( $handle ? $handle . ".{$extension}" : '' );
	$handle    = $handle !== $slug ? "{$slug}-{$handle}" : $handle;
	$deps      = isset( $asset->deps ) ? $asset->deps : array();
	$version   = isset( $asset->ver ) ? $asset->ver : RISECHECKOUT_VERSION;
	if ( ! filter_var( $src, FILTER_VALIDATE_URL ) ) {
		$src = risecheckout_plugin_url() . "/assets/{$extension}/" . $src;
	}
	if ( 'style' === $type ) {
		wp_register_style( $handle, $src, $deps, $version );
	} else {
		wp_register_script( $handle, $src, $deps, $version, true );
	}
}

function risecheckout_register_style( $style ) {
	risecheckout_register_asset( $style, 'style' );
}

function risecheckout_register_script( $script ) {
	risecheckout_register_asset( $script, 'script' );
}
