@extends('layouts.app')

@section('content')
<div class="position-relative w-100" style="min-height: 80vh;">
    <!-- Background with overlay -->
    <div style="
        background: linear-gradient(rgba(255,255,255,0.76), rgba(255,255,255,0.7)), url('{{ asset('images/clinic-bg.jpg') }}') center/cover no-repeat;
        position: absolute; left: 0; top: 0; width: 100%; height: 100%; z-index: 0;">
    </div>
    <div class="container position-relative" style="z-index: 1;">
        <div class="row justify-content-center align-items-start py-5">
            <!-- Contact Info Card -->
            <div class="col-lg-5 col-md-6 mb-4 mb-lg-0">
                <div class="bg-white rounded p-4 shadow-sm h-100">
                    <h2 class="fw-bold mb-4">Contact Us</h2>
                    <div class="mb-3">
                        <h6>Email</h6>
                        <a href="mailto:barangay.clinic@example.com">barangay12@malasakit.com</a>
                    </div>
                    <div class="mb-3">
                        <h6>Phone</h6>
                        <div>+63 955 123 4567</div>
                    </div>
                    <div class="mb-3">
                        <h6>Location</h6>
                        <div>Barangay Health Clinic<br>V. Mapa St Brgy 12, Davao City</div>
                    </div>
                    <div class="mb-3">
                        <h6>Clinic Hours</h6>
                        <div>Monday – Friday, 8:00 AM – 5:00 PM</div>
                    </div>
                </div>
            </div>
            <!-- Contact Form Card -->
            <div class="col-lg-7 col-md-6">
                <div class="bg-white rounded p-4 shadow-sm h-100">
                    <h5 class="mb-4 fw-semibold">Send us a message</h5>
                    <form>
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Your Name">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" placeholder="you@example.com">
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" rows="4" placeholder="Type your message here..."></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg mt-2">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
