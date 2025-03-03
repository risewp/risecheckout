<?php
/**
 * RiseCheckout frontend scripts and styles.
 *
 * @package RiseCheckout
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueues frontend scripts for the checkout page.
 *
 * @return array Array of script information (handle, src, dependencies, etc.)
 */
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
				'wc-checkout',
			),
		),
	);
}

/**
 * Enqueues frontend styles for the checkout page.
 *
 * @return array Array of style information (handle, src, dependencies, etc.)
 */
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
			'ver'    => '5.13.0',
		),
		array(
			'deps' => $deps,
		),
	);
}

/**
 * Loads and enqueues the required scripts and styles for the checkout page.
 */
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
		$l10n = array_merge(
			$l10n,
			array(
				'wcAjaxUrl' => WC_AJAX::get_endpoint( '%%endpoint%%' ),
			)
		);
	}
	$l10n = array_merge(
		$l10n,
		array(
			'customerNonce'   => wp_create_nonce( 'risecheckout-customer' ),
			'postcodeBrNonce' => wp_create_nonce( 'risecheckout-postcode-br' ),
		)
	);
	wp_localize_script( 'risecheckout', 'risecheckoutParams', $l10n );

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
	$request_uri = false;
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$request_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
	}
	if ( is_array( $args[0] ) ) {
		$arg_data = $args[0];
		$url      = $args[1] ?? $request_uri;
	} else {
		$arg_data = array( $args[0] => $args[1] );
		$url      = $args[2] ?? $request_uri;
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
				$query_parts[] = $key . '=' . rawurlencode( $sub_value );
			}
		} else {
			$query_parts[] = $key . '=' . rawurlencode( $value );
		}
	}

	// Build the final URL.
	$separator = ( strpos( $url, '?' ) === false ) ? '?' : '&';
	return $url . $separator . implode( '&', $query_parts );
}

/**
 * Retrieves the URL for Google Fonts.
 *
 * @return string The URL for Google Fonts with the specified font families.
 */
function risecheckoutgoogle_fonts() {
	$families = array(
		'Rubik'      => 'Rubik:ital,wght@0,300..900;1,300..900',
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

	return $fonts_url;
}

/**
 * Modify the style loader tag to include preconnect for fonts.googleapis.com.
 *
 * @param string $tag The HTML tag for the style.
 *
 * @return string The modified HTML tag.
 */
function risecheckout_style_loader_tag( $tag ) {
	preg_match_all( '/(rel|id|title|href|media)=\'([^\']+)\'\s+/', $tag, $matches );
	$tag = (object) array_combine( $matches[1], $matches[2] );

	if ( 'fonts.googleapis.com' === wp_parse_url( $tag->href, PHP_URL_HOST ) ) {
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
				esc_url( $preconnect->href ),
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
add_filter( 'style_loader_tag', 'risecheckout_style_loader_tag', 10 );

/**
 * Registers a script or style asset.
 *
 * @param object $asset The asset information (handle, src, etc.).
 * @param string $type  The type of asset ('script' or 'style').
 */
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

/**
 * Registers a style asset.
 *
 * @param object $style The style asset information.
 */
function risecheckout_register_style( $style ) {
	risecheckout_register_asset( $style, 'style' );
}

/**
 * Registers a script asset.
 *
 * @param object $script The script asset information.
 */
function risecheckout_register_script( $script ) {
	risecheckout_register_asset( $script, 'script' );
}
