<?php
defined( 'ABSPATH' ) || exit;

risecheckout_get_header( 'checkout' );
?>

<div class="container">

	<form novalidate data-required="Campo obrigatório.">
		<fieldset id="step-customer" data-continue="Continuar" data-edit="Editar">
			<legend>Identifique-se</legend>

			<div class="row g-3 fields">

				<?php if ( 'yes' === get_option( 'risecheckout_fullname', 'no' ) ) : ?>

				<div class="col-12">
					<label for="fullname">Nome completo</label>
					<input type="text" class="form-control" id="fullname" placeholder="ex.: Maria de Almeida Cruz" minlength="5" pattern="[A-Za-zÀ-ÖØ-öø-ÿ]{2,}(\s+[A-Za-zÀ-ÖØ-öø-ÿ]{2,})+" data-invalid="Digite o seu nome completo" required value="Maria de Almeida Cruz">
				</div>

				<?php else : ?>

				<div class="col-6 firstname">
					<label for="firstName">Primeiro nome</label>
					<input type="text" class="form-control" id="firstName" placeholder="ex.: Maria" minlength="2" pattern="([A-Za-zÀ-ÖØ-öø-ÿ]{2,})+" data-invalid="Digite o seu primeiro nome" required value="Maria">
				</div>
				<div class="col-6 lastname">
					<label for="lastName">Sobrenome</label>
					<input type="text" class="form-control" id="lastName" placeholder="ex.: Cruz" minlength="2" pattern="([A-Za-zÀ-ÖØ-öø-ÿ]{2,})+" data-invalid="Digite o seu sobrenome" required value="Cruz">
				</div>

				<?php endif; ?>

				<div class="col-12">
					<label for="email">E-mail</label>
					<input type="email" class="form-control" id="email" placeholder="ex.: maria@gmail.com" required value="maria@gmail.com">
				</div>
				<div class="col-12">
					<label for="mobile">Celular / WhatsApp</label>
					<input type="text" class="form-control" id="mobile" placeholder="(00) 00000-0000" minlength="11" maxlength="15" pattern="\(?\d{2}\)?\s?\d{4,5}-?\d{4}" data-invalid="Digite o seu celular / whatsapp" data-mask="phone-br" required value="(47) 98804-3272">
				</div>
			</div>
		</fieldset>
		<fieldset id="step-delivery">
			<legend>Entrega</legend>
			<div class="col-12">
				<label for="zip">CEP</label>
				<input type="text" class="form-control" id="zip" minlength="8" maxlength="9" pattern="\d{5}-?\d{3}" data-mask="zip-br" required>
			</div>
		</fieldset>
		<input type="submit"/>
	</form>

</div>
<?php
risecheckout_get_footer( 'checkout' );
