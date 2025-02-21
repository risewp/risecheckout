(() => {
	'use strict';

	const forms = document.querySelectorAll('form');

	const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

	function addFeedback(field, message) {
		const feedback = document.createElement('div');
		feedback.classList.add('invalid-feedback');
		feedback.textContent = message;
		field.insertAdjacentElement('afterend', feedback);
	}

	function validateField(field, invalid = true) {
		if (!field.hasAttribute('disabled')) {
			let message = '';
			const form = field.closest('form');

			if (field.hasAttribute('required') && field.validity.valueMissing) {
				message = form?.dataset.required || '';
			} else if (field.validity.patternMismatch) {
				message = field.dataset.invalid || '';
			}

			const invalidFeedback = field.nextElementSibling;
			if (invalidFeedback) {
				invalidFeedback.textContent = message;
			}

			field.classList.remove('is-invalid');
			field.classList.remove('is-valid');
			if ('email' === field.type && emailRegex.test(field.value) || 'email' !== field.type && field.checkValidity()) {
				field.classList.add('is-valid');
			} else if (invalid) {
				field.classList.add('is-invalid');
			}

			if (!invalidFeedback) {
				addFeedback(field, message);
			}
		}
	}

	Array.from(forms).forEach(form => {
		form.addEventListener('submit', event => {
			if (!form.checkValidity()) {
				event.preventDefault();
				event.stopPropagation();
			}
			form.classList.add('was-validated');
		}, false);

		const fields = form.querySelectorAll('.form-control');
		Array.from(fields).forEach(field => {
			validateField(field, false);
			field.addEventListener('input', event => validateField(event.target, false));
			field.addEventListener('blur', event => validateField(event.target));
		});
	});
})();
