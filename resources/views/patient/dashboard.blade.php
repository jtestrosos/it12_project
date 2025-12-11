@extends('layouts.admin')

@section('title', 'Dashboard - Patient Portal')
@section('page-title', 'Dashboard')


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

        .dashboard-card {
            background: #fafafa;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
            transition: transform 0.2s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
        }

        body.bg-dark .dashboard-card {
            background: #1e2124;
            border-color: #2a2f35;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .dashboard-header {
            background: #fafafa;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 2rem;
            margin-bottom: 0;
            border-radius: 12px 12px 0 0;
        }

        body.bg-dark .dashboard-header {
            background: #1e2124;
            border-color: #2a2f35;
        }

        .dashboard-body {
            padding: 1.5rem;
        }

        .metric-card {
            background: #fafafa;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
            transition: transform 0.2s ease;
        }

        .metric-card:hover {
            transform: translateY(-2px);
        }

        body.bg-dark .metric-card {
            background: #1e2124;
            border-color: #2a2f35;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .metric-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
        }

        body.bg-dark .metric-number {
            color: #e6e6e6;
        }

        .metric-label {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        body.bg-dark .metric-label {
            color: #b0b0b0;
        }

        .metric-change {
            font-size: 0.8rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .metric-change i {
            font-size: 0.7rem;
        }

        .trend-up {
            animation: trendUp 0.5s ease-out;
        }

        .trend-down {
            animation: trendDown 0.5s ease-out;
        }

        @keyframes trendUp {
            0% {
                transform: translateY(10px);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes trendDown {
            0% {
                transform: translateY(-10px);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .appointment-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #009fb1;
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
        }
        
        .table-modern .text-center {
            padding-right: 4rem;
        }

        .btn-modern {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
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

        body.bg-dark .fw-bold.text-dark {
            color: #e6e6e6 !important;
        }

        /* Empty State Improvements */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state-icon {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 1.5rem;
            animation: float 3s ease-in-out infinite;
        }

        body.bg-dark .empty-state-icon {
            color: #475569;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .empty-state-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        body.bg-dark .empty-state-title {
            color: #94a3b8;
        }

        .empty-state-description {
            color: #94a3b8;
            margin-bottom: 2rem;
        }

        body.bg-dark .empty-state-description {
            color: #64748b;
        }

        .btn-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
                transform: scale(1.02);
            }
        }
    </style>
@endsection

@section('content')
    <!-- Quick Stats -->
    <div class="row mb-4 g-3">
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="metric-label">Pending Appointments</div>
                        <div class="metric-number">{{ $appointments->where('status', 'pending')->count() }}</div>
                        <div class="metric-change text-warning trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>Awaiting approval</span>
                        </div>
                    </div>
                    <div class="text-warning">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="metric-label">Approved Appointments</div>
                        <div class="metric-number">{{ $appointments->where('status', 'approved')->count() }}</div>
                        <div class="metric-change text-success trend-up">
                            <i class="fas fa-check-circle"></i>
                            <span>Ready for visit</span>
                        </div>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Appointments Table -->
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="dashboard-header">
                    <h5 class="mb-0">My Appointments</h5>
                </div>
                <div class="dashboard-body p-0">
                    @if($appointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-modern mb-0">
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
                                    @foreach($appointments->take(10) as $appointment)
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
                                                                                                                    @if($appointment->status == 'pending') bg-warning text-dark
                                                                                                                    @elseif($appointment->status == 'approved') bg-success text-white
                                                                                                                    @elseif($appointment->status == 'completed') bg-info text-white
                                                                                                                    @elseif($appointment->status == 'cancelled') bg-danger text-white
                                                                                                                    @else bg-secondary text-white
                                                                                                                    @endif">
                                                    {{ ucfirst($appointment->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('patient.appointment.show', $appointment) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye me-1"></i> View
                                                    </a>
                                                    @if($appointment->status == 'pending')
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($appointments->count() > 10)
                            <div class="text-center p-3 border-top">
                                <a href="{{ route('patient.appointments') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-calendar me-2"></i> View All Appointments
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <h3 class="empty-state-title">No Appointments Yet</h3>
                            <p class="empty-state-description">
                                You haven't booked any appointments yet. Start your healthcare journey by scheduling your first
                                visit.
                            </p>
                            <a href="{{ route('patient.book-appointment') }}" class="btn btn-primary btn-pulse">
                                <i class="fas fa-plus me-2"></i>Book Your First Appointment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
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


            // Cancel appointment functionality (only if cancel buttons exist)
            const cancelBtns = document.querySelectorAll('.cancel-appointment-btn');
            if (cancelBtns.length > 0) {
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
                                        <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f1f3f4;">
                                            <span style="font-weight: 600; color: #495057;">Date:</span>
                                            <span style="color: #6c757d;">${new Date(appointmentDate).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</span>
                                        </div>
                                        <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f1f3f4;">
                                            <span style="font-weight: 600; color: #495057;">Time:</span>
                                            <span style="color: #6c757d;">${appointmentTime}</span>
                                        </div>
                                        <div style="display: flex; justify-content: space-between; padding: 0.75rem 0;">
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

                        // Add POST method (not PUT for this route)
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'POST';
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
            }


            // Show success/error messages from session
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