document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("input").forEach(input => {
        input.addEventListener("invalid", function (event) {
            const form = event.target.closest("form");
            const isRequired = input.hasAttribute("required");
            const isEmpty = event.target.validity.valueMissing;
            const isPatternMismatch = input.validity.patternMismatch;

            let message = "";

            if (isRequired && isEmpty) {
                message = form?.dataset.required || "";
            } else if (isPatternMismatch && input.value.trim() !== "") {
                message = input.dataset.invalid || "";
            }

            event.target.setCustomValidity(message);
        });

        input.addEventListener("input", function (event) {
            event.target.setCustomValidity(""); // Remove erro personalizado

            const originalValue = event.target.value;
            const validValue = generateValidValue(input.pattern) || "valid";

            event.target.value = validValue;
            event.target.checkValidity();

			if (event.target.validity.valid) {
            	event.target.value = originalValue;
			}
        });

        input.closest("form").addEventListener("submit", function (event) {
            input.reportValidity();
        });
    });
});

// Função para gerar um valor válido baseado no pattern
function generateValidValue(pattern) {
    if (!pattern) return null;

    if (pattern.includes("[A-Za-z]")) return "Ab";
    if (pattern.includes("\\d")) return "123";
    if (pattern.includes("[a-z]")) return "abc";
    if (pattern.includes("[A-Z]")) return "ABC";

    return "valid";
}
