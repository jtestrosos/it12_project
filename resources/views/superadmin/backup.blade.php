<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Backup - Barangay Health Center</title>
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
        .backup-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
            border: none;
            transition: transform 0.2s ease;
        }
        .backup-card:hover {
            transform: translateY(-2px);
        }
        .backup-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .backup-success {
            background-color: #d4edda;
            color: #155724;
        }
        .backup-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        .backup-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        .backup-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .progress-bar {
            height: 8px;
            border-radius: 4px;
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
                            <a class="nav-link" href="{{ route('superadmin.users') }}">
                                <i class="fas fa-user me-2"></i> User Management
                            </a>
                            <a class="nav-link" href="{{ route('superadmin.system-logs') }}">
                                <i class="fas fa-list me-2"></i> System Logs
                            </a>
                            <a class="nav-link" href="{{ route('superadmin.analytics') }}">
                                <i class="fas fa-chart-bar me-2"></i> Analytics
                            </a>
                            <a class="nav-link active" href="{{ route('superadmin.backup') }}">
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
                            <h4 class="mb-0">System Backup</h4>
                            <p class="text-muted mb-0">Manage system backups and data protection</p>
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
                        <!-- Backup Actions -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="backup-card">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="backup-icon backup-info me-3">
                                            <i class="fas fa-database"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1">Database Backup</h5>
                                            <p class="text-muted mb-0">Create a complete backup of the database</p>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary" onclick="createBackup('database')">
                                            <i class="fas fa-download me-2"></i> Backup Database
                                        </button>
                                        <button class="btn btn-outline-info" onclick="scheduleBackup('database')">
                                            <i class="fas fa-clock me-2"></i> Schedule
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="backup-card">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="backup-icon backup-warning me-3">
                                            <i class="fas fa-file-archive"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1">File System Backup</h5>
                                            <p class="text-muted mb-0">Backup uploaded files and documents</p>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-warning" onclick="createBackup('files')">
                                            <i class="fas fa-download me-2"></i> Backup Files
                                        </button>
                                        <button class="btn btn-outline-warning" onclick="scheduleBackup('files')">
                                            <i class="fas fa-clock me-2"></i> Schedule
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Full System Backup -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="backup-card">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="backup-icon backup-danger me-3">
                                            <i class="fas fa-server"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1">Full System Backup</h5>
                                            <p class="text-muted mb-0">Create a complete backup of the entire system (Database + Files + Configuration)</p>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-danger" onclick="createBackup('full')">
                                            <i class="fas fa-download me-2"></i> Full System Backup
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="scheduleBackup('full')">
                                            <i class="fas fa-clock me-2"></i> Schedule Daily
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Backup Status -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="backup-card">
                                    <h5 class="mb-3">
                                        <i class="fas fa-info-circle me-2"></i> Backup Status
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <div class="backup-icon backup-success mx-auto mb-2">
                                                    <i class="fas fa-check"></i>
                                                </div>
                                                <h6>Last Database Backup</h6>
                                                <small class="text-muted">2 hours ago</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <div class="backup-icon backup-warning mx-auto mb-2">
                                                    <i class="fas fa-exclamation"></i>
                                                </div>
                                                <h6>Last File Backup</h6>
                                                <small class="text-muted">1 day ago</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <div class="backup-icon backup-info mx-auto mb-2">
                                                    <i class="fas fa-clock"></i>
                                                </div>
                                                <h6>Next Scheduled</h6>
                                                <small class="text-muted">Tomorrow 2:00 AM</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <div class="backup-icon backup-success mx-auto mb-2">
                                                    <i class="fas fa-hdd"></i>
                                                </div>
                                                <h6>Storage Used</h6>
                                                <small class="text-muted">2.5 GB / 10 GB</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Backups -->
                        <div class="row">
                            <div class="col-12">
                                <div class="backup-card">
                                    <h5 class="mb-3">
                                        <i class="fas fa-history me-2"></i> Recent Backups
                                    </h5>
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Date</th>
                                                    <th>Size</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-primary">Database</span>
                                                    </td>
                                                    <td>Dec 15, 2024 10:30 AM</td>
                                                    <td>45.2 MB</td>
                                                    <td>
                                                        <span class="badge bg-success">Completed</span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-outline-primary btn-sm me-2">
                                                            <i class="fas fa-download me-1"></i> Download
                                                        </button>
                                                        <button class="btn btn-outline-danger btn-sm">
                                                            <i class="fas fa-trash me-1"></i> Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-warning">Files</span>
                                                    </td>
                                                    <td>Dec 14, 2024 2:00 AM</td>
                                                    <td>128.7 MB</td>
                                                    <td>
                                                        <span class="badge bg-success">Completed</span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-outline-primary btn-sm me-2">
                                                            <i class="fas fa-download me-1"></i> Download
                                                        </button>
                                                        <button class="btn btn-outline-danger btn-sm">
                                                            <i class="fas fa-trash me-1"></i> Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-danger">Full System</span>
                                                    </td>
                                                    <td>Dec 13, 2024 2:00 AM</td>
                                                    <td>1.2 GB</td>
                                                    <td>
                                                        <span class="badge bg-success">Completed</span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-outline-primary btn-sm me-2">
                                                            <i class="fas fa-download me-1"></i> Download
                                                        </button>
                                                        <button class="btn btn-outline-danger btn-sm">
                                                            <i class="fas fa-trash me-1"></i> Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function createBackup(type) {
            if (confirm(`Are you sure you want to create a ${type} backup?`)) {
                const button = event.target;
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Creating...';
                button.disabled = true;

                // Simulate backup process
                fetch('{{ route("superadmin.backup.create") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ type: type })
                })
                .then(response => response.json())
                .then(data => {
                    alert(`${type.charAt(0).toUpperCase() + type.slice(1)} backup completed successfully!`);
                    button.innerHTML = originalText;
                    button.disabled = false;
                    // Refresh the page to show updated backup list
                    location.reload();
                })
                .catch(error => {
                    alert('Error creating backup: ' + error);
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
            }
        }

        function scheduleBackup(type) {
            const schedule = prompt(`Enter schedule for ${type} backup (e.g., "daily", "weekly", "monthly"):`);
            if (schedule) {
                fetch('{{ route("superadmin.backup.schedule") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ 
                        type: type, 
                        schedule: schedule 
                    })
                })
                .then(response => response.json())
                .then(data => {
                    alert(`${type.charAt(0).toUpperCase() + type.slice(1)} backup scheduled for ${schedule}!`);
                })
                .catch(error => {
                    alert('Error scheduling backup: ' + error);
                });
            }
        }
    </script>
</body>
</html>
