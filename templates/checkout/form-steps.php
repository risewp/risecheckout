<div class="checkout-steps">

	<?php if ( $checkout->get_checkout_fields() ) : ?>

		<?php foreach ( array_keys( risecheckout_get_steps() ) as $index => $step_key ) : ?>

			<?php do_action( 'risecheckout_step', $step_key, $index ); ?>

		<?php endforeach; ?>

	<?php endif; ?>

</div>
