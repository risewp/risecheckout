const forms = document.querySelectorAll("form");

forms.forEach((form) => {
  const fieldsets = form.querySelectorAll('fieldset');

  fieldsets.forEach((fieldset, index) => {
	if (index === 0) {
	  fieldset.classList.add('active');
	} else {
	  fieldset.setAttribute('disabled', '');
	}
  });
});

const stepCustomer = document.querySelector('#step-customer');
const next = document.createElement('button');
next.classList.add('btn', 'btn-primary', 'd-block', 'w-100', 'btn-pill', 'btn-send', 'mt-4');
next.setAttribute('type', 'submit');
next.textContent = stepCustomer.dataset.continue;

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
	const form = event.target.closest('form');
	if (!form.checkValidity()) {
		event.preventDefault();
		event.stopPropagation();
	} else {
		const stepCustomer = document.querySelector('#step-customer');
		stepCustomer.setAttribute('disabled', '');
		stepCustomer.classList.add('done');
		stepCustomer.classList.remove('active');
		const stepDelivery = document.querySelector('#step-delivery');
		stepDelivery.removeAttribute('disabled');
		stepDelivery.classList.add('active');
		history.pushState({}, '', '/checkout/delivery/');
	}
	form.classList.add('was-validated');
});
stepCustomer.appendChild(next);

const fullname = document.createElement('p');
fullname.classList.add('strong', 'fullname');
const email = document.createElement('p');
email.classList.add('email');
const info = document.createElement('div');
info.classList.add('info');
info.appendChild(fullname);
info.appendChild(email);
stepCustomer.appendChild(info);

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
	fullnameDisplay.textContent = `${firstName} ${lastName}`.trim();
}

function updateEmail() {
	emailDisplay.textContent = emailInput.value.trim();
}

if (fullnameInput) {
	updateFullname();
	fullnameInput.addEventListener('input', updateFullname);
}
if (firstNameInput && lastNameInput) {
	updateFullnameFromParts();
	firstNameInput.addEventListener('input', updateFullnameFromParts);
	lastNameInput.addEventListener('input', updateFullnameFromParts);
}
updateEmail();
emailInput.addEventListener('input', updateEmail);
