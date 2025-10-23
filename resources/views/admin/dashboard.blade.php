@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h2 class="fw-bold mb-4">Admin Dashboard</h2>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Today's Appointments</h5>
                    <h3>{{ $todayAppointments }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Pending</h5>
                    <h3>{{ $pendingAppointments }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Low Stock Items</h5>
                    <h3>{{ $lowStockItems }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <h5 class="card-title">Out of Stock</h5>
                    <h3>{{ $outOfStockItems }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.appointments') }}" class="btn btn-primary me-2">
                        <i class="fas fa-calendar"></i> Manage Appointments
                    </a>
                    <a href="{{ route('admin.inventory') }}" class="btn btn-success me-2">
                        <i class="fas fa-boxes"></i> Manage Inventory
                    </a>
                    <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#walkInModal">
                        <i class="fas fa-user-plus"></i> Add Walk-in Patient
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Appointments -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Appointments</h5>
                </div>
                <div class="card-body">
                    @if($recentAppointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentAppointments as $appointment)
                                    <tr>
                                        <td>{{ $appointment->patient_name }}</td>
                                        <td>{{ $appointment->appointment_date->format('M d') }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($appointment->status == 'pending') bg-warning
                                                @elseif($appointment->status == 'approved') bg-success
                                                @elseif($appointment->status == 'completed') bg-info
                                                @elseif($appointment->status == 'cancelled') bg-danger
                                                @else bg-secondary
                                                @endif">
                                                {{ ucfirst($appointment->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No recent appointments.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Low Stock Items -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Low Stock Items</h5>
                </div>
                <div class="card-body">
                    @if($lowStockInventory->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockInventory as $item)
                                    <tr>
                                        <td>{{ $item->item_name }}</td>
                                        <td>{{ $item->current_stock }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($item->status == 'out_of_stock') bg-danger
                                                @elseif($item->status == 'low_stock') bg-warning
                                                @else bg-success
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">All items are well stocked.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Walk-in Modal -->
<div class="modal fade" id="walkInModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Walk-in Patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.walk-in') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="patient_name" class="form-label">Patient Name *</label>
                        <input type="text" class="form-control" id="patient_name" name="patient_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="patient_phone" class="form-label">Phone Number *</label>
                        <input type="tel" class="form-control" id="patient_phone" name="patient_phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="patient_address" class="form-label">Address *</label>
                        <textarea class="form-control" id="patient_address" name="patient_address" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="service_type" class="form-label">Service *</label>
                        <select class="form-control" id="service_type" name="service_type" required>
                            <option value="">Select Service</option>
                            <option value="General Checkup">General Checkup</option>
                            <option value="Prenatal">Prenatal</option>
                            <option value="Medical Check-up">Medical Check-up</option>
                            <option value="Immunization">Immunization</option>
                            <option value="Family Planning">Family Planning</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Walk-in Patient</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
