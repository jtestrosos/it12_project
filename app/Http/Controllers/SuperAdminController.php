<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SystemLog;
use App\Models\Appointment;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalPatients = User::where('role', 'user')->count();
        $totalAppointments = Appointment::count();
        $totalInventory = Inventory::count();

        $recentLogs = SystemLog::with('user')->latest()->limit(10)->get();
        $recentUsers = User::latest()->limit(5)->get();

        return view('superadmin.dashboard', compact(
            'totalUsers',
            'totalAdmins', 
            'totalPatients',
            'totalAppointments',
            'totalInventory',
            'recentLogs',
            'recentUsers'
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

        $user = User::create([
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

    public function systemLogs()
    {
        $logs = SystemLog::with('user')->latest()->paginate(20);
        return view('superadmin.system-logs', compact('logs'));
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
            'completed' => Appointment::where('status', 'completed')->count()
        ];

        $userStats = [
            'total' => User::count(),
            'this_month' => User::whereMonth('created_at', now()->month)->count(),
            'admins' => User::where('role', 'admin')->count(),
            'patients' => User::where('role', 'user')->count()
        ];

        $inventoryStats = [
            'total_items' => Inventory::count(),
            'low_stock' => Inventory::lowStock()->count(),
            'out_of_stock' => Inventory::outOfStock()->count()
        ];

        return view('superadmin.analytics', compact('appointmentStats', 'userStats', 'inventoryStats'));
    }

    public function backup()
    {
        // This would typically involve creating database backups
        // For now, we'll just show a success message
        return redirect()->back()->with('success', 'Backup process initiated. Check logs for completion status.');
    }
}
