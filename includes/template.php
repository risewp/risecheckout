<?php

defined( 'ABSPATH' ) || exit;

function risecheckout_template_loader( $template ) {
	if ( risecheckout_is_checkout() && ! risecheckout_is_order_received_page() ) {
		$template = risecheckout_plugin_path() . '/templates/checkout.php';
	}
	return $template;
}
add_filter( 'template_include', 'risecheckout_template_loader' );

function risecheckout_wc_get_template( $template, $template_name, $args, $template_path, $default_path ) {
	$plugin_template = risecheckout_plugin_path() . '/templates/' . $template_name;

	if ( file_exists( $plugin_template ) ) {
		$template = $plugin_template;
	}

	return $template;
}
add_filter( 'wc_get_template', 'risecheckout_wc_get_template', 10, 5 );

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

		<?php if ( true ) : ?>

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

function risecheckout_wc_form_field( $field, $key, $args, $value ) {
	$defaults = array(
		'type'              => 'text',
		'label'             => '',
		'description'       => '',
		'placeholder'       => '',
		'maxlength'         => false,
		'minlength'         => false,
		'required'          => false,
		'autocomplete'      => false,
		'id'                => $key,
		'class'             => array(),
		'label_class'       => array(),
		'input_class'       => array(),
		'return'            => false,
		'options'           => array(),
		'custom_attributes' => array(),
		'validate'          => array(),
		'default'           => '',
		'autofocus'         => '',
		'priority'          => '',
		'unchecked_value'   => null,
		'checked_value'     => '1',
		'break'             => false,
	);

	$args = wp_parse_args( $args, $defaults );
	$args = apply_filters( 'woocommerce_form_field_args', $args, $key, $value );

	if ( is_string( $args['class'] ) ) {
		$args['class'] = array( $args['class'] );
	}

	if ( $args['required'] ) {
		// hidden inputs are the only kind of inputs that don't need an `aria-required` attribute.
		// checkboxes apply the `custom_attributes` to the label - we need to apply the attribute on the input itself, instead.
		if ( ! in_array( $args['type'], array( 'hidden', 'checkbox' ), true ) ) {
			$args['custom_attributes']['aria-required'] = 'true';
		}

		$args['class'][] = 'validate-required';
		$required        = '';
	} else {
		$required = ' <span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
	}

	if ( is_string( $args['label_class'] ) ) {
		$args['label_class'] = array( $args['label_class'] );
	}

	if ( is_null( $value ) ) {
		$value = $args['default'];
	}

	// Custom attribute handling.
	$custom_attributes         = array();
	$args['custom_attributes'] = array_filter( (array) $args['custom_attributes'], 'strlen' );

	if ( $args['maxlength'] ) {
		$args['custom_attributes']['maxlength'] = absint( $args['maxlength'] );
	}

	if ( $args['minlength'] ) {
		$args['custom_attributes']['minlength'] = absint( $args['minlength'] );
	}

	if ( ! empty( $args['autocomplete'] ) ) {
		$args['custom_attributes']['autocomplete'] = $args['autocomplete'];
	}

	if ( true === $args['autofocus'] ) {
		$args['custom_attributes']['autofocus'] = 'autofocus';
	}

	if ( $args['description'] ) {
		$args['custom_attributes']['aria-describedby'] = $args['id'] . '-description';
	}

	if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
		foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
		}
	}

	if ( ! empty( $args['validate'] ) ) {
		foreach ( $args['validate'] as $validate ) {
			$args['class'][] = 'validate-' . $validate;
		}
	}

	$field           = '';
	$label_id        = $args['id'];
	$sort            = $args['priority'] ? $args['priority'] : '';
	$field_container = '<p class="form-row %1$s" id="%2$s" data-priority="' . esc_attr( $sort ) . '">%3$s</p>';

	switch ( $args['type'] ) {
		case 'country':
			$countries = 'shipping_country' === $key ? WC()->countries->get_shipping_countries() : WC()->countries->get_allowed_countries();

			if ( 1 === count( $countries ) ) {
				$args['class'][] = 'unique-country';

				$field .= '<input type="text" value="' . current( array_values( $countries ) ) . '" readonly="readonly" />';

				$field .= '<input type="hidden" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="' . current( array_keys( $countries ) ) . '" ' . implode( ' ', $custom_attributes ) . ' class="country_to_state" readonly="readonly" />';
			} else {
				$data_label = ! empty( $args['label'] ) ? 'data-label="' . esc_attr( $args['label'] ) . '"' : '';

				$field = '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="country_to_state country_select ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ? $args['placeholder'] : esc_attr__( 'Select a country / region&hellip;', 'woocommerce' ) ) . '" ' . $data_label . '><option value="">' . esc_html__( 'Select a country / region&hellip;', 'woocommerce' ) . '</option>';

				foreach ( $countries as $ckey => $cvalue ) {
					$field .= '<option value="' . esc_attr( $ckey ) . '" ' . selected( $value, $ckey, false ) . '>' . esc_html( $cvalue ) . '</option>';
				}

				$field .= '</select>';

				$field .= '<noscript><button type="submit" name="woocommerce_checkout_update_totals" value="' . esc_attr__( 'Update country / region', 'woocommerce' ) . '">' . esc_html__( 'Update country / region', 'woocommerce' ) . '</button></noscript>';
			}

			break;
		case 'state':
			/* Get country this state field is representing */
			$for_country = isset( $args['country'] ) ? $args['country'] : WC()->checkout->get_value( 'billing_state' === $key ? 'billing_country' : 'shipping_country' );
			$states      = WC()->countries->get_states( $for_country );

			if ( is_array( $states ) && empty( $states ) ) {
				$field_container = '<p class="form-row %1$s" id="%2$s" style="display: none">%3$s</p>';

				$field .= '<input type="hidden" class="hidden" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="" ' . implode( ' ', $custom_attributes ) . ' placeholder="' . esc_attr( $args['placeholder'] ) . '" readonly="readonly" data-input-classes="' . esc_attr( implode( ' ', $args['input_class'] ) ) . '"/>';
			} elseif ( ! is_null( $for_country ) && is_array( $states ) ) {
				$data_label = ! empty( $args['label'] ) ? 'data-label="' . esc_attr( $args['label'] ) . '"' : '';

				$field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="state_select ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ? $args['placeholder'] : esc_html__( 'Select an option&hellip;', 'woocommerce' ) ) . '"  data-input-classes="' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . $data_label . '>
					<option value="">' . esc_html__( 'Select an option&hellip;', 'woocommerce' ) . '</option>';

				foreach ( $states as $ckey => $cvalue ) {
					$field .= '<option value="' . esc_attr( $ckey ) . '" ' . selected( $value, $ckey, false ) . '>' . esc_html( $cvalue ) . '</option>';
				}

				$field .= '</select>';
			} else {
				$field .= '<input type="text" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" value="' . esc_attr( $value ) . '"  placeholder="' . esc_attr( $args['placeholder'] ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" ' . implode( ' ', $custom_attributes ) . ' data-input-classes="' . esc_attr( implode( ' ', $args['input_class'] ) ) . '"/>';
			}

			break;
		case 'textarea':
			$field .= '<textarea name="' . esc_attr( $key ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" ' . ( empty( $args['custom_attributes']['rows'] ) ? ' rows="2"' : '' ) . ( empty( $args['custom_attributes']['cols'] ) ? ' cols="5"' : '' ) . implode( ' ', $custom_attributes ) . '>' . esc_textarea( $value ) . '</textarea>';

			break;
		case 'checkbox':
			$field = '<label class="checkbox ' . esc_attr( implode( ' ', $args['label_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . '>';

			// Output a hidden field so a value is POSTed if the box is not checked.
			if ( ! is_null( $args['unchecked_value'] ) ) {
				$field .= sprintf( '<input type="hidden" name="%1$s" value="%2$s" />', esc_attr( $key ), esc_attr( $args['unchecked_value'] ) );
			}

			$field .= sprintf(
				'<input type="checkbox" name="%1$s" id="%2$s" value="%3$s" class="%4$s" %5$s%6$s /> %7$s',
				esc_attr( $key ),
				esc_attr( $args['id'] ),
				esc_attr( $args['checked_value'] ),
				esc_attr( 'input-checkbox ' . implode( ' ', $args['input_class'] ) ),
				checked( $value, $args['checked_value'], false ),
				$args['required'] ? ' aria-required="true"' : '',
				wp_kses_post( $args['label'] )
			);

			$field .= $required . '</label>';

			break;
		case 'text':
		case 'password':
		case 'datetime':
		case 'datetime-local':
		case 'date':
		case 'month':
		case 'time':
		case 'week':
		case 'number':
		case 'email':
		case 'url':
		case 'tel':
			$field .= '<input type="' . esc_attr( $args['type'] ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '"  value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';

			break;
		case 'hidden':
			$field .= '<input type="' . esc_attr( $args['type'] ) . '" class="input-hidden ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';

			break;
		case 'select':
			$field   = '';
			$options = '';

			if ( ! empty( $args['options'] ) ) {
				foreach ( $args['options'] as $option_key => $option_text ) {
					if ( '' === $option_key ) {
						// If we have a blank option, select2 needs a placeholder.
						if ( empty( $args['placeholder'] ) ) {
							$args['placeholder'] = $option_text ? $option_text : __( 'Choose an option', 'woocommerce' );
						}
						$custom_attributes[] = 'data-allow_clear="true"';
					}
					$options .= '<option value="' . esc_attr( $option_key ) . '" ' . selected( $value, $option_key, false ) . '>' . esc_html( $option_text ) . '</option>';
				}

				$field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="select ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ) . '">
						' . $options . '
					</select>';
			}

			break;
		case 'radio':
			$label_id .= '_' . current( array_keys( $args['options'] ) );

			if ( ! empty( $args['options'] ) ) {
				foreach ( $args['options'] as $option_key => $option_text ) {
					$field .= '<input type="radio" class="input-radio ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" value="' . esc_attr( $option_key ) . '" name="' . esc_attr( $key ) . '" ' . implode( ' ', $custom_attributes ) . ' id="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '"' . checked( $value, $option_key, false ) . ' />';
					$field .= '<label for="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '" class="radio ' . implode( ' ', $args['label_class'] ) . '">' . esc_html( $option_text ) . '</label>';
				}
			}

			break;
	}

	if ( ! empty( $field ) ) {
		$field_html = '';

		$break = '';
		if ( $args['break'] ) {
			$break = '<div class="break"></div>';
		}

		if ( $args['label'] && 'checkbox' !== $args['type'] ) {
			$field_html .= '<label for="' . esc_attr( $label_id ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) . '">' . wp_kses_post( $args['label'] ) . $required . '</label>';
		}

		$field_html .= $field;

		if ( $args['description'] ) {
			$field_html .= '<span class="description" id="' . esc_attr( $args['id'] ) . '-description" aria-hidden="true">' . wp_kses_post( $args['description'] ) . '</span>';
		}

		$container_class = esc_attr( implode( ' ', $args['class'] ) );
		$container_id    = esc_attr( $args['id'] ) . '_field';
		$field           = $break . sprintf( $field_container, $container_class, $container_id, $field_html );
	}

	return $field;
}
add_filter( 'woocommerce_form_field', 'risecheckout_wc_form_field', 10, 4 );

function risecheckout_field( $field ) {
	$id = $field->id;

	$class = isset( $field->class ) ? $field->class : array( 'col-12' );
	$type  = isset( $field->type ) ? $field->type : 'text';
	if ( isset( $field->column_break ) ) {
		echo '<div class="w-100"></div>';
	}
	?>

	<div class="<?php echo esc_attr( implode( ' ', $class ) ); ?>">
		<label for="<?php echo esc_attr( $id ); ?>" class="form-label">
			<?php echo esc_html( $field->label ); ?>

			<?php if ( ! isset( $field->required ) || ! $field->required ) : ?>

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

function risecheckout_body_class( $classes ) {
	$form_columns = 'yes' === get_option( 'risecheckout_form_columns', 'no' );
	if ( risecheckout_is_checkout() && ! risecheckout_is_order_received_page() && $form_columns ) {
		$classes[] = 'checkout-form-columns';
	}
	return $classes;
}
add_filter( 'body_class', 'risecheckout_body_class' );

function risecheckout_root_class( $classes ) {
	$look_a_like = 'yes' === get_option( 'risecheckout_look_a_like', 'no' );
	if ( risecheckout_is_checkout() && ! risecheckout_is_order_received_page() && $look_a_like ) {
		$classes[] = 'look_a_like';
	}
	return $classes;
}
add_filter( 'root_class', 'risecheckout_root_class' );

function risecheckout_replace_emojis_with_font_awesome( $text ) {
	$replacements = array(
		'🏬'  => '<i class="fal fa-store"></i>',
		'📍'  => '<i class="fal fa-map-marker-alt"></i>',
		'✉️' => '<i class="fal fa-envelope"></i>',
		'📞'  => '<i class="fal fa-phone"></i>',
	);

	foreach ( $replacements as $emoji => $faIcon ) {
		$text = str_replace( $emoji, $faIcon, $text );
	}

	return $text;
}

/**
 * Function to automatically convert emails, URLs, and phone numbers into clickable links.
 *
 * This function uses WordPress' native make_clickable() to auto-link emails and URLs.
 * It then processes phone numbers by removing any characters that are not digits,
 * except for a leading '+' (for international country codes), and wraps them in a clickable tel: link.
 *
 * @param string $content The content to process.
 * @return string The processed content with clickable links.
 */
function risecheckout_make_clickable( $content ) {
	// First, use WordPress' built-in function to link emails and URLs
	$content = make_clickable( $content );

	// Regex pattern to match phone numbers with various formatting (spaces, dashes, parentheses)
	// This pattern matches a phone number that starts optionally with '+' followed by digits and allowed characters.
	$pattern = '/(\+?[0-9\-\s\(\)]{7,}[0-9])/';

	// Process each matched phone number using a callback function
	$content = preg_replace_callback(
		$pattern,
		function ( $matches ) {
			$phone = $matches[0];
			// Clean the phone number: remove all characters except digits,
			// while preserving a '+' if it's at the beginning.
			$cleaned_phone = preg_replace( '/(?!^\+)[^\d]/', '', $phone );
			// Return the original phone number wrapped in a clickable tel: link with the cleaned number
			return '<a href="tel:' . $cleaned_phone . '">' . $phone . '</a>';
		},
		$content
	);

	return $content;
}

function risecheckout_header_text() {
	$text = get_option( 'risecheckout_header_text' );
	if ( 'yes' === get_option( 'risecheckout_text_make_clickable', 'no' ) ) {
		$text = risecheckout_make_clickable( $text );
	}
	$text = risecheckout_replace_emojis_with_font_awesome( wpautop( $text ) );

	$text = preg_replace(
		'/<p>\s*(<i class="fal fa-[^"]+"><\/i>)\s*<a href="([^"]+)">([^<]+)<\/a>\s*<\/p>/',
		'<p><a href="$2">$1 $3</a></p>',
		$text
	);

	return $text;
}

function risecheckout_header_toggler_icon() {
	$icon = '<span class="navbar-toggler-icon"></span>';
	preg_match_all( '/<i\s+class="fal\s+fa-[^"]+"><\/i>/', risecheckout_header_text(), $icons );
	if ( ! empty( current( $icons ) ) ) {
		$icons = array_unique( current( $icons ) );
		$icon  = implode( ' ', $icons );
	}
	return $icon;
}

function risecheckout_order_review_text() {
	echo wp_kses_post( '<div class="order-review-text">' . wpautop( get_option( 'risecheckout_order_review_text' ) ) . '</div>' );
}
add_action( 'woocommerce_checkout_order_review', 'risecheckout_order_review_text', 21 );

function risecheckout_footer_text() {
	return risecheckout_replace_emojis_with_font_awesome( wpautop( get_option( 'risecheckout_footer_text' ) ) );
}

function risecheckout_wc_dequeue_select2() {
	wp_dequeue_script( 'selectWoo' );
	wp_dequeue_style( 'select2' );
}
add_action( 'wp_enqueue_scripts', 'risecheckout_wc_dequeue_select2', 11 );
