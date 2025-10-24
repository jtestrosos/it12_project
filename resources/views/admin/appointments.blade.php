<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - Barangay Health Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            background: #f8f9fa;
            min-height: 100vh;
            border-right: 1px solid #e9ecef;
        }
        .sidebar .nav-link {
            color: #495057;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 8px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover {
            background-color: #e9ecef;
            color: #495057;
        }
        .sidebar .nav-link.active {
            background-color: #007bff;
            color: white;
        }
        .main-content {
            background-color: #f0f0f0;
            min-height: 100vh;
        }
        .header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 2rem;
        }
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
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 p-0">
                <div class="sidebar">
                    <div class="p-3">
                        <div class="d-flex align-items-center mb-4">
                            <img src="{{ asset('images/malasakit-logo-blue.png') }}" alt="Logo" class="me-3" style="width: 40px; height: 40px;">
                            <div>
                                <h6 class="mb-0 fw-bold">Barangay Health Center</h6>
                                <small class="text-muted">Staff Management System</small>
                            </div>
                        </div>
                        <nav class="nav flex-column">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-th-large me-2"></i> Dashboard
                            </a>
                            <a class="nav-link" href="{{ route('admin.patients') }}">
                                <i class="fas fa-user me-2"></i> Patient Management
                            </a>
                            <a class="nav-link active" href="{{ route('admin.appointments') }}">
                                <i class="fas fa-calendar-check me-2"></i> Appointments
                            </a>
                            <a class="nav-link" href="{{ route('admin.reports') }}">
                                <i class="fas fa-chart-bar me-2"></i> Services & Reports
                            </a>
                            <a class="nav-link" href="{{ route('admin.inventory') }}">
                                <i class="fas fa-box me-2"></i> Inventory
                            </a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-0">
                <div class="main-content">
                    <!-- Header -->
                    <div class="header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">Manage Appointments</h4>
                            <p class="text-muted mb-0">View and manage all patient appointments</p>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-bell text-muted me-3"></i>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    {{ substr(Auth::user()->name, 0, 2) }}
                                </div>
                                <div>
                                    <div class="fw-bold">{{ Auth::user()->name }}</div>
                                    <small class="text-muted">Admin</small>
                                </div>
                            </div>
                            <a href="{{ route('logout') }}" class="btn btn-outline-secondary ms-3" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <!-- Add Appointment Button -->
                        <div class="d-flex justify-content-end mb-4">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
                                <i class="fas fa-plus me-2"></i> Add New Appointment
                            </button>
                        </div>

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
                                                    @if($appointment->status == 'pending')
                                                        <div class="btn-group" role="group">
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
                                                        </div>
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
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>