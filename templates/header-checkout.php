<!doctype html>
<html <?php language_attributes(); ?> class="xlook-a-like">
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

<nav class="navbar">
	<div class="container">
		<?php risecheckout_site_title_or_logo(); ?>

		<div><?php echo wp_kses_post( risecheckout_header_text() ); ?></div>
	</div>
</nav>

<main>
	<div class="container">
