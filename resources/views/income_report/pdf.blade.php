<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pemasukan</title>
    <style>
        body { font-family: sans-serif; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; }
        .table th { background-color: #f2f2f2; text-align: left; }
        .text-right { text-align: right; }
        .header { text-align: center; margin-bottom: 20px; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Pemasukan</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMMM YYYY') }} - {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMMM YYYY') }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Deskripsi</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($incomes as $income)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($income->created_at)->isoFormat('D MMMM YYYY') }}</td>
                    <td>{{ $income->description }}</td>
                    <td class="text-right">Rp {{ number_format($income->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center;">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="total">Total Pemasukan</td>
                <td class="text-right total">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
