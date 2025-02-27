<?php

function risecheckout_wc_fields_shippingpartial( $fields ) {
	$new_fields = array();

	$renames = array(
		'billing_country'       => 'country',
		'billing_name'          => 'name',
		'billing_first_name'    => 'firstname',
		'billing_last_name'     => 'lastname',
		'billing_email'         => 'email',
		'billing_cpf'           => 'cpf',
		'billing_phone'         => 'phone',
		'shipping_postcode'     => 'postcode',
		'shipping_city'         => 'city',
		'shipping_state'        => 'state',
		'shipping_address_1'    => 'address1',
		'shipping_number'       => 'number',
		'shipping_neighborhood' => 'neighborhood',
		'shipping_address_2'    => 'address2',
		'shipping_name'         => 'receiver',
		'shipping_first_name'   => 'receiver_firstname',
		'shipping_last_name'    => 'receiver_lastname',
	);

	$steps = array(
		'customer' => array(
			'firstname',
			'lastname',
			'email',
			'cpf',
			'phone',
		),
		'shipping' => array(
			'postcode',
			'city',
			'state',
			'address1',
			'number',
			'neighborhood',
			'address2',
			'receiver',
		),
	);

	if ( 'yes' === get_option( 'risecheckout_fullname', 'yes' ) ) {
		$fields['billing']['billing_name'] = array(
			'autocomplete' => 'name',
		);
		unset( $fields['billing']['billing_first_name'], $fields['billing']['billing_last_name'] );
		$fields['shipping']['shipping_name'] = array(
			'autocomplete' => 'name',
		);
		unset( $fields['shipping']['shipping_first_name'], $fields['shipping']['shipping_last_name'] );
	}

	foreach ( $fields as $type => $type_fields ) {
		foreach ( $type_fields as $name => $field ) {
			if ( isset( $renames[ $name ] ) ) {
				$field['renamed'] = $name;
				$name             = $renames[ $name ];
			}
			foreach ( $steps as $step => $step_fields ) {
				if ( in_array( $name, $step_fields, true ) ) {
					$field['step'] = $step;
					break;
				}
			}
			$new_fields[ $name ] = $field;
		}
	}
	$fields = $new_fields;

	$merge_fields = risecheckout_fields();

	foreach ( $fields as $name => &$field ) {
		if ( isset( $merge_fields[ $name ] ) ) {
			$field = array_merge( $field, $merge_fields[ $name ] );
		}
	}

	$new_fields = array();

	foreach ( $fields as $name => $field ) {
		if ( isset( $field['step'] ) ) {
			$field_step = $field['step'];
			unset( $field['step'] );
			$new_fields[ $field_step ][ $name ] = $field;
		}
	}

	$fields = $new_fields;
	unset( $field );

	foreach ( $fields as $field_step => $step_fields ) {
		uasort( $fields[ $field_step ], 'wc_checkout_fields_uasort_comparison' );
	}

	return $fields;
}

function risecheckout_fields() {
	$fields = array();
	if ( 'yes' === get_option( 'risecheckout_fullname', 'yes' ) ) {
		$fields['name'] = array(
			'label'       => __( 'Full name', 'risecheckout' ),
			'placeholder' => sprintf(
				/* translators: %s: Example */
				__( 'e.g.: %s', 'risecheckout' ),
				__( 'Mary Anne Johnson', 'risecheckout' )
			),
			'minlength'   => 5,
			'pattern'     => '[A-Za-zÀ-ÖØ-öø-ÿ]{2,}(\s+[A-Za-zÀ-ÖØ-öø-ÿ]{2,})+',
			'invalid'     => sprintf(
				/* translators: %s: Field label */
				__( 'Enter your %s', 'risecheckout' ),
				mb_strtolower( __( 'Full name', 'risecheckout' ) )
			),
			'required'    => true,
			'value'       => __( 'Mary Anne Johnson', 'risecheckout' ),
			'step'        => 'customer',
			'priority'    => 10,
			'info'        => true,
		);
	} else {
		$fields = array_merge(
			$fields,
			array(
				'firstname' => array(
					'class'       => array( 'col-6' ),
					'label'       => __( 'First name', 'risecheckout' ),
					'placeholder' => sprintf(
						/* translators: %s: Example */
						__( 'e.g.: %s', 'risecheckout' ),
						__( 'Mary', 'risecheckout' )
					),
					'minlength'   => 2,
					'pattern'     => '([A-Za-zÀ-ÖØ-öø-ÿ]{2,}(?: [A-Za-zÀ-ÖØ-öø-ÿ]+)*)',
					'invalid'     => sprintf(
						/* translators: %s: Field label */
						__( 'Enter your %s', 'risecheckout' ),
						mb_strtolower( __( 'First name', 'risecheckout' ) )
					),
					'required'    => true,
					'value'       => __( 'Mary', 'risecheckout' ),
					'step'        => 'customer',
					'priority'    => 5,
					'info'        => 'name',
					'info_label'  => __( 'Full name', 'risecheckout' ),
				),
				'lastname'  => array(
					'class'       => array( 'col-6' ),
					'label'       => __( 'Last name', 'risecheckout' ),
					'placeholder' => sprintf(
						/* translators: %s: Example */
						__( 'e.g.: %s', 'risecheckout' ),
						__( 'Johnson', 'risecheckout' )
					),
					'minlength'   => 2,
					'pattern'     => '([A-Za-zÀ-ÖØ-öø-ÿ]{2,}(?: [A-Za-zÀ-ÖØ-öø-ÿ]+)*)',
					'invalid'     => sprintf(
						/* translators: %s: Field label */
						__( 'Enter your %s', 'risecheckout' ),
						mb_strtolower( __( 'Last name', 'risecheckout' ) )
					),
					'required'    => true,
					'value'       => __( 'Johnson', 'risecheckout' ),
					'step'        => 'customer',
					'priority'    => 10,
					'info'        => 'name',
					'info_label'  => __( 'Full name', 'risecheckout' ),
				),
			)
		);
	}
	$fields = array_merge(
		$fields,
		array(
			'email'        => array(
				'label'       => __( 'Email', 'risecheckout' ),
				'class'       => array( 'col-12' ),
				'type'        => 'email',
				'placeholder' => sprintf(
					/* translators: %s: Example */
					__( 'e.g.: %s', 'risecheckout' ),
					sprintf( '%s@gmail.com', sanitize_title( __( 'Mary', 'risecheckout' ) ) )
				),
				'pattern'     => '[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}',
				'invalid'     => sprintf(
					/* translators: %s: Field label */
					__( 'Invalid %s. Please check if you typed it correctly.', 'risecheckout' ),
					__( 'Email', 'risecheckout' )
				),
				'required'    => true,
				'value'       => sprintf(
					'%s@gmail.com',
					sanitize_title( __( 'Mary', 'risecheckout' ) )
				),
				'step'        => 'customer',
				'priority'    => 20,
				'info'        => true,
			),
			'cpf'          => array(
				'label'       => 'CPF',
				'class'       => array( 'col-12' ),
				'placeholder' => '000.000.000-00',
				'minlength'   => 11,
				'maxlength'   => 14,
				'pattern'     => '\d{3}\.?\d{3}\.?\d{3}-?\d{2}',
				'validate'    => array( 'cpf' ),
				'mask'        => 'cpf',
				'clean'       => 'numbers',
				'invalid'     => sprintf(
					/* translators: %s: Field label */
					__( 'Enter a valid %s', 'risecheckout' ),
					'CPF'
				),
				'required'    => true,
				'value'       => '154.505.032-53',
				'step'        => 'customer',
				'priority'    => 30,
				'info_prefix' => true,
			),
			'phone'        => array(
				'label'       => __( 'Mobile', 'risecheckout' ),
				'class'       => array( 'col-12' ),
				'prefix'      => '+55',
				'placeholder' => '(00) 000000-0000',
				'minlength'   => 11,
				'maxlength'   => 15,
				'pattern'     => '\(?\d{2}\)?\s?\d{4,5}-?\d{4}',
				// 'pattern'     => '[\(\d\)\s-]+',
				'mask'        => 'phone-br',
				'clean'       => 'numbers',
				'invalid'     => sprintf(
					/* translators: %s: Field label */
					__( 'Enter a valid %s', 'risecheckout' ),
					mb_strtolower( __( 'Mobile', 'risecheckout' ) )
				),
				'required'    => true,
				'value'       => '(47) 98804-3272',
				'step'        => 'customer',
				'priority'    => 40,
			),
			'postcode'     => array(
				'class'     => array( 'col-7', 'postcode-field' ),
				// 'label'         => __( 'Postcode', 'risecheckout' ),
				'minlength' => 8,
				'maxlength' => 9,
				'pattern'   => '\d{5}-?\d{3}',
				'mask'      => 'postcode-br',
				'invalid'   => sprintf(
					/* translators: %s: Field label */
					__( 'Enter a valid %s', 'risecheckout' ),
					mb_strtolower( __( 'Zip', 'risecheckout' ) )
				),
				'required'  => true,
				'step'      => 'shipping',
				'priority'  => 50,
				'loading'   => true,
				// 'value'         => '89240-000',
				'value'     => '79814-054',
			),
			'city'         => array(
				'class'        => array( 'col-8', 'address-field' ),
				'priority'     => 60,
				'column_break' => true,
				// 'value'    => 'São Francisco do Sul',
				'value'        => 'Dourados',
			),
			'state'        => array(
				'class'    => array( 'col-4', 'address-field' ),
				'priority' => 65,
				// 'value'    => 'SC',
				'value'    => 'MS',
			),
			'address1'     => array(
				'class'       => array( 'col-12', 'address-field' ),
				'placeholder' => '',
				'priority'    => 70,
				'value'       => 'Rua Edgar Xavier de Matos',
			),
			'number'       => array(
				'class'    => array( 'col-4', 'address-field' ),
				'priority' => 80,
				'value'    => 256,
			),
			'neighborhood' => array(
				'class'    => array( 'col-8', 'address-field' ),
				'priority' => 85,
				'value'    => 'Jardim Itália',
			),
			'address2'     => array(
				'class'       => array( 'col-12', 'address-field' ),
				'placeholder' => '',
				'priority'    => 90,

				// 'value'    => 'SC',  'value' => 'Ap101',
			),
			'receiver'     => array(
				'label'       => __( 'Receiver', 'risecheckout' ),
				'class'       => array( 'col-12', 'address-field' ),
				'placeholder' => sprintf(
					/* translators: %s: Example */
					__( 'e.g.: %s', 'risecheckout' ),
					__( 'Mary Anne Johnson', 'risecheckout' )
				),
				'minlength'   => 5,
				'pattern'     => '[A-Za-zÀ-ÖØ-öø-ÿ]{2,}(\s+[A-Za-zÀ-ÖØ-öø-ÿ]{2,})+',
				'invalid'     => sprintf(
					/* translators: %s: Field label */
					__( 'Enter %s', 'risecheckout' ),
					mb_strtolower( __( 'Receiver', 'risecheckout' ) )
				),
				'value'       => __( 'Mary Anne Johnson', 'risecheckout' ),
				'step'        => 'shipping',
				'required'    => true,
				'priority'    => 100,
			),
		)
	);

	uasort(
		$fields,
		function ( $a, $b ) {
			return $a['priority'] <=> $b['priority'];
		}
	);

	return $fields;
}

function risecheckout_steps() {
	return array(
		'customer' => array(
			'title'       => __( 'Identify yourself', 'risecheckout' ),
			'description' => __(
				'We will use your email to: Identify your profile, purchase history, order ' .
				'notification and shopping cart.',
				'risecheckout'
			),
			'continue'    => __( 'Continue', 'risecheckout' ),
			'edit'        => __( 'Edit', 'risecheckout' ),
		),
		'shipping' => array(
			'title'       => __( 'Shipping', 'risecheckout' ),
			// 'description' => __(
			//  'Register or select an address',
			//  'risecheckout'
			// ),
			'description' => __(
				'Register an address',
				'risecheckout'
			),
			'placeholder' => __(
				'Fill in your personal information to continue',
				'risecheckout'
			),
			'save'        => __( 'Save', 'risecheckout' ),
		),
		'payment'  => array(
			'title'       => __( 'Payment', 'risecheckout' ),
			'placeholder' => __( 'Fill in your shipping information to continue', 'risecheckout' ),
		),
	);
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

		<?php if ( get_option( 'risecheckout_saved_address' ) ) : ?>

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

		<input <?php echo implode( ' ', $input ); ?>><?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

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
