<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Malasakit</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden; /* ✅ Prevents horizontal scroll */
        }

        /* Admin Dashboard Layout */
        .admin-layout {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* Sidebar */
        .admin-sidebar {
            width: 250px;
            background: #f8f9fa;
            color: #495057;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            overflow-y: auto;
            border-right: 1px solid #e9ecef;
        }

        .admin-sidebar .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .admin-sidebar .sidebar-header h4 {
            color: #2c3e50;
            margin: 0;
            font-weight: 600;
        }

        .admin-sidebar .sidebar-header p {
            color: #6c757d;
            margin: 0;
            font-size: 0.9rem;
        }

        .admin-sidebar .nav-link {
            color: #495057;
            padding: 1rem 1.5rem;
            border: none;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .admin-sidebar .nav-link:hover {
            background: #e9ecef;
            color: #495057;
        }

        .admin-sidebar .nav-link.active {
            background: #007bff;
            color: #ffffff;
        }

        .admin-sidebar .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
        }

        /* Main Content */
        .admin-main {
            flex: 1;
            margin-left: 250px;
            background: #f0f0f0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: calc(100% - 250px); /* ✅ Ensures full width minus sidebar */
            overflow-x: hidden; /* ✅ Prevents right blank space */
        }

        /* Top Header */
        .admin-header {
            background: #ffffff;
            color: #212529;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e9ecef;
            box-shadow: none;
            width: 100%; /* ✅ Fix header stretch */
        }

        .admin-header .header-left {
            display: flex;
            align-items: center;
        }

        .admin-header .header-left .logo {
            display: flex;
            align-items: center;
            margin-right: 2rem;
        }

        .admin-header .header-left .logo i {
            font-size: 1.5rem;
            margin-right: 0.5rem;
        }

        .admin-header .header-left h3 {
            margin: 0;
            font-weight: 600;
        }

        .admin-header .header-nav {
            display: flex;
            align-items: center;
        }

        .admin-header .header-nav .nav-link {
            color: #495057;
            margin: 0 1rem;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background 0.3s ease;
        }

        .admin-header .header-nav .nav-link:hover {
            background: #f8f9fa;
            color: #212529;
        }

        .admin-header .header-right {
            display: flex;
            align-items: center;
        }

        .admin-header .header-right .user-info {
            display: flex;
            align-items: center;
            margin-left: 1rem;
        }

        .admin-header .header-right .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #0d6efd;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            font-weight: 600;
            color: #ffffff;
        }

        .admin-header .header-right .user-details h6 {
            margin: 0;
            font-weight: 600;
        }

        .admin-header .header-right .user-details small {
            opacity: 0.8;
        }

        /* Content Area */
        .admin-content {
            padding: 0;
            flex: 1;
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 100%;
        }

        /* Public Layout */
        .public-layout .navbar {
            background: #17a2b8;
            min-height: 80px;
        }

        .public-layout .navbar-brand img {
            height: 40px;
            margin-right: 1rem;
        }

        .public-layout .navbar-brand span {
            font-size: 1.5rem;
            font-weight: 600;
            color: white;
        }

        .public-layout .navbar-nav .nav-link {
            color: white;
            font-weight: 500;
            margin: 0 1rem;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background 0.3s ease;
        }

        .public-layout .navbar-nav .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .public-layout .navbar-nav .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .admin-sidebar.show {
                transform: translateX(0);
            }

            .admin-main {
                margin-left: 0;
                width: 100%;
            }

            .admin-header {
                padding: 1rem;
            }

            .admin-content {
                padding: 1rem;
            }
        }

        footer {
            background: #e9ecef;
            color: #6c757d;
        }
    </style>

    @stack('styles')
</head>

<body class="{{ isset($adminLayout) && $adminLayout ? 'admin-layout' : 'public-layout' }}">
    @if(isset($adminLayout) && $adminLayout)
        <!-- Admin Layout -->
        <div class="admin-layout">
            <!-- Sidebar -->
            <div class="admin-sidebar">
                <div class="sidebar-header">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('images/malasakit-logo-blue.png') }}" alt="MALASAKIT Logo" style="width: 90px; height: 90px; margin-right: 0.5rem;">
                        <div><h4>MALASAKIT</h4></div>
                    </div>
                </div>
                <nav class="nav flex-column">
                    @if(Auth::user()->isSuperAdmin())
                        <a class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}" href="{{ route('superadmin.dashboard') }}">
                            <i class="fas fa-th-large"></i> Dashboard
                        </a>
                        <a class="nav-link {{ request()->routeIs('superadmin.users') ? 'active' : '' }}" href="{{ route('superadmin.users') }}">
                            <i class="fas fa-users"></i> Patient Management
                        </a>
                        <a class="nav-link {{ request()->routeIs('superadmin.system-logs') ? 'active' : '' }}" href="{{ route('superadmin.system-logs') }}">
                            <i class="fas fa-calendar-check"></i> Appointments
                        </a>
                        <a class="nav-link {{ request()->routeIs('superadmin.analytics') ? 'active' : '' }}" href="{{ route('superadmin.analytics') }}">
                            <i class="fas fa-list"></i> Services & Reports
                        </a>
                        <a class="nav-link" href="#">
                            <i class="fas fa-boxes"></i> Inventory
                        </a>
                    @else
                        <a class="nav-link {{ request()->routeIs('patient.dashboard') ? 'active' : '' }}" href="{{ route('patient.dashboard') }}">
                            <i class="fas fa-th-large"></i> Dashboard
                        </a>
                        <a class="nav-link {{ request()->routeIs('patient.appointments') ? 'active' : '' }}" href="{{ route('patient.appointments') }}">
                            <i class="fas fa-calendar"></i> My Appointments
                        </a>
                        <a class="nav-link {{ request()->routeIs('patient.book-appointment') ? 'active' : '' }}" href="{{ route('patient.book-appointment') }}">
                            <i class="fas fa-plus"></i> Book Appointment
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Main Content -->
            <div class="admin-main">
                <!-- Header -->
                <div class="admin-header">
                    <div class="header-left">
                        <div class="logo"><h3></h3></div>
                    </div>
                    <div class="header-right">
                        <i class="fas fa-bell me-3"></i>
                        <div class="user-info">
                            <div class="user-avatar">{{ Auth::user()->isSuperAdmin() ? 'SA' : 'P' }}</div>
                            <div class="user-details">
                                <h6>{{ Auth::user()->name }}</h6>
                                <small>{{ Auth::user()->isSuperAdmin() ? 'Super Admin' : 'Patient' }}</small>
                            </div>
                        </div>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-outline-secondary ms-3" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </div>

                <!-- Content -->
                <div class="admin-content">
                    @yield('content')
                </div>

                <!-- Footer -->
                <div class="text-center text-muted py-4 mt-auto border-top">
                    <div class="container small">
                        Barangay Health Clinic &copy; {{ date('Y') }}. All rights reserved.
                        <span class="mx-2">|</span>
                        <a href="{{ url('/privacy') }}" class="text-decoration-none text-muted">Privacy Policy</a>
                        <span class="mx-2">|</span>
                        <a href="{{ url('/terms') }}" class="text-decoration-none text-muted">Terms of Service</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logout Form -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    @else
        <!-- Public Layout -->
        <header>
            <nav class="navbar navbar-expand-lg navbar-light shadow-sm">
                <div class="container">
                    <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                        <img src="{{ asset('images/malasakit-logo.png') }}" alt="Malasakit Logo">
                        <span class="fw-bold text-white">MALASAKIT</span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="mainNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item"><a class="nav-link {{ request()->is('/') ? 'active fw-bold' : '' }}" href="{{ url('/') }}">Home</a></li>
                            <li class="nav-item"><a class="nav-link {{ request()->is('policy') ? 'active fw-bold' : '' }}" href="{{ url('/policy') }}">Booking Policy</a></li>
                            <li class="nav-item"><a class="nav-link {{ request()->is('contact') ? 'active fw-bold' : '' }}" href="{{ url('/contact') }}">Contact Us!</a></li>

                            @guest
                                <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a></li>
                            @else
                                @if(Auth::user()->isSuperAdmin())
                                    <li class="nav-item"><a class="nav-link" href="{{ route('superadmin.dashboard') }}">Super Admin</a></li>
                                @elseif(Auth::user()->isAdmin())
                                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Admin</a></li>
                                @else
                                    <li class="nav-item"><a class="nav-link" href="{{ route('patient.dashboard') }}">Dashboard</a></li>
                                @endif
                                <li class="nav-item">
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                                    <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

        <main>@yield('content')</main>
    @endif

    @unless(isset($adminLayout) && $adminLayout)
        <footer class="text-center text-muted py-4 mt-auto">
            <div class="container small">
                Barangay Health Clinic &copy; {{ date('Y') }}. All rights reserved.
                <span class="mx-2">|</span>
                <a href="{{ url('/privacy') }}" class="text-decoration-none text-muted">Privacy Policy</a>
                <span class="mx-2">|</span>
                <a href="{{ url('/terms') }}" class="text-decoration-none text-muted">Terms of Service</a>
            </div>
        </footer>
    @endunless

    @php
        $flashType = null;
        $flashMessage = null;

        if (session('success')) {
            $flashType = 'success';
            $flashMessage = session('success');
        } elseif (session('status')) {
            $flashType = 'success';
            $flashMessage = session('status');
        } elseif (session('error')) {
            $flashType = 'danger';
            $flashMessage = session('error');
        } elseif (session('warning')) {
            $flashType = 'warning';
            $flashMessage = session('warning');
        } elseif (session('info')) {
            $flashType = 'info';
            $flashMessage = session('info');
        }

        $flashTitleMap = [
            'success' => 'Success',
            'danger' => 'Something went wrong',
            'warning' => 'Heads up',
            'info' => 'Notice',
        ];

        $flashIconMap = [
            'success' => 'fa-circle-check',
            'danger' => 'fa-triangle-exclamation',
            'warning' => 'fa-circle-exclamation',
            'info' => 'fa-circle-info',
        ];
    @endphp

    @if($flashType && $flashMessage)
        <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-{{ $flashType }} text-white">
                        <h5 class="modal-title d-flex align-items-center gap-2" id="feedbackModalLabel">
                            <i class="fas {{ $flashIconMap[$flashType] ?? 'fa-circle-info' }}"></i>
                            {{ $flashTitleMap[$flashType] ?? 'Notice' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4">
                        <p class="mb-0">{{ $flashMessage }}</p>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-{{ $flashType === 'danger' ? 'danger' : 'primary' }}" data-bs-dismiss="modal">Got it</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="loginModalLabel">Login to Continue</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" name="email" class="form-control" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-info w-100 text-white">Login</button>
                    </form>
                    <div class="text-center mt-3">
                        <small>Don't have an account? 
                            <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#registerModal">Register</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="registerModalLabel">Register an Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @php
                        $selectedBarangayModal = old('barangay');
                        $purokOptionsModal = match ($selectedBarangayModal) {
                            'Barangay 11' => ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'],
                            'Barangay 12' => ['Purok 1', 'Purok 2', 'Purok 3'],
                            default => [],
                        };
                    @endphp
                    <form method="POST" action="{{ route('register') }}" class="registration-form">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
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
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
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
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="Enter your phone number">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Barangay <span class="text-danger">*</span></label>
                            <select name="barangay" class="form-control @error('barangay') is-invalid @enderror" data-role="barangay" required>
                                <option value="">Select Barangay</option>
                                <option value="Barangay 11" {{ $selectedBarangayModal === 'Barangay 11' ? 'selected' : '' }}>Barangay 11</option>
                                <option value="Barangay 12" {{ $selectedBarangayModal === 'Barangay 12' ? 'selected' : '' }}>Barangay 12</option>
                                <option value="Other" {{ $selectedBarangayModal === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('barangay')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 {{ $selectedBarangayModal === 'Other' ? '' : 'd-none' }}" data-role="barangay-other-group">
                            <label class="form-label">Specify Barangay <span class="text-danger">*</span></label>
                            <input type="text" name="barangay_other" class="form-control @error('barangay_other') is-invalid @enderror" value="{{ old('barangay_other') }}" data-role="barangay-other">
                            @error('barangay_other')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 {{ in_array($selectedBarangayModal, ['Barangay 11', 'Barangay 12']) ? '' : 'd-none' }}" data-role="purok-group">
                            <label class="form-label">Purok <span class="text-danger">*</span></label>
                            <select name="purok" class="form-control @error('purok') is-invalid @enderror" data-role="purok" data-selected="{{ old('purok') }}">
                                <option value="">Select Purok</option>
                                @foreach ($purokOptionsModal as $purok)
                                    <option value="{{ $purok }}" {{ old('purok') === $purok ? 'selected' : '' }}>{{ $purok }}</option>
                                @endforeach
                            </select>
                            @error('purok')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Birth Date <span class="text-danger">*</span></label>
                            <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date') }}" data-role="birth-date" max="{{ now()->toDateString() }}" required>
                            @error('birth_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
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
                            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" required>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-info w-100 text-white">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function requireLogin() {
            @if(Auth::check())
                @if(Auth::user()->isPatient())
                    window.location.href = "{{ route('patient.book-appointment') }}";
                @else
                    window.location.href = "{{ Auth::user()->isSuperAdmin() ? route('superadmin.dashboard') : route('admin.dashboard') }}";
                @endif
            @else
                var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                loginModal.show();
            @endif
        }

        document.addEventListener('DOMContentLoaded', function () {
            const feedbackModalEl = document.getElementById('feedbackModal');
            if (feedbackModalEl) {
                const feedbackModal = new bootstrap.Modal(feedbackModalEl);
                feedbackModal.show();
            }

            const barangayPurokMap = {
                'Barangay 11': ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'],
                'Barangay 12': ['Purok 1', 'Purok 2', 'Purok 3'],
            };

            const registrationForms = document.querySelectorAll('.registration-form');

            registrationForms.forEach((form) => {
                const barangaySelect = form.querySelector('[data-role="barangay"]');
                const barangayOtherGroup = form.querySelector('[data-role="barangay-other-group"]');
                const barangayOtherInput = form.querySelector('[data-role="barangay-other"]');
                const purokGroup = form.querySelector('[data-role="purok-group"]');
                const purokSelect = form.querySelector('[data-role="purok"]');
                const birthDateInput = form.querySelector('[data-role="birth-date"]');

                const updatePurokOptions = (barangay) => {
                    if (!purokSelect) {
                        return;
                    }

                    const previouslySelected = purokSelect.getAttribute('data-selected');
                    purokSelect.innerHTML = '<option value="">Select Purok</option>';

                    if (!barangayPurokMap[barangay]) {
                        purokSelect.removeAttribute('required');
                        purokSelect.setAttribute('data-selected', '');
                        return;
                    }

                    barangayPurokMap[barangay].forEach((purok) => {
                        const option = document.createElement('option');
                        option.value = purok;
                        option.textContent = purok;
                        if (previouslySelected === purok) {
                            option.selected = true;
                        }
                        purokSelect.appendChild(option);
                    });
                    purokSelect.setAttribute('required', 'required');
                };

                const handleBarangayChange = () => {
                    const selectedBarangay = barangaySelect ? barangaySelect.value : '';

                    if (barangayOtherGroup && barangayOtherInput) {
                        if (selectedBarangay === 'Other') {
                            barangayOtherGroup.classList.remove('d-none');
                            barangayOtherInput.setAttribute('required', 'required');
                        } else {
                            barangayOtherGroup.classList.add('d-none');
                            barangayOtherInput.removeAttribute('required');
                        }
                    }

                    if (purokGroup && purokSelect) {
                        if (barangayPurokMap[selectedBarangay]) {
                            purokGroup.classList.remove('d-none');
                            updatePurokOptions(selectedBarangay);
                        } else {
                            purokGroup.classList.add('d-none');
                            purokSelect.removeAttribute('required');
                            purokSelect.value = '';
                            purokSelect.setAttribute('data-selected', '');
                        }
                    }
                };

                if (barangaySelect) {
                    barangaySelect.addEventListener('change', () => {
                        if (purokSelect) {
                            purokSelect.setAttribute('data-selected', '');
                        }
                        handleBarangayChange();
                    });
                    handleBarangayChange();
                }

                // No additional birth date handling needed; age is calculated server-side.
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
