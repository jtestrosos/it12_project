@extends('layouts.app')

@section('content')

<style>
    .service-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        cursor: pointer;
        border-radius: 12px;
    }

    .service-card:hover {
        transform: translateY(-6px);
        box-shadow: 0px 8px 25px rgba(0, 0, 0, 0.15);
        border-color: #0d6efd;
    }

    .service-card:active {
        transform: scale(0.97);
    }

    .service-image {
        transition: transform 0.3s ease;
        border-radius: 10px;
    }

    .service-card:hover .service-image {
        transform: scale(1.08);
    }
</style>

<div class="position-relative w-100" style="min-height: 80vh;">
    <div style="
        background: linear-gradient(rgba(255,255,255,0.77), rgba(255,255,255,0.73)), url('{{ asset('images/clinic-bg.jpg') }}') center/cover no-repeat;
        position:absolute; left:0; top:0; width:100%; height:100%; z-index:0;">
    </div>

    <div class="container position-relative py-5" style="z-index:1;">
        <h2 class="fw-bold mb-5 text-center">Our Healthcenter Services</h2>

        <div class="row justify-content-center g-4">

            <!-- GENERAL CHECKUP -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 service-card">
                    <div class="card-body text-center">
                        <img src="{{ asset('images/gen-checkup.jpg') }}" 
                             alt="General Checkup" 
                             class="img-fluid mb-2 service-image" 
                             style="max-height:140px;">
                        <h5 class="card-title fw-bold mb-1">General Checkup</h5>
                        <p class="card-text mb-0">Comprehensive health checkups and consultation for all ages.</p>
                    </div>
                </div>
            </div>

            <!-- PRENATAL -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 service-card">
                    <div class="card-body text-center">
                        <img src="{{ asset('images/pre-natal.jpg') }}" 
                             alt="Prenatal" 
                             class="img-fluid mb-2 service-image" 
                             style="max-height:140px;">
                        <h5 class="card-title fw-bold mb-1">Prenatal</h5>
                        <p class="card-text mb-0">Regular checkups and guidance for healthy pregnancy.</p>
                    </div>
                </div>
            </div>

            <!-- MEDICAL CHECKUP -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 service-card">
                    <div class="card-body text-center">
                        <img src="{{ asset('images/gen-checkup.jpg') }}" 
                             alt="Medical Checkup" 
                             class="img-fluid mb-2 service-image" 
                             style="max-height:140px;">
                        <h5 class="card-title fw-bold mb-1">Medical Check-up</h5>
                        <p class="card-text mb-0">Routine assessments to monitor and maintain your health.</p>
                    </div>
                </div>
            </div>

            <!-- IMMUNIZATION -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 service-card">
                    <div class="card-body text-center">
                        <img src="{{ asset('images/immunization.jpg') }}" 
                             alt="Immunization" 
                             class="img-fluid mb-2 service-image" 
                             style="max-height:140px;">
                        <h5 class="card-title fw-bold mb-1">Immunization</h5>
                        <p class="card-text mb-0">Vaccinations for preventable diseases in children & adults.</p>
                    </div>
                </div>
            </div>

            <!-- FAMILY PLANNING -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 service-card">
                    <div class="card-body text-center">
                        <img src="{{ asset('images/family-planning.jpg') }}" 
                             alt="Family Planning" 
                             class="img-fluid mb-2 service-image" 
                             style="max-height:140px;">
                        <h5 class="card-title fw-bold mb-1">Family Planning</h5>
                        <p class="card-text mb-0">Counseling & services for reproductive health.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
