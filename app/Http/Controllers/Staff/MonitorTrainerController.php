<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ServiceSession;
use App\Models\Schedule;
use App\Repository\MenuRepository;

class MonitorTrainerController extends Controller
{
    public function index(Request $request)
    {
        $config = [
            'title' => 'Jadwal Trainer / Progress',
            'title-alias' => ' Jadwal Trainer',
            'menu' => MenuRepository::generate($request),
        ];

        // Ambil semua trainer
        $trainers = User::role('User:Trainer')->get();

        // 1. Ambil jadwal PT (Personal Training) atau layanan yang didaftarkan secara per sesi (bersumber dari transaksi)
        $ptSessions = ServiceSession::with(['serviceTransaction.user', 'trainer', 'serviceTransaction.service'])
            ->whereNotNull('scheduled_date')
            ->whereIn('status', ['pending', 'attended']) // Hanya tampilkan yang akan datang atau baru saja dihadiri
            ->orderBy('scheduled_date', 'asc')
            ->get();

        // 2. Ambil jadwal Kelas Terbuka (schedules)
        $classSchedules = Schedule::with(['trainer', 'service', 'bookings.user'])
            ->orderBy('start_time', 'asc')
            ->get();

        // Gabungkan/Format untuk kemudahan tampilan di Blade
        // PT Sessions
        $formattedPtSessions = $ptSessions->map(function ($session) {
            return [
                'type' => 'Personal Training',
                'service_name' => $session->serviceTransaction->service->name ?? '-',
                'member_name' => $session->serviceTransaction->user->name ?? '-',
                'trainer_name' => $session->trainer->name ?? '-',
                'topic' => $session->topic ?? 'Sesi ' . $session->session_number,
                'schedule_time' => \Carbon\Carbon::parse($session->scheduled_date),
                'status' => $session->status,
                'status_badge' => self::getBadgeColor($session->status)
            ];
        });

        // Class Schedules
        $formattedClassSchedules = $classSchedules->map(function ($schedule) {
            $status = $schedule->start_time > now() ? 'pending' : 'attended';
            return [
                'type' => 'Open Class',
                'service_name' => $schedule->service->name ?? $schedule->title,
                'member_name' => $schedule->bookings->count() . ' Orang (Kapasitas: ' . $schedule->capacity . ')',
                'trainer_name' => $schedule->trainer->name ?? '-',
                'topic' => $schedule->title,
                'schedule_time' => \Carbon\Carbon::parse($schedule->start_time),
                'status' => $status,
                'status_badge' => self::getBadgeColor($status)
            ];
        });

        // Tampilkan 50 sesi mendatang paling dekat (Gabungan PT & Class)
        $upcomingSessions = $formattedPtSessions->concat($formattedClassSchedules)
            ->where('schedule_time', '>=', now()->subHours(24)) // Masukkan hari ini juga
            ->sortBy('schedule_time')
            ->take(200)
            ->values();

        return view('staff.monitor-trainer.index', compact('config', 'upcomingSessions'));
    }

    private static function getBadgeColor($status)
    {
        return match ($status) {
            'pending' => 'primary',
            'attended' => 'success',
            'missed' => 'danger',
            'cancelled' => 'secondary',
            default => 'dark'
        };
    }
}
