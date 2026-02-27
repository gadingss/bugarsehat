<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\TrainingProgress;
use App\Repository\MenuRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $config = [
            'title' => 'Progress Latihan',
            'menu' => MenuRepository::generate($request),
        ];

        $progressHistory = TrainingProgress::where('member_id', $user->id)
            ->with('trainer')
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('member.progress.index', compact('config', 'progressHistory'));
    }
}
