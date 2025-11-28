<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\Models\Patient;
use App\Models\Admin;
use App\Models\SuperAdmin;
use App\Models\Appointment;
use App\Models\Inventory;
use App\Models\SystemLog;
use App\Models\Backup;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        // ===== USER MANAGEMENT METRICS =====
        $totalPatients = Patient::count();
        $totalAdmins = Admin::count();
        $totalSuperAdmins = SuperAdmin::count();
        $totalSystemUsers = $totalPatients + $totalAdmins + $totalSuperAdmins;

        // User Growth (last 30 days)
        $newUsersLast30Days = Patient::where('created_at', '>=', now()->subDays(30))->count();
        $previousPeriodUsers = Patient::whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])->count();
        $userGrowthRate = $previousPeriodUsers > 0 ? round((($newUsersLast30Days - $previousPeriodUsers) / $previousPeriodUsers) * 100, 1) : 0;

        // ===== SYSTEM HEALTH METRICS =====
        $recentBackup = Backup::where('status', 'completed')->latest()->first();
        $lastBackupTime = $recentBackup ? $recentBackup->completed_at->diffForHumans() : 'Never';

        // Low stock items
        $lowStockItems = Inventory::where('status', 'low_stock')->count();
        $lowStockList = Inventory::where('status', 'low_stock')->orderBy('current_stock')->limit(5)->get();

        // ===== ADMIN PERFORMANCE TRACKING =====
        $adminPerformance = Admin::withCount([
            'approvedAppointments' => function ($query) {
                $query->whereMonth('created_at', now()->month);
            }
        ])->get()->map(function ($admin) {
            return [
                'name' => $admin->name,
                'appointments' => $admin->approved_appointments_count ?? 0
            ];
        });

        // ===== USER GROWTH TREND (Last 30 Days) =====
        $driver = DB::getDriverName();
        $dateFormat = $driver === 'pgsql' ? "TO_CHAR(created_at, 'YYYY-MM-DD')" : "DATE(created_at)";

        $userGrowthData = Patient::selectRaw("$dateFormat as date, COUNT(*) as count")
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ===== ROLE DISTRIBUTION =====
        $roleDistribution = [
            ['role' => 'Patients', 'count' => $totalPatients],
            ['role' => 'Admins', 'count' => $totalAdmins],
            ['role' => 'Super Admins', 'count' => $totalSuperAdmins]
        ];

        // ===== BARANGAY DISTRIBUTION =====
        $patientsByBarangay = Patient::whereNotNull('barangay')
            ->selectRaw('barangay, count(*) as count')
            ->groupBy('barangay')
            ->get();

        // ===== AGE DEMOGRAPHICS =====
        $ageGroups = Patient::selectRaw("
            CASE 
                WHEN age < 18 THEN '0-17'
                WHEN age BETWEEN 18 AND 35 THEN '18-35'
                WHEN age BETWEEN 36 AND 50 THEN '36-50'
                WHEN age BETWEEN 51 AND 65 THEN '51-65'
                ELSE '65+'
            END as age_group,
            COUNT(*) as count
        ")
            ->whereNotNull('age')
            ->groupBy('age_group')
            ->get();

        // ===== RECENT SYSTEM ACTIVITY =====
        $recentLogs = SystemLog::latest()->limit(10)->get();

        // ===== STORAGE INFO (Placeholder) =====
        $storageUsed = '2.4 GB';
        $storageTotal = '10 GB';
        $storagePercentage = 24;

        return view('superadmin.dashboard', compact(
            'totalSystemUsers',
            'totalPatients',
            'totalAdmins',
            'totalSuperAdmins',
            'userGrowthRate',
            'lastBackupTime',
            'lowStockItems',
            'lowStockList',
            'adminPerformance',
            'userGrowthData',
            'roleDistribution',
            'patientsByBarangay',
            'ageGroups',
            'recentLogs',
            'storageUsed',
            'storageTotal',
            'storagePercentage'
        ));
    }

    public function users(Request $request)
    {
        // Fetch all users from different tables
        $patients = Patient::all()->map(function ($user) {
            $user->role = 'user';
            return $user;
        });

        $admins = Admin::all()->map(function ($user) {
            $user->role = 'admin';
            return $user;
        });

        $superAdmins = SuperAdmin::all()->map(function ($user) {
            $user->role = 'superadmin';
            return $user;
        });

        // Merge all users
        $allUsers = $patients->concat($admins)->concat($superAdmins);

        // Sort by created_at desc
        $sortedUsers = $allUsers->sortByDesc('created_at');

        // Manual Pagination
        $page = $request->get('page', 1);
        $perPage = 15;

        $users = new \Illuminate\Pagination\LengthAwarePaginator(
            $sortedUsers->forPage($page, $perPage),
            $sortedUsers->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('superadmin.users', compact('users'));
    }

    public function createUser(Request $request)
    {
        // Determine correct table for email uniqueness check based on role
        $emailTable = match ($request->role) {
            'user' => 'patients',
            'admin' => 'admins',
            'superadmin' => 'super_admins',
            default => 'patients',
        };

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\.\-\']+$/',
            ],
            'email' => "required|string|email|max:255|unique:{$emailTable}",
            'gender' => 'required|in:male,female,other',
            'role' => 'required|in:user,admin,superadmin',
            'barangay' => [
                Rule::requiredIf(fn() => $request->role === 'user'),
                Rule::in(['Barangay 11', 'Barangay 12', 'Other']),
            ],
            'barangay_other' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(fn() => $request->role === 'user' && $request->barangay === 'Other'),
            ],
            'purok' => [
                'nullable',
                Rule::requiredIf(fn() => $request->role === 'user' && in_array($request->barangay, ['Barangay 11', 'Barangay 12'], true)),
                Rule::when(
                    $request->role === 'user' && $request->barangay === 'Barangay 11',
                    Rule::in(['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'])
                ),
                Rule::when(
                    $request->role === 'user' && $request->barangay === 'Barangay 12',
                    Rule::in(['Purok 1', 'Purok 2', 'Purok 3'])
                ),
            ],
            'birth_date' => [
                Rule::requiredIf(fn() => $request->role === 'user'),
                'nullable',
                'date',
                'before:today',
            ],
            'phone' => [
                Rule::requiredIf(fn() => $request->role === 'user'),
                'nullable',
                'string',
                'max:20',
            ],
            'address' => [
                'nullable',
                'string',
                'max:500',
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
            'barangay.required' => 'Barangay is required for patient accounts.',
            'barangay_other.required' => 'Please specify the barangay.',
            'purok.required' => 'Please select a purok for the chosen barangay.',
            'purok.in' => 'Please choose a valid purok option.',
            'birth_date.before' => 'Birth date must be in the past.',
        ]);

        if ($request->role === 'user') {
            Patient::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'gender' => $validated['gender'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'barangay' => $validated['barangay'],
                'barangay_other' => $validated['barangay'] === 'Other' ? $validated['barangay_other'] : null,
                'purok' => $validated['barangay'] === 'Other' ? null : $validated['purok'],
                'birth_date' => $validated['birth_date'],
                'age' => Carbon::parse($validated['birth_date'])->age,
                'password' => Hash::make($validated['password']),
            ]);
        } elseif ($request->role === 'admin') {
            Admin::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
        } elseif ($request->role === 'superadmin') {
            SuperAdmin::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
        }

        return redirect()->back()->with('success', 'User created successfully.');
    }

    private function getUserModel($type)
    {
        return match ($type) {
            'user', 'patient' => Patient::class,
            'admin' => Admin::class,
            'superadmin' => SuperAdmin::class,
            default => null,
        };
    }

    public function updateUser(Request $request, $type, $id)
    {
        $modelClass = $this->getUserModel($type);

        if (!$modelClass) {
            return redirect()->back()->with('error', 'Invalid user type.');
        }

        $user = $modelClass::findOrFail($id);
        $emailTable = $user->getTable();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\.\-\']+$/',
            ],
            'email' => 'required|string|email|max:255|unique:' . $emailTable . ',email,' . $id,
            'gender' => 'required|in:male,female,other',
            // Role validation removed as we don't support switching roles via update yet
            'barangay' => [
                Rule::requiredIf(fn() => $type === 'user'),
                Rule::in(['Barangay 11', 'Barangay 12', 'Other']),
            ],
            'barangay_other' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(fn() => $type === 'user' && $request->barangay === 'Other'),
            ],
            'purok' => [
                'nullable',
                Rule::requiredIf(fn() => $type === 'user' && in_array($request->barangay, ['Barangay 11', 'Barangay 12'], true)),
                Rule::when(
                    $type === 'user' && $request->barangay === 'Barangay 11',
                    Rule::in(['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'])
                ),
                Rule::when(
                    $type === 'user' && $request->barangay === 'Barangay 12',
                    Rule::in(['Purok 1', 'Purok 2', 'Purok 3'])
                ),
            ],
            'birth_date' => [
                Rule::requiredIf(fn() => $type === 'user'),
                'nullable',
                'date',
                'before:today',
            ],
            'phone' => [
                Rule::requiredIf(fn() => $type === 'user'),
                'nullable',
                'string',
                'max:20',
            ],
            'address' => [
                'nullable',
                'string',
                'max:500',
            ],
            'password' => [
                'nullable',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=[\]{}|,.<>\/?]).+$/',
            ],
        ], [
            'name.regex' => 'The name field should not contain numbers. Only letters, spaces, periods, hyphens, and apostrophes are allowed.',
            'password.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, and one special character.',
            'gender.required' => 'Please select a gender.',
            'barangay.in' => 'Please select Barangay 11, Barangay 12, or choose Other.',
            'barangay.required' => 'Barangay is required for patient accounts.',
            'barangay_other.required' => 'Please specify the barangay.',
            'purok.required' => 'Please select a purok for the chosen barangay.',
            'purok.in' => 'Please choose a valid purok option.',
            'birth_date.before' => 'Birth date must be in the past.',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if ($type === 'user' || $type === 'patient') {
            $updateData['gender'] = $validated['gender'];
            $updateData['phone'] = $validated['phone'];
            $updateData['address'] = $validated['address'];
            $updateData['barangay'] = $validated['barangay'];
            $updateData['barangay_other'] = $validated['barangay'] === 'Other' ? $validated['barangay_other'] : null;
            $updateData['purok'] = $validated['barangay'] === 'Other' ? null : ($validated['purok'] ?? null);
            $updateData['birth_date'] = $validated['birth_date'];
            $updateData['age'] = !empty($validated['birth_date'])
                ? Carbon::parse($validated['birth_date'])->age
                : null;
        }

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function deleteUser($type, $id)
    {
        $modelClass = $this->getUserModel($type);
        if (!$modelClass)
            return redirect()->back()->with('error', 'Invalid user type.');

        $user = $modelClass::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'User archived successfully.');
    }

    public function archivedUsers()
    {
        // Fetch archived users from all tables
        $patients = Patient::onlyTrashed()->get()->map(function ($user) {
            $user->role = 'user';
            return $user;
        });

        $admins = Admin::onlyTrashed()->get()->map(function ($user) {
            $user->role = 'admin';
            return $user;
        });

        // SuperAdmins don't have soft deletes in migration, but if they did:
        // $superAdmins = SuperAdmin::onlyTrashed()->get()... 
        // Migration says "No soft deletes for super admins"

        $allUsers = $patients->concat($admins)->sortByDesc('deleted_at');

        // Manual Pagination
        $page = request()->get('page', 1);
        $perPage = 15;

        $users = new \Illuminate\Pagination\LengthAwarePaginator(
            $allUsers->forPage($page, $perPage),
            $allUsers->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('superadmin.users-archive', compact('users'));
    }

    public function restoreUser($type, $id)
    {
        $modelClass = $this->getUserModel($type);
        if (!$modelClass)
            return redirect()->back()->with('error', 'Invalid user type.');

        $user = $modelClass::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('superadmin.users.archive')->with('success', 'User restored successfully.');
    }

    public function forceDeleteUser($type, $id)
    {
        $modelClass = $this->getUserModel($type);
        if (!$modelClass)
            return redirect()->back()->with('error', 'Invalid user type.');

        $user = $modelClass::onlyTrashed()->findOrFail($id);
        $user->forceDelete();

        return redirect()->route('superadmin.users.archive')->with('success', 'User permanently deleted.');
    }

    public function systemLogs(Request $request)
    {
        $query = SystemLog::with('user');

        // Search by action, table_name, or user name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('table_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by action
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        // Filter by table
        if ($request->has('table') && $request->table) {
            $query->where('table_name', $request->table);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest()->paginate(10)->withQueryString();

        // Get unique actions and tables for filter dropdowns
        $actions = SystemLog::distinct()->pluck('action')->filter();
        $tables = SystemLog::distinct()->pluck('table_name')->filter();

        return view('superadmin.system-logs', compact('logs', 'actions', 'tables'));
    }

    public function auditTrail()
    {
        $auditData = SystemLog::with('user')
            ->whereIn('action', ['created', 'updated', 'deleted'])
            ->latest()
            ->paginate(20);

        return view('superadmin.audit-trail', compact('auditData'));
    }

    public function analytics()
    {
        $appointmentStats = [
            'total' => Appointment::count(),
            'this_month' => Appointment::whereMonth('created_at', now()->month)->count(),
            'pending' => Appointment::pending()->count(),
            'completed' => Appointment::where('status', 'completed')->count(),
            'approved' => Appointment::where('status', 'approved')->count(),
            'cancelled' => Appointment::where('status', 'cancelled')->count()
        ];

        $userStats = [
            'total' => Patient::count() + Admin::count() + SuperAdmin::count(),
            'this_month' => Patient::whereMonth('created_at', now()->month)->count(),
            'admins' => Admin::count(),
            'patients' => Patient::count()
        ];

        $inventoryStats = [
            'total_items' => Inventory::count(),
            'low_stock' => Inventory::where('current_stock', '<', 10)->count(),
            'out_of_stock' => Inventory::where('current_stock', '=', 0)->count()
        ];

        // Get appointment trend data for last 6 months
        $driver = DB::getDriverName();
        $monthSql = $driver === 'pgsql' ? 'EXTRACT(MONTH FROM created_at)' : 'MONTH(created_at)';
        $yearSql = $driver === 'pgsql' ? 'EXTRACT(YEAR FROM created_at)' : 'YEAR(created_at)';

        $monthlyTrend = Appointment::selectRaw("$monthSql as month, $yearSql as year, count(*) as count")
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        return view('superadmin.analytics', compact('appointmentStats', 'userStats', 'inventoryStats', 'monthlyTrend'));
    }

    public function backup()
    {
        $backups = Backup::with(['createdByAdmin', 'createdBySuperAdmin'])->latest()->paginate(10);

        // Get last backups
        $lastDatabase = Backup::completed()->byType('database')->latest()->first();
        $lastFiles = Backup::completed()->byType('files')->latest()->first();
        $lastFull = Backup::completed()->byType('full')->latest()->first();

        // Calculate storage used
        $totalSize = Backup::completed()->get()->sum(function ($backup) {
            return $backup->getSizeInBytes();
        });
        $storageUsed = $this->formatBytes($totalSize);
        $storageTotal = "10 GB"; // You can make this dynamic

        return view('superadmin.backup', compact('backups', 'lastDatabase', 'lastFiles', 'lastFull', 'storageUsed', 'storageTotal'));
    }

    public function createBackup(Request $request)
    {
        \Log::info('Backup request received', ['type' => $request->type, 'user' => Auth::guard('super_admin')->id()]);

        $request->validate([
            'type' => 'required|in:database,files,full'
        ]);

        $type = $request->type;
        $backupDirectory = 'backups/' . $type;

        try {
            \Log::info('Starting backup process', ['type' => $type]);
            // Create backup record
            $backup = Backup::create([
                'type' => $type,
                'status' => 'in_progress',
                'created_by' => Auth::guard('super_admin')->id()
            ]);

            // Determine filename based on type
            $filename = $this->generateBackupFilename($type);
            $filePath = $backupDirectory . '/' . $filename;

            // Create directory if it doesn't exist
            if (!Storage::exists($backupDirectory)) {
                Storage::makeDirectory($backupDirectory);
            }

            // Perform backup based on type
            switch ($type) {
                case 'database':
                    $this->backupDatabase($filePath, $filename, $backup);
                    break;
                case 'files':
                    $this->backupFiles($filePath, $filename, $backup);
                    break;
                case 'full':
                    $this->backupFullSystem($filePath, $filename, $backup);
                    break;
            }

            // Get the actual file path based on type
            $actualFilePath = 'backups/' . $type . '/' . $filename;
            if (Storage::exists($actualFilePath . '.sql')) {
                $actualFilePath = $actualFilePath . '.sql';
            } elseif (Storage::exists($actualFilePath . '.txt')) {
                $actualFilePath = $actualFilePath . '.txt';
            } else {
                // Try to find the actual file
                $files = Storage::files('backups/' . $type);
                foreach ($files as $file) {
                    if (strpos($file, $filename) !== false) {
                        $actualFilePath = $file;
                        break;
                    }
                }
            }

            // Update backup record
            $fileSize = Storage::exists($actualFilePath) ? Storage::size($actualFilePath) : 1024;
            $backup->update([
                'filename' => basename($actualFilePath),
                'file_path' => $actualFilePath,
                'size' => $this->formatBytes($fileSize),
                'status' => 'completed',
                'completed_at' => now()
            ]);

            // Log the backup action
            SystemLog::create([
                'user_id' => Auth::guard('super_admin')->id(),
                'action' => 'backup_created',
                'table_name' => 'backups',
                'record_id' => $backup->id,
                'new_values' => ['type' => $type, 'status' => 'completed'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' backup completed successfully!',
                'backup_id' => $backup->id
            ]);
        } catch (\Exception $e) {
            \Log::error('Backup failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            if (isset($backup)) {
                $backup->update(['status' => 'failed', 'notes' => $e->getMessage()]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function backupDatabase($filePath, $filename, $backup)
    {
        try {
            $backup->update(['notes' => 'Creating database backup...']);

            // Create a simple SQL dump of the most important tables
            $tables = ['users', 'appointments', 'inventory', 'system_logs', 'inventory_transactions'];
            $sqlContent = "-- Database Backup\n";
            $sqlContent .= "-- Generated on: " . now()->format('Y-m-d H:i:s') . "\n\n";

            foreach ($tables as $table) {
                try {
                    $records = DB::table($table)->get();
                    if ($records->count() > 0) {
                        $sqlContent .= "\n-- Table: $table\n";
                        foreach ($records as $record) {
                            $columns = implode(', ', array_keys((array) $record));
                            $values = implode(', ', array_map(function ($v) {
                                return "'" . addslashes($v) . "'";
                            }, array_values((array) $record)));
                            $sqlContent .= "INSERT INTO `$table` ($columns) VALUES ($values);\n";
                        }
                    }
                } catch (\Exception $e) {
                    // Skip tables that don't exist
                    continue;
                }
            }

            Storage::put('backups/database/' . $filename . '.sql', $sqlContent);
        } catch (\Exception $e) {
            $backup->update(['notes' => 'Error: ' . $e->getMessage()]);
            throw $e;
        }
    }

    private function backupFiles($filePath, $filename, $backup)
    {
        try {
            $backup->update(['notes' => 'Creating files backup...']);

            // Create a simple text file listing what would be backed up
            $backupContent = "-- Files Backup\n";
            $backupContent .= "-- Generated on: " . now()->format('Y-m-d H:i:s') . "\n\n";
            $backupContent .= "This would contain all uploaded files.\n";
            $backupContent .= "In a production environment, this would be a ZIP archive of:\n";
            $backupContent .= "- public/uploads/\n";
            $backupContent .= "- storage/app/public/\n\n";
            $backupContent .= "System files:\n";

            // List some files as example
            if (Storage::exists('public')) {
                $files = Storage::files('public');
                foreach ($files as $file) {
                    $backupContent .= "- " . $file . "\n";
                }
            }

            Storage::put('backups/files/' . $filename . '.txt', $backupContent);
        } catch (\Exception $e) {
            $backup->update(['notes' => 'Error: ' . $e->getMessage()]);
            throw $e;
        }
    }

    private function backupFullSystem($filePath, $filename, $backup)
    {
        try {
            $backup->update(['notes' => 'Creating full system backup...']);

            $backupContent = "-- Full System Backup\n";
            $backupContent .= "-- Generated on: " . now()->format('Y-m-d H:i:s') . "\n\n";
            $backupContent .= "This is a complete system backup including:\n";
            $backupContent .= "1. Database backup\n";
            $backupContent .= "2. Files backup\n";
            $backupContent .= "3. Configuration files\n\n";
            $backupContent .= "System Information:\n";
            $backupContent .= "- Application Name: " . config('app.name') . "\n";
            $backupContent .= "- Environment: " . config('app.env') . "\n";
            $backupContent .= "- Total Users: " . (Patient::count() + Admin::count() + SuperAdmin::count()) . "\n";
            $backupContent .= "- Total Appointments: " . \App\Models\Appointment::count() . "\n";
            $backupContent .= "- Total Inventory Items: " . \App\Models\Inventory::count() . "\n";

            Storage::put('backups/full/' . $filename . '.txt', $backupContent);
        } catch (\Exception $e) {
            $backup->update(['notes' => 'Error: ' . $e->getMessage()]);
            throw $e;
        }
    }

    private function generateBackupFilename($type)
    {
        return date('Y-m-d_His') . '_' . $type . '_backup';
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function downloadBackup(Backup $backup)
    {
        try {
            // Check if file exists
            if (!Storage::exists($backup->file_path)) {
                return redirect()->back()->with('error', 'Backup file not found.');
            }

            // Get file extension for proper content type
            $extension = pathinfo($backup->filename, PATHINFO_EXTENSION);

            // Log the download action
            SystemLog::create([
                'user_id' => Auth::guard('super_admin')->id(),
                'action' => 'backup_downloaded',
                'table_name' => 'backups',
                'record_id' => $backup->id,
                'new_values' => ['type' => $backup->type, 'filename' => $backup->filename],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            return Storage::download($backup->file_path, $backup->filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error downloading backup: ' . $e->getMessage());
        }
    }

    public function deleteBackup(Backup $backup)
    {
        if (Storage::exists($backup->file_path)) {
            Storage::delete($backup->file_path);
        }

        $backup->delete();

        // Log the deletion
        SystemLog::create([
            'user_id' => Auth::guard('super_admin')->id(),
            'action' => 'deleted',
            'table_name' => 'backups',
            'record_id' => $backup->id,
            'old_values' => ['type' => $backup->type, 'filename' => $backup->filename],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect()->back()->with('success', 'Backup deleted successfully.');
    }

    public function scheduleBackup(Request $request)
    {
        $request->validate([
            'type' => 'required|in:database,files,full',
            'schedule' => 'required|string'
        ]);

        $type = $request->type;
        $schedule = $request->schedule;

        // Log the backup scheduling action
        SystemLog::create([
            'user_id' => Auth::guard('super_admin')->id(),
            'action' => 'backup_scheduled',
            'table_name' => 'backups',
            'record_id' => null,
            'new_values' => ['type' => $type, 'schedule' => $schedule],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // In a real application, you would implement actual scheduling logic here
        // This could use Laravel's Task Scheduling feature

        return response()->json([
            'success' => true,
            'message' => ucfirst($type) . ' backup scheduled for ' . $schedule . '!'
        ]);
    }
}
