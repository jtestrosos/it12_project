@extends('superadmin.layout')

@section('title', 'Analytics - Barangay Health Center')
@section('page-title', 'Analytics')
@section('page-description', 'System usage and performance metrics')

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

        <!-- User Statistics -->
        <div class="col-md-6">
            <div class="chart-container" style="height: 150px; padding: 0.75rem;">
                <h6 class="mb-2" style="font-size: 1rem;">User Statistics</h6>
                <div class="row justify-content-center">
                    <div class="col-3">
                        <div class="stats-card text-center">
                            <div class="stat-number text-primary">{{ $userStats['total'] ?? 0 }}</div>
                            <div class="stat-label">Total Users</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="stats-card text-center">
                            <div class="stat-number text-success">{{ $userStats['patients'] ?? 0 }}</div>
                            <div class="stat-label">Patients</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="stats-card text-center">
                            <div class="stat-number text-warning">{{ $userStats['admins'] ?? 0 }}</div>
                            <div class="stat-label">Admins</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="stats-card text-center">
                            <div class="stat-number text-danger">{{ $userStats['this_month'] ?? 0 }}</div>
                            <div class="stat-label">This Month</div>
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
                <h6 class="mb-2">Appointments Trend (Last 6 Months)</h6>
                <canvas id="appointmentsTrendChart" height="460"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-container">
                <h6 class="mb-2">User Distribution</h6>
                <canvas id="userDistributionChart" height="460"></canvas>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Get data from Laravel
        const monthlyData = @json($monthlyTrend ?? []);

        // Appointments Trend Chart
        const appointmentsTrendCtx = document.getElementById('appointmentsTrendChart').getContext('2d');

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

        new Chart(appointmentsTrendCtx, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Appointments',
                    data: trendCounts,
                    borderColor: '#009fb1',
                    backgroundColor: 'rgba(0, 159, 177, 0.1)',
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

        // User Distribution Chart
        const userDistributionCtx = document.getElementById('userDistributionChart').getContext('2d');
        new Chart(userDistributionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Patients', 'Admins'],
                datasets: [{
                    data: [{{ $userStats['patients'] ?? 0 }}, {{ $userStats['admins'] ?? 0 }}],
                    backgroundColor: ['#28a745', '#ffc107'],
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
                            font: {
                                size: 16
                            },
                            padding: 25
                        }
                    }
                }
            }
        });
    </script>
@endpush