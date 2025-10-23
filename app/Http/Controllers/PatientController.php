<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $appointments = $user->appointments()->latest()->paginate(10);
        
        return view('patient.dashboard', compact('appointments'));
    }

    public function appointments()
    {
        $user = Auth::user();
        $appointments = $user->appointments()->latest()->paginate(20);
        
        return view('patient.appointments', compact('appointments'));
    }

    public function bookAppointment()
    {
        return view('patient.book-appointment');
    }

    public function storeAppointment(Request $request)
    {
        $request->validate([
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'required|string|max:20',
            'patient_address' => 'required|string|max:500',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
            'service_type' => 'required|string',
            'notes' => 'nullable|string|max:1000',
            'medical_history' => 'nullable|string|max:2000'
        ]);

        $appointment = Appointment::create([
            'user_id' => Auth::id(),
            'patient_name' => $request->patient_name,
            'patient_phone' => $request->patient_phone,
            'patient_address' => $request->patient_address,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'service_type' => $request->service_type,
            'notes' => $request->notes,
            'medical_history' => $request->medical_history,
            'status' => 'pending'
        ]);

        return redirect()->route('patient.dashboard')->with('success', 'Appointment booked successfully! We will contact you soon for confirmation.');
    }

    public function cancelAppointment(Appointment $appointment)
    {
        if ($appointment->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        if ($appointment->status === 'completed') {
            return redirect()->back()->with('error', 'Cannot cancel completed appointment.');
        }

        $appointment->update(['status' => 'cancelled']);
        
        return redirect()->back()->with('success', 'Appointment cancelled successfully.');
    }

    public function showAppointment(Appointment $appointment)
    {
        if ($appointment->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        return view('patient.appointment-details', compact('appointment'));
    }
}
