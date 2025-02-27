document.addEventListener( 'DOMContentLoaded', function () {
	if ( typeof wc_checkout_params === 'undefined' ) {
		return;
	}

	var wc_checkout_login_form = {
		init: function () {
			document.body.addEventListener( 'click', function ( event ) {
				if ( event.target.matches( 'a.showlogin' ) ) {
					this.show_login_form;
					event.preventDefault();
				}
			} );
		},
		show_login_form: function () {
			document
				.querySelectorAll( 'form.login, form.woocommerce-form--login' )
				.forEach( function ( form ) {
					form.style.display =
						form.style.display === 'none' || ! form.style.display
							? 'block'
							: 'none';
				} );
		},
	};

	wc_checkout_login_form.init();
} );
