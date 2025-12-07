@extends('layouts.app')

@section('content')
<div class="position-relative w-100" style="min-height: 80vh;">
  <div style="
    background: linear-gradient(rgba(255,255,255,0.74), rgba(255,255,255,0.68)), url('{{ asset('images/clinic-bg.jpg') }}') center/cover no-repeat;
    position:absolute; left:0; top:0; width:100%; height:100%; z-index:0;">
  </div>
  <div class="container position-relative text-center py-5" style="z-index:1;">
    <h2 class="fw-bold mb-4" style="font-size:2.75rem;">How It Works</h2>
    <p class="mb-5" style="font-size:1.25rem;">Book your appointment in just <span class="text-primary fw-bold">4 simple steps</span></p>
    <div class="row justify-content-center">
      <!-- Step 1 -->
      <div class="col-md-3 mb-4 d-flex align-items-stretch">
        <div class="card shadow-lg border-0 h-100 w-100" style="background:rgba(255,255,255,0.94);">
          <div class="card-body d-flex flex-column align-items-center justify-content-center">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mb-3" style="width:94px;height:94px;font-size:3.5rem;">
              <i class="fas fa-user-circle"></i>
            </div>
            <span class="fw-bold mb-2" style="font-size:1.4rem;">Create Your Account</span>
            <small style="font-size:1.08rem;">Register with your info and barangay details</small>
          </div>
        </div>
      </div>
      <!-- Step 2 -->
      <div class="col-md-3 mb-4 d-flex align-items-stretch">
        <div class="card shadow-lg border-0 h-100 w-100" style="background:rgba(255,255,255,0.94);">
          <div class="card-body d-flex flex-column align-items-center justify-content-center">
            <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center mb-3" style="width:94px;height:94px;font-size:3.5rem;">
              <i class="fas fa-calendar-alt"></i>
            </div>
            <span class="fw-bold mb-2" style="font-size:1.4rem;">Choose Date & Time</span>
            <small style="font-size:1.08rem;">Select your preferred appointment slot</small>
          </div>
        </div>
      </div>
      <!-- Step 3 -->
      <div class="col-md-3 mb-4 d-flex align-items-stretch">
        <div class="card shadow-lg border-0 h-100 w-100" style="background:rgba(255,255,255,0.94);">
          <div class="card-body d-flex flex-column align-items-center justify-content-center">
            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mb-3" style="width:94px;height:94px;font-size:3.5rem;">
              <i class="fas fa-notes-medical"></i>
            </div>
            <span class="fw-bold mb-2" style="font-size:1.4rem;">Select Service</span>
            <small style="font-size:1.08rem;">Pick the service you need from our list</small>
          </div>
        </div>
      </div>
      <!-- Step 4 -->
      <div class="col-md-3 mb-4 d-flex align-items-stretch">
        <div class="card shadow-lg border-0 h-100 w-100" style="background:rgba(255,255,255,0.94);">
          <div class="card-body d-flex flex-column align-items-center justify-content-center">
            <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center mb-3" style="width:94px;height:94px;font-size:3.5rem;">
              <i class="fas fa-check-circle"></i>
            </div>
            <span class="fw-bold mb-2" style="font-size:1.4rem;">Confirm Booking</span>
            <small style="font-size:1.08rem;">Get confirmation and reminders</small>
          </div>
        </div>
      </div>
      
      <!-- Call to Action Section -->
      <div class="row justify-content-center mt-5 pt-4">
        <div class="col-md-8 text-center">
          <h3 class="fw-bold mb-3" style="font-size:2rem;">Ready to Get Started?</h3>
          <p class="mb-4" style="font-size:1.15rem; color:#555;">Join us today and experience hassle-free appointment booking!</p>
          <a href="{{ route('login') }}" class="btn btn-lg px-5 py-3 cta-button" style="
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            border: none;
            border-radius: 50px;
            color: white;
            font-size: 1.25rem;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.3);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
          ">
            <i class="fas fa-sign-in-alt"></i>
            Start Your Journey Now!
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .cta-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(13, 110, 253, 0.4);
    background: linear-gradient(135deg, #0a58ca 0%, #084298 100%);
  }
  
  .cta-button:active {
    transform: translateY(-1px);
  }
</style>

@endsection
