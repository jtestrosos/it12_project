@props([
    'name',
    'label' => null,
    'type' => 'text',
    'placeholder' => null,
    'value' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'helper' => null,
    'icon' => null,
    'iconPosition' => 'left', // left, right
])

@php
    $hasError = $errors->has($name);
    $id = $name . '-' . uniqid();
@endphp

<div class="mb-3">
    @if($label)
        <label for="{{ $id }}" class="form-label fw-medium">
            {{ $label }}
            @if($required) <span class="text-danger">*</span> @endif
        </label>
    @endif

    <div class="input-group {{ $hasError ? 'is-invalid' : '' }}">
        @if($icon && $iconPosition === 'left')
            <span class="input-group-text bg-light border-end-0 text-muted">
                <i class="{{ $icon }}"></i>
            </span>
        @endif

        <input 
            type="{{ $type }}" 
            name="{{ $name }}" 
            id="{{ $id }}"
            value="{{ old($name, $value) }}"
            class="form-control {{ $hasError ? 'is-invalid' : '' }} {{ $icon && $iconPosition === 'left' ? 'border-start-0 ps-0' : '' }} {{ $icon && $iconPosition === 'right' ? 'border-end-0 pe-0' : '' }} {{ $type === 'password' ? 'password-input-outline' : '' }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            {{ $attributes }}
        >

        @if($icon && $iconPosition === 'right')
            <span class="input-group-text bg-light border-start-0 text-muted">
                <i class="{{ $icon }}"></i>
            </span>
        @endif

        @if($type === 'password')
            <button class="btn btn-outline-secondary password-toggle-btn" type="button" onclick="togglePasswordVisibility('{{ $id }}', this)">
                <i class="fa-solid fa-eye"></i>
            </button>
        @endif
    </div>

    @error($name)
        <div class="invalid-feedback d-block mt-1">
            {{ $message }}
        </div>
    @enderror

    @if($helper && !$hasError)
        <div class="form-text text-muted small mt-1">{{ $helper }}</div>
    @endif
</div>

@once
    @push('styles')
    <style>
        /* Password input outline styling */
        .password-input-outline {
            background-color: transparent !important;
            border-color: #6c757d !important;
        }

        .password-input-outline:focus {
            background-color: transparent !important;
            border-color: #6c757d !important;
            box-shadow: 0 0 0 0.25rem rgba(108, 117, 125, 0.25) !important;
        }

        .password-toggle-btn {
            border-color: #6c757d !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0.375rem 0.75rem !important;
            min-width: 38px !important;
        }

        .password-toggle-btn i {
            margin: 0 !important;
            padding: 0 !important;
            line-height: 1 !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }

        .password-toggle-btn:hover,
        .password-toggle-btn:focus {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: white !important;
        }

        /* Dark mode support */
        body.bg-dark .password-input-outline {
            background-color: transparent !important;
            border-color: #6c757d !important;
            color: #e6e6e6 !important;
        }

        body.bg-dark .password-input-outline:focus {
            background-color: transparent !important;
            border-color: #6c757d !important;
            box-shadow: 0 0 0 0.25rem rgba(108, 117, 125, 0.25) !important;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
    @endpush
@endonce
