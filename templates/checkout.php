<?php
defined( 'ABSPATH' ) || exit;

risecheckout_get_header( 'checkout' );
?>
<div class="row" style="--bs-gutter-x:2.5rem">
	<div class="col-md-7 col-lg-8 checkout-steps checkout-cols-3">
		<form method="GET" class="needs-validation" novalidate data-required="<?php echo esc_attr( __( 'Required field.', 'risecheckout' ) ); ?>">

			<?php
			$fields = risecheckout_fields();

			$steps = risecheckout_steps();

			foreach ( $steps as $slug => $step ) :
				$step = (object) $step;
				$step->slug = $slug;

				echo wp_kses_post( risecheckout_step_open( $step ) );
				?>

				<?php
				$step_fields = risecheckout_step_fields( $fields, $slug );

				if ( isset( $step_fields ) ) :
					?>

				<div class="fields row g-3">

					<?php
					foreach ( $step_fields as $id => $field ) {
						$field = (object) $field;
						$field->id = $id;

						risecheckout_field( $field );

					}
					?>

				</div>

				<?php endif; ?>

			</fieldset>

			<?php endforeach; ?>

			<input type="submit">
		</form>
	</div>
</div>
<?php
risecheckout_get_footer( 'checkout' );
