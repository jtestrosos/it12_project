<!-- resources/views/book-appointment.blade.php -->
@extends('layouts.app')
@section('content')
<div class="container py-5">
    <h3>Book Your Appointment</h3>
    <div class="row">
        <!-- Calendar -->
        <div class="col-md-4 mb-4">
            <div class="card p-3">
                <h6>Select Date</h6>
                <!-- Use a JS calendar like Flatpickr, or blade for manual selection -->
                <input type="date" class="form-control" name="appointment_date">
                <small class="text-muted">Services are available Monday to Friday</small>
            </div>
        </div>
        <!-- Patient Information -->
        <div class="col-md-4 mb-4">
            <div class="card p-3">
                <h6>Patient Information</h6>
                <input type="text" class="form-control mb-2" name="name" placeholder="Full Name">
                <input type="text" class="form-control mb-2" name="phone" placeholder="Phone">
                <input type="text" class="form-control mb-2" name="address" placeholder="Address">
                <!-- Booking Summary -->
                <!-- Fill via JS or after timeslot/service selection -->
                <div class="mt-3 p-2 bg-light" style="border-radius:6px">
                    <strong>Booking Summary</strong>
                    <div>Service: Medical Check-up</div>
                    <div>Date: ...</div>
                    <div>Time: ...</div>
                </div>
                <button class="btn btn-primary mt-3 w-100">Confirm Appointment</button>
            </div>
        </div>
        <!-- Weekly Schedule -->
        <div class="col-md-4 mb-4">
            <div class="card p-3">
                <h6>Weekly Service Schedule</h6>
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
    <!-- Time Slot Selection; reveal after service/date pick -->
    <div class="row mt-4">
        <div class="col-md-12">
            <h6>Select Time Slot</h6>
            <div class="btn-group flex-wrap">
                <button class="btn btn-outline-dark m-1">8:00 AM</button>
                <button class="btn btn-outline-dark m-1">9:00 AM</button>
                <!-- Continue for all available slots -->
            </div>
        </div>
    </div>
</div>
@endsection
