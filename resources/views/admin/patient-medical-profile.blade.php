@extends('layouts.admin')

@section('title', 'Patient Medical Profile - Admin Portal')
@section('page-title', 'Patient Medical Profile: ' . $patient->name)

@section('content')
    @if($patient->age < 6)
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            The Individual Treatment Record is available for patients 6 years and older. This patient is currently {{ $patient->age }} years old.
        </div>
        <a href="{{ route('admin.patients') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Patients
        </a>
    @else
        <div class="mb-3">
            <a href="{{ route('admin.patients') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Patients
            </a>
        </div>

        <form action="{{ route('admin.patient.medical-profile.update', $patient) }}" method="POST">
            @csrf
            
            <!-- Patient Info Card -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-user me-2"></i>Patient Information</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Name:</strong> {{ $patient->name }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Age:</strong> {{ $patient->age }} years</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Gender:</strong> {{ ucfirst($patient->gender) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Treatment Record Section -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-file-medical me-2"></i>Individual Treatment Record</h5>
                </div>
                <div class="card-body">
                    <!-- Demographics -->
                    <div class="mb-4">
                        <h6 class="text-muted fw-bold">Family Information</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="mother_name" class="form-label">Mother's Name</label>
                                <input type="text" class="form-control" id="mother_name" name="mother_name" 
                                    value="{{ old('mother_name', $patient->mother_name) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="father_name" class="form-label">Father's Name</label>
                                <input type="text" class="form-control" id="father_name" name="father_name" 
                                    value="{{ old('father_name', $patient->father_name) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="religion" class="form-label">Religion</label>
                                <input type="text" class="form-control" id="religion" name="religion" 
                                    value="{{ old('religion', $patient->religion) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="marital_status" class="form-label">Marital Status</label>
                                <select class="form-control" id="marital_status" name="marital_status">
                                    <option value="">Select Status</option>
                                    <option value="single" {{ old('marital_status', $patient->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                                    <option value="married" {{ old('marital_status', $patient->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                                    <option value="widowed" {{ old('marital_status', $patient->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                    <option value="separated" {{ old('marital_status', $patient->marital_status) == 'separated' ? 'selected' : '' }}>Separated</option>
                                    <option value="co-habitation" {{ old('marital_status', $patient->marital_status) == 'co-habitation' ? 'selected' : '' }}>Co-habitation</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="educational_attainment" class="form-label">Educational Attainment</label>
                                <input type="text" class="form-control" id="educational_attainment" name="educational_attainment" 
                                    value="{{ old('educational_attainment', $patient->educational_attainment) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="occupation" class="form-label">Occupation</label>
                                <input type="text" class="form-control" id="occupation" name="occupation" 
                                    value="{{ old('occupation', $patient->occupation) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="accompanying_person" class="form-label">Accompanying Person</label>
                                <input type="text" class="form-control" id="accompanying_person" name="accompanying_person" 
                                    value="{{ old('accompanying_person', $patient->accompanying_person) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="accompanying_relationship" class="form-label">Relationship</label>
                                <input type="text" class="form-control" id="accompanying_relationship" name="accompanying_relationship" 
                                    value="{{ old('accompanying_relationship', $patient->accompanying_relationship) }}">
                            </div>
                        </div>

                        <!-- Spouse Information -->
                        <div id="spouseInfo" class="mt-3" style="display: {{ old('marital_status', $patient->marital_status) == 'married' ? 'block' : 'none' }}">
                            <h6 class="text-muted fw-bold mt-3">Spouse Information</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="spouse_name" class="form-label">Spouse Name</label>
                                    <input type="text" class="form-control" id="spouse_name" name="spouse_name" 
                                        value="{{ old('spouse_name', $patient->spouse_name) }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="spouse_age" class="form-label">Spouse Age</label>
                                    <input type="number" class="form-control" id="spouse_age" name="spouse_age" 
                                        value="{{ old('spouse_age', $patient->spouse_age) }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="spouse_occupation" class="form-label">Spouse Occupation</label>
                                    <input type="text" class="form-control" id="spouse_occupation" name="spouse_occupation" 
                                        value="{{ old('spouse_occupation', $patient->spouse_occupation) }}">
                                </div>
                                @if($patient->gender == 'female')
                                <div class="col-md-12">
                                    <label for="maiden_name" class="form-label">Maiden Name</label>
                                    <input type="text" class="form-control" id="maiden_name" name="maiden_name" 
                                        value="{{ old('maiden_name', $patient->maiden_name) }}">
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Personal/Social History -->
                    <div class="mb-4">
                        <h6 class="text-muted fw-bold">Personal/Social History</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="smoker" name="smoker" value="1" 
                                        {{ old('smoker', $patient->smoker) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="smoker">Smoker</label>
                                </div>
                                <input type="text" class="form-control form-control-sm mt-1" id="smoker_packs_per_year" 
                                    name="smoker_packs_per_year" placeholder="Packs/year" 
                                    value="{{ old('smoker_packs_per_year', $patient->smoker_packs_per_year) }}">
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="drinks_alcohol" name="drinks_alcohol" value="1" 
                                        {{ old('drinks_alcohol', $patient->drinks_alcohol) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="drinks_alcohol">Drinks Alcohol</label>
                                </div>
                                <input type="text" class="form-control form-control-sm mt-1" id="alcohol_specify" 
                                    name="alcohol_specify" placeholder="Specify" 
                                    value="{{ old('alcohol_specify', $patient->alcohol_specify) }}">
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="illicit_drug_use" name="illicit_drug_use" value="1" 
                                        {{ old('illicit_drug_use', $patient->illicit_drug_use) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="illicit_drug_use">Illicit Drug Use</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="multiple_sexual_partners" name="multiple_sexual_partners" value="1" 
                                        {{ old('multiple_sexual_partners', $patient->multiple_sexual_partners) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="multiple_sexual_partners">Multiple Sexual Partners</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_pwd" name="is_pwd" value="1" 
                                        {{ old('is_pwd', $patient->is_pwd) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_pwd">PWD</label>
                                </div>
                                <input type="text" class="form-control form-control-sm mt-1" id="pwd_specify" 
                                    name="pwd_specify" placeholder="Specify" 
                                    value="{{ old('pwd_specify', $patient->pwd_specify) }}">
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="has_sti" name="has_sti" value="1" 
                                        {{ old('has_sti', $patient->has_sti) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_sti">STI</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="has_allergies" name="has_allergies" value="1" 
                                        {{ old('has_allergies', $patient->has_allergies) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_allergies">Allergies</label>
                                </div>
                                <input type="text" class="form-control form-control-sm mt-1" id="allergies_specify" 
                                    name="allergies_specify" placeholder="Specify allergies" 
                                    value="{{ old('allergies_specify', $patient->allergies_specify) }}">
                            </div>
                            <div class="col-md-12">
                                <label for="social_history_others" class="form-label">Others</label>
                                <textarea class="form-control" id="social_history_others" name="social_history_others" rows="2">{{ old('social_history_others', $patient->social_history_others) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Family History -->
                    <div class="mb-4">
                        <h6 class="text-muted fw-bold">Family History</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="family_hypertension" name="family_hypertension" value="1" 
                                        {{ old('family_hypertension', $patient->family_hypertension) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="family_hypertension">Hypertension</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="family_diabetes" name="family_diabetes" value="1" 
                                        {{ old('family_diabetes', $patient->family_diabetes) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="family_diabetes">Diabetes</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="family_goiter" name="family_goiter" value="1" 
                                        {{ old('family_goiter', $patient->family_goiter) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="family_goiter">Goiter</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="family_cancer" name="family_cancer" value="1" 
                                        {{ old('family_cancer', $patient->family_cancer) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="family_cancer">Cancer</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="family_history_others" class="form-label">Others</label>
                                <textarea class="form-control" id="family_history_others" name="family_history_others" rows="2">{{ old('family_history_others', $patient->family_history_others) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Patient Medical History -->
                    <div class="mb-4">
                        <h6 class="text-muted fw-bold">Patient Medical History</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="history_uti" name="history_uti" value="1" 
                                        {{ old('history_uti', $patient->history_uti) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="history_uti">UTI</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="history_hypertension" name="history_hypertension" value="1" 
                                        {{ old('history_hypertension', $patient->history_hypertension) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="history_hypertension">Hypertension</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="history_diabetes" name="history_diabetes" value="1" 
                                        {{ old('history_diabetes', $patient->history_diabetes) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="history_diabetes">Diabetes</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="history_goiter" name="history_goiter" value="1" 
                                        {{ old('history_goiter', $patient->history_goiter) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="history_goiter">Goiter</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="history_cancer" name="history_cancer" value="1" 
                                        {{ old('history_cancer', $patient->history_cancer) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="history_cancer">Cancer</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="history_tuberculosis" name="history_tuberculosis" value="1" 
                                        {{ old('history_tuberculosis', $patient->history_tuberculosis) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="history_tuberculosis">Tuberculosis</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="medical_history_others" class="form-label">Others</label>
                                <textarea class="form-control" id="medical_history_others" name="medical_history_others" rows="2">{{ old('medical_history_others', $patient->medical_history_others) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Previous Surgeries & Maintenance Medicine -->
                    <div class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="previous_surgeries" class="form-label">Previous Surgeries</label>
                                <textarea class="form-control" id="previous_surgeries" name="previous_surgeries" rows="3" 
                                    placeholder="List any previous surgeries...">{{ old('previous_surgeries', $patient->previous_surgeries) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="maintenance_medicine" class="form-label">Maintenance Medicine</label>
                                <textarea class="form-control" id="maintenance_medicine" name="maintenance_medicine" rows="3" 
                                    placeholder="List current medications...">{{ old('maintenance_medicine', $patient->maintenance_medicine) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Immunization Section -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-syringe me-2"></i>Immunization Records</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="text-muted fw-bold">For Children</h6>
                            <div class="row g-2">
                                @php
                                    $childVaccines = ['BCG', 'DPT1', 'DPT2', 'DPT3', 'OPV1', 'OPV2', 'OPV3', 'Measles', 'Hepatitis B1', 'Hepatitis B2', 'Hepatitis B3', 'Hepatitis A'];
                                @endphp
                                @foreach($childVaccines as $vaccine)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="imm_{{ strtolower(str_replace(' ', '_', $vaccine)) }}" 
                                            name="imm_{{ strtolower(str_replace(' ', '_', $vaccine)) }}" value="1" 
                                            {{ old('imm_' . strtolower(str_replace(' ', '_', $vaccine)), $patient->immunization->{strtolower(str_replace(' ', '_', $vaccine))} ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="imm_{{ strtolower(str_replace(' ', '_', $vaccine)) }}">{{ $vaccine }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted fw-bold">For Elderly and Immunocompromised</h6>
                            <div class="row g-2">
                                @php
                                    $elderlyVaccines = ['Varicella', 'HPV', 'Pneumococcal', 'MMR', 'Flu Vaccine', 'None'];
                                @endphp
                                @foreach($elderlyVaccines as $vaccine)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="imm_{{ strtolower(str_replace(' ', '_', $vaccine)) }}" 
                                            name="imm_{{ strtolower(str_replace(' ', '_', $vaccine)) }}" value="1" 
                                            {{ old('imm_' . strtolower(str_replace(' ', '_', $vaccine)), $patient->immunization->{strtolower(str_replace(' ', '_', $vaccine))} ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="imm_{{ strtolower(str_replace(' ', '_', $vaccine)) }}">{{ $vaccine }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- COVID-19 Immunization -->
                    <div class="mt-4">
                        <h6 class="text-muted fw-bold">COVID-19 Immunization Status</h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="covid_vaccine_name" class="form-label">Vaccine Name</label>
                                <input type="text" class="form-control" id="covid_vaccine_name" name="covid_vaccine_name" 
                                    value="{{ old('covid_vaccine_name', $patient->immunization->covid_vaccine_name ?? '') }}" 
                                    placeholder="e.g., Pfizer, Moderna, Sinovac">
                            </div>
                            <div class="col-md-3">
                                <label for="covid_first_dose" class="form-label">1st Dose</label>
                                <input type="date" class="form-control" id="covid_first_dose" name="covid_first_dose" 
                                    value="{{ old('covid_first_dose', $patient->immunization->covid_first_dose ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="covid_second_dose" class="form-label">2nd Dose</label>
                                <input type="date" class="form-control" id="covid_second_dose" name="covid_second_dose" 
                                    value="{{ old('covid_second_dose', $patient->immunization->covid_second_dose ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="covid_booster1" class="form-label">Booster 1</label>
                                <input type="date" class="form-control" id="covid_booster1" name="covid_booster1" 
                                    value="{{ old('covid_booster1', $patient->immunization->covid_booster1 ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="covid_booster2" class="form-label">Booster 2</label>
                                <input type="date" class="form-control" id="covid_booster2" name="covid_booster2" 
                                    value="{{ old('covid_booster2', $patient->immunization->covid_booster2 ?? '') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.patients') }}" class="btn btn-secondary">
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
                maritalStatus.addEventListener('change', function() {
                    spouseInfo.style.display = this.value === 'married' ? 'block' : 'none';
                });
            }
        });
    </script>
@endpush
