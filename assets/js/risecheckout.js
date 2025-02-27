const risecheckoutUi = {
	init() {
		this.bindEvents();
	},

	bindEvents() {
		window.addEventListener( 'scroll', this.windowScrolledToggle );
	},

	windowScrolledToggle() {
		const className = 'scrolled';
		if ( window.scrollY > 30 ) {
			document.body.classList.add( className );
		} else {
			document.body.classList.remove( className );
		}
	},
};
document.addEventListener( 'DOMContentLoaded', () => risecheckoutUi.init() );
