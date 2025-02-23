<?php

function risecheckout_add_ajax_events() {
	$ajax_events_nopriv = array(
		'customer',
	);

	foreach ( $ajax_events_nopriv as $ajax_event ) {
		add_action( 'wp_ajax_woocommerce_risecheckout_' . $ajax_event, 'risecheckout_ajax_' . $ajax_event );
		add_action( 'wp_ajax_nopriv_woocommerce_risecheckout_' . $ajax_event, 'risecheckout_ajax_' . $ajax_event );

		add_action( 'wc_ajax_risecheckout_' . $ajax_event, 'risecheckout_ajax_' . $ajax_event );
	}
}
risecheckout_add_ajax_events();

function risecheckout_ajax_customer() {
	$nonce = isset( $_SERVER['HTTP_X_WPNONCE'] ) ? sanitize_text_field( $_SERVER['HTTP_X_WPNONCE'] ) : '';
	if ( ! wp_verify_nonce( $nonce, 'risecheckout-customer' ) ) {
		wp_die( -1, 403 );
	}

	// TODO: Server validation, like required fields not empty.
	$name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
	$email = isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : '';
	$cpf = isset( $_POST['cpf'] ) ? sanitize_text_field( $_POST['cpf'] ) : '';
	$mobile = isset( $_POST['mobile'] ) ? sanitize_text_field( $_POST['mobile'] ) : '';

	wp_send_json_success(
		array(
			'name' => $name,
			'email' => $email,
			'cpf' => $cpf,
			// 'mobile' => $mobile,
		)
	);
}
