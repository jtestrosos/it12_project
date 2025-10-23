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
                window.location.href = "{{ route('appointments.create') }}";
            @else
                var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                loginModal.show();
            @endif
        }
    </script>
</body>
</html>
