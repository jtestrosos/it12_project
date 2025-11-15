@extends('superadmin.layout')

@section('title', 'Dashboard - Barangay Health Center')
@section('page-title', 'Dashboard')
@section('page-description', 'System overview and analytics')

@section('page-styles')
<style>
        html body.bg-dark [class*="admin-sidebar"], html body.bg-dark [class*="sidebar"] { background: #131516 !important; border-right-color: #2a2f35 !important; }
        .metric-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent) 0%, var(--accent-light) 100%);
        }
        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        .metric-number {
            font-size: 2.75rem;
            font-weight: 800;
            color: inherit;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        .metric-label {
            color: #6c757d;
            font-size: 0.95rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.75rem;
        }
        .metric-change {
            font-size: 0.85rem;
            margin-top: 0.75rem;
            font-weight: 500;
        }
        .metric-icon {
            width: 60px;
            height: 60px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
        }
        .chart-container {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
            height: 450px;
            position: relative;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .chart-container h6 {
            font-weight: 700;
            font-size: 1.1rem;
            color: #2c3e50;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 0.75rem;
            background: #f8f9fa;
            transition: all 0.2s ease;
        }
        .activity-item:hover {
            background: #e9ecef;
            transform: translateX(4px);
        }
        .activity-item:last-child {
            margin-bottom: 0;
        }
        .activity-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-weight: 600;
            flex-shrink: 0;
        }
        .status-completed {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }
        .status-pending {
            background: linear-gradient(135deg, #fff3cd, #ffeeba);
            color: #856404;
        }
        .status-progress {
            background: linear-gradient(135deg, #d1ecf1, #bee5eb);
            color: #0c5460;
        }
        .activity-scroll { 
            max-height: 320px; 
            overflow-y: auto;
            padding-right: 0.5rem;
        }
        .activity-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .activity-scroll::-webkit-scrollbar-track {
            background: #f1f3f4;
            border-radius: 3px;
        }
        .activity-scroll::-webkit-scrollbar-thumb {
            background: #c1c8cd;
            border-radius: 3px;
        }
    </style>
@endsection

@section('content')
                        <!-- Metrics Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3 mb-3">
                                <div class="metric-card" style="--accent: #0d6efd; --accent-light: #4d94ff;">
                                    <div class="metric-icon" style="background: linear-gradient(135deg, #0d6efd, #4d94ff);">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="metric-label">Total Patients</div>
                                    <div class="metric-number text-primary">{{ $totalUsers ?? 0 }}</div>
                                    <div class="metric-change text-success">
                                        <i class="fas fa-arrow-up me-1"></i> +12% from last month
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="metric-card" style="--accent: #ffc107; --accent-light: #ffda6a;">
                                    <div class="metric-icon" style="background: linear-gradient(135deg, #ffc107, #ffda6a);">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="metric-label">Today's Appointments</div>
                                    <div class="metric-number text-warning">{{ ($todayCompleted ?? 0) + ($todayPending ?? 0) }}</div>
                                    <div class="metric-change text-info">
                                        <i class="fas fa-check-circle me-1"></i> {{ $todayCompleted ?? 0 }} completed
                                        <span class="mx-1">·</span>
                                        <i class="fas fa-clock me-1"></i> {{ $todayPending ?? 0 }} pending
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="metric-card" style="--accent: #dc3545; --accent-light: #f86c6b;">
                                    <div class="metric-icon" style="background: linear-gradient(135deg, #dc3545, #f86c6b);">
                                        <i class="fas fa-boxes"></i>
                                    </div>
                                    <div class="metric-label">Inventory Items</div>
                                    <div class="metric-number text-danger">{{ $totalInventory ?? 0 }}</div>
                                    <div class="metric-change text-muted">
                                        <i class="fas fa-box me-1"></i> Total items in inventory
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="metric-card" style="--accent: #28a745; --accent-light: #5fcf80;">
                                    <div class="metric-icon" style="background: linear-gradient(135deg, #28a745, #5fcf80);">
                                        <i class="fas fa-heartbeat"></i>
                                    </div>
                                    <div class="metric-label">Total Appointments</div>
                                    <div class="metric-number text-success">{{ $totalAppointments ?? 0 }}</div>
                                    <div class="metric-change text-info">
                                        <i class="fas fa-hourglass-half me-1"></i> {{ $pendingAppointments ?? 0 }} pending
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
                                    <div class="activity-scroll">
                                    @php
                                        $recentCollection = collect($recentLogs ?? []);
                                    @endphp
                                    @if($recentCollection->count() > 0)
                                        @foreach($recentCollection as $log)
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
                                                <div class="fw-bold text-dark">{{ $log->user ? $log->user->name : 'System' }}</div>
                                                <div class="text-muted small">{{ $log->action }}</div>
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
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Palette and grid based on theme
        const isDark = document.body.classList.contains('bg-dark');
        const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.08)';
        const textColor = isDark ? '#e6e6e6' : '#2c3e50';
        const accentBlue = '#0d6efd';
        const accentTeal = '#20c997';
        const accentRed = '#dc3545';
        const accentGreen = '#28a745';
        const accentViolet = '#6f42c1';
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
                    borderColor: accentBlue,
                    backgroundColor: 'rgba(13,110,253,0.15)',
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 7,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.8,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor }
                    },
                    x: {
                        grid: { color: gridColor }
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
                    backgroundColor: [accentBlue, accentGreen, '#ffc107', accentRed, accentViolet],
                    hoverBackgroundColor: [
                        'rgba(13,110,253,0.9)', 'rgba(40,167,69,0.9)', 'rgba(255,193,7,0.9)', 'rgba(220,53,69,0.9)', 'rgba(111,66,193,0.9)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.8,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor }
                    },
                    x: {
                        grid: { color: gridColor }
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
                    backgroundColor: [accentBlue, accentGreen, '#ffc107', accentRed, accentViolet, '#17a2b8']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.0,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: textColor }
                    }
                }
            }
        });
    </script>
@endpush