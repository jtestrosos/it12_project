@extends('admin.layout')

@section('title', 'Appointments - Barangay Health Center')
@section('page-title', 'Manage Appointments')
@section('page-description', 'View and manage all patient appointments')

@section('page-styles')
<style>
        .appointment-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 1.5rem;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
            margin-bottom: 1rem;
            border: 1px solid #edf1f7;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }
        .appointment-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.10);
            border-color: #d0e2ff;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        .status-completed {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .table-modern {
            border-collapse: separate;
            border-spacing: 0;
        }
        .table-modern thead th {
            background-color: #f9fafb;
            border: none;
            font-weight: 600;
            color: #4b5563;
            padding: 0.9rem 1rem;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: .04em;
        }
        .table-modern tbody td {
            border: none;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #edf2f7;
        }
        .table-modern tbody tr:hover {
            background-color: #f8f9fa;
        }
        /* Ensure dropdowns inside responsive tables are not clipped */
        .table-responsive { overflow: visible; }
        /* Keep actions column flexible */
        .actions-col { white-space: nowrap; }
        /* Dark mode modal + form fields (booking drawer/modal) */
        body.bg-dark .offcanvas, body.bg-dark .modal-content { background: #1e2124; color: #e6e6e6; border-color: #2a2f35; }
        body.bg-dark .offcanvas .form-label, body.bg-dark .modal-content .form-label { color: #e6e6e6; }
        body.bg-dark .offcanvas .form-control,
        body.bg-dark .offcanvas .form-select,
        body.bg-dark .modal-content .form-control,
        body.bg-dark .modal-content .form-select { background-color: #0f1316; color: #e6e6e6; border-color: #2a2f35; }
        body.bg-dark .offcanvas .form-control::placeholder,
        body.bg-dark .modal-content .form-control::placeholder { color: #9aa4ad; }
        body.bg-dark .offcanvas .input-group-text { background: #1a1f24; color: #cbd3da; border-color: #2a2f35; }
        /* Dark mode dropdown */
        body.bg-dark .dropdown-menu { background: #1e2124; border-color: #2a2f35; }
        body.bg-dark .dropdown-item { color: #e6e6e6; }
        body.bg-dark .dropdown-item:hover, body.bg-dark .dropdown-item.active { background-color: #2a2f35; color: #fff; }
        body.bg-dark .table-modern thead th { background-color: #1a1f24; color: #e6e6e6; }
        body.bg-dark .table-modern tbody td { border-bottom-color: #2a2f35; color: #d6d6d6; }
        body.bg-dark .table-modern tbody tr:hover { background-color: #2a2f35; }

        /* Match Inventory's darker sidebar color in dark mode */
        body.bg-dark .sidebar { background: #131516; border-right-color: #2a2f35; }
    </style>
@endsection

@section('content')
                        <!-- Add Appointment Button -->

                        <!-- Top Actions -->
                        <div class="d-flex flex-wrap justify-content-end align-items-center mb-3 gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <!-- Bulk Actions (hidden by default) -->
                                <div id="bulkActions" class="d-none d-flex align-items-center gap-2">
                                    <span class="text-muted" id="selectedCount">0 selected</span>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-success" id="bulkApprove">
                                            <i class="fas fa-check me-1"></i> Approve
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" id="bulkCancel">
                                            <i class="fas fa-times me-1"></i> Cancel
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-info" id="bulkComplete">
                                            <i class="fas fa-check-circle me-1"></i> Complete
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
                                    <i class="fas fa-plus me-2"></i> Add New Appointment
                                </button>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="filter-card mb-3">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" id="appointmentStatusFilter">
                                        <option value="">All</option>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Confirmed</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                        <option value="rescheduled">Rescheduled</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Service</label>
                                    <select class="form-select" id="appointmentServiceFilter">
                                        <option value="">All</option>
                                        @foreach(($services ?? []) as $service)
                                            <option value="{{ strtolower($service) }}">{{ $service }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Search</label>
                                    <input type="text" id="appointmentSearch" class="form-control" placeholder="Search by patient, email, or phone">
                                </div>
                                <div class="col-md-2 d-flex align-items-center">
                                    <button type="button" class="btn btn-outline-secondary w-100" id="appointmentFiltersReset">
                                        <i class="fas fa-undo me-1"></i> Clear
                                    </button>
                                </div>
                            </div>
                            <div class="mt-2 small text-muted" id="appointmentsFilterSummary">
                                Showing {{ $appointments->count() }} appointments
                            </div>
                        </div>

                        <!-- Appointments Table -->
                        <div class="table-card p-0">
                                <div class="table-responsive">
                                @php
                                    $currentSort = request('sort');
                                    $currentDirection = strtolower(request('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
                                    $nextDirection = $currentSort === 'date' && $currentDirection === 'asc' ? 'desc' : 'asc';
                                @endphp
                                <table class="table table-modern table-hover mb-0 align-middle">
                                    <thead class="table-light position-sticky top-0" style="z-index:1;">
                                            <tr>
                                            <th style="width:30px"><input type="checkbox" id="selectAll"></th>
                                            <th>
                                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'date', 'direction' => $nextDirection]) }}" class="text-decoration-none d-inline-flex align-items-center gap-1">
                                                    Date
                                                    @if($currentSort === 'date')
                                                        <i class="fas fa-sort-{{ $currentDirection === 'asc' ? 'up' : 'down' }} fa-sm ms-1"></i>
                                                    @else
                                                        <i class="fas fa-sort fa-sm ms-1 text-muted"></i>
                                                    @endif
                                                </a>
                                            </th>
                                                <th>Patient</th>
                                                <th>Service</th>
                                            
                                                <th>Status</th>
                                            <th class="actions-col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="appointmentsTableBody">
                                        @forelse($appointments as $appointment)
                                        <tr
                                            data-status="{{ strtolower($appointment->status) }}"
                                            data-service="{{ strtolower($appointment->service_type) }}"
                                            data-patient="{{ strtolower($appointment->patient_name) }}"
                                            data-email="{{ strtolower($appointment->user->email ?? '') }}"
                                            data-phone="{{ strtolower($appointment->patient_phone ?? '') }}"
                                        >
                                            <td><input type="checkbox" class="row-check" value="{{ $appointment->id }}" data-appointment-id="{{ $appointment->id }}"></td>
                                            <td>
                                                <div class="fw-bold">{{ $appointment->appointment_date->format('M d, Y') }} {{ $appointment->appointment_time }}</div>
                                                <div class="progress" style="height:6px;">
                                                    <div class="progress-bar bg-{{ ($appointment->capacity_fill ?? 0) > 80 ? 'danger' : (($appointment->capacity_fill ?? 0) > 50 ? 'warning' : 'success') }}" role="progressbar" style="width: {{ $appointment->capacity_fill ?? 0 }}%"></div>
                                                </div>
                                            </td>
                                                <td>
                                                    <div class="fw-bold">{{ $appointment->patient_name }}</div>
                                                <small class="text-muted">{{ $appointment->user->email ?? $appointment->patient_phone }}</small>
                                                </td>
                                                <td>{{ $appointment->service_type }}</td>
                                            
                                                <td>
                                                <span class="status-badge status-{{ $appointment->status }}">{{ ucfirst($appointment->status) }}</span>
                                                </td>
                                            <td class="actions-col">
                                                <div class="btn-group">
                                                    <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal{{ $appointment->id }}">View</button>
                                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" data-bs-display="static"></button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <form method="POST" action="{{ route('admin.appointment.update', $appointment) }}" class="px-3 py-1">
                                                                @csrf
                                                                <input type="hidden" name="status" value="approved">
                                                                <button type="submit" class="btn btn-link p-0">Confirm</button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reschedModal{{ $appointment->id }}">Reschedule…</button>
                                                        </li>
                                                        <li>
                                                            <form method="POST" action="{{ route('admin.appointment.update', $appointment) }}" class="px-3 py-1">
                                                                @csrf
                                                                <input type="hidden" name="status" value="completed">
                                                                <button type="submit" class="btn btn-link p-0">Mark Completed</button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form method="POST" action="{{ route('admin.appointment.update', $appointment) }}" class="px-3 py-1">
                                                                @csrf
                                                                <input type="hidden" name="status" value="cancelled">
                                                                <input type="hidden" name="notes" value="No-show">
                                                                <button type="submit" class="btn btn-link p-0">Mark No-show</button>
                                                            </form>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form method="POST" action="{{ route('admin.appointment.update', $appointment) }}" class="px-3 py-1">
                                                                @csrf
                                                                <input type="hidden" name="status" value="cancelled">
                                                                <button type="submit" class="btn btn-link text-danger p-0">Cancel</button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            </tr>
                                        <!-- Reschedule Modal -->
                                        <div class="modal fade" id="reschedModal{{ $appointment->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Reschedule Appointment</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST" action="{{ route('admin.appointment.update', $appointment) }}">
                                                        @csrf
                                                        <input type="hidden" name="status" value="rescheduled">
                                                        <div class="modal-body">
                                                            <div class="row g-3">
                                                                <div class="col-md-6">
                                                                    <label class="form-label">New Date</label>
                                                                    <input type="date" name="new_date" class="form-control" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">New Time</label>
                                                                    <input type="time" name="new_time" class="form-control" step="1800" required>
                                                                </div>
                                                            </div>
                                                            <div class="row g-3 mt-3">
                                                                <div class="col-md-6">
                                                                    <label class="form-label">New Duration</label>
                                                                    <select class="form-select" name="new_duration" required>
                                                                        <option value="" disabled selected>Select Duration</option>
                                                                        @for ($i = 30; $i <= 60; $i++)
                                                                            <option value="{{ $i }}">{{ $i }} minutes</option>
                                                                        @endfor
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="mt-3">
                                                                <label class="form-label">Notes (optional)</label>
                                                                <textarea class="form-control" name="notes" rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Save</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- View Modal -->
                                        <div class="modal fade" id="viewAppointmentModal{{ $appointment->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Appointment Details</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-2"><strong>Patient:</strong> {{ $appointment->patient_name }}</div>
                                                        <div class="mb-2"><strong>Service:</strong> {{ $appointment->service_type }}</div>
                                                        <div class="mb-2"><strong>Date/Time:</strong> {{ $appointment->appointment_date->format('M d, Y') }} {{ $appointment->appointment_time }}</div>
                                                        <div class="mb-2"><strong>Status:</strong> {{ ucfirst($appointment->status) }}</div>
                                                        @if($appointment->notes)
                                                            <div class="mb-2"><strong>Notes:</strong> {{ $appointment->notes }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="mb-2">No appointments match your filters.</div>
                                                <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">Add an appointment</a>
                                            </td>
                                        </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $appointments->links('pagination::bootstrap-5') }}
                        </div>

    <!-- Add Appointment Modal -->
    <div class="modal fade" id="addAppointmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.appointment.create') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="patient_name" class="form-label">Patient Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="patient_name" name="patient_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="patient_phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="patient_phone" name="patient_phone" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="patient_address" class="form-label">Address <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="patient_address" name="patient_address" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="service_type" class="form-label">Service Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="service_type" name="service_type" required>
                                        <option value="" disabled selected>Select Service</option>
                                        <option value="General Checkup">General Checkup</option>
                                        <option value="Prenatal">Prenatal</option>
                                        <option value="Immunization">Immunization</option>
                                        <option value="Family Planning">Family Planning</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="appointment_date" class="form-label">Appointment Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="appointment_date" name="appointment_date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="appointment_time" class="form-label">Appointment Time <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="appointment_time" name="appointment_time" step="1800" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="duration" class="form-label">Duration <span class="text-danger">*</span></label>
                                    <select class="form-select" id="duration" name="duration" required>
                                        <option value="" disabled selected>Select Duration</option>
                                        @for ($i = 30; $i <= 60; $i++)
                                            <option value="{{ $i }}">{{ $i }} minutes</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Appointment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
 

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusFilter = document.getElementById('appointmentStatusFilter');
        const serviceFilter = document.getElementById('appointmentServiceFilter');
        const searchInput = document.getElementById('appointmentSearch');
        const resetButton = document.getElementById('appointmentFiltersReset');
        const tableBody = document.getElementById('appointmentsTableBody');
        const summary = document.getElementById('appointmentsFilterSummary');
        const tableRows = tableBody ? Array.from(tableBody.querySelectorAll('tr[data-status]')) : [];

        const normalize = (value) => (value ?? '').toString().trim().toLowerCase();

        const applyAppointmentFilters = () => {
            const statusValue = normalize(statusFilter ? statusFilter.value : '');
            const serviceValue = normalize(serviceFilter ? serviceFilter.value : '');
            const searchValue = normalize(searchInput ? searchInput.value : '');

            let visibleCount = 0;

            tableRows.forEach((row) => {
                const rowStatus = normalize(row.dataset.status);
                const rowService = normalize(row.dataset.service);
                const rowSearchTargets = normalize(
                    [row.dataset.patient, row.dataset.email, row.dataset.phone, row.dataset.service]
                        .filter(Boolean)
                        .join(' ')
                );

                let showRow = true;

                if (statusValue && rowStatus !== statusValue) {
                    showRow = false;
                }

                if (serviceValue && rowService !== serviceValue) {
                    showRow = false;
                }

                if (searchValue && !rowSearchTargets.includes(searchValue)) {
                    showRow = false;
                }

                row.style.display = showRow ? '' : 'none';
                if (showRow) {
                    visibleCount++;
                }
            });

            if (summary) {
                summary.textContent = `Showing ${visibleCount} of ${tableRows.length} appointments`;
            }
        };

        const debounce = (fn, delay = 300) => {
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => fn(...args), delay);
            };
        };

        const debouncedApplyFilters = debounce(applyAppointmentFilters, 250);

        if (statusFilter) {
            statusFilter.addEventListener('change', applyAppointmentFilters);
        }

        if (serviceFilter) {
            serviceFilter.addEventListener('change', applyAppointmentFilters);
        }

        if (searchInput) {
            searchInput.addEventListener('input', debouncedApplyFilters);
        }

        if (resetButton) {
            resetButton.addEventListener('click', () => {
                if (statusFilter) statusFilter.value = '';
                if (serviceFilter) serviceFilter.value = '';
                if (searchInput) searchInput.value = '';
                applyAppointmentFilters();
            });
        }

        applyAppointmentFilters();

        const selectAllCheckbox = document.getElementById('selectAll');
        const rowCheckboxes = document.querySelectorAll('.row-check');
        const bulkActions = document.getElementById('bulkActions');
        const selectedCount = document.getElementById('selectedCount');
        const bulkApproveBtn = document.getElementById('bulkApprove');
        const bulkCancelBtn = document.getElementById('bulkCancel');
        const bulkCompleteBtn = document.getElementById('bulkComplete');

        // Select All functionality
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkActions();
            });
        }

        // Individual checkbox change
        rowCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectAll();
                updateBulkActions();
            });
        });

        function updateSelectAll() {
            if (selectAllCheckbox) {
                const allChecked = Array.from(rowCheckboxes).every(cb => cb.checked);
                const someChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            }
        }

        function updateBulkActions() {
            const checkedBoxes = Array.from(rowCheckboxes).filter(cb => cb.checked);
            const count = checkedBoxes.length;

            if (count > 0) {
                bulkActions.classList.remove('d-none');
                selectedCount.textContent = count + ' selected';
            } else {
                bulkActions.classList.add('d-none');
            }
        }

        // Bulk Approve
        if (bulkApproveBtn) {
            bulkApproveBtn.addEventListener('click', function() {
                const selectedIds = getSelectedIds();
                if (selectedIds.length > 0 && confirm('Are you sure you want to approve ' + selectedIds.length + ' appointment(s)?')) {
                    bulkUpdateStatus(selectedIds, 'approved');
                }
            });
        }

        // Bulk Cancel
        if (bulkCancelBtn) {
            bulkCancelBtn.addEventListener('click', function() {
                const selectedIds = getSelectedIds();
                if (selectedIds.length > 0 && confirm('Are you sure you want to cancel ' + selectedIds.length + ' appointment(s)?')) {
                    bulkUpdateStatus(selectedIds, 'cancelled');
                }
            });
        }

        // Bulk Complete
        if (bulkCompleteBtn) {
            bulkCompleteBtn.addEventListener('click', function() {
                const selectedIds = getSelectedIds();
                if (selectedIds.length > 0 && confirm('Are you sure you want to mark ' + selectedIds.length + ' appointment(s) as completed?')) {
                    bulkUpdateStatus(selectedIds, 'completed');
                }
            });
        }

        function getSelectedIds() {
            return Array.from(rowCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.getAttribute('data-appointment-id'));
        }

        function bulkUpdateStatus(ids, status) {
            // Update appointments one by one using fetch API
            let completed = 0;
            const total = ids.length;
            
            ids.forEach((id, index) => {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('status', status);
                
                fetch(`/admin/appointment/${id}/update`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    completed++;
                    if (completed === total) {
                        // All requests completed, reload the page
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error updating appointment:', error);
                    completed++;
                    if (completed === total) {
                        window.location.reload();
                    }
                });
            });
        }

        // Restrict date inputs to today up to 1 month from now
        const today = new Date();
        const minDate = today.toISOString().split('T')[0];
        const oneMonthFromNow = new Date(today.getFullYear(), today.getMonth() + 1, today.getDate());
        const maxDate = oneMonthFromNow.toISOString().split('T')[0];

        const dateInputs = document.querySelectorAll('input[type="date"]');
        dateInputs.forEach(input => {
            input.min = minDate;
            input.max = maxDate;
        });
    });
</script>
@endpush