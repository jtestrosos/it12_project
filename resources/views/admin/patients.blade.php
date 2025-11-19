@extends('admin.layout')

@section('title', 'Patient Management - Barangay Health Center')
@section('page-title', 'Patient Management')
@section('page-description', 'View and manage all registered patients')

@section('page-styles')
<style>
        body { color: inherit; }
        .patient-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 1.5rem;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
            margin-bottom: 1rem;
            border: 1px solid #edf1f7;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }
        .patient-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.10);
            border-color: #d0e2ff;
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

        /* Calendar Styles */
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
            font-size: 0.8rem;
        }
        .calendar-header {
            text-align: center;
            font-weight: 600;
            padding: 0.5rem;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #dee2e6;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }
        .calendar-day:hover {
            background-color: #e9ecef;
        }
        .calendar-day.selected {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        .calendar-day.occupied {
            background-color: #dc3545;
            color: white;
            border-color: #dc3545;
        }
        .calendar-day.partially-occupied {
            background-color: #ffc107;
            color: #212529;
            border-color: #ffc107;
        }
        .calendar-day.weekend {
            background-color: #f8f9fa;
            color: #6c757d;
        }
        .calendar-day.past {
            background-color: #e9ecef;
            color: #adb5bd;
            cursor: not-allowed;
        }
        .calendar-day .day-number {
            font-weight: 600;
            font-size: 0.9rem;
            z-index: 1;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 70%;
        }
        .calendar-day .slot-indicator {
            position: absolute;
            bottom: 2px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.55rem;
            background: rgba(0,0,0,0.15);
            color: #666;
            padding: 1px 4px;
            border-radius: 3px;
            font-weight: 600;
            z-index: 2;
            line-height: 1;
            width: auto;
            white-space: nowrap;
        }
        .time-slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 0.5rem;
        }
        .time-slot {
            padding: 0.75rem;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .time-slot.available {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .time-slot.available:hover {
            background-color: #c3e6cb;
        }
        .time-slot.occupied {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
            cursor: not-allowed;
        }
        .time-slot.selected {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
        }
        .time-slot .time {
            font-weight: 600;
            font-size: 0.9rem;
        }
        .time-slot .status {
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        /* Dark Mode Overrides */
        body.bg-dark .calendar-header {
            background-color: #2a2f35;
            color: #e6e6e6;
            border-color: #2a2f35;
        }
        body.bg-dark .calendar-day {
            border-color: #2a2f35;
            color: #e6e6e6;
        }
        body.bg-dark .calendar-day:hover {
            background-color: #2a2f35;
        }
        body.bg-dark .calendar-day.weekend {
            background-color: #1e2124;
            color: #6c757d;
        }
        body.bg-dark .calendar-day.past {
            background-color: #1e2124;
            color: #6c757d;
        }
        body.bg-dark .time-slot {
            border-color: #2a2f35;
        }
        body.bg-dark .time-slot.available {
            background-color: #1e3a1f;
            border-color: #2a5f2e;
            color: #90ee90;
        }
        body.bg-dark .time-slot.occupied {
            background-color: #3d1a1a;
            border-color: #5c2a2a;
            color: #ff6b6b;
        }
        body.bg-dark .time-slot.selected {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
        }
        
        /* Dark Mode Pagination */
        body.bg-dark .page-link {
            background-color: #1e2124;
            border-color: #2a2f35;
            color: #e6e6e6;
        }
        body.bg-dark .page-item.disabled .page-link {
            background-color: #1a1d20;
            border-color: #2a2f35;
            color: #6c757d;
        }
        body.bg-dark .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
        }
        body.bg-dark .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
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
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <div></div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('admin.patients.archive') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-archive me-2"></i> View Archived Patients
                                </a>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPatientModal">
                                    <i class="fas fa-plus me-2"></i> Add New Patient
                                </button>
                            </div>
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
                                                        <button class="btn btn-outline-success btn-sm" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#createAppointmentModal"
                                                            data-user-id="{{ $patient->id }}"
                                                            data-user-name="{{ $patient->name }}"
                                                            data-user-phone="{{ $patient->phone ?? '' }}"
                                                            data-user-address="{{ $patient->address ?? $patient->barangay ?? '' }}">
                                                            <i class="fas fa-calendar-plus"></i>
                                                        </button>
                                                        <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editPatientModal{{ $patient->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#archivePatientModal{{ $patient->id }}">
                                                            <i class="fas fa-archive"></i>
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
                            @if ($patients->hasPages())
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center">
                                        {{-- Previous Page Link --}}
                                        @if ($patients->onFirstPage())
                                            <li class="page-item disabled" aria-disabled="true">
                                                <span class="page-link">&lsaquo;</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $patients->previousPageUrl() }}">&lsaquo;</a>
                                            </li>
                                        @endif

                                        {{-- First page --}}
                                        @if ($patients->currentPage() > 3)
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $patients->url(1) }}">1</a>
                                            </li>
                                            @if ($patients->currentPage() > 4)
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            @endif
                                        @endif

                                        {{-- Page numbers --}}
                                        @for ($page = max(1, $patients->currentPage() - 2); $page <= min($patients->lastPage(), $patients->currentPage() + 2); $page++)
                                            @if ($page == $patients->currentPage())
                                                <li class="page-item active" aria-current="page">
                                                    <span class="page-link">{{ $page }}</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $patients->url($page) }}">{{ $page }}</a>
                                                </li>
                                            @endif
                                        @endfor

                                        {{-- Last page --}}
                                        @if ($patients->currentPage() < $patients->lastPage() - 2)
                                            @if ($patients->currentPage() < $patients->lastPage() - 3)
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            @endif
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $patients->url($patients->lastPage()) }}">{{ $patients->lastPage() }}</a>
                                            </li>
                                        @endif

                                        {{-- Next Page Link --}}
                                        @if ($patients->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $patients->nextPageUrl() }}">&rsaquo;</a>
                                            </li>
                                        @else
                                            <li class="page-item disabled" aria-disabled="true">
                                                <span class="page-link">&rsaquo;</span>
                                            </li>
                                        @endif
                                    </ul>
                                </nav>
                            @endif
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
                                        <option value="" disabled selected>Select Gender</option>
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
                                        <option value="" disabled selected>Select Barangay</option>
                                        <option value="Barangay 11" {{ old('barangay') === 'Barangay 11' ? 'selected' : '' }}>Barangay 11</option>
                                        <option value="Barangay 12" {{ old('barangay') === 'Barangay 12' ? 'selected' : '' }}>Barangay 12</option>
                                        <option value="Other" {{ old('barangay') === 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('barangay')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 {{ old('barangay') && in_array(old('barangay'), ['Barangay 11', 'Barangay 12']) ? '' : 'd-none' }}" data-role="purok-group">
                                    <label for="purok" class="form-label">Purok <span class="text-danger">*</span></label>
                                    <select class="form-control @error('purok') is-invalid @enderror" id="purok" name="purok" data-role="purok" data-selected="{{ old('purok') }}">
                                        <option value="" disabled selected>Select Purok</option>
                                    </select>
                                    @error('purok')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3 {{ old('barangay') === 'Other' ? '' : 'd-none' }}" data-role="barangay-other-group">
                                    <label for="barangay_other" class="form-label">Specify Barangay <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('barangay_other') is-invalid @enderror" id="barangay_other" name="barangay_other" value="{{ old('barangay_other') }}" data-role="barangay-other">
                                    @error('barangay_other')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="birth_date" class="form-label">Birth Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" data-role="birth-date" max="{{ now()->toDateString() }}" required>
                                    @error('birth_date')
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

    <!-- Archive Patient Modal -->
    @foreach($patients as $patient)
    <div class="modal fade" id="archivePatientModal{{ $patient->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Archive Patient</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">Are you sure you want to archive this patient?</p>
                    <p class="fw-bold mb-0">{{ $patient->name }}</p>
                    <small class="text-muted">{{ $patient->email }}</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('admin.patient.archive', $patient->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">Archive</button>
                    </form>
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
                                        <option value="" disabled selected>Select Gender</option>
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
                                        <option value="" disabled selected>Select Barangay</option>
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
                                <div class="mb-3 {{ $editBarangay && in_array($editBarangay, ['Barangay 11', 'Barangay 12']) ? '' : 'd-none' }}" data-role="purok-group">
                                    <label for="edit_purok{{ $patient->id }}" class="form-label">Purok <span class="text-danger">*</span></label>
                                    <select class="form-control @error('purok') is-invalid @enderror" id="edit_purok{{ $patient->id }}" name="purok" data-role="purok" data-selected="{{ old('purok', $patient->purok ?? '') }}">
                                        <option value="" disabled selected>Select Purok</option>
                                    </select>
                                    @error('purok')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3 {{ $editBarangay === 'Other' ? '' : 'd-none' }}" data-role="barangay-other-group">
                                    <label for="edit_barangay_other{{ $patient->id }}" class="form-label">Specify Barangay <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('barangay_other') is-invalid @enderror" id="edit_barangay_other{{ $patient->id }}" name="barangay_other" value="{{ old('barangay_other', $patient->barangay_other ?? '') }}" data-role="barangay-other">
                                    @error('barangay_other')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-end">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_birth_date{{ $patient->id }}" class="form-label">Birth Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="edit_birth_date{{ $patient->id }}" name="birth_date" value="{{ old('birth_date', $patient->birth_date ? \Illuminate\Support\Carbon::parse($patient->birth_date)->format('Y-m-d') : '') }}" data-role="birth-date" max="{{ now()->toDateString() }}" required>
                                    @error('birth_date')
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

    <!-- Create Appointment Modal (Single Shared Modal) -->
    <div class="modal fade" id="createAppointmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.appointment.create') }}" method="POST" id="createAppointmentForm">
                    @csrf
                    <input type="hidden" name="user_id" id="appointment_user_id">
                    <div class="modal-body">
                        <!-- Patient Details Section -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Patient Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Patient Name</label>
                                            <input type="text" class="form-control" id="appointment_patient_name" name="patient_name" readonly>
                                        </div>
                                    </div>  
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" id="appointment_patient_phone" name="patient_phone" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Address</label>
                                            <input type="text" class="form-control" id="appointment_patient_address" name="patient_address" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Appointment Details Section -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Appointment Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="service_type" class="form-label">Service Type <span class="text-danger">*</span></label>
                                            <select class="form-select" id="service_type" name="service_type" required>
                                                <option value="">Select Service</option>
                                                <option value="General Checkup">General Checkup</option>
                                                <option value="Prenatal">Prenatal</option>
                                                <option value="Medical Check-up">Medical Check-up</option>
                                                <option value="Immunization">Immunization</option>
                                                <option value="Family Planning">Family Planning</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Calendar Selection -->
                                <div class="mb-3">
                                    <div class="card bg-primary text-white mb-3">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Select Appointment Date & Time</h6>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <!-- Calendar Column -->
                                        <div class="col-md-5">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="prevMonth"><i class="fas fa-chevron-left"></i></button>
                                                <span class="fw-bold" id="currentMonth">Month Year</span>
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="nextMonth"><i class="fas fa-chevron-right"></i></button>
                                            </div>
                                            <div class="calendar-grid" id="calendarGrid">
                                                <!-- Calendar generated by JS -->
                                            </div>
                                        </div>

                                        <!-- Time Slots Column -->
                                        <div class="col-md-7">
                                            <div class="card h-100">
                                                <div class="card-header">
                                                    <h6 class="mb-0" id="selectedDateDisplay">Select a date</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="time-slots-grid" id="timeSlotsGrid">
                                                        <div class="text-center text-muted mt-4">
                                                            <i class="fas fa-clock fa-2x mb-2"></i>
                                                            <p>Select a date to view available time slots</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Hidden Inputs for Date/Time -->
                                    <input type="hidden" name="appointment_date" id="appointment_date" required>
                                    <input type="hidden" name="appointment_time" id="appointment_time" required>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional notes or comments"></textarea>
                                </div>
                            </div>
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

            // Barangay change handler for purok and other fields
            document.querySelectorAll('select[data-role="barangay"]').forEach(select => {
                select.addEventListener('change', function() {
                    const form = this.closest('form');
                    const purokGroup = form.querySelector('[data-role="purok-group"]');
                    const otherGroup = form.querySelector('[data-role="barangay-other-group"]');
                    const purokSelect = form.querySelector('[data-role="purok"]');
                    const value = this.value;

                    if (value === 'Barangay 11' || value === 'Barangay 12') {
                        purokGroup.classList.remove('d-none');
                        otherGroup.classList.add('d-none');

                        // Populate purok options
                        purokSelect.innerHTML = '<option value="" disabled selected>Select Purok</option>';
                        let options = [];
                        if (value === 'Barangay 11') {
                            options = ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'];
                        } else if (value === 'Barangay 12') {
                            options = ['Purok 1', 'Purok 2', 'Purok 3'];
                        }
                        options.forEach(opt => {
                            const option = document.createElement('option');
                            option.value = opt;
                            option.text = opt;
                            purokSelect.add(option);
                        });

                        // Restore selected value if available
                        const selected = purokSelect.dataset.selected;
                        if (selected && options.includes(selected)) {
                            purokSelect.value = selected;
                        }
                    } else if (value === 'Other') {
                        purokGroup.classList.add('d-none');
                        otherGroup.classList.remove('d-none');
                    } else {
                        purokGroup.classList.add('d-none');
                        otherGroup.classList.add('d-none');
                    }
                });

                // Trigger initial state on load
                select.dispatchEvent(new Event('change'));
            });
            
        });
        // Calendar functionality for Create Appointment Modal
        class AppointmentCalendar {
            constructor() {
                this.currentDate = new Date();
                this.selectedDate = null;
                this.calendarData = [];
                this.init();
            }

            init() {
                this.attachEventListeners();
                this.loadCalendar();
            }

            attachEventListeners() {
                document.getElementById('prevMonth').addEventListener('click', () => {
                    this.currentDate.setMonth(this.currentDate.getMonth() - 1);
                    this.loadCalendar();
                });

                document.getElementById('nextMonth').addEventListener('click', () => {
                    this.currentDate.setMonth(this.currentDate.getMonth() + 1);
                    this.loadCalendar();
                });
            }

            async loadCalendar() {
                const year = this.currentDate.getFullYear();
                const month = this.currentDate.getMonth() + 1;
                
                try {
                    const response = await fetch(`/admin/appointments/calendar?year=${year}&month=${month}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    if (!response.ok) throw new Error('Failed to load calendar data');
                    
                    const data = await response.json();
                    this.calendarData = data.calendar;
                    this.renderCalendar();
                    this.updateMonthDisplay();
                } catch (error) {
                    console.error('Error loading calendar:', error);
                    document.getElementById('calendarGrid').innerHTML = `<div class="col-12 text-center text-danger">Error loading calendar</div>`;
                }
            }

            renderCalendar() {
                const calendarGrid = document.getElementById('calendarGrid');
                calendarGrid.innerHTML = '';

                // Add day headers
                const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                dayHeaders.forEach(day => {
                    const header = document.createElement('div');
                    header.className = 'calendar-header';
                    header.textContent = day;
                    calendarGrid.appendChild(header);
                });

                // Add empty cells
                const firstDay = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1).getDay();
                for (let i = 0; i < firstDay; i++) {
                    calendarGrid.appendChild(document.createElement('div'));
                }

                // Add calendar days
                this.calendarData.forEach(dayData => {
                    const dayElement = document.createElement('div');
                    dayElement.className = 'calendar-day';
                    
                    const dayNumber = document.createElement('span');
                    dayNumber.className = 'day-number';
                    dayNumber.textContent = dayData.day;
                    dayElement.appendChild(dayNumber);
                    
                    if (dayData.is_weekend) dayElement.classList.add('weekend');
                    
                    if (dayData.is_past) {
                        dayElement.classList.add('past');
                    } else if (dayData.is_fully_occupied) {
                        dayElement.classList.add('occupied');
                    } else if (dayData.occupied_slots > 0) {
                        dayElement.classList.add('partially-occupied');
                    }

                    const indicator = document.createElement('span');
                    indicator.className = 'slot-indicator';
                    indicator.textContent = `${dayData.occupied_slots}/${dayData.total_slots}`;
                    dayElement.appendChild(indicator);

                    if (!dayData.is_past) {
                        dayElement.addEventListener('click', () => this.selectDate(dayData.date));
                    }

                    calendarGrid.appendChild(dayElement);
                });
            }

            updateMonthDisplay() {
                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'];
                document.getElementById('currentMonth').textContent = 
                    `${monthNames[this.currentDate.getMonth()]} ${this.currentDate.getFullYear()}`;
            }

            async selectDate(date) {
                // Remove previous selection
                document.querySelectorAll('.calendar-day.selected').forEach(el => el.classList.remove('selected'));

                // Add selection to clicked date
                document.querySelectorAll('.calendar-day').forEach(el => {
                    // Note: This is a simple check, might need more robust matching if multiple months displayed (not the case here)
                    // But since we redraw calendar on month change, checking text content or index is tricky. 
                    // Ideally we'd store date in dataset.
                    // Let's rely on re-rendering or just adding a data attribute in renderCalendar (which I didn't do above, let me fix that in renderCalendar logic implicitly or just add it now)
                });
                // Wait, I didn't add data-date in renderCalendar above. Let me fix that in the previous method or just handle it here.
                // Actually, I can't easily find the element without the data attribute. 
                // I will update renderCalendar to add data-date.
                
                this.selectedDate = date;
                
                // Update display
                const selectedDateObj = new Date(date);
                document.getElementById('selectedDateDisplay').textContent = selectedDateObj.toLocaleDateString('en-US', {
                    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
                });

                await this.loadTimeSlots(date);
            }

            async loadTimeSlots(date) {
                try {
                    const response = await fetch(`/admin/appointments/slots?date=${date}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (!response.ok) throw new Error('Failed to load slots');
                    const data = await response.json();
                    this.renderTimeSlots(data.slots);
                } catch (error) {
                    console.error('Error loading slots:', error);
                    document.getElementById('timeSlotsGrid').innerHTML = `<div class="text-danger">Error loading slots</div>`;
                }
            }

            renderTimeSlots(slots) {
                const timeSlotsGrid = document.getElementById('timeSlotsGrid');
                timeSlotsGrid.innerHTML = '';

                if (!slots || slots.length === 0) {
                    timeSlotsGrid.innerHTML = '<div class="text-center text-muted">No time slots available</div>';
                    return;
                }

                slots.forEach(slot => {
                    const slotElement = document.createElement('div');
                    slotElement.className = `time-slot ${slot.available ? 'available' : 'occupied'}`;
                    
                    if (slot.available) {
                        slotElement.addEventListener('click', () => {
                            document.querySelectorAll('.time-slot.selected').forEach(el => el.classList.remove('selected'));
                            slotElement.classList.add('selected');
                            document.getElementById('appointment_time').value = slot.time;
                            document.getElementById('appointment_date').value = this.selectedDate;
                        });
                    }
                    
                    slotElement.innerHTML = `
                        <div class="time">${slot.display}</div>
                        <div class="status">${slot.available ? 'Available' : 'Occupied'}</div>
                    `;
                    
                    timeSlotsGrid.appendChild(slotElement);
                });
            }
        }

        // Initialize modal and calendar
        const createAppointmentModal = document.getElementById('createAppointmentModal');
        let appointmentCalendar = null;

        createAppointmentModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');
            const userPhone = button.getAttribute('data-user-phone');
            const userAddress = button.getAttribute('data-user-address');

            // Populate patient details
            document.getElementById('appointment_user_id').value = userId;
            document.getElementById('appointment_patient_name').value = userName;
            document.getElementById('appointment_patient_phone').value = userPhone;
            document.getElementById('appointment_patient_address').value = userAddress;

            // Initialize calendar if not already done
            if (!appointmentCalendar) {
                appointmentCalendar = new AppointmentCalendar();
                // Monkey-patch renderCalendar to add data-date (since I missed it in the class definition above)
                const originalRender = appointmentCalendar.renderCalendar.bind(appointmentCalendar);
                appointmentCalendar.renderCalendar = function() {
                    const calendarGrid = document.getElementById('calendarGrid');
                    calendarGrid.innerHTML = '';
                    
                    // Headers
                    ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].forEach(day => {
                        const header = document.createElement('div');
                        header.className = 'calendar-header';
                        header.textContent = day;
                        calendarGrid.appendChild(header);
                    });

                    // Empty cells
                    const firstDay = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1).getDay();
                    for (let i = 0; i < firstDay; i++) calendarGrid.appendChild(document.createElement('div'));

                    // Days
                    this.calendarData.forEach(dayData => {
                        const dayElement = document.createElement('div');
                        dayElement.className = 'calendar-day';
                        dayElement.dataset.date = dayData.date; // Added this
                        
                        const dayNumber = document.createElement('span');
                        dayNumber.className = 'day-number';
                        dayNumber.textContent = dayData.day;
                        dayElement.appendChild(dayNumber);
                        
                        if (dayData.is_weekend) dayElement.classList.add('weekend');
                        if (dayData.is_past) dayElement.classList.add('past');
                        else if (dayData.is_fully_occupied) dayElement.classList.add('occupied');
                        else if (dayData.occupied_slots > 0) dayElement.classList.add('partially-occupied');

                        const indicator = document.createElement('span');
                        indicator.className = 'slot-indicator';
                        indicator.textContent = `${dayData.occupied_slots}/${dayData.total_slots}`;
                        dayElement.appendChild(indicator);

                        if (!dayData.is_past) {
                            dayElement.addEventListener('click', () => {
                                document.querySelectorAll('.calendar-day.selected').forEach(el => el.classList.remove('selected'));
                                dayElement.classList.add('selected');
                                this.selectDate(dayData.date);
                            });
                        }
                        calendarGrid.appendChild(dayElement);
                    });
                };
                appointmentCalendar.renderCalendar(); // Initial render
            }
        });

        createAppointmentModal.addEventListener('hidden.bs.modal', function () {
            // Reset form and calendar selection
            document.getElementById('createAppointmentForm').reset();
            document.getElementById('selectedDateDisplay').textContent = 'Select a date';
            document.getElementById('timeSlotsGrid').innerHTML = '<div class="text-center text-muted"><i class="fas fa-clock fa-2x mb-2"></i><p>Select a date to view time slots</p></div>';
            if (appointmentCalendar) {
                appointmentCalendar.selectedDate = null;
                document.querySelectorAll('.calendar-day.selected').forEach(el => el.classList.remove('selected'));
            }
        });
    </script>
@endpush