@extends('layouts.app')

@section('content')
<div class="position-relative w-100" style="min-height: 80vh;">
    <div style="
        background: linear-gradient(rgba(255,255,255,0.77), rgba(255,255,255,0.73)), url('{{ asset('images/clinic-bg.jpg') }}') center/cover no-repeat;
        position:absolute; left:0; top:0; width:100%; height:100%; z-index:0;">
    </div>
    <div class="container position-relative py-5" style="z-index:1;">
        <h2 class="fw-bold mb-5 text-center">Our Healthcenter Services</h2>
        <div class="row justify-content-center g-4">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <img src="{{ asset('images/gen-checkup.jpg') }}" alt="General Checkup" class="img-fluid rounded mb-2" style="max-height:140px;">
                        <h5 class="card-title fw-bold mb-2">General Checkup</h5>
                        <p class="card-text mb-0">Comprehensive health checkups and consultation for all ages.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <img src="{{ asset('images/pre-natal.jpg') }}" alt="Prenatal" class="img-fluid rounded mb-2" style="max-height:140px;">
                        <h5 class="card-title fw-bold mb-2">Prenatal</h5>
                        <p class="card-text mb-0">Regular checkups and guidance for healthy pregnancy.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <img src="{{ asset('images/gen-checkup.jpg') }}" alt="Medical Checkup" class="img-fluid rounded mb-2" style="max-height:140px;">
                        <h5 class="card-title fw-bold mb-2">Medical Check-up</h5>
                        <p class="card-text mb-0">Routine assessments to monitor and maintain your health.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <img src="{{ asset('images/immunization.jpg') }}" alt="Immunization" class="img-fluid rounded mb-2" style="max-height:140px;">
                        <h5 class="card-title fw-bold mb-2">Immunization</h5>
                        <p class="card-text mb-0">Vaccinations for preventable diseases in children & adults.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <img src="{{ asset('images/family-planning.jpg') }}" alt="Family Planning" class="img-fluid rounded mb-2" style="max-height:140px;">
                        <h5 class="card-title fw-bold mb-2">Family Planning</h5>
                        <p class="card-text mb-0">Counseling & services for reproductive health.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
