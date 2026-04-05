<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ServiceTransaction;
use App\Repository\MenuRepository;
use Illuminate\Http\Request;

class TrainerSalaryController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth = $request->input('month', now()->format('m'));
        $selectedYear = $request->input('year', now()->format('Y'));

        // Get all trainers in the system
        $trainers = \App\Models\User::role('User:Trainer')->get();

        // Get all scheduled and completed service transactions including related trainer, member, service
        $transactions = ServiceTransaction::with(['trainer', 'user', 'service'])
            ->whereIn('status', ['completed', 'scheduled']) 
            ->whereNotNull('trainer_id')
            ->whereMonth('created_at', $selectedMonth)
            ->whereYear('created_at', $selectedYear)
            ->orderBy('created_at', 'desc')
            ->get();

        $groupedTransactions = $transactions->groupBy('trainer_id');

        // Construct the array for view ensuring every trainer is listed
        $salaries = collect();
        foreach ($trainers as $trainer) {
            $group = $groupedTransactions->get($trainer->id, collect());
            
            $salaries->put($trainer->id, [
                'trainer' => $trainer,
                'total_sessions' => $group->count(),
                'total_salary' => $group->sum('amount'),
                'details' => $group
            ]);
        }

        $config = [
            'title' => 'Penggajian Trainer',
            'title-alias' => ' Penggajian',
            'menu' => MenuRepository::generate($request),
        ];

        return view('owner.trainer-salary.index', compact('config', 'salaries', 'selectedMonth', 'selectedYear'));
    }
}
