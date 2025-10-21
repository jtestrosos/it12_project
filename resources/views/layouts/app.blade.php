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
            background: #f7fafd;
        }
        .navbar {
            background: linear-gradient(90deg, #36d1c4 0%, #64eaf8 100%);
            min-height: 80px;
        }
        .navbar-brand img {
            height: 80px;
            margin-right: 1.2rem;
            vertical-align: middle;
        }
        .navbar-brand span {
            font-size: 2.2rem;
            letter-spacing: 2px;
            vertical-align: middle;
        }
        .navbar-nav .nav-link {
            font-size: 1.15rem;
            margin: 0 2rem;
            padding-bottom: 7px;
            font-weight: 500;
            border-bottom: 2px solid transparent;
            transition: color 0.2s, border-bottom 0.2s, background 0.2s;
        }
        .navbar-nav .nav-link:hover, 
        .navbar-nav .nav-link:focus {
            color: #007bff !important;
            border-bottom: 2px solid #007bff;
            background: rgba(255,255,255,0.06);
        }
        .navbar-nav .nav-link.active, 
        .navbar-nav .nav-link.fw-bold {
            font-weight: bold;
            border-bottom: 2px solid #007bff !important;
            color: #222 !important;
        }
        @media (max-width: 992px) {
            .navbar-brand img {
                height: 60px;
            }
            .navbar-brand span {
                font-size: 1.3rem;
            }
            .navbar-nav .nav-link {
                margin: 0 0.7rem;
                font-size: 1rem;
                padding-bottom: 4px;
            }
        }
        footer {
            background: linear-gradient(90deg, #e0f7fa 0%, #b2ebf2 100%);
        }
    </style>
    @stack('styles')
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light shadow-sm">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <img src="{{ asset('images/malasakit-logo.png') }}" alt="Malasakit Logo">
                    <span class="fw-bold text-white">MALASAKIT</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false">
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
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main>
        @yield('content')
    </main>
    <footer class="text-center text-muted py-4 mt-auto">
        <div class="container small">
            Barangay Health Clinic &copy; {{ date('Y') }}. All rights reserved.
            <span class="mx-2">|</span>
            <a href="{{ url('/privacy') }}" class="text-decoration-none text-muted">Privacy Policy</a>
            <span class="mx-2">|</span>
            <a href="{{ url('/terms') }}" class="text-decoration-none text-muted">Terms of Service</a>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
