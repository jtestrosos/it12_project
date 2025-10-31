<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services & Reports - Barangay Health Center</title>
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
        .stats-card {
            background: white;
            border-radius: 6px;
            padding: 0.75rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 0.5rem;
            border: none;
            transition: transform 0.2s ease;
        }
        .stats-card:hover {
            transform: translateY(-2px);
        }
        .stat-number {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        .stat-label {
            color: #6c757d;
            font-size: 0.75rem;
            margin-bottom: 0;
        }
        .chart-container {
            background: white;
            border-radius: 8px;
            padding: 0.75rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 0.5rem;
            height: 500px;
        }
        .chart-container canvas {
            max-height: 460px !important;
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
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-th-large me-2"></i> Dashboard
                            </a>
                            <a class="nav-link" href="{{ route('admin.patients') }}">
                                <i class="fas fa-user me-2"></i> Patient Management
                            </a>
                            <a class="nav-link" href="{{ route('admin.appointments') }}">
                                <i class="fas fa-calendar-check me-2"></i> Appointments
                            </a>
                            <a class="nav-link active" href="{{ route('admin.reports') }}">
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
                            <h4 class="mb-0">Services & Reports</h4>
                            <p class="text-muted mb-0">Analytics and reporting dashboard</p>
                        </div>
                        <div class="d-flex align-items-center">
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

                    <!-- Content -->
                    <div class="p-2">
                        <!-- Statistics Row -->
                        <div class="row mb-2">
                            <!-- Appointment Statistics -->
                            <div class="col-md-6">
                                <div class="chart-container" style="height: 150px; padding: 0.75rem;">
                                    <h6 class="mb-2" style="font-size: 1rem;">Appointment Statistics</h6>
                                    <div class="row justify-content-center">
                                        <div class="col-2">
                                            <div class="stats-card text-center">
                                                <div class="stat-number text-primary">{{ $appointmentStats['total'] ?? 0 }}</div>
                                                <div class="stat-label">Total</div>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="stats-card text-center">
                                                <div class="stat-number text-warning">{{ $appointmentStats['pending'] ?? 0 }}</div>
                                                <div class="stat-label">Pending</div>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="stats-card text-center">
                                                <div class="stat-number text-success">{{ $appointmentStats['approved'] ?? 0 }}</div>
                                                <div class="stat-label">Approved</div>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="stats-card text-center">
                                                <div class="stat-number text-info">{{ $appointmentStats['completed'] ?? 0 }}</div>
                                                <div class="stat-label">Completed</div>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="stats-card text-center">
                                                <div class="stat-number text-danger">{{ $appointmentStats['cancelled'] ?? 0 }}</div>
                                                <div class="stat-label">Cancelled</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Inventory Statistics -->
                            <div class="col-md-6">
                                <div class="chart-container" style="height: 150px; padding: 0.75rem;">
                                    <h6 class="mb-2" style="font-size: 1rem;">Inventory Statistics</h6>
                                    <div class="row justify-content-center">
                                        <div class="col-3">
                                            <div class="stats-card text-center">
                                                <div class="stat-number text-primary">{{ $inventoryStats['total_items'] ?? 0 }}</div>
                                                <div class="stat-label">Total Items</div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="stats-card text-center">
                                                <div class="stat-number text-warning">{{ $inventoryStats['low_stock'] ?? 0 }}</div>
                                                <div class="stat-label">Low Stock</div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="stats-card text-center">
                                                <div class="stat-number text-danger">{{ $inventoryStats['out_of_stock'] ?? 0 }}</div>
                                                <div class="stat-label">Out of Stock</div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="stats-card text-center">
                                                <div class="stat-number text-dark">{{ $inventoryStats['expired'] ?? 0 }}</div>
                                                <div class="stat-label">Expired</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts Row -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="chart-container">
                                    <h6 class="mb-2">Appointments by Service Type</h6>
                                    <canvas id="serviceChart" height="460"></canvas>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="chart-container">
                                    <h6 class="mb-2">Monthly Appointments Trend</h6>
                                    <canvas id="trendChart" height="460"></canvas>
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
        // Get data from Laravel
        const serviceData = @json($serviceTypes);
        const monthlyData = @json($monthlyTrend);

        // Service Chart
        const serviceCtx = document.getElementById('serviceChart').getContext('2d');
        const serviceLabels = serviceData.map(item => item.service_type);
        const serviceCounts = serviceData.map(item => item.count);
        
        new Chart(serviceCtx, {
            type: 'doughnut',
            data: {
                labels: serviceLabels,
                datasets: [{
                    data: serviceCounts,
                    backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#17a2b8', '#6f42c1']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 0.9,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 16
                            },
                            padding: 25
                        }
                    }
                }
            }
        });

        // Trend Chart - Monthly Appointments
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        
        // Prepare monthly data
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const trendLabels = [];
        const trendCounts = [];
        
        // Get last 6 months
        for (let i = 5; i >= 0; i--) {
            const date = new Date();
            date.setMonth(date.getMonth() - i);
            const month = date.getMonth() + 1;
            const year = date.getFullYear();
            
            trendLabels.push(monthNames[date.getMonth()]);
            
            const monthData = monthlyData.find(item => item.month == month && item.year == year);
            trendCounts.push(monthData ? monthData.count : 0);
        }
        
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Appointments',
                    data: trendCounts,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4,
                    borderWidth: 5,
                    pointRadius: 8,
                    pointHoverRadius: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.2,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 16
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 16
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                size: 16
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>