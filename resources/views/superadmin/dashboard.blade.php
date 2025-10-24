<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Health Center - Staff Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .metric-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: none;
            transition: transform 0.2s ease;
        }
        .metric-card:hover {
            transform: translateY(-2px);
        }
        .metric-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
        }
        .metric-label {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .metric-change {
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }
        .chart-container {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
            height: 450px;
            position: relative;
        }
        .chart-container canvas {
            max-height: 350px !important;
            max-width: 100% !important;
            width: auto !important;
            height: auto !important;
        }
        .activity-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f3f4;
        }
        .activity-item:last-child {
            border-bottom: none;
        }
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-progress {
            background-color: #d1ecf1;
            color: #0c5460;
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
                            <a class="nav-link active" href="#">
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
                            <h4 class="mb-0">Dashboard Overview</h4>
                            <p class="text-muted mb-0">Welcome back! Here's what's happening today.</p>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-bell text-muted me-3"></i>
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
                        <!-- Metrics Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3 mb-3">
                                <div class="metric-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="metric-label">Total Patients</div>
                                            <div class="metric-number">{{ $totalUsers ?? 0 }}</div>
                                            <div class="metric-change text-success">+12% from last month</div>
                                        </div>
                                        <div class="text-primary">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="metric-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="metric-label">Today's Appointments</div>
                                            <div class="metric-number">{{ $totalAppointments ?? 0 }}</div>
                                            <div class="metric-change text-info">8 completed, 16 pending</div>
                                        </div>
                                        <div class="text-warning">
                                            <i class="fas fa-calendar-check fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="metric-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="metric-label">Low Stock Items</div>
                                            <div class="metric-number">{{ $totalInventory ?? 0 }}</div>
                                            <div class="metric-change text-warning">Needs restocking</div>
                                        </div>
                                        <div class="text-danger">
                                            <i class="fas fa-boxes fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="metric-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="metric-label">Services This Month</div>
                                            <div class="metric-number">{{ $totalPatients ?? 0 }}</div>
                                            <div class="metric-change text-success">+8% from last month</div>
                                        </div>
                                        <div class="text-success">
                                            <i class="fas fa-heartbeat fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="chart-container">
                                    <h6 class="mb-3">Dashboard Overview</h6>
                                    <canvas id="overviewChart"></canvas>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="chart-container">
                                    <h6 class="mb-3">Service this Month</h6>
                                    <canvas id="serviceChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Section -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="chart-container">
                                    <h6 class="mb-3">Patients by Barangay</h6>
                                    <canvas id="barangayChart"></canvas>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="chart-container">
                                    <h6 class="mb-3">Recent Activity</h6>
                                    @if(isset($recentLogs) && $recentLogs->count() > 0)
                                        @foreach($recentLogs->take(5) as $log)
                                        <div class="activity-item">
                                            <div class="activity-icon 
                                                @if($log->action == 'created') status-completed
                                                @elseif($log->action == 'updated') status-progress
                                                @elseif($log->action == 'deleted') status-pending
                                                @else status-progress
                                                @endif">
                                                @if($log->action == 'created')
                                                    <i class="fas fa-plus"></i>
                                                @elseif($log->action == 'updated')
                                                    <i class="fas fa-edit"></i>
                                                @elseif($log->action == 'deleted')
                                                    <i class="fas fa-trash"></i>
                                                @else
                                                    <i class="fas fa-info"></i>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">{{ $log->user ? $log->user->name : 'System' }}</div>
                                                <div class="text-muted">{{ $log->action }}</div>
                                                <small class="text-muted">{{ $log->created_at->format('h:i A') }}</small>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-3">
                                            <p class="text-muted mb-0">No recent activity</p>
                                        </div>
                                    @endif
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
        // Overview Chart (Weekly Appointments)
        const overviewCtx = document.getElementById('overviewChart').getContext('2d');
        new Chart(overviewCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Appointments',
                    data: [12, 19, 3, 5, 2, 3, 8],
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.8,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Service Chart (Monthly Services)
        const serviceCtx = document.getElementById('serviceChart').getContext('2d');
        new Chart(serviceCtx, {
            type: 'bar',
            data: {
                labels: ['General Checkup', 'Prenatal'],
                datasets: [{
                    label: 'Services This Month',
                    data: [2, 1],
                    backgroundColor: ['#007bff', '#28a745']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.8,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Barangay Chart
        const barangayCtx = document.getElementById('barangayChart').getContext('2d');
        new Chart(barangayCtx, {
            type: 'doughnut',
            data: {
                labels: ['Barangay 12', 'Others'],
                datasets: [{
                    data: [85, 15],
                    backgroundColor: ['#007bff', '#e9ecef']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.0,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>