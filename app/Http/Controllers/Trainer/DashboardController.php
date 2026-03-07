<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repository\MenuRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;
use App\Models\Booking;
use App\Models\ServiceTransaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $config = [
            'title' => 'Trainer Dashboard',
            'title-alias' => 'Dashboard Trainer',
            'menu' => MenuRepository::generate($request),
        ];

        $trainerId = Auth::id();
        $today = Carbon::today();

        // Stats (Ganti dengan query real)
        $stats = [
            'upcoming_classes' => Schedule::where('trainer_id', $trainerId)
                ->where('start_time', '>', now())
                ->count(),
            'upcoming_pt' => \App\Models\ServiceSession::where('trainer_id', $trainerId)
                ->where('status', 'pending')
                ->where('scheduled_date', '>', now())
                ->count(),
            'total_students' => Booking::whereHas('schedule', fn($q) => $q->where('trainer_id', $trainerId))->count()
                + ServiceTransaction::where('trainer_id', $trainerId)->distinct('user_id')->count(),
        ];

        // Upcoming Schedule (Classes + PT) starting from today
        $upcomingClasses = Schedule::with('bookings.user')
            ->where('trainer_id', $trainerId)
            ->where('start_time', '>=', $today)
            ->get()
            ->map(function ($s) {
                return [
                    'date' => $s->start_time->format('Y-m-d'),
                    'display_date' => $s->start_time->format('d M Y'),
                    'time' => $s->start_time->format('H:i'),
                    'type' => 'Class',
                    'name' => 'Latihan Bersama',
                    'member' => $s->bookings->count() . ' Members',
                    'status' => 'Scheduled'
                ];
            });

        $upcomingPT = \App\Models\ServiceSession::with(['serviceTransaction.user', 'serviceTransaction.service'])
            ->where('trainer_id', $trainerId)
            ->where('scheduled_date', '>=', $today)
            ->get()
            ->map(function ($session) {
                return [
                    'date' => $session->scheduled_date ? $session->scheduled_date->format('Y-m-d') : '-',
                    'display_date' => $session->scheduled_date ? $session->scheduled_date->format('d M Y') : '-',
                    'time' => $session->scheduled_date ? $session->scheduled_date->format('H:i') : '-',
                    'type' => 'Service Session ' . $session->session_number,
                    'name' => $session->topic ?: ($session->serviceTransaction->service->name ?? 'PT Session'),
                    'member' => $session->serviceTransaction->user->name ?? 'Unknown Member',
                    'status' => $session->status == 'pending' ? 'Scheduled' : 'Attended'
                ];
            });

        $upcomingSchedule = $upcomingClasses->concat($upcomingPT)->sortBy(function ($item) {
            return $item['date'] . $item['time'];
        });

        return view('trainer.dashboard.index', compact('config', 'stats', 'upcomingSchedule'));
    }
}
