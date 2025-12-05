<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Barangay Health Center Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #1f2933;
            margin: 0;
            padding: 20px;
        }

        h1,
        h2,
        h3 {
            margin: 0 0 10px;
            color: #111827;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 8px;
        }

        h2 {
            font-size: 14px;
            margin-top: 20px;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 2px solid #3b82f6;
        }

        .section {
            margin-bottom: 24px;
            page-break-inside: avoid;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 9px;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 4px 6px;
            text-align: left;
        }

        th {
            background-color: #f3f4f6;
            font-weight: 600;
            font-size: 9px;
        }

        .meta {
            margin-bottom: 16px;
            font-size: 11px;
            background: #f9fafb;
            padding: 8px;
            border-radius: 4px;
        }

        .meta span {
            display: inline-block;
            margin-right: 16px;
        }

        .page-break {
            page-break-after: always;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Barangay Health Center Comprehensive Report</h1>
        <div class="meta">
            <span><strong>Coverage:</strong> {{ $startDate->format('F d, Y') }} &ndash;
                {{ $endDate->format('F d, Y') }}</span>
            <span><strong>Generated:</strong> {{ now()->format('F d, Y g:i A') }}</span>
            <span><strong>Total Patients:</strong> {{ $patients->count() }}</span>
            <span><strong>Total Appointments:</strong> {{ $appointments->count() }}</span>
        </div>
    </div>

    <!-- Section 1: All Patients -->
    <div class="section">
        <h2>1. Patients ({{ $patients->count() }})</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Gender</th>
                    <th>Barangay</th>
                    <th>Purok</th>
                    <th>Birth Date</th>
                    <th>Age</th>
                    <th>Registered</th>
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
                        <td>{{ $patient->id }}</td>
                        <td>{{ $patient->name }}</td>
                        <td>{{ $patient->email }}</td>
                        <td>{{ $patient->phone ?? '—' }}</td>
                        <td>{{ ucfirst($patient->gender ?? '') }}</td>
                        <td>{{ $barangayLabel }}</td>
                        <td>{{ $patient->barangay === 'Other' ? '—' : ($patient->purok ?? '—') }}</td>
                        <td>{{ $birthDate ? $birthDate->format('Y-m-d') : '—' }}</td>
                        <td>{{ $age ?? '—' }}</td>
                        <td>{{ optional($patient->created_at)->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">No patients found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Section 2: Approved and Completed Appointments -->
    <div class="section">
        <h2>2. Appointments - Approved & Completed ({{ $appointments->count() }})</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>Phone</th>
                    <th>Barangay</th>
                    <th>Purok</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Service</th>
                    <th>Status</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($appointments as $appointment)
                    @php
                        $barangayLabel = optional($appointment->patient)->barangay === 'Other'
                            ? (optional($appointment->patient)->barangay_other ?: 'Other')
                            : (optional($appointment->patient)->barangay ?? '');
                        $purokLabel = optional($appointment->patient)->barangay === 'Other'
                            ? ''
                            : (optional($appointment->patient)->purok ?? '');
                    @endphp
                    <tr>
                        <td>{{ $appointment->id }}</td>
                        <td>{{ $appointment->patient_name }}</td>
                        <td>{{ $appointment->patient_phone ?? '—' }}</td>
                        <td>{{ $barangayLabel ?: '—' }}</td>
                        <td>{{ $purokLabel ?: '—' }}</td>
                        <td>{{ optional($appointment->appointment_date)->format('Y-m-d') }}</td>
                        <td>{{ optional($appointment->appointment_time)->format('H:i') }}</td>
                        <td>{{ $appointment->service_type ?? '—' }}</td>
                        <td>{{ ucfirst($appointment->status) }}</td>
                        <td>{{ $appointment->notes ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">No approved or completed appointments within the selected
                            range.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Section 3: All Inventory Items -->
    <div class="section">
        <h2>3. Inventory ({{ $inventory->count() }})</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Current Stock</th>
                    <th>Min Stock</th>
                    <th>Unit</th>
                    <th>Total Used</th>
                    <th>Expiry Date</th>
                    <th>Location</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($inventory as $item)
                    @php
                        $totalUsed = $item->transactions
                            ->where('transaction_type', 'usage')
                            ->sum('quantity') ?? 0;
                    @endphp
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->category }}</td>
                        <td>{{ $item->current_stock }}</td>
                        <td>{{ $item->minimum_stock }}</td>
                        <td>{{ $item->unit ?? '—' }}</td>
                        <td>{{ $totalUsed }}</td>
                        <td>{{ optional($item->expiry_date)->format('Y-m-d') ?? '—' }}</td>
                        <td>{{ $item->location ?? '—' }}</td>
                        <td>{{ ucfirst($item->status ?? 'N/A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">No inventory items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Section 4: Walk-In Patients -->
    <div class="section">
        <h2>4. Walk-Ins ({{ $walkIns->count() }})</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Barangay</th>
                    <th>Purok</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Service</th>
                    <th>Status</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($walkIns as $appointment)
                    @php
                        $barangayLabel = optional($appointment->patient)->barangay === 'Other'
                            ? (optional($appointment->patient)->barangay_other ?: 'Other')
                            : (optional($appointment->patient)->barangay ?? '');
                        $purokLabel = optional($appointment->patient)->barangay === 'Other'
                            ? ''
                            : (optional($appointment->patient)->purok ?? '');
                    @endphp
                    <tr>
                        <td>{{ $appointment->id }}</td>
                        <td>{{ $appointment->patient_name }}</td>
                        <td>{{ $appointment->patient_phone ?? '—' }}</td>
                        <td>{{ $appointment->patient_address ?? '—' }}</td>
                        <td>{{ $barangayLabel ?: '—' }}</td>
                        <td>{{ $purokLabel ?: '—' }}</td>
                        <td>{{ optional($appointment->appointment_date)->format('Y-m-d') }}</td>
                        <td>{{ optional($appointment->appointment_time)->format('H:i') }}</td>
                        <td>{{ $appointment->service_type ?? '—' }}</td>
                        <td>{{ ucfirst($appointment->status) }}</td>
                        <td>{{ $appointment->notes ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center">No walk-in appointments within the selected range.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>

</html>