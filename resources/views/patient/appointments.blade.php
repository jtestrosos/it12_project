@extends('layouts.app')

@section('content')
@php
    $adminLayout = true;
@endphp
<style>
    .appointments-container {
        background-color: #f8f9fa;
        min-height: 100vh;
        padding: 2rem 0;
    }
    .appointments-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        border: none;
    }
    .appointments-header {
        border-bottom: 1px solid #e9ecef;
        padding: 1.5rem;
        margin-bottom: 0;
    }
    .appointments-body {
        padding: 1.5rem;
    }
    .table-modern {
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-modern thead th {
        background-color: #f8f9fa;
        border: none;
        font-weight: 600;
        color: #495057;
        padding: 1rem;
    }
    .table-modern tbody td {
        border: none;
        padding: 1rem;
        border-bottom: 1px solid #f1f3f4;
    }
    .table-modern tbody tr:hover {
        background-color: #f8f9fa;
    }
    .appointment-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #007bff, #0056b3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.9rem;
    }
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    .btn-modern {
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        font-weight: 500;
    }
</style>

<div class="appointments-container">
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1 text-dark">My Appointments</h2>
                        <p class="text-muted mb-0">Manage your healthcare appointments.</p>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-bell text-muted fs-5"></i>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="appointment-avatar me-2">P</div>
                            <div>
                                <div class="fw-bold text-dark">Patient</div>
                                <small class="text-muted">User</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Book Appointment Button -->
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('patient.book-appointment') }}" class="btn btn-primary btn-modern">
                    <i class="fas fa-plus me-2"></i> Book New Appointment
                </a>
            </div>
        </div>

        <!-- Appointments Table -->
        <div class="row">
            <div class="col-12">
                <div class="card appointments-card">
                    <div class="appointments-header">
                        <h5 class="mb-0 text-dark">All Appointments</h5>
                    </div>
                    <div class="appointments-body">
                        @if($appointments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-modern">
                                    <thead>
                                        <tr>
                                            <th>Appointment</th>
                                            <th>Date & Time</th>
                                            <th>Service</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($appointments as $appointment)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="appointment-avatar me-3">
                                                        <i class="fas fa-calendar"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark">Appointment #{{ $appointment->id }}</div>
                                                        <small class="text-muted">{{ $appointment->patient_name }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $appointment->appointment_date->format('M d, Y') }}</div>
                                                    <small class="text-muted">{{ $appointment->appointment_time }}</small>
                                                </div>
                                            </td>
                                            <td class="text-muted">{{ $appointment->service_type }}</td>
                                            <td>
                                                <span class="status-badge 
                                                    @if($appointment->status == 'pending') bg-warning text-dark
                                                    @elseif($appointment->status == 'approved') bg-success text-white
                                                    @elseif($appointment->status == 'completed') bg-info text-white
                                                    @elseif($appointment->status == 'cancelled') bg-danger text-white
                                                    @else bg-secondary text-white
                                                    @endif">
                                                    {{ ucfirst($appointment->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('patient.appointment.show', $appointment) }}" class="btn btn-sm btn-outline-primary btn-modern me-2">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </a>
                                                @if($appointment->status == 'pending')
                                                <form method="POST" action="{{ route('patient.appointment.cancel', $appointment) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger btn-modern" 
                                                            onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                                        <i class="fas fa-times me-1"></i> Cancel
                                                    </button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{ $appointments->links() }}
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-calendar text-muted mb-3" style="font-size: 3rem;"></i>
                                <p class="text-muted mb-3">No appointments found.</p>
                                <a href="{{ route('patient.book-appointment') }}" class="btn btn-primary btn-modern">Book Your First Appointment</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
