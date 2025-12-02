@extends('layouts.admin')

@section('title', 'Medical Profile - Patient Portal')
@section('page-title', 'Medical Profile')

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
    <a class="nav-link @if(request()->routeIs('patient.medical-profile')) active @endif"
        href="{{ route('patient.medical-profile') }}" data-tooltip="Medical Profile">
        <i class="fas fa-file-medical"></i> <span class="sidebar-content">Medical Profile</span>
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
        .form-section {
            background: #fafafa;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
        }

        body.bg-dark .form-section {
            background: #1e2124;
            border-color: #2a2f35;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .form-section h6 {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #007bff;
        }

        body.bg-dark .form-section h6 {
            color: #e6e6e6;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        body.bg-dark .form-label {
            color: #b0b0b0;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 0.75rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        body.bg-dark .form-control,
        body.bg-dark .form-select {
            background-color: #2a2f35;
            border-color: #3a3f45;
            color: #e6e6e6;
        }

        body.bg-dark .form-control:focus,
        body.bg-dark .form-select:focus {
            background-color: #2a2f35;
            border-color: #007bff;
            color: #e6e6e6;
        }
    </style>
@endsection

@section('content')
    @if($user->age < 6)
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            The Individual Treatment Record is available for patients 6 years and older. You are currently {{ $user->age }}
            years old.
        </div>
    @else
        <form action="{{ route('patient.medical-profile.update') }}" method="POST">
            @csrf

            <!-- Treatment Record Section -->
            <div class="form-section">
                <h6><i class="fas fa-file-medical me-2"></i>Individual Treatment Record</h6>
                <p class="text-muted small mb-3">
                    <i class="fas fa-info-circle me-1"></i>
                    Update your medical information. All fields are optional.
                </p>

                <!-- Demographics -->
                <div class="mb-4">
                    <h6 class="text-muted small fw-bold">Family Information</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="mother_name" class="form-label">Mother's Name</label>
                            <input type="text" class="form-control" id="mother_name" name="mother_name"
                                value="{{ old('mother_name', $user->mother_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="father_name" class="form-label">Father's Name</label>
                            <input type="text" class="form-control" id="father_name" name="father_name"
                                value="{{ old('father_name', $user->father_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="religion" class="form-label">Religion</label>
                            <input type="text" class="form-control" id="religion" name="religion"
                                value="{{ old('religion', $user->religion) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="marital_status" class="form-label">Marital Status</label>
                            <select class="form-control" id="marital_status" name="marital_status">
                                <option value="">Select Status</option>
                                <option value="single" {{ old('marital_status', $user->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                                <option value="married" {{ old('marital_status', $user->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                                <option value="widowed" {{ old('marital_status', $user->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                <option value="separated" {{ old('marital_status', $user->marital_status) == 'separated' ? 'selected' : '' }}>Separated</option>
                                <option value="co-habitation" {{ old('marital_status', $user->marital_status) == 'co-habitation' ? 'selected' : '' }}>Co-habitation</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="educational_attainment" class="form-label">Educational Attainment</label>
                            <input type="text" class="form-control" id="educational_attainment" name="educational_attainment"
                                value="{{ old('educational_attainment', $user->educational_attainment) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="occupation" class="form-label">Occupation</label>
                            <input type="text" class="form-control" id="occupation" name="occupation"
                                value="{{ old('occupation', $user->occupation) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="accompanying_person" class="form-label">Accompanying Person</label>
                            <input type="text" class="form-control" id="accompanying_person" name="accompanying_person"
                                value="{{ old('accompanying_person', $user->accompanying_person) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="accompanying_relationship" class="form-label">Relationship</label>
                            <input type="text" class="form-control" id="accompanying_relationship"
                                name="accompanying_relationship"
                                value="{{ old('accompanying_relationship', $user->accompanying_relationship) }}">
                        </div>
                    </div>

                    <!-- Spouse Information (shown if married) -->
                    <div id="spouseInfo" class="mt-3"
                        style="display: {{ old('marital_status', $user->marital_status) == 'married' ? 'block' : 'none' }}">
                        <h6 class="text-muted small fw-bold mt-3">Spouse Information</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="spouse_name" class="form-label">Spouse Name</label>
                                <input type="text" class="form-control" id="spouse_name" name="spouse_name"
                                    value="{{ old('spouse_name', $user->spouse_name) }}">
                            </div>
                            <div class="col-md-4">
                                <label for="spouse_age" class="form-label">Spouse Age</label>
                                <input type="number" class="form-control" id="spouse_age" name="spouse_age"
                                    value="{{ old('spouse_age', $user->spouse_age) }}">
                            </div>
                            <div class="col-md-4">
                                <label for="spouse_occupation" class="form-label">Spouse Occupation</label>
                                <input type="text" class="form-control" id="spouse_occupation" name="spouse_occupation"
                                    value="{{ old('spouse_occupation', $user->spouse_occupation) }}">
                            </div>
                            @if($user->gender == 'female')
                                <div class="col-md-12">
                                    <label for="maiden_name" class="form-label">Maiden Name</label>
                                    <input type="text" class="form-control" id="maiden_name" name="maiden_name"
                                        value="{{ old('maiden_name', $user->maiden_name) }}">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Personal/Social History -->
                <div class="mb-4">
                    <h6 class="text-muted small fw-bold">Personal/Social History</h6>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="smoker" name="smoker" value="1" {{ old('smoker', $user->smoker) ? 'checked' : '' }}>
                                <label class="form-check-label" for="smoker">Smoker</label>
                            </div>
                            <input type="text" class="form-control form-control-sm mt-1" id="smoker_packs_per_year"
                                name="smoker_packs_per_year" placeholder="Packs/year"
                                value="{{ old('smoker_packs_per_year', $user->smoker_packs_per_year) }}">
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="drinks_alcohol" name="drinks_alcohol"
                                    value="1" {{ old('drinks_alcohol', $user->drinks_alcohol) ? 'checked' : '' }}>
                                <label class="form-check-label" for="drinks_alcohol">Drinks Alcohol</label>
                            </div>
                            <input type="text" class="form-control form-control-sm mt-1" id="alcohol_specify"
                                name="alcohol_specify" placeholder="Specify"
                                value="{{ old('alcohol_specify', $user->alcohol_specify) }}">
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="illicit_drug_use" name="illicit_drug_use"
                                    value="1" {{ old('illicit_drug_use', $user->illicit_drug_use) ? 'checked' : '' }}>
                                <label class="form-check-label" for="illicit_drug_use">Illicit Drug Use</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="multiple_sexual_partners"
                                    name="multiple_sexual_partners" value="1" {{ old('multiple_sexual_partners', $user->multiple_sexual_partners) ? 'checked' : '' }}>
                                <label class="form-check-label" for="multiple_sexual_partners">Multiple Sexual Partners</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_pwd" name="is_pwd" value="1" {{ old('is_pwd', $user->is_pwd) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_pwd">PWD</label>
                            </div>
                            <input type="text" class="form-control form-control-sm mt-1" id="pwd_specify" name="pwd_specify"
                                placeholder="Specify" value="{{ old('pwd_specify', $user->pwd_specify) }}">
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="has_sti" name="has_sti" value="1" {{ old('has_sti', $user->has_sti) ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_sti">STI</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="has_allergies" name="has_allergies"
                                    value="1" {{ old('has_allergies', $user->has_allergies) ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_allergies">Allergies</label>
                            </div>
                            <input type="text" class="form-control form-control-sm mt-1" id="allergies_specify"
                                name="allergies_specify" placeholder="Specify allergies"
                                value="{{ old('allergies_specify', $user->allergies_specify) }}">
                        </div>
                        <div class="col-md-12">
                            <label for="social_history_others" class="form-label">Others</label>
                            <textarea class="form-control" id="social_history_others" name="social_history_others"
                                rows="2">{{ old('social_history_others', $user->social_history_others) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Family History -->
                <div class="mb-4">
                    <h6 class="text-muted small fw-bold">Family History</h6>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="family_hypertension"
                                    name="family_hypertension" value="1" {{ old('family_hypertension', $user->family_hypertension) ? 'checked' : '' }}>
                                <label class="form-check-label" for="family_hypertension">Hypertension</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="family_diabetes" name="family_diabetes"
                                    value="1" {{ old('family_diabetes', $user->family_diabetes) ? 'checked' : '' }}>
                                <label class="form-check-label" for="family_diabetes">Diabetes</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="family_goiter" name="family_goiter"
                                    value="1" {{ old('family_goiter', $user->family_goiter) ? 'checked' : '' }}>
                                <label class="form-check-label" for="family_goiter">Goiter</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="family_cancer" name="family_cancer"
                                    value="1" {{ old('family_cancer', $user->family_cancer) ? 'checked' : '' }}>
                                <label class="form-check-label" for="family_cancer">Cancer</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="family_history_others" class="form-label">Others</label>
                            <textarea class="form-control" id="family_history_others" name="family_history_others"
                                rows="2">{{ old('family_history_others', $user->family_history_others) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Patient Medical History -->
                <div class="mb-4">
                    <h6 class="text-muted small fw-bold">Patient Medical History</h6>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="history_uti" name="history_uti" value="1" {{ old('history_uti', $user->history_uti) ? 'checked' : '' }}>
                                <label class="form-check-label" for="history_uti">UTI</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="history_hypertension"
                                    name="history_hypertension" value="1" {{ old('history_hypertension', $user->history_hypertension) ? 'checked' : '' }}>
                                <label class="form-check-label" for="history_hypertension">Hypertension</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="history_diabetes" name="history_diabetes"
                                    value="1" {{ old('history_diabetes', $user->history_diabetes) ? 'checked' : '' }}>
                                <label class="form-check-label" for="history_diabetes">Diabetes</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="history_goiter" name="history_goiter"
                                    value="1" {{ old('history_goiter', $user->history_goiter) ? 'checked' : '' }}>
                                <label class="form-check-label" for="history_goiter">Goiter</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="history_cancer" name="history_cancer"
                                    value="1" {{ old('history_cancer', $user->history_cancer) ? 'checked' : '' }}>
                                <label class="form-check-label" for="history_cancer">Cancer</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="history_tuberculosis"
                                    name="history_tuberculosis" value="1" {{ old('history_tuberculosis', $user->history_tuberculosis) ? 'checked' : '' }}>
                                <label class="form-check-label" for="history_tuberculosis">Tuberculosis</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="medical_history_others" class="form-label">Others</label>
                            <textarea class="form-control" id="medical_history_others" name="medical_history_others"
                                rows="2">{{ old('medical_history_others', $user->medical_history_others) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Previous Surgeries & Maintenance Medicine -->
                <div class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="previous_surgeries" class="form-label">Previous Surgeries</label>
                            <textarea class="form-control" id="previous_surgeries" name="previous_surgeries" rows="3"
                                placeholder="List any previous surgeries...">{{ old('previous_surgeries', $user->previous_surgeries) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="maintenance_medicine" class="form-label">Maintenance Medicine</label>
                            <textarea class="form-control" id="maintenance_medicine" name="maintenance_medicine" rows="3"
                                placeholder="List current medications...">{{ old('maintenance_medicine', $user->maintenance_medicine) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Immunization Section -->
            <div class="form-section">
                <h6><i class="fas fa-syringe me-2"></i>Immunization Records</h6>

                <div class="row g-3">
                    <div class="col-md-6">
                        <h6 class="text-muted small fw-bold">For Children</h6>
                        <div class="row g-2">
                            @php
                                $childVaccines = ['BCG', 'DPT1', 'DPT2', 'DPT3', 'OPV1', 'OPV2', 'OPV3', 'Measles', 'Hepatitis B1', 'Hepatitis B2', 'Hepatitis B3', 'Hepatitis A'];
                            @endphp
                            @foreach($childVaccines as $vaccine)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input"
                                            id="imm_{{ strtolower(str_replace(' ', '_', $vaccine)) }}"
                                            name="imm_{{ strtolower(str_replace(' ', '_', $vaccine)) }}" value="1" {{ old('imm_' . strtolower(str_replace(' ', '_', $vaccine)), $user->immunization->{strtolower(str_replace(' ', '_', $vaccine))} ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label"
                                            for="imm_{{ strtolower(str_replace(' ', '_', $vaccine)) }}">{{ $vaccine }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-muted small fw-bold">For Elderly and Immunocompromised</h6>
                        <div class="row g-2">
                            @php
                                $elderlyVaccines = ['Varicella', 'HPV', 'Pneumococcal', 'MMR', 'Flu Vaccine', 'None'];
                            @endphp
                            @foreach($elderlyVaccines as $vaccine)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input"
                                            id="imm_{{ strtolower(str_replace(' ', '_', $vaccine)) }}"
                                            name="imm_{{ strtolower(str_replace(' ', '_', $vaccine)) }}" value="1" {{ old('imm_' . strtolower(str_replace(' ', '_', $vaccine)), $user->immunization->{strtolower(str_replace(' ', '_', $vaccine))} ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label"
                                            for="imm_{{ strtolower(str_replace(' ', '_', $vaccine)) }}">{{ $vaccine }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- COVID-19 Immunization -->
                <div class="mt-4">
                    <h6 class="text-muted small fw-bold">COVID-19 Immunization Status</h6>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="covid_vaccine_name" class="form-label">Vaccine Name</label>
                            <input type="text" class="form-control" id="covid_vaccine_name" name="covid_vaccine_name"
                                value="{{ old('covid_vaccine_name', $user->immunization->covid_vaccine_name ?? '') }}"
                                placeholder="e.g., Pfizer, Moderna, Sinovac">
                        </div>
                        <div class="col-md-3">
                            <label for="covid_first_dose" class="form-label">1st Dose</label>
                            <input type="date" class="form-control" id="covid_first_dose" name="covid_first_dose"
                                value="{{ old('covid_first_dose', $user->immunization->covid_first_dose ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="covid_second_dose" class="form-label">2nd Dose</label>
                            <input type="date" class="form-control" id="covid_second_dose" name="covid_second_dose"
                                value="{{ old('covid_second_dose', $user->immunization->covid_second_dose ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="covid_booster1" class="form-label">Booster 1</label>
                            <input type="date" class="form-control" id="covid_booster1" name="covid_booster1"
                                value="{{ old('covid_booster1', $user->immunization->covid_booster1 ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="covid_booster2" class="form-label">Booster 2</label>
                            <input type="date" class="form-control" id="covid_booster2" name="covid_booster2"
                                value="{{ old('covid_booster2', $user->immunization->covid_booster2 ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Consent Section -->
            <div class="form-section">
                <h6><i class="fas fa-file-signature me-2"></i>Patient Consent (Pagtugot sa Pasyente)</h6>
                <div class="alert alert-info">
                    <p class="mb-2"><strong>IN ENGLISH:</strong></p>
                    <p class="small mb-2">I have read and understand the ITR (Individual Treatment Record) after I have been
                        made aware of its contents. During informational conversation, I was informed in a comprehensive way
                        about the need and importance of the Primary Care Benefit Package (PCB), Konsulta Program, eKonsulta
                        System, iClinicSys (Integrated Clinic Information System) by the CHO DHO/UHC representative. All my
                        questions during the said conversation were addressed accordingly and I have also been given enough time
                        to decide on this matter.</p>
                    <p class="small mb-2">Furthermore, I permit CHO DHO/UHC to encode the information concerning my person and
                        the collected data regarding my health status and consultations conducted by the same on the information
                        system as mentioned above and provide the same to the Philippine Health Information Exchange - Lite
                        (PHIE Lite), the Department of Health (DOH) National Health Data Reporting and PhilhealthKonsulta
                        Program.</p>

                    <p class="mb-2 mt-3"><strong>SA BISAYA:</strong></p>
                    <p class="small mb-0">Ako nakabasa ug nakasabot sa ITR (Individual Treatment Record) paghuman naa ko
                        gipahibalo sa sulod niini ug gipasabot sa importansya sa Primary Care Benefits Package (PCB), Konsulta
                        Program, eKonsulta System ug iClinicsys (Integrated Clinic Information System) sa taga- CHO DHO/UHC.
                        Tanan nakong pangutana kay natubag ug ako na hatagan ug saktong panahon para mahatag saakoa ang
                        pagtugot.
                        Ako pud gihatagan ug permission na isulod ang impormasyon sa akong pagkatao, sa estado sa akong panlawas
                        ug sa nahimo ug mahimong konsultasyon na mga information systems na nahisgot ug ang maong impormasyon
                        ihatag sa Philippine Health Information Exchange - Lte (PHIE Lite), sa Department of Health (DOH)
                        National Health Data Reporting ug Phil Health Konsulta Program.
                        Ang resulta saakong konsultasyon ug estado saakong panglawas kay pwede nako mapangayo o saakong tag
                        tungod. Pwede ra pud nako ikansel akining gihatag nako pagtugot sa CHO DHO/UHC na walay ihatag na rason
                        ug walay maski unsa na desbintaha saakong medical napagtambal..</p>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="consent_signed" name="consent_signed" value="1" {{ old('consent_signed', $user->consent_signed) ? 'checked' : '' }} required>
                    <label class="form-check-label fw-bold" for="consent_signed">
                        I have read and agree to the terms above / Nakabasa ug miuyon sa mga termino sa ibabaw
                    </label>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('patient.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Save Medical Profile
                </button>
            </div>
        </form>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Toggle spouse info based on marital status
            const maritalStatus = document.getElementById('marital_status');
            const spouseInfo = document.getElementById('spouseInfo');

            if (maritalStatus && spouseInfo) {
                maritalStatus.addEventListener('change', function () {
                    spouseInfo.style.display = this.value === 'married' ? 'block' : 'none';
                });
            }

            // Show success/error messages
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