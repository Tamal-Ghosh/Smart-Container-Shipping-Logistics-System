/**
 * Smart Container Shipping & Logistics
 * Authentication — Client-side Interactions & Validation
 */
document.addEventListener('DOMContentLoaded', function () {

    /* ================================================================
       1. Role Toggle (Customer / Admin)
       ================================================================ */
    const roleBtns     = document.querySelectorAll('.role-btn');
    const roleInput    = document.getElementById('role-input');
    const customerDiv  = document.getElementById('customer-fields');

    if (roleBtns.length && roleInput && customerDiv) {
        roleBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                // Swap active class
                roleBtns.forEach(function (b) { b.classList.remove('active'); });
                btn.classList.add('active');

                const role = btn.getAttribute('data-role');
                roleInput.value = role;

                if (role === 'CUSTOMER') {
                    customerDiv.classList.remove('hidden');
                } else {
                    customerDiv.classList.add('hidden');
                }
            });
        });
    }

    /* ================================================================
       2. Live Password Strength Checker
       ================================================================ */
    const passwordField = document.getElementById('password');
    const hints = {
        length   : document.getElementById('hint-length'),
        upper    : document.getElementById('hint-upper'),
        lower    : document.getElementById('hint-lower'),
        number   : document.getElementById('hint-number'),
        special  : document.getElementById('hint-special'),
    };

    function updateHint(el, isValid) {
        if (!el) return;
        if (isValid) {
            el.classList.add('valid');
            el.classList.remove('invalid');
            el.querySelector('.hint-icon').textContent = '✓';
        } else {
            el.classList.remove('valid');
            el.classList.add('invalid');
            el.querySelector('.hint-icon').textContent = '✗';
        }
    }

    if (passwordField) {
        passwordField.addEventListener('input', function () {
            var val = passwordField.value;
            updateHint(hints.length,  val.length >= 8);
            updateHint(hints.upper,   /[A-Z]/.test(val));
            updateHint(hints.lower,   /[a-z]/.test(val));
            updateHint(hints.number,  /[0-9]/.test(val));
            updateHint(hints.special, /[^A-Za-z0-9]/.test(val));

            // Also recheck password match if confirm field has value
            checkPasswordMatch();
        });
    }

    /* ================================================================
       3. Password Match Checker
       ================================================================ */
    const confirmField   = document.getElementById('password_confirmation');
    const matchIndicator = document.getElementById('match-indicator');

    function checkPasswordMatch() {
        if (!confirmField || !matchIndicator || !passwordField) return;
        var confirmVal = confirmField.value;
        if (confirmVal.length === 0) {
            matchIndicator.classList.remove('visible', 'match', 'mismatch');
            return;
        }

        matchIndicator.classList.add('visible');

        if (passwordField.value === confirmVal) {
            matchIndicator.classList.add('match');
            matchIndicator.classList.remove('mismatch');
            matchIndicator.innerHTML = '<span>✓</span> Passwords match';
        } else {
            matchIndicator.classList.add('mismatch');
            matchIndicator.classList.remove('match');
            matchIndicator.innerHTML = '<span>✗</span> Passwords do not match';
        }
    }

    if (confirmField) {
        confirmField.addEventListener('input', checkPasswordMatch);
    }

    /* ================================================================
       4. Inline Validation on Blur
       ================================================================ */
    var requiredInputs = document.querySelectorAll('.form-input[required]');

    requiredInputs.forEach(function (input) {
        input.addEventListener('blur', function () {
            if (input.value.trim() === '') {
                input.classList.add('input-error');
            } else {
                input.classList.remove('input-error');
            }
        });

        // Remove error styling on focus
        input.addEventListener('focus', function () {
            input.classList.remove('input-error');
        });
    });

    /* ================================================================
       5. Form Submit — Scroll to First Error
       ================================================================ */
    var authForm = document.querySelector('.auth-form');

    if (authForm) {
        authForm.addEventListener('submit', function (e) {
            var firstError = null;

            requiredInputs.forEach(function (input) {
                // Skip customer fields if Admin role is selected
                if (roleInput && roleInput.value === 'ADMIN') {
                    var closestCustomer = input.closest('#customer-fields');
                    if (closestCustomer) return;
                }

                if (input.value.trim() === '') {
                    input.classList.add('input-error');
                    if (!firstError) firstError = input;
                }
            });

            // Check password match
            if (passwordField && confirmField && confirmField.value.length > 0) {
                if (passwordField.value !== confirmField.value) {
                    confirmField.classList.add('input-error');
                    if (!firstError) firstError = confirmField;
                }
            }

            if (firstError) {
                e.preventDefault();
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(function () { firstError.focus(); }, 400);
            }
        });
    }



    /* ================================================================
       7. Auto-shake server-side error fields on load
       ================================================================ */
    document.querySelectorAll('.error-text').forEach(function (err) {
        var group = err.closest('.form-group');
        if (group) {
            var input = group.querySelector('.form-input');
            if (input) input.classList.add('input-error');
        }
    });

});
