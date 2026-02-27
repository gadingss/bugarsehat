<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repository\MenuRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $config = [
            'title' => 'Jadwal Saya',
            'title-alias' => 'Kelola Jadwal & Booking PT',
            'menu' => MenuRepository::generate($request),
        ];

        $trainerId = Auth::id();

        // Fetch Classes (Latihan Bersama)
        $classes = Schedule::with('bookings.user')
            ->where('trainer_id', $trainerId)
            ->where('start_time', '>=', now()->subDays(30)) // Show last 30 days and future
            ->get()
            ->map(function ($s) {
                return (object) [
                    'id' => $s->id,
                    'type' => 'Class',
                    'title' => $s->title,
                    'description' => $s->description,
                    'start_time' => $s->start_time,
                    'end_time' => $s->end_time,
                    'member_info' => $s->bookings->count() . ' Peserta',
                    'status' => 'Scheduled'
                ];
            });

        // Fetch Personal Training Sessions
        $ptSessions = \App\Models\ServiceTransaction::with('user', 'service')
            ->where('trainer_id', $trainerId)
            ->whereIn('status', ['scheduled', 'completed'])
            ->where('scheduled_date', '>=', now()->subDays(30))
            ->get()
            ->map(function ($st) {
                return (object) [
                    'id' => $st->id,
                    'type' => 'PT',
                    'title' => $st->service->name ?? 'Personal Training',
                    'description' => $st->notes ?? 'Sesi latihan privat',
                    'start_time' => $st->scheduled_date,
                    'end_time' => $st->scheduled_date->addHour(), // Assuming 1 hour
                    'member_info' => $st->user->name ?? 'Member',
                    'status' => ucfirst($st->status)
                ];
            });

        // Combine and Sort
        $allSchedules = $classes->concat($ptSessions)->sortBy('start_time');

        return view('trainer.schedule.index', compact('config', 'allSchedules'));
    }

    public function create(Request $request)
    {
        $config = [
            'title' => 'Buat Jadwal',
            'title-alias' => 'Tambah Jadwal Baru',
            'menu' => MenuRepository::generate($request),
        ];

        return view('trainer.schedule.create', compact('config'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'capacity' => 'required|integer|min:1',
        ]);

        $data['trainer_id'] = Auth::id();

        Schedule::create($data);

        return redirect()->route('trainer.schedule.index')->with('success', 'Jadwal berhasil dibuat.');
    }

    public function edit(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        if ($schedule->trainer_id !== Auth::id()) {
            abort(403);
        }

        $config = [
            'title' => 'Edit Jadwal',
            'title-alias' => 'Ubah Jadwal',
            'menu' => MenuRepository::generate($request),
        ];

        return view('trainer.schedule.edit', compact('config', 'schedule'));
    }

    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        if ($schedule->trainer_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'capacity' => 'required|integer|min:1',
        ]);

        $schedule->update($data);

        return redirect()->route('trainer.schedule.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        if ($schedule->trainer_id !== Auth::id()) {
            abort(403);
        }

        $schedule->delete();

        return redirect()->route('trainer.schedule.index')->with('success', 'Jadwal berhasil dihapus.');
    }
}
