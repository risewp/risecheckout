<?php
/**
 * RiseCheckout Settings - This file contains all settings-related functions for RiseCheckout.
 *
 * @package RiseCheckout
 */

defined( 'ABSPATH' ) || exit;

/**
 * Retrieves the settings for RiseCheckout.
 *
 * @return array An array of settings for RiseCheckout.
 */
function risecheckout_get_settings() {
	return array(
		array(
			'title' => '',
			'type'  => 'title',
			'id'    => 'risecheckout_section_title',
		),
		array(
			'title'         => __( 'RiseCheckout', 'risecheckout' ),
			'desc'          => __( 'Multi-step', 'risecheckout' ),
			'id'            => 'risecheckout_multistep',
			'default'       => 'no',
			'type'          => 'checkbox',
			'checkboxgroup' => 'start',
			'autoload'      => false,
		),
		array(
			'title'         => __( 'Look a like', 'risecheckout' ),
			'desc'          => __( 'Look a like', 'risecheckout' ),
			'id'            => 'risecheckout_look_a_like',
			'default'       => 'no',
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'autoload'      => false,
		),
		array(
			'title'         => __( 'Two-column form', 'risecheckout' ),
			'desc'          => __( 'Two-column form (when desktop)', 'risecheckout' ),
			'desc_tip'      => __( 'Displays the checkout in three columns in total, two for the form and order review (when desktop).', 'risecheckout' ),
			'id'            => 'risecheckout_form_columns',
			'default'       => 'no',
			'type'          => 'checkbox',
			'checkboxgroup' => '',
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
			'title'    => __( 'Header text', 'risecheckout' ),
			'id'       => 'risecheckout_header_text',
			'default'  => '',
			'css'      => 'height:62px',
			'type'     => 'textarea',
			'autoload' => false,
		),
		array(
			'title'    => __( 'Order review text', 'risecheckout' ),
			'id'       => 'risecheckout_order_review_text',
			'default'  => '',
			'type'     => 'wysiwyg',
			'autoload' => false,
		),
		array(
			'title'    => __( 'Footer text', 'risecheckout' ),
			'id'       => 'risecheckout_footer_text',
			'default'  => '',
			'css'      => 'height:202px',
			'type'     => 'textarea',
			'autoload' => false,
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

/**
 * Adds a custom settings tab to the WooCommerce settings page.
 *
 * @param array $tabs The existing tabs on the settings page.
 * @return array Modified tabs array including the RiseCheckout tab.
 */
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

/**
 * Displays the settings page for RiseCheckout.
 */
function risecheckout_settings_page() {
	woocommerce_admin_fields( risecheckout_get_settings() );
}
add_action( 'woocommerce_settings_tabs_risecheckout', 'risecheckout_settings_page' );

/**
 * Saves the settings for RiseCheckout.
 */
function risecheckout_save_settings() {
	woocommerce_update_options( risecheckout_get_settings() );
}
add_action( 'woocommerce_update_options_risecheckout', 'risecheckout_save_settings' );

/**
 * Renders the WYSIWYG field on the settings page.
 *
 * @param array $value The field settings.
 */
function risecheckout_render_wysiwyg_field( $value ) {
	$option_value = get_option( $value['id'], $value['default'] );
	echo '<tr valign="top">';
	echo '<th scope="row"><label for="' . esc_attr( $value['id'] ) . '">' . esc_html( $value['title'] ) . '</label></th>';
	echo '<td>';
	echo "<div style='width:400px;" . esc_attr( $value['css'] ) . "'>";
	wp_editor(
		$option_value,
		esc_attr( $value['id'] ),
		array(
			'textarea_name' => esc_attr( $value['id'] ),
			'textarea_rows' => 5,
			'media_buttons' => true,
			'tinymce'       => true,
			'quicktags'     => true,
			'teeny'         => true,
		)
	);
	echo '</div>';
	if ( ! empty( $value['desc'] ) ) {
		echo '<p class="description">' . esc_html( $value['desc'] ) . '</p>';
	}
	echo '</td></tr>';
}
add_action( 'woocommerce_admin_field_wysiwyg', 'risecheckout_render_wysiwyg_field' );

/**
 * Sanitizes the WYSIWYG field data before saving.
 *
 * @param string $value The current field value.
 * @param array  $option The field option array.
 * @param string $raw_value The raw submitted value.
 * @return string Sanitized value.
 */
function risecheckout_sanitize_wysiwyg( $value, $option, $raw_value ) {
	if ( 'wysiwyg' === $option['type'] ) {
		$value = wp_kses_post( $raw_value );
	}
	return $value;
}
add_filter( 'woocommerce_admin_settings_sanitize_option', 'risecheckout_sanitize_wysiwyg', 10, 3 );

/**
 * Adds JavaScript to the admin footer to enable the save button when TinyMCE content changes.
 */
function risecheckout_admin_footer_script() {
	?>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			function enableSaveButton() {
				$('.woocommerce-save-button').removeAttr('disabled');
			}

			function addEventsToTinyMCE() {
				if (typeof tinymce !== "undefined") {
					tinymce.editors.forEach(function (editor) {
						editor.on('keyup change', enableSaveButton);
					});
				}
			}

			setTimeout(addEventsToTinyMCE, 1000);

			$('textarea').on('input', enableSaveButton);
		});
	</script>
	<?php
}
add_action( 'admin_footer', 'risecheckout_admin_footer_script' );

