<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
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
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            padding: 3px 7px;
            border-radius: 4px;
            color: #fff;
            font-size: 10px;
        }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: #333; }
        .badge-danger { background-color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Laporan Transaksi</h1>
        <p>Tanggal Ekspor: {{ now()->format('d F Y H:i') }}</p>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Member</th>
                    <th>Email</th>
                    <th>Produk</th>
                    <th class="text-right">Jumlah</th>
                    <th class="text-right">Total</th>
                    <th class="text-center">Status</th>
                    <th>Validator</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $index => $transaction)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $transaction->transaction_date->format('d/m/Y H:i') }}</td>
                        <td>{{ $transaction->user->name ?? '-' }}</td>
                        <td>{{ $transaction->user->email ?? '-' }}</td>
                        <td>{{ $transaction->product->name ?? '-' }}</td>
                        <td class="text-right">{{ number_format($transaction->quantity) }}</td>
                        <td class="text-right">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if($transaction->status == 'validated')
                                <span class="badge badge-success">Validated</span>
                            @elseif($transaction->status == 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @else
                                <span class="badge badge-danger">Cancelled</span>
                            @endif
                        </td>
                        <td>{{ $transaction->validator->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada data untuk ditampilkan.</td>
                    </tr>
                @endforelse
                @if($transactions->isNotEmpty())
                    <tr>
                        <td colspan="5" class="text-right"><strong>TOTAL</strong></td>
                        <td class="text-right"><strong>{{ number_format($transactions->sum('quantity')) }}</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($transactions->sum('amount'), 0, ',', '.') }}</strong></td>
                        <td colspan="2"></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</body>
</html>