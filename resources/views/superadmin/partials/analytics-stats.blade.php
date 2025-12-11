<div class="row mb-2">
    <!-- Appointment Statistics -->
    <div class="col-md-6">
        <div class="chart-container" style="height: auto; min-height: auto; padding: 0.75rem;">
            <h6 class="mb-2" style="font-size: 1rem;">Appointment Statistics</h6>
            <div class="row g-2">
                <div class="col">
                    <div class="stats-card text-center">
                        <div class="stat-number text-primary">{{ $appointmentStats['total'] ?? 0 }}</div>
                        <div class="stat-label">Total</div>
                    </div>
                </div>
                <div class="col">
                    <div class="stats-card text-center">
                        <div class="stat-number text-warning">{{ $appointmentStats['pending'] ?? 0 }}</div>
                        <div class="stat-label">Pending</div>
                    </div>
                </div>
                <div class="col">
                    <div class="stats-card text-center">
                        <div class="stat-number text-success">{{ $appointmentStats['approved'] ?? 0 }}</div>
                        <div class="stat-label">Approved</div>
                    </div>
                </div>
                <div class="col">
                    <div class="stats-card text-center">
                        <div class="stat-number text-info">{{ $appointmentStats['completed'] ?? 0 }}</div>
                        <div class="stat-label">Completed</div>
                    </div>
                </div>
                <div class="col">
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
        <div class="chart-container" style="height: auto; min-height: auto; padding: 0.75rem;">
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
