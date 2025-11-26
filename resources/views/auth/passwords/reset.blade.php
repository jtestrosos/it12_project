@extends('layouts.app')

@section('content')
@include('partials.home-content')

<div class="position-fixed top-0 start-0 w-100 h-100" style="z-index: 1050; background-color: rgba(0,0,0,0.4); backdrop-filter: blur(5px); overflow-y: auto;">
    <div class="d-flex min-vh-100 align-items-center justify-content-center py-5">
        <div class="container" style="max-width: 450px;">
            <x-card class="shadow-lg border-0" noPadding>
                <div class="card-header bg-info text-white text-center position-relative py-3">
                    <h4 class="mb-0">Set New Password</h4>
                    <a href="{{ route('login') }}" class="btn-close btn-close-white position-absolute top-50 end-0 translate-middle-y me-3" aria-label="Close"></a>
                </div>
                <div class="card-body p-4">
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

                        <x-input name="password" id="reset-password" label="New Password" type="password" required />
                        
                        <x-input name="password_confirmation" id="reset-password-confirm" label="Confirm Password" type="password" required />

                        <x-button type="submit" variant="info" class="w-100 text-white">Reset Password</x-button>
                    </form>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
