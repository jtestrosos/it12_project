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
        border-color: #009fb1;
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

    /* Center align action button icons */
    .table tbody td .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .table tbody td .btn i {
        margin: 0;
        line-height: 1;
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
@include('superadmin.partials.system-logs-filters')

    <!-- Table -->
    <div class="table-card">
        @if($logs->count() > 0)
@include('superadmin.partials.system-logs-table')

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
@include('superadmin.partials.log-details-modal')
@endsection

@push('scripts')
@include('superadmin.partials.system-logs-scripts')
@endpush