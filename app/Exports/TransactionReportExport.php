<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TransactionReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $transactions;

    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tanggal Transaksi',
            'Member',
            'Email Member',
            'Produk',
            'Jumlah',
            'Total Harga',
            'Status',
            'Validator',
            'Tanggal Validasi'
        ];
    }

    public function map($transaction): array
    {
        try {
            return [
                $transaction->id ?? '',
                $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y H:i') : '-',
                $transaction->user ? ($transaction->user->name ?? 'N/A') : 'N/A',
                $transaction->user ? ($transaction->user->email ?? 'N/A') : 'N/A',
                $transaction->product ? ($transaction->product->name ?? 'N/A') : 'N/A',
                $transaction->quantity ?? 0,
                number_format($transaction->amount ?? 0, 2, ',', '.'),
                strtoupper($transaction->status ?? 'UNKNOWN'),
                $transaction->validator ? ($transaction->validator->name ?? '-') : '-',
                $transaction->validator && $transaction->updated_at ? $transaction->updated_at->format('d/m/Y H:i') : '-'
            ];
        } catch (\Exception $e) {
            return [
                $transaction->id ?? '',
                '-',
                'Error',
                'Error',
                'Error',
                0,
                0,
                'ERROR',
                '-',
                '-'
            ];
        }
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
