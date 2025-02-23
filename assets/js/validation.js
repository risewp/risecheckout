(() => {
	'use strict';

	const forms = document.querySelectorAll('form');

	function isValidCPF(cpf) {
		cpf = cpf.replace(/\D/g, '');
		if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
			return false;
		}

		let sum = 0, remainder;
		for (let i = 1; i <= 9; i++) {
			sum += parseInt(cpf[i - 1]) * (11 - i);
		}
		remainder = (sum * 10) % 11;
		if (remainder === 10 || remainder === 11) remainder = 0;
		if (remainder !== parseInt(cpf[9])) return false;

		sum = 0;
		for (let i = 1; i <= 10; i++) {
			sum += parseInt(cpf[i - 1]) * (12 - i);
		}
		remainder = (sum * 10) % 11;
		if (remainder === 10 || remainder === 11) remainder = 0;
		if (remainder !== parseInt(cpf[10])) return false;

		return true;
	}

	function isValidEmail(email) {
		return /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email);
	}

	function addFeedback(field, message) {
		let feedback = field.nextElementSibling;
		if (feedback && feedback.classList.contains('invalid-feedback')) {
			if (!field.dataset.invalid) {
				field.dataset.invalid = feedback.textContent;
			}
			feedback.textContent = message;
		}
	}

	function validateField(field, invalid = true) {
		if (!field.hasAttribute('disabled')) {
			let message = '';
			const form = field.closest('form');

			if (field.hasAttribute('required') && field.validity.valueMissing) {
				message = form?.dataset.required || '';
			} else if (('cpf' === field.dataset.validation && field.value && !isValidCPF(field.value)) || field.validity.patternMismatch) {
				message = field.dataset.invalid || '';
			}

			field.classList.remove('is-invalid');
			field.classList.remove('is-valid');
			if ((field.type === 'email' && isValidEmail(field.value)) || (field.type !== 'email' && field.checkValidity() && ('cpf' !== field.dataset.validation || isValidCPF(field.value)))) {
				field.classList.add('is-valid');
			} else if (invalid) {
				field.classList.add('is-invalid');
			}

			addFeedback(field, message);
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
