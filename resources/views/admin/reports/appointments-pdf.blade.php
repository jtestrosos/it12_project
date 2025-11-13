<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointments Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1f2933;
            margin: 0;
            padding: 24px;
        }
        h1, h2, h3 {
            margin: 0 0 12px;
            color: #111827;
        }
        .section {
            margin-bottom: 32px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            page-break-inside: avoid;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            font-weight: 600;
        }
        .meta {
            margin-bottom: 20px;
            font-size: 12px;
        }
        .meta span {
            display: inline-block;
            margin-right: 16px;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
            background-color: #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Appointments Summary</h1>
        <div class="meta">
            <span><strong>Coverage:</strong> {{ $startDate->format('F d, Y') }} &ndash; {{ $endDate->format('F d, Y') }}</span>
            <span><strong>Generated:</strong> {{ now()->format('F d, Y g:i A') }}</span>
            <span><strong>Total Appointments:</strong> {{ $patientAppointments->count() + $walkInAppointments->count() }}</span>
        </div>
    </div>

    <div class="section">
        <h2>Patient Directory</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Gender</th>
                    <th>Barangay</th>
                    <th>Purok</th>
                    <th>Birth Date</th>
                    <th>Age</th>
                    <th>Registered At</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($patients as $patient)
                    @php
                        $barangayLabel = $patient->barangay === 'Other'
                            ? ($patient->barangay_other ?: 'Other')
                            : ($patient->barangay ?? '');
                        $birthDate = $patient->birth_date ? \Illuminate\Support\Carbon::parse($patient->birth_date) : null;
                        $age = $patient->age ?? ($birthDate ? $birthDate->age : null);
                    @endphp
                    <tr>
                        <td>{{ $patient->name }}</td>
                        <td>{{ $patient->email }}</td>
                        <td>{{ $patient->phone ?? '—' }}</td>
                        <td>{{ ucfirst($patient->gender ?? '') }}</td>
                        <td>{{ $barangayLabel }}</td>
                        <td>{{ $patient->barangay === 'Other' ? '—' : ($patient->purok ?? '—') }}</td>
                        <td>{{ $birthDate ? $birthDate->format('Y-m-d') : '—' }}</td>
                        <td>{{ $age ?? '—' }}</td>
                        <td>{{ optional($patient->created_at)->format('Y-m-d g:i A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center;">No registered patients within the selected range.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Patient Appointments</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>Phone</th>
                    <th>Barangay / Purok</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Service</th>
                    <th>Status</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($patientAppointments as $appointment)
                    @php
                        $barangayLabel = optional($appointment->user)->barangay === 'Other'
                            ? (optional($appointment->user)->barangay_other ?: 'Other')
                            : (optional($appointment->user)->barangay ?? '');
                        $purokLabel = optional($appointment->user)->barangay === 'Other'
                            ? ''
                            : (optional($appointment->user)->purok ?? '');
                    @endphp
                    <tr>
                        <td>{{ $appointment->id }}</td>
                        <td>{{ $appointment->patient_name }}</td>
                        <td>{{ $appointment->patient_phone ?? '—' }}</td>
                        <td>{{ trim($barangayLabel . ' ' . ($purokLabel ? '· ' . $purokLabel : '')) ?: '—' }}</td>
                        <td>{{ optional($appointment->appointment_date)->format('Y-m-d') }}</td>
                        <td>{{ optional($appointment->appointment_time)->format('H:i') }}</td>
                        <td>{{ $appointment->service_type ?? '—' }}</td>
                        <td>{{ ucfirst($appointment->status) }}</td>
                        <td>{{ $appointment->notes ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center;">No patient appointments within the selected range.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Walk-in Appointments</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Service</th>
                    <th>Status</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($walkInAppointments as $appointment)
                    <tr>
                        <td>{{ $appointment->id }}</td>
                        <td>{{ $appointment->patient_name }}</td>
                        <td>{{ $appointment->patient_phone ?? '—' }}</td>
                        <td>{{ $appointment->patient_address ?? '—' }}</td>
                        <td>{{ optional($appointment->appointment_date)->format('Y-m-d') }}</td>
                        <td>{{ optional($appointment->appointment_time)->format('H:i') }}</td>
                        <td>{{ $appointment->service_type ?? '—' }}</td>
                        <td>{{ ucfirst($appointment->status) }}</td>
                        <td>{{ $appointment->notes ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center;">No walk-in appointments within the selected range.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>

