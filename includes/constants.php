<?php

function risecheckout_define_constants() {
	risecheckout_define( 'RISECHECKOUT_ABSPATH', dirname( RISECHECKOUT_PLUGIN_FILE ) . '/' );
	risecheckout_define( 'RISECHECKOUT_VERSION', '1.0.0-dev' );
}
risecheckout_define_constants();

function risecheckout_define( $name, $value ) {
	if ( ! defined( $name ) ) {
		define( $name, $value ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.VariableConstantNameFound
	}
}
