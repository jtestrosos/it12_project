@extends('layouts.app')

@section('content')
@php
    $adminLayout = true;
@endphp
<style>
    .patient-dashboard-container {
        background-color: #f8f9fa;
        min-height: 100vh;
        padding: 2rem 0;
    }
    .stats-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        border: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
    }
    .stats-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
    }
    .stats-number {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
    }
    .stats-label {
        color: #6c757d;
        font-size: 0.9rem;
        margin: 0;
    }
    .stats-change {
        font-size: 0.8rem;
        font-weight: 500;
    }
    .stats-change.positive {
        color: #28a745;
    }
    .stats-change.negative {
        color: #dc3545;
    }
    .chart-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        border: none;
    }
    .chart-header {
        border-bottom: 1px solid #e9ecef;
        padding: 1.5rem;
        margin-bottom: 0;
    }
    .chart-body {
        padding: 1.5rem;
    }
    .activity-item {
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f3f4;
    }
    .activity-item:last-child {
        border-bottom: none;
    }
    .activity-avatar {
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
</style>

<div class="patient-dashboard-container">
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1 text-dark">Dashboard Overview</h2>
                        <p class="text-muted mb-0">Welcome back! Here's what's happening today.</p>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-bell text-muted fs-5"></i>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="activity-avatar me-2">P</div>
                            <div>
                                <div class="fw-bold text-dark">Patient</div>
                                <small class="text-muted">User</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overview Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-primary me-3">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div>
                                <h3 class="stats-number">{{ $appointments->total() }}</h3>
                                <p class="stats-label">Total Appointments</p>
                                <small class="stats-change positive">+12% from last month</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-warning me-3">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h3 class="stats-number">{{ $appointments->where('status', 'pending')->count() }}</h3>
                                <p class="stats-label">Pending</p>
                                <small class="text-warning">Awaiting approval</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-success me-3">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <h3 class="stats-number">{{ $appointments->where('status', 'approved')->count() }}</h3>
                                <p class="stats-label">Approved</p>
                                <small class="text-success">Ready for visit</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-info me-3">
                                <i class="fas fa-check-double"></i>
                            </div>
                            <div>
                                <h3 class="stats-number">{{ $appointments->where('status', 'completed')->count() }}</h3>
                                <p class="stats-label">Completed</p>
                                <small class="stats-change positive">+8% from last month</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card chart-card">
                    <div class="chart-header">
                        <h5 class="mb-0 text-dark">Appointment History</h5>
                    </div>
                    <div class="chart-body">
                        <canvas id="appointmentHistoryChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card chart-card">
                    <div class="chart-header">
                        <h5 class="mb-0 text-dark">Service Types</h5>
                    </div>
                    <div class="chart-body">
                        <canvas id="serviceTypesChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Section -->
        <div class="row">
            <!-- Recent Appointments -->
            <div class="col-md-8">
                <div class="card chart-card">
                    <div class="chart-header">
                        <h5 class="mb-0 text-dark">Recent Appointments</h5>
                    </div>
                    <div class="chart-body">
                        @if($appointments->count() > 0)
                            @foreach($appointments->take(5) as $appointment)
                            <div class="activity-item d-flex align-items-center">
                                <div class="activity-avatar me-3">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark">{{ $appointment->service_type }}</div>
                                    <small class="text-muted">{{ $appointment->appointment_date->format('M d, Y') }} at {{ $appointment->appointment_time }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge 
                                        @if($appointment->status == 'pending') bg-warning text-dark
                                        @elseif($appointment->status == 'approved') bg-success
                                        @elseif($appointment->status == 'completed') bg-info
                                        @elseif($appointment->status == 'cancelled') bg-danger
                                        @else bg-secondary
                                        @endif">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                    <div class="mt-1">
                                        <a href="{{ route('patient.appointment.show', $appointment) }}" class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-calendar text-muted mb-3" style="font-size: 3rem;"></i>
                                <p class="text-muted mb-3">No appointments found.</p>
                                <a href="{{ route('patient.book-appointment') }}" class="btn btn-primary">Book Your First Appointment</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-md-4">
                <div class="card chart-card">
                    <div class="chart-header">
                        <h5 class="mb-0 text-dark">Quick Actions</h5>
                    </div>
                    <div class="chart-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('patient.book-appointment') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i> Book New Appointment
                            </a>
                            <a href="{{ route('patient.appointments') }}" class="btn btn-outline-primary">
                                <i class="fas fa-calendar me-2"></i> View All Appointments
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Appointment History Chart
const appointmentHistoryCtx = document.getElementById('appointmentHistoryChart').getContext('2d');
new Chart(appointmentHistoryCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Appointments',
            data: [2, 3, 1, 4, 2, 3],
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: '#f1f3f4'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

// Service Types Chart
const serviceTypesCtx = document.getElementById('serviceTypesChart').getContext('2d');
new Chart(serviceTypesCtx, {
    type: 'doughnut',
    data: {
        labels: ['General Checkup', 'Prenatal', 'Medical Check-up', 'Immunization'],
        datasets: [{
            data: [3, 2, 1, 1],
            backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endsection
