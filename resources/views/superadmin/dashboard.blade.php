@extends('superadmin.layout')

@section('title', 'Dashboard - Barangay Health Center')
@section('page-title', 'Dashboard')
@section('page-description', 'System overview and analytics')

@section('page-styles')
<style>
        /* Theme-aware text */
        body { color: #111; }
        body.bg-dark { color: #fff; }
        
        /* Admin Dashboard Metrics Styling */
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
        
        /* Dark mode for metrics */
        body.bg-dark .metric-card,
        body.bg-dark .chart-container {
            background: #1e2124;
            color: #e6e6e6;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            border-color: #2a2f35;
        }
        body.bg-dark .metric-number {
            color: inherit;
        }
        body.bg-dark .metric-label {
            color: #cbd3da;
        }
        body.bg-dark .metric-change,
        body.bg-dark .text-muted {
            color: #b0b0b0 !important;
        }
        body.bg-dark .metric-icon-pill {
            background: rgba(59,130,246,0.18);
            color: #bfdbfe;
        }
        body.bg-dark .metric-icon-pill.metric-icon-warning {
            background: rgba(251,191,36,0.28);
            color: #fef9c3;
        }
        body.bg-dark .metric-icon-pill.metric-icon-danger {
            background: rgba(248,113,113,0.25);
            color: #fee2e2;
        }
        body.bg-dark .metric-icon-pill.metric-icon-success {
            background: rgba(16,185,129,0.25);
            color: #a7f3d0;
        }

        /* Chart container styles */
        .chart-container {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
            height: 450px;
            position: relative;
            border: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
        }
        
        .chart-container h6 {
            font-weight: 700;
            font-size: 1rem;
            color: #374151;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            flex-shrink: 0;
        }
        
        .chart-container canvas {
            flex: 1;
            max-height: 100% !important;
            max-width: 100% !important;
        }
        
        /* Dark mode for charts */
        body.bg-dark .chart-container {
            background: #1e2124;
            border-color: #2a2f35;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        
        body.bg-dark .chart-container h6 {
            color: #e6e6e6;
        }
        
        /* Activity items */
        .activity-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.75rem;
            background: #f9fafb;
            transition: all 0.2s ease;
        }
        
        .activity-item:hover {
            background: #f3f4f6;
            transform: translateX(4px);
        }
        
        .activity-item:last-child {
            margin-bottom: 0;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-weight: 600;
            flex-shrink: 0;
            font-size: 0.875rem;
        }
        
        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-progress {
            background: #dbeafe;
            color: #1e40af;
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
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        /* Dark mode for activity items */
        body.bg-dark .activity-item {
            background: #2a2f35;
        }
        
        body.bg-dark .activity-item:hover {
            background: #303540;
        }
        
        body.bg-dark .activity-item .fw-bold {
            color: #e6e6e6 !important;
        }
        
        body.bg-dark .status-completed {
            background: rgba(16,185,129,0.2);
            color: #6ee7b7;
        }
        
        body.bg-dark .status-pending {
            background: rgba(251,191,36,0.2);
            color: #fde68a;
        }
        
        body.bg-dark .status-progress {
            background: rgba(59,130,246,0.2);
            color: #93c5fd;
        }
    </style>
@endsection

@push('styles')
<style>
    /* Sidebar dark mode override - loaded AFTER layout styles */
    body.bg-dark .sidebar { 
        background: #131516 !important; 
        border-right-color: #2a2f35 !important; 
    }
</style>
@endpush

@section('content')
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
                    <div class="metric-change text-primary">
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
                    <div class="metric-label">Services This Month</div>
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
            <h6>Dashboard Overview</h6>
            <canvas id="overviewChart"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="chart-container">
            <h6>Service this Month</h6>
            <canvas id="serviceChart"></canvas>
        </div>
    </div>
</div>

<!-- Bottom Section -->
<div class="row">
    <div class="col-md-6">
        <div class="chart-container">
            <h6>Patients by Barangay</h6>
            <canvas id="barangayChart"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="chart-container">
            <h6>Recent Activity</h6>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        // Palette and grid based on theme
        const isDark = document.body.classList.contains('bg-dark');
        const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.08)';
        const textColor = isDark ? '#e6e6e6' : '#374151';
        const accentBlue = '#3b82f6';
        const accentTeal = '#14b8a6';
        const accentRed = '#ef4444';
        const accentGreen = '#10b981';
        const accentViolet = '#8b5cf6';
        
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
                    backgroundColor: isDark ? 'rgba(59,130,246,0.1)' : 'rgba(59,130,246,0.15)',
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: textColor }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: { color: textColor }
                    },
                    x: {
                        grid: { color: gridColor },
                        ticks: { color: textColor }
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
                    backgroundColor: [accentBlue, accentGreen, '#f59e0b', accentRed, accentViolet],
                    hoverBackgroundColor: [
                        'rgba(59,130,246,0.9)', 'rgba(16,185,129,0.9)', 'rgba(245,158,11,0.9)', 'rgba(239,68,68,0.9)', 'rgba(139,92,246,0.9)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: textColor }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: { color: textColor }
                    },
                    x: {
                        grid: { color: gridColor },
                        ticks: { color: textColor }
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
                    backgroundColor: [accentBlue, accentGreen, '#f59e0b', accentRed, accentViolet, '#14b8a6']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: textColor }
                    }
                }
            }
        });
        });
    </script>
@endpush