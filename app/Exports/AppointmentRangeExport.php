<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AppointmentRangeExport implements WithMultipleSheets
{
    protected Collection $patients;
    protected Collection $appointments;
    protected Collection $inventory;
    protected Collection $walkIns;

    public function __construct(Collection $patients, Collection $appointments, Collection $inventory, Collection $walkIns)
    {
        $this->patients = $patients;
        $this->appointments = $appointments;
        $this->inventory = $inventory;
        $this->walkIns = $walkIns;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Sheet 1: All Patients
        if ($this->patients->isNotEmpty()) {
            $sheets[] = new PatientsSheet($this->patients);
        }

        // Sheet 2: Approved and Completed Appointments
        if ($this->appointments->isNotEmpty()) {
            $sheets[] = new AppointmentsSheet($this->appointments);
        }

        // Sheet 3: All Inventory Items
        if ($this->inventory->isNotEmpty()) {
            $sheets[] = new InventorySheet($this->inventory);
        }

        // Sheet 4: Walk-In Patients
        if ($this->walkIns->isNotEmpty()) {
            $sheets[] = new WalkInsSheet($this->walkIns);
        }

        return $sheets;
    }
}

// Sheet 1: All Patients
class PatientsSheet implements FromCollection, WithHeadings, WithTitle
{
    protected Collection $patients;

    public function __construct(Collection $patients)
    {
        $this->patients = $patients;
    }

    public function collection()
    {
        return $this->patients->map(function ($patient) {
            $barangay = $patient->barangay === 'Other'
                ? ($patient->barangay_other ?: 'Other')
                : ($patient->barangay ?? '');

            $birthDate = $patient->birth_date
                ? Carbon::parse($patient->birth_date)->format('Y-m-d')
                : '';

            $age = $patient->age ?? ($patient->birth_date ? Carbon::parse($patient->birth_date)->age : '');

            return [
                'ID' => $patient->id,
                'Name' => $patient->name,
                'Email' => $patient->email,
                'Phone' => $patient->phone ?? '',
                'Gender' => ucfirst($patient->gender ?? ''),
                'Barangay' => $barangay,
                'Purok' => $patient->barangay === 'Other' ? '' : ($patient->purok ?? ''),
                'Birth Date' => $birthDate,
                'Age' => $age,
                'Address' => $patient->address ?? '',
                'Registered At' => optional($patient->created_at)->format('Y-m-d H:i'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Gender',
            'Barangay',
            'Purok',
            'Birth Date',
            'Age',
            'Address',
            'Registered At',
        ];
    }

    public function title(): string
    {
        return 'Patients';
    }
}

// Sheet 2: Approved and Completed Appointments
class AppointmentsSheet implements FromCollection, WithHeadings, WithTitle
{
    protected Collection $appointments;

    public function __construct(Collection $appointments)
    {
        $this->appointments = $appointments;
    }

    public function collection()
    {
        return $this->appointments->map(function ($appt) {
            $patient = $appt->patient;

            $barangay = $barangayOther = $purok = $birthDate = $age = '';

            if ($patient) {
                $barangay = $patient->barangay === 'Other'
                    ? ($patient->barangay_other ?: 'Other')
                    : ($patient->barangay ?? '');
                $barangayOther = $patient->barangay === 'Other'
                    ? ($patient->barangay_other ?? '')
                    : '';
                $purok = $patient->barangay === 'Other'
                    ? ''
                    : ($patient->purok ?? '');

                if (!empty($patient->birth_date)) {
                    $birthCarbon = Carbon::parse($patient->birth_date);
                    $birthDate = $birthCarbon->format('Y-m-d');
                    $age = $patient->age ?? $birthCarbon->age;
                } elseif (!is_null($patient->age)) {
                    $age = $patient->age;
                }
            }

            return [
                'ID' => $appt->id,
                'Patient Name' => $appt->patient_name,
                'Phone' => $appt->patient_phone ?? '',
                'Address' => $appt->patient_address ?? '',
                'Barangay' => $barangay,
                'Barangay Other' => $barangayOther,
                'Purok' => $purok,
                'Birth Date' => $birthDate,
                'Age' => $age,
                'Date' => optional($appt->appointment_date)->format('Y-m-d'),
                'Time' => optional($appt->appointment_time)->format('H:i'),
                'Service Type' => $appt->service_type ?? '',
                'Status' => ucfirst($appt->status),
                'Notes' => $appt->notes ?? '',
                'Created At' => optional($appt->created_at)->format('Y-m-d H:i'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Patient Name',
            'Phone',
            'Address',
            'Barangay',
            'Barangay Other',
            'Purok',
            'Birth Date',
            'Age',
            'Date',
            'Time',
            'Service Type',
            'Status',
            'Notes',
            'Created At'
        ];
    }

    public function title(): string
    {
        return 'Appointments';
    }
}

// Sheet 3: All Inventory Items
class InventorySheet implements FromCollection, WithHeadings, WithTitle
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
                'ID' => $item->id,
                'Item Name' => $item->item_name,
                'Description' => $item->description ?? '',
                'Category' => $item->category,
                'Current Stock' => $item->current_stock,
                'Minimum Stock' => $item->minimum_stock,
                'Unit' => $item->unit ?? '',
                'Unit Price' => $item->unit_price ?? '',
                'Total Used' => $totalUsed,
                'Expiry Date' => optional($item->expiry_date)->format('Y-m-d'),
                'Supplier' => $item->supplier ?? '',
                'Location' => $item->location ?? '',
                'Status' => ucfirst($item->status ?? ''),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Item Name',
            'Description',
            'Category',
            'Current Stock',
            'Minimum Stock',
            'Unit',
            'Unit Price',
            'Total Used',
            'Expiry Date',
            'Supplier',
            'Location',
            'Status'
        ];
    }

    public function title(): string
    {
        return 'Inventory';
    }
}

// Sheet 4: Walk-In Patients
class WalkInsSheet implements FromCollection, WithHeadings, WithTitle
{
    protected Collection $walkIns;

    public function __construct(Collection $walkIns)
    {
        $this->walkIns = $walkIns;
    }

    public function collection()
    {
        return $this->walkIns->map(function ($appt) {
            $patient = $appt->patient;

            $barangay = $barangayOther = $purok = $birthDate = $age = '';

            if ($patient) {
                $barangay = $patient->barangay === 'Other'
                    ? ($patient->barangay_other ?: 'Other')
                    : ($patient->barangay ?? '');
                $barangayOther = $patient->barangay === 'Other'
                    ? ($patient->barangay_other ?? '')
                    : '';
                $purok = $patient->barangay === 'Other'
                    ? ''
                    : ($patient->purok ?? '');

                if (!empty($patient->birth_date)) {
                    $birthCarbon = Carbon::parse($patient->birth_date);
                    $birthDate = $birthCarbon->format('Y-m-d');
                    $age = $patient->age ?? $birthCarbon->age;
                } elseif (!is_null($patient->age)) {
                    $age = $patient->age;
                }
            }

            return [
                'ID' => $appt->id,
                'Patient Name' => $appt->patient_name,
                'Phone' => $appt->patient_phone ?? '',
                'Address' => $appt->patient_address ?? '',
                'Barangay' => $barangay,
                'Barangay Other' => $barangayOther,
                'Purok' => $purok,
                'Birth Date' => $birthDate,
                'Age' => $age,
                'Date' => optional($appt->appointment_date)->format('Y-m-d'),
                'Time' => optional($appt->appointment_time)->format('H:i'),
                'Service Type' => $appt->service_type ?? '',
                'Status' => ucfirst($appt->status),
                'Notes' => $appt->notes ?? '',
                'Created At' => optional($appt->created_at)->format('Y-m-d H:i'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Patient Name',
            'Phone',
            'Address',
            'Barangay',
            'Barangay Other',
            'Purok',
            'Birth Date',
            'Age',
            'Date',
            'Time',
            'Service Type',
            'Status',
            'Notes',
            'Created At'
        ];
    }

    public function title(): string
    {
        return 'Walk-Ins';
    }
}


