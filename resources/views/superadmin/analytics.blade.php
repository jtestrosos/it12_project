@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h2 class="fw-bold mb-4">System Analytics</h2>
        </div>
    </div>

    <!-- Appointment Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Appointment Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="text-center">
                                <h3 class="text-primary">{{ $appointmentStats['total'] }}</h3>
                                <p class="text-muted">Total Appointments</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h3 class="text-warning">{{ $appointmentStats['this_month'] }}</h3>
                                <p class="text-muted">This Month</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h3 class="text-info">{{ $appointmentStats['pending'] }}</h3>
                                <p class="text-muted">Pending</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h3 class="text-success">{{ $appointmentStats['completed'] }}</h3>
                                <p class="text-muted">Completed</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">User Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-primary">{{ $userStats['total'] }}</h3>
                                <p class="text-muted">Total Users</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-info">{{ $userStats['this_month'] }}</h3>
                                <p class="text-muted">New This Month</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-warning">{{ $userStats['admins'] }}</h3>
                                <p class="text-muted">Admins</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-success">{{ $userStats['patients'] }}</h3>
                                <p class="text-muted">Patients</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Statistics -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Inventory Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-primary">{{ $inventoryStats['total_items'] }}</h3>
                                <p class="text-muted">Total Items</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-warning">{{ $inventoryStats['low_stock'] }}</h3>
                                <p class="text-muted">Low Stock</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-danger">{{ $inventoryStats['out_of_stock'] }}</h3>
                                <p class="text-muted">Out of Stock</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
