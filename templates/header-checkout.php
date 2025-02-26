<!doctype html>
<html <?php language_attributes(); ?> <?php root_class(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

<?php
wp_body_open();

?>

<nav class="navbar navbar-expand-md fixed-top bg-dark" data-bs-theme="dark">
	<div class="container">
		<?php risecheckout_site_title_or_logo(); ?>

		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="<?php echo esc_attr( 'Toggle text', 'risecheckout' ); ?>">
			<?php echo wp_kses_post( risecheckout_header_toggler_icon() ); ?>
		</button>

		<div class="collapse navbar-collapse" id="navbarText">
			<div class="navbar-text"><?php echo wp_kses_post( risecheckout_header_text() ); ?></div>
		</div>
	</div>
</nav>

<main>
	<div class="container">

		<?php
		the_title( '<h1>', '</h1>' );
