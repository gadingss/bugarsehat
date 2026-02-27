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
        // Fetch data based on the provided date range
        $incomes = Income::whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])->get();
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
