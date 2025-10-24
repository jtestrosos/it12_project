@extends('layouts.app')

@section('content')
@php
    $adminLayout = true;
@endphp
<style>
    .booking-container {
        background-color: #f0f0f0;
        min-height: 100vh;
        padding: 2rem;
        width: 100%;
        margin: 0;
    }
    .booking-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        border: none;
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
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .form-section h6 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    .form-control {
        border-radius: 8px;
        border: 1px solid #e9ecef;
        padding: 0.75rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
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
</style>

<div class="booking-container">
        <div class="row justify-content-center">
            <div class="col-md-10">
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
                        <form method="POST" action="{{ route('patient.appointment.store') }}">
                            @csrf
                            
                            <!-- Patient Information -->
                            <div class="form-section">
                                <h6><i class="fas fa-user me-2"></i>Patient Information</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="patient_name" class="form-label">Full Name *</label>
                                        <input type="text" class="form-control @error('patient_name') is-invalid @enderror" 
                                               id="patient_name" name="patient_name" value="{{ old('patient_name') }}" required>
                                        @error('patient_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="patient_phone" class="form-label">Phone Number *</label>
                                        <input type="tel" class="form-control @error('patient_phone') is-invalid @enderror" 
                                               id="patient_phone" name="patient_phone" value="{{ old('patient_phone') }}" required>
                                        @error('patient_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label for="patient_address" class="form-label">Address *</label>
                                    <textarea class="form-control @error('patient_address') is-invalid @enderror" 
                                              id="patient_address" name="patient_address" rows="2" required>{{ old('patient_address') }}</textarea>
                                    @error('patient_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Appointment Details -->
                            <div class="form-section">
                                <h6><i class="fas fa-calendar me-2"></i>Appointment Details</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="appointment_date" class="form-label">Preferred Date *</label>
                                        <input type="date" class="form-control @error('appointment_date') is-invalid @enderror" 
                                               id="appointment_date" name="appointment_date" 
                                               value="{{ old('appointment_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                        @error('appointment_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="appointment_time" class="form-label">Preferred Time *</label>
                                        <select class="form-control @error('appointment_time') is-invalid @enderror" 
                                                id="appointment_time" name="appointment_time" required>
                                            <option value="">Select Time</option>
                                            <option value="08:00" {{ old('appointment_time') == '08:00' ? 'selected' : '' }}>8:00 AM</option>
                                            <option value="09:00" {{ old('appointment_time') == '09:00' ? 'selected' : '' }}>9:00 AM</option>
                                            <option value="10:00" {{ old('appointment_time') == '10:00' ? 'selected' : '' }}>10:00 AM</option>
                                            <option value="11:00" {{ old('appointment_time') == '11:00' ? 'selected' : '' }}>11:00 AM</option>
                                            <option value="13:00" {{ old('appointment_time') == '13:00' ? 'selected' : '' }}>1:00 PM</option>
                                            <option value="14:00" {{ old('appointment_time') == '14:00' ? 'selected' : '' }}>2:00 PM</option>
                                            <option value="15:00" {{ old('appointment_time') == '15:00' ? 'selected' : '' }}>3:00 PM</option>
                                            <option value="16:00" {{ old('appointment_time') == '16:00' ? 'selected' : '' }}>4:00 PM</option>
                                        </select>
                                        @error('appointment_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-calendar-check me-2"></i>Book Appointment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </div>
</div>
@endsection
