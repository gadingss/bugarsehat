<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Income;
use Carbon\Carbon;
use App\Repository\MenuRepository; // Make sure this exists and is correct
use App\Exports\IncomesExport;    // Import the Excel export class
use Maatwebsite\Excel\Facades\Excel; // Import the Excel facade
use Barryvdh\DomPDF\Facade\Pdf;      // Import the PDF facade

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

        // Fetch incomes within the date range
        $incomes = Income::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->get();
        $totalIncome = $incomes->sum('amount');

        // Prepare config for the view
        $config = [
            'title' => 'Income Report',
            'title-alias' => 'income',
            // Ensure MenuRepository is correctly implemented or remove if not needed
            'menu' => class_exists(MenuRepository::class) ? MenuRepository::generate($request) : [],
        ];

        return view('income_report.index', compact('incomes', 'totalIncome', 'startDate', 'endDate', 'config'));
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

        // Fetch the data again for the PDF
        $incomes = Income::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->get();
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
