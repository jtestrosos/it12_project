@extends('layouts.app')

@section('content')
<div class="position-relative w-100" style="min-height: 80vh;">
    <div style="
        background: linear-gradient(rgba(255,255,255,0.77), rgba(255,255,255,0.73)), url('{{ asset('images/clinic-bg.jpg') }}') center/cover no-repeat;
        position:absolute; left:0; top:0; width:100%; height:100%; z-index:0;">
    </div>
    <div class="container position-relative py-5" style="z-index:1;">
        <h2 class="fw-bold mb-5 text-center">Book an Appointment</h2>
        <div class="row justify-content-center g-4">
            <!-- Select Date Card -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 p-4">
                    <h6 class="fw-bold mb-3">Select Date</h6>
                    <input type="date" class="form-control mb-3" name="appointment_date">
                    <small class="text-muted">Services available Monday to Friday</small>
                </div>
            </div>
            <!-- Patient Info Card -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 p-4">
                    <h6 class="fw-bold mb-3">Patient Information</h6>
                    <input type="text" class="form-control mb-2" name="name" placeholder="Full Name">
                    <input type="text" class="form-control mb-2" name="phone" placeholder="Phone">
                    <input type="text" class="form-control mb-2" name="address" placeholder="Address">
                    <button class="btn btn-primary w-100 mt-3">Confirm Appointment</button>
                </div>
            </div>
            <!-- Weekly Service Schedule Card -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 p-4">
                    <h6 class="fw-bold mb-3">Weekly Service Schedule</h6>
                    <ul class="list-unstyled mb-0">
                        <li>Monday: General Checkup</li>
                        <li>Tuesday: Prenatal</li>
                        <li>Wednesday: Medical Check-up</li>
                        <li>Thursday: Immunization</li>
                        <li>Friday: Family Planning</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Time Slot Selection Card -->
        <div class="row justify-content-center mt-5">
            <div class="col-md-8">
                <div class="card shadow-sm border-0 p-4 text-center">
                    <h6 class="mb-3">Select Time Slot</h6>
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <button class="btn btn-outline-dark">8:00 AM</button>
                        <button class="btn btn-outline-dark">9:00 AM</button>
                        <button class="btn btn-outline-dark">10:00 AM</button>
                        <button class="btn btn-outline-dark">11:00 AM</button>
                        <button class="btn btn-outline-dark">1:00 PM</button>
                        <button class="btn btn-outline-dark">2:00 PM</button>
                        <button class="btn btn-outline-dark">3:00 PM</button>
                        <button class="btn btn-outline-dark">4:00 PM</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
