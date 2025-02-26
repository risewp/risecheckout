<?php

function risecheckout_plugin_action_links( $links ) {
	if ( class_exists( 'WooCommerce' ) ) {
		$plugin_links   = array();
		$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=risecheckout' ) ) . '">' . __( 'Settings', 'risecheckout' ) . '</a>';

		$links = array_merge( $plugin_links, $links );
	}
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( RISECHECKOUT_PLUGIN_FILE ), 'risecheckout_plugin_action_links' );
