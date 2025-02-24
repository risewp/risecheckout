<?php

function risecheckout_add_ajax_events() {
	$ajax_events_nopriv = array(
		'customer',
		'postcode_br',
	);

	foreach ( $ajax_events_nopriv as $ajax_event ) {
		add_action( 'wp_ajax_woocommerce_risecheckout_' . $ajax_event, 'risecheckout_ajax_' . $ajax_event );
		add_action( 'wp_ajax_nopriv_woocommerce_risecheckout_' . $ajax_event, 'risecheckout_ajax_' . $ajax_event );

		add_action( 'wc_ajax_risecheckout_' . $ajax_event, 'risecheckout_ajax_' . $ajax_event );
	}
}
risecheckout_add_ajax_events();

function risecheckout_ajax_check_referer( $action ) {
	$action = 'risecheckout-' . preg_replace('/^risecheckout-/', '', $action);
	$nonce = isset( $_SERVER['HTTP_X_WPNONCE'] ) ? sanitize_text_field( $_SERVER['HTTP_X_WPNONCE'] ) : '';
	if ( ! wp_verify_nonce( $nonce, $action ) ) {
		wp_die( -1, 403 );
	}
}

function risecheckout_ajax_customer() {
	risecheckout_ajax_check_referer( 'customer' );

	// TODO: Server validation, like required fields not empty.
	if ( 'yes' === get_option( 'risecheckout_fullname', 'yes' ) ) {
		$name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
	} else {
		$firstname = isset( $_POST['firstname'] ) ? sanitize_text_field( $_POST['firstname'] ) : '';
		$lastname = isset( $_POST['lastname'] ) ? sanitize_text_field( $_POST['lastname'] ) : '';
		$name = trim( implode( ' ', array( $firstname, $lastname ) ) );
	}
	$email = isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : '';
	$cpf = isset( $_POST['cpf'] ) ? sanitize_text_field( $_POST['cpf'] ) : '';
	// $phone = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';

	wp_send_json_success(
		array(
			'name' => $name,
			'email' => $email,
			'cpf' => $cpf,
		)
	);
}

function risecheckout_postcode_br($postcode) {
	$postcode = preg_replace('/\D/', '', $postcode);

	$transient_key = 'risecheckout_postcode_br_' . $postcode;
	$cached_data = get_transient($transient_key);

	if (false !== $cached_data) {
		return json_decode($cached_data, true);
	}

	$response = wp_remote_get("https://viacep.com.br/ws/{$postcode}/json/");
	$data = array();

	if (!is_wp_error($response)) {
		$body = wp_remote_retrieve_body($response);
		$json = json_decode($body);

		if (!isset($json->erro)) {
			$data = array_intersect_key((array) $json, array_flip([
				'logradouro', 'bairro', 'localidade', 'uf'
			]));

			$data = array(
				'state'        => $data['uf'],
				'city'         => $data['localidade'],
				'neighborhood' => $data['bairro'],
				'street'       => $data['logradouro'],
			);

			$data = array_filter($data);

			set_transient($transient_key, json_encode($data));
		}
	}

	return $data;
}

function risecheckout_ajax_postcode_br() {
	risecheckout_ajax_check_referer( 'postcode-br' );

	$postcode = sanitize_text_field( trim(file_get_contents('php://input')) );
	$postcode = preg_replace('/\D/', '', $postcode);

	$data = risecheckout_postcode_br($postcode);

	if (!empty($data)) {
		wp_send_json_success($data);
	} else {
		wp_send_json_error(array('message'=>__('Invalid postcode', 'risecheckout')));
	}
}
