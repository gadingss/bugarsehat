<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\ServiceTransaction;
use App\Repository\MenuRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth = $request->input('month', now()->format('m'));
        $selectedYear = $request->input('year', now()->format('Y'));

        $trainer = Auth::user();

        // Get all scheduled and completed service transactions for this trainer
        $transactions = ServiceTransaction::with(['user', 'service'])
            ->whereIn('status', ['completed', 'scheduled']) 
            ->where('trainer_id', $trainer->id)
            ->whereMonth('created_at', $selectedMonth)
            ->whereYear('created_at', $selectedYear)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalSessions = $transactions->count();
        $totalSalary = $transactions->sum('amount');

        $config = [
            'title' => 'Penggajian Saya',
            'title-alias' => 'Penggajian',
            'menu' => MenuRepository::generate($request),
        ];

        return view('trainer.salary.index', compact('config', 'transactions', 'totalSessions', 'totalSalary', 'selectedMonth', 'selectedYear'));
    }
}
