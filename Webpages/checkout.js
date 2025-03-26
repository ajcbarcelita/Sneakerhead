// Form validation
document.getElementById('checkout-form').addEventListener('submit', function (e) {
    let hasErrors = false;
    const delivery = document.querySelector('input[name="delivery"]:checked').value;

    // Only validate shipping info if delivery type is "ship"
    if (delivery === 'ship') {
        const requiredFields = ['firstName', 'lastName', 'address', 'city', 'province'];

        requiredFields.forEach(field => {
            const input = document.querySelector(`input[name="${field}"]`);
            if (!input.value.trim()) {
                hasErrors = true;
                // Add error styling
                input.style.borderColor = 'red';
            } else {
                input.style.borderColor = '';
            }
        });
    }

    // Validate email
    const email = document.querySelector('input[name="email"]');
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email.value)) {
        hasErrors = true;
        email.style.borderColor = 'red';
    } else {
        email.style.borderColor = '';
    }

    // Validate phone
    const phone = document.querySelector('input[name="phone"]');
    const phonePattern = /^[0-9+\-\s]+$/;
    if (!phonePattern.test(phone.value)) {
        hasErrors = true;
        phone.style.borderColor = 'red';
    } else {
        phone.style.borderColor = '';
    }

    if (hasErrors) {
        e.preventDefault();
        alert('Please fill out all required fields correctly.');
    } else {
        // Show processing state
        const completeOrderBtn = document.querySelector('.complete-order-btn');
        completeOrderBtn.textContent = 'Processing...';
        completeOrderBtn.disabled = true;

        // Add subtotal and shipping as hidden fields if not already present
        if (!document.querySelector('input[name="subtotal"]')) {
            const subtotalInput = document.createElement('input');
            subtotalInput.type = 'hidden';
            subtotalInput.name = 'subtotal';
            subtotalInput.value = document.getElementById('subtotal').textContent.replace('₱', '').replace(',', '');
            document.getElementById('checkout-form').appendChild(subtotalInput);
        }

        if (!document.querySelector('input[name="shipping"]')) {
            const shippingInput = document.createElement('input');
            shippingInput.type = 'hidden';
            shippingInput.name = 'shipping';
            shippingInput.value = document.getElementById('shipping').textContent.replace('₱', '').replace(',', '');
            document.getElementById('checkout-form').appendChild(shippingInput);
        }

        // Continue with form submission
        return true;
    }
});

// Promo code functionality
document.getElementById('apply-promo').addEventListener('click', function () {
    const promoCode = document.getElementById('promo_code').value;
    const promoMessage = document.getElementById('promo-message');
    const subtotal = parseFloat(document.getElementById('subtotal').textContent.replace('₱', '').replace(',', ''));
    const shipping = parseFloat(document.getElementById('shipping').textContent.replace('₱', '').replace(',', ''));

    if (!promoCode) {
        promoMessage.textContent = 'Please enter a promo code';
        promoMessage.style.color = 'red';
        console.error('Promo code validation failed: Promo code is empty.');
        return;
    }

    // Send AJAX request to validate promo code
    fetch('checkout-handler.php?action=validate_promo', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'promo_code=' + encodeURIComponent(promoCode) + '&subtotal=' + subtotal
    })
        .then(response => response.json())
        .then(data => {
            if (data.valid) {
                // Show success message
                promoMessage.textContent = data.message;
                promoMessage.style.color = 'green';

                // Update discount and total
                document.getElementById('discount-row').style.display = 'flex';
                document.getElementById('discount').textContent = '-₱' + data.discount.toFixed(2);
                document.getElementById('total').textContent = '₱' + data.new_total.toFixed(2);

                // Update hidden field with discount info
                document.getElementById('discount_amount').value = data.discount;

                console.log('Promo code validation succeeded: Promo code applied with discount ₱' + data.discount.toFixed(2));
            } else {
                // Show error message
                promoMessage.textContent = data.message;
                promoMessage.style.color = 'red';

                // Reset discount and total
                document.getElementById('discount-row').style.display = 'none';
                document.getElementById('total').textContent = '₱' + (subtotal + shipping).toFixed(2);
                document.getElementById('discount_amount').value = '0';

                console.error('Promo code validation failed: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error validating promo code:', error);
            promoMessage.textContent = 'An error occurred. Please try again.';
            promoMessage.style.color = 'red';
        });
});
