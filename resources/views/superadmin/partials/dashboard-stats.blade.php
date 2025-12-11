<div class="row mb-2">
    <!-- System Statistics -->
    <div class="col-md-6">
        <div class="chart-container" style="height: auto; min-height: auto; padding: 0.75rem;">
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
        <div class="chart-container" style="height: auto; min-height: auto; padding: 0.75rem;">
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
