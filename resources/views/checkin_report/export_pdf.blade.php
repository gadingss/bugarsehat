<!DOCTYPE html>
<html>
<head>
    <title>Check-in Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-active {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Check-in Bugar Sehat</h1>
        <p>Periode: {{ now()->format('d F Y') }}</p>
        <p>Total Check-in: {{ $checkins->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Member</th>
                <th>Email</th>
                <th>Paket Membership</th>
                <th>Waktu Check-in</th>
                <th>Waktu Check-out</th>
                <th>Durasi</th>
                <th>Staff</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($checkins as $index => $checkin)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $checkin->user->name }}</td>
                <td>{{ $checkin->user->email }}</td>
                <td>{{ $checkin->membership->package->name ?? '-' }}</td>
                <td>{{ $checkin->checkin_time->format('d/m/Y H:i') }}</td>
                <td>
                    @if($checkin->checkout_time)
                        {{ $checkin->checkout_time->format('d/m/Y H:i') }}
                    @else
                        Belum checkout
                    @endif
                </td>
                <td>
                    @if($checkin->checkout_time)
                        {{ $checkin->getFormattedDuration() }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($checkin->staff)
                        {{ $checkin->staff->name }}
                    @else
                        Mandiri
                    @endif
                </td>
                <td>
                    @if($checkin->checkout_time)
                        <span class="status-badge status-completed">Selesai</span>
                    @else
                        <span class="status-badge status-active">Aktif</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; padding: 20px;">Tidak ada data check-in tersedia.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right;">
        <p>Generated on: {{ now()->format('d F Y H:i') }}</p>
    </div>
</body>
</html>
