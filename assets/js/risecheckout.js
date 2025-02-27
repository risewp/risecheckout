const risecheckoutUi = {
	init: function () {
		this.bindEvents();
	},

	bindEvents: function () {
		window.addEventListener('scroll', this.windowScrolledToggle);
	},

	windowScrolledToggle: function () {
		const className = 'scrolled';
		if (window.scrollY > 30) {
			document.body.classList.add(className);
		} else {
			document.body.classList.remove(className);
		}
	}
};
document.addEventListener('DOMContentLoaded', () => risecheckoutUi.init());
