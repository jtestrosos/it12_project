@extends('layouts.admin')

@section('sidebar-menu')
<a class="nav-link @if(request()->routeIs('admin.dashboard')) active @endif" href="{{ route('admin.dashboard') }}">
    <i class="fas fa-th-large me-2"></i> Dashboard
</a>
<a class="nav-link @if(request()->routeIs('admin.patients')) active @endif" href="{{ route('admin.patients') }}">
    <i class="fas fa-user me-2"></i> Patient Management
</a>
<a class="nav-link @if(request()->routeIs('admin.appointments')) active @endif" href="{{ route('admin.appointments') }}">
    <i class="fas fa-calendar-check me-2"></i> Appointments
</a>
<a class="nav-link @if(request()->routeIs('admin.reports')) active @endif" href="{{ route('admin.reports') }}">
    <i class="fas fa-chart-bar me-2"></i> Services & Reports
</a>
<a class="nav-link @if(request()->routeIs('admin.inventory')) active @endif" href="{{ route('admin.inventory') }}">
    <i class="fas fa-box me-2"></i> Inventory
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

