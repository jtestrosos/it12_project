@extends('admin.layout')

@section('title', 'Patient Management - Barangay Health Center')
@section('page-title', 'Patient Management')
@section('page-description', 'View and manage all registered patients')

@section('page-styles')
<style>
        body { color: inherit; }
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
@endsection

@section('content')
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

                    <div class="p-0 p-md-4">
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
                                        <label class="form-label">Search</label>
                                        <input type="text" class="form-control" id="barangayFilter" placeholder="Enter Patient's Name, Barangay or Purok">
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
                                                            @php
                                                                $listBarangayLabel = $patient->barangay === 'Other'
                                                                    ? ($patient->barangay_other ?? 'Other Barangay')
                                                                    : ($patient->barangay ?? 'N/A');
                                                            @endphp
                                                            <small class="text-muted">
                                                                {{ $listBarangayLabel }}
                                                                @if($patient->purok)
                                                                    · {{ $patient->purok }}
                                                                @endif
                                                            </small>
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
@endsection

    <!-- Add Patient Modal -->
    <div class="modal fade" id="addPatientModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Patient</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.patient.create') }}" method="POST" class="patient-registration-form">
                    @csrf
                    @php
                        $oldBarangay = old('barangay');
                        $purokOptions = match ($oldBarangay) {
                            'Barangay 11' => ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'],
                            'Barangay 12' => ['Purok 1', 'Purok 2', 'Purok 3'],
                            default => [],
                        };
                    @endphp
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                    <select class="form-control @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number <small class="text-muted">(Optional)</small></label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="barangay" class="form-label">Barangay <span class="text-danger">*</span></label>
                                    <select class="form-control @error('barangay') is-invalid @enderror" id="barangay" name="barangay" data-role="barangay" required>
                                        <option value="">Select Barangay</option>
                                        <option value="Barangay 11" {{ $oldBarangay === 'Barangay 11' ? 'selected' : '' }}>Barangay 11</option>
                                        <option value="Barangay 12" {{ $oldBarangay === 'Barangay 12' ? 'selected' : '' }}>Barangay 12</option>
                                        <option value="Other" {{ $oldBarangay === 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('barangay')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 {{ in_array($oldBarangay, ['Barangay 11', 'Barangay 12']) ? '' : 'd-none' }}" data-role="purok-group">
                                    <label for="purok" class="form-label">Purok <span class="text-danger">*</span></label>
                                    <select class="form-control @error('purok') is-invalid @enderror" id="purok" name="purok" data-role="purok" data-selected="{{ old('purok') }}">
                                        <option value="">Select Purok</option>
                                        @foreach ($purokOptions as $purok)
                                            <option value="{{ $purok }}" {{ old('purok') === $purok ? 'selected' : '' }}>{{ $purok }}</option>
                                        @endforeach
                                    </select>
                                    @error('purok')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-end">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="birth_date" class="form-label">Birth Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" data-role="birth-date" max="{{ now()->toDateString() }}" required>
                                    @error('birth_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 {{ $oldBarangay === 'Other' ? '' : 'd-none' }}" data-role="barangay-other-group">
                                    <label for="barangay_other" class="form-label">Specify Barangay <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('barangay_other') is-invalid @enderror" id="barangay_other" name="barangay_other" value="{{ old('barangay_other') }}" data-role="barangay-other">
                                    @error('barangay_other')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address <small class="text-muted">(Optional)</small></label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2" placeholder="Enter complete address (optional)">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" required>
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                <label class="form-label text-muted">Gender</label>
                                <p>{{ ucfirst($patient->gender ?? 'N/A') }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Email</label>
                                <p>{{ $patient->email }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Phone</label>
                                <p>{{ $patient->phone ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Barangay / Purok</label>
                                <p>
                                    @php
                                        $barangayLabel = $patient->barangay === 'Other'
                                            ? ($patient->barangay_other ?? 'Other Barangay')
                                            : ($patient->barangay ?? 'N/A');
                                    @endphp
                                    {{ $barangayLabel }}
                                    @if($patient->purok)
                                        <span class="text-muted">· {{ $patient->purok }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Birth Date</label>
                                <p>{{ $patient->birth_date ? \Illuminate\Support\Carbon::parse($patient->birth_date)->format('F d, Y') : 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Age</label>
                                <p>{{ $patient->age ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Address</label>
                                <p>{{ $patient->address ?? 'N/A' }}</p>
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
                <form action="{{ route('admin.patient.update', $patient->id) }}" method="POST" class="patient-registration-form">
                    @csrf
                    @method('PUT')
                    @php
                        $editBarangay = old('barangay', $patient->barangay);
                        $editPurokOptions = match ($editBarangay) {
                            'Barangay 11' => ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'],
                            'Barangay 12' => ['Purok 1', 'Purok 2', 'Purok 3'],
                            default => [],
                        };
                        $editBirthDateValue = old('birth_date', $patient->birth_date ? \Illuminate\Support\Carbon::parse($patient->birth_date)->format('Y-m-d') : '');
                        $editAgeValue = $editBirthDateValue ? \Illuminate\Support\Carbon::parse($editBirthDateValue)->age : ($patient->age ?? '');
                    @endphp
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_name{{ $patient->id }}" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="edit_name{{ $patient->id }}" name="name" value="{{ old('name', $patient->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_gender{{ $patient->id }}" class="form-label">Gender <span class="text-danger">*</span></label>
                                    <select class="form-control @error('gender') is-invalid @enderror" id="edit_gender{{ $patient->id }}" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $patient->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $patient->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $patient->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_email{{ $patient->id }}" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="edit_email{{ $patient->id }}" name="email" value="{{ old('email', $patient->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_phone{{ $patient->id }}" class="form-label">Phone Number <small class="text-muted">(Optional)</small></label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="edit_phone{{ $patient->id }}" name="phone" value="{{ old('phone', $patient->phone ?? '') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_barangay{{ $patient->id }}" class="form-label">Barangay <span class="text-danger">*</span></label>
                                    <select class="form-control @error('barangay') is-invalid @enderror" id="edit_barangay{{ $patient->id }}" name="barangay" data-role="barangay" required>
                                        <option value="">Select Barangay</option>
                                        <option value="Barangay 11" {{ $editBarangay === 'Barangay 11' ? 'selected' : '' }}>Barangay 11</option>
                                        <option value="Barangay 12" {{ $editBarangay === 'Barangay 12' ? 'selected' : '' }}>Barangay 12</option>
                                        <option value="Other" {{ $editBarangay === 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('barangay')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 {{ in_array($editBarangay, ['Barangay 11', 'Barangay 12']) ? '' : 'd-none' }}" data-role="purok-group">
                                    <label for="edit_purok{{ $patient->id }}" class="form-label">Purok <span class="text-danger">*</span></label>
                                    <select class="form-control @error('purok') is-invalid @enderror" id="edit_purok{{ $patient->id }}" name="purok" data-role="purok" data-selected="{{ old('purok', $patient->purok ?? '') }}">
                                        <option value="">Select Purok</option>
                                        @foreach ($editPurokOptions as $purok)
                                            <option value="{{ $purok }}" {{ old('purok', $patient->purok ?? '') === $purok ? 'selected' : '' }}>{{ $purok }}</option>
                                        @endforeach
                                    </select>
                                    @error('purok')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-end">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_birth_date{{ $patient->id }}" class="form-label">Birth Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="edit_birth_date{{ $patient->id }}" name="birth_date" value="{{ $editBirthDateValue }}" data-role="birth-date" max="{{ now()->toDateString() }}" required>
                                    @error('birth_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 {{ $editBarangay === 'Other' ? '' : 'd-none' }}" data-role="barangay-other-group">
                                    <label for="edit_barangay_other{{ $patient->id }}" class="form-label">Specify Barangay <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('barangay_other') is-invalid @enderror" id="edit_barangay_other{{ $patient->id }}" name="barangay_other" value="{{ old('barangay_other', $patient->barangay_other ?? '') }}" data-role="barangay-other">
                                    @error('barangay_other')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_address{{ $patient->id }}" class="form-label">Address <small class="text-muted">(Optional)</small></label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" id="edit_address{{ $patient->id }}" name="address" rows="2" placeholder="Enter complete address (optional)">{{ old('address', $patient->address ?? '') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_password{{ $patient->id }}" class="form-label">New Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="edit_password{{ $patient->id }}" name="password" placeholder="Leave blank to keep current password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_password_confirmation{{ $patient->id }}" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="edit_password_confirmation{{ $patient->id }}" name="password_confirmation" placeholder="Confirm new password">
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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

@push('scripts')
<script>
        // Search and Filter Functionality
        document.addEventListener('DOMContentLoaded', function() {
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
@endpush
