document.addEventListener( 'DOMContentLoaded', function () {
	if ( typeof wc_checkout_params === 'undefined' ) {
		return;
	}

	const wc_checkout_coupons = {
		init: function () {
			document.body.addEventListener( 'click', function ( event ) {
				if ( event.target.matches( 'a.showcoupon' ) ) {
					event.preventDefault();
					wc_checkout_coupons.show_coupon_form();
				}
				if ( event.target.matches( '.woocommerce-remove-coupon' ) ) {
					event.preventDefault();
					wc_checkout_coupons.remove_coupon( event );
				}
			} );

			document.body.addEventListener( 'input', function ( event ) {
				if ( event.target.matches( '#coupon_code' ) ) {
					wc_checkout_coupons.remove_coupon_error( event );
				}
			} );

			const couponForm = document.querySelector( 'form.checkout_coupon' );
			if ( couponForm ) {
				couponForm.style.display = 'none';
				couponForm.addEventListener(
					'submit',
					wc_checkout_coupons.submit
				);
			}
		},
		show_coupon_form: function () {
			const couponForm = document.querySelector( '.checkout_coupon' );
			if ( couponForm ) {
				couponForm.style.display =
					couponForm.style.display === 'none' ? 'block' : 'none';
				const input = couponForm.querySelector( 'input' );
				if ( input ) input.focus();
			}
		},
		show_coupon_error: function ( html, target ) {
			if ( ! target ) return;
			const msg = new DOMParser()
				.parseFromString( html, 'text/html' )
				.body.textContent.trim();
			if ( ! msg ) return;
			target.querySelector( '#coupon_code' ).classList.add( 'has-error' );
			target.insertAdjacentHTML(
				'beforeend',
				`<span class="coupon-error-notice" id="coupon-error-notice" role="alert">${ msg }</span>`
			);
		},
		remove_coupon_error: function ( event ) {
			const input = event.target;
			input.classList.remove( 'has-error' );
			const errorNotice = document.querySelector(
				'.coupon-error-notice'
			);
			if ( errorNotice ) errorNotice.remove();
		},
		submit: function ( event ) {
			event.preventDefault();
			const form = event.target;
			if ( form.classList.contains( 'processing' ) ) return;
			form.classList.add( 'processing' );

			const data = new FormData( form );
			data.append( 'security', wc_checkout_params.apply_coupon_nonce );
			data.append(
				'billing_email',
				document.querySelector( "input[name='billing_email']" ).value
			);

			fetch(
				wc_checkout_params.wc_ajax_url.replace(
					'%%endpoint%%',
					'apply_coupon'
				),
				{
					method: 'POST',
					body: data,
				}
			)
				.then( ( response ) => response.text() )
				.then( ( response ) => {
					document
						.querySelectorAll(
							'.woocommerce-error, .woocommerce-message, .is-error, .is-success'
						)
						.forEach( ( el ) => el.remove() );
					form.classList.remove( 'processing' );
					if (
						response.includes( 'woocommerce-error' ) ||
						response.includes( 'is-error' )
					) {
						wc_checkout_coupons.show_coupon_error(
							response,
							form.querySelector( '#coupon_code' ).parentElement
						);
					} else {
						form.style.display = 'none';
						form.insertAdjacentHTML( 'beforebegin', response );
					}
					document.body.dispatchEvent(
						new Event( 'update_checkout' )
					);
				} );
		},
		remove_coupon: function ( event ) {
			const button = event.target;
			const container = button.closest(
				'.woocommerce-checkout-review-order'
			);
			const coupon = button.dataset.coupon;
			if ( ! container ) return;
			container.classList.add( 'processing' );

			fetch(
				wc_checkout_params.wc_ajax_url.replace(
					'%%endpoint%%',
					'remove_coupon'
				),
				{
					method: 'POST',
					body: new URLSearchParams( {
						security: wc_checkout_params.remove_coupon_nonce,
						coupon: coupon,
					} ),
				}
			)
				.then( ( response ) => response.text() )
				.then( ( code ) => {
					document
						.querySelectorAll(
							'.woocommerce-error, .woocommerce-message, .is-error, .is-success'
						)
						.forEach( ( el ) => el.remove() );
					container.classList.remove( 'processing' );
					if ( code ) {
						document
							.querySelector( 'form.woocommerce-checkout' )
							.insertAdjacentHTML( 'beforebegin', code );
						document.body.dispatchEvent(
							new Event( 'update_checkout' )
						);
						document.querySelector(
							"form.checkout_coupon input[name='coupon_code']"
						).value = '';
						document.querySelector(
							'form.checkout_coupon'
						).style.display = 'none';
					}
				} );
		},
	};

	wc_checkout_coupons.init();
} );
