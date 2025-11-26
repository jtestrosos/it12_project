@extends('layouts.admin')

@section('title', 'Book Appointment - Patient Portal')
@section('page-title', 'Book Appointment')
@section('page-description', 'Schedule your healthcare visit')

@section('sidebar-menu')
<a class="nav-link @if(request()->routeIs('patient.dashboard')) active @endif" href="{{ route('patient.dashboard') }}" data-tooltip="Dashboard">
    <i class="fas fa-th-large"></i> <span class="sidebar-content">Dashboard</span>
</a>
<a class="nav-link @if(request()->routeIs('patient.appointments') || request()->routeIs('patient.appointment.show')) active @endif" href="{{ route('patient.appointments') }}" data-tooltip="My Appointments">
    <i class="fas fa-calendar"></i> <span class="sidebar-content">My Appointments</span>
</a>
<a class="nav-link @if(request()->routeIs('patient.book-appointment')) active @endif" href="{{ route('patient.book-appointment') }}" data-tooltip="Book Appointment">
    <i class="fas fa-plus"></i> <span class="sidebar-content">Book Appointment</span>
</a>
@endsection

@section('user-initials')
{{ substr(Auth::user()->name, 0, 2) }}
@endsection

@section('user-name')
{{ Auth::user()->name }}
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
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
    }
    body.bg-dark .booking-card {
        background: #1e2124;
        border-color: #2a2f35;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }
    .booking-header {
        background: linear-gradient(135deg, #007bff, #0056b3);
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
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
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
        background: linear-gradient(135deg, #007bff, #0056b3);
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
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }
    .calendar-day.occupied {
        background-color: #dc3545;
        color: white;
        border-color: #dc3545;
    }
    .calendar-day.partially-occupied {
        background-color: #ffc107;
        color: #212529;
        border-color: #ffc107;
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
        background: rgba(0,0,0,0.1);
        padding: 1px 3px;
        border-radius: 2px;
    }
    .time-slots-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 0.5rem;
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
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }
    .time-slot.available:hover {
        background-color: #c3e6cb;
    }
    .time-slot.occupied {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
        cursor: not-allowed;
    }
    .time-slot.selected {
        background-color: #007bff;
        border-color: #007bff;
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
        background-color: #1e3a1f;
        border-color: #2a5f2e;
        color: #90ee90;
    }
    body.bg-dark .time-slot.occupied {
        background-color: #3d1a1a;
        border-color: #5c2a2a;
        color: #ff6b6b;
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
        border-color: #007bff;
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
        border-color: #007bff;
    }
    body.bg-dark #prevMonth:hover,
    body.bg-dark #nextMonth:hover {
        background-color: #007bff;
        color: #fff;
    }
    body.bg-dark #currentMonth,
    body.bg-dark #selectedDateDisplay {
        color: #e6e6e6;
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
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
    }
    body.bg-dark .calendar-day.occupied {
        background-color: #3d1a1a;
        border-color: #5c2a2a;
        color: #ff6b6b;
    }
    body.bg-dark .calendar-day.partially-occupied {
        background-color: #4d3e0b;
        border-color: #66520f;
        color: #ffd700;
    }
    
    /* Dark Mode Time Slots */
    body.bg-dark .time-slot.selected {
        background-color: #007bff;
        border-color: #007bff;
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
        background: #007bff;
        border-color: #007bff;
        color: white;
        box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.2);
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
        color: #007bff;
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
        border: 1px solid rgba(0,0,0,0.1);
    }
    body.bg-dark .legend-color {
        border-color: rgba(255,255,255,0.2);
    }
    .legend-color.available {
        background: white;
        border-color: #dee2e6;
    }
    body.bg-dark .legend-color.available {
        background: #1e2124;
        border-color: #495057;
    }
    .legend-color.partially-occupied {
        background: #ffc107;
    }
    .legend-color.occupied {
        background: #dc3545;
    }
    .legend-color.selected {
        background: #007bff;
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
                                Your information has been pre-filled from your account. You can modify any details as needed.
                            </p>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="patient_name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control @error('patient_name') is-invalid @enderror" 
                                           id="patient_name" name="patient_name" value="{{ old('patient_name', $user->name ?? '') }}" required>
                                    @error('patient_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="patient_phone" class="form-label">Phone Number *</label>
                                    <input type="tel" class="form-control @error('patient_phone') is-invalid @enderror" 
                                           id="patient_phone" name="patient_phone" value="{{ old('patient_phone', $user->phone ?? '') }}" required>
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
                                    <div class="legend-color partially-occupied"></div>
                                    <span>Limited Slots</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color occupied"></div>
                                    <span>Fully Booked</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color selected"></div>
                                    <span>Selected</span>
                                </div>
                            </div>
                            
                            <!-- Calendar View -->
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Select Appointment Date & Time</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
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
                                        <div class="col-md-8">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h6 class="mb-0" id="selectedDateDisplay">Select a date to view available time slots</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div id="timeSlotsGrid" class="time-slots-grid">
                                                        <!-- Time slots will be populated here -->
                                                    </div>
                                                    <input type="hidden" id="appointment_date" name="appointment_date" value="{{ old('appointment_date') }}" required>
                                                    <input type="hidden" id="appointment_time" name="appointment_time" value="{{ old('appointment_time') }}" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">        
                                <label for="service_type" class="form-label">Service Needed *</label>       
                                <select class="form-control @error('service_type') is-invalid @enderror"    
                                        id="service_type" name="service_type" required>                     
                                    <option value="">Select Service</option>     
                                    <option value="General Checkup" {{ old('service_type') == 'General Checkup' ? 'selected' : '' }}>General Checkup</option>     
                                    <option value="Prenatal" {{ old('service_type') == 'Prenatal' ? 'selected' : '' }}>Prenatal</option>                          
                                    <option value="Medical Check-up" {{ old('service_type') == 'Medical Check-up' ? 'selected' : '' }}>Medical Check-up</option>  
                                    <option value="Immunization" {{ old('service_type') == 'Immunization' ? 'selected' : '' }}>Immunization</option>              
                                    <option value="Family Planning" {{ old('service_type') == 'Family Planning' ? 'selected' : '' }}>Family Planning</option>     
                                </select>

                                @error('service_type')
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
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="2" 
                                          placeholder="Any additional information or special requests...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

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
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
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

document.addEventListener('DOMContentLoaded', function() {
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
            const timeSlotsGrid = document.getElementById('timeSlotsGrid');
            timeSlotsGrid.innerHTML = '';

            slots.forEach(slot => {
                const slotElement = document.createElement('div');
                slotElement.className = `time-slot ${slot.available ? 'available' : 'occupied'}`;
                
                const timeElement = document.createElement('div');
                timeElement.className = 'time';
                timeElement.textContent = slot.display;
                
                const statusElement = document.createElement('div');
                statusElement.className = 'status';
                statusElement.textContent = slot.available ? 'Available' : `Occupied (${slot.occupied_count})`;
                
                slotElement.appendChild(timeElement);
                slotElement.appendChild(statusElement);
                
                if (slot.available) {
                    slotElement.addEventListener('click', () => {
                        this.selectTimeSlot(slot);
                    });
                }
                
                timeSlotsGrid.appendChild(slotElement);
            });
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
        const hasService = document.getElementById('service_type').value;
        
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
        const serviceType = document.getElementById('service_type').value;
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
