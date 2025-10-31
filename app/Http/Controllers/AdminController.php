<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalPatients = User::where('role', 'user')->count();
        $todayAppointments = Appointment::whereDate('appointment_date', today())->count();
        $lowStockItems = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')->count();
        $monthlyServices = Appointment::whereMonth('created_at', now()->month)->count();

        $recentAppointments = Appointment::with('user')->latest()->limit(5)->get();
        $lowStockInventory = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')->limit(5)->get();

        // Patients by Barangay data for the doughnut chart
        $patientsByBarangay = User::where('role', 'user')
            ->whereNotNull('barangay')
            ->selectRaw('barangay, count(*) as count')
            ->groupBy('barangay')
            ->get();

        // Service types data for the bar chart
        $serviceTypes = Appointment::selectRaw('service_type, count(*) as count')
            ->whereMonth('created_at', now()->month)
            ->groupBy('service_type')
            ->get();

        // Weekly appointments data for the line chart
        $weeklyAppointments = Appointment::selectRaw('DAYOFWEEK(appointment_date) as day_of_week, count(*) as count')
            ->whereBetween('appointment_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->groupBy('day_of_week')
            ->get();

        return view('admin.dashboard', compact(
            'totalPatients',
            'todayAppointments',
            'lowStockItems',
            'monthlyServices',
            'recentAppointments',
            'lowStockInventory',
            'patientsByBarangay',
            'serviceTypes',
            'weeklyAppointments'
        ));
    }

    public function patients()
    {
        $patients = User::where('role', 'user')
            ->with('appointments')
            ->latest()
            ->paginate(10);

        return view('admin.patients', compact('patients'));
    }

    public function createPatient(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'barangay' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'barangay' => $request->barangay,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'user'
        ]);

        return redirect()->back()->with('success', 'Patient created successfully.');
    }

    public function appointments(Request $request)
    {
        $query = Appointment::with(['user', 'approvedBy'])->latest();
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function($sub) use ($q) {
                $sub->where('patient_name', 'like', "%$q%")
                    ->orWhere('patient_phone', 'like', "%$q%")
                    ->orWhere('service_type', 'like', "%$q%")
                    ->orWhereHas('user', function($u) use ($q) {
                        $u->where('name', 'like', "%$q%")
                            ->orWhere('email', 'like', "%$q%") ;
                    });
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $appointments = $query->paginate(10)->withQueryString();

        // Populate services for filters and booking drawer
        $services = [
            'General Checkup',
            'Prenatal',
            'Medical Check-up',
            'Immunization',
            'Family Planning',
        ];
        if (class_exists(Service::class) && Schema::hasTable('services')) {
            $dbServices = Service::where('active', true)->pluck('name')->toArray();
            if (!empty($dbServices)) {
                $services = array_values(array_unique(array_merge($services, $dbServices)));
            }
        }

        // Simple availability metrics for today (all services)
        $todaySlots = 9; // per service per day
        $todayBooked = Appointment::whereDate('appointment_date', today())
            ->where('status', '!=', 'cancelled')
            ->count();
        $todayCapacity = $todaySlots > 0 ? (int) min(100, round(($todayBooked / $todaySlots) * 100)) : 0;

        return view('admin.appointments', compact('appointments', 'services', 'todaySlots', 'todayBooked', 'todayCapacity'));
    }

    public function createAppointment(Request $request)
    {
        $request->validate([
            'patient_name' => 'required|string|max:255',
            'service_type' => 'required|string',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'patient_phone' => 'nullable|string|max:20',
            'patient_address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000'
        ]);

        // Enforce 9 slots per day per service (excluding cancelled)
        $existingCount = Appointment::whereDate('appointment_date', $request->appointment_date)
            ->where('service_type', $request->service_type)
            ->where('status', '!=', 'cancelled')
            ->count();
        if ($existingCount >= 9) {
            return redirect()->back()->with('error', 'No slots available for this service on the selected date.');
        }

        Appointment::create([
            'user_id' => Auth::id(), // link to creator to satisfy FK; still marked walk-in
            'patient_name' => $request->patient_name,
            'patient_phone' => $request->patient_phone ?: '',
            'patient_address' => $request->patient_address ?: 'N/A',
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'service_type' => $request->service_type,
            'notes' => $request->notes,
            'is_walk_in' => true,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Appointment created successfully.');
    }

    public function updateAppointmentStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rescheduled,cancelled,completed',
            'notes' => 'nullable|string|max:1000',
            'new_date' => 'nullable|date|after_or_equal:today',
            'new_time' => 'nullable'
        ]);

        $oldStatus = $appointment->status;
        
        $update = [
            'status' => $request->status,
            'notes' => $request->notes,
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ];
        if ($request->status === 'rescheduled') {
            if ($request->filled('new_date')) {
                $update['appointment_date'] = $request->new_date;
            }
            if ($request->filled('new_time')) {
                $update['appointment_time'] = $request->new_time;
            }
        }

        $appointment->update($update);

        return redirect()->back()->with('success', 'Appointment status updated successfully.');
    }

    public function inventory()
    {
        $inventory = Inventory::latest()->paginate(15);
        return view('admin.inventory', compact('inventory'));
    }

    public function addInventory(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|max:100',
            'current_stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'unit_price' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date|after:today',
            'supplier' => 'nullable|string|max:255'
        ]);

        $inventory = Inventory::create($request->all());
        $inventory->updateStatus();

        // Create transaction record
        InventoryTransaction::create([
            'inventory_id' => $inventory->id,
            'user_id' => Auth::id(),
            'transaction_type' => 'restock',
            'quantity' => $request->current_stock,
            'notes' => 'Initial stock'
        ]);

        return redirect()->back()->with('success', 'Inventory item added successfully.');
    }

    public function updateInventory(Request $request, Inventory $inventory)
    {
        $request->validate([
            'current_stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'supplier' => 'nullable|string|max:255'
        ]);

        $oldStock = $inventory->current_stock;
        $newStock = $request->current_stock;
        $difference = $newStock - $oldStock;

        $inventory->update($request->all());
        $inventory->updateStatus();

        // Create transaction record if stock changed
        if ($difference != 0) {
            InventoryTransaction::create([
                'inventory_id' => $inventory->id,
                'user_id' => Auth::id(),
                'transaction_type' => $difference > 0 ? 'restock' : 'usage',
                'quantity' => abs($difference),
                'notes' => 'Stock adjustment'
            ]);
        }

        return redirect()->back()->with('success', 'Inventory updated successfully.');
    }

    public function addWalkIn(Request $request)
    {
        $request->validate([
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'required|string|max:20',
            'patient_address' => 'required|string|max:500',
            'service_type' => 'required|string',
            'notes' => 'nullable|string|max:1000'
        ]);

        Appointment::create([
            'user_id' => Auth::id(), // link to admin user to satisfy FK
            'patient_name' => $request->patient_name,
            'patient_phone' => $request->patient_phone,
            'patient_address' => $request->patient_address,
            'appointment_date' => now()->toDateString(),
            'appointment_time' => now()->toTimeString(),
            'service_type' => $request->service_type,
            'notes' => $request->notes,
            'is_walk_in' => true,
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        return redirect()->back()->with('success', 'Walk-in patient added successfully.');
    }

    public function reports()
    {
        $appointmentStats = [
            'total' => Appointment::count(),
            'pending' => Appointment::where('status', 'pending')->count(),
            'approved' => Appointment::where('status', 'approved')->count(),
            'completed' => Appointment::where('status', 'completed')->count(),
            'cancelled' => Appointment::where('status', 'cancelled')->count()
        ];

        $inventoryStats = [
            'total_items' => Inventory::count(),
            'low_stock' => Inventory::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            'out_of_stock' => Inventory::where('current_stock', 0)->count(),
            'expired' => Inventory::where('expiry_date', '<', now())->count()
        ];

        // Service types data for the doughnut chart
        $serviceTypes = Appointment::selectRaw('service_type, count(*) as count')
            ->groupBy('service_type')
            ->get();

        // Monthly appointments trend for the last 6 months
        $monthlyTrend = Appointment::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, count(*) as count')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        return view('admin.reports', compact('appointmentStats', 'inventoryStats', 'serviceTypes', 'monthlyTrend'));
    }
}
