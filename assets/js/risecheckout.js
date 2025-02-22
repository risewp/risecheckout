const stepCustomer = document.querySelector('#step-customer');
stepCustomer.classList.add('active');
const next = document.createElement('button');
next.classList.add('btn', 'btn-primary', 'd-block', 'w-100', 'btn-pill', 'btn-send', 'mt-4');
next.setAttribute('type', 'button');
next.textContent = stepCustomer.dataset.continue;
next.addEventListener('click', () => {
	const stepCustomer = document.querySelector('#step-customer');
	stepCustomer.setAttribute('disabled', '');
	stepCustomer.classList.add('done');
	stepCustomer.classList.remove('active');
	const stepDelivery = document.querySelector('#step-delivery');
	stepDelivery.removeAttribute('disabled');
	stepDelivery.classList.add('active');
    history.pushState({}, '', '/checkout/delivery/');
});
stepCustomer.appendChild(next);
document.querySelector('#step-delivery').setAttribute('disabled', '');

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
