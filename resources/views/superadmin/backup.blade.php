@extends('superadmin.layout')

@section('title', 'Backup - Barangay Health Center')
@section('page-title', 'Backup & Restore')
@section('page-description', 'Manage system backups and data restoration')

@section('page-styles')
<style>
        /* Dark mode styles for backup cards */
        body.bg-dark .backup-card { background: #1e2124; color: #e6e6e6; }
        body.bg-dark .backup-card:hover { background: #2a2f35; }
        .backup-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
            border: none;
            transition: transform 0.2s ease;
        }
        body.bg-dark .backup-card {
            background: #1b1e20;
            border: 1px solid #2a2f35;
            color: #e6e6e6;
        }
        .backup-card:hover {
            transform: translateY(-2px);
        }
        .backup-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .backup-success {
            background-color: #d4edda;
            color: #155724;
        }
        body.bg-dark .backup-success {
            background-color: #1e3a28;
            color: #4ade80;
        }
        .backup-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        body.bg-dark .backup-warning {
            background-color: #3a3a28;
            color: #fbbf24;
        }
        .backup-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        body.bg-dark .backup-danger {
            background-color: #3a2828;
            color: #f87171;
        }
        .backup-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        body.bg-dark .backup-info {
            background-color: #283a3a;
            color: #67e8f9;
        }
        .progress-bar {
            height: 8px;
            border-radius: 4px;
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
                        <!-- Backup Actions -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="backup-card">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="backup-icon backup-info me-3">
                                            <i class="fas fa-database"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1">Database Backup</h5>
                                            <p class="text-muted mb-0">Create a complete backup of the database</p>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary" onclick="createBackup('database', this)">
                                            <i class="fas fa-download me-2"></i> Backup Database
                                        </button>
                                        <button class="btn btn-outline-info" onclick="scheduleBackup('database')">
                                            <i class="fas fa-clock me-2"></i> Schedule
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="backup-card">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="backup-icon backup-warning me-3">
                                            <i class="fas fa-file-archive"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1">File System Backup</h5>
                                            <p class="text-muted mb-0">Backup uploaded files and documents</p>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-warning" onclick="createBackup('files', this)">
                                            <i class="fas fa-download me-2"></i> Backup Files
                                        </button>
                                        <button class="btn btn-outline-warning" onclick="scheduleBackup('files')">
                                            <i class="fas fa-clock me-2"></i> Schedule
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Full System Backup -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="backup-card">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="backup-icon backup-danger me-3">
                                            <i class="fas fa-server"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1">Full System Backup</h5>
                                            <p class="text-muted mb-0">Create a complete backup of the entire system (Database + Files + Configuration)</p>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-danger" onclick="createBackup('full', this)">
                                            <i class="fas fa-download me-2"></i> Full System Backup
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="scheduleBackup('full')">
                                            <i class="fas fa-clock me-2"></i> Schedule Daily
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Backup Status -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="backup-card">
                                    <h5 class="mb-3">
                                        <i class="fas fa-info-circle me-2"></i> Backup Status
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                @if($lastDatabase)
                                                <div class="backup-icon backup-success mx-auto mb-2">
                                                    <i class="fas fa-check"></i>
                                                </div>
                                                <h6>Last Database Backup</h6>
                                                <small class="text-muted">{{ $lastDatabase->completed_at->diffForHumans() }}</small>
                                                @else
                                                <div class="backup-icon backup-warning mx-auto mb-2">
                                                    <i class="fas fa-exclamation"></i>
                                                </div>
                                                <h6>Last Database Backup</h6>
                                                <small class="text-muted">Never</small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                @if($lastFiles)
                                                <div class="backup-icon backup-success mx-auto mb-2">
                                                    <i class="fas fa-check"></i>
                                                </div>
                                                <h6>Last File Backup</h6>
                                                <small class="text-muted">{{ $lastFiles->completed_at->diffForHumans() }}</small>
                                                @else
                                                <div class="backup-icon backup-warning mx-auto mb-2">
                                                    <i class="fas fa-exclamation"></i>
                                                </div>
                                                <h6>Last File Backup</h6>
                                                <small class="text-muted">Never</small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <div class="backup-icon backup-info mx-auto mb-2">
                                                    <i class="fas fa-clock"></i>
                                                </div>
                                                <h6>Next Scheduled</h6>
                                                <small class="text-muted">Tomorrow 2:00 AM</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <div class="backup-icon backup-success mx-auto mb-2">
                                                    <i class="fas fa-hdd"></i>
                                                </div>
                                                <h6>Storage Used</h6>
                                                <small class="text-muted">{{ $storageUsed }} / {{ $storageTotal }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Backups -->
                        <div class="row">
                            <div class="col-12">
                                <div class="backup-card">
                                    <h5 class="mb-3">
                                        <i class="fas fa-history me-2"></i> Recent Backups
                                    </h5>
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Date</th>
                                                    <th>Size</th>
                                                    <th>Status</th>
                                                    <th>Created By</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($backups as $backup)
                                                <tr>
                                                    <td>
                                                        @if($backup->type == 'database')
                                                            <span class="badge bg-primary">Database</span>
                                                        @elseif($backup->type == 'files')
                                                            <span class="badge bg-warning">Files</span>
                                                        @else
                                                            <span class="badge bg-danger">Full System</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $backup->created_at->format('M d, Y h:i A') }}</td>
                                                    <td>{{ $backup->size }}</td>
                                                    <td>
                                                        @if($backup->status == 'completed')
                                                            <span class="badge bg-success">Completed</span>
                                                        @elseif($backup->status == 'in_progress')
                                                            <span class="badge bg-warning">In Progress</span>
                                                        @else
                                                            <span class="badge bg-danger">Failed</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $backup->createdBy->name ?? 'System' }}</td>
                                                    <td>
                                                        @if($backup->status == 'completed')
                                                        <a href="{{ route('superadmin.backup.download', $backup) }}" class="btn btn-outline-primary btn-sm me-2">
                                                            <i class="fas fa-download me-1"></i> Download
                                                        </a>
                                                        @endif
                                                        <form action="{{ route('superadmin.backup.delete', $backup) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this backup?')">
                                                                <i class="fas fa-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <i class="fas fa-history fa-2x text-muted mb-2"></i>
                                                        <p class="text-muted mb-0">No backups found. Create your first backup above!</p>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    @if($backups->hasPages())
                                    <div class="mt-3">
                                        {{ $backups->links() }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
@endsection

@push('scripts')
    <script>
        function createBackup(type, buttonElement) {
            if (confirm(`Are you sure you want to create a ${type} backup?`)) {
                const button = buttonElement || event.target;
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Creating...';
                button.disabled = true;

                fetch('{{ route("superadmin.backup.create") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ type: type })
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        return response.json().then(data => Promise.reject(data));
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Backup response:', data);
                    if (data.success) {
                        alert(data.message || `${type.charAt(0).toUpperCase() + type.slice(1)} backup completed successfully!`);
                        location.reload();
                    } else {
                        alert(data.message || 'Backup failed!');
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Backup error:', error);
                    alert('Error creating backup: ' + (error.message || JSON.stringify(error)));
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
            }
        }

        function scheduleBackup(type) {
            const schedule = prompt(`Enter schedule for ${type} backup (e.g., "daily", "weekly", "monthly"):`);
            if (schedule) {
                fetch('{{ route("superadmin.backup.schedule") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ 
                        type: type, 
                        schedule: schedule 
                    })
                })
                .then(response => response.json())
                .then(data => {
                    alert(`${type.charAt(0).toUpperCase() + type.slice(1)} backup scheduled for ${schedule}!`);
                })
                .catch(error => {
                    alert('Error scheduling backup: ' + error);
                });
            }
        }
    </script>
@endpush
