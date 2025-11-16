@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<div class="position-relative w-100" style="min-height: 100vh;">
    <!-- Background overlay -->
    <div style="
        background: linear-gradient(rgba(255,255,255,0.66), rgba(255,255,255,0.62)), 
        url('{{ asset('images/clinic-bg.jpg') }}') center/cover no-repeat;
        position:absolute; left:0; top:0; width:100%; height:100%; z-index:0;">
    </div>

    <div class="container position-relative" style="z-index:1;">
        <div class="row py-5 align-items-center justify-content-md-start" style="min-height: 80vh;">
            <div class="col-md-7 col-lg-6">
                <h1 class="fw-bold mb-3">Easy Online Booking for Your Healthcare Needs</h1>
                <p class="lead mb-4">Skip the long queues and book your appointment online. Access quality healthcare services right in your barangay with just a few clicks.</p>


                
                <!--  Button triggers login modal if not logged in -->
                @auth
                    @if(Auth::user()->isPatient())
                        <a href="{{ route('patient.book-appointment') }}" class="btn btn-primary btn-lg me-2 mb-2">Book Appointment Now</a>
                    @else
                        <a href="{{ Auth::user()->isSuperAdmin() ? route('superadmin.dashboard') : route('admin.dashboard') }}" class="btn btn-primary btn-lg me-2 mb-2">Go to Dashboard</a>
                    @endif
                @else
                    <button class="btn btn-primary btn-lg me-2 mb-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                        Book Appointment Now
                    </button>
                @endauth

                <a href="{{ route('services') }}" class="btn btn-outline-primary btn-lg mb-2">View Services</a>

                <div class="mt-4">
                    <span class="me-4"><strong>609+</strong> Patients Served</span>
                    <span class="me-4"><strong>98%</strong> Satisfaction Rate</span>
                    <span><strong>24/7</strong> Online Support</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!--  HOW IT WORKS SECTION -->
<section class="py-5 bg-light">
    <div class="container text-center">
        <h2 class="fw-bold mb-4">How It Works</h2>
        <p class="text-muted mb-5">We’ve made healthcare booking simple and fast — just follow these easy steps.</p>

        <div class="row g-4">
            <!-- Step 1 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card border-0 shadow-sm h-100 hover-scale">
                    <div class="card-body">
                        <i class="bi bi-person-plus fs-1 text-primary mb-3"></i>
                        <h5 class="fw-bold mb-2">1. Create an Account</h5>
                        <p class="text-muted">Sign up using your email to start booking appointments and manage your visits online.</p>
                    </div>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card border-0 shadow-sm h-100 hover-scale">
                    <div class="card-body">
                        <i class="bi bi-heart-pulse fs-1 text-danger mb-3"></i>
                        <h5 class="fw-bold mb-2">2. Choose a Service</h5>
                        <p class="text-muted">Select the medical service you need from our list of available healthcare offerings.</p>
                    </div>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card border-0 shadow-sm h-100 hover-scale">
                    <div class="card-body">
                        <i class="bi bi-calendar-check fs-1 text-success mb-3"></i>
                        <h5 class="fw-bold mb-2">3. Book & Confirm</h5>
                        <p class="text-muted">Pick a convenient date and time — then receive instant confirmation of your appointment.</p>
                    </div>
                </div>
            </div>
        </div>

        <a href="{{ route('how-it-works') }}" class="btn btn-outline-secondary mt-5">
            Learn More
        </a>
    </div>
</section>

<!-- Optional Custom Styling -->
<style>
.hover-scale {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-scale:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}
</style>

<!--  Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<!--  Optional: AOS (Animate On Scroll) for smooth fade-ins -->
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true });
</script>
@endsection
