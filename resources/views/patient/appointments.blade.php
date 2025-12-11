@extends('layouts.admin')

@section('title', 'My Appointments - Patient Portal')
@section('page-title', 'My Appointments')
@section('page-description', 'Manage your healthcare appointments.')

@section('sidebar-menu')
    <a class="nav-link @if(request()->routeIs('patient.dashboard')) active @endif" href="{{ route('patient.dashboard') }}"
        data-tooltip="Dashboard">
        <i class="fas fa-th-large"></i> <span class="sidebar-content">Dashboard</span>
    </a>
    <a class="nav-link @if(request()->routeIs('patient.appointments') || request()->routeIs('patient.appointment.show')) active @endif"
        href="{{ route('patient.appointments') }}" data-tooltip="My Appointments">
        <i class="fas fa-calendar"></i> <span class="sidebar-content">My Appointments</span>
    </a>
    <a class="nav-link @if(request()->routeIs('patient.book-appointment')) active @endif"
        href="{{ route('patient.book-appointment') }}" data-tooltip="Book Appointment">
        <i class="fas fa-plus"></i> <span class="sidebar-content">Book Appointment</span>
    </a>
@endsection

@section('user-initials')
    {{ substr(\App\Helpers\AuthHelper::user()->name, 0, 2) }}
@endsection

@section('user-name')
    {{ \App\Helpers\AuthHelper::user()->name }}
@endsection

@section('user-role')
    Patient
@endsection

@section('page-styles')
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .appointments-card {
            background: #fafafa;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
        }

        body.bg-dark .appointments-card {
            background: #1e2124;
            border-color: #2a2f35;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .appointments-header {
            background: #fafafa;
            border-bottom: 1px solid #e9ecef;
            padding: 1.5rem;
            margin-bottom: 0;
            border-radius: 12px 12px 0 0;
        }

        body.bg-dark .appointments-header {
            background: #1e2124;
            border-color: #2a2f35;
        }

        body.bg-dark .appointments-header h5 {
            color: #e6e6e6 !important;
        }

        .appointments-body {
            padding: 1.5rem;
        }

        .table-modern {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-modern thead th {
            background-color: #f5f5f5;
            border: none;
            font-weight: 600;
            color: #495057;
            padding: 1rem;
        }

        body.bg-dark .table-modern thead th {
            background-color: #1a1f24;
            color: #e6e6e6;
        }

        .table-modern tbody td {
            border: none;
            padding: 1rem;
            border-bottom: 1px solid #f1f3f4;
        }

        body.bg-dark .table-modern tbody td {
            border-bottom-color: #2a2f35;
            color: #e6e6e6;
        }

        .table-modern tbody tr:hover {
            background-color: #f8f9fa;
        }

        body.bg-dark .table-modern tbody tr:hover {
            background-color: #2a2f35;
        }

        .appointment-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #009fb1, #008a9a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-block;
            color: #000 !important;
        }
        
        .table-modern .text-center {
            text-align: center;
            padding-right: 4rem;
        }
        
        /* Ensure all badge text is black */
        .status-badge.bg-warning,
        .status-badge.bg-success,
        .status-badge.bg-danger,
        .status-badge.bg-info {
            color: #000 !important;
        }

        .btn-modern {
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        body.bg-dark .fw-bold.text-dark {
            color: #e6e6e6 !important;
        }

        /* Dark Mode Pagination */
        body.bg-dark .page-link {
            background-color: #1e2124;
            border-color: #2a2f35;
            color: #e6e6e6;
        }

        body.bg-dark .page-item.disabled .page-link {
            background-color: #1a1d20;
            border-color: #2a2f35;
            color: #6c757d;
        }

        body.bg-dark .page-item.active .page-link {
            background-color: #009fb1;
            border-color: #009fb1;
            color: #fff;
        }

        /* Dark Mode Text Utilities */
        body.bg-dark .text-muted {
            color: #adb5bd !important;
        }

        body.bg-dark .text-dark {
            color: #e6e6e6 !important;
        }

        /* Dark Mode Table Styling */
        body.bg-dark .table-modern {
            color: #e6e6e6;
        }

        body.bg-dark .table-modern td,
        body.bg-dark .table-modern th {
            color: #e6e6e6 !important;
        }

        body.bg-dark .table-modern .appointment-title {
            color: #fff !important;
        }

        body.bg-dark .table-modern .appointment-subtitle {
            color: #adb5bd !important;
        }

        /* Ultra-specific selectors for table content */
        body.bg-dark .appointments-card .table-modern tbody td {
            color: #e6e6e6 !important;
        }

        body.bg-dark .appointments-card .table-modern tbody td *:not(.status-badge):not(.badge) {
            color: inherit !important;
        }

        /* Force status badges to have black text in dark mode */
        body.bg-dark .appointments-card .table-modern tbody td .status-badge,
        body.bg-dark .appointments-card .table-modern tbody td .badge {
            color: #000 !important;
        }

        body.bg-dark .appointments-card .table-modern tbody td div {
            color: #e6e6e6 !important;
        }

        body.bg-dark .appointments-card .table-modern tbody td span:not(.badge):not(.status-badge) {
            color: #e6e6e6 !important;
        }



        /* Filter Chips */
        .filter-chips {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .filter-chip {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            border: 2px solid #e9ecef;
            background: white;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .filter-chip:hover {
            border-color: #009fb1;
            background: rgba(0, 159, 177, 0.1);
        }

        .filter-chip.active {
            background: #009fb1;
            border-color: #009fb1;
            color: white;
        }

        body.bg-dark .filter-chip {
            background: #1e2124;
            border-color: #2a2f35;
            color: #e6e6e6 !important;
        }

        body.bg-dark .filter-chip:hover {
            border-color: #009fb1;
            background: rgba(0, 159, 177, 0.2);
            color: #fff !important;
        }

        body.bg-dark .filter-chip.active {
            background: #009fb1;
            border-color: #009fb1;
            color: white !important;
        }

        body.bg-dark .filter-chip i {
            color: inherit;
        }

        /* Search Box Enhancement */
        .search-box {
            position: relative;
        }

        .search-box .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .search-box input {
            padding-left: 2.5rem;
        }

        .search-box .clear-search {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            display: none;
        }

        .search-box.has-value .clear-search {
            display: block;
        }
    </style>
@endsection

@section('content')
    <!-- Filter Chips -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="filter-chips">
                <button class="filter-chip active" data-filter="all">
                    <i class="fas fa-list me-1"></i> All
                </button>
                <button class="filter-chip" data-filter="pending">
                    <i class="fas fa-clock me-1"></i> Pending
                </button>
                <button class="filter-chip" data-filter="approved">
                    <i class="fas fa-check-circle me-1"></i> Approved
                </button>
                <button class="filter-chip" data-filter="completed">
                    <i class="fas fa-check-double me-1"></i> Completed
                </button>
                <button class="filter-chip" data-filter="cancelled">
                    <i class="fas fa-times-circle me-1"></i> Cancelled
                </button>
            </div>
        </div>
    </div>

    <!-- Search Box -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="appointmentSearch" class="form-control"
                    placeholder="Search by appointment ID, service type...">
                <button class="clear-search" id="clearSearch">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Appointments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card appointments-card">
                <div class="appointments-header">
                    <h5 class="mb-0">All Appointments</h5>
                </div>
                <div class="appointments-body">
                    @if($appointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>Appointment</th>
                                        <th>Date & Time</th>
                                        <th>Service</th>
                                        <th class="text-center">Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointments as $appointment)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="appointment-avatar me-3">
                                                        <i class="fas fa-calendar"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark">Appointment #{{ $appointment->id }}</div>
                                                        <small class="text-muted">{{ $appointment->patient_name }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-bold text-dark">
                                                        {{ $appointment->appointment_date->format('M d, Y') }}
                                                    </div>
                                                    <small
                                                        class="text-muted">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</small>
                                                </div>
                                            </td>
                                            <td class="text-muted">{{ $appointment->service_type }}</td>
                                            <td class="text-center">
                                                <span class="status-badge 
                                                                        @if($appointment->status == 'pending') bg-warning
                                                                        @elseif($appointment->status == 'approved') bg-success
                                                                        @elseif($appointment->status == 'completed') bg-info
                                                                        @elseif($appointment->status == 'cancelled') bg-danger
                                                                        @else bg-secondary
                                                                        @endif">
                                                    {{ ucfirst($appointment->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('patient.appointment.show', $appointment) }}"
                                                    class="btn btn-sm btn-primary me-2">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </a>
                                                @if($appointment->status !== 'cancelled' && $appointment->status !== 'completed' && $appointment->status !== 'approved')
                                                    <button type="button" class="btn btn-sm btn-danger cancel-appointment-btn"
                                                        data-appointment-id="{{ $appointment->id }}"
                                                        data-cancel-url="{{ route('patient.cancel-appointment', $appointment) }}"
                                                        data-appointment-date="{{ $appointment->appointment_date }}"
                                                        data-appointment-time="{{ $appointment->appointment_time }}"
                                                        data-service-type="{{ $appointment->service_type }}">
                                                        <i class="fas fa-times"></i> Cancel
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $appointments->links() }}
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar text-muted mb-3" style="font-size: 3rem;"></i>
                            <p class="text-muted mb-3">No appointments found.</p>
                            <a href="{{ route('patient.book-appointment') }}" class="btn btn-primary btn-modern">Book Your First
                                Appointment</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>Cancel Appointment
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Are you sure you want to cancel this appointment?</p>
                    <div id="cancelAppointmentDetails">
                        <!-- Details will be populated by JavaScript -->
                    </div>
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>This action cannot be undone. You will need to book a new appointment if you change your
                            mind.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Keep Appointment
                    </button>
                    <button type="button" id="confirmCancelBtn" class="btn btn-danger">
                        <i class="fas fa-check me-2"></i>Yes, Cancel It
                    </button>
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
                const table = document.querySelector('.table-modern');
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

            // Filter functionality
            const filterChips = document.querySelectorAll('.filter-chip');
            const appointmentRows = document.querySelectorAll('.table-modern tbody tr');

            filterChips.forEach(chip => {
                chip.addEventListener('click', () => {
                    // Update active state
                    filterChips.forEach(c => c.classList.remove('active'));
                    chip.classList.add('active');

                    const filter = chip.dataset.filter;

                    // Filter rows
                    appointmentRows.forEach(row => {
                        if (filter === 'all') {
                            row.style.display = '';
                        } else {
                            const statusBadge = row.querySelector('.status-badge');
                            if (statusBadge && statusBadge.textContent.trim().toLowerCase() === filter) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        }
                    });
                });
            });

            // Search functionality
            const searchInput = document.getElementById('appointmentSearch');
            const searchBox = searchInput.closest('.search-box');
            const clearBtn = document.getElementById('clearSearch');

            searchInput.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();

                // Toggle clear button
                if (searchTerm) {
                    searchBox.classList.add('has-value');
                } else {
                    searchBox.classList.remove('has-value');
                }

                // Filter rows
                appointmentRows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            clearBtn.addEventListener('click', () => {
                searchInput.value = '';
                searchBox.classList.remove('has-value');
                appointmentRows.forEach(row => row.style.display = '');
            });

            // Toast notifications for actions
            const cancelBtns = document.querySelectorAll('.cancel-appointment-btn');
            const cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));
            const confirmCancelBtn = document.getElementById('confirmCancelBtn');

            cancelBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();

                    const appointmentId = btn.dataset.appointmentId;
                    const cancelUrl = btn.dataset.cancelUrl;
                    const appointmentDate = btn.dataset.appointmentDate;
                    const appointmentTime = btn.dataset.appointmentTime;
                    const serviceType = btn.dataset.serviceType;

                    // Populate modal with appointment details
                    const detailsContainer = document.getElementById('cancelAppointmentDetails');
                    detailsContainer.innerHTML = `
                            <div class="confirmation-detail" style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f1f3f4;">
                                <span style="font-weight: 600; color: #495057;">Date:</span>
                                <span style="color: #6c757d;">${new Date(appointmentDate).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</span>
                            </div>
                            <div class="confirmation-detail" style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f1f3f4;">
                                <span style="font-weight: 600; color: #495057;">Time:</span>
                                <span style="color: #6c757d;">${appointmentTime}</span>
                            </div>
                            <div class="confirmation-detail" style="display: flex; justify-content: space-between; padding: 0.75rem 0;">
                                <span style="font-weight: 600; color: #495057;">Service:</span>
                                <span style="color: #6c757d;">${serviceType}</span>
                            </div>
                        `;

                    // Store the cancel URL
                    confirmCancelBtn.dataset.cancelUrl = cancelUrl;
                    confirmCancelBtn.dataset.csrfToken = '{{ csrf_token() }}';

                    // Show modal
                    cancelModal.show();
                });
            });

            // Confirm cancel button - use direct event listener without Bootstrap modal interference
            confirmCancelBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                const cancelUrl = confirmCancelBtn.dataset.cancelUrl;
                const csrfToken = confirmCancelBtn.dataset.csrfToken;

                console.log('Cancel button clicked, URL:', cancelUrl);

                if (cancelUrl) {
                    // Hide modal manually
                    const modalElement = document.getElementById('cancelModal');
                    modalElement.classList.remove('show');
                    modalElement.style.display = 'none';
                    modalElement.setAttribute('aria-hidden', 'true');

                    // Remove backdrop
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }

                    // Remove modal-open class from body
                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('overflow');
                    document.body.style.removeProperty('padding-right');

                    // Show toast if available
                    if (window.toast && typeof window.toast.info === 'function') {
                        window.toast.info('Cancelling appointment...', 'Please wait');
                    }

                    // Create form dynamically and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = cancelUrl;
                    form.style.display = 'none';

                    // Add CSRF token
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);

                    // Add PUT method
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PUT';
                    form.appendChild(methodInput);

                    // Append to body and submit
                    document.body.appendChild(form);
                    console.log('Submitting form to:', form.action);
                    form.submit();
                } else {
                    console.error('No cancel URL found');
                    if (window.toast && typeof window.toast.error === 'function') {
                        window.toast.error('Error: Could not cancel appointment', 'Error');
                    } else {
                        alert('Error: Could not cancel appointment');
                    }
                }
            });

            // Show session messages
            @if(session('success'))
                if (window.toast && typeof window.toast.success === 'function') {
                    window.toast.success('{{ session('success') }}');
                }
            @endif

            @if(session('error'))
                if (window.toast && typeof window.toast.error === 'function') {
                    window.toast.error('{{ session('error') }}');
                }
            @endif
            });
    </script>
@endpush