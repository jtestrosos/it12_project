@extends('layouts.admin')

@section('title', 'Appointment Details - Patient Portal')
@section('page-title', 'Appointment Details')
@section('page-description', 'Appointment #' . $appointment->id)

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

        .appointment-details-card {
            background: #fafafa;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
        }

        body.bg-dark .appointment-details-card {
            background: #1e2124;
            border-color: #2a2f35;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .appointment-details-header {
            background: #fafafa;
            border-bottom: 1px solid #e9ecef;
            padding: 1.5rem;
            margin-bottom: 0;
            border-radius: 12px 12px 0 0;
        }

        body.bg-dark .appointment-details-header {
            background: #1e2124;
            border-color: #2a2f35;
        }

        .appointment-details-body {
            padding: 1.5rem;
        }

        .info-section {
            background: #f5f5f5;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e9ecef;
        }

        body.bg-dark .info-section {
            background: #25282c;
            border-color: #2a2f35;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        body.bg-dark .info-item {
            border-bottom-color: #2a2f35;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
        }

        body.bg-dark .info-label {
            color: #e6e6e6;
        }

        .info-value {
            color: #6c757d;
        }

        body.bg-dark .info-value {
            color: #b0b0b0;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .btn-modern {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        body.bg-dark h6.fw-bold.text-dark,
        body.bg-dark h4.fw-bold.text-dark {
            color: #e6e6e6 !important;
        }
    </style>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card appointment-details-card">
                <div class="appointment-details-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold mb-1 text-dark">Appointment Details</h4>
                            <p class="text-muted mb-0">Appointment #{{ $appointment->id }}</p>
                        </div>
                        <span class="status-badge 
                                        @if($appointment->status == 'pending') bg-warning text-dark
                                        @elseif($appointment->status == 'approved') bg-success text-white
                                        @elseif($appointment->status == 'completed') bg-info text-white
                                        @elseif($appointment->status == 'cancelled') bg-danger text-white
                                        @else bg-secondary text-white
                                        @endif">
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </div>
                </div>
                <div class="appointment-details-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-section">
                                <h6 class="fw-bold text-dark mb-3">
                                    <i class="fas fa-user me-2"></i>Patient Information
                                </h6>
                                <div class="info-item">
                                    <span class="info-label">Name:</span>
                                    <span class="info-value">{{ $appointment->patient_name }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Phone:</span>
                                    <span class="info-value">{{ $appointment->patient_phone }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Address:</span>
                                    <span class="info-value">{{ $appointment->patient_address }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-section">
                                <h6 class="fw-bold text-dark mb-3">
                                    <i class="fas fa-calendar me-2"></i>Appointment Details
                                </h6>
                                <div class="info-item">
                                    <span class="info-label">Date:</span>
                                    <span class="info-value">{{ $appointment->appointment_date->format('M d, Y') }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Time:</span>
                                    <span class="info-value">{{ $appointment->appointment_time }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Service:</span>
                                    <span class="info-value">{{ $appointment->service_type }}</span>
                                </div>
                                @if($appointment->is_walk_in)
                                    <div class="info-item">
                                        <span class="info-label">Type:</span>
                                        <span class="badge bg-secondary">Walk-in</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($appointment->medical_history)
                        <div class="info-section">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="fas fa-notes-medical me-2"></i>Medical History
                            </h6>
                            <p class="text-muted mb-0">{{ $appointment->medical_history }}</p>
                        </div>
                    @endif

                    @if($appointment->notes)
                        <div class="info-section">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="fas fa-sticky-note me-2"></i>Additional Notes
                            </h6>
                            <p class="text-muted mb-0">{{ $appointment->notes }}</p>
                        </div>
                    @endif

                    @if($appointment->approved_by)
                        <div class="info-section">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="fas fa-check-circle me-2"></i>Approval Information
                            </h6>
                            <div class="info-item">
                                <span class="info-label">Approved By:</span>
                                <span class="info-value">{{ $appointment->approvedBy->name }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Approved On:</span>
                                <span class="info-value">{{ $appointment->approved_at->format('M d, Y g:i A') }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="d-flex gap-2 mt-4">
                        <a href="{{ route('patient.dashboard') }}" class="btn btn-secondary btn-modern">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                        @if($appointment->status == 'pending')
                            <button type="button" class="btn btn-danger btn-modern cancel-appointment-btn"
                                data-cancel-url="{{ route('patient.appointment.cancel', $appointment) }}"
                                data-appointment-date="{{ $appointment->appointment_date->format('Y-m-d') }}"
                                data-appointment-time="{{ $appointment->appointment_time }}"
                                data-service-type="{{ $appointment->service_type }}">
                                <i class="fas fa-times me-2"></i>Cancel Appointment
                            </button>
                        @endif
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
            const cancelBtn = document.querySelector('.cancel-appointment-btn');

            if (cancelBtn) {
                const cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));
                const confirmCancelBtn = document.getElementById('confirmCancelBtn');

                cancelBtn.addEventListener('click', (e) => {
                    e.preventDefault();

                    const cancelUrl = cancelBtn.dataset.cancelUrl;
                    const appointmentDate = cancelBtn.dataset.appointmentDate;
                    const appointmentTime = cancelBtn.dataset.appointmentTime;
                    const serviceType = cancelBtn.dataset.serviceType;

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

                // Confirm cancel button
                confirmCancelBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    const cancelUrl = confirmCancelBtn.dataset.cancelUrl;
                    const csrfToken = confirmCancelBtn.dataset.csrfToken;

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

                        // Append to body and submit
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }

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