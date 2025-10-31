@extends('superadmin.layout')

@section('title', 'System Logs - Barangay Health Center')
@section('page-title', 'System Logs')
@section('page-description', 'View and manage system activity logs')

@section('page-styles')
<style>
    .pagination {
        margin-bottom: 0;
    }
    .pagination .page-link {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
    }
    /* Hide Prev/Next buttons (keep only page numbers) */
    .pagination .page-item:first-child,
    .pagination .page-item:last-child {
        display: none !important;
    }
</style>
@endsection

@section('content')
<!-- Filters -->
<div class="filter-card">
    <form method="GET" action="{{ route('superadmin.system-logs') }}" class="row g-3">
        <div class="col-md-3">
            <label class="form-label text-muted small">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label text-muted small">Action</label>
            <select name="action" class="form-select">
                <option value="">All</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label text-muted small">Table</label>
            <select name="table" class="form-select">
                <option value="">All</option>
                @foreach($tables as $table)
                    <option value="{{ $table }}" {{ request('table') == $table ? 'selected' : '' }}>{{ $table }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label text-muted small">From Date</label>
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label text-muted small">To Date</label>
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>
</div>

<!-- Table -->
<div class="table-card">
    @if($logs->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Table</th>
                    <th>Record ID</th>
                    <th>Date</th>
                    <th>IP Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td>{{ $log->id }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                {{ substr($log->user ? $log->user->name : 'S', 0, 1) }}
                            </div>
                            {{ $log->user ? $log->user->name : 'System' }}
                        </div>
                    </td>
                    <td>
                        @if($log->action == 'created')
                            <span class="badge bg-success">{{ ucfirst($log->action) }}</span>
                        @elseif($log->action == 'updated')
                            <span class="badge bg-warning text-dark">{{ ucfirst($log->action) }}</span>
                        @elseif($log->action == 'deleted')
                            <span class="badge bg-danger">{{ ucfirst($log->action) }}</span>
                        @else
                            <span class="badge bg-info">{{ ucfirst($log->action) }}</span>
                        @endif
                    </td>
                    <td>{{ $log->table_name ?? 'N/A' }}</td>
                    <td>{{ $log->record_id ?? 'N/A' }}</td>
                    <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                    <td>{{ $log->ip_address ?? 'N/A' }}</td>
                    <td>
                        <button 
                            class="btn btn-outline-primary btn-sm" 
                            data-bs-toggle="modal" 
                            data-bs-target="#logDetailsModal"
                            data-user="{{ $log->user ? $log->user->name : 'System' }}"
                            data-action="{{ ucfirst($log->action) }}"
                            data-table="{{ $log->table_name ?? 'N/A' }}"
                            data-record="{{ $log->record_id ?? 'N/A' }}"
                            data-old='@json($log->old_values)'
                            data-new='@json($log->new_values)'
                            data-ip="{{ $log->ip_address ?? 'N/A' }}"
                            data-timestamp="{{ $log->created_at->format('F d, Y \a\t g:i A') }}"
                        >
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-3 d-flex justify-content-center">
        {{ $logs->withQueryString()->links() }}
    </div>
    @else
    <div class="text-center py-5">
        <i class="fas fa-list fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">No system logs found</h5>
        <p class="text-muted">There are no system logs matching your criteria.</p>
    </div>
    @endif
</div>

<!-- Log Details Modal (single, dynamic) -->
<div class="modal fade" id="logDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Basic Information</h6>
                        <div class="mb-3">
                            <label class="form-label text-muted">User</label>
                            <p class="fw-bold" id="log-user">&nbsp;</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Action</label>
                            <p id="log-action">&nbsp;</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Table</label>
                            <p id="log-table">&nbsp;</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Record ID</label>
                            <p id="log-record">&nbsp;</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Change Details</h6>
                        <div class="mb-3" id="log-old-wrapper" style="display:none;">
                            <strong class="text-dark">Old Values:</strong>
                            <pre class="bg-light p-3 small rounded" style="border: 1px solid #e9ecef;" id="log-old"></pre>
                        </div>
                        <div class="mb-3" id="log-new-wrapper" style="display:none;">
                            <strong class="text-dark">New Values:</strong>
                            <pre class="bg-light p-3 small rounded" style="border: 1px solid #e9ecef;" id="log-new"></pre>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">IP Address</label>
                            <p id="log-ip">&nbsp;</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Timestamp</label>
                            <p id="log-timestamp">&nbsp;</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('show.bs.modal', function (event) {
            const modal = document.getElementById('logDetailsModal');
            if (!modal || event.target !== modal) return;
            const trigger = event.relatedTarget;
            if (!trigger) return;

            const get = (key) => trigger.getAttribute('data-' + key) || '';

            const user = get('user');
            const action = get('action');
            const tableName = get('table');
            const recordId = get('record');
            const ip = get('ip');
            const timestamp = get('timestamp');
            const oldRaw = get('old');
            const newRaw = get('new');

            const setText = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.textContent = value || 'N/A';
            };

            setText('log-user', user);
            setText('log-action', action);
            setText('log-table', tableName);
            setText('log-record', recordId);
            setText('log-ip', ip);
            setText('log-timestamp', timestamp);

            const oldWrapper = document.getElementById('log-old-wrapper');
            const newWrapper = document.getElementById('log-new-wrapper');
            const oldPre = document.getElementById('log-old');
            const newPre = document.getElementById('log-new');

            try {
                if (oldRaw && oldRaw !== 'null') {
                    oldWrapper.style.display = '';
                    oldPre.textContent = JSON.stringify(JSON.parse(oldRaw), null, 2);
                } else {
                    oldWrapper.style.display = 'none';
                    oldPre.textContent = '';
                }
            } catch (e) {
                oldWrapper.style.display = '';
                oldPre.textContent = oldRaw;
            }

            try {
                if (newRaw && newRaw !== 'null') {
                    newWrapper.style.display = '';
                    newPre.textContent = JSON.stringify(JSON.parse(newRaw), null, 2);
                } else {
                    newWrapper.style.display = 'none';
                    newPre.textContent = '';
                }
            } catch (e) {
                newWrapper.style.display = '';
                newPre.textContent = newRaw;
            }
        });
    </script>
@endpush
