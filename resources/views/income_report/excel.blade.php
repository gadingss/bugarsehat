{{-- This view is used by Maatwebsite/Excel to generate the spreadsheet --}}
<table>
    <thead>
        <tr>
            <th colspan="3"><strong>Laporan Pemasukan</strong></th>
        </tr>
        <tr>
            <th colspan="3"><strong>Periode: {{ $startDate }} - {{ $endDate }}</strong></th>
        </tr>
        <tr>
            <th><strong>Tanggal</strong></th>
            <th><strong>Deskripsi</strong></th>
            <th><strong>Jumlah</strong></th>
        </tr>
    </thead>
    <tbody>
        @foreach($incomes as $income)
        <tr>
            <td>{{ \Carbon\Carbon::parse($income->created_at)->format('Y-m-d') }}</td>
            <td>{{ $income->description }}</td>
            <td>{{ $income->amount }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="2"><strong>Total Pemasukan</strong></td>
            <td><strong>{{ $totalIncome }}</strong></td>
        </tr>
    </tbody>
</table>
