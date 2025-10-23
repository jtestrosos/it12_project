<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            ->paginate(20);

        return view('admin.patients', compact('patients'));
    }

    public function appointments()
    {
        $appointments = Appointment::with(['user', 'approvedBy'])
            ->latest()
            ->paginate(15);
            
        return view('admin.appointments', compact('appointments'));
    }

    public function updateAppointmentStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rescheduled,cancelled,completed',
            'notes' => 'nullable|string|max:1000'
        ]);

        $oldStatus = $appointment->status;
        
        $appointment->update([
            'status' => $request->status,
            'notes' => $request->notes,
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

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
            'user_id' => null, // Walk-in patients don't have user accounts
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
