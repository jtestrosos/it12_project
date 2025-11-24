@extends('layouts.app')

@section('content')
@include('partials.home-content')

<div class="position-fixed top-0 start-0 w-100 h-100" style="z-index: 1050; background-color: rgba(0,0,0,0.4); backdrop-filter: blur(5px); overflow-y: auto;">
    <div class="d-flex min-vh-100 align-items-center justify-content-center py-5">
        <div class="container" style="max-width: 450px;">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-info text-white text-center position-relative">
                <h4>Login</h4>
                <a href="{{ route('home') }}" class="btn-close btn-close-white position-absolute top-50 end-0 translate-middle-y me-3" aria-label="Close"></a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="login-password" class="form-control @error('password') is-invalid @enderror" required>
                            <button class="btn btn-outline-secondary" type="button" id="login-show-password-btn">
                                <i id="login-show-password-icon" class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Remember Me
                            </label>
                        </div>
                        <a href="{{ route('password.request') }}" class="text-decoration-none small">Forgot Password?</a>
                    </div>
                    <button type="submit" class="btn btn-info w-100 text-white">Login</button>
                </form>
                <div class="text-center mt-3">
                    <small>Don’t have an account? <a href="{{ route('register') }}">Register here</a></small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginPasswordInput = document.getElementById('login-password');
        const loginShowPasswordBtn = document.getElementById('login-show-password-btn');
        const loginShowPasswordIcon = document.getElementById('login-show-password-icon');

        if (loginShowPasswordBtn && loginPasswordInput) {
            loginShowPasswordBtn.addEventListener('click', function() {
                const showing = loginPasswordInput.type === 'text';
                const show = !showing;
                loginPasswordInput.type = show ? 'text' : 'password';
                if (loginShowPasswordIcon) {
                    if (show) {
                        loginShowPasswordIcon.classList.remove('fa-eye');
                        loginShowPasswordIcon.classList.add('fa-eye-slash');
                    } else {
                        loginShowPasswordIcon.classList.remove('fa-eye-slash');
                        loginShowPasswordIcon.classList.add('fa-eye');
                    }
                }
            });
        }
    });
</script>
@endsection
