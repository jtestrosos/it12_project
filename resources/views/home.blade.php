@extends('layouts.app')

@section('content')
<div class="position-relative w-100" style="min-height: 100vh;">
    <!-- Overlay for better text legibility -->
    <div style="
        background: linear-gradient(rgba(255,255,255,0.66), rgba(255,255,255,0.62)), url('{{ asset('images/clinic-bg.jpg') }}') center/cover no-repeat;
        position:absolute; left:0; top:0; width:100%; height:100%; z-index:0;">
    </div>
    <div class="container position-relative" style="z-index:1;">
        <div class="row py-5 align-items-center justify-content-md-start" style="min-height: 80vh;">
            <div class="col-md-7 col-lg-6">
                <h1 class="fw-bold mb-3">Easy Online Booking for Your Healthcare Needs</h1>
                <p class="lead mb-4">Skip the long queues and book your appointment online. Access quality healthcare services right in your barangay with just a few clicks.</p>
                <a href="{{ url('/booking') }}" class="btn btn-primary btn-lg me-2 mb-2">Book Appointment Now</a>
                <a href="{{ url('/services') }}" class="btn btn-outline-primary btn-lg mb-2">View Services</a>
                <div class="mt-4">
                    <span class="me-4"><strong>609+</strong> Patients Served</span>
                    <span class="me-4"><strong>98%</strong> Satisfaction Rate</span>
                    <span><strong>24/7</strong> Online Support</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
