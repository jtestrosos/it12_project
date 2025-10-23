@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Appointment Details</h4>
                    <span class="badge 
                        @if($appointment->status == 'pending') bg-warning
                        @elseif($appointment->status == 'approved') bg-success
                        @elseif($appointment->status == 'completed') bg-info
                        @elseif($appointment->status == 'cancelled') bg-danger
                        @else bg-secondary
                        @endif fs-6">
                        {{ ucfirst($appointment->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Patient Information</h6>
                            <p><strong>Name:</strong> {{ $appointment->patient_name }}</p>
                            <p><strong>Phone:</strong> {{ $appointment->patient_phone }}</p>
                            <p><strong>Address:</strong> {{ $appointment->patient_address }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">Appointment Details</h6>
                            <p><strong>Date:</strong> {{ $appointment->appointment_date->format('M d, Y') }}</p>
                            <p><strong>Time:</strong> {{ $appointment->appointment_time }}</p>
                            <p><strong>Service:</strong> {{ $appointment->service_type }}</p>
                            @if($appointment->is_walk_in)
                                <p><strong>Type:</strong> <span class="badge bg-secondary">Walk-in</span></p>
                            @endif
                        </div>
                    </div>

                    @if($appointment->medical_history)
                    <div class="mt-3">
                        <h6 class="fw-bold">Medical History</h6>
                        <p>{{ $appointment->medical_history }}</p>
                    </div>
                    @endif

                    @if($appointment->notes)
                    <div class="mt-3">
                        <h6 class="fw-bold">Notes</h6>
                        <p>{{ $appointment->notes }}</p>
                    </div>
                    @endif

                    @if($appointment->approved_by)
                    <div class="mt-3">
                        <h6 class="fw-bold">Approved By</h6>
                        <p>{{ $appointment->approvedBy->name }} on {{ $appointment->approved_at->format('M d, Y g:i A') }}</p>
                    </div>
                    @endif

                    <div class="mt-4 d-flex gap-2">
                        <a href="{{ route('patient.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
                        @if($appointment->status == 'pending')
                        <form method="POST" action="{{ route('patient.appointment.cancel', $appointment) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                Cancel Appointment
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
