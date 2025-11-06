<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MALASAKIT - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Theme-aware text */
        body { color: #111; }
        body.bg-dark { color: #fff; }
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
            color:rgb(255, 255, 255);
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
        /* Dark mode surfaces */
        body.bg-dark .main-content { background-color: #151718; }
        body.bg-dark .sidebar { background: #131516; border-right-color: #2a2f35; }
        body.bg-dark .header { background: #1b1e20; border-bottom-color: #2a2f35; }
        body.bg-dark .metric-card, body.bg-dark .chart-container { background: #1e2124; color: #e6e6e6; box-shadow: 0 2px 8px rgba(0,0,0,0.3); }
        body.bg-dark .table thead { background: #1a1f24; color: #e6e6e6; }
        .metric-number { color: inherit; }
        /* Dark mode modal */
        body.bg-dark .modal-content { background: #1e2124; color: #e6e6e6; border-color: #2a2f35; }
        body.bg-dark .modal-content .form-label { color: #e6e6e6; }
        body.bg-dark .modal-content .form-control,
        body.bg-dark .modal-content .form-select { background-color: #0f1316; color: #e6e6e6; border-color: #2a2f35; }
        body.bg-dark .modal-content .form-control::placeholder { color: #9aa4ad; }
        /* Improve dark-mode text contrast for small/secondary text */
        body.bg-dark .metric-label { color: #cbd3da; }
        body.bg-dark .metric-change, body.bg-dark .text-muted { color: #b0b0b0 !important; }
    </style>
</head>
<body>
    <script>
        // Apply saved theme
        (function(){
            if (localStorage.getItem('app-theme') === 'dark') {
                document.body.classList.add('bg-dark','text-white');
            }
        })();
    </script>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 p-0">
                <div class="sidebar">
                    <div class="p-3">
                        <div class="d-flex align-items-center mb-4">
                            <img src="{{ asset('images/malasakit-logo-blue.png') }}" alt="Logo" class="me-3" style="width: 52px; height: 52px;">
                            <div>
                                <h6 class="mb-0 fw-bold" style="letter-spacing:.5px;">MALASAKIT</h6>
                            </div>
                        </div>
                        <nav class="nav flex-column">
                            <a class="nav-link active" href="#">
                                <i class="fas fa-th-large me-2"></i> Dashboard
                            </a>
                            <a class="nav-link" href="{{ route('admin.patients') }}">
                                <i class="fas fa-user me-2"></i> Patient Management
                            </a>
                            <a class="nav-link" href="{{ route('admin.appointments') }}">
                                <i class="fas fa-calendar-check me-2"></i> Appointments
                            </a>
                            <a class="nav-link" href="{{ route('admin.reports') }}">
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
                            <h4 class="mb-0">Dashboard Overview</h4>
                            <p class="text-muted mb-0">Welcome back! Here's what's happening today.</p>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-bell text-muted me-3"></i>
                            <button class="btn btn-link text-decoration-none text-muted me-2" id="themeToggle" title="Toggle theme" aria-label="Toggle theme">
                                <i class="fas fa-moon"></i>
                            </button>
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
                    <div class="p-4">
                        <!-- Quick Shortcuts -->
                        <div class="d-flex justify-content-end flex-wrap gap-2 mb-4">
                            <a href="{{ route('admin.appointments') }}" class="btn btn-primary"><i class="fas fa-calendar-plus me-2"></i>Book appointment</a>
                            <a href="{{ route('admin.patients') }}" class="btn btn-outline-primary"><i class="fas fa-user-plus me-2"></i>Add patient</a>
                        </div>

                        <!-- Metrics Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3 mb-3">
                                <div class="metric-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="metric-label">Total Patients</div>
                                            <div class="metric-number">{{ $totalPatients ?? 0 }}</div>
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
                                            <div class="metric-number">{{ $todayAppointments ?? 0 }}</div>
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
                                            <div class="metric-number">{{ $lowStockItems ?? 0 }}</div>
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
                                            <div class="metric-number">{{ $monthlyServices ?? 0 }}</div>
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
                                    <h6 class="mb-3">Today's Schedule</h6>
                                    @if(($todaysAppointments ?? collect([]))->count() > 0)
                                        @foreach($todaysAppointments as $appointment)
                                        <div class="activity-item">
                                            <div class="activity-icon status-progress">
                                                <i class="fas fa-user-clock"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">{{ $appointment->patient_name ?? ($appointment->user->name ?? 'Walk-in Patient') }}</div>
                                                <div class="text-muted">{{ $appointment->service_type }} • {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</div>
                                            </div>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button class="btn btn-outline-secondary">Arrived</button>
                                                <button class="btn btn-outline-secondary">In progress</button>
                                                <button class="btn btn-outline-secondary">Completed</button>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-3">
                                            <p class="text-muted mb-0">No appointments for today</p>
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

    <!-- Walk-in Modal -->
    <div class="modal fade" id="walkInModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Walk-in Patient</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.walk-in') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="patient_name" class="form-label">Patient Name *</label>
                            <input type="text" class="form-control" id="patient_name" name="patient_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="patient_phone" class="form-label">Phone Number *</label>
                            <input type="tel" class="form-control" id="patient_phone" name="patient_phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="patient_address" class="form-label">Address *</label>
                            <textarea class="form-control" id="patient_address" name="patient_address" rows="2" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="service_type" class="form-label">Service *</label>
                            <select class="form-control" id="service_type" name="service_type" required>
                                <option value="">Select Service</option>
                                <option value="General Checkup">General Checkup</option>
                                <option value="Prenatal">Prenatal</option>
                                <option value="Medical Check-up">Medical Check-up</option>
                                <option value="Immunization">Immunization</option>
                                <option value="Family Planning">Family Planning</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Walk-in Patient</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Get data from Laravel
        const weeklyData = @json($weeklyAppointments);
        const serviceData = @json($serviceTypes);
        const barangayData = @json($patientsByBarangay);
        // Theme toggle persistence
        (function(){
            const key = 'app-theme';
            const btn = document.getElementById('themeToggle');
            if (btn) {
                btn.addEventListener('click', function(){
                    const isDark = document.body.classList.toggle('bg-dark');
                    document.body.classList.toggle('text-white', isDark);
                    localStorage.setItem(key, isDark ? 'dark' : 'light');
                });
            }
        })();

        // Prepare weekly appointments data
        const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        const weeklyLabels = [];
        const weeklyCounts = [];
        
        // Initialize with zeros for all days
        for (let i = 1; i <= 7; i++) {
            weeklyLabels.push(dayNames[i]);
            const dayData = weeklyData.find(item => item.day_of_week == i);
            weeklyCounts.push(dayData ? dayData.count : 0);
        }

        // Overview Chart (Weekly Appointments)
        const overviewCtx = document.getElementById('overviewChart').getContext('2d');
        new Chart(overviewCtx, {
            type: 'line',
            data: {
                labels: weeklyLabels,
                datasets: [{
                    label: 'Appointments',
                    data: weeklyCounts,
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
        const serviceLabels = serviceData.map(item => item.service_type);
        const serviceCounts = serviceData.map(item => item.count);
        
        new Chart(serviceCtx, {
            type: 'bar',
            data: {
                labels: serviceLabels,
                datasets: [{
                    label: 'Services This Month',
                    data: serviceCounts,
                    backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#17a2b8', '#6f42c1']
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
        const barangayLabels = barangayData.map(item => item.barangay);
        const barangayCounts = barangayData.map(item => item.count);
        
        new Chart(barangayCtx, {
            type: 'doughnut',
            data: {
                labels: barangayLabels,
                datasets: [{
                    data: barangayCounts,
                    backgroundColor: ['#007bff', '#dc3545', '#28a745', '#ffc107', '#17a2b8', '#6f42c1']
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