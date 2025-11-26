@extends('admin.layout')

@section('title', 'Services - Barangay Health Center')
@section('page-title', 'Services Management')
@section('page-description', 'Manage health center services')

@section('content')
<div class="p-0 p-md-4">
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Add New Service
        </a>
    </div>

    @if($services->count() > 0)
        <x-card :no-padding="true">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Service Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $service)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $service->name }}</td>
                            <td>{{ Str::limit($service->description ?? 'N/A', 50) }}</td>
                            <td>
                                <span class="badge bg-{{ $service->active ? 'success' : 'secondary' }}">
                                    {{ $service->active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.services.destroy', $service) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this service?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($services->hasPages())
                <x-slot name="footer">
                    {{ $services->links('pagination::bootstrap-5') }}
                </x-slot>
            @endif
        </x-card>
    @else
        <x-card>
            <x-empty-state 
                icon="fa-briefcase-medical" 
                title="No services found" 
                description="Start by adding your first service to the system."
            >
                <x-slot name="action">
                    <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Add First Service
                    </a>
                </x-slot>
            </x-empty-state>
        </x-card>
    @endif
</div>
@endsection
