@extends('layouts.app')

@section('content')
<div class="position-relative w-100" style="min-height: 80vh;">
    <div style="
        background: linear-gradient(rgba(255,255,255,0.74), rgba(255,255,255,0.68)), url('{{ asset('images/clinic-bg.jpg') }}') center/cover no-repeat;
        position:absolute; left:0; top:0; width:100%; height:100%; z-index:0;">
    </div>
    <div class="container position-relative text-center py-5" style="z-index:1;">
        <h2 class="fw-bold mb-4">How It Works</h2>
        <p class="mb-5">Book your appointment in just 4 simple steps</p>
        <div class="row justify-content-center">
            <div class="col-md-3 mb-3">
                <div class="p-3">
                    <i class="fas fa-user-circle fa-2x text-primary mb-2"></i>
                    <div class="fw-bold">Create Your Account</div>
                    <small>Register with your info and barangay details</small>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="p-3">
                    <i class="fas fa-calendar-alt fa-2x text-primary mb-2"></i>
                    <div class="fw-bold">Choose Date & Time</div>
                    <small>Select your preferred appointment slot</small>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="p-3">
                    <i class="fas fa-notes-medical fa-2x text-primary mb-2"></i>
                    <div class="fw-bold">Select Service</div>
                    <small>Pick the service you need from our list</small>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="p-3">
                    <i class="fas fa-check-circle fa-2x text-primary mb-2"></i>
                    <div class="fw-bold">Confirm Booking</div>
                    <small>Get confirmation and reminders</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
