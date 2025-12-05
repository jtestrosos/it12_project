@extends('superadmin.layout')

@section('title', 'System Logs - Barangay Health Center')
@section('page-title', 'System Logs')
@section('page-description', 'View system activity and audit logs')

@section('page-styles')
    <style>
        /* Filter card and table card - dark mode */
        .filter-card,
        .table-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
            border: 1px solid #e9ecef;
        }

        body.bg-dark .filter-card,
        body.bg-dark .table-card {
            background: #1e2124;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            border-color: #2a2f35;
            color: #e6e6e6;
        }

        /* Table styles - clean and theme-aware */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            padding: 1rem;
            background: transparent;
        }

        .table.table-dark thead th,
        body.bg-dark .table thead th {
            border-bottom-color: #2a2f35;
            color: #e6e6e6;
            background: transparent !important;
        }

        .table tbody tr {
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table.table-dark tbody tr,
        body.bg-dark .table tbody tr {
            border-bottom-color: #2a2f35;
        }

        .table.table-dark tbody tr:hover,
        body.bg-dark .table tbody tr:hover {
            background-color: #2a2f35;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .table.table-dark tbody td,
        body.bg-dark .table tbody td {
            color: #e6e6e6;
        }

        /* Empty state */
        body.bg-dark .text-muted {
            color: #b0b0b0 !important;
        }

    /* Modal dark mode */
    body.bg-dark .modal-content {
        background: #1e2124;
        color: #e6e6e6;
        border-color: #2a2f35;
    }
    body.bg-dark .modal-header {
        border-bottom-color: #2a2f35;
    }
    body.bg-dark .modal-footer {
        border-top-color: #2a2f35;
    }
    body.bg-dark .modal-body pre {
        background: #0f1316 !important;
        color: #e6e6e6 !important;
        border-color: #2a2f35 !important;
    }
    body.bg-dark .modal-body strong {
        color: #e6e6e6;
    }

    /* Dark mode for form controls */
    body.bg-dark .form-control,
    body.bg-dark .form-select {
        background-color: #1e2124;
        border-color: #2a2f35;
        color: #e6e6e6;
    }
    
    body.bg-dark .form-control:focus,
    body.bg-dark .form-select:focus {
        background-color: #2a2f35;
        border-color: #007bff;
        color: #e6e6e6;
    }
    
    body.bg-dark .form-control::placeholder {
        color: #6c757d;
    }
    
    body.bg-dark .form-label {
        color: #b0b0b0;
    }
    
    /* Hide Bootstrap pagination's built-in "Showing" text on the left */
    nav[role="navigation"] p,
    nav[role="navigation"] .text-sm {
        display: none !important;
    }
    
    /* Bring showing text closer to pagination */
    #logsPaginationContainer > div:last-child {
        margin-top: -0.5rem !important;
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
    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('superadmin.system-logs') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label text-muted small">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search..."
                    value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small">Action</label>
                <select name="action" class="form-select">
                    <option value="">All</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}
                        </option>
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
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Table</th>
                            <th>Record ID</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>IP Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            @php
                                $formattedUserId = 'N/A';
                                if ($log->user) {
                                    $role = $log->user->role;
                                    $userId = $log->user->id;
                                    if ($role === 'user') {
                                        $formattedUserId = 'PA' . str_pad($userId, 3, '0', STR_PAD_LEFT);
                                    } elseif ($role === 'admin') {
                                        $formattedUserId = 'BHW' . str_pad($userId, 3, '0', STR_PAD_LEFT);
                                    } elseif ($role === 'superadmin') {
                                        $formattedUserId = 'DEV' . str_pad($userId, 3, '0', STR_PAD_LEFT);
                                    } else {
                                        $formattedUserId = 'USR' . str_pad($userId, 3, '0', STR_PAD_LEFT);
                                    }
                                }
                            @endphp
                            <tr>
                                <td>{{ $formattedUserId }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                            style="width: 32px; height: 32px; font-size: 0.8rem;">
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
                                <td>
                                    @if($log->status == 'active')
                                        <span class="badge bg-success">{{ ucfirst($log->status) }}</span>
                                    @elseif($log->status == 'inactive')
                                        <span class="badge bg-secondary">{{ ucfirst($log->status) }}</span>
                                    @elseif($log->status == 'archived')
                                        <span class="badge bg-warning text-dark">{{ ucfirst($log->status) }}</span>
                                    @else
                                        <span class="badge bg-info">{{ ucfirst($log->status ?? 'active') }}</span>
                                    @endif
                                </td>
                                <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                                <td>{{ $log->ip_address ?? 'N/A' }}</td>
                                <td>
                                    <button
                                        class="btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#logDetailsModal"
                                        data-user="{{ $log->user ? $log->user->name : 'System' }}"
                                        data-action="{{ ucfirst($log->action) }}"
                                        data-table="{{ $log->table_name ?? 'N/A' }}"
                                        data-record="{{ $log->record_id ?? 'N/A' }}"
                                        data-status="{{ ucfirst($log->status ?? 'active') }}"
                                        data-old='@json($log->old_values)'
                                        data-new='@json($log->new_values)'
                                        data-ip="{{ $log->ip_address ?? 'N/A' }}"
                                        data-timestamp="{{ $log->created_at->format('F d, Y \a\t g:i A') }}"
                                        title="View Details"
                                    >
                                        <i class="fas fa-eye text-info"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($logs->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            {{-- Previous Page Link --}}
                            @if ($logs->onFirstPage())
                                <li class="page-item disabled" aria-disabled="true">
                                    <span class="page-link">&lsaquo;</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $logs->previousPageUrl() }}">&lsaquo;</a>
                                </li>
                            @endif

                            {{-- First page --}}
                            @if ($logs->currentPage() > 3)
                                <li class="page-item">
                                    <a class="page-link" href="{{ $logs->url(1) }}">1</a>
                                </li>
                                @if ($logs->currentPage() > 4)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                            @endif

                            {{-- Page numbers --}}
                            @for ($page = max(1, $logs->currentPage() - 2); $page <= min($logs->lastPage(), $logs->currentPage() + 2); $page++)
                                @if ($page == $logs->currentPage())
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $logs->url($page) }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endfor

                            {{-- Last page --}}
                            @if ($logs->currentPage() < $logs->lastPage() - 2)
                                @if ($logs->currentPage() < $logs->lastPage() - 3)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                                <li class="page-item">
                                    <a class="page-link" href="{{ $logs->url($logs->lastPage()) }}">{{ $logs->lastPage() }}</a>
                                </li>
                            @endif

                            {{-- Next Page Link --}}
                            @if ($logs->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $logs->nextPageUrl() }}">&rsaquo;</a>
                                </li>
                            @else
                                <li class="page-item disabled" aria-disabled="true">
                                    <span class="page-link">&rsaquo;</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            @endif
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
                            <div class="mb-3">
                                <label class="form-label text-muted">Status</label>
                                <p id="log-status">&nbsp;</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Change Details</h6>
                            <div class="mb-3" id="log-old-wrapper" style="display:none;">
                                <strong class="text-dark">Old Values:</strong>
                                <pre class="bg-light p-3 small rounded" style="border: 1px solid #e9ecef;"
                                    id="log-old"></pre>
                            </div>
                            <div class="mb-3" id="log-new-wrapper" style="display:none;">
                                <strong class="text-dark">New Values:</strong>
                                <pre class="bg-light p-3 small rounded" style="border: 1px solid #e9ecef;"
                                    id="log-new"></pre>
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
        document.addEventListener('DOMContentLoaded', () => {
            // Sync table with dark mode on page load
            const syncTableDark = () => {
                const isDark = document.body.classList.contains('bg-dark');
                const table = document.querySelector('.table');
                if (table) {
                    table.classList.toggle('table-dark', isDark);
                }
            };

            // Sync on load
            syncTableDark();

            // Watch for theme changes
            const observer = new MutationObserver(() => {
                syncTableDark();
            });
            observer.observe(document.body, {
                attributes: true,
                attributeFilter: ['class']
            });
        });

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
            const status = get('status');
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
            setText('log-status', status);
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