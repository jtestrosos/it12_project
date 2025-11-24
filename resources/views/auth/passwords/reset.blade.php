@extends('layouts.app')

@section('content')
@include('partials.home-content')

<div class="position-fixed top-0 start-0 w-100 h-100" style="z-index: 1050; background-color: rgba(0,0,0,0.4); backdrop-filter: blur(5px); overflow-y: auto;">
    <div class="d-flex min-vh-100 align-items-center justify-content-center py-5">
        <div class="container" style="max-width: 450px;">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-info text-white text-center position-relative">
                    <h4>Set New Password</h4>
                    <a href="{{ route('login') }}" class="btn-close btn-close-white position-absolute top-50 end-0 translate-middle-y me-3" aria-label="Close"></a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                         <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">
                        <input type="hidden" name="otp" value="{{ $otp }}">

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="reset-password" class="form-control @error('password') is-invalid @enderror" required>
                                <button class="btn btn-outline-secondary" type="button" id="reset-show-password-btn">
                                    <i id="reset-show-password-icon" class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password-confirm" class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="reset-password-confirm" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-info w-100 text-white">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('reset-password');
        const passwordConfirmInput = document.getElementById('reset-password-confirm');
        const toggleBtn = document.getElementById('reset-show-password-btn');
        const toggleIcon = document.getElementById('reset-show-password-icon');

        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', function() {
                const showing = passwordInput.type === 'text';
                const show = !showing;
                
                passwordInput.type = show ? 'text' : 'password';
                if (passwordConfirmInput) {
                    passwordConfirmInput.type = show ? 'text' : 'password';
                }

                if (toggleIcon) {
                    if (show) {
                        toggleIcon.classList.remove('fa-eye');
                        toggleIcon.classList.add('fa-eye-slash');
                    } else {
                        toggleIcon.classList.remove('fa-eye-slash');
                        toggleIcon.classList.add('fa-eye');
                    }
                }
            });
        }
    });
</script>
@endsection
