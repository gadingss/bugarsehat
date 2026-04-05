<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repository\MenuRepository;
use App\Models\Booking;
use App\Models\ServiceSession;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $config = [
            'title' => 'Jadwal Saya',
            'title-alias' => 'Jadwal Latihan & Kelas',
            'menu' => MenuRepository::generate($request),
        ];

        // 1. Ambil Booking Kelas Grup
        $groupClasses = Booking::where('user_id', $user->id)
            ->whereHas('schedule', function ($q) {
                $q->where('start_time', '>', now()->subHours(2)); // Tampilkan yang baru saja lewat atau akan datang
            })
            ->with(['schedule.trainer', 'schedule.package'])
            ->get()
            ->map(function ($booking) {
                return [
                    'type' => 'Group Class',
                    'name' => $booking->schedule->package->name ?? 'Kelas',
                    'trainer' => $booking->schedule->trainer->name ?? '-',
                    'start_time' => $booking->schedule->start_time,
                    'topic' => $booking->schedule->package->desc ?? '-',
                    'status' => 'Booked',
                    'color' => 'info'
                ];
            });

        // 2. Ambil Sesi Personal Training
        $personalSessions = ServiceSession::whereHas('serviceTransaction', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->where('scheduled_date', '>', now()->subHours(2))
            ->with(['trainer', 'serviceTransaction.service'])
            ->get()
            ->map(function ($session) {
                return [
                    'type' => 'Personal Training',
                    'name' => $session->serviceTransaction->service->name ?? 'Personal Training',
                    'trainer' => $session->trainer->name ?? '-',
                    'start_time' => $session->scheduled_date,
                    'topic' => $session->topic ?? '-',
                    'status' => $session->status,
                    'color' => 'success'
                ];
            });

        // 3. Ambil Transaksi Layanan yang Dijadwalkan (khusus yang belum punya sesi individu)
        $scheduledServices = \App\Models\ServiceTransaction::where('user_id', $user->id)
            ->where('status', 'scheduled')
            ->where('scheduled_date', '>', now()->subHours(2))
            ->whereDoesntHave('serviceSessions')
            ->with(['trainer', 'service'])
            ->get()
            ->map(function ($transaction) {
                return [
                    'type' => 'Service Booking',
                    'name' => $transaction->service->name ?? 'Layanan',
                    'trainer' => $transaction->trainer->name ?? '-',
                    'start_time' => $transaction->scheduled_date,
                    'topic' => $transaction->notes ?? 'Sesi Tunggal',
                    'status' => 'Scheduled',
                    'color' => 'primary'
                ];
            });

        // 4. Gabungkan dan Urutkan
        $schedules = $groupClasses->concat($personalSessions)->concat($scheduledServices)->sortBy('start_time');


        return view('member.schedule.index', compact('config', 'schedules'));
    }
}

