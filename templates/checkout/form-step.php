<div class="<?php echo esc_html( implode( ' ', $step->classes ) ); ?>" id="<?php echo esc_html( $step->id ); ?>">

	<?php
	/**
	 * Functions hooked in to risecheckout_step_content action
	 *
	 * @hooked risecheckout_step_title       - 10
	 * @hooked risecheckout_step_desc        - 20
	 * @hooked risecheckout_step_placeholder - 30
	 * @hooked risecheckout_step_fields      - 40
	 * @hooked risecheckout_step_button      - 50
	 */
	do_action( 'risecheckout_step_content', $step );
	?>

</div>
