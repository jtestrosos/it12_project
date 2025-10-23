@extends('layouts.app')

@section('content')
<style>
    .booking-container {
        background-color: #f0f0f0;
        min-height: 100vh;
        padding: 2rem 0;
    }
    .booking-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        border: none;
    }
    .booking-header {
        background: linear-gradient(135deg, #007bff, #0056b3);
        border-radius: 12px 12px 0 0;
        padding: 1.5rem;
    }
    .form-control {
        border-radius: 8px;
        border: 1px solid #e9ecef;
        padding: 0.75rem;
    }
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    }
    .btn-primary {
        background: linear-gradient(135deg, #007bff, #0056b3);
        border: none;
        border-radius: 8px;
        padding: 0.75rem 2rem;
    }
    .btn-secondary {
        border-radius: 8px;
        padding: 0.75rem 2rem;
    }
</style>

<div class="booking-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card booking-card">
                    <div class="booking-header text-white">
                        <h4 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Book an Appointment</h4>
                    </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('patient.appointment.store') }}">
                        @csrf
                        
                        <!-- Patient Information -->
                        <div class="row mb-3">
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

                        <div class="mb-3">
                            <label for="patient_address" class="form-label">Address *</label>
                            <textarea class="form-control @error('patient_address') is-invalid @enderror" 
                                      id="patient_address" name="patient_address" rows="2" required>{{ old('patient_address') }}</textarea>
                            @error('patient_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Appointment Details -->
                        <div class="row mb-3">
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

                        <div class="mb-3">
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

                        <!-- Additional Information -->
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

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('patient.dashboard') }}" class="btn btn-secondary me-md-2">
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
</div>
@endsection
