@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0 text-dark">My Appointments</h2>
                <a href="{{ route('patient.book-appointment') }}" class="btn btn-dark">
                    <i class="fas fa-plus me-2"></i> Book New Appointment
                </a>
            </div>
        </div>
    </div>

    <!-- Appointments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0 text-dark">All Appointments</h5>
                </div>
                <div class="card-body">
                    @if($appointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-dark">Date</th>
                                        <th class="text-dark">Time</th>
                                        <th class="text-dark">Service</th>
                                        <th class="text-dark">Status</th>
                                        <th class="text-dark">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointments as $appointment)
                                    <tr>
                                        <td class="text-dark">{{ $appointment->appointment_date->format('M d, Y') }}</td>
                                        <td class="text-dark">{{ $appointment->appointment_time }}</td>
                                        <td class="text-dark">{{ $appointment->service_type }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($appointment->status == 'pending') bg-warning text-dark
                                                @elseif($appointment->status == 'approved') bg-success
                                                @elseif($appointment->status == 'completed') bg-info
                                                @elseif($appointment->status == 'cancelled') bg-danger
                                                @else bg-secondary
                                                @endif">
                                                {{ ucfirst($appointment->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('patient.appointment.show', $appointment) }}" class="btn btn-sm btn-outline-dark">
                                                View
                                            </a>
                                            @if($appointment->status == 'pending')
                                            <form method="POST" action="{{ route('patient.appointment.cancel', $appointment) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                                    Cancel
                                                </button>
                                            </form>
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
                            <p class="text-muted mb-3">No appointments found.</p>
                            <a href="{{ route('patient.book-appointment') }}" class="btn btn-dark">Book Your First Appointment</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
