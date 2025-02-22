<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>

<?php if ( 'yes' === get_option( 'risecheckout_gfonts', 'yes' ) ) : ?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

<?php endif; ?>

</head>

<body <?php body_class(); ?>>

<?php
wp_body_open();

?>

<nav class="navbar navbar-dark bg-dark" style="margin-bottom:1.5625rem;">
	<div class="container justify-content-center">
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
				$custom_logo = str_replace( '>', ' width="125" height="54">', get_custom_logo() );
				// $custom_logo = get_custom_logo();
				echo wp_kses( $custom_logo, $allowed_html );
				// echo $custom_logo;
			} else {
				echo esc_html( get_bloginfo( 'name' ) );
			}
			?>
		</a>
	</div>
</nav>

<main>
	<div class="container">
