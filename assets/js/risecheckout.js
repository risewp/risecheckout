class RisecheckoutBlockUI {
	constructor(selectorOrElements) {
		// Check if it's a jQuery object or NodeList
		if (typeof selectorOrElements === 'string') {
			this.elements = document.querySelectorAll(selectorOrElements);
		} else if (selectorOrElements instanceof NodeList || Array.isArray(selectorOrElements)) {
			this.elements = selectorOrElements;
		} else if (selectorOrElements instanceof Element) {
			this.elements = [selectorOrElements];
		} else {
			this.elements = [];
		}

		if (this.elements.length === 0) return;
		this.block();
	}

	block() {
		this.elements.forEach(element => {
			if (element.querySelector('.blockUI')) return; // Prevent duplicate overlays

			const overlay = document.createElement('div');
			overlay.classList.add('risecheckout-block-ui', 'blockUI', 'blockOverlay');

			// Default overlay styles
			const styles = {
				zIndex: "1000",
				border: "none",
				margin: "0px",
				padding: "0px",
				width: "100%",
				height: "100%",
				top: "0px",
				left: "0px",
				background: "rgb(255, 255, 255)",
				opacity: "0.6",
				cursor: "default",
				position: "absolute"
			};

			// Apply styles dynamically
			Object.assign(overlay.style, styles);

			if (window.getComputedStyle(element).position === "static") {
				element.style.position = "relative";
				element.style.zoom = 1;
			}

			element.appendChild(overlay);
		});
	}

	unblock() {
		this.elements.forEach(element => {
			const overlay = element.querySelector('.blockUI');
			if (overlay) {
				overlay.remove();
			}
		});
	}

	static getInstance(selectorOrElements) {
		return new RisecheckoutBlockUI(selectorOrElements);
	}
}

// Add to risecheckout namespace
const risecheckout = {
	BlockUI: RisecheckoutBlockUI
};

// jQuery Plugin Support
if (typeof jQuery !== 'undefined') {
	(function($) {
		if (!$.blockUI) {
			$.blockUI = {
				defaults: {
					overlayCSS: {}
				}
			};
		}

		$.fn.block = function(options) {
			// options.message and options.overlayCSS are ignored but received
			this.each(function() {
				new risecheckout.BlockUI(this);
			});
			return this;
		};

		$.fn.unblock = function() {
			// Use getInstance() to retrieve and unblock
			this.each(function() {
				risecheckout.BlockUI.getInstance(this).unblock();
			});
			return this;
		};
	})(jQuery);
}

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

const risecheckoutForm = {
	init: function () {
	}
};
document.addEventListener('DOMContentLoaded', () => risecheckoutForm.init());
