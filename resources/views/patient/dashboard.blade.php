@extends('layouts.app')

@section('content')
@php
    $adminLayout = true;
@endphp
<style>
    body {
        background-color: #f8f9fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .patient-dashboard-container {
        background-color: #f0f0f0;
        min-height: 100vh;
        padding: 0;
        width: 100%;
        margin: 0;
        display: flex;
        flex-direction: column;
    }
    .main-content {
        background-color: #f0f0f0;
        min-height: 100vh;
        padding-bottom: 2rem;
        width: 100%;
        margin: 0;
        flex: 1;
    }
    .dashboard-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: none;
        transition: transform 0.2s ease;
    }
    .dashboard-card:hover {
        transform: translateY(-2px);
    }
    .dashboard-header {
        background: white;
        border-bottom: 1px solid #e9ecef;
        padding: 1rem 2rem;
        margin-bottom: 0;
    }
    .dashboard-body {
        padding: 1.5rem;
    }
    .metric-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: none;
        transition: transform 0.2s ease;
    }
    .metric-card:hover {
        transform: translateY(-2px);
    }
    .metric-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
    }
    .metric-label {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }
    .metric-change {
        font-size: 0.8rem;
        margin-top: 0.5rem;
    }
    .appointment-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #007bff;
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
        padding: 0.75rem 1.5rem;
        font-size: 0.9rem;
        font-weight: 500;
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
</style>

<div class="patient-dashboard-container">
    <!-- Header -->
    <div class="dashboard-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-0">Dashboard Overview</h4>
            <p class="text-muted mb-0">Welcome back! Here's what's happening today.</p>
        </div>
    </div>

    <!-- Content -->
    <div class="main-content">
        <div class="p-4">
                <!-- Quick Stats -->
                <div class="row mb-4 g-3">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="metric-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="metric-label">Total Appointments</div>
                                    <div class="metric-number">{{ $appointments->total() }}</div>
                                    <div class="metric-change text-success">+12% from last month</div>
                                </div>
                                <div class="text-primary">
                                    <i class="fas fa-calendar fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="metric-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="metric-label">Pending</div>
                                    <div class="metric-number">{{ $appointments->where('status', 'pending')->count() }}</div>
                                    <div class="text-warning">Awaiting approval</div>
                                </div>
                                <div class="text-warning">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="metric-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="metric-label">Approved</div>
                                    <div class="metric-number">{{ $appointments->where('status', 'approved')->count() }}</div>
                                    <div class="text-success">Ready for visit</div>
                                </div>
                                <div class="text-success">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="metric-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="metric-label">Completed</div>
                                    <div class="metric-number">{{ $appointments->where('status', 'completed')->count() }}</div>
                                    <div class="metric-change text-success">+8% from last month</div>
                                </div>
                                <div class="text-info">
                                    <i class="fas fa-check-double fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Book Appointment Button -->
            <div class="row mb-4">
                <div class="col-12">
                    <a href="{{ route('patient.book-appointment') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Book New Appointment
                    </a>
                </div>
            </div>

            <!-- Appointments Table -->
            <div class="row">
                <div class="col-12">
                    <div class="dashboard-card">
                        <div class="dashboard-header">
                            <h5 class="mb-0">My Appointments</h5>
                        </div>
                        <div class="dashboard-body p-0">
                            @if($appointments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-modern mb-0">
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
                                            @foreach($appointments->take(10) as $appointment)
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
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ route('patient.appointment.show', $appointment) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye me-1"></i> View
                                                        </a>
                                                        @if($appointment->status == 'pending')
                                                        <form method="POST" action="{{ route('patient.appointment.cancel', $appointment) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                    onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                                                <i class="fas fa-times me-1"></i> Cancel
                                                            </button>
                                                        </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($appointments->count() > 10)
                                <div class="text-center p-3 border-top">
                                    <a href="{{ route('patient.appointments') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-calendar me-2"></i> View All Appointments
                                    </a>
                                </div>
                                @endif
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar text-muted mb-3" style="font-size: 3rem;"></i>
                                    <p class="text-muted mb-3">No appointments found.</p>
                                    <a href="{{ route('patient.book-appointment') }}" class="btn btn-primary">Book Your First Appointment</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
