<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TrainingProgress;
use App\Models\TrainerAvailability;
use Illuminate\Support\Facades\Auth;
use App\Repository\MenuRepository;

class TrainerController extends Controller
{
    // 🔹 Daftar Member
    public function members(Request $request)
    {
        $config = [
            'title' => 'Daftar Member',
            'title-alias' => 'Member Anda',
            'menu' => MenuRepository::generate($request),
        ];

        $trainer = Auth::user();
        $members = $trainer->assignedMembers()->get();

        return view('trainer.members.index', compact('config', 'members'));
    }

    // 🔹 Progress Latihan (History)
    public function progressIndex(Request $request)
    {
        $config = [
            'title' => 'Progress & Sesi Latihan',
            'title-alias' => 'Manajemen Jadwal dan Progress Member',
            'menu' => MenuRepository::generate($request),
        ];

        $trainer = Auth::user();

        $transactions = \App\Models\ServiceTransaction::where('trainer_id', $trainer->id)
            ->whereIn('status', ['scheduled', 'completed'])
            ->with([
                'user',
                'service',
                'serviceSessions' => function ($q) {
                    $q->orderBy('session_number', 'asc');
                }
            ])
            ->orderBy('scheduled_date', 'desc')
            ->get();

        return view('trainer.progress.index', compact('config', 'transactions'));
    }

    // 🔹 Update Session (Date, Topic, Status)
    public function updateSession(Request $request, \App\Models\ServiceSession $session)
    {
        $trainer = Auth::user();

        // Ensure the session belongs to a transaction assigned to this trainer
        if ($session->serviceTransaction->trainer_id !== $trainer->id) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'scheduled_date' => 'required|date',
            'topic' => 'nullable|string|max:255',
            'status' => 'required|in:pending,attended,missed,cancelled',
        ]);

        // If checking in a member for the first time via this form, we can optionally link it to a CheckinLog
        // But for simplicity, we just update the text fields
        $session->update([
            'scheduled_date' => $request->scheduled_date,
            'topic' => $request->topic,
            'status' => $request->status,
        ]);

        return redirect()->route('trainer.progress.index')->with('success', 'Sesi ke-' . $session->session_number . ' pada layanan ' . $session->serviceTransaction->service->name . ' berhasil diperbarui');
    }

    // 🔹 Ketersediaan Waktu
    public function availability(Request $request)
    {
        $config = [
            'title' => 'Ketersediaan Waktu',
            'title-alias' => 'Atur Jadwal Ketersediaan',
            'menu' => MenuRepository::generate($request),
        ];

        $trainer = Auth::user();
        $availabilities = TrainerAvailability::where('trainer_id', $trainer->id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('trainer.availability.index', compact('config', 'availabilities'));
    }

    public function storeAvailability(Request $request)
    {
        $request->validate([
            'day_of_week' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        TrainerAvailability::create([
            'trainer_id' => Auth::id(),
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_available' => true,
        ]);

        return back()->with('success', 'Ketersediaan berhasil ditambahkan');
    }

    public function destroyAvailability($id)
    {
        TrainerAvailability::where('id', $id)
            ->where('trainer_id', Auth::id())
            ->delete();

        return back()->with('success', 'Slot ketersediaan berhasil dihapus');
    }
}
