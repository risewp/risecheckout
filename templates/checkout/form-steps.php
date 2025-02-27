<div class="checkout-steps">

	<?php
	if ( $checkout->get_checkout_fields() ) :
		$checkout_fields = [];
		foreach ( $checkout->get_checkout_fields() as $type => $fields ) {
			$checkout_fields = array_merge( $checkout_fields, $fields );
		}
		?>

		<?php foreach ( risecheckout_get_steps() as $id => $step ) : ?>

			<div class="<?php echo esc_html( str_replace( '_', '-', $id ) ); ?>" id="<?php echo esc_html( $id ); ?>">

				<?php if ( isset( $step->title ) && $step->title ) : ?>

					<p class="h3"><?php echo esc_html($step->title); ?></p>

				<?php endif; ?>

				<?php do_action( $id ); ?>

				<?php if ( isset( $step->fields ) && $step->fields ) : ?>

					<div class="step-fields">

						<?php
						foreach ( $step->fields as $key ) {
							if ( ! isset( $checkout_fields[ $key ] ) ) {
								continue;
							}
							woocommerce_form_field( $key, $checkout_fields[ $key ], $checkout->get_value( $key ) );
						}
						?>

					</div>

				<?php endif; ?>

			</div>

		<?php endforeach; ?>

	<?php endif; ?>

</div>
