<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - Barangay Health Center')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('styles')
    <style>
        /* Global text colors */
        body { color: #111; }
        body.bg-dark { color: #fff; }
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            background: #f8f9fa;
            min-height: 100vh;
            border-right: 1px solid #e9ecef;
        }
        .sidebar .nav-link {
            color: #495057;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 8px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(13,110,253,0.08);
            color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13,110,253,0.12) inset;
        }
        .sidebar .nav-link.active {
            background-color: #0d6efd;
            color: white;
            border-left: 3px solid #66b2ff;
        }
        .main-content {
            background-color: #f0f0f0;
            min-height: 100vh;
        }
        .header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 2rem;
        }
        .filter-card {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        .table-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .metric-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            border: none;
            transition: transform 0.2s ease;
        }
        .metric-card:hover {
            transform: translateY(-2px);
        }
        .chart-container {
            background: white;
            border-radius: 12px;
            padding: 1.75rem 1.5rem 1.5rem 1.5rem; /* extra top padding */
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        /* Button consistency */
        .btn { border-radius: 8px; font-weight: 600; }
        .btn i { margin-right: 0.35rem; }
        .btn-sm { padding: 0.375rem 0.75rem; font-size: 0.875rem; }
        .btn-outline-primary, .btn-primary, .btn-outline-danger, .btn-danger, .btn-outline-secondary {
            display: inline-flex; align-items: center; gap: 0.35rem;
        }
        .table .btn { height: 34px; }
        @yield('page-styles')
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 p-0">
                <div class="sidebar">
                    <div class="p-3">
                        <div class="d-flex align-items-center mb-4">
                            <img src="{{ asset('images/malasakit-logo-blue.png') }}" alt="Logo" class="me-3" style="width: 40px; height: 40px;">
                            <div>
                                <h6 class="mb-0 fw-bold">Barangay Health Center</h6>
                                <small class="text-muted">Staff Management System</small>
                            </div>
                        </div>
                        <nav class="nav flex-column">
                            @yield('sidebar-menu')
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-0">
                <div class="main-content">
                    <!-- Header -->
                    <div class="header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">@yield('page-title', 'Dashboard')</h4>
                            <p class="text-muted mb-0">@yield('page-description', 'Welcome!')</p>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <button class="btn btn-link text-decoration-none text-muted" title="Notifications" aria-label="Notifications">
                                <i class="fas fa-bell"></i>
                            </button>
                            <button class="btn btn-link text-decoration-none text-muted" id="themeToggle" title="Toggle theme" aria-label="Toggle theme">
                                <i class="fas fa-moon"></i>
                            </button>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    @yield('user-initials', 'A')
                                </div>
                                <div>
                                    <div class="fw-bold">@yield('user-name', 'Admin')</div>
                                    <small class="text-muted">@yield('user-role', 'Administrator')</small>
                                </div>
                            </div>
                            <a href="{{ route('logout') }}" class="btn btn-outline-secondary ms-3" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Theme persistence + toggle
        (function() {
            const body = document.body;
            const key = 'app-theme';
            const saved = localStorage.getItem(key);
            if (saved === 'dark') {
                body.classList.add('bg-dark','text-white');
            }
            const toggle = document.getElementById('themeToggle');
            if (toggle) {
                toggle.addEventListener('click', function () {
                    const isDark = body.classList.toggle('bg-dark');
                    body.classList.toggle('text-white', isDark);
                    localStorage.setItem(key, isDark ? 'dark' : 'light');
                });
            }
        })();
    </script>
    <style>
        /* Dark mode surfaces */
        /* Surface shades for hierarchy */
        body.bg-dark .main-content { background-color: #151718; }
        body.bg-dark .sidebar { background: #131516; border-right-color: #2a2f35; }
        body.bg-dark .header { background: #1b1e20; border-bottom-color: #2a2f35; }
        body.bg-dark .filter-card,
        body.bg-dark .table-card,
        body.bg-dark .metric-card,
        body.bg-dark .chart-container,
        body.bg-dark .card,
        body.bg-dark .modal-content { background: #1e2124; box-shadow: 0 2px 8px rgba(0,0,0,0.3); border-color: #2a2f35; color: #e6e6e6; }
        body.bg-dark .table thead, body.bg-dark .table-light { background: #1a1f24 !important; color: #e6e6e6; }
        body.bg-dark .table { color: #d6d6d6; }
        body.bg-dark .table tr { border-color: #2a2f35; }
        body.bg-dark .input-group-text { background: #1a1f24; color: #cbd3da; border-color: #2a2f35; }
        body.bg-dark .form-control, body.bg-dark .form-select, body.bg-dark textarea.form-control {
            background-color: #0f1316; color: #e6e6e6; border-color: #2a2f35;
        }
        body.bg-dark .form-control::placeholder { color: #9aa4ad; }
        body.bg-dark .text-muted, body.bg-dark small, body.bg-dark .metric-change { color: #b0b0b0 !important; }
        /* Avatar ring */
        body.bg-dark .header .bg-primary { outline: 2px solid rgba(255,255,255,0.08); outline-offset: 2px; }
        body.bg-dark .btn-outline-primary { color: #6ea8fe; border-color: #6ea8fe; }
        body.bg-dark .btn-outline-primary:hover { background: rgba(13,110,253,0.15); color: #9ec5fe; }
        body.bg-dark .btn-outline-danger { color: #ff8787; border-color: #ff8787; }
        body.bg-dark .btn-outline-danger:hover { background: rgba(220,53,69,0.15); color: #ffc2c2; }
        body.bg-dark .sidebar .nav-link { color: #cbd3da; }
        body.bg-dark .sidebar .nav-link:hover { background: #14181c; color: #e6e6e6; }
        body.bg-dark .sidebar .nav-link.active { background-color: #0d6efd; color: #fff; }
        /* Keep badges readable in dark */
        body.bg-dark .status-user { background-color: rgba(13,110,253,0.2); color: #9ec5fe; }
        body.bg-dark .status-admin { background-color: rgba(255,193,7,0.2); color: #ffe08a; }
        body.bg-dark .status-superadmin { background-color: rgba(220,53,69,0.2); color: #f5a3ab; }
        /* Headings and key numbers inherit theme color */
        .metric-number, .stat-number, h1, h2, h3, h4, h5, h6 { color: inherit; }
    </style>
    @stack('scripts')
</body>
</html>

