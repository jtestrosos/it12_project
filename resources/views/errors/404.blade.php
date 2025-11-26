@extends('layouts.app')

@section('title', 'Page Not Found')

@section('content')
<div class="container d-flex flex-column align-items-center justify-content-center min-vh-100 py-5">
    <div class="text-center">
        <div class="mb-4 text-primary opacity-75">
            <i class="fas fa-map-signs fa-6x"></i>
        </div>
        <h1 class="display-1 fw-bold text-primary">404</h1>
        <h2 class="h4 mb-4 text-muted">Oops! The page you're looking for doesn't exist.</h2>
        <p class="mb-5 text-muted" style="max-width: 500px; margin: 0 auto;">
            It seems you've stumbled upon a broken link or entered a URL that doesn't exist. 
            Don't worry, you can find your way back home.
        </p>
        
        <div class="d-flex gap-3 justify-content-center">
            <a href="{{ url('/') }}" class="btn btn-outline-primary px-4">
                <i class="fas fa-home me-2"></i>Go Home
            </a>
            <a href="{{ url()->previous() }}" class="btn btn-primary px-4 text-white">
                <i class="fas fa-arrow-left me-2"></i>Go Back
            </a>
        </div>
    </div>
</div>
@endsection
