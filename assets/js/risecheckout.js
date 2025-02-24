/* global risecheckoutParams */
const risecheckoutForm = {
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
			// this.setupInfoFields();
			this.setupEditButton(fieldset);
			this.setupLoadingFields();
			this.setupCityStateInfo();
		});

		this.setupContinueButton();
		this.appendOverlaySpinner();

		['input', 'paste'].forEach(type => {
			this.form.querySelector('[name=postcode]').addEventListener(type, this.updatePostcode);
		});
	},
	setupFieldset: function (fieldset, index) {
		fieldset.classList.toggle('active', index === 0);
		if (index !== 0) fieldset.setAttribute('disabled', '');

		if (fieldset.dataset.placeholder) {
			const desc = this.createElement('p', 'desc desc-disabled', fieldset.dataset.placeholder);
			fieldset.querySelector('legend')?.after(desc);
		}

		// fieldset.appendChild(this.createElement('div', 'info'));
	},
	setupInfoFields: function () {
		const step = this.currentStep();
		step.appendChild(this.createElement('div', 'infos'));
		step.querySelectorAll('[data-info], [data-info-prefix]').forEach(info => {
			const infoDiv = step.querySelector('.infos');
			const infoId = info.dataset.info || info.id;

			if (!document.getElementById(`info-${infoId}`)) {
				const label = info.dataset.infoLabel || (step.querySelector(`label[for=${info.id}`)	.textContent.trim() || '');
				const content = info.hasAttribute('data-info-prefix') ? `${label} ` : '';
				const infoP = this.createElement('p', infoId, content, { id: `info-${infoId}`, 'data-label': label });

				if (info.hasAttribute('data-info-prefix')) {
					const infoSpan = this.createElement('span');
					infoP.appendChild(infoSpan);
				}

				infoDiv.appendChild(infoP);
			}

			// this.updateInfoText(info);
			// ['input', 'paste'].forEach(event => info.addEventListener(event, () => this.updateInfoText(info)));
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
	setupContinueButton: function () {
		const step = this.currentStep();
		if (!step.dataset.continue) return;

		const next = this.createElement(
			'button',
			'btn btn-primary btn-send',
			(step.dataset.continue || step.dataset.save),
			{ type: 'submit' }
		);
		if (step.dataset.continue) {
			const svg = this.createSvgIcon();
			next.appendChild(svg);
		}

		this.form.addEventListener('submit', event => this.submit(event));
		step.appendChild(next);
	},
	setupSaveButton: function () {
		const step = this.currentStep();
		if (!step.dataset.save) return;

		const next = this.createElement(
			'button',
			'btn btn-primary btn-send',
			(step.dataset.continue || step.dataset.save),
			{ type: 'submit' }
		);

		this.form.addEventListener('submit', event => this.submit(event));
		step.appendChild(next);
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
			const maybePrefix = field.previousElementSibling;
			if (maybePrefix && maybePrefix.classList.contains('input-group-text')) {
				const prefix = maybePrefix.textContent.trim();
				value = prefix + value;
			}
			cleanedFormData.append(key, value);
		}

		return cleanedFormData;
	},
	formData: function () {
		return this.cleanFormData(new FormData(this.form));
	},
	currentStep: function () {
		return this.form.querySelector('fieldset:not(:disabled)');
	},
	submit: function (event) {
		event.preventDefault();

		const step = this.currentStep();
		if (!this.form.checkValidity()) return this.form.classList.add('was-validated');

		const body = this.formData();

		step.querySelectorAll('.form-control').forEach(field => field.setAttribute('disabled', ''));
		step.querySelector('.btn-send').classList.add('sending');

		fetch(this.params.wcAjaxUrl.replace('%%endpoint%%', 'risecheckout_customer'), {
			method: 'POST',
			body,
			headers: {
				'X-WPNONCE': this.params.customerNonce
			}
		})
		.then(response => response.text())
		.then(text => {
			if (!text || -1 === parseInt(text)) {
				text = '{"success":false}';
			}
			return JSON.parse(text);
		})
		.then(response => {

			if (response.success) {
				this.responseInfo(response.data);

				this.nextStep();

				// step.querySelector('.fields').remove();
			}
		});
		// .catch(error => {
		// 	console.error('Request error:', error);
		// });
	},
	responseInfo: function(info) {
		this.setupInfoFields();

		Object.keys(info).forEach(name => {
			let value = info[name];
			let field = this.form.querySelector(`[name=${name}]`);
			if (!field) {
				field = this.form.querySelector(`[data-info=${name}]`);
			}

			const mask = field?.dataset.mask;
			value = risecheckoutMask.format(value, mask);
			let infoPlace;
			if (field && field.hasAttribute('data-info-prefix')) {
				infoPlace = document.querySelector(`#info-${name} span`);
			} else {
				infoPlace = document.getElementById(`info-${name}`);
			}
			if (infoPlace) {
				infoPlace.textContent = value;
			}
		});
	},
	nextStep: function () {
		this.toggleStep('#step-customer', false);
		this.toggleStep('#step-shipping', true);

		this.form.classList.remove('was-validated');
	},
	fillAddress: function (postcode) {
		const step = this.currentStep();
		const field = this.form.querySelector('[name=postcode]');
		const wrapper = field.parentNode.parentNode;
		const invalidFeedback = wrapper.querySelector('.invalid-feedback');
		const previous = step.dataset.postcode;
		postcode = postcode.replace(/\D/g, '');
		if (postcode.length !== 8) {
			step.classList.remove('filled-address');
			field.focus();
		} else if (postcode !== previous) {
			field.classList.remove('is-valid');
			wrapper.classList.add('loading');
			const params = risecheckoutParams;
			fetch(params.wcAjaxUrl.replace('%%endpoint%%', 'risecheckout_postcode_br'), {
				method: 'POST',
				body: postcode,
				headers: {
					'Content-Type': 'text/plain',
					'X-WPNONCE': params.postcodeBrNonce
				}
			})
			.then(response => response.text())
			.then(text => {
				if (!text || -1 === parseInt(text)) {
					text = '{"success":false}';
				}
				return JSON.parse(text);
			})
			.then(response => {
				wrapper.classList.remove('loading');
				if (response.success) {
					field.classList.add('is-valid');
					step.classList.add('filled-address');
					step.dataset.postcode = postcode;
					this.setupSaveButton();

					const fields = step.querySelectorAll('.address-field .form-control:not([name=receiver])');

					let data = response.data;

					let same = true;
					Object.keys(data).forEach(name => {
						let value = data[name];
						if (name === 'street') {
							name = 'address1';
						}

						let place = this.form.querySelector(`[name=${name}]`);
						console.log(place);
						if (!place) {
							place = this.form.querySelector(`[data-info="${name}"]`);
							console.log(place);
							console.log(place.textContent.trim());
							console.log(place.innerHTML);
							console.log(place.className);
							console.log(place.id);
						}

						let filledValue;
						if (place.hasAttribute('data-info')) {
							filledValue = place.textContent.trim();
						} else {
							filledValue = place.value;
						}
						console.log('value: ' + value,'filledValue: ' + filledValue);
						if (value !== filledValue) {
							same = false;
						}
					});

					if (same) {
						step.dataset.postcode = postcode;
						previous = step.dataset.postcode;
					}

					fields.forEach(field => {
						field.classList.remove('is-invalid');
						field.classList.remove('is-valid');
					});

					if (postcode !== previous) {
						fields.forEach(field => {
							field.value = '';
						});
						data = {
							...data,
							neighborhood: data.neighborhood || '',
							street: data.street || ''
						};
						Object.keys(data).forEach(name => {
							let value = data[name];
							if (name === 'street') {
								name = 'address1';
							}
							let place = this.form.querySelector(`[name=${name}]`);
							if (!place) {
								place = this.form.querySelector(`[data-info=${name}]`);
							}

							if (place.hasAttribute('data-info')) {
								place.textContent = value;
							} else {
								place.value = value;
								if (value) {
									place.setAttribute('disabled', '');
									place.classList.add('is-valid');
								} else {
									place.removeAttribute('disabled');
								}
							}

							let focus = 'number';
							if (!data['street']) {
								focus = 'address1';
							}
							this.form.querySelector(`[name=${focus}]`).focus();

							// const mask = field?.dataset.mask;
							// value = risecheckoutMask.format(value, mask);
							// let infoPlace;
							// if (field && field.hasAttribute('data-info-prefix')) {
							// 	infoPlace = document.querySelector(`#info-${name} span`);
							// } else {
							// 	infoPlace = document.getElementById(`info-${name}`);
							// }
							// if (infoPlace) {
							// 	infoPlace.textContent = value;
							// }
						});
					}
				} else {
					field.dataset.invalid = response.data.message;
					invalidFeedback.textContent = field.dataset.invalid;
					let currentPattern = field.getAttribute('pattern');
					if (currentPattern) {
						const newPattern = `^(?!${field.value}$)${currentPattern}$`;
						field.setAttribute('pattern', newPattern);
					}
					field.classList.add('is-invalid');
				}
			});
			// if (response.success) {
			// 	// this.responseInfo(response.data);

			// 	// this.nextStep();

			// 	// step.querySelector('.fields').remove();
			// }
		} else {
			step.classList.add('filled-address');
		}
	},
	updatePostcode: function (event) {
		const field = this.form.querySelector('[name=postcode]');
		if (!field.hasAttribute('data-mask') || field.dataset.mask !== 'postcode-br') {
			return;
		}
		let postcode;
		if (event.target) {
			postcode = event.target.value;
		} else {
			postcode = (event.clipboardData || window.clipboardData).getData('text');
		}
		risecheckoutForm.fillAddress(postcode);

		// e.preventDefault();
		// let pastedData = (e.clipboardData || window.clipboardData).getData('text');
		// this.postcode.value = risecheckoutMask.formatPostcodeBr(pastedData);

		// console.log(this.postcode.value);

		// const postcode = this.postcode();
		// if (
		// 	this.postcode = this.form.querySelector('[data-mask=postcode-br]');)
		// console.log(this.form);
		// console.log(this.postcode);
		// const postcodeBr = this.postcode.value.replace(/\D/g, '');
		// if (postcodeBr.length === 8) {
		// 	console.log(postcodeBr);
		// }

		// console.log(event);
		// const numericValue = input.value.replace(/\D/g, ""); // Remove tudo que não for número

		// if (numericValue.length === 8) {
		// 	fetch("?wc-ajax=risecheckout_postcode_br", {
		// 		method: "POST",
		// 		headers: {
		// 			"Content-Type": "application/json",
		// 		},
		// 		body: JSON.stringify({ postcode: numericValue }),
		// 	})
		// 	.then(response => response.json())
		// 	.then(data => console.log("Resposta:", data))
		// 	.catch(error => console.error("Erro:", error));
		// }

		// fetch(this.params.wcAjaxUrl.replace('%%endpoint%%', 'risecheckout_postcode_br'), {
		// 	method: 'POST',
		// 	body,
		// 	headers: {
		// 		'X-WPNONCE': this.params.postcodeBrNonce
		// 	}
		// })
		// .then(response => response.text())
		// .then(text => {
		// 	if (!text || -1 === parseInt(text)) {
		// 		text = '{"success":false}';
		// 	}
		// 	return JSON.parse(text);
		// })
		// .then(response => {

		// 	if (response.success) {
		// 		console.log(response.data);
		// 	}
		// });

		// https://viacep.com.br/ws/89220120/json/ 200
		// {
		// 	"cep": "89220-120",
		// 	"logradouro": "Rua Araquã",
		// 	"complemento": "",
		// 	"unidade": "",
		// 	"bairro": "Costa e Silva",
		// 	"localidade": "Joinville",
		// 	"uf": "SC",
		// 	"estado": "Santa Catarina",
		// 	"regiao": "Sul",
		// 	"ibge": "4209102",
		// 	"gia": "",
		// 	"ddd": "47",
		// 	"siafi": "8179"
		//   }
		// https://viacep.com.br/ws/89220999/json/ 200
		// {
		// 	"erro": "true"
		//   }
		// https://seguro.mrmaverick.com.br/shipping/zipcode?zipcode=89237342 400
		// {"error":true,"message":"CEP inv\u00e1lido"}
		// https://seguro.mrmaverick.com.br/shipping/zipcode?zipcode=89237452 200
		// {
		// 	"zipcode": "89237452",
		// 	"street": "Rua Ant\u00f4nio Meras Sagas",
		// 	"neighborhood": "Vila Nova",
		// 	"city": "Joinville",
		// 	"uf": "SC",
		// 	"source": "database",
		// 	"city_id": 189
		//   }
		// https://viacep.com.br/ws/89240000/json/ 200
		// {
		// 	"cep": "89240-000",
		// 	"logradouro": "",
		// 	"complemento": "",
		// 	"unidade": "",
		// 	"bairro": "",
		// 	"localidade": "São Francisco do Sul",
		// 	"uf": "SC",
		// 	"estado": "Santa Catarina",
		// 	"regiao": "Sul",
		// 	"ibge": "4216206",
		// 	"gia": "",
		// 	"ddd": "47",
		// 	"siafi": "8319"
		//   }
		// https://seguro.mrmaverick.com.br/shipping/zipcode?zipcode=89240000 200
		// {
		// 	"zipcode": "89240000",
		// 	"street": "",
		// 	"neighborhood": "",
		// 	"city": "S\u00e3o Francisco do Sul",
		// 	"uf": "SC",
		// 	"source": "database",
		// 	"city_id": 1258
		//   }
	},
	checkMail: function () {
		// fetch(this.params.wcAjaxUrl.replace('%%endpoint%%', 'risecheckout_check_email'), {
		// 	method: 'POST',
		// })
		// REQUIRED 422
		// {
		// 	"message": "The given data was invalid.",
		// 	"errors": {
		// 	  "email": [
		// 		"Campo obrigat\u00f3rio."
		// 	  ]
		// 	}
		//   }
		// INVALID 422
		// {
		// 	"message": "The given data was invalid.",
		// 	"errors": {
		// 	  "email": [
		// 		"O campo email n\u00e3o cont\u00e9m um endere\u00e7o de email v\u00e1lido."
		// 	  ]
		// 	}
		// }
		// EXIST 200
		// {"has_email":false}
	},
	toggleStep: function (selector, isActive) {
		const step = document.querySelector(selector);
		const postcode = step.querySelector(`[name=postcode]`);
		step.classList.toggle('done', !isActive);
		step.classList.toggle('active', isActive);
		step.toggleAttribute('disabled', !isActive);
		if (isActive) {
			this.setupContinueButton();
			if (!step.dataset.save) {
				this.appendOverlaySpinner();
			}
			if (postcode) {
				this.fillAddress(postcode.value);
			}
			const firstField = step.querySelector('.form-control:empty');
			firstField?.classList.remove('is-invalid');
			firstField?.focus();
		} else {
			step.querySelector('.btn').remove();
		}
	},
	appendOverlaySpinner: function () {
		const step = this.currentStep();
		const overlay = this.createElement('div', 'overlay-spinner overlay-spinner-box');
		overlay.appendChild(this.createElement('div', 'spinner spinner-grey'));
		step.appendChild(overlay);
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
	},
	setupLoadingFields: function () {
		this.form.querySelectorAll('[data-loading]').forEach(field => {
			if (field.parentNode.classList.contains('holder-input')) {
				return;
			}

			const holderDiv = this.createElement('div', 'holder-input');
			const spinnerDiv = this.createElement('div', 'spinner spinner-grey spinner-form');

			const parent = field.parentNode;

			parent.insertBefore(holderDiv, field);

			holderDiv.appendChild(field);

			const invalidFeedback = parent.querySelector('.invalid-feedback');
			if (invalidFeedback) {
				holderDiv.appendChild(invalidFeedback);
			}

			holderDiv.appendChild(spinnerDiv);
		});
	},
	setupCityStateInfo: function () {
		if (this.form.querySelector('#cityStateInfo')) {
			return;
		}

		const fields = this.form.querySelectorAll('[name=city], [name=state]');

		let classList = fields[0].parentNode.classList;
		classList.add('city-state-info');
		classList = Array.from(classList);

		const cityStateInfoWrapper = this.createElement('div', classList.join(' ').replace('col-8', 'col-5'));

		const cityStateInfoDiv = this.createElement('div', 'city-state-info-text', '', { id: 'cityStateInfo' });

		fields.forEach((field, index) => {
			if (index > 0) {
				cityStateInfoDiv.appendChild(document.createTextNode(' / '));
			}
			const span = this.createElement('span', field.id, field.value, { 'data-info': field.id });
			cityStateInfoDiv.appendChild(span);
		});

		cityStateInfoWrapper.appendChild(cityStateInfoDiv);

		if (fields[0]) {
			const parent = fields[0].parentNode;

			if (parent && parent.parentNode) {
				parent.parentNode.insertBefore(cityStateInfoWrapper, parent);
			}
		}

		const cityStateInfoParts = this.form.querySelectorAll('#cityStateInfo span');
		cityStateInfoParts.forEach(part => {
			const id = part.classList[0];
			document.getElementById(id).parentNode.remove();
			part.classList.remove(id);
			part.removeAttribute('class');
			part.id = id;
		});
	}
};

document.addEventListener('DOMContentLoaded', () => risecheckoutForm.init());
