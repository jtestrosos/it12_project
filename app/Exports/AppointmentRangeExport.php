<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class AppointmentRangeExport implements FromCollection, WithHeadings, WithTitle
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
                'Date' => optional($appt->appointment_date)->format('Y-m-d'),
                'Status' => ucfirst($appt->status),
                'Created At' => optional($appt->created_at)->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return ['ID', 'Patient Name', 'Date', 'Status', 'Created At'];
    }

    public function title(): string
    {
        return 'Appointments';
    }
}


