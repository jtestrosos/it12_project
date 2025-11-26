@extends('layouts.app')

@section('content')
@include('partials.home-content')

<div class="position-fixed top-0 start-0 w-100 h-100" style="z-index: 1050; background-color: rgba(0,0,0,0.4); backdrop-filter: blur(5px); overflow-y: auto;">
    <div class="d-flex min-vh-100 align-items-center justify-content-center py-5">
        <div class="container" style="max-width: 450px;">
            <x-card class="shadow-lg border-0" noPadding>
                <div class="card-header bg-info text-white text-center position-relative py-3">
                    <h4 class="mb-0">Login</h4>
                    <a href="{{ route('home') }}" class="btn-close btn-close-white position-absolute top-50 end-0 translate-middle-y me-3" aria-label="Close"></a>
                </div>
                <div class="card-body p-4">
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

                        <x-input 
                            name="email" 
                            label="Email" 
                            type="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                        />

                        <x-input 
                            name="password" 
                            label="Password" 
                            type="password" 
                            required 
                        />

                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Remember Me
                                </label>
                            </div>
                            <a href="{{ route('password.request') }}" class="text-decoration-none small">Forgot Password?</a>
                        </div>

                        <x-button type="submit" variant="info" class="w-100 text-white">Login</x-button>
                    </form>
                    <div class="text-center mt-3">
                        <small>Don’t have an account? <a href="{{ route('register') }}">Register here</a></small>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
