<?php

function risecheckout_add_settings_tab( $tabs ) {
	$has_checkout_label = false;
	foreach ( $tabs as $key => $label ) {
		if ( __( 'Checkout', 'risecheckout' ) === $label ) {
			$has_checkout_label = true;
			break;
		}
	}
	$tabs['risecheckout'] = $has_checkout_label ? __( 'RiseCheckout', 'risecheckout' ) : __( 'Checkout', 'risecheckout' );
	return $tabs;
}
add_filter( 'woocommerce_settings_tabs_array', 'risecheckout_add_settings_tab', 50 );

function risecheckout_settings_page() {
	woocommerce_admin_fields( risecheckout_get_settings() );
}
add_action( 'woocommerce_settings_tabs_risecheckout', 'risecheckout_settings_page' );

function risecheckout_save_settings() {
	woocommerce_update_options( risecheckout_get_settings() );
}
add_action( 'woocommerce_update_options_risecheckout', 'risecheckout_save_settings' );

function risecheckout_get_settings() {
	return array(
		array(
			'title' => '',
			'type'  => 'title',
			'id'    => 'risecheckout_section_title',
		),
		array(
			'title'         => __( 'RiseCheckout', 'risecheckout' ),
			'desc'          => __( 'Two-column form (when desktop)', 'risecheckout' ),
			'desc_tip'      => __( 'Displays the checkout in three columns in total, two for the form and order review (when desktop).', 'risecheckout' ),
			'id'            => 'risecheckout_form_columns',
			'default'       => 'no',
			'type'          => 'checkbox',
			'checkboxgroup' => 'start',
			'autoload'      => false,
		),
		array(
			'title'         => __( 'Fullname', 'risecheckout' ),
			'desc'          => __( 'Full name field', 'risecheckout' ),
			'desc_tip'      => __( 'Displays first and last name as a single field.', 'risecheckout' ),
			'id'            => 'risecheckout_fullname',
			'default'       => 'no',
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'autoload'      => false,
		),
		array(
			'title'         => __( 'Fonts', 'risecheckout' ),
			/* translators: %s: Google Fonts */
			'desc'          => sprintf( _x( 'Load %s', 'Google Fonts', 'risecheckout' ), 'Google Fonts' ),
			'desc_tip'      => __( 'Keep this option disabled to avoid loading external fonts, which improves performance.', 'risecheckout' ),
			'id'            => 'risecheckout_gfonts',
			'default'       => 'no',
			'type'          => 'checkbox',
			'checkboxgroup' => 'end',
			'autoload'      => false,
		),
		array(
			'title'    => __( 'Additional fields', 'risecheckout' ),
			'desc'     => __( 'Enable order notes', 'risecheckout' ),
			'id'       => 'woocommerce_enable_order_comments',
			'default'  => 'yes',
			'type'     => 'checkbox',
			'autoload' => false,
		),
		array(
			'type' => 'sectionend',
			'id'   => 'risecheckout_section_end',
		),
	);
}

function risecheckout_toggle_order_comments( $enabled ) {
	return get_option( 'woocommerce_enable_order_comments', 'yes' ) === 'yes';
}
add_filter( 'woocommerce_enable_order_notes_field', 'risecheckout_toggle_order_comments' );
