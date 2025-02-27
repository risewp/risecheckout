<?php

function risecheckout_get_option( $name ) {
	return get_option( "risecheckout_{$name}" );
}

function risecheckout_set_option( $name, $value ) {
	return set_option( "risecheckout_{$name}", $value );
}

function risecheckout_option( $name ) {
	return 'yes' === risecheckout_get_option( $name );
}
