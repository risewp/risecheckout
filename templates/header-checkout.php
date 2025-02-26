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

<nav class="navbar" style="display:none">
	<div class="container">
		<?php risecheckout_site_title_or_logo(); ?>

		<div><?php echo wp_kses_post( risecheckout_header_text() ); ?></div>
	</div>
</nav>
<nav class="navbar navbar-dark navbar-expand-lg">
  <div class="container">
  	<?php risecheckout_site_title_or_logo(); ?>
	<div class="col">
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="btn"><i class="fal fa-envelope"></i> <span class="visually-hidden">E-mail</span></span><span class="btn"><i class="fal fa-phone"></i> <span class="visually-hidden">Telefone</span></span>
		</button>
	</div>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 text-end">
        <li class="nav-item">
			<a class="nav-link" href="mailto:atendimento@elevacalcados.com.br"><i class="fal fa-envelope"></i> atendimento@elevacalcados.com.br</a>
        </li>
        <li class="nav-item">
			<a class="nav-link" href="tel:+5521964801937"><i class="fal fa-phone"></i> +55 21 96480 1937</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<!--nav class="navbar navbar-dark bg-dark navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="#">Navbar</a>
	<button class="navbar-toggler col d-flex justify-content-end" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Features</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Pricing</a>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled" aria-disabled="true">Disabled</a>
        </li>
      </ul>
    </div>
  </div>
</nav-->
<nav class="navbar bg-dark navbar-expand-lg" data-bs-theme="dark" style="display:none">
	<div class="container">
		<a class="navbar-brand" href="#">Navbar</a>
		<div class="col d-flex justify-content-end">
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
			<span class="btn"><i class="fal fa-envelope"></i> <span class="visually-hidden">E-mail</span></span><span class="btn"><i class="fal fa-phone"></i> <span class="visually-hidden">Telefone</span></span>
			</button>
		</div>
		<div class="collapse navbar-collapse" id="navbarNav">
			<!--ul class="navbar-nav">
				<li class="nav-item">
				<a class="nav-link active" aria-current="page" href="#">Home</a>
				</li>
				<li class="nav-item">
				<a class="nav-link" href="#">Features</a>
				</li>
				<li class="nav-item">
				<a class="nav-link" href="#">Pricing</a>
				</li>
				<li class="nav-item">
				<a class="nav-link disabled" aria-disabled="true">Disabled</a>
				</li>
			</ul-->
			<div class="navbar-text"><?php echo wp_kses_post( risecheckout_header_text() ); ?></div-->
		</div>
	</div>
</nav>

<main>
	<div class="container">
