<?php
defined( 'ABSPATH' ) || exit;

risecheckout_get_header( 'checkout' );
?>

<nav class="navbar navbar-dark bg-dark" style="margin-bottom:1.5625rem;">
	<div class="container justify-content-center">
		<a class="navbar-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php
			if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
				$allowed_html = array(
					'img' => array(
						'src'      => array(),
						'alt'      => array(),
						'decoding' => array(),
					),
				);
				echo wp_kses( get_custom_logo(), $allowed_html );
			} else {
				echo esc_html( get_bloginfo( 'name' ) );
			}
			?>
		</a>
	</div>
</nav>

<main>
	<div class="container">
		<div class="row" style="--bs-gutter-x:2.5rem">
			<div class="col-md-5 col-lg-4 order-md-last" style="width:31.7807443%">
				<h4 class="d-flex justify-content-between align-items-center mb-3">
					<span class="text-primary">Your cart</span>
					<span class="badge bg-primary rounded-pill">3</span>
				</h4>
				<ul class="list-group mb-3">
					<li class="list-group-item d-flex justify-content-between lh-sm">
					<div>
						<h6 class="my-0">Product name</h6>
						<small class="text-body-secondary">Brief description</small>
					</div>
					<span class="text-body-secondary">$12</span>
					</li>
					<li class="list-group-item d-flex justify-content-between lh-sm">
					<div>
						<h6 class="my-0">Second product</h6>
						<small class="text-body-secondary">Brief description</small>
					</div>
					<span class="text-body-secondary">$8</span>
					</li>
					<li class="list-group-item d-flex justify-content-between lh-sm">
					<div>
						<h6 class="my-0">Third item</h6>
						<small class="text-body-secondary">Brief description</small>
					</div>
					<span class="text-body-secondary">$5</span>
					</li>
					<li class="list-group-item d-flex justify-content-between bg-body-tertiary">
					<div class="text-success">
						<h6 class="my-0">Promo code</h6>
						<small>EXAMPLECODE</small>
					</div>
					<span class="text-success">−$5</span>
					</li>
					<li class="list-group-item d-flex justify-content-between">
					<span>Total (USD)</span>
					<strong>$20</strong>
					</li>
				</ul>

				<form class="card p-2">
					<div class="input-group">
					<input type="text" class="form-control" placeholder="Promo code">
					<button type="submit" class="btn btn-secondary">Redeem</button>
					</div>
				</form>
			</div>
			<div class="col-md-7 col-lg-8 checkout-steps checkout-cols-3" style="width:68.2192557%">
				<form class="needs-validation" novalidate>
					<div class="maybe-row">
						<div class="maybe-col">
							<div class="card mb-4 active">
								<div class="card-body">
									<h4 class="card-title mb-3"><?php esc_html_e( 'Identify yourself', 'risecheckout' ); ?></h4>
									<p class="desc desc-form"><?php esc_html_e( 'We will use your email to: Identify your profile, purchase history, order notification and shopping cart.', 'risecheckout' ); ?></p>
									<div class="row g-3 row-form">

										<?php if ( 'yes' === get_option( 'risecheckout_fullname', 'no' ) ) : ?>

										<div class="col-12">
											<label for="fullname" class="form-label"><?php esc_html_e( 'Full name', 'risecheckout' ); ?></label>
											<div class="holder-input">
											<input type="text" class="form-control" id="fullname" placeholder="
											<?php
											/* translators: %s: Example */
											echo esc_attr( sprintf( __( 'e.g.: %s', 'risecheckout' ), __( 'Mary Anne Johnson', 'risecheckout' ) ) );
											?>
											" value="" required>
											<span class="spinner spinner-grey spinner-form"></span>
											</div>
											<div class="invalid-feedback" data-empty="<?php echo esc_attr( __( 'Required field.', 'risecheckout' ) ); ?>" data-invalid="
																								<?php
																									/* translators: %s: Field label */
																									echo esc_attr( sprintf( __( 'Enter your %s', 'risecheckout' ), __( 'Full name', 'risecheckout' ) ) );
																								?>
											">><?php esc_html_e( 'Required field.', 'risecheckout' ); ?></div>
										</div>

										<?php else : ?>

										<div class="col-6">
											<label for="firstName" class="form-label"><?php esc_html_e( 'First name', 'risecheckout' ); ?></label>
											<input type="text" class="form-control" id="firstName" placeholder="
											<?php
											/* translators: %s: Example */
											echo esc_attr( sprintf( __( 'e.g.: %s', 'risecheckout' ), __( 'Mary', 'risecheckout' ) ) );
											?>
											" value="" required>
											<div class="invalid-feedback"><?php esc_html_e( 'Required field.', 'risecheckout' ); ?></div>
										</div>

										<div class="col-6">
											<label for="lastName" class="form-label"><?php esc_html_e( 'Last name', 'risecheckout' ); ?></label>
											<input type="text" class="form-control" id="lastName" placeholder="
											<?php
											/* translators: %s: Example */
											echo esc_attr( sprintf( __( 'e.g.: %s', 'risecheckout' ), __( 'Johnson', 'risecheckout' ) ) );
											?>
											" value="" required>
											<div class="invalid-feedback"><?php esc_html_e( 'Required field.', 'risecheckout' ); ?></div>
										</div>

										<?php endif; ?>

										<div class="col-12">
											<label for="email" class="form-label"><?php esc_html_e( 'Email', 'risecheckout' ); ?></label>
											<div class="holder-input">
											<input type="email" class="form-control" id="email" placeholder="
											<?php
											/* translators: %s: Example */
											echo esc_attr( sprintf( __( 'e.g.: %s', 'risecheckout' ), sprintf( '%s@gmail.com', sanitize_title( __( 'Mary', 'risecheckout' ) ) ) ) );
											?>
											" required>
											<span class="spinner spinner-grey spinner-form"></span>
											</div>
											<div class="invalid-feedback" data-empty="<?php echo esc_attr( __( 'Required field.', 'risecheckout' ) ); ?>" data-invalid="
																								<?php
																									/* translators: %s: Field label */
																									echo esc_attr( sprintf( __( 'Invalid %s. Please check if you typed it correctly.', 'risecheckout' ), __( 'Email', 'risecheckout' ) ) );
																								?>
											">><?php esc_html_e( 'Required field.', 'risecheckout' ); ?></div>
										</div>

										<div class="col-12">
											<label for="mobile" class="form-label"><?php esc_html_e( 'Mobile', 'risecheckout' ); ?></label>
											<div class="input-group has-validation">
												<span class="input-group-text">+55</span>
												<input type="text" class="form-control" id="mobile" placeholder="(00) 00000-0000" minlength="15" maxlength="15" required>
												<div class="invalid-feedback" data-empty="<?php echo esc_attr( __( 'Required field.', 'risecheckout' ) ); ?>" data-invalid="
																									<?php
																										/* translators: %s: Field label */
																										echo esc_attr( sprintf( __( 'Enter a valid %s', 'risecheckout' ), mb_strtolower( __( 'Mobile', 'risecheckout' ) ) ) );
																									?>
												"><?php esc_html_e( 'Required field.', 'risecheckout' ); ?></div>
											</div>
										</div>

										<div class="col-12 gy-4">
											<button type="button" class="btn btn-primary d-block w-100 btn-pill btn-send">
												<?php esc_html_e( 'Continue', 'risecheckout' ); ?>
												<svg width="17" height="13" viewBox="0 0 17 13" fill="white" xmlns="http://www.w3.org/2000/svg">
													<path d="M10.4913 0.083736L8.9516 1.66506C8.84623 1.7729 8.84652 1.94512 8.95215 2.05271L11.5613 4.71372L0.277266 4.71372C0.124222 4.71372 -3.2782e-07 4.83794 -3.21005e-07 4.99098L-2.22234e-07 7.20921C-2.1542e-07 7.36225 0.124222 7.48648 0.277266 7.48648L11.5613 7.48648L8.95216 10.1475C8.84678 10.2551 8.84652 10.427 8.9516 10.5348L10.4913 12.1162C10.5435 12.1699 10.615 12.2002 10.6899 12.2002C10.7647 12.2002 10.8363 12.1697 10.8884 12.1162L16.5579 6.29335C16.6103 6.23958 16.6366 6.16968 16.6366 6.10008C16.6366 6.03022 16.6103 5.96062 16.5579 5.90655L10.8884 0.083736C10.8363 0.0302186 10.7647 4.91753e-07 10.6899 4.94966e-07C10.615 4.98178e-07 10.5435 0.0302186 10.4913 0.083736Z"/>
												</svg>
											</button>
										</div>
									</div>
									<div class="info">
										<p class="strong fullname" data-label="<?php echo esc_attr( __( 'Full name', 'risecheckout' ) ); ?>"><?php esc_html_e( 'Mary Anne Johnson', 'risecheckout' ); ?></p>
										<p class="email" data-label="<?php echo esc_attr( __( 'Email', 'risecheckout' ) ); ?>"><?php echo esc_html( sprintf( '%s@gmail.com', sanitize_title( __( 'Mary', 'risecheckout' ) ) ) ); ?></p>
									</div>
								</div>
								<div class="overlay-spinner overlay-spinner-box">
									<div class="spinner spinner-grey"></div>
								</div>
							</div>
							<div class="card mb-4 active">
								<div class="card-body">
									<h4 class="card-title mb-3"><?php esc_html_e( 'Delivery', 'risecheckout' ); ?></h4>
									<p class="desc desc-form"><?php esc_html_e( 'Fill in your personal information to continue', 'risecheckout' ); ?></p>
									<p class="desc desc-disabled"><?php esc_html_e( 'Register or select an address', 'risecheckout' ); ?></p>
									<div class="row row-form g-3">

										<div class="col-7" style="width:57%">
											<label for="zip" class="form-label"><?php esc_html_e( 'Zip', 'risecheckout' ); ?></label>
											<input type="text" class="form-control" id="zip" minlength="9" maxlength="9" required>
											<div class="invalid-feedback"><?php esc_html_e( 'Required field.', 'risecheckout' ); ?></div>
										</div>

									</div>

									<div class="row row-form row-form-address g-3">
										<div class="col-12">
											<label for="address" class="form-label">Address</label>
											<input type="text" class="form-control" id="address" placeholder="1234 Main St" required>
											<div class="invalid-feedback">
												Please enter your shipping address.
											</div>
										</div>

										<div class="col-12">
											<label for="address2" class="form-label">Address 2 <span class="text-body-secondary">(Optional)</span></label>
											<input type="text" class="form-control" id="address2" placeholder="Apartment or suite">
										</div>

										<div class="col-md-6">
											<label for="country" class="form-label">Country</label>
											<select class="form-select" id="country" required>
												<option value="">Choose...</option>
												<option>United States</option>
											</select>
											<div class="invalid-feedback">
												Please select a valid country.
											</div>
										</div>

										<div class="col-md-6">
											<label for="state" class="form-label">State</label>
											<select class="form-select" id="state" required>
											<option value="">Choose...</option>
											<option>California</option>
											</select>
											<div class="invalid-feedback">
											Please provide a valid state.
											</div>
										</div>

										<div class="col-12">
											<div class="form-check">
												<input type="checkbox" class="form-check-input" id="same-address" checked>
												<label class="form-check-label" for="same-address">Billing address is the same as my shipping address</label>
											</div>
										</div>
									</div>
								</div>
								<div class="overlay-spinner overlay-spinner-box">
									<div class="spinner spinner-grey"></div>
								</div>
							</div>
						</div>
						<div class="maybe-col">
							<div class="card mb-4 disabled">
								<div class="card-body">
									<h4 class="card-title mb-3"><?php esc_html_e( 'Payment', 'risecheckout' ); ?></h4>
									<p class="desc"><?php esc_html_e( 'Fill in your shipping information to continue', 'risecheckout' ); ?></p>

									<div class="my-3">
										<div class="form-check">
											<input id="credit" name="paymentMethod" type="radio" class="form-check-input" checked required>
											<label class="form-check-label" for="credit">Credit card</label>
										</div>
										<div class="form-check">
											<input id="debit" name="paymentMethod" type="radio" class="form-check-input" required>
											<label class="form-check-label" for="debit">Debit card</label>
										</div>
										<div class="form-check">
											<input id="paypal" name="paymentMethod" type="radio" class="form-check-input" required>
											<label class="form-check-label" for="paypal">PayPal</label>
										</div>
									</div>

									<div class="row gy-3">
										<div class="col-md-6">
											<label for="cc-name" class="form-label">Name on card</label>
											<input type="text" class="form-control" id="cc-name" placeholder="" required>
											<small class="text-body-secondary">Full name as displayed on card</small>
											<div class="invalid-feedback">
											Name on card is required
											</div>
										</div>

										<div class="col-md-6">
											<label for="cc-number" class="form-label">Credit card number</label>
											<input type="text" class="form-control" id="cc-number" placeholder="" required>
											<div class="invalid-feedback">
											Credit card number is required
											</div>
										</div>

										<div class="col-md-3">
											<label for="cc-expiration" class="form-label">Expiration</label>
											<input type="text" class="form-control" id="cc-expiration" placeholder="" required>
											<div class="invalid-feedback">
											Expiration date required
											</div>
										</div>

										<div class="col-md-3">
											<label for="cc-cvv" class="form-label">CVV</label>
											<input type="text" class="form-control" id="cc-cvv" placeholder="" required>
											<div class="invalid-feedback">
											Security code required
											</div>
										</div>
									</div>
								</div>
								<div class="overlay-spinner overlay-spinner-box">
									<div class="spinner spinner-grey"></div>
								</div>
							</div>
						</div>
					</div>

					<button class="w-100 btn btn-primary btn-lg" type="submit">Continue to checkout</button>
				</form>
			</div>
		</div>
	</div>
</main>
<?php
risecheckout_get_footer( 'checkout' );
