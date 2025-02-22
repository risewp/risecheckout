<?php
defined( 'ABSPATH' ) || exit;

risecheckout_get_header( 'checkout' );
?>
		<div class="row" style="--bs-gutter-x:2.5rem">
			<div class="col-md-7 col-lg-8 checkout-steps checkout-cols-3">
				<form class="needs-validation" novalidate data-required="<?php echo esc_attr( __( 'Required field.', 'risecheckout' ) ); ?>">

					<?php
					$customer_fields = array();
					if ( 'yes' === get_option( 'risecheckout_fullname', 'yes' ) ) {
						$customer_fields['fullname'] = array(
							'label'       => __( 'Full name', 'risecheckout' ),
							'placeholder' => sprintf(
								/* translators: %s: Example */
								__( 'e.g.: %s', 'risecheckout' ),
								__( 'Mary Anne Johnson', 'risecheckout' )
							),
							'minlength'   => 5,
							'pattern'     => '[A-Za-zÀ-ÖØ-öø-ÿ]{2,}(\s+[A-Za-zÀ-ÖØ-öø-ÿ]{2,})+',
							'invalid'     => sprintf(
								/* translators: %s: Field label */
								__( 'Enter your %s', 'risecheckout' ),
								mb_strtolower( __( 'Full name', 'risecheckout' ) )
							),
							'required'    => true,
							'value'       => __( 'Mary Anne Johnson', 'risecheckout' ),
						);
					} else {
						$customer_fields = array_merge(
							$customer_fields,
							array(
								'firstName' => array(
									'wrapper_class' => 'col-6',
									'label'         => __( 'First name', 'risecheckout' ),
									'placeholder'   => sprintf(
										/* translators: %s: Example */
										__( 'e.g.: %s', 'risecheckout' ),
										__( 'Mary', 'risecheckout' )
									),
									'minlength'     => 2,
									'pattern'       => '([A-Za-zÀ-ÖØ-öø-ÿ]{2,})+',
									'invalid'       => sprintf(
										/* translators: %s: Field label */
										__( 'Enter your %s', 'risecheckout' ),
										mb_strtolower( __( 'First name', 'risecheckout' ) )
									),
									'required'      => true,
									'value'         => __( 'Mary', 'risecheckout' ),
								),
								'lastName'  => array(
									'wrapper_class' => 'col-6',
									'label'         => __( 'Last name', 'risecheckout' ),
									'placeholder'   => sprintf(
										/* translators: %s: Example */
										__( 'e.g.: %s', 'risecheckout' ),
										__( 'Johnson', 'risecheckout' )
									),
									'minlength'     => 2,
									'pattern'       => '([A-Za-zÀ-ÖØ-öø-ÿ]{2,})+',
									'invalid'       => sprintf(
										/* translators: %s: Field label */
										__( 'Enter your %s', 'risecheckout' ),
										mb_strtolower( __( 'Last name', 'risecheckout' ) )
									),
									'required'      => true,
									'value'         => __( 'Johnson', 'risecheckout' ),
								),
							)
						);
					}
					$customer_fields = array_merge(
						$customer_fields,
						array(
							'email' => array(
								'label'       => __( 'Email', 'risecheckout' ),
								'type'        => 'email',
								'placeholder' => sprintf(
									/* translators: %s: Example */
									__( 'e.g.: %s', 'risecheckout' ),
									sprintf( '%s@gmail.com', sanitize_title( __( 'Mary', 'risecheckout' ) ) )
								),
								'pattern'     => '[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}',
								'invalid'     => sprintf(
									/* translators: %s: Field label */
									__( 'Invalid %s. Please check if you typed it correctly.', 'risecheckout' ),
									__( 'Email', 'risecheckout' )
								),
								'required'    => true,
								'value'       => sprintf(
									'%s@gmail.com',
									sanitize_title( __( 'Mary', 'risecheckout' ) )
								),
							),
							'mobile' => array(
								'label'       => __( 'Mobile', 'risecheckout' ),
								'prefix'      => '+55',
								'placeholder' => '(00) 000000-0000',
								'minlength'   => 11,
								'maxlength'   => 15,
								'pattern'     => '\(?\d{2}\)?\s?\d{4,5}-?\d{4}',
								// 'pattern'     => '[\(\d\)\s-]+',
								'mask'        => 'phone-br',
								'invalid'     => sprintf(
									/* translators: %s: Field label */
									__( 'Enter a valid %s', 'risecheckout' ),
									mb_strtolower( __( 'Mobile', 'risecheckout' ) )
								),
								'required'    => true,
								'value'       => '(47) 98804-3272',
							),
						)
					);
					$steps = array(
						'customer' => array(
							'title'       => __( 'Identify yourself', 'risecheckout' ),
							'description' => __(
								'We will use your email to: Identify your profile, purchase history, order ' .
								'notification and shopping cart.',
								'risecheckout'
							),
							'continue' => __( 'Continue', 'risecheckout' ),
							'edit'     => __( 'Edit', 'risecheckout' ),
							'fields'      => $customer_fields,
						),
						'delivery' => array(
							'title'       => __( 'Delivery', 'risecheckout' ),
							'description' => __(
								'Register or select an address',
								'risecheckout'
							),
							'placeholder' => __(
								'Fill in your personal information to continue',
								'risecheckout'
							),
							'continue' => __( 'Continue', 'risecheckout' ),
							'edit'     => __( 'Edit', 'risecheckout' ),
							'fields'      => array(
								'zip' => array(
									'wrapper_class' => 'col-7 zip',
									'label'       => __( 'Zip', 'risecheckout' ),
									'minlength'   => 8,
									'maxlength'   => 9,
									'pattern'     => '\d{5}-?\d{3}',
									'mask'        => 'zip-br',
									'invalid'     => sprintf(
										/* translators: %s: Field label */
										__( 'Enter a valid %s', 'risecheckout' ),
										mb_strtolower( __( 'Zip', 'risecheckout' ) )
									),
									'required'    => true,
								),
							),
						),
					);

					foreach ( $steps as $slug => $step ) :
						$step = (object) $step;

						?>

					<fieldset
						class="checkout-step card mb-4 card-body"
						id="<?php echo esc_attr( "step-{$slug}" ); ?>"
						data-continue="<?php echo esc_attr( $step->continue ); ?>"
						data-edit="<?php echo esc_attr( $step->edit ); ?>">

						<legend><?php echo esc_html( $step->title ); ?></legend>
						<p class="desc"><?php echo esc_html( $step->description ); ?></p>
						<div class="fields row g-3">

							<?php
							foreach ( $step->fields as $id => $field ) :
								$field = (object) $field;

								$wrapper_class = isset( $field->wrapper_class ) ? $field->wrapper_class : 'col-12';
								$type = isset( $field->type ) ? $field->type : 'text';
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
								if ( isset( $field->mask ) ) {
									$input->data_mask = $field->mask;
								}
								if ( isset( $field->required ) && $field->required ) {
									$input->required = true;
								}
								if ( isset( $field->value ) ) {
									$input->value = $field->value;
								}
								$input = (array) $input;
								foreach ( $input as $key => &$value ) {
									$key = preg_replace( '/^(data)_/', '$1-', $key );
									if ( in_array( $key, array( 'required' ), true ) ) {
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

								<input <?php echo implode( ' ', $input ) ?>>

								<?php if ( isset( $field->invalid ) ) : ?>

								<div class="invalid-feedback"><?php echo esc_html( $field->invalid ); ?></div>

								<?php endif; ?>

								<?php if ( isset( $field->prefix ) ) : ?>

								</div>

								<?php endif; ?>
							</div>

							<?php endforeach; ?>

						</div>
					</fieldset>

					<?php endforeach; ?>
				</form>
			</div>
		</div>
	</div>
</main>
<?php
risecheckout_get_footer( 'checkout' );
