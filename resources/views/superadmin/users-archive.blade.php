@extends('superadmin.layout')

@section('title', 'Archived Users - Barangay Health Center')
@section('page-title', 'Archived Users')
@section('page-description', 'View and manage archived user accounts')

@section('page-styles')
<style>
        html body.bg-dark [class*="admin-sidebar"], html body.bg-dark [class*="sidebar"] { background: #131516 !important; border-right-color: #2a2f35 !important; }
</style>
@endsection

@section('content')
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <a href="{{ route('superadmin.users') }}" class="btn btn-outline-secondary d-flex align-items-center">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    <span>Back to Users</span>
                                </a>
                            </div>
                            <div class="text-muted small">
                                Archived users cannot log in until they are restored.
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body p-0">
                                @if($users->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>User</th>
                                                    <th>Email</th>
                                                    <th class="text-center">Role</th>
                                                    <th>Archived At</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($users as $user)
                                                <tr>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td class="text-center">{{ ucfirst($user->role === 'user' ? 'patient' : $user->role) }}</td>
                                                    <td>{{ optional($user->deleted_at)->format('M d, Y g:i A') }}</td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <form method="POST" action="{{ route('superadmin.user.restore', $user->id) }}">
                                                                @csrf
                                                                <button type="button" class="btn btn-outline-success btn-sm d-flex align-items-center action-btn" data-confirm data-confirm-title="Restore User" data-confirm-message="Are you sure you want to restore this user?">
                                                                    <i class="fas fa-undo me-1"></i>
                                                                    <span>Restore</span>
                                                                </button>
                                                            </form>
                                                            <form method="POST" action="{{ route('superadmin.user.force-delete', $user->id) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button" class="btn btn-outline-danger btn-sm d-flex align-items-center action-btn" data-confirm data-confirm-title="Delete User" data-confirm-message="Permanently delete this user? This cannot be undone.">
                                                                    <i class="fas fa-trash me-1"></i>
                                                                    <span>Delete</span>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center p-3 border-top">
                                        <div class="text-muted">
                                            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} archived users
                                        </div>
                                        <div>
                                            {{ $users->withQueryString()->links() }}
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="fas fa-archive fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No archived users</h5>
                                        <p class="text-muted">Archived users will appear here when you archive them from the Users page.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Confirm Action Modal -->
                        <div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmActionTitle">Confirm Action</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" id="confirmActionMessage">
                                        Are you sure?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-primary" id="confirmActionBtn">Confirm</button>
                                    </div>
                                </div>
                            </div>
                        </div>
@endsection

@push('scripts')
    <script>
        (function() {
            const modalEl = document.getElementById('confirmActionModal');
            if (!modalEl || typeof bootstrap === 'undefined') {
                return;
            }

            const modal = new bootstrap.Modal(modalEl);
            const titleEl = document.getElementById('confirmActionTitle');
            const messageEl = document.getElementById('confirmActionMessage');
            const confirmBtn = document.getElementById('confirmActionBtn');

            let pendingForm = null;

            document.querySelectorAll('[data-confirm]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const form = btn.closest('form');
                    if (!form) return;
                    pendingForm = form;

                    if (titleEl) {
                        titleEl.textContent = btn.getAttribute('data-confirm-title') || 'Confirm Action';
                    }
                    if (messageEl) {
                        messageEl.textContent = btn.getAttribute('data-confirm-message') || 'Are you sure you want to proceed?';
                    }

                    modal.show();
                });
            });

            if (confirmBtn) {
                confirmBtn.addEventListener('click', () => {
                    if (pendingForm) {
                        pendingForm.submit();
                        pendingForm = null;
                    }
                    modal.hide();
                });
            }
        })();
    </script>
@endpush
