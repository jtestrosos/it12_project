@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h2 class="fw-bold mb-4">System Logs</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($logs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Table</th>
                                        <th>Record ID</th>
                                        <th>IP Address</th>
                                        <th>Timestamp</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($logs as $log)
                                    <tr>
                                        <td>{{ $log->user ? $log->user->name : 'System' }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($log->action == 'created') bg-success
                                                @elseif($log->action == 'updated') bg-warning
                                                @elseif($log->action == 'deleted') bg-danger
                                                @else bg-info
                                                @endif">
                                                {{ ucfirst($log->action) }}
                                            </span>
                                        </td>
                                        <td>{{ $log->table_name ?? 'N/A' }}</td>
                                        <td>{{ $log->record_id ?? 'N/A' }}</td>
                                        <td>{{ $log->ip_address ?? 'N/A' }}</td>
                                        <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#logDetailsModal{{ $log->id }}">
                                                View Details
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $logs->links() }}
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">No system logs found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Log Details Modals -->
@foreach($logs as $log)
<div class="modal fade" id="logDetailsModal{{ $log->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Basic Information</h6>
                        <p><strong>User:</strong> {{ $log->user ? $log->user->name : 'System' }}</p>
                        <p><strong>Action:</strong> {{ ucfirst($log->action) }}</p>
                        <p><strong>Table:</strong> {{ $log->table_name ?? 'N/A' }}</p>
                        <p><strong>Record ID:</strong> {{ $log->record_id ?? 'N/A' }}</p>
                        <p><strong>IP Address:</strong> {{ $log->ip_address ?? 'N/A' }}</p>
                        <p><strong>Timestamp:</strong> {{ $log->created_at->format('M d, Y H:i:s') }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Change Details</h6>
                        @if($log->old_values)
                            <p><strong>Old Values:</strong></p>
                            <pre class="bg-light p-2 small">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                        @endif
                        @if($log->new_values)
                            <p><strong>New Values:</strong></p>
                            <pre class="bg-light p-2 small">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
