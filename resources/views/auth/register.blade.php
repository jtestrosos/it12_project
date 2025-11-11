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
                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Name should not contain numbers</small>
                </div>
                <div class="mb-3">
                    <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                    <select name="gender" class="form-control @error('gender') is-invalid @enderror" required>
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number <small class="text-muted">(Optional - can be filled when booking appointments)</small></label>
                    <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="Enter your phone number (optional)">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address <small class="text-muted">(Optional - can be filled when booking appointments)</small></label>
                    <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" placeholder="Enter your complete address (optional)">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="barangay" class="form-label">Barangay <span class="text-danger">*</span></label>
                    <select name="barangay" class="form-control @error('barangay') is-invalid @enderror" required>
                        <option value="">Select Barangay</option>
                        <option value="Barangay 1" {{ old('barangay') == 'Barangay 1' ? 'selected' : '' }}>Barangay 1</option>
                        <option value="Barangay 2" {{ old('barangay') == 'Barangay 2' ? 'selected' : '' }}>Barangay 2</option>
                        <option value="Barangay 3" {{ old('barangay') == 'Barangay 3' ? 'selected' : '' }}>Barangay 3</option>
                        <option value="Barangay 4" {{ old('barangay') == 'Barangay 4' ? 'selected' : '' }}>Barangay 4</option>
                        <option value="Barangay 5" {{ old('barangay') == 'Barangay 5' ? 'selected' : '' }}>Barangay 5</option>
                        <option value="Barangay 6" {{ old('barangay') == 'Barangay 6' ? 'selected' : '' }}>Barangay 6</option>
                        <option value="Barangay 7" {{ old('barangay') == 'Barangay 7' ? 'selected' : '' }}>Barangay 7</option>
                        <option value="Barangay 8" {{ old('barangay') == 'Barangay 8' ? 'selected' : '' }}>Barangay 8</option>
                        <option value="Barangay 9" {{ old('barangay') == 'Barangay 9' ? 'selected' : '' }}>Barangay 9</option>
                        <option value="Barangay 10" {{ old('barangay') == 'Barangay 10' ? 'selected' : '' }}>Barangay 10</option>
                        <option value="Barangay 11" {{ old('barangay') == 'Barangay 11' ? 'selected' : '' }}>Barangay 11</option>
                        <option value="Barangay 12" {{ old('barangay') == 'Barangay 12' ? 'selected' : '' }}>Barangay 12</option>
                    </select>
                    @error('barangay')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Password must contain at least one lowercase letter, one uppercase letter, and one special character</small>
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" required>
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
