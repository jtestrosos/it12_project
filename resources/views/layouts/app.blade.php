<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Malasakit</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Admin Dashboard Layout */
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .admin-sidebar {
            width: 250px;
            background: #007bff;
            color: white;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            overflow-y: auto;
        }
        
        .admin-sidebar .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .admin-sidebar .sidebar-header h4 {
            color: white;
            margin: 0;
            font-weight: 600;
        }
        
        .admin-sidebar .sidebar-header p {
            color: rgba(255,255,255,0.8);
            margin: 0;
            font-size: 0.9rem;
        }
        
        .admin-sidebar .nav-link {
            color: white;
            padding: 1rem 1.5rem;
            border: none;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .admin-sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .admin-sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        
        .admin-sidebar .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
        }
        
        /* Main Content */
        .admin-main {
            flex: 1;
            margin-left: 250px;
            background: #f8f9fa;
        }
        
        /* Top Header */
        .admin-header {
            background: #17a2b8;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
            color: white;
            margin: 0 1rem;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background 0.3s ease;
        }
        
        .admin-header .header-nav .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
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
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            font-weight: 600;
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
            padding: 2rem;
        }
        
        /* Public Layout (Non-Admin) */
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
                        <i class="fas fa-shield-alt me-2"></i>
                        <div>
                            <h4>Barangay Health Center</h4>
                            <p>Staff Management System</p>
                        </div>
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
                <!-- Top Header -->
                <div class="admin-header">
                    <div class="header-left">
                        <div class="logo">
                            <i class="fas fa-plus"></i>
                            <h3>MALASAKIT</h3>
                        </div>
                        <nav class="header-nav">
                            <a class="nav-link" href="{{ url('/') }}">Home</a>
                            <a class="nav-link" href="{{ url('/policy') }}">Booking Policy</a>
                            <a class="nav-link" href="{{ url('/contact') }}">Contact Us!</a>
                            @if(Auth::user()->isSuperAdmin())
                                <a class="nav-link" href="{{ route('superadmin.dashboard') }}">Super Admin</a>
                            @else
                                <a class="nav-link" href="{{ route('patient.dashboard') }}">Dashboard</a>
                            @endif
                        </nav>
                    </div>
                    <div class="header-right">
                        <i class="fas fa-bell me-3"></i>
                        <div class="user-info">
                            <div class="user-avatar">{{ Auth::user()->isSuperAdmin() ? 'SA' : 'P' }}</div>
                            <div class="user-details">
                                <h6>{{ Auth::user()->isSuperAdmin() ? 'Super Admin' : 'Patient' }}</h6>
                                <small>{{ Auth::user()->isSuperAdmin() ? 'Administrator' : 'User' }}</small>
                            </div>
                        </div>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="ms-3">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </div>

                <!-- Content -->
                <div class="admin-content">
                    @yield('content')
                </div>
            </div>
        </div>
        
        <!-- Logout Form for Admin -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
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
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('/') ? 'active fw-bold' : '' }}" href="{{ url('/') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('policy') ? 'active fw-bold' : '' }}" href="{{ url('/policy') }}">Booking Policy</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('contact') ? 'active fw-bold' : '' }}" href="{{ url('/contact') }}">Contact Us!</a>
                        </li>

                        @guest
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a>
                        </li>
                        @else
                        @if(Auth::user()->isSuperAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('superadmin.dashboard') }}">Super Admin</a>
                        </li>
                        @elseif(Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">Admin</a>
                        </li>
                        @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('patient.dashboard') }}">Dashboard</a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                            <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                        </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>
    @endif

    <footer class="text-center text-muted py-4 mt-auto">
        <div class="container small">
            Barangay Health Clinic &copy; {{ date('Y') }}. All rights reserved.
            <span class="mx-2">|</span>
            <a href="{{ url('/privacy') }}" class="text-decoration-none text-muted">Privacy Policy</a>
            <span class="mx-2">|</span>
            <a href="{{ url('/terms') }}" class="text-decoration-none text-muted">Terms of Service</a>
        </div>
    </footer>

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
                        <small>Don’t have an account? 
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
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Barangay</label>
                            <select name="barangay" class="form-control" required>
                                <option value="">Select Barangay</option>
                                <option value="Barangay 1">Barangay 1</option>
                                <option value="Barangay 2">Barangay 2</option>
                                <option value="Barangay 3">Barangay 3</option>
                                <option value="Barangay 4">Barangay 4</option>
                                <option value="Barangay 5">Barangay 5</option>
                                <option value="Barangay 6">Barangay 6</option>
                                <option value="Barangay 7">Barangay 7</option>
                                <option value="Barangay 8">Barangay 8</option>
                                <option value="Barangay 9">Barangay 9</option>
                                <option value="Barangay 10">Barangay 10</option>
                                <option value="Barangay 11">Barangay 11</option>
                                <option value="Barangay 12">Barangay 12</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
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
    </script>
</body>
</html>
