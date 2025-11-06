<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Appointment Approved</title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; }
        .container { max-width: 640px; margin: 0 auto; padding: 16px; }
        .card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; }
        .h1 { font-size: 20px; margin: 0 0 12px; }
        .muted { color: #6b7280; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 9999px; background: #10b981; color: #fff; font-size: 12px; }
        .row { margin: 6px 0; }
    </style>
    </head>
<body>
<div class="container">
    <div class="card">
        <p class="badge">Approved</p>
        <h1 class="h1">Your appointment has been approved</h1>
        <p>Hello {{ $appointment->patient_name ?? ($appointment->user->name ?? 'Patient') }},</p>
        <p>Great news! Your appointment request has been approved. Here are the details:</p>

        <div class="row"><strong>Service:</strong> {{ $appointment->service_type }}</div>
        <div class="row"><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</div>
        <div class="row"><strong>Time:</strong> {{ $appointment->appointment_time }}</div>
        <div class="row"><strong>Location:</strong> {{ $clinicLocation }}</div>

        <p class="row"><strong>Instructions:</strong> {{ $instructions }}</p>

        @if(!empty($appointment->notes))
            <p class="row"><span class="muted">Notes:</span> {{ $appointment->notes }}</p>
        @endif

        <p class="muted">If you have questions or need to reschedule, please reply to this email.</p>
        <p>— {{ config('app.name') }}</p>
    </div>
</div>
</body>
</html>


