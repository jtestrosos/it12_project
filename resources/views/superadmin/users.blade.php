@extends('superadmin.layout')

@section('title', 'User Management - Barangay Health Center')
@section('page-title', 'User Management')
@section('page-description', 'View and manage all registered users')

@section('page-styles')
    <style>
        /* User cards */
        .patient-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            border: none;
            transition: transform 0.2s ease;
        }

        .patient-card:hover {
            transform: translateY(-2px);
        }

        body.bg-dark .patient-card {
            background: #1e2124;
            color: #e6e6e6;
        }

        body.bg-dark .patient-card:hover {
            background: #2a2f35;
        }

        /* Status badges */
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #000 !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            min-width: 90px;
            line-height: 1;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-user {
            background-color: #cce5ff;
            color: #000;
        }

        .status-admin {
            background-color: #fff3cd;
            color: #000;
        }

        .status-superadmin {
            background-color: #f8d7da;
            color: #000;
        }

        /* Patient avatar */
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

        /* Modal */
        .modal-content {
            border-radius: 12px;
            border: none;
        }

        /* Form controls */
        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Role cell alignment */
        .role-cell {
            text-align: center;
            vertical-align: middle !important;
        }

        /* Action buttons */
        .action-btn {
            min-width: 110px;
        }

        /* Table container - responds to dark mode */
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        body.bg-dark .table-container {
            background: #1e2124;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        /* Table styles - clean and theme-aware */
        .users-table {
            margin-bottom: 0;
        }

        .users-table thead th {
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            padding: 1rem;
            background: transparent;
        }

        .users-table.table-dark thead th,
        body.bg-dark .users-table thead th {
            border-bottom-color: #2a2f35;
            color: #e6e6e6;
        }

        .users-table tbody tr {
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.2s ease;
        }

        .users-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .users-table.table-dark tbody tr,
        body.bg-dark .users-table tbody tr {
            border-bottom-color: #2a2f35;
        }

        .users-table.table-dark tbody tr:hover,
        body.bg-dark .users-table tbody tr:hover {
            background-color: #2a2f35;
        }

        .users-table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .users-table.table-dark tbody td,
        body.bg-dark .users-table tbody td {
            color: #e6e6e6;
        }

        /* Pagination */
        .pagination-container {
            border-top: 1px solid #e9ecef;
            padding: 1rem;
        }

        body.bg-dark .pagination-container {
            border-top-color: #2a2f35;
        }

        /* Action buttons */
        .users-table .btn-outline-secondary {
            border-color: #dee2e6;
            color: #6c757d;
        }

        .users-table .btn-outline-secondary:hover {
            background-color: #f8f9fa;
            border-color: #adb5bd;
        }

        body.bg-dark .users-table .btn-outline-secondary {
            border-color: #2a2f35;
            color: #adb5bd;
        }

        body.bg-dark .users-table .btn-outline-secondary:hover {
            background-color: #2a2f35;
            border-color: #495057;
            color: #e6e6e6;
        }
        
        /* Dark mode for search input group */
        body.bg-dark .input-group-text {
            background-color: #1e2124;
            border-color: #2a2f35;
            color: #e6e6e6;
        }
        
        body.bg-dark .form-control,
        body.bg-dark .form-select {
            background-color: #1e2124;
            border-color: #2a2f35;
            color: #e6e6e6;
        }
        
        body.bg-dark .form-control:focus,
        body.bg-dark .form-select:focus {
            background-color: #2a2f35;
            border-color: #007bff;
            color: #e6e6e6;
        }
        
        body.bg-dark .form-control::placeholder {
            color: #6c757d;
        }
        
        /* Hide Bootstrap pagination's built-in "Showing" text on the left */
        nav[role="navigation"] p,
        nav[role="navigation"] .text-sm,
        nav p,
        .pagination-wrapper p,
        #usersPaginationContainer nav p,
        #usersPaginationContainer p:first-child,
        #usersPaginationContainer > p {
            display: none !important;
        }
        
        /* Bring showing text closer to pagination */
        #usersPaginationContainer > div:last-child {
            margin-top: -0.5rem !important;
        }
    </style>
@endsection

@push('styles')
    <style>
        /* Sidebar dark mode override - loaded AFTER layout styles */
        body.bg-dark .sidebar {
            background: #131516 !important;
            border-right-color: #2a2f35 !important;
        }
    </style>
@endpush

@section('content')
    <!-- Add User + Archive Buttons -->
    <div class="d-flex justify-content-end align-items-center mb-4 gap-2">
        <a href="{{ route('superadmin.users.archive') }}" class="btn btn-outline-secondary d-flex align-items-center">
            <i class="fas fa-archive me-2"></i>
            <span>Archive History</span>
        </a>
        <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-plus me-2"></i>
            <span>Add Admin</span>
        </button>
    </div>

    <!-- Search and Filter (client-side) -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" class="form-control" id="searchInput" placeholder="Search users by name or email..."
                    value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select class="form-select" id="roleFilter">
                <option value="">All Roles</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center"
                id="clearFiltersBtn">
                <i class="fas fa-times me-2"></i>
                <span>Clear</span>
            </button>
        </div>
    </div>

    <!-- Users Table -->
    <div class="table-container">
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table users-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">User</th>
                            <th>Email</th>
                            <th class="text-center">Role</th>
                            <th>Registered</th>
                            <th class="text-center pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        @foreach($users as $user)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3 text-white"
                                            style="width: 40px; height: 40px; font-size: 0.9rem; font-weight: 600;">
                                            {{ substr($user->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span>{{ $user->email }}</span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge rounded-pill 
                                                                                                                                @if($user->role == 'superadmin') bg-danger
                                                                                                                                @elseif($user->role == 'admin') bg-warning
                                                                                                                                @else bg-primary
                                                                                                                                @endif"
                                        style="font-size: 0.75rem; padding: 0.5rem 1rem;">
                                        {{ ucfirst($user->role === 'user' ? 'patient' : $user->role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="small">{{ $user->created_at->format('M d, Y') }}</span>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="btn-group" role="group">
                                        <button
                                            class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center"
                                            data-bs-toggle="modal" data-bs-target="#viewUserModal{{ $user->id }}" title="View User">
                                            <i class="fas fa-eye text-info"></i>
                                        </button>
                                        <button
                                            class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center"
                                            data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}" title="Edit User">
                                            <i class="fas fa-edit text-warning"></i>
                                        </button>
                                        @if($user->id !== Auth::id())
                                            <button
                                                class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center"
                                                data-bs-toggle="modal" data-bs-target="#archiveUserModal{{ $user->id }}"
                                                title="Archive User">
                                                <i class="fas fa-archive text-danger"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No users found</h5>
                <p class="text-muted">There are no registered users at the moment.</p>
            </div>
        @endif
    </div>

    @if($users->count() > 0)
        <!-- Pagination -->
        <div class="d-flex flex-column align-items-center mt-4"
            id="usersPaginationContainer">
            <div>
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
            <div class="small text-muted mb-0 mt-n2">
                @if($users->total() > 0)
                    Showing {{ $users->firstItem() }}-{{ $users->lastItem() }} of {{ $users->total() }} users
                @else
                    Showing 0 users
                @endif
            </div>
        </div>
    @endif

    <!-- Confirm Action Modal -->
    <div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmActionTitle">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="confirmActionMessage">
                    Are you sure?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmActionBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>
@endsection

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            @php
                $selectedBarangayModal = old('barangay');
                $purokOptionsModal = match ($selectedBarangayModal) {
                    'Barangay 11' => ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'],
                    'Barangay 12' => ['Purok 1', 'Purok 2', 'Purok 3'],
                    default => [],
                };
            @endphp
            <form method="POST" action="{{ route('superadmin.user.create') }}" class="superadmin-user-form">
                @csrf
                <input type="hidden" name="role" value="admin">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required>
                        <div class="invalid-feedback">There should be no number in name</div>
                        @error('name')
                            @if (str_contains($message, 'should not contain numbers'))
                                <div class="invalid-feedback">{{ $message }}</div>
                            @elseif ($message)
                                <div class="invalid-feedback">{{ $message }}</div>
                            @endif
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gender <span class="text-danger">*</span></label>
                        <select name="gender" class="form-control @error('gender') is-invalid @enderror" required>
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" required>
                        <div class="invalid-feedback">Please enter a valid email address.</div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone') }}" placeholder="Enter phone number" required>
                        <div class="invalid-feedback">Format for the number is 09123456789</div>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" required
                                data-role="register-password-input">
                            <button class="btn btn-outline-secondary @error('password') d-none @enderror" type="button"
                                data-role="register-password-toggle-btn">
                                <i class="fa-solid fa-eye" data-role="register-password-toggle-icon"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">Needs 1 Large Character with number/s and special character
                            consisting 8 characters</div>
                        @error('password')
                            @if (str_contains($message, 'lowercase letter') || str_contains($message, 'uppercase letter') || str_contains($message, 'special character'))
                                <div class="invalid-feedback">Password must be at least 8 characters with uppercase, lowercase,
                                    and special character</div>
                            @elseif ($message)
                                <div class="invalid-feedback">{{ $message }}</div>
                            @endif
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation"
                            class="form-control @error('password_confirmation') is-invalid @enderror" required>
                        <div class="invalid-feedback">The password confirmation does not match.</div>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modals -->
@foreach($users as $user)
    <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User - {{ $user->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST"
                    action="{{ route('superadmin.user.update', ['type' => $user->role, 'id' => $user->id]) }}"
                    class="superadmin-user-form">
                    @csrf
                    @php
                        $editRole = old('role', $user->role);
                        $editBarangay = old('barangay', $user->barangay);
                        $editPurokOptions = match ($editBarangay) {
                            'Barangay 11' => ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'],
                            'Barangay 12' => ['Purok 1', 'Purok 2', 'Purok 3'],
                            default => [],
                        };
                        $editBirthDateRaw = old(
                            'birth_date',
                            $user->birth_date ? \Illuminate\Support\Carbon::parse($user->birth_date)->format('Y-m-d') : ''
                        );
                    @endphp
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name{{ $user->id }}" class="form-label">Full Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name{{ $user->id }}" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="gender{{ $user->id }}" class="form-label">Gender *</label>
                            <select class="form-control @error('gender') is-invalid @enderror" id="gender{{ $user->id }}"
                                name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male
                                </option>
                                <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female
                                </option>
                                <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other
                                </option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email{{ $user->id }}" class="form-label">Email *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email{{ $user->id }}" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="role{{ $user->id }}" class="form-label">Role *</label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role{{ $user->id }}"
                                name="role" data-role="user-role" required>
                                <option value="user" {{ $editRole == 'user' ? 'selected' : '' }}>Patient</option>
                                <option value="admin" {{ $editRole == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="superadmin" {{ $editRole == 'superadmin' ? 'selected' : '' }}>Super Admin
                                </option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password{{ $user->id }}" class="form-label">Password (leave blank to keep
                                current)</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password{{ $user->id }}" name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation{{ $user->id }}" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                id="password_confirmation{{ $user->id }}" name="password_confirmation">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="{{ $editRole === 'user' ? '' : 'd-none' }}" data-role="patient-fields">
                            <div class="mb-3">
                                <label for="patient_phone_edit{{ $user->id }}" class="form-label">Phone Number <span
                                        class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                    id="patient_phone_edit{{ $user->id }}" name="phone"
                                    value="{{ old('phone', $user->phone ?? '') }}" data-role="patient-phone">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="patient_address_edit{{ $user->id }}" class="form-label">Address <small
                                        class="text-muted">(Optional)</small></label>
                                <textarea class="form-control @error('address') is-invalid @enderror"
                                    id="patient_address_edit{{ $user->id }}" name="address" rows="2"
                                    placeholder="Enter complete address (optional)">{{ old('address', $user->address ?? '') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="patient_barangay_edit{{ $user->id }}" class="form-label">Barangay <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('barangay') is-invalid @enderror"
                                    id="patient_barangay_edit{{ $user->id }}" name="barangay" data-role="barangay">
                                    <option value="">Select Barangay</option>
                                    <option value="Barangay 11" {{ $editBarangay === 'Barangay 11' ? 'selected' : '' }}>
                                        Barangay 11</option>
                                    <option value="Barangay 12" {{ $editBarangay === 'Barangay 12' ? 'selected' : '' }}>
                                        Barangay 12</option>
                                    <option value="Other" {{ $editBarangay === 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('barangay')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 {{ $editBarangay === 'Other' ? '' : 'd-none' }}"
                                data-role="barangay-other-group">
                                <label for="patient_barangay_other_edit{{ $user->id }}" class="form-label">Specify Barangay
                                    <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('barangay_other') is-invalid @enderror"
                                    id="patient_barangay_other_edit{{ $user->id }}" name="barangay_other"
                                    value="{{ old('barangay_other', $user->barangay_other ?? '') }}"
                                    data-role="barangay-other">
                                @error('barangay_other')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 {{ in_array($editBarangay, ['Barangay 11', 'Barangay 12']) ? '' : 'd-none' }}"
                                data-role="purok-group">
                                <label for="patient_purok_edit{{ $user->id }}" class="form-label">Purok <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('purok') is-invalid @enderror"
                                    id="patient_purok_edit{{ $user->id }}" name="purok" data-role="purok"
                                    data-selected="{{ old('purok', $user->purok ?? '') }}">
                                    <option value="">Select Purok</option>
                                    @foreach ($editPurokOptions as $purok)
                                        <option value="{{ $purok }}" {{ old('purok', $user->purok ?? '') === $purok ? 'selected' : '' }}>{{ $purok }}</option>
                                    @endforeach
                                </select>
                                @error('purok')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="patient_birth_date_edit{{ $user->id }}" class="form-label">Birth Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                                    id="patient_birth_date_edit{{ $user->id }}" name="birth_date"
                                    value="{{ $editBirthDateRaw }}" data-role="birth-date"
                                    max="{{ now()->toDateString() }}">
                                @error('birth_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- View User Modals -->
@foreach($users as $user)
    <div class="modal fade" id="viewUserModal{{ $user->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User Details - {{ $user->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ $user->name }}</p>
                            <p><strong>Email:</strong> {{ $user->email }}</p>
                            <p><strong>Role:</strong>
                                <span class="status-badge 
                                                            @if($user->role == 'superadmin') status-superadmin
                                                            @elseif($user->role == 'admin') status-admin
                                                            @else status-user
                                                            @endif">
                                    {{ ucfirst($user->role === 'user' ? 'patient' : $user->role) }}
                                </span>
                            </p>
                            @if($user->gender)
                                <p><strong>Gender:</strong> {{ ucfirst($user->gender) }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            @if($user->phone)
                                <p><strong>Phone:</strong> {{ $user->phone }}</p>
                            @endif
                            @if($user->barangay)
                                <p><strong>Barangay:</strong>
                                    @if($user->barangay === 'Other')
                                        {{ $user->barangay_other ?? 'Other' }}
                                    @else
                                        {{ $user->barangay }}
                                    @endif
                                </p>
                            @endif
                            @if($user->purok)
                                <p><strong>Purok:</strong> {{ $user->purok }}</p>
                            @endif
                            @if($user->birth_date)
                                <p><strong>Birth Date:</strong>
                                    {{ \Illuminate\Support\Carbon::parse($user->birth_date)->format('M d, Y') }}</p>
                            @endif
                        </div>
                    </div>
                    @if($user->address)
                        <div class="row mt-3">
                            <div class="col-12">
                                <p><strong>Address:</strong> {{ $user->address }}</p>
                            </div>
                        </div>
                    @endif
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <p><strong>Registered:</strong> {{ $user->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#editUserModal{{ $user->id }}" data-bs-dismiss="modal">
                        <i class="fas fa-edit me-1"></i> Edit User
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- Archive User Modals -->
@foreach($users as $user)
    @if($user->id !== Auth::id())
        <div class="modal fade" id="archiveUserModal{{ $user->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Archive User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to archive <strong>{{ $user->name }}</strong>?</p>
                        <p class="text-muted">This action will remove the user from the active list but preserve their data.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form method="POST"
                            action="{{ route('superadmin.user.delete', ['type' => $user->role, 'id' => $user->id]) }}"
                            class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-archive me-1"></i> Archive User
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const roleFilter = document.getElementById('roleFilter');
            const clearBtn = document.getElementById('clearFiltersBtn');
            const tableContainer = document.querySelector('.table-container');

            function fetchUsers(url) {
                tableContainer.style.opacity = '0.5';

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newTableContainer = doc.querySelector('.table-container');

                        if (newTableContainer && tableContainer) {
                            tableContainer.innerHTML = newTableContainer.innerHTML;
                        }

                        window.history.pushState({}, '', url);
                        tableContainer.style.opacity = '1';
                    })
                    .catch(error => {
                        console.error('Error fetching users:', error);
                        tableContainer.style.opacity = '1';
                    });
            }

            function updateFilters() {
                const search = searchInput.value;
                const role = roleFilter.value;
                const url = new URL(window.location.href);

                if (search) url.searchParams.set('search', search);
                else url.searchParams.delete('search');

                if (role) url.searchParams.set('role', role);
                else url.searchParams.delete('role');

                url.searchParams.set('page', 1);

                fetchUsers(url.toString());
            }

            let timeout = null;
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    clearTimeout(timeout);
                    timeout = setTimeout(updateFilters, 500);
                });
            }

            if (roleFilter) {
                roleFilter.addEventListener('change', updateFilters);
            }

            if (clearBtn) {
                clearBtn.addEventListener('click', function () {
                    searchInput.value = '';
                    roleFilter.value = '';
                    updateFilters();
                });
            }

            // Handle pagination clicks
            if (tableContainer) {
                tableContainer.addEventListener('click', function (e) {
                    const link = e.target.closest('.pagination .page-link');
                    if (link && !link.parentElement.classList.contains('disabled') && !link.parentElement.classList.contains('active')) {
                        e.preventDefault();
                        fetchUsers(link.href);
                    }
                });
            }
        });

        // Generic confirmation modal for destructive actions
        (function () {
            const modalEl = document.getElementById('confirmActionModal');
            if (!modalEl || typeof bootstrap === 'undefined') {
                return;
            }

            const modal = new bootstrap.Modal(modalEl);
            const titleEl = document.getElementById('confirmActionTitle');
            const messageEl = document.getElementById('confirmActionMessage');
            const confirmBtn = document.getElementById('confirmActionBtn');

            let pendingForm = null;

            document.querySelectorAll('[data-confirm]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const form = btn.closest('form');
                    if (!form) return;
                    pendingForm = form;

                    if (titleEl) {
                        titleEl.textContent = btn.getAttribute('data-confirm-title') || 'Confirm Action';
                    }
                    if (messageEl) {
                        messageEl.textContent = btn.getAttribute('data-confirm-message') || 'Are you sure you want to proceed?';
                    }

                    modal.show();
                });
            });

            if (confirmBtn) {
                confirmBtn.addEventListener('click', () => {
                    if (pendingForm) {
                        pendingForm.submit();
                        pendingForm = null;
                    }
                    modal.hide();
                });
            }
        })();

        const barangayPurokMap = {
            'Barangay 11': ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'],
            'Barangay 12': ['Purok 1', 'Purok 2', 'Purok 3'],
        };

        function initSuperadminUserForms() {
            const forms = document.querySelectorAll('.superadmin-user-form');

            forms.forEach((form) => {
                const roleSelect = form.querySelector('[data-role="user-role"]');
                const barangaySelect = form.querySelector('[data-role="barangay"]');
                const barangayOtherGroup = form.querySelector('[data-role="barangay-other-group"]');
                const barangayOtherInput = form.querySelector('[data-role="barangay-other"]');
                const purokGroup = form.querySelector('[data-role="purok-group"]');
                const purokSelect = form.querySelector('[data-role="purok"]');
                const birthDateInput = form.querySelector('[data-role="birth-date"]');
                const nameInput = form.querySelector('input[name="name"]');
                const phoneInput = form.querySelector('input[name="phone"]');
                const passwordInput = form.querySelector('input[name="password"]');
                const passwordConfirmInput = form.querySelector('input[name="password_confirmation"]');
                const registerPasswordToggleBtn = form.querySelector('[data-role="register-password-toggle-btn"]');
                const registerPasswordToggleIcon = form.querySelector('[data-role="register-password-toggle-icon"]');

                const barangayPurokMap = {
                    'Barangay 11': ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'],
                    'Barangay 12': ['Purok 1', 'Purok 2', 'Purok 3'],
                };

                const updatePurokOptions = (barangay) => {
                    if (!purokSelect) {
                        return;
                    }

                    const previouslySelected = purokSelect.getAttribute('data-selected');
                    purokSelect.innerHTML = '<option value="">Select Purok</option>';

                    if (!barangayPurokMap[barangay]) {
                        purokSelect.removeAttribute('required');
                        purokSelect.setAttribute('data-selected', '');
                        return;
                    }

                    barangayPurokMap[barangay].forEach((purok) => {
                        const option = document.createElement('option');
                        option.value = purok;
                        option.textContent = purok;
                        if (previouslySelected === purok) {
                            option.selected = true;
                        }
                        purokSelect.appendChild(option);
                    });
                    purokSelect.setAttribute('required', 'required');
                };

                const handleBarangayChange = () => {
                    const selectedBarangay = barangaySelect ? barangaySelect.value : '';

                    if (barangayOtherGroup && barangayOtherInput) {
                        if (selectedBarangay === 'Other') {
                            barangayOtherGroup.classList.remove('d-none');
                            barangayOtherInput.setAttribute('required', 'required');
                        } else {
                            barangayOtherGroup.classList.add('d-none');
                            barangayOtherInput.removeAttribute('required');
                            barangayOtherInput.value = '';
                        }
                    }

                    if (purokGroup) {
                        if (selectedBarangay === 'Barangay 11' || selectedBarangay === 'Barangay 12') {
                            purokGroup.classList.remove('d-none');
                            updatePurokOptions(selectedBarangay);
                        } else {
                            purokGroup.classList.add('d-none');
                            purokSelect.removeAttribute('required');
                            purokSelect.innerHTML = '<option value="">Select Purok</option>';
                        }
                    }
                };

                // Initial state
                handleBarangayChange();
                updatePurokOptions();

                // Event Listeners
                if (roleSelect) {
                    roleSelect.addEventListener('change', () => {
                        handleBarangayChange();
                        updatePurokOptions();
                    });
                }
                if (barangaySelect) {
                    barangaySelect.addEventListener('change', handleBarangayChange);
                }

                // Handle form submission - let Laravel handle validation
                form.addEventListener('submit', function (event) {
                    // Allow form to submit normally to get Laravel validation errors
                    // No need to prevent default or use Bootstrap validation
                    form.classList.add('was-validated');
                }, false);

                // Real-time validation for name field
                if (nameInput) {
                    nameInput.addEventListener('input', function () {
                        const value = this.value.trim();
                        const feedbackDiv = this.parentNode.querySelector('.invalid-feedback');

                        if (value && /\d/.test(value)) {
                            this.classList.add('is-invalid');
                            this.classList.remove('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.textContent = 'There should be no number in name';
                                feedbackDiv.style.display = 'block';
                            }
                        } else if (value.length > 0) {
                            this.classList.remove('is-invalid');
                            this.classList.add('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.style.display = 'none';
                            }
                        } else {
                            this.classList.remove('is-invalid', 'is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.style.display = 'none';
                            }
                        }
                    });
                }

                // Real-time validation for phone field
                if (phoneInput) {
                    phoneInput.addEventListener('input', function () {
                        const value = this.value.trim();
                        const feedbackDiv = this.parentNode.querySelector('.invalid-feedback');

                        if (value && !/^09\d{9}$/.test(value)) {
                            this.classList.add('is-invalid');
                            this.classList.remove('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.textContent = 'Format for the number is 09123456789';
                                feedbackDiv.style.display = 'block';
                            }
                        } else if (value.length > 0) {
                            this.classList.remove('is-invalid');
                            this.classList.add('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.style.display = 'none';
                            }
                        } else {
                            this.classList.remove('is-invalid', 'is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.style.display = 'none';
                            }
                        }
                    });
                }

                // Real-time validation for email field
                const emailInput = form.querySelector('input[name="email"]');
                if (emailInput) {
                    emailInput.addEventListener('input', function () {
                        const value = this.value.trim();
                        const feedbackDiv = this.parentNode.querySelector('.invalid-feedback');

                        if (value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                            this.classList.add('is-invalid');
                            this.classList.remove('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.textContent = 'Please enter a valid email address.';
                                feedbackDiv.style.display = 'block';
                            }
                        } else if (value.length > 0) {
                            this.classList.remove('is-invalid');
                            this.classList.add('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.style.display = 'none';
                            }
                        } else {
                            this.classList.remove('is-invalid', 'is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.style.display = 'none';
                            }
                        }
                    });
                }

                // Real-time validation for password field
                if (passwordInput) {
                    passwordInput.addEventListener('input', function () {
                        const value = this.value;
                        const feedbackDiv = this.parentNode.parentNode.querySelector('.invalid-feedback');

                        if (value.length > 0 && value.length < 8) {
                            this.classList.add('is-invalid');
                            this.classList.remove('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.textContent = 'Needs 1 Large Character with number/s and special character consisting 8 characters';
                                feedbackDiv.style.display = 'block';
                            }
                        } else if (value.length > 0 && !/^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]).+$/.test(value)) {
                            this.classList.add('is-invalid');
                            this.classList.remove('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.textContent = 'Needs 1 Large Character with number/s and special character consisting 8 characters';
                                feedbackDiv.style.display = 'block';
                            }
                        } else if (value.length >= 8 && /^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]).+$/.test(value)) {
                            this.classList.remove('is-invalid');
                            this.classList.add('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.style.display = 'none';
                            }
                        } else {
                            this.classList.remove('is-invalid', 'is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.style.display = 'none';
                            }
                        }
                    });
                }

                // Real-time validation for birth date field
                if (birthDateInput) {
                    birthDateInput.addEventListener('input', function () {
                        const value = this.value;
                        const feedbackDiv = this.parentNode.querySelector('.invalid-feedback');
                        const today = new Date().toISOString().split('T')[0];

                        if (value && value >= today) {
                            this.classList.add('is-invalid');
                            this.classList.remove('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.textContent = 'Birth date must be in the past.';
                                feedbackDiv.style.display = 'block';
                            }
                        } else if (value && value < today) {
                            this.classList.remove('is-invalid');
                            this.classList.add('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.style.display = 'none';
                            }
                        } else {
                            this.classList.remove('is-invalid', 'is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.style.display = 'none';
                            }
                        }
                    });
                }

                // Real-time validation for password confirmation
                if (passwordConfirmInput) {
                    passwordConfirmInput.addEventListener('input', function () {
                        const value = this.value;
                        const passwordValue = passwordInput ? passwordInput.value : '';
                        const feedbackDiv = this.parentNode.querySelector('.invalid-feedback');

                        if (value && value !== passwordValue) {
                            this.classList.add('is-invalid');
                            this.classList.remove('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.textContent = 'The password confirmation does not match.';
                                feedbackDiv.style.display = 'block';
                            }
                        } else if (value && value === passwordValue) {
                            this.classList.remove('is-invalid');
                            this.classList.add('is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.style.display = 'none';
                            }
                        } else {
                            this.classList.remove('is-invalid', 'is-valid');
                            if (feedbackDiv) {
                                feedbackDiv.style.display = 'none';
                            }
                        }
                    });
                }

                // Password toggle functionality
                if (registerPasswordToggleBtn && passwordInput) {
                    registerPasswordToggleBtn.addEventListener('click', function () {
                        const showing = passwordInput.type === 'text';
                        const show = !showing;

                        passwordInput.type = show ? 'text' : 'password';
                        if (passwordConfirmInput) {
                            passwordConfirmInput.type = show ? 'text' : 'password';
                        }

                        if (registerPasswordToggleIcon) {
                            if (show) {
                                registerPasswordToggleIcon.classList.remove('fa-eye');
                                registerPasswordToggleIcon.classList.add('fa-eye-slash');
                            } else {
                                registerPasswordToggleIcon.classList.remove('fa-eye-slash');
                                registerPasswordToggleIcon.classList.add('fa-eye');
                            }
                        }
                    });
                }

                if (birthDateInput) {
                    birthDateInput.addEventListener('change', () => { });
                    birthDateInput.addEventListener('keyup', () => { });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initSuperadminUserForms();

            // Sync table with dark mode on page load
            const syncTableDark = () => {
                const isDark = document.body.classList.contains('bg-dark');
                const table = document.querySelector('.users-table');
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