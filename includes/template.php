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

function risecheckout_step_open( $step ) {
	$slug = $step->slug;

	$fieldset = (object) array(
		'class' => "{$slug}-step card card-body",
		'id'    => "step-{$slug}",
	);
	if ( isset( $step->placeholder ) ) {
		$fieldset->data_placeholder = $step->placeholder;
	}
	if ( isset( $step->continue ) ) {
		$fieldset->data_continue = $step->continue;
	}
	if ( isset( $step->save ) ) {
		$fieldset->data_save = $step->save;
	}
	if ( isset( $step->edit ) ) {
		$fieldset->data_edit = $step->edit;
	}
	$fieldset = (array) $fieldset;
	foreach ( $fieldset as $key => &$value ) {
		$key   = preg_replace( '/^(data)_/', '$1-', $key );
		$value = sprintf(
			'%s="%s"',
			esc_attr( $key ),
			esc_attr( $value )
		);
	}

	$html = sprintf( '<fieldset %s>', implode( ' ', array_values( $fieldset ) ) );

	ob_start();
	?>

	<legend><?php echo esc_html( $step->title ); ?></legend>

	<?php if ( isset( $step->description ) ) : ?>

	<p class="desc desc-form"><?php echo esc_html( $step->description ); ?></p>

	<?php endif; ?>

	<?php if ( 'xcustomer' === $slug ) : ?>

	<div class="infos">
		<p class="name" id="info-name" data-label="Nome completo">Maria de Almeida Cruz</p>
		<p class="email" id="info-email" data-label="E-mail">maria@gmail.com</p>
		<p class="cpf" id="info-cpf" data-label="CPF">CPF <span>154.505.032-53</span></p>
	</div>

	<?php elseif ( 'xshipping' === $slug ) : ?>

		<?php if (true) : ?>

	<div class="container-addresses ">
		<div class="box-address selected">
			<label for="address-189074050" class="inner-box holder-icheck">
				<div class="iradio_minimal checked" style="position: relative;">
					<input type="radio" name="address" id="address-189074050" class="input-icheck select-customer-address" value="189074050" checked="" data-url="https://seguro.mrmaverick.com.br/cart/address" data-zipcode="79814054" style="position: absolute; visibility: hidden;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
				</div>
				<span class="inner-label black-80 f11">
					<span class="medium">Rua Edgar Xavier de Matos, 256 - Jardim Itália</span> <br>
					Dourados-MS | CEP <span class="zipcode">79814-054</span>
				</span>
			</label>
		</div>
		<div class="container-shipment">
			<hr>
			<div class="black-80 mt15 mb15 --primary-text">Escolha uma forma de entrega:</div>
				<div class="shipment-options">
					<label for="shipment-m-1" class="option clearfix selected">
						<div class="iradio_minimal checked" style="position: relative;">
							<input type="radio" name="shipment-service" class="input-icheck select-shipment-service" value="JADLOG - DE 4 A 7 DIAS ÚTEIS" checked style="position: absolute; visibility: hidden;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
						</div>
						<div class="inner-label">
							<div class="text pull-left black-80 f12">
								<span class="medium block">JADLOG - de 4 a 7 dias úteis</span>
								<span class="shipping-time">Entrega garantida</span>
							</div>
							<div class="price pull-right f12 text-right">R$ 9,90</div>
						</div>
					</label>
				</div>
			</div>
		</div>

		<div class="mt25">
			<button type="submit" class="btn btn-primary btn-block btn-send link-box-checkout">
				Continuar
			</button>
		</div>
	</div>

		<?php else : ?>

	<button class="btn-edit" type="submit" data-action="edit" aria-label="Editar" data-bs-toggle="tooltip" data-bs-title="Editar"></button>
	<dl class="infos">
		<dt>Endereço para entrega</dt>
		<dd>
			<span data-info="address1">Rua Edgar Xavier de Matos</span>, <span data-info="number">256</span> - <span data-info="neighborhood">Jardim Itália</span><br>
			<span>Complemento: <span data-info="address2">Ap101</span><br>
			<span><span data-info="city">Dourados</span>-<span data-info="state">SC</span> | <span>CEP <span data-info="postcode">79814-054</span></span>
		</dd>
		<dt>Forma de entrega</dt>
		<dd>JADLOG - de 4 a 7 dias úteis R$ 9,90</dd>
	</dl>

		<?php endif; ?>

	<?php endif; ?>

	<?php

	$html .= ob_get_clean();

	return $html;
}

function risecheckout_step_fields( $fields, $slug ) {
	$step_fields = array();
	foreach ( $fields as $id => $field ) {
		$field = (object) $field;

		if ( isset( $field->step ) && $slug === $field->step ) {
			$step_fields[ $id ] = (array) $field;
		}
	}
	return $step_fields;
}

function risecheckout_field( $field ) {
	$id = $field->id;

	$class = isset( $field->class ) ? $field->class : array( 'col-12' );
	$type          = isset( $field->type ) ? $field->type : 'text';
	if ( isset( $field->column_break ) ) {
		echo '<div class="w-100"></div>';
	}
	?>

	<div class="<?php echo esc_attr( implode( ' ', $class ) ); ?>">
		<label for="<?php echo esc_attr( $id ); ?>" class="form-label">
			<?php echo esc_html( $field->label ); ?>

			<?php if (!isset($field->required) || !$field->required) :?>

			<span class="text-body-secondary"><?php echo esc_html( sprintf( '(%s)', __( 'Optional', 'risecheckout' ) ) ); ?></span>

			<?php endif; ?>

		</label>

		<?php
		$input = (object) array(
			'type'  => $type,
			'class' => 'form-control',
			'id'    => $id,
			'name'  => $id,
		);
		if ( isset( $field->placeholder ) ) {
			$input->placeholder = $field->placeholder;
		}
		if ( isset( $field->minlength ) ) {
			$input->minlength = $field->minlength;
		}
		if ( isset( $field->maxlength ) ) {
			$input->maxlength = $field->maxlength;
		}
		if ( isset( $field->pattern ) ) {
			$input->pattern = $field->pattern;
		}
		if ( isset( $field->validate ) ) {
			$input->data_validate = implode( ',', $field->validate );
		}
		if ( isset( $field->mask ) ) {
			$input->data_mask = $field->mask;
		}
		if ( isset( $field->clean ) ) {
			$input->data_clean = $field->clean;
		}
		if ( isset( $field->required ) && $field->required ) {
			$input->required = true;
		}
		if ( isset( $field->value ) ) {
			$input->value = $field->value;
		}
		if ( isset( $field->info ) ) {
			$input->data_info = $field->info;
		}
		if ( isset( $field->info_prefix ) ) {
			$input->data_info_prefix = $field->info_prefix;
		}
		if ( isset( $field->info_label ) ) {
			$input->data_info_label = $field->info_label;
		}
		if ( isset( $field->loading ) ) {
			$input->data_loading = $field->loading;
		}
		if ( isset( $field->autocomplete ) ) {
			$input->autocomplete = $field->autocomplete;
		}
		if ( isset( $field->priority ) ) {
			$input->data_priority = $field->priority;
		}
		$input = (array) $input;
		foreach ( $input as $key => &$value ) {
			if ( preg_match( '/^(data)_/', $key ) ) {
				$key = str_replace( '_', '-', $key );
			}
			$true_without_value = array(
				'required',
				'data-info',
				'data-info-prefix',
				'data-loading',
			);
			if ( in_array( $key, $true_without_value, true ) && true === $value ) {
				$value = esc_attr( $key );
			} else {
				$value = esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
			}
		}
		$input = array_values( $input );

		if ( isset( $field->prefix ) ) :
			?>

		<div class="input-group has-validation">
			<span class="input-group-text"><?php echo esc_html( $field->prefix ); ?></span>

		<?php endif; ?>

		<input <?php echo implode( ' ', $input ); ?>>

		<?php if ( isset( $field->invalid ) ) : ?>

		<div class="invalid-feedback"><?php echo esc_html( $field->invalid ); ?></div>

		<?php endif; ?>

		<?php if ( isset( $field->prefix ) ) : ?>

		</div>

		<?php endif; ?>
	</div>

	<?php
}
