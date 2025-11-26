@extends('layouts.admin')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('sidebar-menu')
    @if(Auth::user()->isSuperAdmin())
        <a class="nav-link" href="{{ route('superadmin.dashboard') }}" data-tooltip="Dashboard">
            <i class="fas fa-th-large"></i> <span class="sidebar-content">Dashboard</span>
        </a>
        <a class="nav-link" href="{{ route('superadmin.users') }}" data-tooltip="User Management">
            <i class="fas fa-user"></i> <span class="sidebar-content">User Management</span>
        </a>
        <a class="nav-link" href="{{ route('superadmin.system-logs') }}" data-tooltip="System Logs">
            <i class="fas fa-list"></i> <span class="sidebar-content">System Logs</span>
        </a>
        <a class="nav-link" href="{{ route('superadmin.analytics') }}" data-tooltip="Analytics">
            <i class="fas fa-chart-bar"></i> <span class="sidebar-content">Analytics</span>
        </a>
        <a class="nav-link" href="{{ route('superadmin.backup') }}" data-tooltip="Backup">
            <i class="fas fa-download"></i> <span class="sidebar-content">Backup</span>
        </a>
    @elseif(Auth::user()->isAdmin())
        <a class="nav-link" href="{{ route('admin.dashboard') }}" data-tooltip="Dashboard">
            <i class="fas fa-th-large"></i> <span class="sidebar-content">Dashboard</span>
        </a>
        <a class="nav-link" href="{{ route('admin.patients') }}" data-tooltip="Patient Management">
            <i class="fas fa-user"></i> <span class="sidebar-content">Patient Management</span>
        </a>
        <a class="nav-link" href="{{ route('admin.appointments') }}" data-tooltip="Appointments">
            <i class="fas fa-calendar-check"></i> <span class="sidebar-content">Appointments</span>
        </a>
        <a class="nav-link" href="{{ route('admin.reports') }}" data-tooltip="Services & Reports">
            <i class="fas fa-chart-bar"></i> <span class="sidebar-content">Services & Reports</span>
        </a>
        <a class="nav-link" href="{{ route('admin.inventory') }}" data-tooltip="Inventory">
            <i class="fas fa-box"></i> <span class="sidebar-content">Inventory</span>
        </a>
    @else
        <a class="nav-link" href="{{ route('patient.dashboard') }}" data-tooltip="Dashboard">
            <i class="fas fa-th-large"></i> <span class="sidebar-content">Dashboard</span>
        </a>
        <a class="nav-link" href="{{ route('patient.appointments') }}" data-tooltip="My Appointments">
            <i class="fas fa-calendar"></i> <span class="sidebar-content">My Appointments</span>
        </a>
        <a class="nav-link" href="{{ route('patient.book-appointment') }}" data-tooltip="Book Appointment">
            <i class="fas fa-plus"></i> <span class="sidebar-content">Book Appointment</span>
        </a>
    @endif
@endsection

@section('user-initials')
    @if(Auth::user()->isSuperAdmin())
        SA
    @else
        {{ substr(Auth::user()->name, 0, 2) }}
    @endif
@endsection

@section('user-name')
    @if(Auth::user()->isSuperAdmin())
        Super Admin
    @else
        {{ Auth::user()->name }}
    @endif
@endsection

@section('user-role')
    @if(Auth::user()->isSuperAdmin())
        Administrator
    @elseif(Auth::user()->isAdmin())
        {{ ucfirst(Auth::user()->role) }}
    @else
        Patient
    @endif
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-surface shadow-sm border-0">
            <div class="card-body p-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            @if($user->profile_picture)
                                <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture" class="rounded-circle object-fit-cover" style="width: 120px; height: 120px; border: 3px solid #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                            @else
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px; font-size: 3rem; border: 3px solid #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                            <label for="profile_picture" class="position-absolute bottom-0 end-0 bg-white rounded-circle shadow-sm p-2" style="cursor: pointer;">
                                <i class="fas fa-camera text-primary"></i>
                                <input type="file" name="profile_picture" id="profile_picture" class="d-none" accept="image/*" onchange="this.form.submit()">
                            </label>
                        </div>
                        <h4 class="mt-3 mb-1">{{ $user->name }}</h4>
                        <p class="text-muted">{{ ucfirst($user->role) }}</p>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3">Personal Information</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" placeholder="09123456789">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', $user->date_of_birth) }}">
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea name="address" id="address" rows="3" class="form-control @error('address') is-invalid @enderror" placeholder="Enter your full address">{{ old('address', $user->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3">Change Password</h5>
                    <p class="text-muted small mb-3">Leave the password fields blank if you don't want to change your password.</p>
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <div class="input-group">
                            <input type="password" name="current_password" id="current-password" class="form-control @error('current_password') is-invalid @enderror">
                            <button class="btn btn-outline-secondary" type="button" id="current-show-password-btn">
                                <i id="current-show-password-icon" class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Required if you want to change your password.</div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="profile-password" class="form-control @error('password') is-invalid @enderror">
                            <button class="btn btn-outline-secondary" type="button" id="profile-show-password-btn">
                                <i id="profile-show-password-icon" class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Must be at least 8 characters long.</div>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="profile-password-confirm" class="form-control">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Current password toggle
        const currentPasswordInput = document.getElementById('current-password');
        const currentToggleBtn = document.getElementById('current-show-password-btn');
        const currentToggleIcon = document.getElementById('current-show-password-icon');

        if (currentToggleBtn && currentPasswordInput) {
            currentToggleBtn.addEventListener('click', function() {
                const showing = currentPasswordInput.type === 'text';
                currentPasswordInput.type = showing ? 'password' : 'text';
                
                if (currentToggleIcon) {
                    currentToggleIcon.classList.toggle('fa-eye');
                    currentToggleIcon.classList.toggle('fa-eye-slash');
                }
            });
        }

        // New password toggle
        const passwordInput = document.getElementById('profile-password');
        const passwordConfirmInput = document.getElementById('profile-password-confirm');
        const toggleBtn = document.getElementById('profile-show-password-btn');
        const toggleIcon = document.getElementById('profile-show-password-icon');

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
