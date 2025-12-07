@extends('layouts.app')

@push('styles')
<style>
    /* Darker borders for form inputs to match buttons */
    .form-control {
        border-color: #6c757d !important;
    }
    
    .form-control:focus {
        border-color: #495057 !important;
        box-shadow: 0 0 0 0.25rem rgba(108, 117, 125, 0.25) !important;
    }
    
    .btn-outline-secondary {
        border-color: #6c757d !important;
    }
</style>
@endpush

@section('content')
    @include('partials.home-content')

    <div class="position-fixed top-0 start-0 w-100 h-100"
        style="z-index: 1050; background-color: rgba(0,0,0,0.4); backdrop-filter: blur(5px); overflow-y: auto;">
        <div class="d-flex min-vh-100 align-items-center justify-content-center py-5">
            <div class="container" style="max-width: 450px;">
                <x-card class="shadow-lg border-0" noPadding>
                <div class="card-header bg-primary text-white text-center position-relative py-3">
                    <h4 class="mb-0">Register</h4>
                    <a href="{{ route('home') }}"
                        class="btn-close btn-close-white position-absolute top-50 end-0 translate-middle-y me-3"
                        aria-label="Close"></a>
                </div>
                <div class="card-body p-4">
                    @php
                        $selectedBarangay = old('barangay');
                        $purokOptions = match ($selectedBarangay) {
                            'Barangay 11' => ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'],
                            'Barangay 12' => ['Purok 1', 'Purok 2', 'Purok 3'],
                            default => [],
                        };
                    @endphp
                    <form method="POST" action="{{ route('register') }}" class="registration-form">
                        @csrf

                        <x-input name="name" label="Full Name" required value="{{ old('name') }}" />

                        <div class="mb-3">
                            <label for="gender" class="form-label fw-medium">Gender <span
                                    class="text-danger">*</span></label>
                            <select name="gender" class="form-control @error('gender') is-invalid @enderror" required>
                                <option value="" disabled selected>Select Gender</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Prefer not to say</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <x-input name="email" label="Email" type="email" required value="{{ old('email') }}" />

                        <x-input name="phone" label="Phone Number" placeholder="Enter your phone number (optional)"
                            value="{{ old('phone') }}" />

                        <div class="mb-3">
                            <label for="address" class="form-label fw-medium">Address <small
                                    class="text-muted fw-normal">(Optional)</small></label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                                rows="2"
                                placeholder="Enter your complete address (optional)">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="barangay" class="form-label fw-medium">Barangay <span
                                    class="text-danger">*</span></label>
                            <select name="barangay" class="form-control @error('barangay') is-invalid @enderror"
                                data-role="barangay" required>
                                <option value="">Select Barangay</option>
                                <option value="Barangay 11" {{ $selectedBarangay === 'Barangay 11' ? 'selected' : '' }}>
                                    Barangay 11</option>
                                <option value="Barangay 12" {{ $selectedBarangay === 'Barangay 12' ? 'selected' : '' }}>
                                    Barangay 12</option>
                                <option value="Other" {{ $selectedBarangay === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('barangay')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 {{ $selectedBarangay === 'Other' ? '' : 'd-none' }}"
                            data-role="barangay-other-group">
                            <x-input name="barangay_other" label="Specify Barangay" required
                                value="{{ old('barangay_other') }}" data-role="barangay-other" />
                        </div>

                        <div class="mb-3 {{ in_array($selectedBarangay, ['Barangay 11', 'Barangay 12']) ? '' : 'd-none' }}"
                            data-role="purok-group">
                            <label for="purok" class="form-label fw-medium">Purok <span
                                    class="text-danger">*</span></label>
                            <select name="purok" class="form-control @error('purok') is-invalid @enderror"
                                data-role="purok" data-selected="{{ old('purok') }}">
                                <option value="">Select Purok</option>
                                @foreach ($purokOptions as $purok)
                                    <option value="{{ $purok }}" {{ old('purok') === $purok ? 'selected' : '' }}>{{ $purok }}
                                    </option>
                                @endforeach
                            </select>
                            @error('purok')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <x-input name="birth_date" label="Birth Date" type="date" required
                            value="{{ old('birth_date') }}" data-role="birth-date" max="{{ now()->toDateString() }}" />

                        <x-input name="password" id="register-password" label="Password" type="password" required />

                        <x-input name="password_confirmation" id="register-password-confirm" label="Confirm Password"
                            type="password" required />

                        <x-button type="submit" variant="primary" class="w-100 text-white">Register</x-button>
                        </form>
                        <div class="text-center mt-3">
                            <small>Already have an account? <a href="{{ route('login') }}">Login here</a></small>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- Barangay and Purok Logic ---
            const barangayPurokMap = {
                'Barangay 11': ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'],
                'Barangay 12': ['Purok 1', 'Purok 2', 'Purok 3']
            };

            const registrationForms = document.querySelectorAll('.registration-form');
            registrationForms.forEach((form) => {
                const barangaySelect = form.querySelector('[data-role="barangay"]');
                const barangayOtherGroup = form.querySelector('[data-role="barangay-other-group"]');
                const barangayOtherInput = form.querySelector('[data-role="barangay-other"]');
                const purokGroup = form.querySelector('[data-role="purok-group"]');
                const purokSelect = form.querySelector('[data-role="purok"]');

                const updatePurokOptions = (barangay) => {
                    if (!purokSelect) return;
                    const previouslySelected = purokSelect.getAttribute('data-selected');
                    purokSelect.innerHTML = '<option value="">Select Purok</option>';

                    if (!barangayPurokMap[barangay]) {
                        purokSelect.removeAttribute('required');
                        purokSelect.setAttribute('data-selected', '');
                        return;
                    }

                    barangayPurokMap[barangay].forEach((purok) => {
                        const option = document.createElement('option');
                        option.value = purok;
                        option.textContent = purok;
                        if (previouslySelected === purok) {
                            option.selected = true;
                        }
                        purokSelect.appendChild(option);
                    });
                    purokSelect.setAttribute('required', 'required');
                };

                const handleBarangayChange = () => {
                    const selectedBarangay = barangaySelect ? barangaySelect.value : '';

                    if (barangayOtherGroup && barangayOtherInput) {
                        if (selectedBarangay === 'Other') {
                            barangayOtherGroup.classList.remove('d-none');
                            barangayOtherInput.setAttribute('required', 'required');
                        } else {
                            barangayOtherGroup.classList.add('d-none');
                            barangayOtherInput.removeAttribute('required');
                        }
                    }

                    if (purokGroup && purokSelect) {
                        if (barangayPurokMap[selectedBarangay]) {
                            purokGroup.classList.remove('d-none');
                            updatePurokOptions(selectedBarangay);
                        } else {
                            purokGroup.classList.add('d-none');
                            purokSelect.removeAttribute('required');
                            purokSelect.value = '';
                            purokSelect.setAttribute('data-selected', '');
                        }
                    }
                };

                if (barangaySelect) {
                    barangaySelect.addEventListener('change', () => {
                        if (purokSelect) {
                            purokSelect.setAttribute('data-selected', '');
                        }
                        handleBarangayChange();
                    });
                    handleBarangayChange();
                }
            });

            // --- Validation Logic ---
            const form = document.querySelector('.registration-form');
            const passwordInput = document.getElementById('register-password');
            const passwordConfirmInput = document.getElementById('register-password-confirm');
            const nameInput = form.querySelector('input[name="name"]');
            const phoneInput = form.querySelector('input[name="phone"]');

            const clearClientErrors = () => {
                const clientErrors = form.querySelectorAll('.invalid-feedback.js-register-error');
                clientErrors.forEach((el) => el.remove());
                if (nameInput) nameInput.classList.remove('is-invalid');
                if (phoneInput) phoneInput.classList.remove('is-invalid');
                if (passwordInput) passwordInput.classList.remove('is-invalid');
                if (passwordConfirmInput) passwordConfirmInput.classList.remove('is-invalid');
            };

            const appendError = (input, message) => {
                if (!input) return;
                input.classList.add('is-invalid');
                const div = document.createElement('div');
                div.className = 'invalid-feedback js-register-error d-block';
                div.textContent = message;
                // Insert after the parent div (input-group) if it exists, otherwise after input
                const parent = input.closest('.input-group') || input;
                if (parent.nextElementSibling && parent.nextElementSibling.classList.contains('invalid-feedback')) {
                    // Already has error
                } else {
                    parent.insertAdjacentElement('afterend', div);
                }
            };

            const validateNameAndPhone = () => {
                clearClientErrors();
                let valid = true;

                if (nameInput) {
                    const value = nameInput.value.trim();
                    const nameRegex = /^[a-zA-Z\s.\-']+$/;
                    if (!value) {
                        appendError(nameInput, 'Full name is required.');
                        valid = false;
                    } else if (!nameRegex.test(value)) {
                        appendError(nameInput, 'The name field should not contain numbers. Only letters, spaces, periods, hyphens, and apostrophes are allowed.');
                        valid = false;
                    }
                }

                if (phoneInput) {
                    const value = phoneInput.value.trim();
                    if (value && !/^\d{11}$/.test(value)) {
                        appendError(phoneInput, 'Phone number must be exactly 11 digits (e.g. 09123456789).');
                        valid = false;
                    }
                }

                if (passwordInput) {
                    const value = passwordInput.value;
                    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=[\]{}|,.<>\/?]).{8,}$/;
                    if (!value) {
                        appendError(passwordInput, 'Password is required.');
                        valid = false;
                    } else if (!passwordRegex.test(value)) {
                        appendError(passwordInput, 'Password must be at least 8 characters and include one uppercase letter, one lowercase letter, one number, and one special character.');
                        valid = false;
                    }
                }

                if (passwordInput && passwordConfirmInput) {
                    const value = passwordInput.value;
                    const confirmValue = passwordConfirmInput.value;
                    if (confirmValue && value !== confirmValue) {
                        appendError(passwordConfirmInput, 'Password and confirm password must match.');
                        valid = false;
                    }
                }

                return valid;
            };

            if (form) {
                if (nameInput) {
                    nameInput.addEventListener('input', validateNameAndPhone);
                    nameInput.addEventListener('blur', validateNameAndPhone);
                }
                if (phoneInput) {
                    phoneInput.addEventListener('input', validateNameAndPhone);
                    phoneInput.addEventListener('blur', validateNameAndPhone);
                }
                if (passwordInput) {
                    passwordInput.addEventListener('input', validateNameAndPhone);
                    passwordInput.addEventListener('blur', validateNameAndPhone);
                }
                if (passwordConfirmInput) {
                    passwordConfirmInput.addEventListener('input', validateNameAndPhone);
                    passwordConfirmInput.addEventListener('blur', validateNameAndPhone);
                }

                form.addEventListener('submit', function (e) {
                    if (!validateNameAndPhone()) {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
@endsection