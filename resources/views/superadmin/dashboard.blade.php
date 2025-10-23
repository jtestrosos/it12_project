@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h2 class="fw-bold mb-4">Super Admin Dashboard</h2>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <h3>{{ $totalUsers }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Admins</h5>
                    <h3>{{ $totalAdmins }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Patients</h5>
                    <h3>{{ $totalPatients }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Appointments</h5>
                    <h3>{{ $totalAppointments }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <h5 class="card-title">Inventory</h5>
                    <h3>{{ $totalInventory }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5 class="card-title">System Logs</h5>
                    <h3>{{ $recentLogs->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('superadmin.users') }}" class="btn btn-primary me-2">
                        <i class="fas fa-users"></i> Manage Users
                    </a>
                    <a href="{{ route('superadmin.system-logs') }}" class="btn btn-info me-2">
                        <i class="fas fa-list"></i> System Logs
                    </a>
                    <a href="{{ route('superadmin.analytics') }}" class="btn btn-success me-2">
                        <i class="fas fa-chart-bar"></i> Analytics
                    </a>
                    <button class="btn btn-warning" onclick="backup()">
                        <i class="fas fa-download"></i> Backup System
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent System Logs -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent System Logs</h5>
                </div>
                <div class="card-body">
                    @if($recentLogs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentLogs as $log)
                                    <tr>
                                        <td>{{ $log->user ? $log->user->name : 'System' }}</td>
                                        <td>{{ $log->action }}</td>
                                        <td>{{ $log->created_at->format('M d, H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No recent logs.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Users</h5>
                </div>
                <div class="card-body">
                    @if($recentUsers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentUsers as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($user->role == 'superadmin') bg-danger
                                                @elseif($user->role == 'admin') bg-warning
                                                @else bg-primary
                                                @endif">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td>{{ $user->created_at->format('M d') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No recent users.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function backup() {
    if (confirm('Are you sure you want to initiate a system backup?')) {
        fetch('{{ route("superadmin.backup") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert('Backup process initiated successfully!');
        })
        .catch(error => {
            alert('Error initiating backup: ' + error);
        });
    }
}
</script>
@endsection
