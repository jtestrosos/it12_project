<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\AppointmentApproved;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AppointmentRangeExport;

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
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\.\-\']+$/',
            ],
            'email' => 'required|string|email|max:255|unique:users',
            'gender' => 'required|in:male,female,other',
            'barangay' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]).+$/',
            ],
        ], [
            'name.regex' => 'The name field should not contain numbers. Only letters, spaces, periods, hyphens, and apostrophes are allowed.',
            'password.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, and one special character.',
            'gender.required' => 'Please select a gender.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'gender' => $request->gender,
            'barangay' => $request->barangay,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'user'
        ]);

        return redirect()->back()->with('success', 'Patient created successfully.');
    }

    public function updatePatient(Request $request, User $user)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\.\-\']+$/',
            ],
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'gender' => 'required|in:male,female,other',
            'barangay' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'password' => [
                'nullable',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]).+$/',
            ],
        ], [
            'name.regex' => 'The name field should not contain numbers. Only letters, spaces, periods, hyphens, and apostrophes are allowed.',
            'password.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, and one special character.',
            'gender.required' => 'Please select a gender.',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'gender' => $request->gender,
            'barangay' => $request->barangay,
            'phone' => $request->phone,
            'address' => $request->address,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->back()->with('success', 'Patient updated successfully.');
    }

    public function appointments(Request $request)
    {
        $query = Appointment::with(['user', 'approvedBy']);

        $sort = $request->get('sort');
        $direction = strtolower($request->get('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        if ($sort === 'date') {
            $query->orderBy('appointment_date', $direction)
                ->orderBy('appointment_time', $direction);
        } else {
            $query->orderByDesc('appointment_date')
                ->orderByDesc('appointment_time');
        }

        $searchInput = $request->input('search', $request->input('q'));
        if (filled($searchInput)) {
            $search = trim($searchInput);
            $query->where(function ($sub) use ($search) {
                $sub->where('patient_name', 'like', "%{$search}%")
                    ->orWhere('patient_phone', 'like', "%{$search}%")
                    ->orWhere('service_type', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('service')) {
            $query->where('service_type', $request->service);
        }

        if ($request->filled('from')) {
            $query->whereDate('appointment_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('appointment_date', '<=', $request->to);
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

        // If user_id is provided, link to registered patient; otherwise link to admin
        $userId = $request->filled('user_id') ? $request->user_id : Auth::id();
        $isWalkIn = !$request->filled('user_id'); // Only walk-in if no user_id provided

        Appointment::create([
            'user_id' => $userId,
            'patient_name' => $request->patient_name,
            'patient_phone' => $request->patient_phone ?: '',
            'patient_address' => $request->patient_address ?: 'N/A',
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'service_type' => $request->service_type,
            'notes' => $request->notes,
            'is_walk_in' => $isWalkIn,
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

        // Send approval email only when transitioning to approved
        if ($oldStatus !== 'approved' && $request->status === 'approved') {
            $appointment->loadMissing('user');
            $targetEmail = $appointment->user->email ?? null;
            Log::info('[AppointmentApprovedEmail] Preparing to send', [
                'appointment_id' => $appointment->id,
                'user_id' => $appointment->user->id ?? null,
                'target_email' => $targetEmail,
            ]);
            if (!empty($targetEmail)) {
                try {
                    Mail::to($targetEmail)->send(new AppointmentApproved($appointment));
                    Log::info('[AppointmentApprovedEmail] Sent successfully', [
                        'appointment_id' => $appointment->id,
                        'target_email' => $targetEmail,
                    ]);
                } catch (\Throwable $e) {
                    Log::error('[AppointmentApprovedEmail] Failed to send', [
                        'appointment_id' => $appointment->id,
                        'target_email' => $targetEmail,
                        'error' => $e->getMessage(),
                    ]);
                }
            } else {
                Log::warning('[AppointmentApprovedEmail] No recipient email found for appointment approval', [
                    'appointment_id' => $appointment->id,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Appointment status updated successfully.');
    }

    public function inventory(Request $request)
    {
        $query = Inventory::query()->latest();
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $q = trim($request->search);
            $query->where(function ($sub) use ($q) {
                $sub->where('item_name', 'like', "%{$q}%")
                    ->orWhere('category', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%")
                    ->orWhere('unit', 'like', "%{$q}%");

                if (is_numeric($q)) {
                    $sub->orWhere('id', (int) $q);
                }
            });
        }
        $inventory = $query->paginate(10)->withQueryString();

        // Stats for header cards and alerts
        $totalItems = Inventory::count();
        $lowStockCount = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')->count();
        $outOfStockCount = Inventory::where('current_stock', 0)->count();
        $expiringSoonCount = Inventory::whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now(), now()->addDays(90)])
            ->count();

        // Build category options from existing items
        $defaultCategories = [
            'Medicines','Medical Supplies','Equipment','Vaccines','PPE',
            'Syringes & Needles','Lab Supplies','Test Kits','Disinfectants',
            'Consumables','Dressings','Nutritional Supplements','Oxygen Supplies','Other'
        ];

        $categories = Inventory::select('category')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->toArray();

        $categories = array_values(array_unique(array_merge($defaultCategories, $categories)));

        $stats = [
            'total_items' => $totalItems,
            'low_stock' => $lowStockCount,
            'out_of_stock' => $outOfStockCount,
            'expiring_soon' => $expiringSoonCount,
        ];

        return view('admin.inventory', compact('inventory', 'categories', 'stats'));
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
            'supplier' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255'
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

    public function restockInventory(Request $request, Inventory $inventory)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        $inventory->current_stock += (int) $request->quantity;
        if ($request->filled('expiry_date')) {
            $inventory->expiry_date = $request->expiry_date;
        }
        $inventory->save();
        $inventory->updateStatus();

        InventoryTransaction::create([
            'inventory_id' => $inventory->id,
            'user_id' => Auth::id(),
            'transaction_type' => 'restock',
            'quantity' => (int) $request->quantity,
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Stock restocked successfully.');
    }

    public function deductInventory(Request $request, Inventory $inventory)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);

        $quantity = (int) $request->quantity;
        $inventory->current_stock = max(0, $inventory->current_stock - $quantity);
        $inventory->save();
        $inventory->updateStatus();

        InventoryTransaction::create([
            'inventory_id' => $inventory->id,
            'user_id' => Auth::id(),
            'transaction_type' => 'usage',
            'quantity' => $quantity,
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Stock deducted successfully.');
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

    public function exportAppointmentsExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $appointments = Appointment::whereBetween('appointment_date', [
                $request->start_date,
                $request->end_date,
            ])
            ->orderBy('appointment_date')
            ->get();

        $inventory = Inventory::with('transactions')->orderBy('item_name')->get();

        $filename = 'appointments_' . $request->start_date . '_to_' . $request->end_date . '.xlsx';
        return Excel::download(new AppointmentRangeExport($appointments, $inventory), $filename);
    }
}
