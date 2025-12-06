@extends('admin.layout')

@section('title', 'MALASAKIT - Admin')
@section('page-title', 'Dashboard Overview')
@section('page-description', "Welcome back! Here's what's happening today.")

@section('page-styles')
    <style>
        /* Theme-aware text */
        body {
            color: #111;
        }

        body.bg-dark {
            color: #fff;
        }

        .metric-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 1.5rem;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
            border: 1px solid #edf1f7;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            height: 100%;
        }

        .metric-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.10);
            border-color: #d0e2ff;
        }

        .metric-number {
            font-size: 2.35rem;
            font-weight: 700;
            color: #111827;
            line-height: 1.1;
        }

        .metric-label {
            color: #6b7280;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.35rem;
        }

        .metric-change {
            font-size: 0.78rem;
            margin-top: 0.35rem;
        }

        .metric-icon-pill {
            width: 42px;
            height: 42px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eff6ff;
            color: #1d4ed8;
        }

        .metric-icon-pill.metric-icon-warning {
            background: #fff7ed;
            color: #c05621;
        }

        .metric-icon-pill.metric-icon-danger {
            background: #fef2f2;
            color: #b91c1c;
        }

        .metric-icon-pill.metric-icon-success {
            background: #ecfdf5;
            color: #047857;
        }

        .chart-container {
            background: #ffffff;
            border-radius: 14px;
            padding: 1.5rem 1.5rem 1.25rem;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
            margin-bottom: 1.5rem;
            height: 420px;
            position: relative;
            border: 1px solid #edf1f7;
        }

        .chart-container canvas {
            max-height: 320px !important;
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
        body.bg-dark .main-content {
            background-color: #151718;
        }

        /* Use the same darker sidebar color as Inventory/Appointments */
        body.bg-dark .sidebar {
            background: #131516;
            border-right-color: #2a2f35;
        }

        body.bg-dark .header {
            background: #1b1e20;
            border-bottom-color: #2a2f35;
        }

        body.bg-dark .metric-card,
        body.bg-dark .chart-container {
            background: #1e2124;
            color: #e6e6e6;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            border-color: #2a2f35;
        }

        body.bg-dark .table thead {
            background: #1a1f24;
            color: #e6e6e6;
        }

        .metric-number {
            color: inherit;
        }

        /* Dark mode modal */
        body.bg-dark .modal-content {
            background: #1e2124;
            color: #e6e6e6;
            border-color: #2a2f35;
        }

        body.bg-dark .modal-content .form-label {
            color: #e6e6e6;
        }

        body.bg-dark .modal-content .form-control,
        body.bg-dark .modal-content .form-select {
            background-color: #0f1316;
            color: #e6e6e6;
            border-color: #2a2f35;
        }

        body.bg-dark .modal-content .form-control::placeholder {
            color: #9aa4ad;
        }

        /* Improve dark-mode text contrast for small/secondary text */
        body.bg-dark .metric-label {
            color: #cbd3da;
        }

        body.bg-dark .metric-change,
        body.bg-dark .text-muted {
            color: #b0b0b0 !important;
        }

        body.bg-dark .metric-icon-pill {
            background: rgba(59, 130, 246, 0.18);
            color: #bfdbfe;
        }

        body.bg-dark .metric-icon-pill.metric-icon-warning {
            background: rgba(251, 191, 36, 0.28);
            color: #fef9c3;
        }

        body.bg-dark .metric-icon-pill.metric-icon-danger {
            background: rgba(248, 113, 113, 0.25);
            color: #fee2e2;
        }

        body.bg-dark .metric-icon-pill.metric-icon-success {
            background: rgba(16, 185, 129, 0.25);
            color: #a7f3d0;
        }

        @media (max-width: 767.98px) {
            .chart-container {
                height: auto;
            }

            .chart-container canvas {
                max-height: 260px !important;
            }
        }
        }

        /* Filter Toggle Styles */
        .filter-toggle {
            background: #e2e8f0;
            border-radius: 50rem;
            padding: 4px;
            display: inline-flex;
            align-items: center;
        }

        .filter-btn {
            border: none;
            background: transparent;
            padding: 6px 18px;
            border-radius: 50rem;
            font-size: 0.85rem;
            font-weight: 500;
            color: #64748b;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            line-height: 1.2;
        }

        .filter-btn:hover {
            color: #334155;
        }

        .filter-btn.active {
            background: #ffffff;
            color: #0f172a;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            font-weight: 600;
        }

        /* Dark mode adjustments for filters */
        body.bg-dark .filter-toggle {
            background: #27272a;
        }

        body.bg-dark .filter-btn {
            color: #a1a1aa;
        }

        body.bg-dark .filter-btn:hover {
            color: #e4e4e7;
        }

        body.bg-dark .filter-btn.active {
            background: #3f3f46;
            color: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        /* Dark mode for alerts */
        body.bg-dark .alert-primary {
            background-color: rgba(0, 102, 230, 0.2);
            border-color: hsl(210, 100%, 45%);
            color: hsl(210, 100%, 75%);
        }

        body.bg-dark .alert-warning {
            background-color: rgba(250, 114, 76, 0.2);
            border-color: hsl(14, 90%, 60%);
            color: hsl(14, 90%, 75%);
        }

        body.bg-dark .alert-danger {
            background-color: rgba(220, 53, 69, 0.2);
            border-color: hsl(0, 75%, 55%);
            color: hsl(0, 75%, 75%);
        }
    </style>
@endsection

@section('content')
    <div class="p-0 p-md-4">
        <!-- Announcements and Alerts -->
        @if (session('announcement'))
            <div class="alert alert-primary alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-bullhorn me-2"></i> {{ session('announcement') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (($expiringSoonCount ?? 0) > 0)
            <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> <strong>Attention:</strong> {{ $expiringSoonCount }} item(s)
                are expiring within the next 90 days.
                <a href="{{ route('admin.inventory') }}" class="alert-link">View Inventory</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (($outOfStockCount ?? 0) > 0)
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-times-circle me-2"></i> <strong>Critical:</strong> {{ $outOfStockCount }} item(s) are out of
                stock.
                <a href="{{ route('admin.inventory') }}" class="alert-link">Restock Now</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Metrics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="metric-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="metric-label">Total Patients</div>
                            <div class="metric-number">{{ $totalPatients ?? 0 }}</div>
                            @if(!is_null($patientsChange ?? null))
                                <div class="metric-change {{ ($patientsChange ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ ($patientsChange ?? 0) >= 0 ? '+' : '' }}{{ $patientsChange }}% from last month
                                </div>
                            @else
                                <div class="metric-change text-muted">No data from last month</div>
                            @endif
                        </div>
                        <div class="metric-icon-pill">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="metric-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="metric-label">Today's Appointments</div>
                            <div class="metric-number">{{ $todayAppointments ?? 0 }}</div>
                            <div class="metric-change text-info">
                                {{ $todayCompleted ?? 0 }} completed, {{ $todayPending ?? 0 }} pending
                            </div>
                        </div>
                        <div class="metric-icon-pill metric-icon-warning">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="metric-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="metric-label">Low Stock Items</div>
                            <div class="metric-number">{{ $lowStockItems ?? 0 }}</div>
                            <div class="metric-change {{ ($lowStockItems ?? 0) > 0 ? 'text-warning' : 'text-success' }}">
                                {{ ($lowStockItems ?? 0) > 0 ? 'Needs restocking' : 'All stocks healthy' }}
                            </div>
                        </div>
                        <div class="metric-icon-pill metric-icon-danger">
                            <i class="fas fa-boxes"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="metric-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="metric-label">Services Overview</div>
                            <div class="metric-number">{{ $monthlyServices ?? 0 }}</div>
                            @if(!is_null($servicesChange ?? null))
                                <div class="metric-change {{ ($servicesChange ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ ($servicesChange ?? 0) >= 0 ? '+' : '' }}{{ $servicesChange }}% from last month
                                </div>
                            @else
                                <div class="metric-change text-muted">No data from last month</div>
                            @endif
                        </div>
                        <div class="metric-icon-pill metric-icon-success">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="chart-container">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Dashboard Overview</h6>
                        <div class="filter-toggle" id="overviewFilter">
                            <button class="filter-btn active" onclick="updateOverviewChart('weekly', this)">Weekly</button>
                            <button class="filter-btn" onclick="updateOverviewChart('monthly', this)">Monthly</button>
                            <button class="filter-btn" onclick="updateOverviewChart('yearly', this)">Yearly</button>
                        </div>
                    </div>
                    <canvas id="overviewChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Services Overview</h6>
                        <div class="filter-toggle" id="serviceFilter">
                            <button class="filter-btn" onclick="updateServiceChart('weekly', this)">Weekly</button>
                            <button class="filter-btn active" onclick="updateServiceChart('monthly', this)">Monthly</button>
                            <button class="filter-btn" onclick="updateServiceChart('yearly', this)">Yearly</button>
                        </div>
                    </div>
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
                                    <div class="fw-bold">
                                        {{ $appointment->patient_name ?? ($appointment->user->name ?? 'Walk-in Patient') }}</div>
                                    <div class="text-muted">{{ $appointment->service_type }} •
                                        {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</div>
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
    <!-- Walk-in Modal -->
    <div class="modal fade" id="walkInModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Walk-in Patient</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.walk-in.store') }}">
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
                            <textarea class="form-control" id="patient_address" name="patient_address" rows="2"
                                required></textarea>
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
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Get data from Laravel
        const chartData = @json($chartData);
        const barangayData = @json($patientsByBarangay);

        // --- Overview Chart ---
        const overviewCtx = document.getElementById('overviewChart').getContext('2d');
        let overviewChart = new Chart(overviewCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Appointments',
                    data: [],
                    borderColor: 'hsl(210, 100%, 45%)',
                    backgroundColor: 'hsla(210, 100%, 45%, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.8,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        function updateOverviewChart(timeframe, element) {
            // Update active state
            document.querySelectorAll('#overviewFilter .filter-btn').forEach(btn => btn.classList.remove('active'));
            element.classList.add('active');

            let labels = [];
            let data = [];
            const rawData = chartData.overview[timeframe];

            if (timeframe === 'weekly') {
                const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                for (let i = 1; i <= 7; i++) {
                    labels.push(dayNames[i - 1]); // Adjust index for array access
                    data.push(rawData[i] || 0);
                }
            } else if (timeframe === 'monthly') {
                const daysInMonth = new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).getDate();
                for (let i = 1; i <= daysInMonth; i++) {
                    labels.push(i);
                    data.push(rawData[i] || 0);
                }
            } else if (timeframe === 'yearly') {
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                for (let i = 1; i <= 12; i++) {
                    labels.push(monthNames[i - 1]);
                    data.push(rawData[i] || 0);
                }
            }

            overviewChart.data.labels = labels;
            overviewChart.data.datasets[0].data = data;
            overviewChart.update();
        }

        // Initialize Overview Chart
        // No need to call manually as the HTML has 'active' class and initial data is empty but will be filled on first click or we can init here
        // Actually, let's just trigger the click on the active button to load data
        document.querySelector('#overviewFilter .filter-btn.active').click();


        // --- Service Chart ---
        const serviceCtx = document.getElementById('serviceChart').getContext('2d');
        let serviceChart = new Chart(serviceCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Services',
                    data: [],
                    backgroundColor: [
                        'hsl(210, 100%, 45%)',  // Primary Blue
                        'hsl(174, 62%, 47%)',   // Secondary Teal
                        'hsl(14, 90%, 60%)',    // Accent Coral
                        'hsl(0, 75%, 55%)',     // Danger Red
                        'hsl(280, 60%, 55%)',   // Purple
                        'hsl(210, 100%, 65%)',  // Light Blue
                        'hsl(174, 62%, 67%)'    // Light Teal
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.8,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        function updateServiceChart(timeframe, element) {
            // Update active state
            document.querySelectorAll('#serviceFilter .filter-btn').forEach(btn => btn.classList.remove('active'));
            element.classList.add('active');

            const rawData = chartData.services[timeframe];
            const labels = rawData.map(item => item.service_type);
            const data = rawData.map(item => item.count);

            serviceChart.data.labels = labels;
            serviceChart.data.datasets[0].data = data;
            serviceChart.data.datasets[0].label = 'Services (' + timeframe.charAt(0).toUpperCase() + timeframe.slice(1) + ')';
            serviceChart.update();
        }

        // Initialize Service Chart
        document.querySelector('#serviceFilter .filter-btn.active').click();

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
                    backgroundColor: [
                        'hsl(210, 100%, 45%)',  // Primary Blue
                        'hsl(174, 62%, 47%)',   // Secondary Teal
                        'hsl(14, 90%, 60%)',    // Accent Coral
                        'hsl(0, 75%, 55%)',     // Danger Red
                        'hsl(280, 60%, 55%)',   // Purple
                        'hsl(210, 100%, 65%)'   // Light Blue
                    ]
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
@endpush