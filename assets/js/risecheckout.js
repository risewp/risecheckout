/* global risecheckoutParams */
let risecheckoutForm = {
	init: function () {
		if (typeof risecheckoutParams === 'undefined') {
			return false;
		}

		this.params = risecheckoutParams;
		this.form = document.querySelector('form.checkout');

		if (!this.form) {
			return false;
		}

		this.fieldsets = this.form.querySelectorAll('fieldset');

		this.fieldsets.forEach((fieldset, index) => {
			this.setupFieldset(fieldset, index);
			this.setupInfoFields(fieldset);
			this.setupEditButton(fieldset);
			this.setupContinueButton(fieldset);
			this.appendOverlaySpinner(fieldset);
		});
	},
	setupFieldset: function (fieldset, index) {
		fieldset.classList.toggle('active', index === 0);
		if (index !== 0) fieldset.setAttribute('disabled', '');

		if (fieldset.dataset.placeholder) {
			const desc = this.createElement('p', 'desc desc-disabled', fieldset.dataset.placeholder);
			fieldset.querySelector('legend')?.after(desc);
		}

		fieldset.appendChild(this.createElement('div', 'info'));
	},
	setupInfoFields: function (fieldset) {
		fieldset.querySelectorAll('[data-info], [data-info-prefix]').forEach(info => {
			const infoDiv = fieldset.querySelector('.info');
			const infoId = info.dataset.info || info.id;

			if (!document.getElementById(`info-${infoId}`)) {
				const label = info.dataset.infoLabel || (info.previousElementSibling?.textContent.trim() || '');
				const content = info.hasAttribute('data-info-prefix') ? `${label} ` : '';
				const infoP = this.createElement('p', infoId, content, { id: `info-${infoId}`, 'data-label': label });

				if (info.hasAttribute('data-info-prefix')) {
					const infoSpan = this.createElement('span');
					infoP.appendChild(infoSpan);
				}

				infoDiv.appendChild(infoP);
			}

			this.updateInfoText(info);
			['input', 'paste'].forEach(event => info.addEventListener(event, () => this.updateInfoText(info)));
		});
	},
	updateInfoText: function (info) {
		const infoId = info.dataset.info || info.id;
		const form = info.closest('form');
		let infoValue;

		if (info.dataset.info) {
			infoValue = Array.from(form.querySelectorAll(`[data-info=${infoId}]`))
				.map(item => item.value).join(' ').trim();
		} else {
			infoValue = info.value;
		}

		if (info.hasAttribute('data-info-prefix')) {
			document.querySelector(`#info-${infoId} span`).textContent = infoValue;
		} else {
			document.getElementById(`info-${infoId}`).textContent = infoValue;
		}
	},
	setupEditButton: function (fieldset) {
		if (!fieldset.dataset.edit) return;
		const edit = this.createElement('button', 'btn-edit', '', {
			type: 'submit',
			'data-action': 'edit',
			'aria-label': fieldset.dataset.edit,
			'data-bs-toggle': 'tooltip',
			'data-bs-title': fieldset.dataset.edit
		});
		new bootstrap.Tooltip(edit);
		fieldset.querySelector('legend')?.after(edit);
	},
	setupContinueButton: function (fieldset) {
		if (!fieldset.dataset.continue) return;

		const stepHidden = this.createElement('input', '', '', { type: 'hidden', name: 'step', value: 'customer' });
		fieldset.appendChild(stepHidden);

		const next = this.createElement(
			'button',
			'btn btn-primary d-block w-100 btn-pill btn-send mt-4',
			fieldset.dataset.continue,
			{ type: 'submit' }
		);
		const svg = this.createSvgIcon();
		next.appendChild(svg);

		this.form.addEventListener('submit', event => this.handleNextClick(event));
		fieldset.appendChild(next);
	},
	cleanFormData: function (formData) {
		const cleanedFormData = new FormData();
		for (let [key, value] of formData.entries()) {
			const field = this.form.querySelector(`[name=${key}]`);
			let clean = field?.dataset.clean;

			if (clean) {
				switch (clean) {
					case 'numbers':
						value = value.replace(/\D/g, '');
						break;
				}
			}
			cleanedFormData.append(key, value);
		}

		return cleanedFormData;
	},
	formData: function () {
		return this.cleanFormData(new FormData(this.form));
	},
	handleNextClick: function (event) {
		event.preventDefault();

		// const next = event.target;
		// const step = next.closest('fieldset');
		// if (!form.checkValidity()) return form.classList.add('was-validated');

		// step.querySelectorAll('.form-control').forEach(field => field.setAttribute('disabled', ''));
		// next.classList.add('sending');

		fetch(this.params.wcAjaxUrl.replace('%%endpoint%%', 'risecheckout_customer'), {
			method: 'POST',
			body: this.formData(),
			headers: {
				'X-WPNONCE': this.params.customerNonce
			}
		})
		.then(response => response.text())
		.then(text => {
			if (!text) {
				text = '{"success":false}';
			}
			return JSON.parse(text);
		})
		.then(data => {
			console.log('Server response:', data);
		})
		.catch(error => {
			console.error('Request error:', error);
		});

		// toggleStep('#step-customer', false);
		// toggleStep('#step-delivery', true);
	},
	toggleStep: function (selector, isActive) {
		const step = document.querySelector(selector);
		step.classList.toggle('done', !isActive);
		step.classList.toggle('active', isActive);
		step.toggleAttribute('disabled', !isActive);
		if (isActive) step.querySelector('.form-control')?.focus();
	},
	appendOverlaySpinner: function (fieldset) {
		const overlay = this.createElement('div', 'overlay-spinner overlay-spinner-box');
		overlay.appendChild(this.createElement('div', 'spinner spinner-grey'));
		fieldset.appendChild(overlay);
	},
	createElement: function (tag, className, textContent = '', attributes = {}) {
		const el = document.createElement(tag);
		if (className) el.className = className;
		if (textContent) el.textContent = textContent;
		Object.entries(attributes).forEach(([key, value]) => el.setAttribute(key, value));
		return el;
	},
	createSvgIcon: function () {
		const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
		svg.setAttribute('width', '17');
		svg.setAttribute('height', '13');
		svg.setAttribute('viewBox', '0 0 17 13');
		svg.setAttribute('fill', 'white');

		const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
		path.setAttribute('d', 'M10.4913 0.083736L8.9516 1.66506C8.84623 1.7729 8.84652 1.94512 8.95215 2.05271L11.5613 4.71372L0.277266 4.71372C0.124222 4.71372 -3.2782e-07 4.83794 -3.21005e-07 4.99098L-2.22234e-07 7.20921C-2.1542e-07 7.36225 0.124222 7.48648 0.277266 7.48648L11.5613 7.48648L8.95216 10.1475C8.84678 10.2551 8.84652 10.427 8.9516 10.5348L10.4913 12.1162C10.5435 12.1699 10.615 12.2002 10.6899 12.2002C10.7647 12.2002 10.8363 12.1697 10.8884 12.1162L16.5579 6.29335C16.6103 6.23958 16.6366 6.16968 16.6366 6.10008C16.6366 6.03022 16.6103 5.96062 16.5579 5.90655L10.8884 0.083736C10.8363 0.0302186 10.7647 4.91753e-07 10.6899 4.94966e-07C10.615 4.98178e-07 10.5435 0.0302186 10.4913 0.083736Z');

		svg.appendChild(path);
		return svg;
	}
};

document.addEventListener('DOMContentLoaded', () => risecheckoutForm.init());
