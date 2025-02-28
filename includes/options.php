<?php

function risecheckout_get_option( $option, $default_value = false ) {
	return get_option( "risecheckout_{$option}", $default_value );
}

function risecheckout_update_option( $option, $value ) {
	return update_option( "risecheckout_{$option}", $value );
}

function risecheckout_option( $option, $default_value ) {
	return 'yes' === risecheckout_get_option( $option, $default_value );
}
