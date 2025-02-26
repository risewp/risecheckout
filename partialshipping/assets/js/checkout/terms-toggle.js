document.addEventListener("DOMContentLoaded", function () {
    if (typeof wc_checkout_params === "undefined") {
        return false;
    }

    const wc_terms_toggle = {
        init: function () {
            document.body.addEventListener("click", function (event) {
                if (event.target.matches("a.woocommerce-terms-and-conditions-link")) {
                    wc_terms_toggle.toggle_terms(event);
                }
            });
        },

        toggle_terms: function (event) {
            const termsElement = document.querySelector(".woocommerce-terms-and-conditions");
            if (termsElement) {
                event.preventDefault();
                const linkToggle = document.querySelector(".woocommerce-terms-and-conditions-link");
                const isVisible = termsElement.style.display !== "none";
                termsElement.style.display = isVisible ? "none" : "block";
                linkToggle.classList.toggle("woocommerce-terms-and-conditions-link--open", !isVisible);
                linkToggle.classList.toggle("woocommerce-terms-and-conditions-link--closed", isVisible);
            }
        }
    };

    wc_terms_toggle.init();
});
