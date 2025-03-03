jQuery( function ( $ ) {
	const risecheckoutForm = {
		$checkoutForm: $( 'form.checkout' ),
		init: function () {},

		init: function () {
			if ( typeof risecheckoutParams === 'undefined' ) {
				return false;
			}

			this.params = risecheckoutParams;

			if ( ! this.$checkoutForm.length ) {
				return false;
			}

			this.$steps = this.$checkoutForm.find( '.checkout-steps > div' );

			var self = this;
			this.$steps.each( function () {
				self.setupStep( $( this ) );
				// self.setupEditButton($(this));
				// self.setupLoadingFields();
				// self.setupCityStateInfo();
			} );

			// this.setupContinueButton();$steps
			// this.appendOverlaySpinner();

			// ['input', 'paste'].forEach(function (type) {
			// 	self.form.find('[name=postcode]').on(type, self.updatePostcode);
			// });

			this.$checkoutForm.on( 'checkout_place_order', this.submit );
		},

		setupStep: function ( $step ) {
			if ( $step.is( '.active' ) ) {
				const currentStep = $step.attr( 'data-step' );
				risecheckoutForm.currentStep = currentStep;
				risecheckoutForm.$currentStep = $step;

				const $inputHidden = $(
					'<input type="hidden" name="risecheckout_step">'
				);
				$inputHidden.val( currentStep );
				risecheckoutForm.$checkoutForm
					.find( '.checkout-steps' )
					.prepend( $inputHidden );

				const $button = $step.find( '.button' );
				$button.prop( 'disabled', false );
				$button.show();
			} else if ( ! $step.is( '.done' ) ) {
				$step.addClass( 'disabled' );
			}
		},

		submit: function () {
			// event.preventDefault();
			const $form = $( this );
			const $steps = risecheckoutForm.$steps;
			const currentStep = risecheckoutForm.currentStep;

			if ( currentStep && currentStep !== 'payment' ) {
				if ( $form.is( '.processing' ) ) {
					return false;
				}

				$form.addClass( 'processing' );

				$steps.each( function () {
					const $step = $( this );
					if ( ! $step.is( '.active' ) ) {
						$step.find( 'input,select' ).each( function () {
							const $input = $( this );
							$input.prop( 'disabled', true );
							$input.addClass( 'input-disabled' );
						} );
					}
				} );

				$form
					.find( 'wc-order-attribution-inputs input' )
					.attr( 'disabled', '' )
					.addClass( 'input-disabled' );
				$form
					.find( '[name=_wp_http_referer]' )
					.attr( 'disabled', '' )
					.addClass( 'input-disabled' );

				// 	// return true;
				// 	// const step = this.currentStep();
				// 	// if (!this.form[0].checkValidity()) {
				// 	// 	return this.form.addClass('was-validated');
				// 	// }

				const data = $form.serialize();
				// const data = $form.serializeArray();
				// console.log(data);

				const $currentStep = risecheckoutForm.$currentStep;

				$currentStep.addClass( 'xhring' );

				$currentStep.block( {
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6,
					},
				} );

				$.ajax( {
					type: 'POST',
					url: risecheckoutParams.wcAjaxUrl.replace(
						'%%endpoint%%',
						`risecheckout_${ currentStep }`
					),
					data: $form.serialize(),
					dataType: 'json',
					success: function ( result ) {
						try {
							if ( 'success' === result.result ) {
								console.log( 'nextStep' );
								if ( result.fragments ) {
									$.each( result.fragments, function ( key ) {
										$( key )
											.addClass( 'updating' )
											.fadeTo( '400', '0.6' )
											.block( {
												message: null,
												overlayCSS: {
													opacity: 0.6,
												},
											} );
									} );

									$.each(
										result.fragments,
										function ( key, value ) {
											$( key ).replaceWith( value );
											$( key )
												.stop( true )
												.css( 'opacity', '1' )
												.unblock();
										}
									);
								}
							} else if ( 'failure' === result.result ) {
								throw 'Result failure';
							} else {
								throw 'Invalid response';
							}
						} catch ( err ) {
							console.log( result );

							if ( true === result.refresh ) {
								$( document.body ).trigger( 'update_checkout' );
							}

							risecheckoutForm.submitError();
						}
						// console.log(result);
						// if (!result || parseInt(result) === -1) {
						// 	result = '{"success":false}';
						// }
						// const response = JSON.parse(result);

						// if (response.success) {
						// 	console.log(result);
						// }
					},
				} );
				// $.ajax({
				// 	url: this.params.wcAjaxUrl.replace('%%endpoint%%', 'risecheckout_customer'),
				// 	method: 'POST',
				// 	data: body,
				// 	headers: {
				// 		'X-WPNONCE': this.params.customerNonce
				// 	},
				// 	success: function (result) {
				// 		if (!result || parseInt(result) === -1) {
				// 			result = '{"success":false}';
				// 		}
				// 		const response = JSON.parse(result);

				// 		if (response.success) {
				// 			this.responseInfo(response.data);
				// 			this.nextStep();
				// 		}
				// 	}.bind(this)
				// });

				return false;
			}

			return true;
		},

		submitError: function () {
			console.log( 'submitError' );
			risecheckoutForm.$checkoutForm.removeClass( 'processing' );
			const $currentStep = risecheckoutForm.$checkoutForm.find(
				'.checkout-steps > .active'
			);
			$currentStep.unblock();
			$currentStep.removeClass( 'xhring' );
		},
	};

	risecheckoutForm.init();
} );
