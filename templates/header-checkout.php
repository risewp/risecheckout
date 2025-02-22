<!doctype html>
<html <?php language_attributes(); ?>>
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

<nav class="navbar navbar-dark bg-dark" style="margin-bottom:1.5625rem;">
	<div class="container justify-content-center">
		<?php risecheckout_site_title_or_logo(); ?>
	</div>
</nav>

<main>
	<div class="container">
