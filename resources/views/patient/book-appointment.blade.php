@extends('layouts.admin')

@section('title', 'Book Appointment - Patient Portal')
@section('page-title', 'Book Appointment')
@section('page-description', 'Schedule your healthcare visit')

@section('sidebar-menu')
    <a class="nav-link @if(request()->routeIs('patient.dashboard')) active @endif" href="{{ route('patient.dashboard') }}"
        data-tooltip="Dashboard">
        <i class="fas fa-th-large"></i> <span class="sidebar-content">Dashboard</span>
    </a>
    <a class="nav-link @if(request()->routeIs('patient.appointments') || request()->routeIs('patient.appointment.show')) active @endif"
        href="{{ route('patient.appointments') }}" data-tooltip="My Appointments">
        <i class="fas fa-calendar"></i> <span class="sidebar-content">My Appointments</span>
    </a>
    <a class="nav-link @if(request()->routeIs('patient.book-appointment')) active @endif"
        href="{{ route('patient.book-appointment') }}" data-tooltip="Book Appointment">
        <i class="fas fa-plus"></i> <span class="sidebar-content">Book Appointment</span>
    </a>
@endsection

@section('user-initials')
    {{ substr(optional(\App\Helpers\AuthHelper::user())->name ?? 'Gu', 0, 2) }}
@endsection

@section('user-name')
    {{ optional(\App\Helpers\AuthHelper::user())->name ?? 'Guest' }}
@endsection

@section('user-role')
    Patient
@endsection

@section('page-styles')
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .booking-card {
            background: #fafafa;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
        }

        body.bg-dark .booking-card {
            background: #1e2124;
            border-color: #2a2f35;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .booking-header {
            background: linear-gradient(135deg, #009fb1, #008a9a);
            border-radius: 12px 12px 0 0;
            padding: 1.5rem;
        }

        .booking-body {
            padding: 2rem;
        }

        .form-section {
            background: #f5f5f5;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e9ecef;
        }

        body.bg-dark .form-section {
            background: #25282c;
            border-color: #2a2f35;
        }

        .form-section h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        body.bg-dark .form-section h6 {
            color: #e6e6e6;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #e9ecef;
            padding: 0.75rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            background: #ffffff;
        }

        body.bg-dark .form-control {
            background: #0f1316;
            border-color: #2a2f35;
            color: #e6e6e6;
        }

        .form-control:focus {
            border-color: #009fb1;
            box-shadow: 0 0 0 0.2rem rgba(0, 159, 177, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        body.bg-dark .form-label {
            color: #e6e6e6;
        }

        .btn-primary {
            background: linear-gradient(135deg, #009fb1, #008a9a);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 2rem;
            font-weight: 500;
        }

        .btn-secondary {
            border-radius: 8px;
            padding: 0.75rem 2rem;
            font-weight: 500;
        }

        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875rem;
            color: #dc3545;
        }

        .is-invalid {
            border-color: #dc3545;
        }

        /* Calendar Styles */
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
            font-size: 0.8rem;
        }

        .calendar-header {
            text-align: center;
            font-weight: 600;
            padding: 0.5rem;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #dee2e6;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .calendar-day:hover {
            background-color: #e9ecef;
        }

        .calendar-day.selected {
            background-color: #009fb1;
            color: white;
            border-color: #009fb1;
        }

        .calendar-day.occupied {
            background-color: #F53838;
            color: #000;
            border-color: #F53838;
        }

        .calendar-day.partially-occupied {
            background-color: #FFF52E;
            color: #000;
            border-color: #FFF52E;
        }

        /* Selected state takes priority over partially-occupied */
        .calendar-day.partially-occupied.selected {
            background-color: #009fb1;
            color: white;
            border-color: #009fb1;
        }

        .calendar-day.weekend {
            background-color: #f8f9fa;
            color: #6c757d;
        }

        .calendar-day.past {
            background-color: #e9ecef;
            color: #adb5bd;
            cursor: not-allowed;
        }

        .calendar-day .slot-indicator {
            position: absolute;
            bottom: 2px;
            right: 2px;
            font-size: 0.6rem;
            background: rgba(0, 0, 0, 0.1);
            padding: 1px 3px;
            border-radius: 2px;
        }

        .time-slots-container {
            background: #ffffff;
            border: 1px solid #e9ecef !important;
            max-height: 500px;
        }

        body.bg-dark .time-slots-container {
            background: #1e2124 !important;
            border-color: #2a2f35 !important;
        }

        .time-slots-grid-wrapper {
            max-height: 400px;
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 0.5rem;
        }

        .time-slots-grid-wrapper::-webkit-scrollbar {
            width: 8px;
        }

        .time-slots-grid-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        body.bg-dark .time-slots-grid-wrapper::-webkit-scrollbar-track {
            background: #2a2f35;
        }

        .time-slots-grid-wrapper::-webkit-scrollbar-thumb {
            background: #009fb1;
            border-radius: 4px;
        }

        .time-slots-grid-wrapper::-webkit-scrollbar-thumb:hover {
            background: #008a9a;
        }

        .time-slots-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }

        .time-slot {
            padding: 0.75rem;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .time-slot.available {
            background-color: #77dd77;
            border-color: #66cc66;
            color: #000;
        }

        .time-slot.available:hover {
            background-color: #66cc66;
        }

        .time-slot.occupied {
            background-color: #F53838;
            border-color: #e62929;
            color: #000;
            cursor: not-allowed;
        }

        .time-slot.past {
            background-color: #e9ecef;
            border-color: #dee2e6;
            color: #6c757d;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .time-slot.selected {
            background-color: #009fb1;
            border-color: #009fb1;
            color: white;
        }

        .time-slot .time {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .time-slot .status {
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        body.bg-dark .calendar-header {
            background-color: #2a2f35;
            color: #e6e6e6;
            border-color: #2a2f35;
        }

        body.bg-dark .calendar-day {
            border-color: #2a2f35;
            color: #e6e6e6;
        }

        body.bg-dark .calendar-day:hover {
            background-color: #2a2f35;
        }

        body.bg-dark .calendar-day.weekend {
            background-color: #1e2124;
            color: #6c757d;
        }

        body.bg-dark .calendar-day.past {
            background-color: #1e2124;
            color: #6c757d;
        }

        body.bg-dark .time-slot {
            border-color: #2a2f35;
        }

        body.bg-dark .time-slot.available {
            background-color: #77dd77;
            border-color: #66cc66;
            color: #000;
        }

        body.bg-dark .time-slot.occupied {
            background-color: #F53838;
            border-color: #e62929;
            color: #000;
        }

        body.bg-dark .time-slot.past {
            background-color: #1e2124;
            border-color: #2a2f35;
            color: #6c757d;
            opacity: 0.5;
        }

        /* Dark Mode Form Controls */
        body.bg-dark select.form-control,
        body.bg-dark textarea.form-control {
            background-color: #0f1316;
            border-color: #2a2f35;
            color: #e6e6e6;
        }

        body.bg-dark select.form-control:focus,
        body.bg-dark textarea.form-control:focus {
            background-color: #161b20;
            border-color: #009fb1;
            color: #e6e6e6;
        }

        body.bg-dark option {
            background-color: #1e2124;
            color: #e6e6e6;
        }

        /* Dark Mode Calendar Navigation & Components */
        body.bg-dark #prevMonth,
        body.bg-dark #nextMonth {
            color: #e6e6e6;
            border-color: #009fb1;
        }

        body.bg-dark #prevMonth:hover,
        body.bg-dark #nextMonth:hover {
            background-color: #009fb1;
            color: #fff;
        }

        body.bg-dark #currentMonth,
        body.bg-dark #selectedDateDisplay {
            color: #e6e6e6;
        }

        /* Dark Mode Time Slots Container */
        body.bg-dark .border.rounded {
            border-color: #2a2f35 !important;
            background-color: #1e2124 !important;
        }

        /* Dark Mode Inner Cards */
        body.bg-dark .card {
            background-color: #1e2124;
            border-color: #2a2f35;
        }

        body.bg-dark .card-header:not(.bg-primary) {
            background-color: #2a2f35;
            border-bottom-color: #343a40;
        }

        body.bg-dark .card-header h6 {
            color: #e6e6e6;
        }

        /* Dark Mode Calendar Grid Refinements */
        body.bg-dark .calendar-day.selected {
            background-color: #009fb1;
            border-color: #009fb1;
            color: #fff;
        }

        body.bg-dark .calendar-day.occupied {
            background-color: #F53838;
            border-color: #e62929;
            color: #000;
        }

        body.bg-dark .calendar-day.partially-occupied {
            background-color: #FFF52E;
            border-color: #ffe61f;
            color: #000;
        }

        /* Selected state takes priority in dark mode too */
        body.bg-dark .calendar-day.partially-occupied.selected {
            background-color: #009fb1;
            border-color: #009fb1;
            color: #fff;
        }

        /* Dark Mode Time Slots */
        body.bg-dark .time-slot.selected {
            background-color: #009fb1;
            border-color: #009fb1;
            color: #fff;
        }

        /* Step Indicator */
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }

        .step-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e9ecef;
            z-index: 0;
        }

        body.bg-dark .step-indicator::before {
            background: #2a2f35;
        }

        .step {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: 2px solid #e9ecef;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .step.active .step-circle {
            background: #009fb1;
            border-color: #009fb1;
            color: white;
            box-shadow: 0 0 0 4px rgba(0, 159, 177, 0.2);
        }

        .step.completed .step-circle {
            background: #28a745;
            border-color: #28a745;
            color: white;
        }

        .step-label {
            font-size: 0.875rem;
            color: #6c757d;
            font-weight: 500;
        }

        .step.active .step-label {
            color: #009fb1;
            font-weight: 600;
        }

        .step.completed .step-label {
            color: #28a745;
        }

        body.bg-dark .step-circle {
            background: #1e2124;
            border-color: #2a2f35;
            color: #e6e6e6;
        }

        body.bg-dark .step-label {
            color: #b0b0b0;
        }

        /* Calendar Legend */
        .calendar-legend {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        body.bg-dark .calendar-legend {
            background: #25282c;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #495057;
        }

        body.bg-dark .legend-item {
            color: #e6e6e6;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        body.bg-dark .legend-color {
            border-color: rgba(255, 255, 255, 0.2);
        }

        .legend-color.available {
            background: #77dd77;
            border-color: #66cc66;
        }

        body.bg-dark .legend-color.available {
            background: #77dd77;
            border-color: #66cc66;
        }

        .legend-color.partially-occupied {
            background: #FFF52E;
        }

        .legend-color.occupied {
            background: #F53838;
        }

        .legend-color.selected {
            background: #009fb1;
        }

        .legend-color.unavailable {
            background: white;
            border-color: #dee2e6;
        }

        body.bg-dark .legend-color.unavailable {
            background: #1e2124;
            border-color: #495057;
        }

        /* Confirmation Modal */
        .modal-content {
            border-radius: 16px;
            border: none;
        }

        .modal-header {
            border-bottom: 1px solid #e9ecef;
            padding: 1.5rem;
        }

        body.bg-dark .modal-content {
            background: #1e2124;
            color: #e6e6e6;
        }

        body.bg-dark .modal-header {
            border-bottom-color: #2a2f35;
        }

        .confirmation-detail {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f3f4;
        }

        .confirmation-detail:last-child {
            border-bottom: none;
        }

        body.bg-dark .confirmation-detail {
            border-bottom-color: #2a2f35;
        }

        .confirmation-label {
            font-weight: 600;
            color: #495057;
        }

        body.bg-dark .confirmation-label {
            color: #cbd3da;
        }

        .confirmation-value {
            color: #6c757d;
        }

        body.bg-dark .confirmation-value {
            color: #b0b0b0;
        }

        /* Dark Mode Form Check Labels */
        body.bg-dark .form-check-label {
            color: #e6e6e6;
        }

        /* Dark Mode Text Utilities */
        body.bg-dark .text-muted {
            color: #b0b0b0 !important;
        }

        body.bg-dark .text-muted.small.fw-bold,
        body.bg-dark h6.text-muted.small.fw-bold {
            color: #cbd3da !important;
        }

        /* Responsive adjustments */
        @media (max-width: 1199px) {
            .col-xl-6 {
                width: 100%;
                margin-bottom: 1rem;
            }
        }
        
        @media (max-width: 768px) {
            .time-slots-grid {
                grid-template-columns: 1fr;
            }
            
            .calendar-grid {
                font-size: 0.75rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Step Indicator -->
            <div class="step-indicator mb-4">
                <div class="step active" data-step="1">
                    <div class="step-circle">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="step-label">Patient Info</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-circle">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="step-label">Date & Time</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-circle">
                        <i class="fas fa-notes-medical"></i>
                    </div>
                    <div class="step-label">Details</div>
                </div>
                <div class="step" data-step="4">
                    <div class="step-circle">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="step-label">Confirm</div>
                </div>
            </div>

            <div class="card booking-card">
                <div class="booking-header text-white">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-calendar-plus fs-3"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">Book an Appointment</h4>
                            <p class="mb-0 opacity-75">Schedule your healthcare visit</p>
                        </div>
                    </div>
                </div>
                <div class="booking-body">
                    <form method="POST" action="{{ route('patient.store-appointment') }}">
                        @csrf

                        <!-- Patient Information -->
                        <div class="form-section">
                            <h6><i class="fas fa-user me-2"></i>Patient Information</h6>
                            <p class="text-muted small mb-3">
                                <i class="fas fa-info-circle me-1"></i>
                                Your information has been pre-filled from your account. You can modify any details as
                                needed.
                            </p>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="patient_name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control @error('patient_name') is-invalid @enderror"
                                        id="patient_name" name="patient_name"
                                        value="{{ old('patient_name', $user->name ?? '') }}" required>
                                    @error('patient_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="patient_phone" class="form-label">Phone Number *</label>
                                    <input type="tel" class="form-control @error('patient_phone') is-invalid @enderror"
                                        id="patient_phone" name="patient_phone"
                                        value="{{ old('patient_phone', $user->phone ?? '') }}" required>
                                    @error('patient_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Appointment Details -->
                        <div class="form-section">
                            <h6><i class="fas fa-calendar me-2"></i>Appointment Details</h6>

                            <!-- Calendar Legend -->
                            <div class="calendar-legend">
                                <div class="legend-item">
                                    <div class="legend-color available"></div>
                                    <span>Available</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color occupied"></div>
                                    <span>Fully Booked</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color partially-occupied"></div>
                                    <span>Limited Slots</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color selected"></div>
                                    <span>Selected</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color unavailable"></div>
                                    <span>Unavailable</span>
                                </div>
                            </div>

                            <!-- Calendar View -->
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Select Appointment Date & Time
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-4">
                                        <!-- Calendar Column -->
                                        <div class="col-xl-6 col-lg-6 col-md-12">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="prevMonth">
                                                    <i class="fas fa-chevron-left"></i>
                                                </button>
                                                <h6 class="mb-0" id="currentMonth">Loading...</h6>
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="nextMonth">
                                                    <i class="fas fa-chevron-right"></i>
                                                </button>
                                            </div>
                                            <div id="calendarGrid" class="calendar-grid">
                                                <!-- Calendar will be populated here -->
                                            </div>
                                        </div>
                                        
                                        <!-- Time Slots Column -->
                                        <div class="col-xl-6 col-lg-6 col-md-12">
                                            <div class="border rounded p-3 time-slots-container">
                                                <h6 class="mb-3 pb-2 border-bottom" id="selectedDateDisplay">
                                                    Select a date to view available time slots
                                                </h6>
                                                <div id="timeSlotsGrid" class="time-slots-grid-wrapper">
                                                    <!-- Time slots will be populated here -->
                                                </div>
                                                <input type="hidden" id="appointment_date" name="appointment_date"
                                                    value="{{ old('appointment_date') }}" required>
                                                <input type="hidden" id="appointment_time" name="appointment_time"
                                                    value="{{ old('appointment_time') }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <label for="service_id" class="form-label">Service Needed *</label>
                                <select class="form-control @error('service_id') is-invalid @enderror" id="service_id"
                                    name="service_id" required>
                                    <option value="" disabled selected>Select Service</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('service_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="form-section">
                            <h6><i class="fas fa-notes-medical me-2"></i>Additional Information</h6>
                            <div class="mb-3">
                                <label for="medical_history" class="form-label">Medical History</label>
                                <textarea class="form-control @error('medical_history') is-invalid @enderror"
                                    id="medical_history" name="medical_history" rows="3"
                                    placeholder="Please provide any relevant medical history...">{{ old('medical_history') }}</textarea>
                                @error('medical_history')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
                                    rows="2"
                                    placeholder="Any additional information or special requests...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @if($user->age >= 6)
                            <!-- Treatment Record Section -->
                            <div class="form-section">
                                <h6><i class="fas fa-file-medical me-2"></i>Individual Treatment Record</h6>
                                <p class="text-muted small mb-3">
                                    <i class="fas fa-info-circle me-1"></i>
                                    This information helps us provide better healthcare. Fields are optional and auto-filled
                                    from your previous records.
                                </p>

                                <!-- Demographics -->
                                <div class="mb-4">
                                    <h6 class="text-muted small fw-bold">Family Information</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="mother_name" class="form-label">Mother's Name</label>
                                            <input type="text" class="form-control" id="mother_name" name="mother_name"
                                                value="{{ old('mother_name', $user->mother_name) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="father_name" class="form-label">Father's Name</label>
                                            <input type="text" class="form-control" id="father_name" name="father_name"
                                                value="{{ old('father_name', $user->father_name) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="religion" class="form-label">Religion</label>
                                            <input type="text" class="form-control" id="religion" name="religion"
                                                value="{{ old('religion', $user->religion) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="marital_status" class="form-label">Marital Status</label>
                                            <select class="form-control" id="marital_status" name="marital_status">
                                                <option value="">Select Status</option>
                                                <option value="single" {{ old('marital_status', $user->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                                                <option value="married" {{ old('marital_status', $user->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                                                <option value="widowed" {{ old('marital_status', $user->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                                <option value="separated" {{ old('marital_status', $user->marital_status) == 'separated' ? 'selected' : '' }}>Separated</option>
                                                <option value="co-habitation" {{ old('marital_status', $user->marital_status) == 'co-habitation' ? 'selected' : '' }}>Co-habitation
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="educational_attainment" class="form-label">Educational
                                                Attainment</label>
                                            <input type="text" class="form-control" id="educational_attainment"
                                                name="educational_attainment"
                                                value="{{ old('educational_attainment', $user->educational_attainment) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="occupation" class="form-label">Occupation</label>
                                            <input type="text" class="form-control" id="occupation" name="occupation"
                                                value="{{ old('occupation', $user->occupation) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="accompanying_person" class="form-label">Accompanying Person</label>
                                            <input type="text" class="form-control" id="accompanying_person"
                                                name="accompanying_person"
                                                value="{{ old('accompanying_person', $user->accompanying_person) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="accompanying_relationship" class="form-label">Relationship</label>
                                            <input type="text" class="form-control" id="accompanying_relationship"
                                                name="accompanying_relationship"
                                                value="{{ old('accompanying_relationship', $user->accompanying_relationship) }}">
                                        </div>
                                    </div>

                                    <!-- Spouse Information (shown if married) -->
                                    <div id="spouseInfo" class="mt-3"
                                        style="display: {{ old('marital_status', $user->marital_status) == 'married' ? 'block' : 'none' }}">
                                        <h6 class="text-muted small fw-bold mt-3">Spouse Information</h6>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label for="spouse_name" class="form-label">Spouse Name</label>
                                                <input type="text" class="form-control" id="spouse_name" name="spouse_name"
                                                    value="{{ old('spouse_name', $user->spouse_name) }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="spouse_age" class="form-label">Spouse Age</label>
                                                <input type="number" class="form-control" id="spouse_age" name="spouse_age"
                                                    value="{{ old('spouse_age', $user->spouse_age) }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="spouse_occupation" class="form-label">Spouse Occupation</label>
                                                <input type="text" class="form-control" id="spouse_occupation"
                                                    name="spouse_occupation"
                                                    value="{{ old('spouse_occupation', $user->spouse_occupation) }}">
                                            </div>
                                            @if($user->gender == 'female')
                                                <div class="col-md-12">
                                                    <label for="maiden_name" class="form-label">Maiden Name</label>
                                                    <input type="text" class="form-control" id="maiden_name" name="maiden_name"
                                                        value="{{ old('maiden_name', $user->maiden_name) }}">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Personal/Social History -->
                                <div class="mb-4">
                                    <h6 class="text-muted small fw-bold">Personal/Social History</h6>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="smoker" name="smoker"
                                                    value="1" {{ old('smoker', $user->smoker) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="smoker">Smoker</label>
                                            </div>
                                            <input type="text" class="form-control form-control-sm mt-1"
                                                id="smoker_packs_per_year" name="smoker_packs_per_year" placeholder="Packs/year"
                                                value="{{ old('smoker_packs_per_year', $user->smoker_packs_per_year) }}">
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="drinks_alcohol"
                                                    name="drinks_alcohol" value="1" {{ old('drinks_alcohol', $user->drinks_alcohol) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="drinks_alcohol">Drinks Alcohol</label>
                                            </div>
                                            <input type="text" class="form-control form-control-sm mt-1" id="alcohol_specify"
                                                name="alcohol_specify" placeholder="Specify"
                                                value="{{ old('alcohol_specify', $user->alcohol_specify) }}">
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="illicit_drug_use"
                                                    name="illicit_drug_use" value="1" {{ old('illicit_drug_use', $user->illicit_drug_use) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="illicit_drug_use">Illicit Drug Use</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="multiple_sexual_partners"
                                                    name="multiple_sexual_partners" value="1" {{ old('multiple_sexual_partners', $user->multiple_sexual_partners) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="multiple_sexual_partners">Multiple Sexual
                                                    Partners</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="is_pwd" name="is_pwd"
                                                    value="1" {{ old('is_pwd', $user->is_pwd) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_pwd">PWD</label>
                                            </div>
                                            <input type="text" class="form-control form-control-sm mt-1" id="pwd_specify"
                                                name="pwd_specify" placeholder="Specify"
                                                value="{{ old('pwd_specify', $user->pwd_specify) }}">
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="has_sti" name="has_sti"
                                                    value="1" {{ old('has_sti', $user->has_sti) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="has_sti">STI</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="has_allergies"
                                                    name="has_allergies" value="1" {{ old('has_allergies', $user->has_allergies) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="has_allergies">Allergies</label>
                                            </div>
                                            <input type="text" class="form-control form-control-sm mt-1" id="allergies_specify"
                                                name="allergies_specify" placeholder="Specify allergies"
                                                value="{{ old('allergies_specify', $user->allergies_specify) }}">
                                        </div>
                                        <div class="col-md-12">
                                            <label for="social_history_others" class="form-label">Others</label>
                                            <textarea class="form-control" id="social_history_others"
                                                name="social_history_others"
                                                rows="2">{{ old('social_history_others', $user->social_history_others) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Family History -->
                                <div class="mb-4">
                                    <h6 class="text-muted small fw-bold">Family History</h6>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="family_hypertension"
                                                    name="family_hypertension" value="1" {{ old('family_hypertension', $user->family_hypertension) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="family_hypertension">Hypertension</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="family_diabetes"
                                                    name="family_diabetes" value="1" {{ old('family_diabetes', $user->family_diabetes) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="family_diabetes">Diabetes</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="family_goiter"
                                                    name="family_goiter" value="1" {{ old('family_goiter', $user->family_goiter) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="family_goiter">Goiter</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="family_cancer"
                                                    name="family_cancer" value="1" {{ old('family_cancer', $user->family_cancer) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="family_cancer">Cancer</label>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="family_history_others" class="form-label">Others</label>
                                            <textarea class="form-control" id="family_history_others"
                                                name="family_history_others"
                                                rows="2">{{ old('family_history_others', $user->family_history_others) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Patient Medical History -->
                                <div class="mb-4">
                                    <h6 class="text-muted small fw-bold">Patient Medical History</h6>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="history_uti"
                                                    name="history_uti" value="1" {{ old('history_uti', $user->history_uti) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="history_uti">UTI</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="history_hypertension"
                                                    name="history_hypertension" value="1" {{ old('history_hypertension', $user->history_hypertension) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="history_hypertension">Hypertension</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="history_diabetes"
                                                    name="history_diabetes" value="1" {{ old('history_diabetes', $user->history_diabetes) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="history_diabetes">Diabetes</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="history_goiter"
                                                    name="history_goiter" value="1" {{ old('history_goiter', $user->history_goiter) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="history_goiter">Goiter</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="history_cancer"
                                                    name="history_cancer" value="1" {{ old('history_cancer', $user->history_cancer) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="history_cancer">Cancer</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="history_tuberculosis"
                                                    name="history_tuberculosis" value="1" {{ old('history_tuberculosis', $user->history_tuberculosis) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="history_tuberculosis">Tuberculosis</label>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="medical_history_others" class="form-label">Others</label>
                                            <textarea class="form-control" id="medical_history_others"
                                                name="medical_history_others"
                                                rows="2">{{ old('medical_history_others', $user->medical_history_others) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Previous Surgeries & Maintenance Medicine -->
                                <div class="mb-4">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="previous_surgeries" class="form-label">Previous Surgeries</label>
                                            <textarea class="form-control" id="previous_surgeries" name="previous_surgeries"
                                                rows="3"
                                                placeholder="List any previous surgeries...">{{ old('previous_surgeries', $user->previous_surgeries) }}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="maintenance_medicine" class="form-label">Maintenance Medicine</label>
                                            <textarea class="form-control" id="maintenance_medicine" name="maintenance_medicine"
                                                rows="3"
                                                placeholder="List current medications...">{{ old('maintenance_medicine', $user->maintenance_medicine) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Immunization Section -->
                            <div class="form-section">
                                <h6><i class="fas fa-syringe me-2"></i>Immunization Records</h6>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <h6 class="text-muted small fw-bold">For Children</h6>
                                        <div class="row g-2">
                                            @php
                                                $childVaccines = ['BCG', 'DPT1', 'DPT2', 'DPT3', 'OPV1', 'OPV2', 'OPV3', 'Measles', 'Hepatitis B1', 'Hepatitis B2', 'Hepatitis B3', 'Hepatitis A'];
                                            @endphp
                                            @foreach($childVaccines as $vaccine)
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input"
                                                            id="imm_{{ strtolower(str_replace(' ', '_', $vaccine)) }}"
                                                            name="imm_{{ strtolower(str_replace(' ', '_', $vaccine)) }}" value="1"
                                                            {{ old('imm_' . strtolower(str_replace(' ', '_', $vaccine)), $user->immunization->{strtolower(str_replace(' ', '_', $vaccine))} ?? false) ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="imm_{{ strtolower(str_replace(' ', '_', $vaccine)) }}">{{ $vaccine }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="text-muted small fw-bold">For Elderly and Immunocompromised</h6>
                                        <div class="row g-2">
                                            @php
                                                $elderlyVaccines = ['Varicella', 'HPV', 'Pneumococcal', 'MMR', 'Flu Vaccine', 'None'];
                                            @endphp
                                            @foreach($elderlyVaccines as $vaccine)
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input"
                                                            id="imm_{{ strtolower(str_replace(' ', '_', $vaccine)) }}"
                                                            name="imm_{{ strtolower(str_replace(' ', '_', $vaccine)) }}" value="1"
                                                            {{ old('imm_' . strtolower(str_replace(' ', '_', $vaccine)), $user->immunization->{strtolower(str_replace(' ', '_', $vaccine))} ?? false) ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="imm_{{ strtolower(str_replace(' ', '_', $vaccine)) }}">{{ $vaccine }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- COVID-19 Immunization -->
                                <div class="mt-4">
                                    <h6 class="text-muted small fw-bold">COVID-19 Immunization Status</h6>
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label for="covid_vaccine_name" class="form-label">Vaccine Name</label>
                                            <input type="text" class="form-control" id="covid_vaccine_name"
                                                name="covid_vaccine_name"
                                                value="{{ old('covid_vaccine_name', $user->immunization->covid_vaccine_name ?? '') }}"
                                                placeholder="e.g., Pfizer, Moderna, Sinovac">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="covid_first_dose" class="form-label">1st Dose</label>
                                            <input type="date" class="form-control" id="covid_first_dose"
                                                name="covid_first_dose"
                                                value="{{ old('covid_first_dose', $user->immunization->covid_first_dose ?? '') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="covid_second_dose" class="form-label">2nd Dose</label>
                                            <input type="date" class="form-control" id="covid_second_dose"
                                                name="covid_second_dose"
                                                value="{{ old('covid_second_dose', $user->immunization->covid_second_dose ?? '') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="covid_booster1" class="form-label">Booster 1</label>
                                            <input type="date" class="form-control" id="covid_booster1" name="covid_booster1"
                                                value="{{ old('covid_booster1', $user->immunization->covid_booster1 ?? '') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="covid_booster2" class="form-label">Booster 2</label>
                                            <input type="date" class="form-control" id="covid_booster2" name="covid_booster2"
                                                value="{{ old('covid_booster2', $user->immunization->covid_booster2 ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Consent Section -->
                            <div class="form-section">
                                <h6><i class="fas fa-file-signature me-2"></i>Patient Consent (Pagtugot sa Pasyente)</h6>
                                <div class="alert alert-info">
                                    <p class="mb-2"><strong>IN ENGLISH:</strong></p>
                                    <p class="small mb-2">I have read and understand the ITR (Individual Treatment Record) after
                                        I have been made aware of its contents. During informational conversation, I was
                                        informed in a comprehensive way about the need and importance of the Primary Care
                                        Benefit Package (PCB), Konsulta Program, eKonsulta System, iClinicSys (Integrated Clinic
                                        Information System) by the CHO DHO/UHC representative. All my questions during the said
                                        conversation were addressed accordingly and I have also been given enough time to decide
                                        on this matter.</p>
                                    <p class="small mb-2">Furthermore, I permit CHO DHO/UHC to encode the information concerning
                                        my person and the collected data regarding my health status and consultations conducted
                                        by the same on the information system as mentioned above and provide the same to the
                                        Philippine Health Information Exchange - Lite (PHIE Lite), the Department of Health
                                        (DOH) National Health Data Reporting and PhilhealthKonsulta Program.</p>

                                    <p class="mb-2 mt-3"><strong>SA BISAYA:</strong></p>
                                    <p class="small mb-0">Ako nakabasa ug nakasabot sa ITR (Individual Treatment Record)
                                        paghuman naa ko gipahibalo sa sulod niini ug gipasabot sa importansya sa Primary Care
                                        Benefits Package (PCB), Konsulta Program, eKonsulta System ug iClinicsys (Integrated
                                        Clinic Information System) sa taga- CHO DHO/UHC. Tanan nakong pangutana kay natubag ug
                                        ako na hatagan ug saktong panahon para mahatag saakoa ang pagtugot.
                                        Ako pud gihatagan ug permission na isulod ang impormasyon sa akong pagkatao, sa estado
                                        sa akong panlawas ug sa nahimo ug mahimong konsultasyon na mga information systems na
                                        nahisgot ug ang maong impormasyon ihatag sa Philippine Health Information Exchange - Lte
                                        (PHIE Lite), sa Department of Health (DOH) National Health Data Reporting ug Phil Health
                                        Konsulta Program.
                                        Ang resulta saakong konsultasyon ug estado saakong panglawas kay pwede nako mapangayo o
                                        saakong tag tungod. Pwede ra pud nako ikansel akining gihatag nako pagtugot sa CHO
                                        DHO/UHC na walay ihatag na rason ug walay maski unsa na desbintaha saakong medical
                                        napagtambal..</p>
                                </div>

                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="consent_signed" name="consent_signed"
                                        value="1" {{ old('consent_signed', $user->consent_signed) ? 'checked' : '' }} required>
                                    <label class="form-check-label fw-bold" for="consent_signed">
                                        I have read and agree to the terms above / Nakabasa ug miuyon sa mga termino sa ibabaw
                                    </label>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('patient.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="button" id="bookAppointmentBtn" class="btn btn-primary">
                                <i class="fas fa-calendar-check me-2"></i>Book Appointment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">
                        <i class="fas fa-check-circle text-success me-2"></i>Confirm Your Appointment
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Please review your appointment details before confirming:</p>
                    <div id="confirmationDetails">
                        <!-- Details will be populated by JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Go Back
                    </button>
                    <button type="button" id="confirmBookingBtn" class="btn btn-primary">
                        <i class="fas fa-check me-2"></i>Confirm Booking
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        console.log('Calendar script loading...');

        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOM loaded, initializing calendar...');

            // Calendar functionality
            class PatientAppointmentCalendar {
                constructor() {
                    console.log('PatientAppointmentCalendar constructor called');
                    this.currentDate = new Date();
                    this.selectedDate = null;
                    this.selectedTime = null;
                    this.calendarData = [];
                    this.init();
                }

                init() {
                    this.attachEventListeners();
                    this.loadCalendar();
                }

                attachEventListeners() {
                    document.getElementById('prevMonth').addEventListener('click', () => {
                        this.currentDate.setMonth(this.currentDate.getMonth() - 1);
                        this.loadCalendar();
                    });

                    document.getElementById('nextMonth').addEventListener('click', () => {
                        this.currentDate.setMonth(this.currentDate.getMonth() + 1);
                        this.loadCalendar();
                    });

                    // Form validation
                    const form = document.querySelector('form[method="POST"]');
                    if (form) {
                        form.addEventListener('submit', (e) => {
                            const dateInput = document.getElementById('appointment_date');
                            const timeInput = document.getElementById('appointment_time');

                            if (!dateInput.value || !timeInput.value) {
                                e.preventDefault();
                                alert('Please select both date and time for your appointment.');
                                return false;
                            }
                        });
                    }
                }

                async loadCalendar() {
                    const year = this.currentDate.getFullYear();
                    const month = this.currentDate.getMonth() + 1;

                    console.log('Loading calendar for:', year, month);

                    try {
                        const response = await fetch(`/patient/appointments/calendar?year=${year}&month=${month}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        console.log('Response status:', response.status);

                        if (!response.ok) {
                            console.error('Response not ok:', response.statusText);
                            throw new Error('Failed to load calendar data');
                        }

                        const data = await response.json();
                        console.log('Calendar data received:', data);
                        this.calendarData = data.calendar;
                        this.renderCalendar();
                        this.updateMonthDisplay();
                    } catch (error) {
                        console.error('Error loading calendar:', error);
                        document.getElementById('calendarGrid').innerHTML = '<div class="col-12 text-center text-danger">Error loading calendar: ' + error.message + '</div>';
                    }
                }

                renderCalendar() {
                    const calendarGrid = document.getElementById('calendarGrid');
                    calendarGrid.innerHTML = '';

                    // Add day headers
                    const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                    dayHeaders.forEach(day => {
                        const header = document.createElement('div');
                        header.className = 'calendar-header';
                        header.textContent = day;
                        calendarGrid.appendChild(header);
                    });

                    // Add empty cells for days before month starts
                    const firstDay = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1).getDay();
                    for (let i = 0; i < firstDay; i++) {
                        const emptyDay = document.createElement('div');
                        calendarGrid.appendChild(emptyDay);
                    }

                    // Add calendar days
                    this.calendarData.forEach(dayData => {
                        const dayElement = document.createElement('div');
                        dayElement.className = 'calendar-day';
                        dayElement.textContent = dayData.day;

                        if (dayData.is_weekend) {
                            dayElement.classList.add('weekend');
                        }

                        if (dayData.is_past) {
                            dayElement.classList.add('past');
                        } else if (dayData.is_fully_occupied) {
                            dayElement.classList.add('occupied');
                        } else if (dayData.occupied_slots > 0) {
                            dayElement.classList.add('partially-occupied');
                        }

                        // Add slot indicator
                        if (dayData.occupied_slots > 0) {
                            const indicator = document.createElement('span');
                            indicator.className = 'slot-indicator';
                            indicator.textContent = `${dayData.occupied_slots}/${dayData.total_slots}`;
                            dayElement.appendChild(indicator);
                        }

                        if (!dayData.is_past && !dayData.is_fully_occupied) {
                            dayElement.addEventListener('click', () => {
                                this.selectDate(dayData.date);
                            });
                        }

                        calendarGrid.appendChild(dayElement);
                    });
                }

                updateMonthDisplay() {
                    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'];
                    document.getElementById('currentMonth').textContent =
                        `${monthNames[this.currentDate.getMonth()]} ${this.currentDate.getFullYear()}`;
                }

                async selectDate(date) {
                    this.selectedDate = date;

                    // Update selected state in calendar
                    document.querySelectorAll('.calendar-day').forEach(day => {
                        day.classList.remove('selected');
                    });
                    event.target.classList.add('selected');

                    // Update selected date display
                    const dateObj = new Date(date);
                    const formattedDate = dateObj.toLocaleDateString('en-US', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    document.getElementById('selectedDateDisplay').textContent = formattedDate;

                    // Update hidden input
                    document.getElementById('appointment_date').value = date;

                    // Load time slots for selected date
                    await this.loadTimeSlots(date);
                }

                async loadTimeSlots(date) {
                    try {
                        const response = await fetch(`/patient/appointments/slots?date=${date}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) throw new Error('Failed to load time slots');

                        const data = await response.json();
                        this.renderTimeSlots(data.slots);
                    } catch (error) {
                        console.error('Error loading time slots:', error);
                        document.getElementById('timeSlotsGrid').innerHTML = '<div class="col-12 text-center text-danger">Error loading time slots</div>';
                    }
                }

                renderTimeSlots(slots) {
                    const timeSlotsWrapper = document.getElementById('timeSlotsGrid');
                    timeSlotsWrapper.innerHTML = '';
                    
                    // Create the grid container
                    const gridContainer = document.createElement('div');
                    gridContainer.className = 'time-slots-grid';

                    slots.forEach(slot => {
                        const slotElement = document.createElement('div');
                        
                        // Determine slot class based on availability and time
                        let slotClass = 'time-slot';
                        let statusText = '';
                        
                        if (slot.is_past) {
                            slotClass += ' past';
                            statusText = 'Unavailable';
                        } else if (!slot.available) {
                            slotClass += ' occupied';
                            statusText = `Occupied (${slot.occupied_count})`;
                        } else {
                            slotClass += ' available';
                            statusText = 'Available';
                        }
                        
                        slotElement.className = slotClass;

                        const timeElement = document.createElement('div');
                        timeElement.className = 'time';
                        timeElement.textContent = slot.display;

                        const statusElement = document.createElement('div');
                        statusElement.className = 'status';
                        statusElement.textContent = statusText;

                        slotElement.appendChild(timeElement);
                        slotElement.appendChild(statusElement);

                        // Only allow clicking on available slots that haven't passed
                        if (slot.available && !slot.is_past) {
                            slotElement.addEventListener('click', () => {
                                this.selectTimeSlot(slot);
                            });
                        }

                        gridContainer.appendChild(slotElement);
                    });
                    
                    timeSlotsWrapper.appendChild(gridContainer);
                }

                selectTimeSlot(slot) {
                    // Remove previous selection
                    document.querySelectorAll('.time-slot').forEach(s => {
                        s.classList.remove('selected');
                    });

                    // Add selection to clicked slot
                    event.currentTarget.classList.add('selected');

                    // Update hidden input
                    document.getElementById('appointment_time').value = slot.time;
                    this.selectedTime = slot.time;
                }
            }

            // Initialize calendar
            console.log('Creating calendar instance...');
            const calendar = new PatientAppointmentCalendar();
            console.log('Calendar initialized successfully');

            // Step tracking
            const steps = document.querySelectorAll('.step');
            const form = document.querySelector('form[method="POST"]');
            const bookBtn = document.getElementById('bookAppointmentBtn');
            const confirmBtn = document.getElementById('confirmBookingBtn');
            const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));

            // Track form progress and update steps
            const updateStepProgress = () => {
                const hasPatientInfo = document.getElementById('patient_name').value &&
                    document.getElementById('patient_phone').value;
                const hasDateTime = document.getElementById('appointment_date').value &&
                    document.getElementById('appointment_time').value;
                const hasService = document.getElementById('service_id').value;

                // Update step 1
                if (hasPatientInfo) {
                    steps[0].classList.add('completed');
                } else {
                    steps[0].classList.remove('completed');
                }

                // Update step 2
                if (hasDateTime) {
                    steps[1].classList.add('completed');
                } else {
                    steps[1].classList.remove('completed');
                }

                // Update step 3
                if (hasService) {
                    steps[2].classList.add('completed');
                } else {
                    steps[2].classList.remove('completed');
                }
            };

            // Listen for form changes
            form.addEventListener('change', updateStepProgress);
            form.addEventListener('input', updateStepProgress);

            // Book appointment button - show confirmation modal
            bookBtn.addEventListener('click', (e) => {
                e.preventDefault();

                // Validate form
                if (!form.checkValidity()) {
                    form.reportValidity();
                    window.toast.warning('Please fill in all required fields', 'Incomplete Form');
                    return;
                }

                // Check if date and time are selected
                const dateInput = document.getElementById('appointment_date');
                const timeInput = document.getElementById('appointment_time');

                if (!dateInput.value || !timeInput.value) {
                    if (window.toast && typeof window.toast.warning === 'function') {
                        window.toast.warning('Please select both date and time for your appointment', 'Missing Information');
                    } else {
                        alert('Please select both date and time for your appointment');
                    }
                    return;
                }

                // Populate confirmation modal
                const confirmationDetails = document.getElementById('confirmationDetails');
                const patientName = document.getElementById('patient_name').value;
                const patientPhone = document.getElementById('patient_phone').value;
                const appointmentDate = new Date(dateInput.value).toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                const appointmentTime = timeInput.value;
                // Get selected service name using the select element
                const serviceSelect = document.getElementById('service_id');
                const serviceType = serviceSelect.options[serviceSelect.selectedIndex].text;
                const medicalHistory = document.getElementById('medical_history').value || 'None provided';
                const notes = document.getElementById('notes').value || 'None';

                confirmationDetails.innerHTML = `
                                                <div class="confirmation-detail">
                                                    <span class="confirmation-label">Patient Name:</span>
                                                    <span class="confirmation-value">${patientName}</span>
                                                </div>
                                                <div class="confirmation-detail">
                                                    <span class="confirmation-label">Phone Number:</span>
                                                    <span class="confirmation-value">${patientPhone}</span>
                                                </div>
                                                <div class="confirmation-detail">
                                                    <span class="confirmation-label">Appointment Date:</span>
                                                    <span class="confirmation-value">${appointmentDate}</span>
                                                </div>
                                                <div class="confirmation-detail">
                                                    <span class="confirmation-label">Appointment Time:</span>
                                                    <span class="confirmation-value">${appointmentTime}</span>
                                                </div>
                                                <div class="confirmation-detail">
                                                    <span class="confirmation-label">Service Type:</span>
                                                    <span class="confirmation-value">${serviceType}</span>
                                                </div>
                                                <div class="confirmation-detail">
                                                    <span class="confirmation-label">Medical History:</span>
                                                    <span class="confirmation-value">${medicalHistory}</span>
                                                </div>
                                                <div class="confirmation-detail">
                                                    <span class="confirmation-label">Additional Notes:</span>
                                                    <span class="confirmation-value">${notes}</span>
                                                </div>
                                            `;

                // Mark step 4 as active
                steps.forEach(s => s.classList.remove('active'));
                steps[3].classList.add('active');

                // Show modal
                confirmationModal.show();
            });

            // Confirm booking button - submit form
            confirmBtn.addEventListener('click', () => {
                confirmationModal.hide();
                if (window.toast && typeof window.toast.info === 'function') {
                    window.toast.info('Submitting your appointment...', 'Please wait');
                }
                form.submit();
            });

            // Show session messages
            @if(session('success'))
                if (window.toast && typeof window.toast.success === 'function') {
                    window.toast.success('{{ session('success') }}');
                }
            @endif

            @if(session('error'))
                if (window.toast && typeof window.toast.error === 'function') {
                    window.toast.error('{{ session('error') }}');
                }
            @endif
                                    });
    </script>
@endpush