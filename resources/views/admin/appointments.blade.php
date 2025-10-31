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
        /* Ensure dropdowns inside responsive tables are not clipped */
        .table-responsive { overflow: visible; }
        /* Keep actions column flexible */
        .actions-col { white-space: nowrap; }
        /* Dark mode modal + form fields (booking drawer/modal) */
        body.bg-dark .offcanvas, body.bg-dark .modal-content { background: #1e2124; color: #e6e6e6; border-color: #2a2f35; }
        body.bg-dark .offcanvas .form-label, body.bg-dark .modal-content .form-label { color: #e6e6e6; }
        body.bg-dark .offcanvas .form-control,
        body.bg-dark .offcanvas .form-select,
        body.bg-dark .modal-content .form-control,
        body.bg-dark .modal-content .form-select { background-color: #0f1316; color: #e6e6e6; border-color: #2a2f35; }
        body.bg-dark .offcanvas .form-control::placeholder,
        body.bg-dark .modal-content .form-control::placeholder { color: #9aa4ad; }
        body.bg-dark .offcanvas .input-group-text { background: #1a1f24; color: #cbd3da; border-color: #2a2f35; }
    </style>
@endsection

@section('content')
                        <!-- Top Actions -->
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#bookDrawer">
                                    <i class="fas fa-plus me-2"></i> Book for Patient
                            </button>
                            </div>
                            <div></div>
                        </div>

                        <!-- Status Tabs -->
                        <ul class="nav nav-tabs mb-3">
                            @php($current = request('status'))
                            <li class="nav-item"><a class="nav-link {{ $current=='' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['status' => null]) }}">All</a></li>
                            <li class="nav-item"><a class="nav-link {{ $current=='pending' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}">Pending</a></li>
                            <li class="nav-item"><a class="nav-link {{ $current=='approved' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}">Confirmed</a></li>
                            <li class="nav-item"><a class="nav-link {{ $current=='completed' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['status' => 'completed']) }}">Completed</a></li>
                            <li class="nav-item"><a class="nav-link {{ $current=='cancelled' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['status' => 'cancelled']) }}">Cancelled</a></li>
                        </ul>

                        <!-- Filters -->
                        <form method="GET" class="filter-card">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">Date range</label>
                                    <div class="input-group">
                                        <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                                        <span class="input-group-text">to</span>
                                        <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                                    </div>
                            </div>
                                <div class="col-md-2">
                                    <label class="form-label">Service</label>
                                    <select name="service" class="form-select">
                                        <option value="">All</option>
                                        @foreach(($services ?? []) as $service)
                                            <option value="{{ $service }}" @selected(request('service')==$service)>{{ $service }}</option>
                                        @endforeach
                                </select>
                            </div>
                                
                                <div class="col-md-3">
                                    <label class="form-label">Patient</label>
                                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Name, phone, or email">
                                </div>
                                <div class="col-md-2 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-2"></i>Search</button>
                                    <a href="{{ route('admin.appointments') }}" class="btn btn-outline-secondary">Reset</a>
                                </div>
                            </div>
                            <div class="mt-2 small text-muted">Showing {{ $appointments->total() ?? count($appointments) }} results</div>
                        </form>

                        <!-- Appointments Table -->
                        <div class="table-card p-0">
                                <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="table-light position-sticky top-0" style="z-index:1;">
                                            <tr>
                                            <th style="width:30px"><input type="checkbox" id="selectAll"></th>
                                            <th><a href="{{ request()->fullUrlWithQuery(['sort' => 'date']) }}" class="text-decoration-none">Date</a></th>
                                                <th>Patient</th>
                                                <th>Service</th>
                                            
                                                <th>Status</th>
                                            <th class="actions-col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($appointments as $appointment)
                                        <tr>
                                            <td><input type="checkbox" class="row-check"></td>
                                            <td>
                                                <div class="fw-bold">{{ $appointment->appointment_date->format('M d, Y') }} {{ $appointment->appointment_time }}</div>
                                                <div class="progress" style="height:6px;">
                                                    <div class="progress-bar bg-{{ ($appointment->capacity_fill ?? 0) > 80 ? 'danger' : (($appointment->capacity_fill ?? 0) > 50 ? 'warning' : 'success') }}" role="progressbar" style="width: {{ $appointment->capacity_fill ?? 0 }}%"></div>
                                                </div>
                                            </td>
                                                <td>
                                                    <div class="fw-bold">{{ $appointment->patient_name }}</div>
                                                <small class="text-muted">{{ $appointment->user->email ?? $appointment->patient_phone }}</small>
                                                </td>
                                                <td>{{ $appointment->service_type }}</td>
                                            
                                                <td>
                                                <span class="status-badge status-{{ $appointment->status }}">{{ ucfirst($appointment->status) }}</span>
                                                </td>
                                            <td class="actions-col">
                                                <div class="btn-group">
                                                    <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal{{ $appointment->id }}">View</button>
                                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" data-bs-display="static"></button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <form method="POST" action="{{ route('admin.appointment.update', $appointment) }}" class="px-3 py-1">
                                                                @csrf
                                                                <input type="hidden" name="status" value="approved">
                                                                <button type="submit" class="btn btn-link p-0">Confirm</button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reschedModal{{ $appointment->id }}">Reschedule…</button>
                                                        </li>
                                                        <li>
                                                            <form method="POST" action="{{ route('admin.appointment.update', $appointment) }}" class="px-3 py-1">
                                                                @csrf
                                                                <input type="hidden" name="status" value="completed">
                                                                <button type="submit" class="btn btn-link p-0">Mark Completed</button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form method="POST" action="{{ route('admin.appointment.update', $appointment) }}" class="px-3 py-1">
                                                                @csrf
                                                                <input type="hidden" name="status" value="cancelled">
                                                                <input type="hidden" name="notes" value="No-show">
                                                                <button type="submit" class="btn btn-link p-0">Mark No-show</button>
                                                            </form>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form method="POST" action="{{ route('admin.appointment.update', $appointment) }}" class="px-3 py-1">
                                                                @csrf
                                                                <input type="hidden" name="status" value="cancelled">
                                                                <button type="submit" class="btn btn-link text-danger p-0">Cancel</button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            </tr>
                                        <!-- Reschedule Modal -->
                                        <div class="modal fade" id="reschedModal{{ $appointment->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Reschedule Appointment</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST" action="{{ route('admin.appointment.update', $appointment) }}">
                                                        @csrf
                                                        <input type="hidden" name="status" value="rescheduled">
                                                        <div class="modal-body">
                                                            <div class="row g-3">
                                                                <div class="col-md-6">
                                                                    <label class="form-label">New Date</label>
                                                                    <input type="date" name="new_date" class="form-control" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">New Time</label>
                                                                    <input type="time" name="new_time" class="form-control" required>
                                                                </div>
                                                            </div>
                                                            <div class="mt-3">
                                                                <label class="form-label">Notes (optional)</label>
                                                                <textarea class="form-control" name="notes" rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Save</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- View Modal -->
                                        <div class="modal fade" id="viewAppointmentModal{{ $appointment->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Appointment Details</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-2"><strong>Patient:</strong> {{ $appointment->patient_name }}</div>
                                                        <div class="mb-2"><strong>Service:</strong> {{ $appointment->service_type }}</div>
                                                        <div class="mb-2"><strong>Date/Time:</strong> {{ $appointment->appointment_date->format('M d, Y') }} {{ $appointment->appointment_time }}</div>
                                                        <div class="mb-2"><strong>Status:</strong> {{ ucfirst($appointment->status) }}</div>
                                                        @if($appointment->notes)
                                                            <div class="mb-2"><strong>Notes:</strong> {{ $appointment->notes }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <div class="mb-2">No appointments match your filters.</div>
                                                <a class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#bookDrawer">Book an appointment</a>
                                            </td>
                                        </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $appointments->links() }}
                        </div>

                        <!-- Book Drawer (Right Side) -->
                        <div class="offcanvas offcanvas-end" tabindex="-1" id="bookDrawer" style="width: 520px;">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title">Book Appointment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="{{ route('admin.appointment.create') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Patient Full Name</label>
                                        <input type="text" class="form-control" name="patient_name" placeholder="e.g., Juan Dela Cruz" required>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Phone (optional)</label>
                                            <input type="tel" class="form-control" name="patient_phone" placeholder="09XXXXXXXXX">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Address (optional)</label>
                                            <input type="text" class="form-control" name="patient_address" placeholder="Barangay / Street">
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Service</label>
                                            <select class="form-select" name="service_type" required>
                                                <option value="">Select service</option>
                                                @foreach(($services ?? []) as $service)
                                                    <option value="{{ $service }}">{{ $service }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label class="form-label">Date</label>
                                            <input type="date" class="form-control" name="appointment_date" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Time</label>
                                            <input type="time" class="form-control" name="appointment_time" required>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <label class="form-label">Availability (today)</label>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $todayCapacity ?? 0 }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $todayBooked ?? 0 }} booked / {{ $todaySlots ?? 0 }} slots</small>
                                    </div>
                                    <div class="mt-3">
                                        <label class="form-label">Notes</label>
                                        <textarea class="form-control" name="notes" rows="3"></textarea>
                                    </div>
                                    <div class="d-grid mt-3">
                                        <button type="submit" class="btn btn-primary">Create booking</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Add Appointment Modal (legacy, kept for now) -->
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