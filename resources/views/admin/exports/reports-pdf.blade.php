<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Services & Reports Export</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #333333; padding: 7px 5px; }
        th { background: #f2f2f2; }
        h2 { margin-top: 32px; margin-bottom: 12px; }
    </style>
</head>
<body>
    <h1>Barangay Health Center: Services & Reports (PDF Export)</h1>

    <h2>Appointments</h2>
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
            @foreach($appointments as $appt)
            <tr>
                <td>{{ $appt->id }}</td>
                <td>{{ $appt->patient_name }}</td>
                <td>{{ $appt->patient_phone }}</td>
                <td>{{ $appt->patient_address }}</td>
                <td>{{ $appt->appointment_date }}</td>
                <td>{{ $appt->appointment_time }}</td>
                <td>{{ $appt->service_type }}</td>
                <td>{{ ucfirst($appt->status) }}</td>
                <td>{{ $appt->notes }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Inventory</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Category</th>
                <th>Current Stock</th>
                <th>Min. Stock</th>
                <th>Unit</th>
                <th>Supplier</th>
                <th>Expiry</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventory as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->item_name }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->category }}</td>
                <td>{{ $item->current_stock }}</td>
                <td>{{ $item->minimum_stock }}</td>
                <td>{{ $item->unit }}</td>
                <td>{{ $item->supplier }}</td>
                <td>{{ $item->expiry_date }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
