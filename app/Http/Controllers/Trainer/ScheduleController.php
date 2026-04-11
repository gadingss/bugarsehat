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

        $filters = [
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'type' => $request->get('type', 'all'),
            'search' => $request->get('search')
        ];

        // Fetch Classes (Latihan Bersama)
        $classes = collect();
        if (in_array($filters['type'], ['all', 'class'])) {
            $queryClasses = Schedule::with('bookings.user', 'service')
                ->where('trainer_id', $trainerId);

            if (!empty($filters['date_from'])) {
                $queryClasses->where('start_time', '>=', $filters['date_from'] . ' 00:00:00');
            } else {
                $queryClasses->where('start_time', '>=', now()->subDays(30)); 
            }

            if (!empty($filters['date_to'])) {
                $queryClasses->where('start_time', '<=', $filters['date_to'] . ' 23:59:59');
            }

            if (!empty($filters['search'])) {
                $queryClasses->where(function($q) use ($filters) {
                    $q->where('title', 'like', '%' . $filters['search'] . '%')
                      ->orWhereHas('service', function($sq) use ($filters) {
                          $sq->where('name', 'like', '%' . $filters['search'] . '%');
                      });
                });
            }

            $classes = $queryClasses->get()
                ->groupBy(function($item) {
                    return $item->service_id . '_' . $item->created_at->format('Y-m-d_H:i');
                })
                ->map(function ($group) {
                    $first = $group->sortBy('start_time')->first();
                    $last = $group->sortBy('start_time')->last();
                    
                    return (object) [
                        'id' => $first->id,
                        'type' => 'Class',
                        'title' => $first->service->name ?? preg_replace('/ Sesi \d+$/', '', $first->title),
                        'description' => $group->count() > 1 
                            ? $group->count() . ' Pertemuan Rutin (' . $first->start_time->format('d M') . ' - ' . $last->start_time->format('d M Y') . ')'
                            : ($first->description ?? '1 Pertemuan Tunggal'),
                        'start_time' => $first->start_time,
                        'end_time' => $first->end_time,
                        'member_info' => $group->sum(function($s) { return $s->bookings->count(); }) . ' Total Booking',
                        'status' => 'Scheduled Batch',
                        'item_count' => $group->count()
                    ];
                })
                ->values();
        }

        // Fetch Personal Training Sessions
        $ptSessions = collect();
        if (in_array($filters['type'], ['all', 'pt'])) {
            $queryPt = \App\Models\ServiceTransaction::with('user', 'service')
                ->where('trainer_id', $trainerId)
                ->whereIn('status', ['scheduled', 'completed']);

            if (!empty($filters['date_from'])) {
                $queryPt->where('scheduled_date', '>=', $filters['date_from'] . ' 00:00:00');
            } else {
                $queryPt->where('scheduled_date', '>=', now()->subDays(30));
            }

            if (!empty($filters['date_to'])) {
                $queryPt->where('scheduled_date', '<=', $filters['date_to'] . ' 23:59:59');
            }

            if (!empty($filters['search'])) {
                $queryPt->whereHas('user', function($q) use ($filters) {
                    $q->where('name', 'like', '%' . $filters['search'] . '%');
                });
            }

            $ptSessions = $queryPt->get()
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
        }

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

        $services = \App\Models\Service::active()->where('requires_booking', true)->with('sessionTemplates')->get();

        return view('trainer.schedule.create', compact('config', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id'  => 'required|exists:additional_services,id',
            'capacity'    => 'required|integer|min:1',
            'sessions'    => 'required|array|min:1',
            'sessions.*.start_time' => 'required|date',
            'sessions.*.end_time'   => 'required|date|after:sessions.*.start_time',
        ]);

        $trainerId = Auth::id();
        $serviceId = $request->service_id;
        $capacity  = $request->capacity;
        $sessions  = $request->sessions;

        foreach ($sessions as $num => $session) {
            Schedule::create([
                'trainer_id'     => $trainerId,
                'service_id'     => $serviceId,
                'title'          => $session['title'] ?? "Sesi {$num}",
                'description'    => $session['description'] ?? null,
                'start_time'     => $session['start_time'],
                'end_time'       => $session['end_time'],
                'capacity'       => $capacity,
                'session_number' => $num,
            ]);
        }

        $count = count($sessions);
        return redirect()->route('trainer.schedule.index')
            ->with('success', "{$count} jadwal kelas berhasil dibuat.");
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

        $services = \App\Models\Service::active()->where('requires_booking', true)->with('sessionTemplates')->get();

        return view('trainer.schedule.edit', compact('config', 'schedule', 'services'));
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
            'service_id' => 'required|exists:additional_services,id',
            'session_number' => 'nullable|integer',
        ]);

        $schedule->update($data);

        return redirect()->route('trainer.schedule.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function show(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        if ($schedule->trainer_id !== Auth::id()) {
            abort(403);
        }

        $createdAtStart = $schedule->created_at->copy()->startOfMinute();
        $createdAtEnd = $schedule->created_at->copy()->endOfMinute();

        $batchSchedules = Schedule::where('trainer_id', Auth::id())
            ->where('service_id', $schedule->service_id)
            ->whereBetween('created_at', [$createdAtStart, $createdAtEnd])
            ->orderBy('start_time')
            ->get();

        $config = [
            'title' => 'Detail Rangkaian Sesi',
            'title-alias' => 'Kelola Sesi ' . ($schedule->service->name ?? 'Kelas'),
            'menu' => MenuRepository::generate($request),
        ];

        return view('trainer.schedule.show', compact('config', 'schedule', 'batchSchedules'));
    }

    public function destroy(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        if ($schedule->trainer_id !== Auth::id()) {
            abort(403);
        }

        if ($request->has('single')) {
            $schedule->delete();
            return back()->with('success', '1 Sesi berhasil dihapus dari rangkaian kelas.');
        }

        // Ambil waktu pembuatan hingga ke menit
        $createdAtStart = $schedule->created_at->copy()->startOfMinute();
        $createdAtEnd = $schedule->created_at->copy()->endOfMinute();

        // Hapus massal berdasarkan service, trainer, dan waktu pembuatan yang identik
        $deletedCount = Schedule::where('trainer_id', Auth::id())
            ->where('service_id', $schedule->service_id)
            ->whereBetween('created_at', [$createdAtStart, $createdAtEnd])
            ->delete();

        return redirect()->route('trainer.schedule.index')->with('success', "{$deletedCount} sesi pertemuan kelas berhasil dihapus.");
    }
}
