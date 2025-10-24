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
        .log-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #007bff, #0056b3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.9rem;
        }
        .modal-content {
            border-radius: 12px;
            border: none;
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
                            <a class="nav-link active" href="{{ route('superadmin.system-logs') }}">
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
                            <h4 class="mb-0">System Logs</h4>
                            <p class="text-muted mb-0">View and manage system activity logs</p>
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
                        @if($logs->count() > 0)
                            <div class="row">
                                @foreach($logs as $log)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="appointment-card">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="log-avatar me-3">
                                                    {{ substr($log->user ? $log->user->name : 'System', 0, 1) }}
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-1">{{ $log->user ? $log->user->name : 'System' }}</h6>
                                                    <small class="text-muted">{{ $log->action }}</small>
                                                </div>
                                            </div>
                                            <span class="status-badge 
                                                @if($log->action == 'created') status-approved
                                                @elseif($log->action == 'updated') status-pending
                                                @elseif($log->action == 'deleted') status-cancelled
                                                @else status-completed
                                                @endif">
                                                {{ ucfirst($log->action) }}
                                            </span>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-table text-primary me-2"></i>
                                                <span class="text-muted">Table:</span>
                                                <span class="ms-2">{{ $log->table_name ?? 'N/A' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-hashtag text-primary me-2"></i>
                                                <span class="text-muted">Record ID:</span>
                                                <span class="ms-2">{{ $log->record_id ?? 'N/A' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-clock text-primary me-2"></i>
                                                <span class="text-muted">Time:</span>
                                                <span class="ms-2">{{ $log->created_at->format('M d, Y H:i:s') }}</span>
                                            </div>
                                            @if($log->ip_address)
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-globe text-primary me-2"></i>
                                                <span class="text-muted">IP:</span>
                                                <span class="ms-2">{{ $log->ip_address }}</span>
                                            </div>
                                            @endif
                                        </div>

                                        <div class="d-flex gap-2">
                                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#logDetailsModal{{ $log->id }}">
                                                <i class="fas fa-eye me-1"></i> View Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-list fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No system logs found</h5>
                                <p class="text-muted">There are no system logs to display at the moment.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Details Modals -->
    @foreach($logs as $log)
    <div class="modal fade" id="logDetailsModal{{ $log->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Log Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Basic Information</h6>
                            <div class="mb-3">
                                <label class="form-label text-muted">User</label>
                                <p class="fw-bold">{{ $log->user ? $log->user->name : 'System' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Action</label>
                                <p>{{ ucfirst($log->action) }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Table</label>
                                <p>{{ $log->table_name ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Record ID</label>
                                <p>{{ $log->record_id ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Change Details</h6>
                            @if($log->old_values)
                                <div class="mb-3">
                                    <strong class="text-dark">Old Values:</strong>
                                    <pre class="bg-light p-3 small rounded" style="border: 1px solid #e9ecef;">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @endif
                            @if($log->new_values)
                                <div class="mb-3">
                                    <strong class="text-dark">New Values:</strong>
                                    <pre class="bg-light p-3 small rounded" style="border: 1px solid #e9ecef;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label class="form-label text-muted">IP Address</label>
                                <p>{{ $log->ip_address ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Timestamp</label>
                                <p>{{ $log->created_at->format('F d, Y \a\t g:i A') }}</p>
                            </div>
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

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>