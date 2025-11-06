<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Management - Barangay Health Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { color: #111; }
        body.bg-dark { color: #fff; }
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
        .patient-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
            border: none;
            transition: transform 0.2s ease;
        }
        .patient-card:hover {
            transform: translateY(-2px);
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        .patient-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #007bff, #0056b3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }
        /* Cards inherit theme text color */
        .card, .patient-card { color: inherit; }
        /* Dark mode surfaces */
        body.bg-dark .main-content { background-color: #151718; }
        body.bg-dark .sidebar { background: #131516; border-right-color: #2a2f35; }
        body.bg-dark .header { background: #1b1e20; border-bottom-color: #2a2f35; }
        body.bg-dark .card, body.bg-dark .patient-card { background: #1e2124; color: #e6e6e6; box-shadow: 0 2px 8px rgba(0,0,0,0.3); }
        body.bg-dark .table thead, body.bg-dark .table-light { background: #1a1f24 !important; color: #e6e6e6; }
        /* Muted text visibility */
        h1, h2, h3, h4, h5, h6 { color: inherit; }
        body.bg-dark .text-muted, body.bg-dark small { color: #b0b0b0 !important; }
        /* Dark mode modal (Add/Edit Patient) */
        body.bg-dark .modal-content { background: #1e2124; color: #e6e6e6; border-color: #2a2f35; }
        body.bg-dark .modal-content .form-label { color: #e6e6e6; }
        body.bg-dark .modal-content .form-control,
        body.bg-dark .modal-content .form-select { background-color: #0f1316; color: #e6e6e6; border-color: #2a2f35; }
        body.bg-dark .modal-content .form-control::placeholder { color: #9aa4ad; }
        /* Dark mode alerts */
        body.bg-dark .alert-success { background-color: #1e3a1e; color: #d4edda; border-color: #28a745; }
        body.bg-dark .alert-danger { background-color: #3a1e1e; color: #f8d7da; border-color: #dc3545; }
    </style>
</head>
<body>
    <script>
        (function(){
            if (localStorage.getItem('app-theme') === 'dark') {
                document.body.classList.add('bg-dark','text-white');
            }
        })();
    </script>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 p-0">
                <div class="sidebar">
                    <div class="p-3">
                        <div class="d-flex align-items-center mb-4">
                            <img src="{{ asset('images/malasakit-logo-blue.png') }}" alt="Logo" class="me-3" style="width: 52px; height: 52px;">
                            <div>
                                <h6 class="mb-0 fw-bold" style="letter-spacing:.5px;">MALASAKIT</h6>
                            </div>
                        </div>
                        <nav class="nav flex-column">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-th-large me-2"></i> Dashboard
                            </a>
                            <a class="nav-link active" href="{{ route('admin.patients') }}">
                                <i class="fas fa-user me-2"></i> Patient Management
                            </a>
                            <a class="nav-link" href="{{ route('admin.appointments') }}">
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
                            <h4 class="mb-0">Patient Management</h4>
                            <p class="text-muted mb-0">View and manage all registered patients</p>
                        </div>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-link text-decoration-none text-muted me-2" id="themeToggle" title="Toggle theme" aria-label="Toggle theme">
                                <i class="fas fa-moon"></i>
                            </button>
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

                    <!-- Success/Error Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show m-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show m-4" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show m-4" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Content -->
                                                                                                    @if($patients->count() > 0)

                    <div class="p-4">
                        <!-- Top actions -->
                        <div class="d-flex flex-wrap justify-content-end align-items-center mb-3 ">
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPatientModal">
                                    <i class="fas fa-plus me-2"></i> Add New Patient
                                </button>
                            </div>
                            <div></div>
                        </div>

                        <!-- Search and Filter -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-2">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" id="statusFilter">
                                            <option value="">All</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Barangay</label>
                                        <input type="text" class="form-control" id="barangayFilter" placeholder="e.g. Poblacion">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Appointments</label>
                                        <select class="form-select" id="appointmentFilter">
                                            <option value="">All</option>
                                            <option value="with-appointments">With Appointments</option>
                                            <option value="no-appointments">No Appointments</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Patients Table -->
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Patient</th>
                                                <th>Email</th>
                                                <th>Status</th>
                                                <th>Registered</th>
                                                <th>Appointments</th>
                                                <th>Last Visit</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="patientsTableBody">
                                            @foreach($patients as $patient)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="patient-avatar me-3" style="width: 40px; height: 40px; font-size: 0.9rem;">
                                                            {{ substr($patient->name, 0, 2) }}
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold">{{ $patient->name }}</div>
                                                            <small class="text-muted">{{ $patient->barangay ?? 'N/A' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $patient->email }}</td>
                                                <td>
                                                    <span class="status-badge status-active">
                                                        Active Patient
                                                    </span>
                                                </td>
                                                <td>{{ $patient->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $patient->appointments->count() }}</span>
                                                </td>
                                                <td>
                                                    @if($patient->appointments->count() > 0)
                                                        {{ $patient->appointments->sortByDesc('appointment_date')->first()->appointment_date->format('M d, Y') }}
                                                    @else
                                                        <span class="text-muted">Never</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#viewPatientModal{{ $patient->id }}">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#appointmentModal{{ $patient->id }}">
                                                            <i class="fas fa-calendar-plus"></i>
                                                        </button>
                                                        <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editPatientModal{{ $patient->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                                                                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-user me-2 fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No patient found</h5>
                                <p class="text-muted">Pending for users to create their account.</p>
                                
                            </div> 
@endif
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $patients->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Patient Modal -->
    <div class="modal fade" id="addPatientModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Patient</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.patient.create') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="barangay" class="form-label">Barangay <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="barangay" name="barangay" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Patient</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Patient Modal -->
    @foreach($patients as $patient)
    <div class="modal fade" id="viewPatientModal{{ $patient->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Patient Details - {{ $patient->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Personal Information</h6>
                            <div class="mb-3">
                                <label class="form-label text-muted">Full Name</label>
                                <p class="fw-bold">{{ $patient->name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Email</label>
                                <p>{{ $patient->email }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Registration Date</label>
                                <p>{{ $patient->created_at->format('F d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Appointment History</h6>
                            @if($patient->appointments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Service</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($patient->appointments->take(5) as $appointment)
                                            <tr>
                                                <td>{{ $appointment->appointment_date->format('M d, Y') }}</td>
                                                <td>{{ $appointment->service_type }}</td>
                                                <td>
                                                    <span class="badge 
                                                        @if($appointment->status == 'pending') bg-warning
                                                        @elseif($appointment->status == 'approved') bg-success
                                                        @elseif($appointment->status == 'completed') bg-info
                                                        @elseif($appointment->status == 'cancelled') bg-danger
                                                        @else bg-secondary
                                                        @endif">
                                                        {{ ucfirst($appointment->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No appointment history</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Edit Patient Modal -->
    @foreach($patients as $patient)
    <div class="modal fade" id="editPatientModal{{ $patient->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Patient - {{ $patient->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.patient.update', $patient->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_name{{ $patient->id }}" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_name{{ $patient->id }}" name="name" value="{{ $patient->name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_email{{ $patient->id }}" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="edit_email{{ $patient->id }}" name="email" value="{{ $patient->email }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_barangay{{ $patient->id }}" class="form-label">Barangay <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_barangay{{ $patient->id }}" name="barangay" value="{{ $patient->barangay ?? '' }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_phone{{ $patient->id }}" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="edit_phone{{ $patient->id }}" name="phone" value="{{ $patient->phone ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="edit_address{{ $patient->id }}" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="edit_address{{ $patient->id }}" name="address" value="{{ $patient->address ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_password{{ $patient->id }}" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="edit_password{{ $patient->id }}" name="password" placeholder="Leave blank to keep current password">
                                    <small class="text-muted">Leave blank if you don't want to change the password</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_password_confirmation{{ $patient->id }}" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="edit_password_confirmation{{ $patient->id }}" name="password_confirmation" placeholder="Confirm new password">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Patient</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Create Appointment Modal -->
    @foreach($patients as $patient)
    <div class="modal fade" id="appointmentModal{{ $patient->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Appointment for {{ $patient->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.appointment.create') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $patient->id }}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="appointment_patient_name{{ $patient->id }}" class="form-label">Patient Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="appointment_patient_name{{ $patient->id }}" name="patient_name" value="{{ $patient->name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="appointment_patient_phone{{ $patient->id }}" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="appointment_patient_phone{{ $patient->id }}" name="patient_phone" value="{{ $patient->phone ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="appointment_patient_address{{ $patient->id }}" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="appointment_patient_address{{ $patient->id }}" name="patient_address" value="{{ $patient->address ?? $patient->barangay ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="appointment_service_type{{ $patient->id }}" class="form-label">Service Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="appointment_service_type{{ $patient->id }}" name="service_type" required>
                                        <option value="">Select Service</option>
                                        <option value="General Checkup">General Checkup</option>
                                        <option value="Prenatal">Prenatal</option>
                                        <option value="Medical Check-up">Medical Check-up</option>
                                        <option value="Immunization">Immunization</option>
                                        <option value="Family Planning">Family Planning</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="appointment_date{{ $patient->id }}" class="form-label">Appointment Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="appointment_date{{ $patient->id }}" name="appointment_date" min="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="appointment_time{{ $patient->id }}" class="form-label">Appointment Time <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="appointment_time{{ $patient->id }}" name="appointment_time" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="appointment_notes{{ $patient->id }}" class="form-label">Notes</label>
                            <textarea class="form-control" id="appointment_notes{{ $patient->id }}" name="notes" rows="3" placeholder="Additional notes or comments"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Appointment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search and Filter Functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Theme toggle persistence
            (function(){
                const key = 'app-theme';
                const btn = document.getElementById('themeToggle');
                if (btn) {
                    btn.addEventListener('click', function(){
                        const isDark = document.body.classList.toggle('bg-dark');
                        document.body.classList.toggle('text-white', isDark);
                        localStorage.setItem(key, isDark ? 'dark' : 'light');
                    });
                }
            })();
            const statusFilter = document.getElementById('statusFilter');
            const appointmentFilter = document.getElementById('appointmentFilter');
            const barangayFilter = document.getElementById('barangayFilter');
            const tableBody = document.getElementById('patientsTableBody');
            const rows = tableBody.querySelectorAll('tr');

            function filterTable() {
                const statusValue = statusFilter.value;
                const appointmentValue = appointmentFilter.value;
                const barangayValue = barangayFilter.value.toLowerCase();

                rows.forEach(row => {
                    const name = row.cells[0].textContent.toLowerCase();
                    const email = row.cells[1].textContent.toLowerCase();
                    const status = row.cells[2].textContent.toLowerCase();
                    const appointments = parseInt(row.cells[4].textContent);
                    const barangay = row.cells[0].textContent.toLowerCase();

                    let showRow = true;

                    // Status filter
                    if (statusValue) {
                        if (statusValue === 'active' && !status.includes('active')) {
                            showRow = false;
                        } else if (statusValue === 'inactive' && !status.includes('inactive')) {
                            showRow = false;
                        }
                    }

                    // Appointment filter
                    if (appointmentValue) {
                        if (appointmentValue === 'with-appointments' && appointments === 0) {
                            showRow = false;
                        } else if (appointmentValue === 'no-appointments' && appointments > 0) {
                            showRow = false;
                        }
                    }

                    // Barangay filter (simple contains)
                    if (barangayValue && !barangay.includes(barangayValue)) {
                        showRow = false;
                    }

                    row.style.display = showRow ? '' : 'none';
                });
            }

            statusFilter.addEventListener('change', filterTable);
            appointmentFilter.addEventListener('change', filterTable);
            barangayFilter.addEventListener('input', filterTable);
            
        });
    </script>
</body>
</html>
