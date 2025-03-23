import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['form', 'submitButton']
    static values = {
        step: Number,
        totalSteps: Number,
        translations: Object
    }

    connect() {
        this.form = this.formTarget;
        this.submitButton = this.submitButtonTarget;
        this.currentStep = this.stepValue;
        this.totalSteps = this.totalStepsValue;
        this.translations = this.translationsValue;

        // Initialize form validation
        this.initializeValidation();
        this.updateProgressBar();
    }

    initializeValidation() {
        // Add validation on form submission
        this.form.addEventListener('submit', (e) => {
            if (!this.validateForm()) {
                e.preventDefault();
            }
        });

        // Add real-time validation to all inputs
        this.form.querySelectorAll('input, select').forEach(field => {
            field.addEventListener('input', () => this.validateField(field));
            field.addEventListener('blur', () => this.validateField(field));
        });
    }

    validateForm() {
        const form = this.formTarget;
        const submitButton = this.submitButtonTarget;

        // Add CSRF token validation
        if (!form.querySelector('input[name="_token"]')) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
        }

        // Add step validation
        form.addEventListener('submit', (event) => {
            if (!this.validateStep()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });

        // Real-time validation
        form.querySelectorAll('input, select').forEach(field => {
            field.addEventListener('input', () => this.validateField(field));
        });
    }

    validateStep() {
        const form = this.formTarget;
        const fields = form.querySelectorAll('input[required], select[required]');
        let isValid = true;

        fields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    validateField(field) {
        const fieldName = field.name;
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';

        // Clear previous error
        this.removeError(field);

        // Validate based on field type
        switch (fieldName) {
            case 'user[name]':
                isValid = value.length >= 2;
                errorMessage = this.translations.form.user.errors.name;
                break;
            case 'user[email]':
                isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                errorMessage = this.translations.form.user.errors.email;
                break;
            case 'user[phoneNumber]':
                isValid = /^[0-9]{8,10}$/.test(value);
                errorMessage = this.translations.form.user.errors.phone;
                break;
            case 'address[addressLine1]':
                isValid = value.length > 0;
                errorMessage = this.translations.form.address.errors.addressLine1;
                break;
            case 'address[city]':
                isValid = value.length > 0;
                errorMessage = this.translations.form.address.errors.city;
                break;
            case 'address[postalCode]':
                isValid = this.validatePostalCode(value, this.form.querySelector('#address_country').value);
                errorMessage = this.translations.form.address.errors.postalCode;
                break;
            case 'address[stateProvince]':
                isValid = value.length > 0;
                errorMessage = this.translations.form.address.errors.stateProvince;
                break;
            case 'address[country]':
                isValid = value !== '';
                errorMessage = this.translations.form.address.errors.country;
                break;
            case 'payment_info[cardNumber]':
                isValid = this.validateCardNumber(value);
                errorMessage = this.translations.form.payment.errors.cardNumber;
                break;
            case 'payment_info[expirationDate]':
                isValid = this.validateExpirationDate(value);
                errorMessage = this.translations.form.payment.errors.expirationDate;
                break;
            case 'payment_info[cvv]':
                isValid = /^\d{3,4}$/.test(value);
                errorMessage = this.translations.form.payment.errors.cvv;
                break;
        }

        if (!isValid) {
            this.showError(field, errorMessage);
        }

        return isValid;
    }

    showError(field, message) {
        const errorDiv = field.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
            errorDiv.textContent = message;
        } else {
            const newErrorDiv = document.createElement('div');
            newErrorDiv.className = 'invalid-feedback';
            newErrorDiv.textContent = message;
            field.parentNode.insertBefore(newErrorDiv, field.nextSibling);
        }
        field.classList.add('is-invalid');
    }

    removeError(field) {
        const errorDiv = field.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
            errorDiv.remove();
        }
        field.classList.remove('is-invalid');
    }

    updateProgressBar() {
        const progress = (this.currentStep / this.totalSteps) * 100;
        const progressBar = this.form.querySelector('.progress-bar');
        if (progressBar) {
            progressBar.style.width = `${progress}%`;
            progressBar.setAttribute('aria-valuenow', progress);
            progressBar.textContent = `Step ${this.currentStep} of ${this.totalSteps}`;
        }
    }

    // Navigation methods
    nextStep(event) {
        if (this.validateStep()) {
            this.submitButton.disabled = true;
            this.submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
        } else {
            event.preventDefault();
        }
    }

    previousStep() {
        window.history.back();
    }
} 