@extends('layouts.app')

@section('content')
<div class="position-relative w-100" style="min-height: 80vh;">
    <!-- Background with semi-transparent overlay -->
    <div style="
        background: linear-gradient(rgba(255,255,255,0.76), rgba(255,255,255,0.68)), url('{{ asset('images/clinic-bg.jpg') }}') center/cover no-repeat;
        position:absolute; left:0; top:0; width:100%; height:100%; z-index:0;">
    </div>
    <div class="container position-relative" style="z-index:1;">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white rounded p-5 shadow-sm my-5">
                    <h2 class="fw-bold mb-4">Booking Policy</h2>
                    <p>To provide a smooth and fair experience for all patients, please review our appointment booking policies below:</p>
                    <ul class="mb-4">
                        <li><strong>Eligibility:</strong> Appointments are available to all barangay residents and registered patients.</li>
                        <li><strong>How to Book:</strong> Use our online system or visit/call the clinic to schedule an appointment.</li>
                        <li><strong>Confirmation:</strong> You will receive a confirmation via SMS or email after booking. Please bring a valid ID on your appointment day.</li>
                        <li><strong>Rescheduling & Cancellation:</strong> You may reschedule or cancel up to 1 day prior to your appointment through the website or by calling the clinic.</li>
                        <li><strong>Late Policy:</strong> If you arrive more than 15 minutes late, your slot may be given to another patient.</li>
                        <li><strong>No-shows:</strong> Repeated no-shows may result in temporary suspension of online booking privileges.</li>
                        <li><strong>Walk-ins:</strong> We accept walk-ins, but priority is given to patients with confirmed bookings.</li>
                        <li><strong>Data Privacy:</strong> All personal information will be kept strictly confidential in accordance with local privacy laws.</li>
                    </ul>
                    <p>If you have any questions regarding our booking policy, please contact us. We look forward to serving you and your family with compassionate community healthcare!</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
