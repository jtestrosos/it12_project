@extends('superadmin.layout')

@section('title', 'Dashboard - Barangay Health Center')
@section('page-title', 'Dashboard Overview')


@section('page-styles')
<style>
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
@endsection

@section('content')
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
                                            <div class="metric-number">{{ ($todayCompleted ?? 0) + ($todayPending ?? 0) }}</div>
                                            <div class="metric-change text-info">{{ $todayCompleted ?? 0 }} completed, {{ $todayPending ?? 0 }} pending</div>
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
                                            <div class="metric-label">Total Appointments</div>
                                            <div class="metric-number">{{ $totalAppointments ?? 0 }}</div>
                                            <div class="metric-change text-success">{{ $pendingAppointments ?? 0 }} pending</div>
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
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Get data from Laravel
        const weeklyData = @json($weeklyAppointments ?? []);
        const serviceData = @json($serviceTypes ?? []);
        const barangayData = @json($patientsByBarangay ?? []);

        // Overview Chart (Weekly Appointments)
        const overviewCtx = document.getElementById('overviewChart').getContext('2d');
        
        const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        const weeklyLabels = [];
        const weeklyCounts = [];
        
        for (let i = 0; i < 7; i++) {
            weeklyLabels.push(daysOfWeek[i]);
            const dayData = weeklyData.find(item => item.day_of_week == i + 1);
            weeklyCounts.push(dayData ? dayData.count : 0);
        }
        
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
        const serviceLabels = serviceData.map(item => item.service_type || 'Other');
        const serviceCounts = serviceData.map(item => item.count);
        
        new Chart(serviceCtx, {
            type: 'bar',
            data: {
                labels: serviceLabels,
                datasets: [{
                    label: 'Services This Month',
                    data: serviceCounts,
                    backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1']
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
                labels: barangayLabels.length > 0 ? barangayLabels : ['No Data'],
                datasets: [{
                    data: barangayCounts.length > 0 ? barangayCounts : [0],
                    backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#17a2b8']
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