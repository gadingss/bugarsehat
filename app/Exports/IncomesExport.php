<?php

namespace App\Exports;

use App\Models\Income;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class IncomesExport implements FromView, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    // Constructor to accept the date range
    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
    * @return \Illuminate\Contracts\View\View
    */
    public function view(): View
    {
        $start = $this->startDate . ' 00:00:00';
        $end = $this->endDate . ' 23:59:59';

        // 1. Transactions (Membership / Product Purchase)
        $transactions = \App\Models\Transaction::with(['product'])
            ->whereBetween('transaction_date', [$start, $end])
            ->where('status', 'validated')
            ->get()
            ->map(function ($item) {
                $description = 'Pembelian ' . ($item->product->name ?? 'Produk');
                if ($item->invoice_id) {
                    $description .= ' (#' . $item->invoice_id . ')';
                }
                return (object) [
                    'created_at' => $item->transaction_date,
                    'description' => $description,
                    'amount' => $item->amount,
                ];
            });

        // 2. Service Transactions (Trainer)
        $serviceTransactions = \App\Models\ServiceTransaction::with(['service'])
            ->whereBetween('transaction_date', [$start, $end])
            ->whereIn('status', ['scheduled', 'completed'])
            ->get()
            ->map(function ($item) {
                return (object) [
                    'created_at' => $item->transaction_date,
                    'description' => 'Layanan ' . ($item->service->name ?? 'Trainer'),
                    'amount' => $item->amount,
                ];
            });

        // 3. Manual Incomes
        $manualIncomes = \App\Models\Income::whereBetween('created_at', [$start, $end])
            ->get()
            ->map(function ($item) {
                return (object) [
                    'created_at' => $item->created_at,
                    'description' => $item->description,
                    'amount' => $item->amount,
                ];
            });

        // Combine and Sort
        $incomes = $transactions->concat($serviceTransactions)
            ->concat($manualIncomes)
            ->sortByDesc('created_at');

        $totalIncome = $incomes->sum('amount');

        // Pass the data to a dedicated Blade view for the Excel sheet
        return view('income_report.excel', [
            'incomes' => $incomes,
            'totalIncome' => $totalIncome,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
        ]);
    }
}
