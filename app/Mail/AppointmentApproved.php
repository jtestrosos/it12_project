<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentApproved extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Appointment $appointment;
    public string $clinicLocation;
    public string $instructions;

    public function __construct(Appointment $appointment, ?string $clinicLocation = null, ?string $instructions = null)
    {
        $this->appointment = $appointment->loadMissing('user');
        $this->clinicLocation = $clinicLocation ?? (env('CLINIC_LOCATION', 'Your Barangay Health Center'));
        $this->instructions = $instructions ?? 'Please wear a face mask and arrive 10 minutes early.';
    }

    public function build(): self
    {
        return $this
            ->subject('Your appointment has been approved')
            ->view('emails.appt-email-confirmed')
            ->with([
                'appointment' => $this->appointment,
                'clinicLocation' => $this->clinicLocation,
                'instructions' => $this->instructions,
            ]);
    }
}


