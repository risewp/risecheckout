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

		const fullnameField = document.getElementById('fullname');
		if (fullnameField) {
			const invalidFeedback = fullnameField.nextElementSibling;

			fullnameField.addEventListener('blur', () => {
				const names = fullnameField.value.trim().split(' ');
				if (fullnameField.value.trim() === '' || names.length < 2) {
					fullnameField.classList.remove('is-valid');
					fullnameField.classList.add('is-invalid');
					if (invalidFeedback) {
						invalidFeedback.textContent = invalidFeedback.dataset.invalid;
					}
				} else {
					fullnameField.classList.remove('is-invalid');
					fullnameField.classList.add('is-valid');
				}
			});

			fullnameField.addEventListener('focus', () => {
				fullnameField.classList.remove('is-invalid', 'is-valid');
			});
		}

		const emailField = document.getElementById('email');
		if (emailField) {
			const invalidFeedback = emailField.nextElementSibling;

			emailField.addEventListener('blur', () => {
				if (emailField.value.trim() === '') {
					emailField.classList.remove('is-valid');
					emailField.classList.add('is-invalid');
					if (invalidFeedback) {
						invalidFeedback.textContent = invalidFeedback.dataset.empty;
					}
				} else if (!emailField.checkValidity()) {
					emailField.classList.remove('is-valid');
					emailField.classList.add('is-invalid');
					if (invalidFeedback) {
						invalidFeedback.textContent = invalidFeedback.dataset.invalid;
					}
				} else {
					emailField.classList.remove('is-invalid');
					emailField.classList.add('is-valid');
				}
			});

			emailField.addEventListener('focus', () => {
				emailField.classList.remove('is-invalid', 'is-valid');
			});
		}

		const phoneField = document.getElementById('mobile');
		if (phoneField) {
			const invalidFeedback = phoneField.nextElementSibling;

			phoneField.addEventListener('input', (e) => {
				let value = e.target.value.replace(/\D/g, '');
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
					phoneField.classList.remove('is-valid');
					phoneField.classList.add('is-invalid');
					if (invalidFeedback) {
						invalidFeedback.textContent = invalidFeedback.dataset.empty;
					}
				} else if (value.length < 10 || value.length > 11) {
					phoneField.classList.remove('is-valid');
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

		const fullnameInput = document.getElementById('fullname');
		const firstNameInput = document.getElementById('firstName');
		const lastNameInput = document.getElementById('lastName');
		const emailInput = document.getElementById('email');

		const fullnameDisplay = document.querySelector('.info .fullname');
		const emailDisplay = document.querySelector('.info .email');

		function updateFullname() {
			fullnameDisplay.textContent = fullnameInput.value.trim();
		}

		function updateFullnameFromParts() {
			const firstName = firstNameInput.value.trim();
			const lastName = lastNameInput.value.trim();
			fullnameInput.value = `${firstName} ${lastName}`.trim();
			updateFullname();
		}

		function updateEmail() {
			emailDisplay.textContent = emailInput.value.trim();
		}

		if (fullnameInput) {
			fullnameInput.addEventListener('input', updateFullname);
		}
		if (firstNameInput && lastNameInput) {
			firstNameInput.addEventListener('input', updateFullnameFromParts);
			lastNameInput.addEventListener('input', updateFullnameFromParts);
		}
		emailInput.addEventListener('input', updateEmail);

		const zipField = document.getElementById('zip');
		if (zipField) {
			const invalidFeedback = zipField.nextElementSibling;

			const formatZip = (value) => {
				value = value.replace(/\D/g, ''); // Remove não numéricos
				if (value.length > 8) {
					value = value.slice(0, 8);
				}
				if (value.length > 5) {
					return value.substring(0, 5) + '-' + value.substring(5);
				}
				return value;
			};

			zipField.addEventListener('input', (e) => {
				e.target.value = formatZip(e.target.value);
				zipField.classList.remove('is-valid', 'is-invalid');
			});

			zipField.addEventListener('paste', (e) => {
				e.preventDefault();
				let pastedData = (e.clipboardData || window.clipboardData).getData('text');
				zipField.value = formatZip(pastedData);
			});

			zipField.addEventListener('keydown', (e) => {
				if (e.key === 'Backspace' || e.key === 'Delete') {
					const selectionStart = zipField.selectionStart;
					const selectionEnd = zipField.selectionEnd;
					if (selectionStart !== selectionEnd) {
						zipField.value = '';
						e.preventDefault();
						return;
					}
				}
			});

			zipField.addEventListener('blur', () => {
				const value = zipField.value.replace(/\D/g, '');
				if (value.length === 0) {
					zipField.classList.remove('is-valid');
					zipField.classList.add('is-invalid');
					if (invalidFeedback) {
						invalidFeedback.textContent = invalidFeedback.dataset.empty;
					}
				} else if (value.length !== 8) {
					zipField.classList.remove('is-valid');
					zipField.classList.add('is-invalid');
					if (invalidFeedback) {
						invalidFeedback.textContent = invalidFeedback.dataset.invalid;
					}
				} else {
					zipField.classList.remove('is-invalid');
					zipField.classList.add('is-valid');
				}
			});

			zipField.addEventListener('focus', () => {
				zipField.classList.remove('is-invalid', 'is-valid');
			});
		}
	});
})();
