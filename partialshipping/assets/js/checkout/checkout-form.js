if (typeof wc_checkout_params === 'undefined') {
    return false;
}

const wc_checkout_form = {
    updateTimer: false,
    dirtyInput: false,
    selectedPaymentMethod: false,
    xhr: false,
    $order_review: document.getElementById('order_review'),
    $checkout_form: document.querySelector('form.checkout'),
    init: function() {
        document.body.addEventListener('update_checkout', this.update_checkout);
        document.body.addEventListener('init_checkout', this.init_checkout);
        this.$checkout_form.addEventListener('click', event => {
            if (event.target.matches('input[name="payment_method"]')) {
                this.payment_method_selected(event);
            }
        });

        if (document.body.classList.contains('woocommerce-order-pay')) {
            this.$order_review.addEventListener('click', event => {
                if (event.target.matches('input[name="payment_method"]')) {
                    this.payment_method_selected(event);
                }
            });
            this.$order_review.addEventListener('submit', this.submitOrder);
            this.$order_review.setAttribute('novalidate', 'novalidate');
        }

        this.$checkout_form.setAttribute('novalidate', 'novalidate');
        this.$checkout_form.addEventListener('submit', this.submit);
        this.$checkout_form.addEventListener('input', this.validate_field);
        this.$checkout_form.addEventListener('validate', this.validate_field);
        this.$checkout_form.addEventListener('change', this.validate_field);
        this.$checkout_form.addEventListener('focusout', this.validate_field);
        this.$checkout_form.addEventListener('update', this.trigger_update_checkout);
        this.$checkout_form.addEventListener('change', event => {
            if (event.target.matches('select.shipping_method, input[name^="shipping_method"], #ship-to-different-address input')) {
                this.trigger_update_checkout(event);
            }
        });
        this.$checkout_form.addEventListener('change', event => {
            if (event.target.matches('.address-field select')) {
                this.input_changed(event);
            }
        });
        this.$checkout_form.addEventListener('change', event => {
            if (event.target.matches('.address-field input.input-text, .update_totals_on_change input.input-text')) {
                this.maybe_input_changed(event);
            }
        });
        this.$checkout_form.addEventListener('keydown', event => {
            if (event.target.matches('.address-field input.input-text, .update_totals_on_change input.input-text')) {
                this.queue_update_checkout(event);
            }
        });
        this.$checkout_form.addEventListener('change', event => {
            if (event.target.matches('#ship-to-different-address input')) {
                this.ship_to_different_address(event);
            }
        });

        document.querySelectorAll('#ship-to-different-address input').forEach(input => input.dispatchEvent(new Event('change')));
        this.init_payment_methods();

        if (wc_checkout_params.is_checkout === '1') {
            document.body.dispatchEvent(new Event('init_checkout'));
        }

        if (wc_checkout_params.option_guest_checkout === 'yes') {
            const createAccountInput = document.querySelector('input#createaccount');
            if (createAccountInput) {
                createAccountInput.addEventListener('change', this.toggle_create_account);
                createAccountInput.dispatchEvent(new Event('change'));
            }
        }
    },
    init_payment_methods: function() {
        const paymentMethods = document.querySelectorAll('.woocommerce-checkout input[name="payment_method"]');
        if (paymentMethods.length === 1) {
            paymentMethods[0].style.display = 'none';
        }
        if (this.selectedPaymentMethod) {
            document.getElementById(this.selectedPaymentMethod).checked = true;
        }
        if (!Array.from(paymentMethods).some(method => method.checked)) {
            paymentMethods[0].checked = true;
        }
        const checkedPaymentMethod = Array.from(paymentMethods).find(method => method.checked)?.id;
        if (paymentMethods.length > 1) {
            document.querySelectorAll(`div.payment_box:not(.${checkedPaymentMethod})`).forEach(box => box.style.display = 'none');
        }
        if (checkedPaymentMethod) {
            document.getElementById(checkedPaymentMethod).click();
        }
    },
    get_payment_method: function() {
        return this.$checkout_form.querySelector('input[name="payment_method"]:checked')?.value;
    },
    payment_method_selected: function(event) {
        event.stopPropagation();
        const selected = event.target;
        const paymentBoxes = document.querySelectorAll('div.payment_box');
        paymentBoxes.forEach(box => box.style.display = 'none');
        const targetBox = document.querySelector(`div.payment_box.${selected.id}`);
        if (targetBox) {
            targetBox.style.display = 'block';
        }
        const placeOrderButton = document.getElementById('place_order');
        if (selected.dataset.order_button_text) {
            placeOrderButton.textContent = selected.dataset.order_button_text;
        } else {
            placeOrderButton.textContent = placeOrderButton.dataset.value;
        }
        this.selectedPaymentMethod = selected.id;
        document.body.dispatchEvent(new Event('payment_method_selected'));
    },
    trigger_update_checkout: function(event) {
        clearTimeout(this.updateTimer);
        this.dirtyInput = false;
        document.body.dispatchEvent(new CustomEvent('update_checkout', { detail: { current_target: event ? event.target : null } }));
    },
    ship_to_different_address: function(event) {
        const shippingAddress = document.querySelector('div.shipping_address');
        shippingAddress.style.display = event.target.checked ? 'block' : 'none';
    },
    queue_update_checkout: function(event) {
        if (event.keyCode === 9) return true;
        this.dirtyInput = event.target;
        clearTimeout(this.updateTimer);
        this.updateTimer = setTimeout(() => this.trigger_update_checkout(), 1000);
    }
};

wc_checkout_form.init();
