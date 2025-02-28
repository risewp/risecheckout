<?php
/**
 * RiseCheckout Template Functions
 *
 * This file contains functions related to template loading and customization.
 *
 * @package RiseCheckout
 */

defined( 'ABSPATH' ) || exit;

/**
 * Loads the custom checkout template if applicable.
 *
 * @param string $template The path to the template file.
 * @return string Modified template path if conditions are met.
 */
function risecheckout_template_loader( $template ) {
	if ( risecheckout_is_checkout() && ! risecheckout_is_order_received_page() ) {
		$template = risecheckout_plugin_path() . '/templates/checkout.php';
	}
	return $template;
}
add_filter( 'template_include', 'risecheckout_template_loader' );

/**
 * Retrieves the WooCommerce template from the plugin if available.
 *
 * @param string $template The current template file.
 * @param string $template_name The template name.
 * @return string The modified template path if found.
 */
function risecheckout_wc_get_template( $template, $template_name ) {
	$plugin_template = risecheckout_plugin_path() . '/templates/' . $template_name;

	if ( file_exists( $plugin_template ) ) {
		$template = $plugin_template;
	}

	return $template;
}
add_filter( 'wc_get_template', 'risecheckout_wc_get_template', 10, 2 );

/**
 * Loads the header template.
 *
 * @param string|null $name Optional. Name of the specific header template.
 * @param array       $args Optional. Arguments to pass to the template.
 * @return bool False if template is not found.
 */
function risecheckout_get_header( $name = null, $args = array() ) {
	$templates = array();
	$name      = (string) $name;
	if ( '' !== $name ) {
		$templates[] = "header-{$name}.php";
	}

	$templates[] = 'header.php';

	if ( ! risecheckout_locate_template( $templates, true, true, $args ) ) {
		return false;
	}
}

/**
 * Loads the footer template.
 *
 * @param string|null $name Optional. Name of the specific footer template.
 * @param array       $args Optional. Arguments to pass to the template.
 * @return bool False if template is not found.
 */
function risecheckout_get_footer( $name = null, $args = array() ) {
	$templates = array();
	$name      = (string) $name;
	if ( '' !== $name ) {
		$templates[] = "footer-{$name}.php";
	}

	$templates[] = 'footer.php';

	if ( ! risecheckout_locate_template( $templates, true, true, $args ) ) {
		return false;
	}
}

/**
 * Locate template file.
 *
 * This function searches for a template file within the plugin, child theme, or parent theme directories.
 *
 * @param array|string $template_names Template file name(s).
 * @param bool         $load Whether to load the template file.
 * @param bool         $load_once Whether to load the template file once.
 * @param array        $args Optional arguments to pass to the template file.
 * @return string Located template file path.
 */
function risecheckout_locate_template( $template_names, $load = false, $load_once = true, $args = array() ) {
	global $wp_stylesheet_path, $wp_template_path;

	if ( ! isset( $wp_stylesheet_path ) || ! isset( $wp_template_path ) ) {
		wp_set_template_globals();
	}

	$templates_path = risecheckout_plugin_path() . '/templates';

	$is_child_theme = is_child_theme();

	$located = '';
	foreach ( (array) $template_names as $template_name ) {
		if ( ! $template_name ) {
			continue;
		}
		if ( file_exists( $templates_path . '/' . $template_name ) ) {
			$located = $templates_path . '/' . $template_name;
			break;
		} elseif ( file_exists( $wp_stylesheet_path . '/' . $template_name ) ) {
			$located = $wp_stylesheet_path . '/' . $template_name;
			break;
		} elseif ( $is_child_theme && file_exists( $wp_template_path . '/' . $template_name ) ) {
			$located = $wp_template_path . '/' . $template_name;
			break;
		} elseif ( file_exists( ABSPATH . WPINC . '/theme-compat/' . $template_name ) ) {
			$located = ABSPATH . WPINC . '/theme-compat/' . $template_name;
			break;
		}
	}

	if ( $load && '' !== $located ) {
		load_template( $located, $load_once, $args );
	}

	return $located;
}

/**
 * Outputs the site title or custom logo.
 *
 * Displays the custom logo if available, otherwise falls back to the site title.
 */
function risecheckout_site_title_or_logo() {
	ob_start();
	?>

	<a class="navbar-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php
		if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
			$allowed_html = array(
				'img' => array(
					'src'      => array(),
					'alt'      => array(),
					'decoding' => array(),
					'width'    => array(),
					'height'   => array(),
				),
			);
			$custom_logo  = str_replace( '>', ' width="125" height="54">', get_custom_logo() );
			echo wp_kses( $custom_logo, $allowed_html );
		} else {
			echo esc_html( get_bloginfo( 'name' ) );
		}
		?>
	</a>

	<?php
	echo wp_kses_post( ob_get_clean() );
}

/**
 * Customizes the WooCommerce form fields output.
 *
 * This function modifies the HTML output of WooCommerce form fields by filtering the default
 * field structure and allowing customizations via WordPress filters.
 *
 * @param string $field HTML output of the field.
 * @param string $key Field key.
 * @param array  $args Field arguments.
 * @param mixed  $value Field value.
 * @return string Modified HTML output.
 */
function risecheckout_wc_form_field( $field, $key, $args, $value ) {
	$defaults = array(
		'type'              => 'text',
		'label'             => '',
		'description'       => '',
		'placeholder'       => '',
		'maxlength'         => false,
		'minlength'         => false,
		'required'          => false,
		'autocomplete'      => false,
		'id'                => $key,
		'class'             => array(),
		'label_class'       => array(),
		'input_class'       => array(),
		'return'            => false,
		'options'           => array(),
		'custom_attributes' => array(),
		'validate'          => array(),
		'default'           => '',
		'autofocus'         => '',
		'priority'          => '',
		'unchecked_value'   => null,
		'checked_value'     => '1',
		'break'             => false,
	);

	$args = wp_parse_args( $args, $defaults );
	$args = apply_filters( 'woocommerce_form_field_args', $args, $key, $value ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

	if ( is_string( $args['class'] ) ) {
		$args['class'] = array( $args['class'] );
	}

	if ( $args['required'] ) {
		// hidden inputs are the only kind of inputs that don't need an `aria-required` attribute.
		// checkboxes apply the `custom_attributes` to the label - we need to apply the attribute on the input itself, instead.
		if ( ! in_array( $args['type'], array( 'hidden', 'checkbox' ), true ) ) {
			$args['custom_attributes']['aria-required'] = 'true';
		}

		$args['class'][] = 'validate-required';
		$required        = '';
	} else {
		// phpcs:disable WordPress.WP.I18n.TextDomainMismatch
		$required = ' <span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
	}

	if ( is_string( $args['label_class'] ) ) {
		$args['label_class'] = array( $args['label_class'] );
	}

	if ( is_null( $value ) ) {
		$value = $args['default'];
	}

	// Custom attribute handling.
	$custom_attributes         = array();
	$args['custom_attributes'] = array_filter( (array) $args['custom_attributes'], 'strlen' );

	if ( $args['maxlength'] ) {
		$args['custom_attributes']['maxlength'] = absint( $args['maxlength'] );
	}

	if ( $args['minlength'] ) {
		$args['custom_attributes']['minlength'] = absint( $args['minlength'] );
	}

	if ( ! empty( $args['autocomplete'] ) ) {
		$args['custom_attributes']['autocomplete'] = $args['autocomplete'];
	}

	if ( true === $args['autofocus'] ) {
		$args['custom_attributes']['autofocus'] = 'autofocus';
	}

	if ( $args['description'] ) {
		$args['custom_attributes']['aria-describedby'] = $args['id'] . '-description';
	}

	if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
		foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
		}
	}

	if ( ! empty( $args['validate'] ) ) {
		foreach ( $args['validate'] as $validate ) {
			$args['class'][] = 'validate-' . $validate;
		}
	}

	$field           = '';
	$label_id        = $args['id'];
	$sort            = $args['priority'] ? $args['priority'] : '';
	$field_container = '<p class="form-row %1$s" id="%2$s" data-priority="' . esc_attr( $sort ) . '">%3$s</p>';

	switch ( $args['type'] ) {
		case 'country':
			$countries = 'shipping_country' === $key ? WC()->countries->get_shipping_countries() : WC()->countries->get_allowed_countries();

			if ( 1 === count( $countries ) ) {
				$args['class'][] = 'unique-country';

				$field .= '<input type="text" value="' . current( array_values( $countries ) ) . '" readonly="readonly" />';

				$field .= '<input type="hidden" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="' . current( array_keys( $countries ) ) . '" ' . implode( ' ', $custom_attributes ) . ' class="country_to_state" readonly="readonly" />';
			} else {
				$data_label = ! empty( $args['label'] ) ? 'data-label="' . esc_attr( $args['label'] ) . '"' : '';

				$field = '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="country_to_state country_select ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ? $args['placeholder'] : esc_attr__( 'Select a country / region&hellip;', 'woocommerce' ) ) . '" ' . $data_label . '><option value="">' . esc_html__( 'Select a country / region&hellip;', 'woocommerce' ) . '</option>';

				foreach ( $countries as $ckey => $cvalue ) {
					$field .= '<option value="' . esc_attr( $ckey ) . '" ' . selected( $value, $ckey, false ) . '>' . esc_html( $cvalue ) . '</option>';
				}

				$field .= '</select>';

				$field .= '<noscript><button type="submit" name="woocommerce_checkout_update_totals" value="' . esc_attr__( 'Update country / region', 'woocommerce' ) . '">' . esc_html__( 'Update country / region', 'woocommerce' ) . '</button></noscript>';
			}

			break;
		case 'state':
			/* Get country this state field is representing */
			$for_country = isset( $args['country'] ) ? $args['country'] : WC()->checkout->get_value( 'billing_state' === $key ? 'billing_country' : 'shipping_country' );
			$states      = WC()->countries->get_states( $for_country );

			if ( is_array( $states ) && empty( $states ) ) {
				$field_container = '<p class="form-row %1$s" id="%2$s" style="display: none">%3$s</p>';

				$field .= '<input type="hidden" class="hidden" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="" ' . implode( ' ', $custom_attributes ) . ' placeholder="' . esc_attr( $args['placeholder'] ) . '" readonly="readonly" data-input-classes="' . esc_attr( implode( ' ', $args['input_class'] ) ) . '"/>';
			} elseif ( ! is_null( $for_country ) && is_array( $states ) ) {
				$data_label = ! empty( $args['label'] ) ? 'data-label="' . esc_attr( $args['label'] ) . '"' : '';

				$field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="state_select ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ? $args['placeholder'] : esc_html__( 'Select an option&hellip;', 'woocommerce' ) ) . '"  data-input-classes="' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . $data_label . '>
					<option value="">' . esc_html__( 'Select an option&hellip;', 'woocommerce' ) . '</option>';

				foreach ( $states as $ckey => $cvalue ) {
					$field .= '<option value="' . esc_attr( $ckey ) . '" ' . selected( $value, $ckey, false ) . '>' . esc_html( $cvalue ) . '</option>';
				}

				$field .= '</select>';
			} else {
				$field .= '<input type="text" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" value="' . esc_attr( $value ) . '"  placeholder="' . esc_attr( $args['placeholder'] ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" ' . implode( ' ', $custom_attributes ) . ' data-input-classes="' . esc_attr( implode( ' ', $args['input_class'] ) ) . '"/>';
			}

			break;
		case 'textarea':
			$field .= '<textarea name="' . esc_attr( $key ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" ' . ( empty( $args['custom_attributes']['rows'] ) ? ' rows="2"' : '' ) . ( empty( $args['custom_attributes']['cols'] ) ? ' cols="5"' : '' ) . implode( ' ', $custom_attributes ) . '>' . esc_textarea( $value ) . '</textarea>';

			break;
		case 'checkbox':
			$field = '<label class="checkbox ' . esc_attr( implode( ' ', $args['label_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . '>';

			// Output a hidden field so a value is POSTed if the box is not checked.
			if ( ! is_null( $args['unchecked_value'] ) ) {
				$field .= sprintf( '<input type="hidden" name="%1$s" value="%2$s" />', esc_attr( $key ), esc_attr( $args['unchecked_value'] ) );
			}

			$field .= sprintf(
				'<input type="checkbox" name="%1$s" id="%2$s" value="%3$s" class="%4$s" %5$s%6$s /> %7$s',
				esc_attr( $key ),
				esc_attr( $args['id'] ),
				esc_attr( $args['checked_value'] ),
				esc_attr( 'input-checkbox ' . implode( ' ', $args['input_class'] ) ),
				checked( $value, $args['checked_value'], false ),
				$args['required'] ? ' aria-required="true"' : '',
				wp_kses_post( $args['label'] )
			);

			$field .= $required . '</label>';

			break;
		case 'text':
		case 'password':
		case 'datetime':
		case 'datetime-local':
		case 'date':
		case 'month':
		case 'time':
		case 'week':
		case 'number':
		case 'email':
		case 'url':
		case 'tel':
			$field .= '<input type="' . esc_attr( $args['type'] ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '"  value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';

			break;
		case 'hidden':
			$field .= '<input type="' . esc_attr( $args['type'] ) . '" class="input-hidden ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';

			break;
		case 'select':
			$field   = '';
			$options = '';

			if ( ! empty( $args['options'] ) ) {
				foreach ( $args['options'] as $option_key => $option_text ) {
					if ( '' === $option_key ) {
						// If we have a blank option, select2 needs a placeholder.
						if ( empty( $args['placeholder'] ) ) {
							$args['placeholder'] = $option_text ? $option_text : __( 'Choose an option', 'woocommerce' );
							// phpcs:enable WordPress.WP.I18n.TextDomainMismatch
						}
						$custom_attributes[] = 'data-allow_clear="true"';
					}
					$options .= '<option value="' . esc_attr( $option_key ) . '" ' . selected( $value, $option_key, false ) . '>' . esc_html( $option_text ) . '</option>';
				}

				$field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="select ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ) . '">
						' . $options . '
					</select>';
			}

			break;
		case 'radio':
			$label_id .= '_' . current( array_keys( $args['options'] ) );

			if ( ! empty( $args['options'] ) ) {
				foreach ( $args['options'] as $option_key => $option_text ) {
					$field .= '<input type="radio" class="input-radio ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" value="' . esc_attr( $option_key ) . '" name="' . esc_attr( $key ) . '" ' . implode( ' ', $custom_attributes ) . ' id="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '"' . checked( $value, $option_key, false ) . ' />';
					$field .= '<label for="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '" class="radio ' . implode( ' ', $args['label_class'] ) . '">' . esc_html( $option_text ) . '</label>';
				}
			}

			break;
	}

	if ( ! empty( $field ) ) {
		$field_html = '';

		$break = '';
		if ( $args['break'] ) {
			$break = '<div class="break"></div>';
		}

		if ( $args['label'] && 'checkbox' !== $args['type'] ) {
			$field_html .= '<label for="' . esc_attr( $label_id ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) . '">' . wp_kses_post( $args['label'] ) . $required . '</label>';
		}

		$field_html .= $field;

		if ( $args['description'] ) {
			$field_html .= '<span class="description" id="' . esc_attr( $args['id'] ) . '-description" aria-hidden="true">' . wp_kses_post( $args['description'] ) . '</span>';
		}

		$container_class = esc_attr( implode( ' ', $args['class'] ) );
		$container_id    = esc_attr( $args['id'] ) . '_field';
		$field           = $break . sprintf( $field_container, $container_class, $container_id, $field_html );
	}

	return $field;
}
add_filter( 'woocommerce_form_field', 'risecheckout_wc_form_field', 10, 4 );

/**
 * Adds a custom class to the root element if the "look_a_like" option is enabled.
 *
 * This function checks if the custom option 'risecheckout_look_a_like' is enabled and adds
 * a CSS class to the body element accordingly.
 *
 * @param array $classes Array of body classes.
 * @return array Modified array of body classes.
 */
function risecheckout_root_class_look_a_like( $classes ) {
	$look_a_like = 'yes' === get_option( 'risecheckout_look_a_like', 'no' );
	if ( risecheckout_is_checkout() && ! risecheckout_is_order_received_page() && $look_a_like ) {
		$classes[] = 'look-a-like';
	}
	return $classes;
}
add_filter( 'risecheckout_root_class', 'risecheckout_root_class_look_a_like' );

/**
 * Replace emojis with Font Awesome icons in text.
 *
 * @param string $text The input text containing emojis.
 * @return string The text with emojis replaced by Font Awesome icons.
 */
function risecheckout_replace_emojis_with_font_awesome( $text ) {
	$replacements = array(
		'🏬'  => '<i class="fal fa-store"></i>',
		'📍'  => '<i class="fal fa-map-marker-alt"></i>',
		'✉️' => '<i class="fal fa-envelope"></i>',
		'📞'  => '<i class="fal fa-phone"></i>',
	);

	foreach ( $replacements as $emoji => $fa_icon ) {
		$text = str_replace( $emoji, $fa_icon, $text );
	}

	return $text;
}

/**
 * Function to automatically convert emails, URLs, and phone numbers into clickable links.
 *
 * This function uses WordPress' native make_clickable() to auto-link emails and URLs.
 * It then processes phone numbers by removing any characters that are not digits,
 * except for a leading '+' (for international country codes), and wraps them in a clickable tel: link.
 *
 * @param string $content The content to process.
 * @return string The processed content with clickable links.
 */
function risecheckout_make_clickable( $content ) {
	// First, use WordPress' built-in function to link emails and URLs.
	$content = make_clickable( $content );

	// Regex pattern to match phone numbers with various formatting (spaces, dashes, parentheses)
	// This pattern matches a phone number that starts optionally with '+' followed by digits and allowed characters.
	$pattern = '/(\+?[0-9\-\s\(\)]{7,}[0-9])/';

	// Process each matched phone number using a callback function.
	$content = preg_replace_callback(
		$pattern,
		function ( $matches ) {
			$phone = $matches[0];
			// Clean the phone number: remove all characters except digits,
			// while preserving a '+' if it's at the beginning.
			$cleaned_phone = preg_replace( '/(?!^\+)[^\d]/', '', $phone );
			// Return the original phone number wrapped in a clickable tel: link with the cleaned number.
			return '<a href="tel:' . $cleaned_phone . '">' . $phone . '</a>';
		},
		$content
	);

	return $content;
}

/**
 * Get and process the header text option.
 *
 * @return string The processed header text with clickable links and icons.
 */
function risecheckout_header_text() {
	$text = get_option( 'risecheckout_header_text' );
	if ( 'yes' === get_option( 'risecheckout_text_make_clickable', 'no' ) ) {
		$text = risecheckout_make_clickable( $text );
	}
	$text = risecheckout_replace_emojis_with_font_awesome( wpautop( $text ) );

	$text = preg_replace(
		'/<p>\s*(<i class="fal fa-[^"]+"><\/i>)\s*<a href="([^"]+)">([^<]+)<\/a>\s*<\/p>/',
		'<p><a href="$2">$1 $3</a></p>',
		$text
	);

	return $text;
}

/**
 * Get the toggler icon for the header.
 *
 * @return string The toggler icon HTML.
 */
function risecheckout_header_toggler_icon() {
	$icon = '<span class="navbar-toggler-icon"></span>';
	preg_match_all( '/<i\s+class="fal\s+fa-[^"]+"><\/i>/', risecheckout_header_text(), $icons );
	if ( ! empty( current( $icons ) ) ) {
		$icons = array_unique( current( $icons ) );
		$icon  = implode( ' ', $icons );
	}
	return $icon;
}

/**
 * Display the order review text on the WooCommerce checkout page.
 */
function risecheckout_order_review_text() {
	if ( ! risecheckout_option( 'multistep', 'no' ) || risecheckout_get_steps() ) {
		return;
	}
	echo wp_kses_post( '<div class="order-review-text">' . wpautop( get_option( 'risecheckout_order_review_text' ) ) . '</div>' );
}
add_action( 'woocommerce_checkout_order_review', 'risecheckout_order_review_text', 21 );

/**
 * Get and process the footer text option.
 *
 * @return string The processed footer text with emojis replaced by icons.
 */
function risecheckout_footer_text() {
	return risecheckout_replace_emojis_with_font_awesome( wpautop( get_option( 'risecheckout_footer_text' ) ) );
}

/**
 * Dequeue Select2 scripts and styles from WooCommerce.
 */
function risecheckout_wc_dequeue_select2() {
	wp_dequeue_script( 'selectWoo' );
	wp_dequeue_style( 'select2' );
}
add_action( 'wp_enqueue_scripts', 'risecheckout_wc_dequeue_select2', 11 );

/**
 * Output root element classes.
 *
 * @param array $classes Optional. Additional classes to add.
 */
function risecheckout_root_class( $classes = array() ) {
	$classes = apply_filters( 'risecheckout_root_class', $classes );
	echo esc_attr( 'class' ) . '="' . esc_attr( implode( ' ', $classes ) ) . '"';
}
