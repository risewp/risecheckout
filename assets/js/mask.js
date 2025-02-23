const risecheckoutMask = {
	init: function () {
		const fields = document.querySelectorAll('[data-mask]');
		fields.forEach(field => {
			const mask = field.dataset.mask;
			this.mask(field, mask);
		});
	},
	format: function (value, mask) {
		const formated = value;
		switch (mask) {
			case 'phone-br':
				formated = this.formatPhoneBr(value);
				break;
			case 'zip-br':
				formated = this.formatZipBr(value);
				break;
			case 'cpf':
				formated = this.formatCPF(value);
				break;
		}
		return formated;
	},
	formatPhoneBr: function (value) {
		value = value.replace(/\D/g, '');
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
		return formatted;
	},
	formatZipBr: function (value) {
		value = value.replace(/\D/g, '');
		if (value.length > 8) {
			value = value.slice(0, 8);
		}
		let formatted = value;
		if (value.length > 5) {
			formatted = value.substring(0, 5) + '-' + value.substring(5);
		}
		return formatted;
	},
	formatCPF: function (value) {
		value = value.replace(/\D/g, '');
		if (value.length > 11) {
			value = value.slice(0, 11);
		}
		let formatted = value;
		if (value.length > 3) {
			formatted = value.substring(0, 3) + '.' + value.substring(3);
		}
		if (value.length > 6) {
			formatted = formatted.substring(0, 7) + '.' + formatted.substring(7);
		}
		if (value.length > 9) {
			formatted = formatted.substring(0, 11) + '-' + formatted.substring(11);
		}
		return formatted;
	},
	mask: function (selector, mask) {
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
					phoneField.addEventListener('input', (e) => {
						e.target.value = this.format(e.target.value);
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
							phoneField.value = this.format(phoneField.value.replace(/\D/g, '').slice(0, -1));
							e.preventDefault();
						}
					});
				}
			} else if ('zip-br' === mask) {
				const zipField = el;
				if (zipField) {
					zipField.addEventListener('input', (e) => {
						e.target.value = this.formatZipBr(e.target.value);
					});

					zipField.addEventListener('paste', (e) => {
						e.preventDefault();
						let pastedData = (e.clipboardData || window.clipboardData).getData('text');
						zipField.value = this.formatZipBr(pastedData);
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
			} else if ('cpf' === mask) {
				const cpfField = el;
				if (cpfField) {
					cpfField.addEventListener('input', (e) => {
						e.target.value = this.formatCPF(e.target.value);
					});

					cpfField.addEventListener('paste', (e) => {
						e.preventDefault();
						let pastedData = (e.clipboardData || window.clipboardData).getData('text');
						cpfField.value = this.formatCPF(pastedData);
					});

					cpfField.addEventListener('keydown', (e) => {
						if (e.key === 'Backspace' || e.key === 'Delete') {
							const selectionStart = cpfField.selectionStart;
							const selectionEnd = cpfField.selectionEnd;
							if (selectionStart !== selectionEnd) {
								cpfField.value = '';
								e.preventDefault();
								return;
							}
						}
					});
				}

			}
		});
	}
}

document.addEventListener('DOMContentLoaded', () => risecheckoutMask.init());
