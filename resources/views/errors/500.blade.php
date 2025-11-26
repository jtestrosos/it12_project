@extends('layouts.app')

@section('title', 'Server Error')

@section('content')
<div class="container d-flex flex-column align-items-center justify-content-center min-vh-100 py-5">
    <div class="text-center">
        <div class="mb-4 text-danger opacity-75">
            <i class="fas fa-server fa-6x"></i>
        </div>
        <h1 class="display-1 fw-bold text-danger">500</h1>
        <h2 class="h4 mb-4 text-muted">Internal Server Error</h2>
        <p class="mb-5 text-muted" style="max-width: 500px; margin: 0 auto;">
            Something went wrong on our end. We're working to fix it as soon as possible.
            Please try again later or contact support if the problem persists.
        </p>
        
        <div class="d-flex gap-3 justify-content-center">
            <a href="{{ url('/') }}" class="btn btn-outline-secondary px-4">
                <i class="fas fa-home me-2"></i>Go Home
            </a>
            <button onclick="window.location.reload()" class="btn btn-danger px-4 text-white">
                <i class="fas fa-sync-alt me-2"></i>Try Again
            </button>
        </div>
    </div>
</div>
@endsection
