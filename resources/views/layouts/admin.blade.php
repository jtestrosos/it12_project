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
            --sidebar-bg-dark: #212121;
            --header-bg-light: #ffffff;
            --header-bg-dark: #1b1e20;
            --card-bg-light: #ffffff;
            --card-bg-dark: #1e2124;
            --border-dark: #2a2f35;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }
        body.bg-dark { background-color: var(--bg-dark); color: var(--text-dark); }

        /* ---------- Sidebar ---------- */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg-light);
            border-right: 1px solid var(--border-color);
            z-index: 1000;
            transition: width var(--transition), transform var(--transition);
            overflow: hidden;
            box-shadow: 2px 0 8px rgba(0,0,0,.05);
            contain: layout style;
        }
        .sidebar.collapsed { width: var(--sidebar-collapsed); }

        /* Header — fixed height */
        .sidebar-header {
            position: relative;
            height: 72px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* BURGER — UNTOUCHED, EXACTLY AS YOU PROVIDED */
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
            color: #0d6efd;
        }
        .burger-menu-btn:active {
            background: #e0e0e0 !important;
            transform: none !important;
        }

        /* NAV — CENTER ALL MENU ITEMS */
        .sidebar .nav {
            padding: 0.5rem 0.5rem 0;
            margin-top: 60px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* MENU ITEMS — 44x44 CORE, CENTERED ICON */
        .sidebar .nav-link {
            width: 44px;
            height: 44px;
            min-height: 44px;
            padding: 0 14px;
            margin: 2px 0;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
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
        }

        .sidebar .nav-link span {
            opacity: 1;
            transition: opacity .2s ease;
            white-space: nowrap;
            margin-left: 12px;
        }

        .sidebar .nav-link:hover { background: #f2f2f2; }
        .sidebar .nav-link.active {
            background: #f0f0f0;
            font-weight: 500;
        }
        .sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0; top: 50%; transform: translateY(-50%);
            width: 3px; height: 24px;
            background: #ff0000;
            border-radius: 0 2px 2px 0;
        }

        /* EXPANDED: Full width, left-aligned text */
        .sidebar:not(.collapsed) .nav-link {
            width: calc(100% - 16px);
            justify-content: flex-start;
            padding: 0 14px;
        }

        /* COLLAPSED: Icon-only, perfectly centered */
        .sidebar.collapsed .nav-link {
            width: 44px;
            padding: 0;
            justify-content: center;
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
            position: absolute; left: 100%; margin-left: 12px;
            background: rgba(0,0,0,.8); color: #fff; padding: 8px 12px;
            border-radius: 4px; font-size: 12px; white-space: nowrap;
            z-index: 1001; pointer-events: none;
        }

        /* Mobile close button — same position */
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
            .close-sidebar-btn { display: flex; }
            .burger-menu-btn:not(.close-sidebar-btn) { display: none; }
        }

        /* ---------- Header ---------- */
        .header {
            position: sticky; top: 0; z-index: 100;
            background: var(--header-bg-light);
            border-bottom: 1px solid var(--border-color);
            min-height: var(--header-height);
            padding: 1rem 2rem;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 1px 2px rgba(0,0,0,.05);
        }
        .header h4 { font-size: 1.25rem; font-weight: 500; margin: 0; }
        .header .text-muted { font-size: .875rem; margin: 0; }

        /* ---------- Main Content ---------- */
        .main-content {
            min-height: 100vh;
            margin-left: var(--sidebar-width);
            background: #f9f9f9;
            transition: margin-left var(--transition);
        }
        .main-content.sidebar-closed { margin-left: var(--sidebar-collapsed); }

        /* ---------- Cards ---------- */
        .card-surface {
            background: var(--card-bg-light); border-radius: 12px;
            padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,.15);
            transition: transform .2s ease;
        }
        .card-surface:hover { transform: translateY(-2px); }
        .filter-card { padding: 1rem; margin-bottom: 1rem; }
        .chart-container { padding: 1.75rem 1.5rem 1.5rem; margin-bottom: 1.5rem; }

        .btn { border-radius: 8px; font-weight: 600; }
        .btn i { margin-right: .35rem; }
        .btn-sm { padding: .375rem .75rem; font-size: .875rem; }

        /* ---------- Dark Mode ---------- */
        body.bg-dark .sidebar { background: var(--sidebar-bg-dark); border-color: var(--border-dark); }
        body.bg-dark .header { background: var(--header-bg-dark); border-color: var(--border-dark); }
        body.bg-dark .main-content { background: var(--bg-dark); }
        body.bg-dark .card-surface,
        body.bg-dark .filter-card,
        body.bg-dark .chart-container,
        body.bg-dark .modal-content {
            background: var(--card-bg-dark); color: var(--text-dark);
            border-color: var(--border-dark); box-shadow: 0 2px 8px rgba(0,0,0,.3);
        }
        body.bg-dark .table { color: #d6d6d6; }
        body.bg-dark .table thead { background: #1a1f24 !important; color: #e6e6e6; }
        body.bg-dark .form-control,
        body.bg-dark .form-select { background: #0f1316; color: #e6e6e6; border-color: var(--border-dark); }
        body.bg-dark .form-control::placeholder { color: #9aa4ad; }
        body.bg-dark .text-muted { color: #b0b0b0 !important; }
        body.bg-dark .burger-menu-btn { color: #cbd3da; }
        body.bg-dark .burger-menu-btn:hover { background: #303030; color: #6ea8fe; }
        body.bg-dark .sidebar .nav-link { color: #f1f1f1; }
        body.bg-dark .sidebar .nav-link:hover { background: #303030; color: #fff; }
        body.bg-dark .sidebar .nav-link.active { background: #303030; color: #fff; }

        /* ---------- Responsive ---------- */
        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); width: var(--sidebar-width); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0 !important; }
            .header { padding: 1rem; }
            .header h4 { font-size: 1.1rem; }
            .header .text-muted { font-size: .85rem; }
        }
        @media (max-width: 576px) {
            .header img { width: 32px !important; height: 32px !important; margin-right: 8px !important; }
        }

        @yield('page-styles')
    </style>
</head>
<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <!-- Burger (Desktop) — UNCHANGED -->
            <button class="burger-menu-btn" id="toggleSidebarBtn" aria-label="Toggle sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <!-- Close (Mobile) — UNCHANGED -->
            <button class="burger-menu-btn close-sidebar-btn" id="closeSidebarBtn" aria-label="Close sidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <nav class="nav flex-column">
            @yield('sidebar-menu')
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
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
                <button class="btn btn-link text-muted" id="themeToggle" title="Toggle theme"><i class="fas fa-moon"></i></button>

                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width:40px;height:40px;">
                        @yield('user-initials', 'A')
                    </div>
                    <div>
                        <div class="fw-bold">@yield('user-name', 'Admin')</div>
                        <small class="text-muted">@yield('user-role', 'Administrator')</small>
                    </div>
                </div>

                <a href="{{ route('logout') }}"
                   class="btn btn-outline-secondary"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-4">
            @yield('content')
        </main>
    </div>

    <!-- Flash Modal -->
    @php
        $flashType = $flashMessage = null;
        $map = ['success'=>'success','status'=>'success','error'=>'danger','warning'=>'warning','info'=>'info'];
        foreach($map as $k=>$v){ if(session($k)){ $flashType=$v; $flashMessage=session($k); break; } }
        $titles = ['success'=>'Success','danger'=>'Error','warning'=>'Warning','info'=>'Notice'];
        $icons  = ['success'=>'fa-circle-check','danger'=>'fa-triangle-exclamation','warning'=>'fa-circle-exclamation','info'=>'fa-circle-info'];
    @endphp

    @if($flashType && $flashMessage)
        <div class="modal fade" id="feedbackModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow card-surface">
                    <div class="modal-header bg-{{ $flashType }} text-white">
                        <h5 class="modal-title d-flex align-items-center gap-2">
                            <i class="fas {{ $icons[$flashType] }}"></i>
                            {{ $titles[$flashType] }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body py-4"><p class="mb-0">{{ $flashMessage }}</p></div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-{{ $flashType==='danger'?'danger':'primary' }}" data-bs-dismiss="modal">Got it</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar      = document.getElementById('sidebar');
            const mainContent  = document.getElementById('mainContent');
            const toggleBtn    = document.getElementById('toggleSidebarBtn');
            const closeBtn     = document.getElementById('closeSidebarBtn');
            const overlay      = document.getElementById('sidebarOverlay');
            const themeToggle  = document.getElementById('themeToggle');
            const sidebarKey   = 'sidebar-collapsed';
            const themeKey     = 'app-theme';

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

            if (localStorage.getItem(themeKey) === 'dark') document.body.classList.add('bg-dark');
            themeToggle?.addEventListener('click', () => {
                const dark = document.body.classList.toggle('bg-dark');
                localStorage.setItem(themeKey, dark ? 'dark' : 'light');
            });

            const modalEl = document.getElementById('feedbackModal');
            if (modalEl) new bootstrap.Modal(modalEl).show();

            window.initPatientRegistrationForms = () => {
                const map = {
                    'Barangay 11': ['Purok 1','Purok 2','Purok 3','Purok 4','Purok 5'],
                    'Barangay 12': ['Purok 1','Purok 2','Purok 3']
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
        });
    </script>

    @stack('scripts')
</body>
</html>