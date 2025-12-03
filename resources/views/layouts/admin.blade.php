<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - Barangay Health Center')</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- NProgress -->
    <script src="https://unpkg.com/nprogress@0.2.0/nprogress.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/nprogress@0.2.0/nprogress.css" />
    <style>
        #nprogress .bar {
            background: #0d6efd !important;
            height: 3px !important;
        }

        #nprogress .peg {
            box-shadow: 0 0 10px #0d6efd, 0 0 5px #0d6efd !important;
        }

        #nprogress .spinner-icon {
            border-top-color: #0d6efd !important;
            border-left-color: #0d6efd !important;
        }
    </style>


    @stack('styles')

    <style>
        :root {
            --sidebar-width: 240px;
            --sidebar-collapsed: 72px;
            --header-height: 64px;
            --transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --border-color: #e9ecef;
            --bg-light: #f8f9fa;
            --bg-dark: #151718;
            --text-light: #111;
            --text-dark: #e6e6e6;
            --sidebar-bg-light: #ffffff;
            --sidebar-bg-dark: #131516;
            --header-bg-light: #ffffff;
            --header-bg-dark: #1b1e20;
            --card-bg-light: #ffffff;
            --card-bg-dark: #1e2124;
            --border-dark: #2a2f35;

            /* Theme Colors - Trust Blue, Vitality Teal, Energy Coral */
            --color-primary: hsl(210, 100%, 45%);
            --color-primary-dark: hsl(210, 100%, 35%);
            --color-primary-light: hsl(210, 100%, 65%);
            --color-primary-hover: hsl(210, 100%, 40%);
            --color-primary-rgb: 0, 102, 230;

            --color-secondary: hsl(174, 62%, 47%);
            --color-secondary-dark: hsl(174, 62%, 37%);
            --color-secondary-light: hsl(174, 62%, 67%);
            --color-secondary-rgb: 45, 196, 186;

            --color-accent: hsl(14, 90%, 60%);
            --color-accent-dark: hsl(14, 90%, 50%);
            --color-accent-light: hsl(14, 90%, 75%);
            --color-accent-rgb: 250, 114, 76;

            /* Legacy variables for compatibility */
            --primary: var(--color-primary);
            --primary-light: var(--color-primary-light);
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: background-color .3s, color .3s;
        }

        body.bg-dark {
            background-color: var(--bg-dark);
            color: var(--text-dark);
        }

        /* ---------- Sidebar ---------- */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg-light);
            border-right: 1px solid var(--border-color);
            z-index: 1000;
            transition: width var(--transition), transform var(--transition);
            overflow: hidden;
            box-shadow: 2px 0 8px rgba(0, 0, 0, .05);
            contain: layout style;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }

        .sidebar-header {
            position: relative;
            height: 72px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .burger-menu-btn {
            position: absolute;
            left: 14px;
            top: 14px;
            width: 44px;
            height: 44px;
            background: none;
            border: none;
            font-size: 1.4rem;
            color: #495057;
            cursor: pointer;
            padding: 0;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1001;
            outline: none !important;
            box-shadow: none !important;
            transition: background 0.15s ease, color 0.15s ease;
        }

        .burger-menu-btn:hover {
            background: #f2f2f2;
            color: var(--primary);
        }

        .burger-menu-btn:active {
            background: #e0e0e0 !important;
            transform: none !important;
        }

        .sidebar .nav {
            padding: 0.5rem 0.5rem 0;
            margin-top: 60px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sidebar .nav-link {
            width: 44px;
            height: 44px;
            min-height: 44px;
            padding: 0 14px;
            margin: 2px 0 2px 13px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            font-size: 14px;
            text-decoration: none;
            transition: all .2s ease;
            position: relative;
            box-sizing: border-box;
            color: #0f0f0f;
            font-weight: 400;
        }

        .sidebar .nav-link i {
            font-size: 20px;
            width: 24px;
            text-align: center;
            margin: 0;
            flex-shrink: 0;
        }

        .sidebar .nav-link span {
            opacity: 1;
            transition: opacity .2s ease;
            white-space: nowrap;
            margin-left: 12px;
        }

        /* Hover State */
        .sidebar .nav-link:hover {
            background: rgba(var(--color-primary-rgb), 0.08);
        }

        /* Active State - Using Theme Colors */
        .sidebar .nav-link.active {
            background: rgba(var(--color-primary-rgb), 0.1);
            color: var(--color-primary) !important;
            font-weight: 500;
        }

        .sidebar .nav-link.active i {
            color: var(--color-primary);
        }

        .sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 24px;
            background: var(--color-primary);
            border-radius: 0 2px 2px 0;
        }

        .sidebar:not(.collapsed) .nav-link {
            width: calc(100% - 16px);
            justify-content: flex-start;
            padding: 0 14px;
        }

        .sidebar.collapsed .nav-link {
            width: 44px;
            padding: 0 10px;
            justify-content: flex-start;
        }

        .sidebar.collapsed .nav-link span {
            opacity: 0;
            pointer-events: none;
            width: 0;
            overflow: hidden;
            margin: 0;
        }

        .sidebar.collapsed .nav-link:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 100%;
            margin-left: 12px;
            background: rgba(0, 0, 0, .8);
            color: #fff;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 1001;
            pointer-events: none;
        }

        .close-sidebar-btn {
            display: none;
            position: absolute;
            left: 14px;
            top: 14px;
            width: 44px;
            height: 44px;
            z-index: 1001;
        }

        @media (max-width: 991px) {
            .close-sidebar-btn {
                display: flex;
            }

            .burger-menu-btn:not(.close-sidebar-btn) {
                display: none;
            }
        }

        /* ---------- Header ---------- */
        .header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: var(--header-bg-light);
            border-bottom: 1px solid var(--border-color);
            min-height: var(--header-height);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .05);
        }

        .header h4 {
            font-size: 1.25rem;
            font-weight: 500;
            margin: 0;
        }

        .header .text-muted {
            font-size: .875rem;
            margin: 0;
        }

        /* ---------- Main Content ---------- */
        .main-content {
            min-height: 100vh;
            margin-left: var(--sidebar-width);
            background: #f9f9f9;
            transition: margin-left var(--transition);
        }

        .main-content.sidebar-closed {
            margin-left: var(--sidebar-collapsed);
        }

        /* ---------- Cards ---------- */
        .card-surface {
            background: var(--card-bg-light);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .15);
            transition: transform .2s ease;
        }

        /* Feedback modal should have full-width colored header (no inner padding) */
        #feedbackModal .modal-content.card-surface {
            padding: 0;
        }

        .card-surface:hover {
            transform: translateY(-2px);
        }

        .filter-card {
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .chart-container {
            padding: 1.75rem 1.5rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .btn {
            border-radius: 8px;
            font-weight: 600;
        }

        .btn i {
            margin-right: .35rem;
        }

        .btn-sm {
            padding: .375rem .75rem;
            font-size: .875rem;
        }

        /* ---------- Dark Mode – General ---------- */
        body.bg-dark .sidebar {
            background: var(--sidebar-bg-dark);
            border-color: var(--border-dark);
        }

        body.bg-dark .header {
            background: var(--header-bg-dark);
            border-color: var(--border-dark);
        }

        body.bg-dark .main-content {
            background: var(--bg-dark);
        }

        body.bg-dark .card-surface,
        body.bg-dark .filter-card,
        body.bg-dark .chart-container,
        body.bg-dark .modal-content {
            background: var(--card-bg-dark);
            color: var(--text-dark);
            border-color: var(--border-dark);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .3);
        }

        body.bg-dark .form-control,
        body.bg-dark .form-select {
            background: #0f1316;
            color: #e6e6e6;
            border-color: var(--border-dark);
        }

        body.bg-dark .form-control::placeholder {
            color: #9aa4ad;
        }

        body.bg-dark .text-muted {
            color: #b0b0b0 !important;
        }

        body.bg-dark .burger-menu-btn {
            color: #cbd3da;
        }

        body.bg-dark .burger-menu-btn:hover {
            background: #303030;
            color: var(--primary-light);
        }

        body.bg-dark .sidebar .nav-link {
            color: #f1f1f1;
        }

        body.bg-dark .sidebar .nav-link:hover {
            background: rgba(var(--color-primary-rgb), 0.15);
        }

        /* Dark Mode Active State - Using Theme Colors */
        body.bg-dark .sidebar .nav-link.active {
            background: rgba(var(--color-primary-rgb), 0.2);
            color: var(--color-primary-light) !important;
        }

        body.bg-dark .sidebar .nav-link.active i {
            color: var(--color-primary-light);
        }

        body.bg-dark .sidebar .nav-link.active::before {
            background: var(--color-primary-light);
        }

        /* ---------- Dark Mode – Tables ---------- */
        body.bg-dark .table {
            --bs-table-bg: #1e2124;
            --bs-table-striped-bg: #232629;
            --bs-table-active-bg: #2a2f35;
            --bs-table-hover-bg: #2a2f35;
            color: #d6d6d6;
            border-color: var(--border-dark);
        }

        body.bg-dark .table thead th {
            background: #1a1f24 !important;
            color: #e6e6e6;
            border-color: var(--border-dark);
        }

        body.bg-dark .table tbody td {
            border-color: var(--border-dark);
        }

        body.bg-dark .table-striped>tbody>tr:nth-of-type(odd)>* {
            --bs-table-accent-bg: var(--bs-table-striped-bg);
        }

        body.bg-dark .table-hover>tbody>tr:hover>* {
            --bs-table-accent-bg: var(--bs-table-hover-bg);
        }

        .table-dark {
            --bs-table-bg: #1e2124;
            --bs-table-striped-bg: #232629;
            --bs-table-active-bg: #2a2f35;
            --bs-table-hover-bg: #2a2f35;
            color: #d6d6d6;
        }

        /* ---------- Sidebar Dropdown Styles ---------- */
        .sidebar-dropdown {
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .sidebar-dropdown>.nav-link {
            position: relative;
        }

        .dropdown-arrow {
            font-size: 12px;
            transition: transform 0.2s ease;
            position: absolute;
            right: 14px;
            margin-left: 0 !important;
        }

        .sidebar.collapsed .dropdown-arrow {
            display: none;
        }

        .rotate-180 {
            transform: rotate(180deg);
        }

        .submenu {
            padding-left: 0;
            overflow: hidden;
        }

        .sidebar:not(.collapsed) .submenu {
            padding-left: 12px;
        }

        .submenu-section {
            margin-bottom: 8px;
        }

        .submenu-header {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            padding: 8px 14px 4px;
            margin-top: 4px;
        }

        .submenu-link {
            font-size: 13px;
            height: 38px;
            min-height: 38px;
            display: flex;
            align-items: center;
        }

        .sidebar:not(.collapsed) .submenu-link {
            padding-left: 28px !important;
        }

        .submenu-link i {
            font-size: 16px;
            width: 24px;
            text-align: center;
            flex-shrink: 0;
        }

        .submenu-link span {
            margin-left: 12px;
        }

        /* Collapsed sidebar - hide submenu */
        .sidebar.collapsed .submenu {
            display: none !important;
        }

        .sidebar.collapsed .dropdown-arrow {
            display: none;
        }

        /* Dark mode submenu */
        body.bg-dark .submenu-header {
            color: #9aa4ad;
        }

        body.bg-dark .submenu-link {
            color: #e0e0e0;
        }

        /* Light mode submenu hover */
        .submenu-link:hover {
            background: rgba(var(--color-primary-rgb), 0.08);
        }

        body.bg-dark .submenu-link:hover {
            background: rgba(var(--color-primary-rgb), 0.12);
        }

        /* Submenu Active State - CONSISTENT with nav-link */
        .submenu-link.active {
            background: rgba(var(--color-primary-rgb), 0.1);
            color: var(--color-primary) !important;
            font-weight: 500;
        }

        .submenu-link.active i {
            color: var(--color-primary);
        }

        body.bg-dark .submenu-link.active {
            background: rgba(var(--color-primary-rgb), 0.2);
            color: var(--color-primary-light) !important;
        }

        body.bg-dark .submenu-link.active i {
            color: var(--color-primary-light);
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-width);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0 !important;
            }

            .header {
                padding: 1rem;
            }

            .header h4 {
                font-size: 1.1rem;
            }

            .header .text-muted {
                font-size: .85rem;
            }
        }

        @media (max-width: 576px) {
            .header img {
                width: 32px !important;
                height: 32px !important;
                margin-right: 8px !important;
            }
        }

        @yield('page-styles')
    </style>
</head>

<body>
    <!-- Sidebar Overlay -->
    @if(!View::hasSection('hide-sidebar'))
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    @endif

    <!-- Sidebar -->
    @if(!View::hasSection('hide-sidebar'))
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <button class="burger-menu-btn" id="toggleSidebarBtn" aria-label="Toggle sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <button class="burger-menu-btn close-sidebar-btn" id="closeSidebarBtn" aria-label="Close sidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <nav class="nav flex-column">
            @yield('sidebar-menu')
        </nav>
    </aside>
    @endif

    <!-- Main Content -->
    <div class="main-content" id="mainContent" @if(View::hasSection('hide-sidebar')) style="margin-left: 0" @endif>
        <!-- Header -->
        <header class="header">
            <div class="d-flex align-items-center">
                <img src="{{ asset('images/malasakit-logo-blue.png') }}" alt="Logo"
                    style="width:40px;height:40px;margin-right:12px;">
                <div>
                    <h4 class="mb-0">@yield('page-title', 'Dashboard')</h4>
                    <p class="text-muted mb-0">@yield('page-description', 'Welcome!')</p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-link text-muted" title="Notifications"><i class="fas fa-bell"></i></button>
                <button class="btn btn-link text-muted" id="themeToggle" title="Toggle theme"><i
                        class="fas fa-moon"></i></button>

                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle"
                        id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                            style="width:40px;height:40px; overflow: hidden;">
                            @if(\App\Helpers\AuthHelper::user()->profile_picture)
                                <img src="{{ asset('storage/' . \App\Helpers\AuthHelper::user()->profile_picture) }}"
                                    alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                {{ substr(\App\Helpers\AuthHelper::user()->name, 0, 1) }}
                            @endif
                        </div>
                        <div class="fw-bold me-2">{{ \App\Helpers\AuthHelper::user()->name }}</div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end text-small shadow" aria-labelledby="dropdownUser1">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-4">
            @yield('content')
        </main>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('success') || session('status') || session('error') || session('warning') || session('info'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                @if(session('success') || session('status'))
                    Toast.fire({ icon: 'success', title: "{{ session('success') ?? session('status') }}" });
                @endif
                @if(session('error'))
                    Toast.fire({ icon: 'error', title: "{{ session('error') }}" });
                @endif
                @if(session('warning'))
                    Toast.fire({ icon: 'warning', title: "{{ session('warning') }}" });
                @endif
                @if(session('info'))
                    Toast.fire({ icon: 'info', title: "{{ session('info') }}" });
                @endif
                        });
        </script>
    @endif

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleBtn = document.getElementById('toggleSidebarBtn');
            const closeBtn = document.getElementById('closeSidebarBtn');
            const overlay = document.getElementById('sidebarOverlay');
            const themeToggle = document.getElementById('themeToggle');
            const sidebarKey = 'sidebar-collapsed';
            const themeKey = 'app-theme';

            const isDesktop = () => window.innerWidth >= 992;

            const init = () => {
                sidebar.classList.add('no-transition');
                mainContent.classList.add('no-transition');

                if (isDesktop()) {
                    const collapsed = localStorage.getItem(sidebarKey) !== 'false';
                    sidebar.classList.toggle('collapsed', collapsed);
                    mainContent.classList.toggle('sidebar-closed', collapsed);
                    sidebar.classList.add('show');
                } else {
                    sidebar.classList.remove('collapsed', 'show');
                    overlay.classList.remove('show');
                }

                requestAnimationFrame(() => {
                    sidebar.classList.remove('no-transition');
                    mainContent.classList.remove('no-transition');
                });
            };

            const toggleSidebar = () => {
                if (isDesktop()) {
                    const willCollapse = !sidebar.classList.contains('collapsed');
                    sidebar.classList.toggle('collapsed', willCollapse);
                    mainContent.classList.toggle('sidebar-closed', willCollapse);
                    localStorage.setItem(sidebarKey, willCollapse);
                } else {
                    sidebar.classList.toggle('show');
                    overlay.classList.toggle('show');
                }
            };

            toggleBtn?.addEventListener('click', toggleSidebar);
            closeBtn?.addEventListener('click', () => {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });
            overlay?.addEventListener('click', () => {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });

            let rt;
            window.addEventListener('resize', () => {
                clearTimeout(rt);
                rt = setTimeout(init, 250);
            });

            /* ---------- Theme toggle + table-dark sync ---------- */
            const syncTableDark = (isDark) => {
                document.querySelectorAll('table').forEach(t => {
                    t.classList.toggle('table-dark', isDark);
                });
            };

            const applySavedTheme = () => {
                const saved = localStorage.getItem(themeKey);
                const isDark = saved === 'dark';
                document.body.classList.toggle('bg-dark', isDark);
                syncTableDark(isDark);
            };

            applySavedTheme();

            themeToggle?.addEventListener('click', () => {
                const isDark = document.body.classList.toggle('bg-dark');
                localStorage.setItem(themeKey, isDark ? 'dark' : 'light');
                syncTableDark(isDark);
            });

            const modalEl = document.getElementById('feedbackModal');
            if (modalEl) new bootstrap.Modal(modalEl).show();

            window.initPatientRegistrationForms = () => {
                const map = {
                    'Barangay 11': ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'],
                    'Barangay 12': ['Purok 1', 'Purok 2', 'Purok 3']
                };
                document.querySelectorAll('.patient-registration-form').forEach(form => {
                    const barangay = form.querySelector('[data-role="barangay"]');
                    const otherGroup = form.querySelector('[data-role="barangay-other-group"]');
                    const otherInput = form.querySelector('[data-role="barangay-other"]');
                    const purokGroup = form.querySelector('[data-role="purok-group"]');
                    const purok = form.querySelector('[data-role="purok"]');

                    const updatePurok = val => {
                        if (!purok) return;
                        const prev = purok.dataset.selected || '';
                        purok.innerHTML = '<option value="">Select Purok</option>';
                        if (map[val]) {
                            map[val].forEach(p => {
                                const opt = new Option(p, p, false, prev === p);
                                purok.appendChild(opt);
                            });
                            purok.required = true;
                            purokGroup.classList.remove('d-none');
                        } else {
                            purok.required = false;
                            purokGroup.classList.add('d-none');
                        }
                    };

                    barangay?.addEventListener('change', () => {
                        const v = barangay.value;
                        if (v === 'Other') {
                            otherGroup?.classList.remove('d-none');
                            otherInput?.setAttribute('required', '');
                        } else {
                            otherGroup?.classList.add('d-none');
                            otherInput?.removeAttribute('required');
                            otherInput.value = '';
                        }
                        updatePurok(v);
                    });
                    barangay && updatePurok(barangay.value);
                });
            };
            initPatientRegistrationForms();
            init();

            // NProgress Configuration
            NProgress.configure({ showSpinner: false });

            // Show progress bar on link clicks
            document.addEventListener('click', function (e) {
                const link = e.target.closest('a');
                if (link &&
                    !link.getAttribute('target') &&
                    !link.getAttribute('href').startsWith('#') &&
                    !link.getAttribute('href').startsWith('javascript') &&
                    link.getAttribute('href') !== '#'
                ) {
                    NProgress.start();
                }
            });

            // Show progress bar on form submit
            document.addEventListener('submit', function (e) {
                if (!e.defaultPrevented) {
                    NProgress.start();
                }
            });

            // Stop progress bar when page finishes loading (in case of back button or cache)
            window.addEventListener('pageshow', function () {
                NProgress.done();
            });
        });
    </script>

    @stack('scripts')
</body>

</html>