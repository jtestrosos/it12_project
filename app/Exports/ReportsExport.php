<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportsExport implements WithMultipleSheets
{
    protected Collection $appointments;
    protected Collection $inventory;
    protected Collection $patients;

    public function __construct(Collection $appointments, Collection $inventory, Collection $patients = null)
    {
        $this->appointments = $appointments;
        $this->inventory = $inventory;

        if ($this->appointments->isNotEmpty()) {
            $this->appointments->load('user');
        }

        $this->patients = $patients ?? $this->appointments
            ->pluck('user')
            ->filter()
            ->unique(fn ($user) => $user->id ?? spl_object_id($user))
            ->values();
    }

    public function sheets(): array
    {
        $patientAppointments = $this->appointments
            ->filter(fn ($appt) => $appt->user_id !== null)
            ->values();
        $walkInAppointments = $this->appointments
            ->filter(fn ($appt) => $appt->user_id === null)
            ->values();

        $sheets = [
            new ReportsPatientListSheet($this->patients),
        ];

        if ($patientAppointments->isNotEmpty()) {
            $sheets[] = new ReportsPatientAppointmentSheet($patientAppointments);
        }

        if ($walkInAppointments->isNotEmpty()) {
            $sheets[] = new ReportsWalkInAppointmentSheet($walkInAppointments);
        }

        if ($this->inventory->isNotEmpty()) {
            $sheets[] = new ReportsInventorySheet($this->inventory);
        }

        return $sheets;
    }
}

abstract class ReportsBaseAppointmentSheet implements FromCollection, WithHeadings, WithTitle
{
    protected Collection $appointments;

    public function __construct(Collection $appointments)
    {
        $this->appointments = $appointments;
    }

    public function collection()
    {
        return $this->appointments->map(function ($appt) {
            $user = $appt->user;

            $barangay = $barangayOther = $purok = $birthDate = $age = '';

            if ($user) {
                $barangay = $user->barangay === 'Other'
                    ? ($user->barangay_other ?: 'Other')
                    : ($user->barangay ?? '');
                $barangayOther = $user->barangay === 'Other'
                    ? ($user->barangay_other ?? '')
                    : '';
                $purok = $user->barangay === 'Other'
                    ? ''
                    : ($user->purok ?? '');

                if (!empty($user->birth_date)) {
                    $birthCarbon = Carbon::parse($user->birth_date);
                    $birthDate = $birthCarbon->format('Y-m-d');
                    $age = $user->age ?? $birthCarbon->age;
                } elseif (!is_null($user->age)) {
                    $age = $user->age;
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
                'Walk-in' => $appt->is_walk_in ? 'Yes' : 'No',
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
            'Walk-in',
            'Notes',
            'Created At'
        ];
    }

    abstract public function title(): string;
}

class ReportsPatientAppointmentSheet extends ReportsBaseAppointmentSheet
{
    public function title(): string
    {
        return 'Patient Appointments';
    }
}

class ReportsWalkInAppointmentSheet extends ReportsBaseAppointmentSheet
{
    public function title(): string
    {
        return 'Walk-in Appointments';
    }
}

class ReportsPatientListSheet implements FromCollection, WithHeadings, WithTitle
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
        return 'Patient Directory';
    }
}

class ReportsInventorySheet implements FromCollection, WithHeadings, WithTitle
{
    protected Collection $inventory;

    public function __construct(Collection $inventory)
    {
        $this->inventory = $inventory;
    }

    public function collection()
    {
        return $this->inventory->map(function ($item) {
            return [
                'ID' => $item->id,
                'Name' => $item->item_name,
                'Description' => $item->description ?? '',
                'Category' => $item->category ?? '',
                'Current Stock' => $item->current_stock,
                'Minimum Stock' => $item->minimum_stock,
                'Unit' => $item->unit ?? '',
                'Supplier' => $item->supplier ?? '',
                'Expiry Date' => optional($item->expiry_date)->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Description',
            'Category',
            'Current Stock',
            'Minimum Stock',
            'Unit',
            'Supplier',
            'Expiry Date'
        ];
    }

    public function title(): string
    {
        return 'Inventory';
    }
}
