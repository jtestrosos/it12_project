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

<!-- Services & Reports Dropdown -->
<div x-data="{ open: {{ request()->routeIs('admin.services.*') || request()->routeIs('admin.reports.*') ? 'true' : 'false' }} }" class="sidebar-dropdown">
    <a class="nav-link @if(request()->routeIs('admin.services.*') || request()->routeIs('admin.reports.*')) active @endif" 
       href="#" 
       @click.prevent="open = !open"
       data-tooltip="Services & Reports">
        <i class="fas fa-chart-bar"></i><span class="sidebar-content">Services & Reports</span>
        <i class="fas fa-chevron-down dropdown-arrow" :class="{ 'rotate-180': open }"></i>
    </a>
    
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="submenu">
        
        <!-- Services Section -->
        <div class="submenu-section">
            <div class="submenu-header">Services</div>
            <a class="nav-link submenu-link @if(request()->routeIs('admin.services.index')) active @endif" 
               href="{{ route('admin.services.index') }}" 
               data-tooltip="Manage Services">
                <i class="fas fa-briefcase-medical"></i> 
                <span class="sidebar-content">Manage Services</span>
            </a>
        </div>

        <!-- Reports Section -->
        <div class="submenu-section">
            <div class="submenu-header">Reports</div>
            <a class="nav-link submenu-link @if(request()->routeIs('admin.reports.analytics')) active @endif" 
               href="{{ route('admin.reports.analytics') }}" 
               data-tooltip="Analytics">
                <i class="fas fa-chart-line"></i> 
                <span class="sidebar-content">Analytics</span>
            </a>
            <a class="nav-link submenu-link @if(request()->routeIs('admin.reports.patients')) active @endif" 
               href="{{ route('admin.reports.patients') }}" 
               data-tooltip="Patient Reports">
                <i class="fas fa-user-injured"></i> 
                <span class="sidebar-content">Patient Reports</span>
            </a>
            <a class="nav-link submenu-link @if(request()->routeIs('admin.reports.inventory')) active @endif" 
               href="{{ route('admin.reports.inventory') }}" 
               data-tooltip="Inventory Reports">
                <i class="fas fa-boxes"></i> 
                <span class="sidebar-content">Inventory Reports</span>
            </a>
        </div>
    </div>
</div>

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

