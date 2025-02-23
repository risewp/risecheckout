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
	check_ajax_referer( 'risecheckout-customer' );

	wp_send_json_success($_POST);
}
