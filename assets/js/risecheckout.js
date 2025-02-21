const stepCustomer = document.querySelector('#step-customer');
const next = document.createElement('button');
next.classList.add('btn', 'btn-primary');
next.setAttribute('type', 'button');
next.textContent = stepCustomer.dataset.continue;
stepCustomer.insertAdjacentElement('afterend', next);
document.querySelector('#step-delivery').setAttribute('disabled', '');
