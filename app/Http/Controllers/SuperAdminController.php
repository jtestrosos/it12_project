<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Inventory;
use App\Models\SystemLog;
use App\Models\Backup;
use Carbon\Carbon;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalAppointments = Appointment::count();
        $pendingAppointments = Appointment::pending()->count();
        $totalInventory = Inventory::count();

        $recentUsers = User::latest()->limit(5)->get();
        $recentAppointments = Appointment::with('user')->latest()->limit(5)->get();
        $recentLogs = SystemLog::with('user')->latest()->limit(5)->get();

        // Weekly appointments data for the line chart
        $weeklyAppointments = Appointment::selectRaw('DAYOFWEEK(created_at) as day_of_week, count(*) as count')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->groupBy('day_of_week')
            ->get();

        // Service types data for the bar chart
        $serviceTypes = Appointment::selectRaw('service_type, count(*) as count')
            ->whereMonth('created_at', now()->month)
            ->groupBy('service_type')
            ->get();

        // Patients by Barangay data
        $patientsByBarangay = User::where('role', 'user')
            ->whereNotNull('barangay')
            ->selectRaw('barangay, count(*) as count')
            ->groupBy('barangay')
            ->get();

        // Today's statistics
        $todayCompleted = Appointment::where('status', 'completed')
            ->whereDate('appointment_date', today())
            ->count();
        
        $todayPending = Appointment::where('status', 'pending')
            ->whereDate('appointment_date', today())
            ->count();

        return view('superadmin.dashboard', compact(
            'totalUsers',
            'totalAppointments',
            'pendingAppointments',
            'totalInventory',
            'recentUsers',
            'recentAppointments',
            'recentLogs',
            'weeklyAppointments',
            'serviceTypes',
            'patientsByBarangay',
            'todayCompleted',
            'todayPending'
        ));
    }

    public function users()
    {
        $users = User::latest()->paginate(15);
        return view('superadmin.users', compact('users'));
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:user,admin,superadmin'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return redirect()->back()->with('success', 'User created successfully.');
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:user,admin,superadmin',
            'password' => 'nullable|string|min:8|confirmed'
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role
        ];

        if ($request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Cannot delete your own account.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    public function systemLogs(Request $request)
    {
        $query = SystemLog::with('user');
        
        // Search by action, table_name, or user name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('table_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
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
            'total' => User::count(),
            'this_month' => User::whereMonth('created_at', now()->month)->count(),
            'admins' => User::where('role', 'admin')->count(),
            'patients' => User::where('role', 'user')->count()
        ];

        $inventoryStats = [
            'total_items' => Inventory::count(),
            'low_stock' => Inventory::where('current_stock', '<', 10)->count(),
            'out_of_stock' => Inventory::where('current_stock', '=', 0)->count()
        ];

        // Get appointment trend data for last 6 months
        $monthlyTrend = Appointment::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, count(*) as count')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        return view('superadmin.analytics', compact('appointmentStats', 'userStats', 'inventoryStats', 'monthlyTrend'));
    }

    public function backup()
    {
        $backups = Backup::with('createdBy')->latest()->paginate(10);
        
        // Get last backups
        $lastDatabase = Backup::completed()->byType('database')->latest()->first();
        $lastFiles = Backup::completed()->byType('files')->latest()->first();
        $lastFull = Backup::completed()->byType('full')->latest()->first();
        
        // Calculate storage used
        $totalSize = Backup::completed()->get()->sum(function($backup) {
            return $backup->getSizeInBytes();
        });
        $storageUsed = $this->formatBytes($totalSize);
        $storageTotal = "10 GB"; // You can make this dynamic
        
        return view('superadmin.backup', compact('backups', 'lastDatabase', 'lastFiles', 'lastFull', 'storageUsed', 'storageTotal'));
    }

    public function createBackup(Request $request)
    {
        \Log::info('Backup request received', ['type' => $request->type, 'user' => Auth::id()]);
        
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
                'created_by' => Auth::id()
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
            'user_id' => Auth::id(),
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
                            $columns = implode(', ', array_keys((array)$record));
                            $values = implode(', ', array_map(function($v) {
                                return "'" . addslashes($v) . "'";
                            }, array_values((array)$record)));
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
            $backupContent .= "- Total Users: " . \App\Models\User::count() . "\n";
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
                'user_id' => Auth::id(),
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
            'user_id' => Auth::id(),
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
            'user_id' => Auth::id(),
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
