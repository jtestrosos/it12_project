<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Management - Barangay Health Center</title>
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
        .status-user {
            background-color: #cce5ff;
            color: #004085;
        }
        .status-admin {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-superadmin {
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
                            <a class="nav-link" href="{{ route('superadmin.dashboard') }}">
                                <i class="fas fa-th-large me-2"></i> Dashboard
                            </a>
                            <a class="nav-link active" href="{{ route('superadmin.users') }}">
                                <i class="fas fa-user me-2"></i> User Management
                            </a>
                            <a class="nav-link" href="{{ route('superadmin.system-logs') }}">
                                <i class="fas fa-list me-2"></i> System Logs
                            </a>
                            <a class="nav-link" href="{{ route('superadmin.analytics') }}">
                                <i class="fas fa-chart-bar me-2"></i> Analytics
                            </a>
                            <a class="nav-link" href="{{ route('superadmin.backup') }}">
                                <i class="fas fa-download me-2"></i> Backup
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
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    SA
                                </div>
                                <div>
                                    <div class="fw-bold">Super Admin</div>
                                    <small class="text-muted">Administrator</small>
                                </div>
                            </div>
                            <a href="{{ route('logout') }}" class="btn btn-outline-secondary ms-3" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <!-- Add User Button -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="mb-0">User Management</h5>
                                <small class="text-muted">Manage system users and their roles</small>
                            </div>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="fas fa-plus me-2"></i> Add User
                            </button>
                        </div>

                        <!-- Search and Filter -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="searchInput" placeholder="Search users by name or email...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="roleFilter">
                                    <option value="">All Roles</option>
                                    <option value="superadmin">Super Admin</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                                    <i class="fas fa-times me-2"></i>Clear Filters
                                </button>
                            </div>
                        </div>

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
                                                    <td>
                                                        <span class="status-badge 
                                                            @if($user->role == 'superadmin') status-superadmin
                                                            @elseif($user->role == 'admin') status-admin
                                                            @else status-user
                                                            @endif">
                                                            {{ ucfirst($user->role) }}
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
                                            {{ $users->links() }}
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
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                                <option value="patient">Patient</option>
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
                    @method('PUT')
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
                                <option value="patient" {{ $user->role == 'patient' ? 'selected' : '' }}>Patient</option>
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

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            filterTable();
        });

        document.getElementById('roleFilter').addEventListener('change', function() {
            filterTable();
        });

        function filterTable() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const roleFilter = document.getElementById('roleFilter').value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const name = row.cells[0].textContent.toLowerCase();
                const email = row.cells[1].textContent.toLowerCase();
                const roleText = row.cells[2].textContent.toLowerCase();

                const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
                
                // Map role filter values to actual role text
                let matchesRole = true;
                if (roleFilter) {
                    if (roleFilter === 'superadmin' && roleText.includes('superadmin')) {
                        matchesRole = true;
                    } else if (roleFilter === 'admin' && roleText.includes('admin')) {
                        matchesRole = true;
                    } else if (roleFilter === 'user' && roleText.includes('user')) {
                        matchesRole = true;
                    } else {
                        matchesRole = false;
                    }
                }

                if (matchesSearch && matchesRole) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function clearFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('roleFilter').value = '';
            filterTable();
        }
    </script>
</body>
</html>