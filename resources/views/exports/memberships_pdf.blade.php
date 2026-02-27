<!DOCTYPE html>
<html>
<head>
    <title>Laporan Membership</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; }
        .header p { margin: 0; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #777; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .badge { display: inline-block; padding: .35em .65em; font-size: .75em; font-weight: 700; line-height: 1; color: #fff; text-align: center; white-space: nowrap; vertical-align: baseline; border-radius: .25rem; }
        .bg-success { background-color: #198754; }
        .bg-danger { background-color: #dc3545; }
        .bg-secondary { background-color: #6c757d; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Membership</h1>
        <p>Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th>Nama Member</th>
                <th>Paket</th>
                <th class="text-center">Status</th>
                <th>Tanggal Aktivasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($memberships as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->user->name ?? '-' }}</td>
                    <td>{{ $item->package->name ?? '-' }}</td>
                    <td class="text-center">
                        @if($item->status == 'active')
                            <span class="badge bg-success">Aktif</span>
                        @elseif($item->status == 'expired')
                            <span class="badge bg-danger">Expired</span>
                        @else
                            <span class="badge bg-secondary">{{ $item->status }}</span>
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d F Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data yang ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>