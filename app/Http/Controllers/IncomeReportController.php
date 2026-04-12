<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\Transaction;
use App\Models\ServiceTransaction;
use Carbon\Carbon;
use App\Repository\MenuRepository; 
use App\Exports\IncomesExport;    
use Maatwebsite\Excel\Facades\Excel; 
use Barryvdh\DomPDF\Facade\Pdf;      

class IncomeReportController extends Controller
{
    /**
     * Display the income report page.
     */
    public function index(Request $request)
    {
        // Set default date range to the current month if not provided
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Fetch incomes from multiple sources for the selected period
        $incomes = $this->getAggregatedIncomes($startDate, $endDate);
        $totalIncome = $incomes->sum('amount');
        $totalTransactions = $incomes->count();
        $averageTransaction = $totalTransactions > 0 ? $totalIncome / $totalTransactions : 0;

        // Fetch overall stats for today and this month
        $todayStr = Carbon::today()->format('Y-m-d');
        $incomeToday = $this->getAggregatedIncomes($todayStr, $todayStr)->sum('amount');
        
        $monthStartStr = Carbon::now()->startOfMonth()->format('Y-m-d');
        $monthEndStr = Carbon::now()->endOfMonth()->format('Y-m-d');
        $incomeThisMonth = $this->getAggregatedIncomes($monthStartStr, $monthEndStr)->sum('amount');

        // Prepare config for the view
        $config = [
            'title' => 'Income Report',
            'title-alias' => 'income',
            'menu' => class_exists(MenuRepository::class) ? MenuRepository::generate($request) : [],
        ];

        return view('income_report.index', compact(
            'incomes', 'totalIncome', 'startDate', 'endDate', 'config',
            'incomeToday', 'incomeThisMonth', 'totalTransactions', 'averageTransaction'
        ));
    }

    /**
     * Aggregate incomes from multiple transaction tables.
     */
    private function getAggregatedIncomes($startDate, $endDate)
    {
        $start = $startDate . ' 00:00:00';
        $end = $endDate . ' 23:59:59';

        // 1. Transactions (Membership / Product Purchase)
        $transactions = Transaction::with(['product'])
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
                    'type' => 'produk',
                ];
            });

        // 2. Service Transactions (Trainer)
        $serviceTransactions = ServiceTransaction::with(['service'])
            ->whereBetween('transaction_date', [$start, $end])
            ->whereIn('status', ['scheduled', 'completed'])
            ->get()
            ->map(function ($item) {
                return (object) [
                    'created_at' => $item->transaction_date,
                    'description' => 'Layanan ' . ($item->service->name ?? 'Trainer'),
                    'amount' => $item->amount,
                    'type' => 'layanan',
                ];
            });

        // 3. Manual Incomes
        $manualIncomes = Income::whereBetween('created_at', [$start, $end])
            ->get()
            ->map(function ($item) {
                return (object) [
                    'created_at' => $item->created_at,
                    'description' => $item->description,
                    'amount' => $item->amount,
                    'type' => 'lainnya',
                ];
            });

        // Combine and Sort
        return $transactions->concat($serviceTransactions)
            ->concat($manualIncomes)
            ->sortByDesc('created_at');

    }

    /**
     * Handle the Excel export request.
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $fileName = 'laporan-pemasukan-' . $startDate . '-sampai-' . $endDate . '.xlsx';

        // Use the IncomesExport class to generate and download the file
        return Excel::download(new IncomesExport($startDate, $endDate), $fileName);
    }

    /**
     * Handle the PDF export request.
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Fetch aggregated data
        $incomes = $this->getAggregatedIncomes($startDate, $endDate);
        $totalIncome = $incomes->sum('amount');

        // Load the dedicated PDF view with the data
        $pdf = PDF::loadView('income_report.pdf', compact('incomes', 'totalIncome', 'startDate', 'endDate'));
        
        // Set paper size and orientation
        $pdf->setPaper('a4', 'portrait');

        $fileName = 'laporan-pemasukan-' . $startDate . '-sampai-' . $endDate . '.pdf';

        // Download the generated PDF
        return $pdf->download($fileName);
    }
}
