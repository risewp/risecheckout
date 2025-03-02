<div class="checkout-steps">

	<?php if ( $checkout->get_checkout_fields() ) : ?>

		<?php foreach ( array_keys( risecheckout_get_steps() ) as $step_key ) : ?>

			<?php do_action( 'risecheckout_step', $step_key ); ?>

		<?php endforeach; ?>

	<?php endif; ?>

</div>
