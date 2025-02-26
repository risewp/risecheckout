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
