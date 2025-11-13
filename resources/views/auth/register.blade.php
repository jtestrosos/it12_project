@extends('layouts.app')

@section('content')
<div class="container my-5" style="max-width: 450px;">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-info text-white text-center">
            <h4>Register</h4>
        </div>
        <div class="card-body">
            @php
                $selectedBarangay = old('barangay');
                $purokOptions = match ($selectedBarangay) {
                    'Barangay 11' => ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'],
                    'Barangay 12' => ['Purok 1', 'Purok 2', 'Purok 3'],
                    default => [],
                };
            @endphp
            <form method="POST" action="{{ route('register') }}" class="registration-form">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')
                        @if (str_contains($message, 'should not contain numbers'))
                            <div class="invalid-feedback">{{ $message }}</div>
                        @elseif ($message)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @endif
                    @enderror
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
                    <select name="barangay" class="form-control @error('barangay') is-invalid @enderror" data-role="barangay" required>
                        <option value="">Select Barangay</option>
                        <option value="Barangay 11" {{ $selectedBarangay === 'Barangay 11' ? 'selected' : '' }}>Barangay 11</option>
                        <option value="Barangay 12" {{ $selectedBarangay === 'Barangay 12' ? 'selected' : '' }}>Barangay 12</option>
                        <option value="Other" {{ $selectedBarangay === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('barangay')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3 {{ $selectedBarangay === 'Other' ? '' : 'd-none' }}" data-role="barangay-other-group">
                    <label for="barangay_other" class="form-label">Specify Barangay <span class="text-danger">*</span></label>
                    <input type="text" name="barangay_other" class="form-control @error('barangay_other') is-invalid @enderror" value="{{ old('barangay_other') }}" data-role="barangay-other">
                    @error('barangay_other')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3 {{ in_array($selectedBarangay, ['Barangay 11', 'Barangay 12']) ? '' : 'd-none' }}" data-role="purok-group">
                    <label for="purok" class="form-label">Purok <span class="text-danger">*</span></label>
                    <select name="purok" class="form-control @error('purok') is-invalid @enderror" data-role="purok" data-selected="{{ old('purok') }}">
                        <option value="">Select Purok</option>
                        @foreach ($purokOptions as $purok)
                            <option value="{{ $purok }}" {{ old('purok') === $purok ? 'selected' : '' }}>{{ $purok }}</option>
                        @endforeach
                    </select>
                    @error('purok')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="birth_date" class="form-label">Birth Date <span class="text-danger">*</span></label>
                    <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date') }}" data-role="birth-date" max="{{ now()->toDateString() }}" required>
                    @error('birth_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                        @if (str_contains($message, 'lowercase letter') || str_contains($message, 'uppercase letter') || str_contains($message, 'special character'))
                            <div class="invalid-feedback">{{ $message }}</div>
                        @elseif ($message)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @endif
                    @enderror
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
