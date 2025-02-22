<?php

defined( 'ABSPATH' ) || exit;

function risecheckout_template_loader( $template ) {
	if ( risecheckout_is_checkout() && ! risecheckout_is_order_received_page() ) {
		$template = risecheckout_plugin_path() . '/templates/checkout.php';
	}
	return $template;
}
add_filter( 'template_include', 'risecheckout_template_loader' );

function risecheckout_get_header( $name = null, $args = array() ) {
	$templates = array();
	$name      = (string) $name;
	if ( '' !== $name ) {
		$templates[] = "header-{$name}.php";
	}

	$templates[] = 'header.php';

	if ( ! risecheckout_locate_template( $templates, true, true, $args ) ) {
		return false;
	}
}

function risecheckout_get_footer( $name = null, $args = array() ) {
	$templates = array();
	$name      = (string) $name;
	if ( '' !== $name ) {
		$templates[] = "footer-{$name}.php";
	}

	$templates[] = 'footer.php';

	if ( ! risecheckout_locate_template( $templates, true, true, $args ) ) {
		return false;
	}
}

function risecheckout_locate_template( $template_names, $load = false, $load_once = true, $args = array() ) {
	global $wp_stylesheet_path, $wp_template_path;

	if ( ! isset( $wp_stylesheet_path ) || ! isset( $wp_template_path ) ) {
		wp_set_template_globals();
	}

	$templates_path = risecheckout_plugin_path() . '/templates';

	$is_child_theme = is_child_theme();

	$located = '';
	foreach ( (array) $template_names as $template_name ) {
		if ( ! $template_name ) {
			continue;
		}
		if ( file_exists( $templates_path . '/' . $template_name ) ) {
			$located = $templates_path . '/' . $template_name;
			break;
		} elseif ( file_exists( $wp_stylesheet_path . '/' . $template_name ) ) {
			$located = $wp_stylesheet_path . '/' . $template_name;
			break;
		} elseif ( $is_child_theme && file_exists( $wp_template_path . '/' . $template_name ) ) {
			$located = $wp_template_path . '/' . $template_name;
			break;
		} elseif ( file_exists( ABSPATH . WPINC . '/theme-compat/' . $template_name ) ) {
			$located = ABSPATH . WPINC . '/theme-compat/' . $template_name;
			break;
		}
	}

	if ( $load && '' !== $located ) {
		load_template( $located, $load_once, $args );
	}

	return $located;
}

function risecheckout_site_title_or_logo() {
	ob_start();
	?>

	<a class="navbar-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php
		if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
			$allowed_html = array(
				'img' => array(
					'src'      => array(),
					'alt'      => array(),
					'decoding' => array(),
					'width'    => array(),
					'height'   => array(),
				),
			);
			$custom_logo  = str_replace( '>', ' width="125" height="54">', get_custom_logo() );
			echo wp_kses( $custom_logo, $allowed_html );
		} else {
			echo esc_html( get_bloginfo( 'name' ) );
		}
		?>
	</a>

	<?php
	echo wp_kses_post( ob_get_clean() );
}
