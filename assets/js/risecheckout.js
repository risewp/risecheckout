(() => {
	'use strict';

	const forms = document.querySelectorAll('.needs-validation');

	Array.from(forms).forEach(form => {
		form.addEventListener('submit', event => {
			if (!form.checkValidity()) {
				event.preventDefault();
				event.stopPropagation();
			}
			form.classList.add('was-validated');
		}, false);
	});

	document.addEventListener('DOMContentLoaded', () => {
		const fields = document.querySelectorAll('.needs-validation .form-control');
		
		fields.forEach(field => {
			field.addEventListener('input', () => {
				field.classList.remove('is-valid', 'is-invalid');
			});
			
			field.addEventListener('blur', () => {
				if (!field.checkValidity()) {
					field.classList.remove('is-valid');
					field.classList.add('is-invalid');
				} else {
					field.classList.remove('is-invalid');
					field.classList.add('is-valid');
				}
			});

			field.addEventListener('focus', () => {
				field.classList.remove('is-invalid');
			});
		});

		const phoneField = document.getElementById('mobile');
		if (phoneField) {
			const invalidFeedback = phoneField.nextElementSibling;
			
			phoneField.addEventListener('input', (e) => {
				let value = e.target.value.replace(/\D/g, ''); // Remove non-numeric characters
				if (value.length > 11) {
					value = value.slice(0, 11);
				}
				let formatted = '';
				if (value.length > 0) {
					formatted = '(' + value.substring(0, 1);
				}
				if (value.length >= 2) {
					formatted += value.substring(1, 2) + ')';
				}
				if (value.length >= 3) {
					formatted += ' ' + value.substring(2, value.length >= 11 ? 7 : 6);
				}
				if (value.length >= 7) {
					formatted += '-' + value.substring(value.length >= 11 ? 7 : 6, value.length);
				}
				e.target.value = formatted;
				phoneField.classList.remove('is-valid', 'is-invalid');
			});

			phoneField.addEventListener('keydown', (e) => {
				if (e.key === 'Backspace') {
					const selectionStart = phoneField.selectionStart;
					const selectionEnd = phoneField.selectionEnd;
					if (selectionStart !== selectionEnd) {
						phoneField.value = '';
						e.preventDefault();
						return;
					}
					
					let value = phoneField.value.replace(/\D/g, '');
					value = value.slice(0, -1); // Remove last digit
					let formatted = '';
					if (value.length > 0) {
						formatted = '(' + value.substring(0, 1);
					}
					if (value.length >= 2) {
						formatted += value.substring(1, 2) + ')';
					}
					if (value.length >= 3) {
						formatted += ' ' + value.substring(2, value.length >= 11 ? 7 : 6);
					}
					if (value.length >= 7) {
						formatted += '-' + value.substring(value.length >= 11 ? 7 : 6, value.length);
					}
					phoneField.value = formatted;
					e.preventDefault();
				}
			});

			phoneField.addEventListener('blur', () => {
				const value = phoneField.value.replace(/\D/g, '');
				if (value.length === 0) {
					phoneField.classList.add('is-invalid');
					if (invalidFeedback) {
						invalidFeedback.textContent = invalidFeedback.dataset.empty;
					}
				} else if (value.length < 10 || value.length > 11) {
					phoneField.classList.add('is-invalid');
					if (invalidFeedback) {
						invalidFeedback.textContent = invalidFeedback.dataset.invalid;
					}
				} else {
					phoneField.classList.remove('is-invalid');
					phoneField.classList.add('is-valid');
				}
			});

			phoneField.addEventListener('focus', () => {
				phoneField.classList.remove('is-invalid', 'is-valid');
			});
		}
	});
})();
