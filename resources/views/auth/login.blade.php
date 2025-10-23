@extends('layouts.app')

@section('content')
<div class="container my-5" style="max-width: 450px;">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-info text-white text-center">
            <h4>Login</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-info w-100 text-white">Login</button>
            </form>
            <div class="text-center mt-3">
                <small>Don’t have an account? <a href="{{ route('register') }}">Register here</a></small>
            </div>
        </div>
    </div>
</div>
@endsection
