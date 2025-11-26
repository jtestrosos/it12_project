@extends('layouts.app')

@section('content')

<style>
    .service-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        border-radius: 12px;
    }
    .service-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.15);
    }
    .service-card:active {
        transform: scale(0.98);
    }
    .service-image {
        transition: transform 0.4s ease;
        border-radius: 10px;
    }
    .service-card:hover .service-image {
        transform: scale(1.1);
    }
</style>

<div class="position-relative w-100" style="min-height: 80vh;">
    <div style="
        background: linear-gradient(rgba(255,255,255,0.77), rgba(255,255,255,0.73)), 
                    url('{{ asset('images/clinic-bg.jpg') }}') center/cover no-repeat;
        position:absolute; left:0; top:0; width:100%; height:100%; z-index:0;">
    </div>

    <div class="container position-relative py-5" style="z-index:1;">
        <h2 class="fw-bold mb-5 text-center text-primary">Choose a Service to Get Started</h2>

        <div class="row justify-content-center g-4">

            @php
                $services = [
                    ['name' => 'General Checkup', 'img' => 'gen-checkup.jpg', 'desc' => 'Comprehensive health checkups and consultation for all ages.'],
                    ['name' => 'Prenatal Care', 'img' => 'pre-natal.jpg', 'desc' => 'Regular checkups and guidance for a healthy pregnancy journey.'],
                    ['name' => 'Medical Check-up', 'img' => 'gen-checkup.jpg', 'desc' => 'Routine health assessments to keep you in top shape.'],
                    ['name' => 'Immunization', 'img' => 'immunization.jpg', 'desc' => 'Vaccines for children and adults to prevent diseases.'],
                    ['name' => 'Family Planning', 'img' => 'family-planning.jpg', 'desc' => 'Counseling and services for reproductive health & planning.'],
                ];
            @endphp

            @foreach($services as $service)
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 service-card" 
                         data-bs-toggle="modal" 
                         data-bs-target="#serviceModal"
                         data-service-name="{{ $service['name'] }}"
                         data-service-desc="{{ $service['desc'] }}"
                         data-service-img="{{ asset('images/' . $service['img']) }}">
                        
                        <div class="card-body text-center p-4">
                            <img src="{{ asset('images/' . $service['img']) }}" 
                                 alt="{{ $service['name'] }}" 
                                 class="img-fluid mb-3 service-image" 
                                 style="max-height:140px; object-fit: cover;">
                            <h5 class="card-title fw-bold text-primary">{{ $service['name'] }}</h5>
                            <p class="card-text text-muted small">{{ Str::limit($service['desc'], 70) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content overflow-hidden border-0 shadow-lg">
            <div class="row g-0">
                <div class="col-md-5 bg-primary text-white d-flex align-items-center justify-content-center p-4">
                    <img id="modal-image" src="" alt="" class="img-fluid rounded" style="max-height: 280px;">
                </div>
                <div class="col-md-7">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold text-primary" id="serviceModalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-0">
                        <p id="modal-description" class="text-muted"></p>
                        <div class="alert alert-info small mt-3">
                            You're one step away from booking this service!
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <a href="{{ route('register') }}" class="btn btn-primary px-4">
                            Register Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('serviceModal');
    modal.addEventListener('show.bs.modal', function (event) {
        const card = event.relatedTarget; // Card that triggered the modal

        const name = card.getAttribute('data-service-name');
        const desc = card.getAttribute('data-service-desc');
        const img = card.getAttribute('data-service-img');

        // Update modal content
        modal.querySelector('#serviceModalLabel').textContent = name;
        modal.querySelector('#modal-description').textContent = desc;
        modal.querySelector('#modal-image').src = img;
    });
});
</script>

@endsection