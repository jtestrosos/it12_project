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
                                <td>{{ $backup->creator->name ?? 'System' }}</td>
                                <td>
<div class="d-flex gap-1">
    @if($backup->status == 'completed')
        <a href="{{ route('superadmin.backup.download', $backup) }}"
            class="btn btn-sm btn-outline-secondary" title="Download">
            <i class="fas fa-download text-primary"></i>
        </a>
    @endif
    <form action="{{ route('superadmin.backup.delete', $backup) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-secondary"
            onclick="return confirm('Are you sure you want to delete this backup?')"
            title="Delete">
            <i class="fas fa-trash text-danger"></i>
        </button>
    </form>
</div>
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
        </div>
    </div>
</div>
