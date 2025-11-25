<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Patient Reports</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { text-align: center; color: #333; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f8f9fa; padding: 10px; text-align: left; border: 1px solid #dee2e6; font-weight: bold; }
        td { padding: 8px; border: 1px solid #dee2e6; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        .header { text-align: center; margin-bottom: 30px; }
        .date { text-align: center; color: #666; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Patient Reports</h1>
    </div>
    <div class="date">
        Generated on: {{ now()->format('F d, Y') }}
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Gender</th>
                <th>Age</th>
                <th>Barangay</th>
                <th>Registered</th>
            </tr>
        </thead>
        <tbody>
            @foreach($patients as $patient)
            <tr>
                <td>{{ $patient->name }}</td>
                <td>{{ $patient->email }}</td>
                <td>{{ ucfirst($patient->gender ?? 'N/A') }}</td>
                <td>{{ $patient->age ?? 'N/A' }}</td>
                <td>{{ $patient->barangay === 'Other' ? $patient->barangay_other : $patient->barangay }}</td>
                <td>{{ $patient->created_at->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 30px; text-align: center; color: #666; font-size: 10px;">
        Total Patients: {{ $patients->count() }}
    </div>
</body>
</html>
