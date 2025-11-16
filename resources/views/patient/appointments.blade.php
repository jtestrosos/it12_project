@extends('layouts.admin')

@section('title', 'My Appointments - Patient Portal')
@section('page-title', 'My Appointments')
@section('page-description', 'Manage your healthcare appointments.')

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
    .appointments-card {
        background: #fafafa;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
    }
    body.bg-dark .appointments-card {
        background: #1e2124;
        border-color: #2a2f35;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }
    .appointments-header {
        background: #fafafa;
        border-bottom: 1px solid #e9ecef;
        padding: 1.5rem;
        margin-bottom: 0;
        border-radius: 12px 12px 0 0;
    }
    body.bg-dark .appointments-header {
        background: #1e2124;
        border-color: #2a2f35;
    }
    .appointments-body {
        padding: 1.5rem;
    }
    .table-modern {
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-modern thead th {
        background-color: #f5f5f5;
        border: none;
        font-weight: 600;
        color: #495057;
        padding: 1rem;
    }
    body.bg-dark .table-modern thead th {
        background-color: #1a1f24;
        color: #e6e6e6;
    }
    .table-modern tbody td {
        border: none;
        padding: 1rem;
        border-bottom: 1px solid #f1f3f4;
    }
    body.bg-dark .table-modern tbody td {
        border-bottom-color: #2a2f35;
        color: #e6e6e6;
    }
    .table-modern tbody tr:hover {
        background-color: #f8f9fa;
    }
    body.bg-dark .table-modern tbody tr:hover {
        background-color: #2a2f35;
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
    body.bg-dark .fw-bold.text-dark {
        color: #e6e6e6 !important;
    }
</style>
@endsection

@section('content')
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
            <div class="card appointments-card">
                <div class="appointments-header">
                    <h5 class="mb-0">All Appointments</h5>
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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Sync table with dark mode on page load
        const syncTableDark = () => {
            const isDark = document.body.classList.contains('bg-dark');
            const table = document.querySelector('.table-modern');
            if (table) {
                table.classList.toggle('table-dark', isDark);
            }
        };
        
        // Sync on load
        syncTableDark();
        
        // Watch for theme changes
        const observer = new MutationObserver(() => {
            syncTableDark();
        });
        observer.observe(document.body, {
            attributes: true,
            attributeFilter: ['class']
        });
    });
</script>
@endpush
