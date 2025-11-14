@extends('superadmin.layout')

@section('title', 'User Management - Barangay Health Center')
@section('page-title', 'User Management')
@section('page-description', 'View and manage all registered users')

@section('page-styles')
<style>
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
            color: #000 !important;
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
        .modal-content {
            border-radius: 12px;
            border: none;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        .form-control:focus, .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        /* Center role badges nicely */
        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            min-width: 90px;
            line-height: 1;
        }
        /* Ensure role cell centers the pill perfectly without affecting row layout */
        .role-cell {
            text-align: center;
            vertical-align: middle !important;
        }
        .action-btn {
            min-width: 110px;
        }
    </style>
@endsection

@section('content')
                        <!-- Add User + Archive Buttons -->
                        <div class="d-flex justify-content-end align-items-center mb-4 gap-2">
                            <a href="{{ route('superadmin.users.archive') }}" class="btn btn-outline-secondary d-flex align-items-center">
                                <i class="fas fa-archive me-2"></i>
                                <span>Archive History</span>
                            </a>
                            <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="fas fa-plus me-2"></i>
                                <span>Add User</span>
                            </button>
                        </div>

                        <!-- Search and Filter (server-side) -->
                        <form method="GET" action="{{ route('superadmin.users') }}" class="row mb-4" id="userFilterForm">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Search users by name or email...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="role" id="roleFilter">
                                    <option value="">All Roles</option>
                                    <option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="patient" {{ request('role') == 'patient' ? 'selected' : '' }}>Patient</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-search me-2"></i>
                                    <span>Search</span>
                                </button>
                                <button type="button" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center" id="clearFiltersBtn">
                                    <i class="fas fa-times me-2"></i>
                                    <span>Clear</span>
                                </button>
                            </div>
                        </form>

                        <!-- Users Table -->
                        <div class="card">
                            <div class="card-body p-0">
                                @if($users->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>User</th>
                                                    <th>Email</th>
                                                    <th class="text-center">Role</th>
                                                    <th>Registered</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($users as $user)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="patient-avatar me-3" style="width: 40px; height: 40px; font-size: 0.9rem;">
                                                                {{ substr($user->name, 0, 2) }}
                                                            </div>
                                                            <div>
                                                                <div class="fw-bold">{{ $user->name }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $user->email }}</td>
                                                    <td class="role-cell">
                                                        <span class="status-badge 
                                                            @if($user->role == 'superadmin') status-superadmin
                                                            @elseif($user->role == 'admin') status-admin
                                                            @else status-user
                                                            @endif">
                                                            {{ ucfirst($user->role === 'user' ? 'patient' : $user->role) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button class="btn btn-outline-primary btn-sm d-flex align-items-center action-btn" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                                                <i class="fas fa-edit me-1"></i>
                                                                <span>Edit</span>
                                                            </button>
                                                            @if($user->id !== Auth::id())
                                                            <form method="POST" action="{{ route('superadmin.user.delete', $user) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to archive this user?')">
                                                                @csrf
                                                                <button type="submit" class="btn btn-outline-danger btn-sm d-flex align-items-center action-btn">
                                                                    <i class="fas fa-archive me-1"></i>
                                                                    <span>Archive</span>
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
                                    
                                    <!-- Pagination -->
                                    <div class="d-flex justify-content-between align-items-center p-3 border-top">
                                        <div class="text-muted">
                                            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                                        </div>
                                        <div>
                                            {{ $users->withQueryString()->links() }}
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No users found</h5>
                                        <p class="text-muted">There are no registered users at the moment.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
@endsection

<!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('superadmin.user.create') }}" class="superadmin-user-form">
                    @csrf
                    @php
                        $createRole = old('role');
                        $createBarangay = old('barangay');
                        $createPurokOptions = match ($createBarangay) {
                            'Barangay 11' => ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'],
                            'Barangay 12' => ['Purok 1', 'Purok 2', 'Purok 3'],
                            default => [],
                        };
                        $createBirthDate = old('birth_date');
                    @endphp
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender *</label>
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
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role *</label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" data-role="user-role" required>
                                <option value="">Select Role</option>
                                <option value="user" {{ $createRole == 'user' ? 'selected' : '' }}>Patient</option>
                                <option value="admin" {{ $createRole == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="superadmin" {{ $createRole == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password *</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" required>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="{{ $createRole === 'user' ? '' : 'd-none' }}" data-role="patient-fields">
                            <div class="mb-3">
                                <label for="patient_phone_create" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="patient_phone_create" name="phone" value="{{ old('phone') }}" data-role="patient-phone">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="patient_address_create" class="form-label">Address <small class="text-muted">(Optional)</small></label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="patient_address_create" name="address" rows="2" placeholder="Enter complete address (optional)">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="patient_barangay_create" class="form-label">Barangay <span class="text-danger">*</span></label>
                                <select class="form-select @error('barangay') is-invalid @enderror" id="patient_barangay_create" name="barangay" data-role="barangay">
                                    <option value="">Select Barangay</option>
                                    <option value="Barangay 11" {{ $createBarangay === 'Barangay 11' ? 'selected' : '' }}>Barangay 11</option>
                                    <option value="Barangay 12" {{ $createBarangay === 'Barangay 12' ? 'selected' : '' }}>Barangay 12</option>
                                    <option value="Other" {{ $createBarangay === 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('barangay')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 {{ $createBarangay === 'Other' ? '' : 'd-none' }}" data-role="barangay-other-group">
                                <label for="patient_barangay_other_create" class="form-label">Specify Barangay <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('barangay_other') is-invalid @enderror" id="patient_barangay_other_create" name="barangay_other" value="{{ old('barangay_other') }}" data-role="barangay-other">
                                @error('barangay_other')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 {{ in_array($createBarangay, ['Barangay 11', 'Barangay 12']) ? '' : 'd-none' }}" data-role="purok-group">
                                <label for="patient_purok_create" class="form-label">Purok <span class="text-danger">*</span></label>
                                <select class="form-select @error('purok') is-invalid @enderror" id="patient_purok_create" name="purok" data-role="purok" data-selected="{{ old('purok') }}">
                                    <option value="">Select Purok</option>
                                    @foreach ($createPurokOptions as $purok)
                                        <option value="{{ $purok }}" {{ old('purok') === $purok ? 'selected' : '' }}>{{ $purok }}</option>
                                    @endforeach
                                </select>
                                @error('purok')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="patient_birth_date_create" class="form-label">Birth Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="patient_birth_date_create" name="birth_date" value="{{ $createBirthDate }}" data-role="birth-date" max="{{ now()->toDateString() }}">
                                @error('birth_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add User</button>
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
                <form method="POST" action="{{ route('superadmin.user.update', $user) }}" class="superadmin-user-form">
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
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name{{ $user->id }}" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="gender{{ $user->id }}" class="form-label">Gender *</label>
                            <select class="form-control @error('gender') is-invalid @enderror" id="gender{{ $user->id }}" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email{{ $user->id }}" class="form-label">Email *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email{{ $user->id }}" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="role{{ $user->id }}" class="form-label">Role *</label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role{{ $user->id }}" name="role" data-role="user-role" required>
                                <option value="user" {{ $editRole == 'user' ? 'selected' : '' }}>Patient</option>
                                <option value="admin" {{ $editRole == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="superadmin" {{ $editRole == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password{{ $user->id }}" class="form-label">Password (leave blank to keep current)</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password{{ $user->id }}" name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation{{ $user->id }}" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation{{ $user->id }}" name="password_confirmation">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="{{ $editRole === 'user' ? '' : 'd-none' }}" data-role="patient-fields">
                            <div class="mb-3">
                                <label for="patient_phone_edit{{ $user->id }}" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="patient_phone_edit{{ $user->id }}" name="phone" value="{{ old('phone', $user->phone ?? '') }}" data-role="patient-phone">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="patient_address_edit{{ $user->id }}" class="form-label">Address <small class="text-muted">(Optional)</small></label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="patient_address_edit{{ $user->id }}" name="address" rows="2" placeholder="Enter complete address (optional)">{{ old('address', $user->address ?? '') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="patient_barangay_edit{{ $user->id }}" class="form-label">Barangay <span class="text-danger">*</span></label>
                                <select class="form-select @error('barangay') is-invalid @enderror" id="patient_barangay_edit{{ $user->id }}" name="barangay" data-role="barangay">
                                    <option value="">Select Barangay</option>
                                    <option value="Barangay 11" {{ $editBarangay === 'Barangay 11' ? 'selected' : '' }}>Barangay 11</option>
                                    <option value="Barangay 12" {{ $editBarangay === 'Barangay 12' ? 'selected' : '' }}>Barangay 12</option>
                                    <option value="Other" {{ $editBarangay === 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('barangay')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 {{ $editBarangay === 'Other' ? '' : 'd-none' }}" data-role="barangay-other-group">
                                <label for="patient_barangay_other_edit{{ $user->id }}" class="form-label">Specify Barangay <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('barangay_other') is-invalid @enderror" id="patient_barangay_other_edit{{ $user->id }}" name="barangay_other" value="{{ old('barangay_other', $user->barangay_other ?? '') }}" data-role="barangay-other">
                                @error('barangay_other')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 {{ in_array($editBarangay, ['Barangay 11', 'Barangay 12']) ? '' : 'd-none' }}" data-role="purok-group">
                                <label for="patient_purok_edit{{ $user->id }}" class="form-label">Purok <span class="text-danger">*</span></label>
                                <select class="form-select @error('purok') is-invalid @enderror" id="patient_purok_edit{{ $user->id }}" name="purok" data-role="purok" data-selected="{{ old('purok', $user->purok ?? '') }}">
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
                                <label for="patient_birth_date_edit{{ $user->id }}" class="form-label">Birth Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="patient_birth_date_edit{{ $user->id }}" name="birth_date" value="{{ $editBirthDateRaw }}" data-role="birth-date" max="{{ now()->toDateString() }}">
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

@push('scripts')
    <script>
        (function() {
            const form = document.getElementById('userFilterForm');
            const searchInput = document.getElementById('searchInput');
            const roleFilter = document.getElementById('roleFilter');
            const clearBtn = document.getElementById('clearFiltersBtn');

            let debounceTimer;
            const submitDebounced = () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => form.requestSubmit(), 400);
            };

            if (searchInput) {
                searchInput.addEventListener('input', submitDebounced);
            }
            if (roleFilter) {
                roleFilter.addEventListener('change', () => form.requestSubmit());
            }
            if (clearBtn) {
                clearBtn.addEventListener('click', () => {
                    if (searchInput) searchInput.value = '';
                    if (roleFilter) roleFilter.value = '';
                    form.requestSubmit();
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
                const patientFields = form.querySelector('[data-role="patient-fields"]');
                const phoneInput = form.querySelector('[data-role="patient-phone"]');
                const barangaySelect = form.querySelector('[data-role="barangay"]');
                const barangayOtherGroup = form.querySelector('[data-role="barangay-other-group"]');
                const barangayOtherInput = form.querySelector('[data-role="barangay-other"]');
                const purokGroup = form.querySelector('[data-role="purok-group"]');
                const purokSelect = form.querySelector('[data-role="purok"]');
                const birthDateInput = form.querySelector('[data-role="birth-date"]');

                const togglePatientFields = () => {
                    const isPatient = roleSelect && roleSelect.value === 'user';
                    if (patientFields) {
                        patientFields.classList.toggle('d-none', !isPatient);
                    }
                    if (phoneInput) {
                        if (isPatient) {
                            phoneInput.setAttribute('required', 'required');
                        } else {
                            phoneInput.removeAttribute('required');
                            phoneInput.value = '';
                        }
                    }
                    if (barangaySelect) {
                        if (isPatient) {
                            barangaySelect.setAttribute('required', 'required');
                        } else {
                            barangaySelect.removeAttribute('required');
                            barangaySelect.value = '';
                        }
                    }
                    if (birthDateInput) {
                        if (isPatient) {
                            birthDateInput.setAttribute('required', 'required');
                        } else {
                            birthDateInput.removeAttribute('required');
                            birthDateInput.value = '';
                        }
                    }
                    if (!isPatient) {
                        if (barangayOtherInput) {
                            barangayOtherInput.value = '';
                            barangayOtherInput.removeAttribute('required');
                        }
                        if (purokSelect) {
                            purokSelect.value = '';
                            purokSelect.removeAttribute('required');
                        }
                        if (barangayOtherGroup) {
                            barangayOtherGroup.classList.add('d-none');
                        }
                        if (purokGroup) {
                            purokGroup.classList.add('d-none');
                        }
                    } else {
                        handleBarangayChange();
                    }
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
                    if (!barangaySelect) {
                        return;
                    }
                    const selectedBarangay = barangaySelect.value;
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

                    if (purokGroup && purokSelect) {
                        if (barangayPurokMap[selectedBarangay]) {
                            purokGroup.classList.remove('d-none');
                            updatePurokOptions(selectedBarangay);
                        } else {
                            purokGroup.classList.add('d-none');
                            purokSelect.removeAttribute('required');
                            purokSelect.value = '';
                            purokSelect.setAttribute('data-selected', '');
                        }
                    }
                };

                if (roleSelect) {
                    roleSelect.addEventListener('change', togglePatientFields);
                    togglePatientFields();
                }

                if (barangaySelect) {
                    barangaySelect.addEventListener('change', () => {
                        if (purokSelect) {
                            purokSelect.setAttribute('data-selected', '');
                        }
                        handleBarangayChange();
                    });
                    handleBarangayChange();
                }

                if (birthDateInput) {
                    birthDateInput.addEventListener('change', () => {});
                    birthDateInput.addEventListener('keyup', () => {});
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initSuperadminUserForms();
        });
    </script>
@endpush