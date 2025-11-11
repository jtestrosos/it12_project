<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AppointmentRangeExport implements WithMultipleSheets
{
    protected Collection $appointments;
    protected Collection $inventory;

    public function __construct(Collection $appointments, Collection $inventory = null)
    {
        $this->appointments = $appointments;
        $this->inventory = $inventory ?? collect();
    }

    public function sheets(): array
    {
        $sheets = [
            new AppointmentSheet($this->appointments),
        ];

        if ($this->inventory->isNotEmpty()) {
            $sheets[] = new MedicineSheet($this->inventory);
        }

        return $sheets;
    }
}

class AppointmentSheet implements FromCollection, WithHeadings, WithTitle
{
    protected Collection $appointments;

    public function __construct(Collection $appointments)
    {
        $this->appointments = $appointments;
    }

    public function collection()
    {
        return $this->appointments->map(function ($appt) {
            return [
                'ID' => $appt->id,
                'Patient Name' => $appt->patient_name,
                'Phone' => $appt->patient_phone ?? '',
                'Address' => $appt->patient_address ?? '',
                'Date' => optional($appt->appointment_date)->format('Y-m-d'),
                'Time' => optional($appt->appointment_time)->format('H:i'),
                'Service Type' => $appt->service_type ?? '',
                'Status' => ucfirst($appt->status),
                'Walk-in' => $appt->is_walk_in ? 'Yes' : 'No',
                'Notes' => $appt->notes ?? '',
                'Created At' => optional($appt->created_at)->format('Y-m-d H:i'),
            ];
        });
    }

    public function headings(): array
    {
        return ['ID', 'Patient Name', 'Phone', 'Address', 'Date', 'Time', 'Service Type', 'Status', 'Walk-in', 'Notes', 'Created At'];
    }

    public function title(): string
    {
        return 'Patient List';
    }
}

class MedicineSheet implements FromCollection, WithHeadings, WithTitle
{
    protected Collection $inventory;

    public function __construct(Collection $inventory)
    {
        $this->inventory = $inventory;
    }

    public function collection()
    {
        return $this->inventory->map(function ($item) {
            // Calculate total usage from loaded transactions
            $totalUsed = $item->transactions
                ->where('transaction_type', 'usage')
                ->sum('quantity') ?? 0;

            return [
                'Item Name' => $item->item_name,
                'Category' => $item->category,
                'Stock' => $item->current_stock,
                'Min Stock' => $item->minimum_stock,
                'Total Used' => $totalUsed,
                'Expiry Date' => optional($item->expiry_date)->format('Y-m-d'),
                'Location' => $item->location ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return ['Item Name', 'Category', 'Stock', 'Min Stock', 'Total Used', 'Expiry Date', 'Location'];
    }

    public function title(): string
    {
        return 'Medicine';
    }
}


