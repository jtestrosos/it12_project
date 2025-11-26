@extends('admin.layout')

@section('title', 'Edit Service - Barangay Health Center')
@section('page-title', 'Edit Service')
@section('page-description', 'Update service details')

@section('content')
<div class="p-0 p-md-4">
    <x-card>
        <form method="POST" action="{{ route('admin.services.update', $service) }}">
            @csrf
            @method('PUT')
            
            <x-input 
                name="name" 
                label="Service Name" 
                :value="$service->name" 
                required 
            />

            <div class="mb-3">
                <label for="description" class="form-label fw-medium">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="3">{{ old('description', $service->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>



            <div class="mb-4 form-check">
                <input type="checkbox" class="form-check-input" id="active" name="active" value="1"
                       {{ old('active', $service->active) ? 'checked' : '' }}>
                <label class="form-check-label" for="active">
                    Active (available for booking)
                </label>
            </div>

            <div class="d-flex gap-2">
                <x-button type="submit" icon="fas fa-save">
                    Update Service
                </x-button>
                <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i> Cancel
                </a>
            </div>
        </form>
    </x-card>
</div>
@endsection
