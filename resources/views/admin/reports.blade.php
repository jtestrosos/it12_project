@extends('admin.layout')

@section('title', 'Services & Reports - Barangay Health Center')
@section('page-title', 'Services & Reports')
@section('page-description', 'Analytics and reporting dashboard')

@section('page-styles')
<style>
        body { color: inherit; }
        .stats-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 0.85rem;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
            margin-bottom: 0.75rem;
            border: 1px solid #edf1f7;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }
        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.10);
            border-color: #d0e2ff;
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
            background: #ffffff;
            border-radius: 14px;
            padding: 1rem;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
            margin-bottom: 0.75rem;
            height: 500px;
            border: 1px solid #edf1f7;
        }
        .chart-container canvas {
            max-height: 460px !important;
        }
        /* Ensure cards inherit theme text color */
        .stats-card, .chart-container { color: inherit; }
        .stat-number { color: inherit; }

        /* Dark mode surfaces */
        body.bg-dark .main-content { background-color: #151718; }
        body.bg-dark .sidebar { background: #131516; border-right-color: #2a2f35; }
        body.bg-dark .header { background: #1b1e20; border-bottom-color: #2a2f35; }
        body.bg-dark .stats-card, body.bg-dark .chart-container { background: #1e2124; color: #e6e6e6; box-shadow: 0 2px 8px rgba(0,0,0,0.3); }
        body.bg-dark .table thead { background: #1a1f24; color: #e6e6e6; }
        /* Headings and muted text visibility */
        h1, h2, h3, h4, h5, h6 { color: inherit; }
        body.bg-dark .text-muted, body.bg-dark small, body.bg-dark .stat-label { color: #b0b0b0 !important; }
    </style>
@endsection

@section('content')
                    <div class="p-0 p-md-2">
                        <div class="d-flex flex-wrap justify-content-end align-items-end mb-3 gap-2">
                            <form class="d-flex align-items-end gap-2 flex-wrap" method="GET" action="{{ route('admin.reports.export.appointments') }}">
                                <div>
                                    <label class="form-label mb-1">From</label>
                                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" required>
                                </div>
                                <div>
                                    <label class="form-label mb-1">To</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" required>
                                </div>
                                <div class="mb-0 d-flex gap-2">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-file-excel me-2"></i> Export Excel
                                    </button>
                                    <button type="submit" class="btn btn-danger" formaction="{{ route('admin.reports.export.appointments.pdf') }}">
                                        <i class="fas fa-file-pdf me-2"></i> Export PDF
                                    </button>
                                </div>
                            </form>
                        </div>
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
@endpush