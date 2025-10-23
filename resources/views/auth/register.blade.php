@extends('layouts.app')

@section('content')
<div class="container my-5" style="max-width: 450px;">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-info text-white text-center">
            <h4>Register</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-info w-100 text-white">Register</button>
            </form>
            <div class="text-center mt-3">
                <small>Already have an account? <a href="{{ route('login') }}">Login here</a></small>
            </div>
        </div>
    </div>
</div>
@endsection
