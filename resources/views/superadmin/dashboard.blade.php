@extends('superadmin.layout')

@section('title', 'Dashboard - Barangay Health Center')
@section('page-title', 'System Overview')
@section('page-description', 'Strategic insights and system-wide analytics')

@section('page-styles')
    <style>
        /* Theme-aware text */
        body {
            color: #111;
        }

        body.bg-dark {
            color: #fff;
        }

        /* Dark mode styles for stats cards */
        body.bg-dark .stats-card {
            background: #1e2124;
            color: #e6e6e6;
        }

        body.bg-dark .stats-card:hover {
            background: #2a2f35;
        }

        body.bg-dark .chart-container {
            background: #1e2124;
            color: #e6e6e6;
        }

        body.bg-dark .stat-label {
            color: #cbd3da;
        }

        .stats-card {
            background: white;
            border-radius: 6px;
            padding: 0.75rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 0.5rem;
            height: 500px;
        }

        .chart-container canvas {
            max-height: 460px !important;
        }
    </style>
@endsection

@push('styles')
    <style>
        body.bg-dark .sidebar {
            background: #131516 !important;
            border-right-color: #2a2f35 !important;
        }
    </style>
@endpush

@section('content')
    <!-- Statistics Row -->
    <div class="row mb-2">
        <!-- System Statistics -->
        <div class="col-md-6">
            <div class="chart-container" style="height: 150px; padding: 0.75rem;">
                <h6 class="mb-2" style="font-size: 1rem;">System Statistics</h6>
                <div class="row justify-content-center">
                    <div class="col-3">
                        <div class="stats-card text-center">
                            <div class="stat-number text-primary">{{ $totalSystemUsers }}</div>
                            <div class="stat-label">Total Users</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="stats-card text-center">
                            <div class="stat-number text-success">{{ $totalPatients }}</div>
                            <div class="stat-label">Patients</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="stats-card text-center">
                            <div class="stat-number text-warning">{{ $totalAdmins }}</div>
                            <div class="stat-label">Admins</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="stats-card text-center">
                            <div class="stat-number text-danger">{{ $totalSuperAdmins }}</div>
                            <div class="stat-label">Super Admins</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Health Statistics -->
        <div class="col-md-6">
            <div class="chart-container" style="height: 150px; padding: 0.75rem;">
                <h6 class="mb-2" style="font-size: 1rem;">System Health</h6>
                <div class="row justify-content-center">
                    <div class="col-4">
                        <div class="stats-card text-center">
                            <div class="stat-number text-{{ $userGrowthRate >= 0 ? 'success' : 'danger' }}">
                                {{ $userGrowthRate > 0 ? '+' : '' }}{{ $userGrowthRate }}%</div>
                            <div class="stat-label">User Growth</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stats-card text-center">
                            <div class="stat-number text-{{ $lowStockItems > 0 ? 'warning' : 'success' }}">
                                {{ $lowStockItems }}</div>
                            <div class="stat-label">Low Stock</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stats-card text-center">
                            <div class="stat-number text-info" style="font-size: 0.9rem;">{{ $lastBackupTime }}</div>
                            <div class="stat-label">Last Backup</div>
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
                <h6 class="mb-2">User Growth Trend (Last 30 Days)</h6>
                <canvas id="userGrowthChart" height="460"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-container">
                <h6 class="mb-2">User Role Distribution</h6>
                <canvas id="roleDistributionChart" height="460"></canvas>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // User Growth Chart
        const userGrowthData = @json($userGrowthData);
        const growthLabels = userGrowthData.map(item => new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
        const growthCounts = userGrowthData.map(item => item.count);

        const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
        new Chart(userGrowthCtx, {
            type: 'bar',
            data: {
                labels: growthLabels,
                datasets: [{
                    label: 'New Users',
                    data: growthCounts,
                    backgroundColor: 'rgba(0, 159, 177, 0.8)',
                    borderColor: '#009fb1',
                    borderWidth: 2,
                    borderRadius: 8,
                    barThickness: 40
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
                            font: { size: 16 },
                            stepSize: 1
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        ticks: {
                            font: { size: 16 }
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            font: { size: 16 }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 14 },
                        bodyFont: { size: 14 }
                    }
                }
            }
        });

        // Role Distribution Chart
        const roleData = @json($roleDistribution);
        const roleDistributionCtx = document.getElementById('roleDistributionChart').getContext('2d');
        new Chart(roleDistributionCtx, {
            type: 'doughnut',
            data: {
                labels: roleData.map(item => item.role),
                datasets: [{
                    data: roleData.map(item => item.count),
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                    borderWidth: 0
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
                            font: { size: 16 },
                            padding: 25
                        }
                    }
                }
            }
        });
    </script>
@endpush