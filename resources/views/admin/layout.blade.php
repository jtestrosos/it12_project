@extends('layouts.admin')

@section('sidebar-menu')
<a class="nav-link @if(request()->routeIs('admin.dashboard')) active @endif" href="{{ route('admin.dashboard') }}" data-tooltip="Dashboard">
    <i class="fas fa-th-large"></i> <span class="sidebar-content">Dashboard</span>
</a>
<a class="nav-link @if(request()->routeIs('admin.patients') || request()->routeIs('admin.patients.archive')) active @endif" href="{{ route('admin.patients') }}" data-tooltip="Patient Management">
    <i class="fas fa-user"></i> <span class="sidebar-content">Patient Management</span>
</a>
<a class="nav-link @if(request()->routeIs('admin.appointments')) active @endif" href="{{ route('admin.appointments') }}" data-tooltip="Appointments">
    <i class="fas fa-calendar-check"></i> <span class="sidebar-content">Appointments</span>
</a>
<a class="nav-link @if(request()->routeIs('admin.reports')) active @endif" href="{{ route('admin.reports') }}" data-tooltip="Services & Reports">
    <i class="fas fa-chart-bar"></i> <span class="sidebar-content">Services & Reports</span>
</a>
<a class="nav-link @if(request()->routeIs('admin.inventory')) active @endif" href="{{ route('admin.inventory') }}" data-tooltip="Inventory">
    <i class="fas fa-box"></i> <span class="sidebar-content">Inventory</span>
</a>
@endsection

@section('user-initials')
{{ substr(Auth::user()->name, 0, 2) }}
@endsection

@section('user-name')
{{ Auth::user()->name }}
@endsection

@section('user-role')
{{ ucfirst(Auth::user()->role) }}
@endsection

