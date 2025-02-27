<?php

function risecheckout_load_plugin_textdomain() {
	$locale = determine_locale();

	$locale = apply_filters( 'plugin_locale', $locale, 'risecheckout' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

	unload_textdomain( 'risecheckout', true );
	load_textdomain( 'risecheckout', dirname( RISECHECKOUT_PLUGIN_FILE ) . '/languages/' . $locale . '.mo' );
	load_plugin_textdomain( 'risecheckout', false, plugin_basename( dirname( RISECHECKOUT_PLUGIN_FILE ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'risecheckout_load_plugin_textdomain' );
