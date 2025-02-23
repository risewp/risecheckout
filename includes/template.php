<?php

defined( 'ABSPATH' ) || exit;

function risecheckout_template_loader( $template ) {
	if ( risecheckout_is_checkout() && ! risecheckout_is_order_received_page() ) {
		$template = risecheckout_plugin_path() . '/templates/checkout.php';
	}
	return $template;
}
add_filter( 'template_include', 'risecheckout_template_loader' );

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

function risecheckout_step_open( $step ) {
	$slug = $step->slug;

	$fieldset = (object) array(
		'class' => 'checkout-step card card-body',
		'id'    => "step-{$slug}",
	);
	if ( isset( $step->placeholder ) ) {
		$fieldset->data_placeholder = $step->placeholder;
	}
	if ( isset( $step->continue ) ) {
		$fieldset->data_continue = $step->continue;
	}
	if ( isset( $step->edit ) ) {
		$fieldset->data_edit = $step->edit;
	}
	$fieldset = (array) $fieldset;
	foreach ( $fieldset as $key => &$value ) {
		$key   = preg_replace( '/^(data)_/', '$1-', $key );
		$value = sprintf(
			'%s="%s"',
			esc_attr( $key ),
			esc_attr( $value )
		);
	}

	$html = sprintf( '<fieldset %s>', implode( ' ', array_values( $fieldset ) ) );

	ob_start();
	?>

	<legend><?php echo esc_html( $step->title ); ?></legend>

	<?php if ( isset( $step->description ) ) : ?>

	<p class="desc desc-form"><?php echo esc_html( $step->description ); ?></p>

	<?php endif; ?>

	<?php

	$html .= ob_get_clean();

	return $html;
}

function risecheckout_step_fields( $fields, $slug ) {
	$step_fields = array();
	foreach ( $fields as $id => $field ) {
		$field = (object) $field;

		if ( isset( $field->step ) && $slug === $field->step ) {
			$step_fields[ $id ] = (array) $field;
		}
	}
	return $step_fields;
}

function risecheckout_field( $field ) {
	$id = $field->id;

	$wrapper_class = isset( $field->wrapper_class ) ? $field->wrapper_class : 'col-12';
	$type          = isset( $field->type ) ? $field->type : 'text';
	?>

	<div class="<?php echo esc_attr( $wrapper_class ); ?>">
		<label for="<?php echo esc_attr( $id ); ?>" class="form-label">
			<?php echo esc_html( $field->label ); ?>
		</label>

		<?php
		$input = (object) array(
			'type'  => $type,
			'class' => 'form-control',
			'id'    => $id,
			'name'  => $id,
		);
		if ( isset( $field->placeholder ) ) {
			$input->placeholder = $field->placeholder;
		}
		if ( isset( $field->minlength ) ) {
			$input->minlength = $field->minlength;
		}
		if ( isset( $field->maxlength ) ) {
			$input->maxlength = $field->maxlength;
		}
		if ( isset( $field->pattern ) ) {
			$input->pattern = $field->pattern;
		}
		if ( isset( $field->validation ) ) {
			$input->data_validation = $field->validation;
		}
		if ( isset( $field->mask ) ) {
			$input->data_mask = $field->mask;
		}
		if ( isset( $field->clean ) ) {
			$input->data_clean = $field->clean;
		}
		if ( isset( $field->required ) && $field->required ) {
			$input->required = true;
		}
		if ( isset( $field->value ) ) {
			$input->value = $field->value;
		}
		if ( isset( $field->info ) ) {
			$input->data_info = $field->info;
		}
		if ( isset( $field->info_prefix ) ) {
			$input->data_info_prefix = $field->info_prefix;
		}
		if ( isset( $field->info_label ) ) {
			$input->data_info_label = $field->info_label;
		}
		$input = (array) $input;
		foreach ( $input as $key => &$value ) {
			if ( preg_match( '/^(data)_/', $key ) ) {
				$key = str_replace( '_', '-', $key );
			}
			if ( in_array( $key, array( 'required', 'data-info', 'data-info-prefix' ), true ) && true === $value ) {
				$value = esc_attr( $key );
			} else {
				$value = esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
			}
		}
		$input = array_values( $input );

		if ( isset( $field->prefix ) ) :
			?>

		<div class="input-group has-validation">
			<span class="input-group-text"><?php echo esc_html( $field->prefix ); ?></span>

		<?php endif; ?>

		<input <?php echo implode( ' ', $input ); ?>>

		<?php if ( isset( $field->invalid ) ) : ?>

		<div class="invalid-feedback"><?php echo esc_html( $field->invalid ); ?></div>

		<?php endif; ?>

		<?php if ( isset( $field->prefix ) ) : ?>

		</div>

		<?php endif; ?>
	</div>

	<?php
}
