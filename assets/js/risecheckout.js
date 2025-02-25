class RisecheckoutBlockUI {
    constructor(selector) {
        this.elements = document.querySelectorAll(selector);

        if (this.elements.length === 0) {
            return;
        }

        this.block();
    }

    block() {
        this.elements.forEach(element => {
            if (element.querySelector('.blockUI')) return; // Prevent duplicate overlays

            const overlay = document.createElement('div');
            overlay.classList.add('risecheckout-block-ui', 'blockUI', 'blockOverlay');

            // CSS styles as an object
            const styles = {
                zIndex: '1000',
                border: 'none',
                margin: '0px',
                padding: '0px',
                width: '100%',
                height: '100%',
                top: '0px',
                left: '0px',
                background: 'rgb(255, 255, 255)',
                opacity: '0.6',
                cursor: 'default',
                position: 'absolute'
            };

            // Apply styles dynamically
            Object.assign(overlay.style, styles);

            // Ensure the parent element has position: relative
            if (window.getComputedStyle(element).position === 'static') {
                element.style.position = 'relative';
            }

            element.appendChild(overlay);
        });
    }

    unblock() {
        this.elements.forEach(element => {
            const overlay = element.querySelector('.risecheckout-block-ui');
            if (overlay) {
                overlay.remove();
            }
        });
    }

    static getInstance(selector) {
        return new RisecheckoutBlockUI(selector);
    }
}

// Adding to risecheckout namespace
const risecheckout = {
    Block: RisecheckoutBlockUI
};

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
