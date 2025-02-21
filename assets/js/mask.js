(() => {
	'use strict';

	const Mask = (selector, mask) => {
		let elements;
		if ( 'string' === typeof selector ) {
			elements = document.querySelectorAll(selector);
		} else {
			elements = [selector];
		}

		elements.forEach(el => {
			if ('phone-br' === mask) {
				const phoneField = el;
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

						let isMobile = false;
						let slicePoint = 6;

						if (value.length >= 3) {
							isMobile = 9 === parseInt(value[2]);
							slicePoint = isMobile || value.length > 10 ? 7 : 6;
							formatted += ' ' + value.substring(2, slicePoint);
						}

						if (value.length >= slicePoint) {
							formatted += '-' + value.substring(slicePoint, value.length);
						}

						e.target.value = formatted;
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
							let value = phoneField.value.replace(/\D/g, '').slice(0, -1);
							let formatted = '';
							if (value.length > 0) {
								formatted = '(' + value.substring(0, 1);
							}
							if (value.length >= 2) {
								formatted += value.substring(1, 2) + ')';
							}
							let isMobile = false;
							let slicePoint = 6;
							if (value.length >= 3) {
								isMobile = 9 === parseInt(value[2]);
								slicePoint = isMobile || value.length > 10 ? 7 : 6;
								formatted += ' ' + value.substring(2, slicePoint);
							}
							if (value.length >= slicePoint) {
								formatted += '-' + value.substring(slicePoint, value.length);
							}
							phoneField.value = formatted;
							e.preventDefault();
						}
					});
				}
			} else if ('zip-br' === mask) {
				const zipField = el;
				if (zipField) {
					const invalidFeedback = zipField.nextElementSibling;

					const formatZip = (value) => {
						value = value.replace(/\D/g, '');
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
				}
			}
		});
	};

	const fields = document.querySelectorAll('[data-mask]');
	fields.forEach(field => {
		const mask = field.dataset.mask;
		Mask(field, mask);
	});
})();
