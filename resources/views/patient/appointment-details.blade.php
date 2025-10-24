@extends('layouts.app')

@section('content')
@php
    $adminLayout = true;
@endphp
<style>
    .appointment-details-container {
        background-color: #f0f0f0;
        min-height: 100vh;
        padding: 2rem;
        width: 100%;
        margin: 0;
    }
    .appointment-details-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        border: none;
    }
    .appointment-details-header {
        border-bottom: 1px solid #e9ecef;
        padding: 1.5rem;
        margin-bottom: 0;
        border-radius: 12px 12px 0 0;
    }
    .appointment-details-body {
        padding: 1.5rem;
    }
    .info-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e9ecef;
    }
    .info-item:last-child {
        border-bottom: none;
    }
    .info-label {
        font-weight: 600;
        color: #495057;
    }
    .info-value {
        color: #6c757d;
    }
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
    }
    .btn-modern {
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-size: 0.9rem;
        font-weight: 500;
    }
</style>

<div class="appointment-details-container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card appointment-details-card">
                    <div class="appointment-details-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="fw-bold mb-1 text-dark">Appointment Details</h4>
                                <p class="text-muted mb-0">Appointment #{{ $appointment->id }}</p>
                            </div>
                            <span class="status-badge 
                                @if($appointment->status == 'pending') bg-warning text-dark
                                @elseif($appointment->status == 'approved') bg-success text-white
                                @elseif($appointment->status == 'completed') bg-info text-white
                                @elseif($appointment->status == 'cancelled') bg-danger text-white
                                @else bg-secondary text-white
                                @endif">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="appointment-details-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-section">
                                    <h6 class="fw-bold text-dark mb-3">
                                        <i class="fas fa-user me-2"></i>Patient Information
                                    </h6>
                                    <div class="info-item">
                                        <span class="info-label">Name:</span>
                                        <span class="info-value">{{ $appointment->patient_name }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Phone:</span>
                                        <span class="info-value">{{ $appointment->patient_phone }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Address:</span>
                                        <span class="info-value">{{ $appointment->patient_address }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-section">
                                    <h6 class="fw-bold text-dark mb-3">
                                        <i class="fas fa-calendar me-2"></i>Appointment Details
                                    </h6>
                                    <div class="info-item">
                                        <span class="info-label">Date:</span>
                                        <span class="info-value">{{ $appointment->appointment_date->format('M d, Y') }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Time:</span>
                                        <span class="info-value">{{ $appointment->appointment_time }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Service:</span>
                                        <span class="info-value">{{ $appointment->service_type }}</span>
                                    </div>
                                    @if($appointment->is_walk_in)
                                    <div class="info-item">
                                        <span class="info-label">Type:</span>
                                        <span class="badge bg-secondary">Walk-in</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($appointment->medical_history)
                        <div class="info-section">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="fas fa-notes-medical me-2"></i>Medical History
                            </h6>
                            <p class="text-muted mb-0">{{ $appointment->medical_history }}</p>
                        </div>
                        @endif

                        @if($appointment->notes)
                        <div class="info-section">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="fas fa-sticky-note me-2"></i>Additional Notes
                            </h6>
                            <p class="text-muted mb-0">{{ $appointment->notes }}</p>
                        </div>
                        @endif

                        @if($appointment->approved_by)
                        <div class="info-section">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="fas fa-check-circle me-2"></i>Approval Information
                            </h6>
                            <div class="info-item">
                                <span class="info-label">Approved By:</span>
                                <span class="info-value">{{ $appointment->approvedBy->name }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Approved On:</span>
                                <span class="info-value">{{ $appointment->approved_at->format('M d, Y g:i A') }}</span>
                            </div>
                        </div>
                        @endif

                        <div class="d-flex gap-2 mt-4">
                            <a href="{{ route('patient.dashboard') }}" class="btn btn-secondary btn-modern">
                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                            </a>
                            @if($appointment->status == 'pending')
                            <form method="POST" action="{{ route('patient.appointment.cancel', $appointment) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-modern" 
                                        onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                    <i class="fas fa-times me-2"></i>Cancel Appointment
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>
@endsection
