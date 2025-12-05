@extends('superadmin.layout')

@section('title', 'Archived Users - Barangay Health Center')
@section('page-title', 'Archived Users')
@section('page-description', 'View and manage archived user accounts')

@section('page-styles')
    <style>
        /* Card container - responds to dark mode */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }

        body.bg-dark .card {
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

        /* Pagination */
        .border-top {
            border-top: 1px solid #e9ecef !important;
        }

        body.bg-dark .border-top {
            border-top-color: #2a2f35 !important;
        }

        /* Empty state */
        body.bg-dark .text-muted {
            color: #b0b0b0 !important;
        }

        body.bg-dark .card-body {
            color: #e6e6e6;
        }
        
        /* Hide Bootstrap pagination's built-in "Showing" text on the left */
        nav[role="navigation"] p,
        nav[role="navigation"] .text-sm {
            display: none !important;
        }
        
        /* Bring showing text closer to pagination */
        #usersArchivePaginationContainer > div:last-child {
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
                        <thead>
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
                                            <form method="POST"
                                                action="{{ route('superadmin.user.restore', ['type' => $user->role, 'id' => $user->id]) }}">
                                                @csrf
                                                <button type="button"
                                                    class="btn btn-outline-success btn-sm d-flex align-items-center action-btn"
                                                    data-confirm data-confirm-title="Restore User"
                                                    data-confirm-message="Are you sure you want to restore this user?">
                                                    <i class="fas fa-undo me-1"></i>
                                                    <span>Restore</span>
                                                </button>
                                            </form>
                                            <form method="POST"
                                                action="{{ route('superadmin.user.force-delete', ['type' => $user->role, 'id' => $user->id]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="btn btn-outline-danger btn-sm d-flex align-items-center action-btn"
                                                    data-confirm data-confirm-title="Delete User"
                                                    data-confirm-message="Permanently delete this user? This cannot be undone.">
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
            @else
                <div class="text-center py-5">
                    <i class="fas fa-archive fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No archived users</h5>
                    <p class="text-muted">Archived users will appear here when you archive them from the Users page.</p>
                </div>
            @endif
        </div>
    </div>

    @if($users->count() > 0)
        <div class="d-flex flex-column align-items-center mt-4"
            id="usersArchivePaginationContainer">
            <div>
                {{ $users->withQueryString()->links() }}
            </div>
            <div class="small text-muted mb-0 mt-n2">
                Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} archived users
            </div>
        </div>
    @endif

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

        (function () {
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