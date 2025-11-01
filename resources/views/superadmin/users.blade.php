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
    </style>
@endsection

@section('content')
                        <!-- Add User Button -->
                        <div class="d-flex justify-content-end align-items-center mb-4">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="fas fa-plus me-2"></i> Add User
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
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-2"></i>Search
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="clearFiltersBtn">
                                    <i class="fas fa-times me-2"></i>Clear
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
                                                    <th>Role</th>
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
                                                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                                                <i class="fas fa-edit me-1"></i> Edit
                                                            </button>
                                                            @if($user->id !== Auth::id())
                                                            <form method="POST" action="{{ route('superadmin.user.delete', $user) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                                @csrf
                                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                                    <i class="fas fa-trash me-1"></i> Delete
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
                <form method="POST" action="{{ route('superadmin.user.create') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role *</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="user">Patient</option>
                                <option value="admin">Admin</option>
                            </select>
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
                <form method="POST" action="{{ route('superadmin.user.update', $user) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name{{ $user->id }}" class="form-label">Full Name *</label>
                            <input type="text" class="form-control" id="name{{ $user->id }}" name="name" value="{{ $user->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="email{{ $user->id }}" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email{{ $user->id }}" name="email" value="{{ $user->email }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="password{{ $user->id }}" class="form-label">Password (leave blank to keep current)</label>
                            <input type="password" class="form-control" id="password{{ $user->id }}" name="password">
                        </div>
                        <div class="mb-3">
                            <label for="role{{ $user->id }}" class="form-label">Role *</label>
                            <select class="form-select" id="role{{ $user->id }}" name="role" required>
                                <option value="user" {{ ($user->role == 'user' || $user->role == 'patient') ? 'selected' : '' }}>Patient</option>
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
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
    </script>
@endpush