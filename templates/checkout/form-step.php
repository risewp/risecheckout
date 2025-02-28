<div class="<?php echo esc_html( implode( ' ', $step->classes ) ); ?>" id="<?php echo esc_html( $step->id ); ?>">

	<?php do_action( "risecheckout_before_step_{$step->key}", $step->key ); ?>

	<?php if ( $step->title ) : ?>

		<p class="h3"><?php echo esc_html( $step->title ); ?></p>

	<?php endif; ?>

	<?php if ( $step->desc ) : ?>

		<p class="desc"><?php echo esc_html( $step->desc ); ?></p>

	<?php endif; ?>

	<?php if ( $step->placeholder ) : ?>

		<p class="placeholder" style="display:none"><?php echo esc_html( $step->placeholder ); ?></p>

	<?php endif; ?>

	<?php do_action( "risecheckout_step_{$step->key}", $step->key ); ?>

	<?php if ( $step->fields ) : ?>

		<div class="step-fields">

			<?php
			foreach ( $step->fields as $key => $field ) {
				woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
			}
			?>

		</div>

	<?php endif; ?>

	<?php do_action( "risecheckout_after_step_{$step->key}", $step->key ); ?>

</div>
