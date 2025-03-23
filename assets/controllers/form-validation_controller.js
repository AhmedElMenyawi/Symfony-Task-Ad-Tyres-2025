import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['submitButton']
    static values = {
        step: Number,
        totalSteps: Number,
        translations: Object
    }

    connect() {
        this.form = this.element;
        this.setupValidation();
        this.setupNavigation();
    }

    setupValidation() {
        this.form.addEventListener('submit', (event) => {
            if (!this.form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            this.form.classList.add('was-validated');
        });
    }

    setupNavigation() {
        const progressBar = this.form.querySelector('.progress-bar');
        if (progressBar) {
            const progress = (this.stepValue / this.totalStepsValue) * 100;
            progressBar.style.width = `${progress}%`;
            progressBar.setAttribute('aria-valuenow', progress);
        }
    }

    validateField(event) {
        const field = event.target;
        const fieldName = field.name;
        const value = field.value;

        // Clear previous error messages
        const errorElement = field.nextElementSibling;
        if (errorElement && errorElement.classList.contains('invalid-feedback')) {
            errorElement.style.display = 'none';
        }

        // Validate based on field type
        switch (fieldName) {
            case 'user[phoneNumber]':
                if (!/^[0-9]{8,15}$/.test(value)) {
                    this.showError(field, this.translationsValue.form.user.errors.phone);
                }
                break;
            case 'payment[cardNumber]':
                if (!/^[0-9]{16}$/.test(value)) {
                    this.showError(field, 'Card number must be exactly 16 digits');
                }
                break;
            case 'payment[expirationDate]':
                if (!/^(0[1-9]|1[0-2])\/([0-9]{2})$/.test(value)) {
                    this.showError(field, 'Invalid expiration date format (MM/YY)');
                }
                break;
            case 'payment[cvv]':
                if (!/^[0-9]{3}$/.test(value)) {
                    this.showError(field, 'CVV must be exactly 3 digits');
                }
                break;
        }
    }

    showError(field, message) {
        let errorElement = field.nextElementSibling;
        if (!errorElement || !errorElement.classList.contains('invalid-feedback')) {
            errorElement = document.createElement('div');
            errorElement.className = 'invalid-feedback';
            field.parentNode.insertBefore(errorElement, field.nextSibling);
        }
        errorElement.textContent = message;
        errorElement.style.display = 'block';
        field.classList.add('is-invalid');
    }

    // Handle subscription type change
    handleSubscriptionChange(event) {
        const subscriptionType = event.target.value;
        const submitButton = this.submitButtonTarget;
        
        if (subscriptionType === 'free') {
            submitButton.textContent = 'Complete Registration';
        } else {
            submitButton.textContent = 'Next Step';
        }
    }
} 