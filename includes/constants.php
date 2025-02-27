<?php
/**
 * RiseCheckout Constants File
 *
 * This file defines essential constants used throughout the plugin.
 *
 * @package RiseCheckout
 */

defined( 'ABSPATH' ) || exit;

/**
 * Define plugin constants.
 *
 * This function defines key constants like plugin path and version.
 *
 * @return void
 */
function risecheckout_define_constants() {
	risecheckout_define( 'RISECHECKOUT_ABSPATH', dirname( RISECHECKOUT_PLUGIN_FILE ) . '/' );
	risecheckout_define( 'RISECHECKOUT_VERSION', '1.0.0-dev' );
}
risecheckout_define_constants();

/**
 * Define a constant if not already defined.
 *
 * @param string $name  Constant name.
 * @param mixed  $value Constant value.
 * @return void
 */
function risecheckout_define( $name, $value ) {
	if ( ! defined( $name ) ) {
		define( $name, $value ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.VariableConstantNameFound
	}
}
