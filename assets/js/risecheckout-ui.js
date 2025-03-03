jQuery( function ( $ ) {
	const risecheckoutUi = {
		init: function () {
			this.bindEvents();
		},

		bindEvents: function () {
			$( window ).on( 'scroll', this.windowScrolledToggle );
		},

		windowScrolledToggle: function () {
			const $body = $( 'body' );
			const className = 'scrolled';
			if ( $( window ).scrollTop() > 30 ) {
				$body.addClass( className );
			} else {
				$body.removeClass( className );
			}
		},
	};

	risecheckoutUi.init();
} );
