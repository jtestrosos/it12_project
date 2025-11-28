<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Helpers\AppointmentHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PatientController extends Controller
{
    public function dashboard()
    {
        $user = Auth::guard('patient')->user();
        $appointments = $user->appointments()->latest()->paginate(10);

        return view('patient.dashboard', compact('appointments'));
    }

    public function appointments()
    {
        $user = Auth::guard('patient')->user();
        $appointments = $user->appointments()->latest()->paginate(20);

        return view('patient.appointments', compact('appointments'));
    }

    public function bookAppointment()
    {
        $user = Auth::guard('patient')->user();
        return view('patient.book-appointment', compact('user'));
    }

    public function storeAppointment(Request $request)
    {
        $request->validate([
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'required|string|max:20',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
            'service_type' => 'required|string',
            'notes' => 'nullable|string|max:1000',
            'medical_history' => 'nullable|string|max:2000'
        ]);

        $patient = Auth::guard('patient')->user();

        $appointment = Appointment::create([
            'patient_id' => Auth::guard('patient')->id(),
            'patient_name' => $request->patient_name,
            'patient_phone' => $request->patient_phone,
            'patient_address' => $patient->address ?? 'N/A',
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
        if ($appointment->patient_id !== Auth::guard('patient')->id()) {
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
        if ($appointment->patient_id !== Auth::guard('patient')->id()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        return view('patient.appointment-details', compact('appointment'));
    }

    /**
     * Get available slots for a specific date
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        $date = $request->date;
        $slots = AppointmentHelper::getAvailableSlots($date);

        return response()->json([
            'date' => $date,
            'slots' => $slots,
            'total_slots' => count($slots),
            'available_count' => count(array_filter($slots, fn($slot) => $slot['available'])),
            'occupied_count' => count(array_filter($slots, fn($slot) => !$slot['available'])),
        ]);
    }

    /**
     * Get calendar data for a month
     */
    public function getCalendarData(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $year = $request->year;
        $month = $request->month;

        $calendarData = AppointmentHelper::getCalendarData($year, $month);

        return response()->json([
            'year' => $year,
            'month' => $month,
            'calendar' => $calendarData,
        ]);
    }
}
