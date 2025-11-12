@extends('layouts.admin')

@section('sidebar-menu')
<a class="nav-link @if(request()->routeIs('superadmin.dashboard')) active @endif" href="{{ route('superadmin.dashboard') }}" data-tooltip="Dashboard">
    <i class="fas fa-th-large"></i> <span class="sidebar-content">Dashboard</span>
</a>
<a class="nav-link @if(request()->routeIs('superadmin.users')) active @endif" href="{{ route('superadmin.users') }}" data-tooltip="User Management">
    <i class="fas fa-user"></i> <span class="sidebar-content">User Management</span>
</a>
<a class="nav-link @if(request()->routeIs('superadmin.system-logs')) active @endif" href="{{ route('superadmin.system-logs') }}" data-tooltip="System Logs">
    <i class="fas fa-list"></i> <span class="sidebar-content">System Logs</span>
</a>
<a class="nav-link @if(request()->routeIs('superadmin.analytics')) active @endif" href="{{ route('superadmin.analytics') }}" data-tooltip="Analytics">
    <i class="fas fa-chart-bar"></i> <span class="sidebar-content">Analytics</span>
</a>
<a class="nav-link @if(request()->routeIs('superadmin.backup')) active @endif" href="{{ route('superadmin.backup') }}" data-tooltip="Backup">
    <i class="fas fa-download"></i> <span class="sidebar-content">Backup</span>
</a>
@endsection

@section('user-initials')
SA
@endsection

@section('user-name')
Super Admin
@endsection

@section('user-role')
Administrator
@endsection

