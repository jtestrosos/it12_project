<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Patient;
use App\Helpers\AppointmentHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\AppointmentApproved;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AppointmentRangeExport;
use Dompdf\Dompdf;
use Dompdf\Options;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalPatients = Patient::query()->count();

        // Today metrics
        $todayAppointments = Appointment::whereDate('appointment_date', today())->count();
        $todayCompleted = Appointment::whereDate('appointment_date', today())
            ->where('status', 'completed')
            ->count();
        $todayPending = Appointment::whereDate('appointment_date', today())
            ->where('status', 'pending')
            ->count();

        // Inventory metrics
        $lowStockItems = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')->count();
        $outOfStockCount = Inventory::where('current_stock', 0)->count();
        $expiringSoonCount = Inventory::whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now(), now()->addDays(90)])
            ->count();

        // Monthly services metrics
        $now = now();
        $lastMonth = $now->copy()->subMonth();

        $monthlyServices = Appointment::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();
        $lastMonthServices = Appointment::whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();
        $servicesChange = $lastMonthServices > 0
            ? round((($monthlyServices - $lastMonthServices) / $lastMonthServices) * 100)
            : null;

        // Monthly patient growth metrics
        $patientsThisMonth = Patient::query()
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();
        $patientsLastMonth = Patient::query()
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();
        $patientsChange = $patientsLastMonth > 0
            ? round((($patientsThisMonth - $patientsLastMonth) / $patientsLastMonth) * 100)
            : null;

        $recentAppointments = Appointment::with('user')->latest()->limit(5)->get();
        $lowStockInventory = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')->limit(5)->get();

        // Patients by Barangay data for the doughnut chart
        $patientsByBarangay = Patient::query()
            ->whereNotNull('barangay')
            ->selectRaw('barangay, count(*) as count')
            ->groupBy('barangay')
            ->get();

        // --- Chart Data Preparation ---

        // 1. Overview Chart Data (Appointments Count)

        $driver = DB::getDriverName();

        // Weekly (Current Week: Sun-Sat)
        $dayOfWeekSql = $driver === 'pgsql' ? 'EXTRACT(DOW FROM appointment_date) + 1' : 'DAYOFWEEK(appointment_date)';
        $weeklyOverview = Appointment::selectRaw("$dayOfWeekSql as label_key, count(*) as count")
            ->whereBetween('appointment_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->groupBy('label_key')
            ->pluck('count', 'label_key')
            ->toArray();

        // Monthly (Current Month: 1-31)
        $daySql = $driver === 'pgsql' ? 'EXTRACT(DAY FROM appointment_date)' : 'DAY(appointment_date)';
        $monthlyOverview = Appointment::selectRaw("$daySql as label_key, count(*) as count")
            ->whereMonth('appointment_date', now()->month)
            ->whereYear('appointment_date', now()->year)
            ->groupBy('label_key')
            ->pluck('count', 'label_key')
            ->toArray();

        // Yearly (Current Year: 1-12)
        $monthSql = $driver === 'pgsql' ? 'EXTRACT(MONTH FROM appointment_date)' : 'MONTH(appointment_date)';
        $yearlyOverview = Appointment::selectRaw("$monthSql as label_key, count(*) as count")
            ->whereYear('appointment_date', now()->year)
            ->groupBy('label_key')
            ->pluck('count', 'label_key')
            ->toArray();

        // 2. Services Chart Data (Service Types Distribution)

        // Weekly
        $weeklyServices = Appointment::selectRaw('service_type, count(*) as count')
            ->whereBetween('appointment_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->groupBy('service_type')
            ->get();

        // Monthly
        $monthlyServicesChart = Appointment::selectRaw('service_type, count(*) as count')
            ->whereMonth('appointment_date', now()->month)
            ->whereYear('appointment_date', now()->year)
            ->groupBy('service_type')
            ->get();

        // Yearly
        $yearlyServices = Appointment::selectRaw('service_type, count(*) as count')
            ->whereYear('appointment_date', now()->year)
            ->groupBy('service_type')
            ->get();

        $chartData = [
            'overview' => [
                'weekly' => $weeklyOverview,
                'monthly' => $monthlyOverview,
                'yearly' => $yearlyOverview,
            ],
            'services' => [
                'weekly' => $weeklyServices,
                'monthly' => $monthlyServicesChart,
                'yearly' => $yearlyServices,
            ]
        ];

        return view('admin.dashboard', compact(
            'totalPatients',
            'todayAppointments',
            'todayCompleted',
            'todayPending',
            'lowStockItems',
            'monthlyServices',
            'servicesChange',
            'patientsChange',
            'recentAppointments',
            'lowStockInventory',
            'patientsByBarangay',
            'patientsByBarangay',
            'chartData',
            'outOfStockCount',
            'expiringSoonCount'
        ));
    }

    public function patients()
    {
        $patients = Patient::query()
            ->with('appointments')
            ->latest()
            ->paginate(10);

        return view('admin.patients', compact('patients'));
    }

    public function archivePatient(Patient $patient)
    {
        // Patients don't have a role field, they are all patients
        // No need to check role

        if ($patient->id === Auth::guard('admin')->id()) {
            return redirect()->back()->with('error', 'You cannot archive your own account.');
        }

        $patient->delete();

        return redirect()->back()->with('success', 'Patient archived successfully.');
    }

    public function archivedPatients()
    {
        $patients = Patient::onlyTrashed()

            ->orderByDesc('deleted_at')
            ->paginate(10);

        return view('admin.patients-archive', compact('patients'));
    }

    public function restorePatient($id)
    {
        $patient = Patient::onlyTrashed()->findOrFail($id);
        $patient->restore();

        return redirect()->route('admin.patients.archive')->with('success', 'Patient restored successfully.');
    }

    public function forceDeletePatient($id)
    {
        $patient = Patient::onlyTrashed()->findOrFail($id);
        $patient->forceDelete();

        return redirect()->route('admin.patients.archive')->with('success', 'Patient permanently deleted.');
    }

    public function createPatient(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\.\-\']+$/',
            ],
            'email' => 'required|string|email|max:255|unique:patients',
            'gender' => 'required|in:male,female,other',
            'barangay' => [
                'required',
                Rule::in(['Barangay 11', 'Barangay 12', 'Other']),
            ],
            'barangay_other' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(fn() => $request->barangay === 'Other'),
            ],
            'purok' => [
                'nullable',
                Rule::requiredIf(fn() => in_array($request->barangay, ['Barangay 11', 'Barangay 12'], true)),
                Rule::when(
                    $request->barangay === 'Barangay 11',
                    Rule::in(['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'])
                ),
                Rule::when(
                    $request->barangay === 'Barangay 12',
                    Rule::in(['Purok 1', 'Purok 2', 'Purok 3'])
                ),
            ],
            'phone' => 'nullable|string|max:20',
            'birth_date' => [
                'required',
                'date',
                'before:today',
            ],
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
            'barangay.in' => 'Please select Barangay 11, Barangay 12, or choose Other.',
            'barangay_other.required' => 'Please specify the barangay.',
            'purok.required' => 'Please select a purok for the chosen barangay.',
            'purok.in' => 'Please choose a valid purok option.',
            'birth_date.before' => 'Birth date must be in the past.',
        ]);

        $age = Carbon::parse($validated['birth_date'])->age;

        Patient::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'gender' => $validated['gender'],
            'barangay' => $validated['barangay'],
            'barangay_other' => $validated['barangay'] === 'Other' ? $validated['barangay_other'] : null,
            'purok' => $validated['barangay'] === 'Other' ? null : ($validated['purok'] ?? null),
            'phone' => $validated['phone'],
            'birth_date' => $validated['birth_date'],
            'age' => $age,
            'password' => Hash::make($validated['password']),
            'role' => 'user'
        ]);

        return redirect()->back()->with('success', 'Patient created successfully.');
    }

    public function updatePatient(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\.\-\']+$/',
            ],
            'email' => 'required|string|email|max:255|unique:patients,email,' . $patient->id,
            'gender' => 'required|in:male,female,other',
            'barangay' => [
                'required',
                Rule::in(['Barangay 11', 'Barangay 12', 'Other']),
            ],
            'barangay_other' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(fn() => $request->barangay === 'Other'),
            ],
            'purok' => [
                'nullable',
                Rule::requiredIf(fn() => in_array($request->barangay, ['Barangay 11', 'Barangay 12'], true)),
                Rule::when(
                    $request->barangay === 'Barangay 11',
                    Rule::in(['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'])
                ),
                Rule::when(
                    $request->barangay === 'Barangay 12',
                    Rule::in(['Purok 1', 'Purok 2', 'Purok 3'])
                ),
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'birth_date' => [
                'required',
                'date',
                'before:today',
            ],
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
            'barangay.in' => 'Please select Barangay 11, Barangay 12, or choose Other.',
            'barangay_other.required' => 'Please specify the barangay.',
            'purok.required' => 'Please select a purok for the chosen barangay.',
            'purok.in' => 'Please choose a valid purok option.',
            'birth_date.before' => 'Birth date must be in the past.',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'gender' => $validated['gender'],
            'barangay' => $validated['barangay'],
            'barangay_other' => $validated['barangay'] === 'Other' ? $validated['barangay_other'] : null,
            'purok' => $validated['barangay'] === 'Other' ? null : ($validated['purok'] ?? null),
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'birth_date' => $validated['birth_date'],
            'age' => Carbon::parse($validated['birth_date'])->age,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $patient->update($updateData);

        return redirect()->back()->with('success', 'Patient updated successfully.');
    }

    public function appointments(Request $request)
    {
        $query = Appointment::with(['patient', 'approvedByAdmin', 'approvedBySuperAdmin']);

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
        $userId = $request->filled('user_id') ? $request->user_id : Auth::guard('admin')->id();
        $isWalkIn = !$request->filled('user_id'); // Only walk-in if no user_id provided

        Appointment::create([
            'patient_id' => $userId,
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
            'status' => 'required|in:pending,approved,rescheduled,cancelled,completed,no_show',
            'notes' => 'nullable|string|max:1000',
            'new_date' => 'nullable|date|after_or_equal:today',
            'new_time' => 'nullable'
        ]);

        $oldStatus = $appointment->status;

        $update = [
            'status' => $request->status,
            'notes' => $request->notes,
            'approved_by' => Auth::guard('admin')->id(),
            'approved_at' => now()
        ];
        if ($request->status === 'rescheduled') {
            if ($request->filled('new_date')) {
                // Check for availability on the new date (Limit: 9 per service per day)
                $existingCount = Appointment::whereDate('appointment_date', $request->new_date)
                    ->where('service_type', $appointment->service_type)
                    ->where('status', '!=', 'cancelled')
                    ->count();

                if ($existingCount >= 9) {
                    return redirect()->back()->with('error', 'Cannot reschedule: The selected date is fully booked for ' . $appointment->service_type . '.');
                }

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
        $query = Inventory::query()->with('transactions')->latest();
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
            'Medicines',
            'Medical Supplies',
            'Equipment',
            'Vaccines',
            'PPE',
            'Syringes & Needles',
            'Lab Supplies',
            'Test Kits',
            'Disinfectants',
            'Consumables',
            'Dressings',
            'Nutritional Supplements',
            'Oxygen Supplies',
            'Other'
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
            'performable_type' => \App\Models\Admin::class,
            'performable_id' => Auth::guard('admin')->id(),
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
                'performable_type' => \App\Models\Admin::class,
                'performable_id' => Auth::guard('admin')->id(),
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
            'performable_type' => \App\Models\Admin::class,
            'performable_id' => Auth::guard('admin')->id(),
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
            'performable_type' => \App\Models\Admin::class,
            'performable_id' => Auth::guard('admin')->id(),
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
            'user_id' => Auth::guard('admin')->id(), // link to admin user to satisfy FK
            'patient_name' => $request->patient_name,
            'patient_phone' => $request->patient_phone,
            'patient_address' => $request->patient_address,
            'appointment_date' => now()->toDateString(),
            'appointment_time' => now()->toTimeString(),
            'service_type' => $request->service_type,
            'notes' => $request->notes,
            'is_walk_in' => true,
            'status' => 'approved',
            'approved_by' => Auth::guard('admin')->id(),
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

        // --- Multi-timeframe Trend Data ---

        $driver = DB::getDriverName();

        // 1. Weekly (Current Week: Sun-Sat)
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $dayOfWeekSql = $driver === 'pgsql' ? 'EXTRACT(DOW FROM appointment_date) + 1' : 'DAYOFWEEK(appointment_date)';
        $weeklyData = Appointment::selectRaw("$dayOfWeekSql as day, count(*) as count")
            ->whereBetween('appointment_date', [$startOfWeek, $endOfWeek])
            ->groupBy('day')
            ->pluck('count', 'day')
            ->toArray();

        // 2. Monthly (Current Month: 1st-End)
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $daySql = $driver === 'pgsql' ? 'EXTRACT(DAY FROM appointment_date)' : 'DAY(appointment_date)';
        $monthlyData = Appointment::selectRaw("$daySql as day, count(*) as count")
            ->whereBetween('appointment_date', [$startOfMonth, $endOfMonth])
            ->groupBy('day')
            ->pluck('count', 'day')
            ->toArray();

        // 3. Yearly (Current Year: Jan-Dec)
        $startOfYear = now()->startOfYear();
        $endOfYear = now()->endOfYear();
        $monthSql = $driver === 'pgsql' ? 'EXTRACT(MONTH FROM appointment_date)' : 'MONTH(appointment_date)';
        $yearlyData = Appointment::selectRaw("$monthSql as month, count(*) as count")
            ->whereBetween('appointment_date', [$startOfYear, $endOfYear])
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $trendData = [
            'weekly' => $weeklyData,
            'monthly' => $monthlyData,
            'yearly' => $yearlyData
        ];

        return view('admin.reports', compact('appointmentStats', 'inventoryStats', 'serviceTypes', 'trendData'));
    }

    public function analytics()
    {
        // Reusing the main reports logic for now, or we can create a dedicated analytics view
        return $this->reports();
    }

    public function patientReports()
    {
        // Patient statistics
        $totalPatients = Patient::query()->count();
        $maleCount = Patient::query()->where('gender', 'male')->count();
        $femaleCount = Patient::query()->where('gender', 'female')->count();
        $newPatientsThisMonth = Patient::query()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Age distribution
        $ageGroups = [
            '0-17' => Patient::query()->whereBetween('age', [0, 17])->count(),
            '18-30' => Patient::query()->whereBetween('age', [18, 30])->count(),
            '31-50' => Patient::query()->whereBetween('age', [31, 50])->count(),
            '51-70' => Patient::query()->whereBetween('age', [51, 70])->count(),
            '71+' => Patient::query()->where('age', '>', 70)->count(),
        ];

        // Barangay distribution
        $barangayDistribution = Patient::query()
            ->selectRaw('barangay, count(*) as count')
            ->groupBy('barangay')
            ->get();

        // Patients with most appointments
        $topPatients = Patient::query()
            ->withCount('appointments')
            ->orderByDesc('appointments_count')
            ->limit(5)
            ->get();

        // Recent registrations
        $recentPatients = Patient::query()
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.reports.patients', compact(
            'totalPatients',
            'maleCount',
            'femaleCount',
            'newPatientsThisMonth',
            'ageGroups',
            'barangayDistribution',
            'topPatients',
            'recentPatients'
        ));
    }

    public function inventoryReports()
    {
        // Inventory statistics
        $totalItems = Inventory::count();
        $lowStockCount = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')->count();
        $outOfStockCount = Inventory::where('current_stock', 0)->count();
        $expiringSoonCount = Inventory::whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now(), now()->addDays(90)])
            ->count();

        // Category breakdown
        $categoryBreakdown = Inventory::selectRaw('category, count(*) as count, sum(current_stock) as total_stock')
            ->groupBy('category')
            ->get();

        // Low stock items
        $lowStockItems = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')
            ->orderBy('current_stock', 'asc')
            ->limit(10)
            ->get();

        // Expiring soon items
        $expiringSoonItems = Inventory::whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now(), now()->addDays(90)])
            ->orderBy('expiry_date', 'asc')
            ->limit(10)
            ->get();

        // Recent transactions
        $recentTransactions = InventoryTransaction::with(['inventory', 'user'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.reports.inventory', compact(
            'totalItems',
            'lowStockCount',
            'outOfStockCount',
            'expiringSoonCount',
            'categoryBreakdown',
            'lowStockItems',
            'expiringSoonItems',
            'recentTransactions'
        ));
    }

    public function services()
    {
        $services = Service::latest()->paginate(10);
        return view('admin.services.index', compact('services'));
    }

    public function createService()
    {
        return view('admin.services.create');
    }

    public function storeService(Request $request)
    {
        Log::info('storeService called', $request->all());
        $request->validate([
            'name' => 'required|string|max:255|unique:services',
            'description' => 'nullable|string',
            'active' => 'boolean'
        ]);

        Service::create([
            'name' => $request->name,
            'description' => $request->description,
            'active' => $request->has('active')
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
    }

    public function editService(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function updateService(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:services,name,' . $service->id,
            'description' => 'nullable|string',
            'active' => 'boolean'
        ]);

        $service->update([
            'name' => $request->name,
            'description' => $request->description,
            'active' => $request->has('active')
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
    }

    public function deleteService(Service $service)
    {
        // Check if service has appointments
        if ($service->appointments()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete service because it has associated appointments. Deactivate it instead.');
        }

        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully.');
    }

    public function exportAppointmentsExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $appointments = Appointment::with('user')
            ->whereBetween('appointment_date', [
                $request->start_date,
                $request->end_date,
            ])
            ->orderBy('appointment_date')
            ->get();

        $inventory = Inventory::with('transactions')->orderBy('item_name')->get();

        $filename = 'appointments_' . $request->start_date . '_to_' . $request->end_date . '.xlsx';
        return Excel::download(new AppointmentRangeExport($appointments, $inventory), $filename);
    }

    public function exportAppointmentsPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $appointments = Appointment::with('user')
            ->whereBetween('appointment_date', [
                $request->start_date,
                $request->end_date,
            ])
            ->orderBy('appointment_date')
            ->get();

        $patients = $appointments
            ->pluck('user')
            ->filter()
            ->unique(fn($user) => $user->id ?? spl_object_id($user))
            ->values();

        $patientAppointments = $appointments
            ->filter(fn($appt) => $appt->user_id !== null)
            ->values();
        $walkInAppointments = $appointments
            ->filter(fn($appt) => $appt->user_id === null)
            ->values();

        $html = view('admin.reports.appointments-pdf', [
            'startDate' => Carbon::parse($request->start_date),
            'endDate' => Carbon::parse($request->end_date),
            'patients' => $patients,
            'patientAppointments' => $patientAppointments,
            'walkInAppointments' => $walkInAppointments,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'landscape');
        $dompdf->render();

        $filename = 'appointments_' . $request->start_date . '_to_' . $request->end_date . '.pdf';
        return response()->streamDownload(
            function () use ($dompdf) {
                echo $dompdf->output();
            },
            $filename,
            [
                'Content-Type' => 'application/pdf',
            ]
        );
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

        // Debug: Check what appointments exist for this date
        $appointments = \App\Models\Appointment::whereDate('appointment_date', $date)
            ->whereIn('status', ['pending', 'approved', 'completed'])
            ->get();

        \Log::info("Appointments for date {$date}: " . $appointments->toJson());

        $slots = AppointmentHelper::getAvailableSlots($date);

        \Log::info("Slots data for date {$date}: " . json_encode($slots));

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

        // Debug: Log calendar data
        \Log::info("Calendar data for {$year}-{$month}: " . json_encode($calendarData));

        return response()->json([
            'year' => $year,
            'month' => $month,
            'calendar' => $calendarData,
        ]);
    }

    // Patient Reports Export Methods
    public function exportPatientsExcel()
    {
        $patients = Patient::query()->get();

        $export = new class ($patients) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            protected $patients;

            public function __construct($patients)
            {
                $this->patients = $patients;
            }

            public function collection()
            {
                return $this->patients->map(function ($patient) {
                    return [
                        'Name' => $patient->name,
                        'Email' => $patient->email,
                        'Gender' => ucfirst($patient->gender ?? 'N/A'),
                        'Age' => $patient->age ?? 'N/A',
                        'Barangay' => $patient->barangay === 'Other' ? $patient->barangay_other : $patient->barangay,
                        'Registered' => $patient->created_at->format('M d, Y'),
                    ];
                });
            }

            public function headings(): array
            {
                return ['Name', 'Email', 'Gender', 'Age', 'Barangay', 'Registered'];
            }
        };

        return Excel::download($export, 'patient_reports_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPatientsPdf()
    {
        $patients = Patient::query()->get();

        $html = view('admin.reports.patients-pdf', compact('patients'))->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'portrait');
        $dompdf->render();

        $filename = 'patient_reports_' . now()->format('Y-m-d') . '.pdf';
        return response()->streamDownload(
            function () use ($dompdf) {
                echo $dompdf->output();
            },
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    // Inventory Reports Export Methods
    public function exportInventoryExcel()
    {
        $inventory = Inventory::all();

        $export = new class ($inventory) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            protected $inventory;

            public function __construct($inventory)
            {
                $this->inventory = $inventory;
            }

            public function collection()
            {
                return $this->inventory->map(function ($item) {
                    return [
                        'Item Name' => $item->item_name,
                        'Category' => $item->category,
                        'Current Stock' => $item->current_stock . ' ' . $item->unit,
                        'Minimum Stock' => $item->minimum_stock . ' ' . $item->unit,
                        'Status' => str_replace('_', ' ', ucfirst($item->status)),
                        'Expiry Date' => $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('M d, Y') : 'N/A',
                    ];
                });
            }

            public function headings(): array
            {
                return ['Item Name', 'Category', 'Current Stock', 'Minimum Stock', 'Status', 'Expiry Date'];
            }
        };

        return Excel::download($export, 'inventory_reports_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportInventoryPdf()
    {
        $inventory = Inventory::all();

        $html = view('admin.reports.inventory-pdf', compact('inventory'))->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'landscape');
        $dompdf->render();

        $filename = 'inventory_reports_' . now()->format('Y-m-d') . '.pdf';
        return response()->streamDownload(
            function () use ($dompdf) {
                echo $dompdf->output();
            },
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }
}
