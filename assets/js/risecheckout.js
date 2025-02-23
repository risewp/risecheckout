const forms = document.querySelectorAll("form");

forms.forEach((form) => {
  const fieldsets = form.querySelectorAll('fieldset');

  fieldsets.forEach((fieldset, index) => {
	if (0 === index) {
	  fieldset.classList.add('active');
	} else {
	  fieldset.setAttribute('disabled', '');
	}
	const legend = fieldset.querySelector('legend');
	if (fieldset.dataset.placeholder) {
		let desc = document.createElement('p');
		desc.className = 'desc desc-disabled';
		desc.textContent = fieldset.dataset.placeholder;
		legend.after(desc);
	}
	const info = document.createElement('div');
	info.classList.add('info');
	fieldset.appendChild(info);
	const infos = fieldset.querySelectorAll('[data-info], [data-info-prefix]');
	infos.forEach((info) => {
		const infoDiv = info.closest('fieldset').querySelector('.info');
		if (info.dataset.info) {
			const infoId = info.dataset.info;
			if (!document.getElementById(`info-${infoId}`)) {
				const infoP = document.createElement('p');
				infoP.id = `info-${infoId}`;
				infoP.classList.add(infoId);
				let label = '';
				if (info.dataset.infoLabel) {
					label = info.dataset.infoLabel;
				} else {
					label = info.previousElementSibling.textContent.trim();
				}
				infoP.dataset.label = label;
				infoDiv.appendChild(infoP);
			}
		} else {
			const infoId = info.id;
			const infoP = document.createElement('p');
			infoP.id = `info-${infoId}`;
			infoP.classList.add(infoId);
			let label = '';
			if (info.dataset.infoLabel) {
				label = info.dataset.infoLabel;
			} else {
				label = info.previousElementSibling.textContent.trim();
			}
			infoP.dataset.label = label;
			infoDiv.appendChild(infoP);
		}

		let infoId = '';
		if (info.dataset.info) {
			infoId = info.dataset.info
			let infoValue = '';
			const form = info.closest('form');
			const group = form.querySelectorAll(`[data-info=${infoId}]`);
			infoValue = [];
			group.forEach((item) => {
				infoValue.push(item.value);
			});
			infoValue = infoValue.join(' ').trim();
			document.getElementById(`info-${infoId}`).textContent = infoValue;
		} else {
			infoValue = info.value;
			infoId = info.id;
			document.getElementById(`info-${infoId}`).textContent = infoValue;
		}
		info.addEventListener('input', function (event) {
			const field = event.target;
			let infoId = '';
			if (field.dataset.info) {
				infoId = info.dataset.info
				let infoValue = '';
				const form = field.closest('form');
				const group = form.querySelectorAll(`[data-info=${infoId}]`);
				infoValue = [];
				group.forEach((item) => {
					infoValue.push(item.value);
				});
				infoValue = infoValue.join(' ').trim();
				document.getElementById(`info-${infoId}`).textContent = infoValue;
			} else {
				infoValue = field.value;
				infoId = field.id;
				document.getElementById(`info-${infoId}`).textContent = infoValue;
			}
		});
		info.addEventListener('paste', function (event) {
			const field = event.target;
			let infoId = '';
			if (field.dataset.info) {
				infoId = info.dataset.info
				let infoValue = '';
				const form = field.closest('form');
				const group = form.querySelectorAll(`[data-info=${infoId}]`);
				infoValue = [];
				group.forEach((item) => {
					infoValue.push(item.value);
				});
				infoValue = infoValue.join(' ').trim();
				document.getElementById(`info-${infoId}`).textContent = infoValue;
			} else {
				infoValue = field.value;
				infoId = field.id;
				document.getElementById(`info-${infoId}`).textContent = infoValue;
			}
		});
	});

	if (fieldset.dataset.edit) {
		const edit = document.createElement('button');
		edit.setAttribute('type', 'submit');
		edit.classList.add('btn-edit');
		edit.setAttribute('data-action', 'edit');
		edit.setAttribute('aria-label', fieldset.dataset.edit);
		edit.setAttribute('data-bs-toggle', 'tooltip');
		edit.setAttribute('data-bs-title', fieldset.dataset.edit);
		new bootstrap.Tooltip(edit);
		legend.after(edit);
	}

	if (fieldset.dataset.continue) {

		const next = document.createElement('button');
		next.classList.add('btn', 'btn-primary', 'd-block', 'w-100', 'btn-pill', 'btn-send', 'mt-4');
		next.setAttribute('type', 'submit');
		next.textContent = fieldset.dataset.continue;

		const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
		svg.setAttribute('width', '17');
		svg.setAttribute('height', '13');
		svg.setAttribute('viewBox', '0 0 17 13');
		svg.setAttribute('fill', 'white');
		const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
		path.setAttribute('d', 'M10.4913 0.083736L8.9516 1.66506C8.84623 1.7729 8.84652 1.94512 8.95215 2.05271L11.5613 4.71372L0.277266 4.71372C0.124222 4.71372 -3.2782e-07 4.83794 -3.21005e-07 4.99098L-2.22234e-07 7.20921C-2.1542e-07 7.36225 0.124222 7.48648 0.277266 7.48648L11.5613 7.48648L8.95216 10.1475C8.84678 10.2551 8.84652 10.427 8.9516 10.5348L10.4913 12.1162C10.5435 12.1699 10.615 12.2002 10.6899 12.2002C10.7647 12.2002 10.8363 12.1697 10.8884 12.1162L16.5579 6.29335C16.6103 6.23958 16.6366 6.16968 16.6366 6.10008C16.6366 6.03022 16.6103 5.96062 16.5579 5.90655L10.8884 0.083736C10.8363 0.0302186 10.7647 4.91753e-07 10.6899 4.94966e-07C10.615 4.98178e-07 10.5435 0.0302186 10.4913 0.083736Z');
		svg.appendChild(path);
		next.appendChild(svg);

		next.addEventListener('click', (event) => {
			const next = event.target;
			const step = next.closest('fieldset');
			const form = step.closest('form');

			if (form.checkValidity()) {
				next.classList.add('sending');

				const fields = step.querySelectorAll('.form-control');
				fields.forEach(field => {
					field.setAttribute('disabled', '');
				});

				const stepCustomer = document.querySelector('#step-customer');
				stepCustomer.setAttribute('disabled', '');
				stepCustomer.classList.add('done');
				stepCustomer.classList.remove('active');
				form.classList.remove('was-validated');
				const stepDelivery = document.querySelector('#step-delivery');
				stepDelivery.removeAttribute('disabled');
				stepDelivery.classList.add('active');
				stepDelivery.querySelector('.form-control').focus();
			} else {
				form.classList.add('was-validated');
			}
		});
		fieldset.appendChild(next);
	}

	const overlySpinner = document.createElement('div');
	overlySpinner.className = 'overlay-spinner overlay-spinner-box';
	const spinner = document.createElement('div');
	spinner.className = 'spinner spinner-grey';
	overlySpinner.appendChild(spinner);
	fieldset.appendChild(overlySpinner);
  });
});
