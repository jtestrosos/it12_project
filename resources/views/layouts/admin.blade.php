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
            background: white;
            min-height: 100vh;
            border-right: 1px solid #e9ecef;
            position: fixed;
            top: 0;
            left: 0;
            width: 240px;
            z-index: 1000;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            overflow-y: auto;
            height: 100vh;
            box-shadow: 2px 0 8px rgba(0,0,0,0.05);
        }
        .sidebar.no-transition {
            transition: none !important;
        }
        .sidebar.no-transition ~ .main-content {
            transition: none !important;
        }
        .sidebar.collapsed {
            width: 72px;
        }
        .sidebar.collapsed .sidebar-content {
            opacity: 0;
            pointer-events: none;
            width: 0;
            overflow: hidden;
        }
        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 12px 0;
            margin: 2px 8px;
            min-height: 40px;
        }
        .sidebar.collapsed .nav-link i {
            margin-right: 0;
            margin-left: 0;
        }
        .sidebar.collapsed .nav-link span {
            display: none;
        }
        .nav-link span {
            transition: opacity 0.2s ease;
        }
        @media (min-width: 992px) {
            .sidebar:not(.collapsed) {
                width: 240px;
            }
        }
        @media (max-width: 991px) {
            .sidebar {
                width: 240px;
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .sidebar.collapsed {
                width: 240px;
            }
        }
        .sidebar-content {
            transition: opacity 0.2s ease;
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
        .sidebar .nav {
            padding-top: 32px;
            margin-top: 0;
        }
        @media (min-width: 992px) {
            .sidebar .nav {
                padding-top: 80px;
                margin-top: 0;
            }
        }
        .sidebar .nav-link {
            color: #0f0f0f;
            padding: 12px 16px;
            border-radius: 10px;
            margin: 2px 8px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            text-decoration: none;
            font-size: 14px;
            font-weight: 400;
            position: relative;
        }
        .sidebar .nav-link i {
            margin-right: 16px;
            font-size: 20px;
            width: 24px;
            text-align: center;
            transition: margin 0.3s ease;
        }
        .sidebar .nav-link:hover {
            background-color: #f2f2f2;
            color: #0f0f0f;
        }
        .sidebar .nav-link.active {
            background-color: #f0f0f0;
            color: #0f0f0f;
            font-weight: 500;
        }
        .sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 24px;
            background-color: #ff0000;
            border-radius: 0 2px 2px 0;
        }
        .sidebar.collapsed .nav-link {
            position: relative;
        }
        .sidebar.collapsed .nav-link:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 100%;
            margin-left: 12px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            white-space: nowrap;
            z-index: 1001;
            font-size: 12px;
            pointer-events: none;
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
            background-color: #f9f9f9;
            min-height: 100vh;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-left: 0;
        }
        .main-content.no-transition {
            transition: none !important;
        }
        @media (min-width: 992px) {
            .main-content {
                margin-left: 240px;
            }
            .main-content.sidebar-closed {
                margin-left: 72px;
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
            min-height: 64px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .header h4 {
            font-size: 1.25rem;
            font-weight: 500;
        }
        .header .text-muted {
            font-size: 0.875rem;
        }
        .header img {
            flex-shrink: 0;
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
            .header img {
                width: 32px !important;
                height: 32px !important;
                margin-right: 8px !important;
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
            <div class="d-flex align-items-center justify-content-end mb-3 d-lg-none" style="padding: 8px 4px;">
                <button class="burger-menu-btn" id="closeSidebarBtn" aria-label="Close sidebar" style="margin: 0;">
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
                <div class="d-flex align-items-center ms-3">
                    <img src="{{ asset('images/malasakit-logo-blue.png') }}" alt="Logo" style="width: 40px; height: 40px; margin-right: 12px;">
                    <div>
                        <h4 class="mb-0">@yield('page-title', 'Dashboard')</h4>
                        <p class="text-muted mb-0">@yield('page-description', 'Welcome!')</p>
                    </div>
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
        <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
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
            const sidebarKey = 'sidebar-collapsed';
            
            // Check if sidebar should be open by default (desktop) or closed (mobile)
            function isDesktop() {
                return window.innerWidth >= 992;
            }
            
            // Initialize sidebar state
function initSidebar() {
    // Disable transitions during initialization
    sidebar.classList.add('no-transition');
    mainContent.classList.add('no-transition');

    if (isDesktop()) {
        // Desktop: DEFAULT TO COLLAPSED (unless user previously expanded it)
        const saved = localStorage.getItem(sidebarKey);
        const shouldCollapse = saved === null || saved === 'true'; // null = first visit → collapse

        if (shouldCollapse) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('sidebar-closed');
            localStorage.setItem(sidebarKey, 'true'); // save default
        } else {
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('sidebar-closed');
        }
        sidebar.classList.add('show');
    } else {
        // Mobile: always start hidden
        sidebar.classList.remove('collapsed');
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
    }

    // Re-enable transitions
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            sidebar.classList.remove('no-transition');
            mainContent.classList.remove('no-transition');
        });
    });
}
            
            function toggleCollapse() {
                if (!isDesktop()) return;
                
                const isCollapsed = sidebar.classList.contains('collapsed');
                if (isCollapsed) {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('sidebar-closed');
                    localStorage.setItem(sidebarKey, 'false');
                } else {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('sidebar-closed');
                    localStorage.setItem(sidebarKey, 'true');
                }
            }
            
            function openSidebar() {
                sidebar.classList.add('show');
                if (!isDesktop()) {
                    overlay.classList.add('show');
                }
            }
            
            function closeSidebar() {
                if (isDesktop()) {
                    // On desktop, just collapse it
                    toggleCollapse();
                } else {
                    // On mobile, hide it
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                }
            }
            
            function toggleSidebar() {
                if (isDesktop()) {
                    toggleCollapse();
                } else {
                    const isShowing = sidebar.classList.contains('show');
                    if (isShowing) {
                        closeSidebar();
                    } else {
                        openSidebar();
                    }
                }
            }
            
            // Event listeners
            if (toggleBtn) {
                toggleBtn.addEventListener('click', toggleSidebar);
            }
            
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                });
            }
            
            if (overlay) {
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                });
            }
            
            // Handle window resize
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (isDesktop()) {
                        // Desktop: restore collapsed state
                        const isCollapsed = localStorage.getItem(sidebarKey) === 'true';
                        if (isCollapsed) {
                            sidebar.classList.add('collapsed');
                            mainContent.classList.add('sidebar-closed');
                        } else {
                            sidebar.classList.remove('collapsed');
                            mainContent.classList.remove('sidebar-closed');
                        }
                        sidebar.classList.add('show');
                        overlay.classList.remove('show');
                    } else {
                        // Mobile: always hide on resize to mobile
                        sidebar.classList.remove('collapsed');
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

        const barangayPurokMap = {
            'Barangay 11': ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'],
            'Barangay 12': ['Purok 1', 'Purok 2', 'Purok 3'],
        };

        function initPatientRegistrationForms() {
            const forms = document.querySelectorAll('.patient-registration-form');

            forms.forEach((form) => {
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
                            barangayOtherInput.value = '';
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

                if (birthDateInput) {
                    birthDateInput.addEventListener('change', () => {});
                    birthDateInput.addEventListener('keyup', () => {});
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const feedbackModalEl = document.getElementById('feedbackModal');
            if (feedbackModalEl) {
                const feedbackModal = new bootstrap.Modal(feedbackModalEl);
                feedbackModal.show();
            }

            initPatientRegistrationForms();
        });
    </script>
    <style>
        /* Dark mode surfaces */
        /* Surface shades for hierarchy */
        body.bg-dark .main-content { background-color: #151718; }
        body.bg-dark .sidebar { background: #212121; border-right-color: #303030; }
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
        body.bg-dark .sidebar .nav-link { color: #f1f1f1; }
        body.bg-dark .sidebar .nav-link:hover { background: #303030; color: #fff; }
        body.bg-dark .sidebar .nav-link.active { background-color: #303030; color: #fff; }
        body.bg-dark .sidebar .nav-link.active::before { background-color: #ff0000; }
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

