<?php
defined( 'ABSPATH' ) || exit;

risecheckout_get_header( 'checkout' );
?>

<nav class="navbar navbar-dark bg-dark mb-5">
	<div class="container justify-content-center">
		<a class="navbar-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php
			if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
				$allowed_html = array( 'img' => array( 'src' => array(), 'alt' => array(), 'decoding' => array(), ) );
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
							<div class="card mb-3 active">
								<div class="card-body">
									<h4 class="card-title mb-3"><?php esc_html_e( 'Identify yourself', 'risecheckout' ); ?></h4>
									<p><?php esc_html_e( 'We will use your email to: Identify your profile, purchase history, order notification and shopping cart.', 'risecheckout' ); ?></p>
									<div class="row g-3">

										<?php if ( 'yes' === get_option( 'risecheckout_fullname', 'yes' ) ) : ?>

										<div class="col-12">
											<label for="fullname" class="form-label"><?php esc_html_e( 'Full name', 'risecheckout' ); ?></label>
											<input type="text" class="form-control" id="fullname" placeholder="<?php
											/* translators: %s: Example */
											echo esc_attr( sprintf( __( 'e.g.: %s', 'risecheckout' ), __( 'Mary Anne Johnson', 'risecheckout' ) ) ); ?>" value="" required>
											<div class="invalid-feedback">
												<?php
												/* translators: %s: Field label */
												echo esc_html( sprintf( __( 'Valid %s is required.', 'risecheckout' ), mb_strtolower( __( 'Full name', 'risecheckout' ) ) ) );
												?>
											</div>
										</div>

										<?php else : ?>

										<div class="col-6">
											<label for="firstName" class="form-label"><?php esc_html_e( 'First name', 'risecheckout' ); ?></label>
											<input type="text" class="form-control" id="firstName" placeholder="<?php
											/* translators: %s: Example */
											echo esc_attr( sprintf( __( 'e.g.: %s', 'risecheckout' ), __( 'Mary', 'risecheckout' ) ) ); ?>" value="" required>
											<div class="invalid-feedback">
												<?php
												/* translators: %s: Field label */
												echo esc_html( sprintf( __( 'Valid %s is required.', 'risecheckout' ), mb_strtolower( __( 'First name', 'risecheckout' ) ) ) );
												?>
											</div>
										</div>

										<div class="col-6">
											<label for="lastName" class="form-label"><?php esc_html_e( 'Last name', 'risecheckout' ); ?></label>
											<input type="text" class="form-control" id="lastName" placeholder="<?php
											/* translators: %s: Example */
											echo esc_attr( sprintf( __( 'e.g.: %s', 'risecheckout' ), __( 'Johnson', 'risecheckout' ) ) ); ?>" value="" required>
											<div class="invalid-feedback">
												<?php
												/* translators: %s: Field label */
												echo esc_html( sprintf( __( 'Valid %s is required.', 'risecheckout' ), mb_strtolower( __( 'Last name', 'risecheckout' ) ) ) );
												?>
											</div>
										</div>

										<?php endif; ?>

										<div class="col-12">
											<label for="email" class="form-label"><?php esc_html_e( 'Email', 'risecheckout' ); ?></span></label>
											<input type="email" class="form-control" id="email" placeholder="<?php
											/* translators: %s: Example */
											echo esc_attr( sprintf( __( 'e.g.: %s', 'risecheckout' ), sprintf( '%s@gmail.com', sanitize_title( __( 'Mary', 'risecheckout' ) ) ) ) ); ?>" required>
											<div class="invalid-feedback">
												<?php
												/* translators: %s: Field label */
												echo esc_html( sprintf( __( 'Valid %s is required.', 'risecheckout' ), mb_strtolower( __( 'Email', 'risecheckout' ) ) ) );
												?>
											</div>
										</div>

										<div class="col-12">
											<label for="mobile" class="form-label"><?php esc_html_e( 'Mobile', 'risecheckout' ); ?></label>
											<div class="input-group has-validation">
												<span class="input-group-text">+55</span>
												<input type="text" class="form-control" id="mobile" placeholder="(00) 00000-0000" required>
												<div class="invalid-feedback">
													<?php
													/* translators: %s: Field label */
													echo esc_html( sprintf( __( 'Valid %s is required.', 'risecheckout' ), mb_strtolower( __( 'Mobile', 'risecheckout' ) ) ) );
													?>
												</div>
											</div>
										</div>

										<div class="d-grid gap-2">
											<button type="button" class="btn btn-primary"><?php esc_html_e( 'Continue', 'risecheckout' ); ?></button>
										</div>
									</div>
								</div>
							</div>
							<div class="card mb-3">
								<div class="card-body">
									<h4 class="card-title mb-3"><?php esc_html_e( 'Delivery', 'risecheckout' ); ?></h4>
									<p><?php esc_html_e( 'Fill in your personal information to continue', 'risecheckout' ); ?></p>
									<div class="row g-3">

										<div class="col-12">
											<label for="zip" class="form-label">Zip</label>
											<input type="text" class="form-control" id="zip" placeholder="" required>
											<div class="invalid-feedback">
												Zip code required.
											</div>
										</div>

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
							</div>
						</div>
						<div class="maybe-col">
							<div class="card mb-3">
								<div class="card-body">
									<h4 class="card-title mb-3"><?php esc_html_e( 'Payment', 'risecheckout' ); ?></h4>
									<p><?php esc_html_e( 'Fill in your shipping information to continue', 'risecheckout' ); ?></p>

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
