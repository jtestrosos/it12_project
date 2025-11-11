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
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
            transition: transform 0.3s ease-in-out;
            overflow-y: auto;
            height: 100vh;
        }
        .sidebar.collapsed {
            transform: translateX(-100%);
        }
        @media (min-width: 992px) {
            .sidebar:not(.collapsed) {
                transform: translateX(0);
            }
            .sidebar.collapsed {
                transform: translateX(-100%);
            }
        }
        @media (max-width: 991px) {
            .sidebar:not(.show) {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
        }
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            transition: opacity 0.3s ease;
        }
        .sidebar-overlay.show {
            display: block;
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
        .burger-menu-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #495057;
            cursor: pointer;
            padding: 0.5rem;
            margin-right: 1rem;
            transition: color 0.3s ease;
        }
        .burger-menu-btn:hover {
            color: #0d6efd;
        }
        .main-content {
            background-color: #f0f0f0;
            min-height: 100vh;
            transition: margin-left 0.3s ease-in-out;
            margin-left: 0;
        }
        @media (min-width: 992px) {
            .main-content {
                margin-left: 250px;
            }
            .main-content.sidebar-closed {
                margin-left: 0;
            }
        }
        @media (max-width: 991px) {
            .main-content {
                margin-left: 0;
            }
        }
        .header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        @media (max-width: 576px) {
            .header {
                padding: 1rem;
            }
            .header h4 {
                font-size: 1.1rem;
            }
            .header .text-muted {
                font-size: 0.85rem;
            }
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
    <!-- Sidebar Overlay (for mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="p-3">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('images/malasakit-logo-blue.png') }}" alt="Logo" class="me-3" style="width: 52px; height: 52px;">
                    <div>
                        <h6 class="mb-0 fw-bold" style="letter-spacing:.5px;">MALASAKIT</h6>
                    </div>
                </div>
                <button class="burger-menu-btn d-lg-none" id="closeSidebarBtn" aria-label="Close sidebar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <nav class="nav flex-column">
                @yield('sidebar-menu')
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Header -->
        <div class="header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="burger-menu-btn" id="toggleSidebarBtn" aria-label="Toggle sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h4 class="mb-0">@yield('page-title', 'Dashboard')</h4>
                    <p class="text-muted mb-0">@yield('page-description', 'Welcome!')</p>
                </div>
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

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle functionality
        (function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleBtn = document.getElementById('toggleSidebarBtn');
            const closeBtn = document.getElementById('closeSidebarBtn');
            const overlay = document.getElementById('sidebarOverlay');
            const sidebarKey = 'sidebar-state';
            
            // Check if sidebar should be open by default (desktop) or closed (mobile)
            function isDesktop() {
                return window.innerWidth >= 992;
            }
            
            // Initialize sidebar state
            function initSidebar() {
                if (isDesktop()) {
                    // Desktop: check localStorage, default to open
                    const savedState = localStorage.getItem(sidebarKey);
                    if (savedState === 'closed') {
                        sidebar.classList.add('collapsed');
                        sidebar.classList.remove('show');
                        mainContent.classList.add('sidebar-closed');
                    } else {
                        sidebar.classList.remove('collapsed');
                        sidebar.classList.add('show');
                        mainContent.classList.remove('sidebar-closed');
                        // Only set localStorage if it wasn't already set to avoid overwriting user preference
                        if (!savedState) {
                            localStorage.setItem(sidebarKey, 'open');
                        }
                    }
                } else {
                    // Mobile: always start closed
                    sidebar.classList.add('collapsed');
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                }
            }
            
            function openSidebar() {
                sidebar.classList.remove('collapsed');
                sidebar.classList.add('show');
                if (isDesktop()) {
                    mainContent.classList.remove('sidebar-closed');
                    localStorage.setItem(sidebarKey, 'open');
                } else {
                    overlay.classList.add('show');
                }
            }
            
            function closeSidebar() {
                sidebar.classList.add('collapsed');
                sidebar.classList.remove('show');
                if (isDesktop()) {
                    mainContent.classList.add('sidebar-closed');
                    localStorage.setItem(sidebarKey, 'closed');
                } else {
                    overlay.classList.remove('show');
                }
            }
            
            function toggleSidebar() {
                const isCollapsed = sidebar.classList.contains('collapsed');
                const isNotShowing = !sidebar.classList.contains('show');
                
                if (isCollapsed || isNotShowing) {
                    openSidebar();
                } else {
                    closeSidebar();
                }
            }
            
            // Event listeners
            if (toggleBtn) {
                toggleBtn.addEventListener('click', toggleSidebar);
            }
            
            if (closeBtn) {
                closeBtn.addEventListener('click', closeSidebar);
            }
            
            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }
            
            // Handle window resize
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (isDesktop()) {
                        // Desktop: restore saved state
                        const savedState = localStorage.getItem(sidebarKey);
                        if (savedState === 'closed') {
                            sidebar.classList.add('collapsed');
                            sidebar.classList.remove('show');
                            mainContent.classList.add('sidebar-closed');
                        } else {
                            sidebar.classList.remove('collapsed');
                            sidebar.classList.add('show');
                            mainContent.classList.remove('sidebar-closed');
                        }
                    } else {
                        // Mobile: always close on resize to mobile
                        sidebar.classList.add('collapsed');
                        sidebar.classList.remove('show');
                        overlay.classList.remove('show');
                        mainContent.classList.remove('sidebar-closed');
                    }
                }, 250);
            });
            
            // Initialize on page load
            initSidebar();
        })();
        
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
        body.bg-dark .sidebar-overlay { background: rgba(0, 0, 0, 0.7); }
        body.bg-dark .burger-menu-btn { color: #cbd3da; }
        body.bg-dark .burger-menu-btn:hover { color: #6ea8fe; }
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

