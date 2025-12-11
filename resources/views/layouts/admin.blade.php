<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - Barangay Health Center')</title>

    <!-- Loading Screen -->
    <style>
        /* Hide scrollbar during loading */
        body:has(#page-loader:not(.loaded)) {
            overflow: hidden !important;
        }

        #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            transition: opacity 0.3s ease;
        }
        #page-loader.dark {
            background: #151718;
        }
        #page-loader.loaded {
            opacity: 0;
            pointer-events: none;
        }
        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #e9ecef;
            border-top-color: #009fb1;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        #page-loader.dark .spinner {
            border-color: #2a2f35;
            border-top-color: #009fb1;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Theme Transition Overlay - Prevents flash when navigating between themes */
        #theme-transition-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--bg-light);
            z-index: 100000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s ease;
        }
        #theme-transition-overlay.dark {
            background: var(--bg-dark);
        }
        #theme-transition-overlay.active {
            opacity: 1;
            pointer-events: all;
        }
    </style>
    <script>
        // Check theme and apply to loader
        (function() {
            var isDark = localStorage.getItem('app-theme') === 'dark';
            if (isDark) {
                document.write('<div id="page-loader" class="dark"><div class="spinner"></div></div>');
            } else {
                document.write('<div id="page-loader"><div class="spinner"></div></div>');
            }
            // Hide body overflow during loading
            document.documentElement.style.overflow = 'hidden';
        })();
    </script>
    
    <!-- Theme Transition Overlay -->
    <div id="theme-transition-overlay"></div>

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
            background: #009fb1 !important;
            height: 3px !important;
        }

        #nprogress .peg {
            box-shadow: 0 0 10px #009fb1, 0 0 5px #009fb1 !important;
        }

        #nprogress .spinner-icon {
            border-top-color: #009fb1 !important;
            border-left-color: #009fb1 !important;
        }
    </style>


    @stack('styles')

    <style>
        :root {
            --sidebar-width: 240px;
            --sidebar-collapsed: 72px;
            --header-height: 64px;
            --transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-fast: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
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
            --color-primary: #009fb1;
            --color-primary-dark: #008a9a;
            --color-primary-light: #4dbdcf;
            --color-primary-hover: #007d8a;
            --color-primary-rgb: 0, 159, 177;

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
            
            /* Consistent spacing */
            --spacing-xs: 0.5rem;
            --spacing-sm: 0.75rem;
            --spacing-md: 1rem;
            --spacing-lg: 1.5rem;
            --spacing-xl: 2rem;
            
            /* Consistent border radius */
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 14px;
            
            /* Consistent shadows */
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, .05);
            --shadow-md: 0 6px 18px rgba(15, 23, 42, 0.06);
            --shadow-lg: 0 10px 30px rgba(15, 23, 42, 0.10);
            --shadow-dark-sm: 0 2px 8px rgba(0, 0, 0, .3);
            --shadow-dark-md: 0 6px 18px rgba(0, 0, 0, .4);
            --shadow-dark-lg: 0 10px 30px rgba(0, 0, 0, .5);
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: background-color .3s, color .3s;
        }

        /* Global Theme Overrides */
        .btn-primary {
            background-color: var(--color-primary) !important;
            border-color: var(--color-primary) !important;
            color: #fff;
        }

        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active {
            background-color: var(--color-primary-hover) !important;
            border-color: var(--color-primary-hover) !important;
            color: #fff;
        }

        .btn-outline-primary {
            color: var(--color-primary) !important;
            border-color: var(--color-primary) !important;
        }

        .btn-outline-primary:hover {
            background-color: var(--color-primary) !important;
            color: #fff !important;
        }

        .text-primary {
            color: var(--color-primary) !important;
        }

        .bg-primary {
            background-color: var(--color-primary) !important;
        }

        /* Global Green Color Override - Change all green to #77dd77 */
        .bg-success,
        .badge.bg-success,
        .btn-success,
        .alert-success {
            background-color: #77dd77 !important;
            border-color: #77dd77 !important;
            color: #000 !important;
        }

        .text-success {
            color: #77dd77 !important;
        }

        .btn-success:hover,
        .btn-success:focus,
        .btn-success:active {
            background-color: #66cc66 !important;
            border-color: #66cc66 !important;
            color: #000 !important;
        }

        .btn-outline-success {
            color: #77dd77 !important;
            border-color: #77dd77 !important;
        }

        .btn-outline-success:hover {
            background-color: #77dd77 !important;
            border-color: #77dd77 !important;
            color: #000 !important;
        }

        .bg-success-subtle {
            background-color: rgba(119, 221, 119, 0.1) !important;
            color: #000 !important;
        }

        /* Dark mode green overrides */
        body.bg-dark .alert-success {
            background-color: rgba(119, 221, 119, 0.15) !important;
            border-color: rgba(119, 221, 119, 0.3) !important;
            color: #77dd77 !important;
        }

        body.bg-dark .bg-success {
            background-color: #77dd77 !important;
            color: #000 !important;
        }

        body.bg-dark .text-success {
            color: #77dd77 !important;
        }

        body.bg-dark .badge.bg-success {
            color: #000 !important;
        }

        body.bg-dark .btn-success {
            color: #000 !important;
        }

        /* Force black text on all status badges in dark mode */
        body.bg-dark .status-badge {
            color: #000 !important;
        }

        body.bg-dark .badge.bg-warning,
        body.bg-dark .badge.bg-danger,
        body.bg-dark .badge.bg-info,
        body.bg-dark span.bg-warning,
        body.bg-dark span.bg-success,
        body.bg-dark span.bg-danger,
        body.bg-dark span.bg-info {
            color: #000 !important;
        }

        /* Global Yellow/Warning Color Override - Change all yellow to #FFF52E */
        .bg-warning,
        .badge.bg-warning,
        .btn-warning,
        .alert-warning {
            background-color: #FFF52E !important;
            border-color: #FFF52E !important;
            color: #000 !important;
        }

        .text-warning {
            color: #FFF52E !important;
        }

        .btn-warning:hover,
        .btn-warning:focus,
        .btn-warning:active {
            background-color: #ffe61f !important;
            border-color: #ffe61f !important;
            color: #000 !important;
        }

        .btn-outline-warning {
            color: #FFF52E !important;
            border-color: #FFF52E !important;
        }

        .btn-outline-warning:hover {
            background-color: #FFF52E !important;
            border-color: #FFF52E !important;
            color: #000 !important;
        }

        .bg-warning-subtle {
            background-color: rgba(255, 245, 46, 0.1) !important;
            color: #000 !important;
        }

        /* Dark mode yellow overrides */
        body.bg-dark .alert-warning {
            background-color: rgba(255, 245, 46, 0.15) !important;
            border-color: rgba(255, 245, 46, 0.3) !important;
            color: #FFF52E !important;
        }

        body.bg-dark .bg-warning {
            background-color: #FFF52E !important;
            color: #000 !important;
        }

        body.bg-dark .text-warning {
            color: #FFF52E !important;
        }

        body.bg-dark .badge.bg-warning {
            color: #000 !important;
        }

        body.bg-dark .btn-warning {
            color: #000 !important;
        }

        /* Global Red/Danger Color Override - Change all red to #F53838 */
        .bg-danger,
        .badge.bg-danger,
        .alert-danger {
            background-color: #F53838 !important;
            border-color: #F53838 !important;
            color: #000 !important;
        }

        .btn-danger {
            background-color: #F53838 !important;
            border-color: #F53838 !important;
            color: #fff !important;
        }

        .text-danger {
            color: #F53838 !important;
        }

        .btn-danger:hover,
        .btn-danger:focus,
        .btn-danger:active {
            background-color: #e62929 !important;
            border-color: #e62929 !important;
            color: #fff !important;
        }

        .btn-outline-danger {
            color: #F53838 !important;
            border-color: #F53838 !important;
        }

        .btn-outline-danger:hover {
            background-color: #F53838 !important;
            border-color: #F53838 !important;
            color: #fff !important;
        }

        .bg-danger-subtle {
            background-color: rgba(245, 56, 56, 0.1) !important;
            color: #000 !important;
        }

        /* Dark mode red overrides */
        body.bg-dark .alert-danger {
            background-color: rgba(245, 56, 56, 0.15) !important;
            border-color: rgba(245, 56, 56, 0.3) !important;
            color: #F53838 !important;
        }

        body.bg-dark .bg-danger {
            background-color: #F53838 !important;
            color: #000 !important;
        }

        body.bg-dark .text-danger {
            color: #F53838 !important;
        }

        body.bg-dark .badge.bg-danger {
            color: #000 !important;
        }

        body.bg-dark .btn-danger {
            color: #fff !important;
        }

        body.bg-dark {
            background-color: var(--bg-dark);
            color: var(--text-dark);
            scrollbar-color: #3a3f47 #1a1d1f;
            scrollbar-width: thin;
        }

        /* Dark Mode Scrollbar Styling - Highest Priority */
        body.bg-dark::-webkit-scrollbar,
        body.bg-dark *::-webkit-scrollbar,
        html:has(body.bg-dark)::-webkit-scrollbar {
            width: 10px !important;
            height: 10px !important;
        }

        body.bg-dark::-webkit-scrollbar-track,
        body.bg-dark *::-webkit-scrollbar-track,
        html:has(body.bg-dark)::-webkit-scrollbar-track {
            background: #1a1d1f !important;
        }

        body.bg-dark::-webkit-scrollbar-thumb,
        body.bg-dark *::-webkit-scrollbar-thumb,
        html:has(body.bg-dark)::-webkit-scrollbar-thumb {
            background: #3a3f47 !important;
            border-radius: 5px !important;
        }

        body.bg-dark::-webkit-scrollbar-thumb:hover,
        body.bg-dark *::-webkit-scrollbar-thumb:hover,
        html:has(body.bg-dark)::-webkit-scrollbar-thumb:hover {
            background: #4a5058 !important;
        }

        /* Apply to html and body directly */
        :root:has(body.bg-dark) {
            scrollbar-color: #3a3f47 #1a1d1f;
            scrollbar-width: thin;
        }

        /* Sidebar specific scrollbar */
        body.bg-dark .sidebar {
            scrollbar-color: #2a2f35 #0d0f10;
            scrollbar-width: thin;
        }

        body.bg-dark .sidebar::-webkit-scrollbar {
            width: 6px !important;
        }

        body.bg-dark .sidebar::-webkit-scrollbar-track {
            background: #0d0f10 !important;
        }

        body.bg-dark .sidebar::-webkit-scrollbar-thumb {
            background: #2a2f35 !important;
            border-radius: 3px !important;
        }

        body.bg-dark .sidebar::-webkit-scrollbar-thumb:hover {
            background: #3a3f47 !important;
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
            overflow-y: auto;
            scrollbar-gutter: stable;
            box-shadow: var(--shadow-sm);
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
            padding: 0 0.5rem 0 0;
            margin-top: 60px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .sidebar .nav-link {
            width: 44px;
            height: 44px;
            min-height: 44px;
            padding: 0;
            margin-left: 14px; /* align with burger button */
            margin-top: 2px;
            margin-bottom: 2px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center; /* center icon */
            font-size: 14px;
            text-decoration: none;
            transition: all .2s ease;
            position: relative;
            box-sizing: border-box;
            color: #0f0f0f;
            font-weight: 400;
        }

        .nav-link i {
            vertical-align: middle;
            line-height: 1;
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
            box-shadow: var(--shadow-sm);
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

        /* ---------- Unified Card System ---------- */
        .card-surface,
        .metric-card,
        .inventory-card,
        .chart-container,
        .filter-card,
        .table-card {
            background: var(--card-bg-light);
            border-radius: var(--radius-lg);
            padding: var(--spacing-lg);
            box-shadow: var(--shadow-md);
            transition: transform var(--transition-fast), box-shadow var(--transition-fast);
            border: 1px solid #edf1f7;
        }

        .card-surface:hover,
        .metric-card:hover,
        .inventory-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            border-color: #d0e2ff;
        }

        .filter-card {
            padding: var(--spacing-md);
            margin-bottom: var(--spacing-md);
        }

        .chart-container {
            padding: var(--spacing-lg);
            margin-bottom: var(--spacing-lg);
            min-height: 420px;
        }

        .table-card {
            padding: 0;
            overflow: hidden;
        }

        /* Feedback modal should have full-width colored header (no inner padding) */
        #feedbackModal .modal-content.card-surface {
            padding: 0;
        }

        /* ---------- Buttons ---------- */
        .btn {
            border-radius: var(--radius-sm);
            font-weight: 600;
            transition: all var(--transition-fast);
        }

        .btn i {
            margin-right: .35rem;
        }

        .btn-sm {
            padding: .375rem .75rem;
            font-size: .875rem;
        }

        /* ---------- Forms ---------- */
        .form-control,
        .form-select {
            border-radius: var(--radius-sm);
            border: 1px solid #e9ecef;
            transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 0.2rem rgba(var(--color-primary-rgb), 0.25);
        }

        /* ---------- Tables ---------- */
        .table-modern {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-modern thead th {
            background-color: #f9fafb;
            border: none;
            font-weight: 600;
            color: #4b5563;
            padding: 0.85rem 1rem;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: .04em;
        }

        .table-modern tbody td {
            border: none;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #f1f3f4;
        }

        .table-modern tbody tr:hover {
            background-color: #f8f9fa;
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
        body.bg-dark .metric-card,
        body.bg-dark .inventory-card,
        body.bg-dark .filter-card,
        body.bg-dark .chart-container,
        body.bg-dark .table-card,
        body.bg-dark .modal-content {
            background: var(--card-bg-dark);
            color: var(--text-dark);
            border-color: var(--border-dark);
            box-shadow: var(--shadow-dark-md);
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
            color: var(--color-primary) !important;
        }

        body.bg-dark .sidebar .nav-link.active i {
            color: var(--color-primary);
        }

        body.bg-dark .sidebar .nav-link.active::before {
            background: var(--color-primary);
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

        body.bg-dark .table-modern thead th {
            background-color: #1a1f24;
            color: #e6e6e6;
        }

        body.bg-dark .table-modern tbody td {
            border-bottom-color: #2a2f35;
            color: #d6d6d6;
        }

        body.bg-dark .table-modern tbody tr:hover {
            background-color: #2a2f35;
        }

        /* ---------- Dark Mode – Pagination ---------- */
        body.bg-dark .pagination .page-link {
            background-color: #1e2124;
            border-color: #2a2f35;
            color: #e6e6e6;
        }

        body.bg-dark .pagination .page-link:hover {
            background-color: #2a2f35;
            border-color: #3f4751;
            color: #ffffff;
        }

        body.bg-dark .pagination .page-item.active .page-link {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
            color: #ffffff;
        }

        body.bg-dark .pagination .page-item.disabled .page-link {
            background-color: #1a1f24;
            border-color: #2a2f35;
            color: #6c757d;
        }

        /* Dark mode for dropdown menu (Profile/Logout) */
        body.bg-dark .dropdown-menu {
            background-color: #1e2124;
            border-color: #2a2f35;
        }

        body.bg-dark .dropdown-item {
            color: #e6e6e6;
        }

        body.bg-dark .dropdown-item:hover,
        body.bg-dark .dropdown-item:focus {
            background-color: #2a2f35;
            color: #ffffff;
        }

        body.bg-dark .dropdown-divider {
            border-top-color: #2a2f35;
        }

        /* Dark mode for alerts/notifications */
        body.bg-dark .alert-info {
            background-color: #1a3a4a;
            border-color: #2a5a6a;
            color: #a8d5e2;
        }

        body.bg-dark .alert-warning {
            background-color: #4a3a1a;
            border-color: #6a5a2a;
            color: #f5d88a;
        }

        body.bg-dark .alert-success {
            background-color: #1a3a2a;
            border-color: #2a5a3a;
            color: #a8e2b8;
        }

        body.bg-dark .alert-danger {
            background-color: #3a1a1a;
            border-color: #5a2a2a;
            color: #f5a8a8;
        }

        body.bg-dark .alert .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        body.bg-dark .alert a {
            color: inherit;
            text-decoration: underline;
            font-weight: 600;
        }

        /* Light mode scrollbar - ensure consistent width */
        ::-webkit-scrollbar {
            width: 12px;
            height: 12px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 6px;
            border: 2px solid #f1f1f1;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Firefox scrollbar */
        * {
            scrollbar-width: thin;
            scrollbar-color: #c1c1c1 #f1f1f1;
        }

        /* Dark mode scrollbar */
        body.bg-dark ::-webkit-scrollbar {
            width: 12px;
            height: 12px;
        }

        body.bg-dark ::-webkit-scrollbar-track {
            background: #1a1f24;
        }

        body.bg-dark ::-webkit-scrollbar-thumb {
            background: #2a2f35;
            border-radius: 6px;
            border: 2px solid #1a1f24;
        }

        body.bg-dark ::-webkit-scrollbar-thumb:hover {
            background: #3a3f45;
        }

        /* Firefox scrollbar */
        body.bg-dark * {
            scrollbar-width: thin;
            scrollbar-color: #2a2f35 #1a1f24;
        }

        /* ---------- Dark Mode SweetAlert2 Styling ---------- */
        body.bg-dark .swal2-popup {
            background: #1e2124 !important;
            color: #e6e6e6 !important;
            border: 1px solid #2a2f35 !important;
        }

        body.bg-dark .swal2-title {
            color: #e6e6e6 !important;
        }

        body.bg-dark .swal2-html-container,
        body.bg-dark .swal2-content {
            color: #b0b0b0 !important;
        }

        body.bg-dark .swal2-confirm {
            background-color: #009fb1 !important;
            border-color: #009fb1 !important;
        }

        body.bg-dark .swal2-confirm:hover {
            background-color: #008a9a !important;
        }

        body.bg-dark .swal2-cancel {
            background-color: #495057 !important;
            border-color: #495057 !important;
        }

        body.bg-dark .swal2-cancel:hover {
            background-color: #5a6268 !important;
        }

        body.bg-dark .swal2-timer-progress-bar {
            background: #009fb1 !important;
        }

        body.bg-dark .swal2-icon.swal2-warning {
            border-color: #ffc107 !important;
            color: #ffc107 !important;
        }

        body.bg-dark .swal2-icon.swal2-success {
            border-color: #28a745 !important;
        }

        body.bg-dark .swal2-icon.swal2-success [class^='swal2-success-line'] {
            background-color: #28a745 !important;
        }

        body.bg-dark .swal2-icon.swal2-success .swal2-success-ring {
            border-color: rgba(40, 167, 69, 0.3) !important;
        }

        body.bg-dark .swal2-icon.swal2-error {
            border-color: #dc3545 !important;
        }

        body.bg-dark .swal2-icon.swal2-error [class^='swal2-x-mark-line'] {
            background-color: #dc3545 !important;
        }

        body.bg-dark .swal2-icon.swal2-question {
            border-color: #009fb1 !important;
            color: #009fb1 !important;
        }

        body.bg-dark .swal2-icon.swal2-info {
            border-color: #009fb1 !important;
            color: #009fb1 !important;
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
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            transform-origin: center;
        }
        
        .dropdown-arrow.rotate-180 {
            transform: translateY(-50%) rotate(180deg);
            transform-origin: center;
        }

        .sidebar.collapsed .dropdown-arrow {
            display: none;
        }

        .submenu {
            padding-left: 0;
            overflow-y: auto;
            max-height: 80vh;
            box-sizing: border-box;
            scrollbar-gutter: stable;
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
                <button class="btn btn-link text-muted" id="themeToggle" title="Toggle theme"><i
                        class="fas fa-moon"></i></button>

                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle user-dropdown-toggle"
                        id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2 flex-shrink-0"
                            style="width:32px;height:32px; overflow: hidden;">
                            @if(\App\Helpers\AuthHelper::user()->profile_picture)
                                <img src="{{ asset('storage/' . \App\Helpers\AuthHelper::user()->profile_picture) }}"
                                    alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                {{ substr(\App\Helpers\AuthHelper::user()->name, 0, 1) }}
                            @endif
                        </div>
                        <div class="fw-bold user-name-text text-nowrap">{{ \App\Helpers\AuthHelper::user()->name }}</div>
                    </a>
                    <style>
                        .dropdown-toggle::after {
                            transition: transform 0.2s ease-in-out;
                        }
                        .dropdown-toggle.show::after {
                            transform: rotate(180deg);
                        }
                        .dropdown-menu-profile {
                            min-width: 240px;
                            margin-top: 8px !important;
                            font-size: 0.9rem;
                            padding: 0;
                            overflow: hidden;
                        }

                        /* Dark Mode Support */
                        body.bg-dark .dropdown-menu-profile {
                            background-color: #1e2124;
                            border: 1px solid #2a2f35 !important;
                        }
                        body.bg-dark .dropdown-menu-profile .bg-light {
                            background-color: #1a1d1f !important;
                            border-bottom: 1px solid #2a2f35 !important;
                        }
                        body.bg-dark .dropdown-menu-profile .text-dark {
                            color: #e6e6e6 !important;
                        }
                        body.bg-dark .dropdown-menu-profile .text-muted {
                            color: #a0a0a0 !important;
                        }
                        body.bg-dark .dropdown-menu-profile .dropdown-item {
                            color: #e6e6e6;
                        }
                        body.bg-dark .dropdown-menu-profile .dropdown-item:hover {
                            background-color: rgba(255, 255, 255, 0.05);
                            color: #fff;
                        }

                        /* User Name Theme Adaptation */
                        .user-dropdown-toggle {
                            color: #343a40; /* Dark for Light Mode */
                            transition: color 0.3s;
                        }
                        .user-name-text {
                            color: #343a40;
                        }
                        
                        body.bg-dark .user-dropdown-toggle,
                        body.bg-dark .user-name-text {
                            color: #fff !important;
                        }
                    </style>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 dropdown-menu-profile" aria-labelledby="dropdownUser1">
                        <li class="px-3 py-3 border-bottom bg-light">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                    style="width:40px;height:40px; overflow: hidden;">
                                    @if(\App\Helpers\AuthHelper::user()->profile_picture)
                                        <img src="{{ asset('storage/' . \App\Helpers\AuthHelper::user()->profile_picture) }}"
                                            alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        {{ substr(\App\Helpers\AuthHelper::user()->name, 0, 1) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ \App\Helpers\AuthHelper::user()->name }}</div>
                                    <small class="text-muted d-block">
                                        @if(\App\Helpers\AuthHelper::user()->isSuperAdmin()) Super Admin
                                        @elseif(\App\Helpers\AuthHelper::user()->isAdmin()) Administrator
                                        @else Patient
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </li>
                        <li class="p-1">
                            <a class="dropdown-item rounded py-2 mb-1" href="{{ route('profile.edit') }}">
                                <i class="fas fa-user-circle me-2 text-muted"></i> My Profile
                            </a>
                            <a class="dropdown-item rounded py-2 text-danger" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); handleLogout();">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
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
        // Global function for logout with fade transition
        function handleLogout() {
            const overlay = document.getElementById('theme-transition-overlay');
            const isDark = document.body.classList.contains('bg-dark');
            
            if (overlay) {
                if (isDark) {
                    // For dark mode: fade to dark, then the app page will fade from dark to light
                    overlay.classList.add('dark');
                    overlay.classList.add('active');
                    
                    // Set a one-time flag in sessionStorage for the dark fade transition
                    sessionStorage.setItem('logout-from-dark', 'true');
                    // Clear theme preference so app page doesn't show dark on reload
                    localStorage.removeItem('app-theme');
                    
                    // Submit logout form after dark fade completes
                    setTimeout(() => {
                        document.getElementById('logout-form').submit();
                    }, 400);
                } else {
                    // For light mode: fade to light
                    overlay.classList.remove('dark');
                    overlay.classList.add('active');
                    
                    // Clear theme preference
                    localStorage.removeItem('app-theme');
                    
                    setTimeout(() => {
                        document.getElementById('logout-form').submit();
                    }, 400);
                }
            } else {
                // Fallback if overlay doesn't exist
                localStorage.removeItem('app-theme');
                document.getElementById('logout-form').submit();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Initialize theme transition overlay
            const themeOverlay = document.getElementById('theme-transition-overlay');
            const savedTheme = localStorage.getItem('app-theme');
            const isDark = savedTheme === 'dark';
            
            // Set overlay to match current theme on page load
            if (themeOverlay) {
                themeOverlay.classList.toggle('dark', isDark);
                // Fade out overlay after page loads
                setTimeout(() => {
                    themeOverlay.classList.remove('active');
                }, 100);
            }

            // Hide loading screen
            const loader = document.getElementById('page-loader');
            if (loader) {
                loader.classList.add('loaded');
                // Restore overflow
                document.documentElement.style.overflow = '';
                setTimeout(() => loader.remove(), 300);
            }

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

            const updateThemeIcon = (isDark) => {
                const icon = themeToggle?.querySelector('i');
                if (icon) {
                    if (isDark) {
                        icon.classList.remove('fa-moon');
                        icon.classList.add('fa-sun');
                    } else {
                        icon.classList.remove('fa-sun');
                        icon.classList.add('fa-moon');
                    }
                }
            };

            const applySavedTheme = () => {
                const saved = localStorage.getItem(themeKey);
                const isDark = saved === 'dark';
                document.body.classList.toggle('bg-dark', isDark);
                syncTableDark(isDark);
                updateThemeIcon(isDark);
            };

            applySavedTheme();

            // Theme toggle with confirmation and cooldown
            let lastThemeChange = 0;
            const THEME_COOLDOWN = 2000; // 2 seconds cooldown
            
            themeToggle?.addEventListener('click', () => {
                const now = Date.now();
                const timeSinceLastChange = now - lastThemeChange;
                
                // If within cooldown period, show warning
                if (timeSinceLastChange < THEME_COOLDOWN && lastThemeChange !== 0) {
                    Swal.fire({
                        title: 'Please Wait',
                        html: `Please wait ${Math.ceil((THEME_COOLDOWN - timeSinceLastChange) / 1000)} second(s) before switching theme again.<br><small class="text-muted">This prevents rapid flashing that may cause discomfort.</small>`,
                        icon: 'warning',
                        position: 'top-end',
                        toast: true,
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'swal-theme-warning'
                        }
                    });
                    return;
                }
                
                // Show confirmation dialog
                Swal.fire({
                    title: 'Switch Theme?',
                    text: 'Do you want to switch between light and dark mode?',
                    icon: 'question',
                    position: 'top-end',
                    toast: true,
                    showCancelButton: true,
                    confirmButtonText: 'Yes, switch',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#009fb1',
                    cancelButtonColor: '#6c757d',
                    timer: 5000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'swal-theme-confirm'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const willBeDark = !document.body.classList.contains('bg-dark');
                        
                        // Show fade overlay with current theme
                        if (themeOverlay) {
                            themeOverlay.classList.toggle('dark', !willBeDark);
                            themeOverlay.classList.add('active');
                        }
                        
                        // Wait for fade, then switch theme
                        setTimeout(() => {
                            const isDark = document.body.classList.toggle('bg-dark');
                            localStorage.setItem(themeKey, isDark ? 'dark' : 'light');
                            syncTableDark(isDark);
                            updateThemeIcon(isDark);
                            lastThemeChange = Date.now();
                            
                            // Update overlay to new theme and fade out
                            if (themeOverlay) {
                                themeOverlay.classList.toggle('dark', isDark);
                                setTimeout(() => {
                                    themeOverlay.classList.remove('active');
                                }, 50);
                            }
                            
                            // Show success message
                            Swal.fire({
                                title: `Switched to ${isDark ? 'Dark' : 'Light'} Mode`,
                                icon: 'success',
                                position: 'top-end',
                                toast: true,
                                showConfirmButton: false,
                                timer: 1500,
                                timerProgressBar: true
                            });
                        }, 200);
                    }
                });
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

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')
</body>

</html>