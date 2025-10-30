@extends('admin.layout')

@section('title', 'Appointments - Barangay Health Center')
@section('page-title', 'Manage Appointments')
@section('page-description', 'View and manage all patient appointments')

@section('page-styles')
<style>
        .appointment-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
            border: none;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        .status-completed {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
@endsection

@section('content')
                        <!-- Add Appointment Button -->
                        <div class="d-flex justify-content-end mb-4">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
                                <i class="fas fa-plus me-2"></i> Add New Appointment
                            </button>
                        </div>

                        <!-- Search and Filter -->
                        <form method="GET" class="row g-2 mb-3 align-items-end">
                            <div class="col-md-4">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by patient, phone, or service...">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-2"></i>Search
                                </button>
                            </div>
                        </form>

                        <!-- Appointments Table -->
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Patient</th>
                                                <th>Phone</th>
                                                <th>Date & Time</th>
                                                <th>Service</th>
                                                <th>Status</th>
                                                <th>Notes</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($appointments as $appointment)
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">{{ $appointment->patient_name }}</div>
                                                    @if($appointment->user)
                                                        <small class="text-muted">{{ $appointment->user->email }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $appointment->patient_phone }}</td>
                                                <td>
                                                    <div class="fw-bold">{{ $appointment->appointment_date->format('M d, Y') }}</div>
                                                    <small class="text-muted">{{ $appointment->appointment_time }}</small>
                                                </td>
                                                <td>{{ $appointment->service_type }}</td>
                                                <td>
                                                    <span class="status-badge status-{{ $appointment->status }}">
                                                        {{ ucfirst($appointment->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($appointment->notes)
                                                        <small class="text-muted">{{ Str::limit($appointment->notes, 30) }}</small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal{{ $appointment->id }}">
                                                            <i class="fas fa-eye"></i> View
                                                        </button>
                                                    @if($appointment->status == 'pending')
                                                        <form method="POST" action="{{ route('admin.appointment.update', $appointment) }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="status" value="approved">
                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('admin.appointment.update', $appointment) }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="status" value="cancelled">
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    @elseif($appointment->status == 'approved')
                                                        <form method="POST" action="{{ route('admin.appointment.update', $appointment) }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="status" value="completed">
                                                            <button type="submit" class="btn btn-info btn-sm">
                                                                <i class="fas fa-check-circle"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $appointments->links() }}
                        </div>
@endsection
@section('content')
<!-- Add Appointment Modal -->
    <div class="modal fade" id="addAppointmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.appointment.create') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="patient_name" class="form-label">Patient Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="patient_name" name="patient_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="patient_phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="patient_phone" name="patient_phone" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="patient_address" class="form-label">Address <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="patient_address" name="patient_address" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="service_type" class="form-label">Service Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="service_type" name="service_type" required>
                                        <option value="">Select Service</option>
                                        <option value="General Checkup">General Checkup</option>
                                        <option value="Prenatal">Prenatal</option>
                                        <option value="Immunization">Immunization</option>
                                        <option value="Family Planning">Family Planning</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="appointment_date" class="form-label">Appointment Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="appointment_date" name="appointment_date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="appointment_time" class="form-label">Appointment Time <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="appointment_time" name="appointment_time" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Appointment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
<script>
    // This script block is for the new per-appointment detail modals
    // It will be added to the existing file's scripts section
</script>
@endsection