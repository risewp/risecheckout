jQuery( function ( $ ) {
	const risecheckoutForm = {
		$checkoutForm: $( 'form.checkout' ),
		init: function () {
			this.$checkoutForm.on( 'checkout_place_order', this.submit );
		},
		submit: function ( event ) {
			const $form = $( this );
			const $steps = $form.find('.checkout-steps > *');

			if ( $form.is( '.processing' ) ) {
				return false;
			}

			$form.addClass( 'processing' );
			return false;

			return true;
		},
	};

	risecheckoutForm.init();
} );
