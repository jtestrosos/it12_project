@extends('layouts.admin')

@section('sidebar-menu')
<a class="nav-link @if(request()->routeIs('superadmin.dashboard')) active @endif" href="{{ route('superadmin.dashboard') }}">
    <i class="fas fa-th-large me-2"></i> Dashboard
</a>
<a class="nav-link @if(request()->routeIs('superadmin.users')) active @endif" href="{{ route('superadmin.users') }}">
    <i class="fas fa-user me-2"></i> User Management
</a>
<a class="nav-link @if(request()->routeIs('superadmin.system-logs')) active @endif" href="{{ route('superadmin.system-logs') }}">
    <i class="fas fa-list me-2"></i> System Logs
</a>
<a class="nav-link @if(request()->routeIs('superadmin.analytics')) active @endif" href="{{ route('superadmin.analytics') }}">
    <i class="fas fa-chart-bar me-2"></i> Analytics
</a>
<a class="nav-link @if(request()->routeIs('superadmin.backup')) active @endif" href="{{ route('superadmin.backup') }}">
    <i class="fas fa-download me-2"></i> Backup
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

