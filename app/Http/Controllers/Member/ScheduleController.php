<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repository\MenuRepository;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $config = [
            'title' => 'Jadwal Kelas',
            'title-alias' => 'Cari Kelas',
            'menu' => MenuRepository::generate($request),
        ];

        $schedules = Schedule::with('trainer')
            ->where('start_time', '>', now())
            ->orderBy('start_time', 'asc')
            ->paginate(12);

        return view('member.schedule.index', compact('config', 'schedules'));
    }
}
