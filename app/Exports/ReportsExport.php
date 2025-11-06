<?php

namespace App\Exports;

use App\Models\Appointment;
use App\Models\Inventory;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportsExport implements WithMultipleSheets
{
    protected $appointments;
    protected $inventory;
    public function __construct($appointments, $inventory)
    {
        $this->appointments = $appointments;
        $this->inventory = $inventory;
    }
    public function sheets(): array
    {
        return [
            new AppointmentsSheet($this->appointments),
            new InventorySheet($this->inventory),
        ];
    }
}

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AppointmentsSheet implements FromCollection, WithHeadings
{
    protected $appointments;
    public function __construct($appointments) { $this->appointments = $appointments; }
    public function collection()
    {
        return $this->appointments->map(function($appt) {
            return [
                $appt->id,
                $appt->patient_name,
                $appt->patient_phone,
                $appt->patient_address,
                $appt->appointment_date,
                $appt->appointment_time,
                $appt->service_type,
                $appt->status,
                $appt->notes
            ];
        });
    }
    public function headings(): array
    {
        return [
            'ID', 'Patient', 'Phone', 'Address', 'Date', 'Time', 'Service', 'Status', 'Notes'
        ];
    }
}

class InventorySheet implements FromCollection, WithHeadings
{
    protected $inventory;
    public function __construct($inventory) { $this->inventory = $inventory; }
    public function collection()
    {
        return $this->inventory->map(function($item) {
            return [
                $item->id,
                $item->item_name,
                $item->description,
                $item->category,
                $item->current_stock,
                $item->minimum_stock,
                $item->unit,
                $item->supplier,
                $item->expiry_date
            ];
        });
    }
    public function headings(): array
    {
        return [
            'ID', 'Name', 'Description', 'Category', 'Current Stock', 'Min Stock', 'Unit', 'Supplier', 'Expiry'
        ];
    }
}
