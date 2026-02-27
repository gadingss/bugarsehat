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
            'title' => 'Progress Latihan',
            'title-alias' => 'Riwayat Progress Member',
            'menu' => MenuRepository::generate($request),
        ];

        $trainer = Auth::user();
        $progresses = TrainingProgress::where('trainer_id', $trainer->id)
            ->with('member')
            ->orderBy('date', 'desc')
            ->get();

        return view('trainer.progress.index', compact('config', 'progresses'));
    }

    // 🔹 Form Input Progress
    public function createProgress(Request $request)
    {
        $config = [
            'title' => 'Input Progress',
            'title-alias' => 'Catat Progress Member',
            'menu' => MenuRepository::generate($request),
        ];

        $trainer = Auth::user();
        $members = $trainer->assignedMembers()->get();

        return view('trainer.progress.create', compact('config', 'members'));
    }

    // 🔹 Input Progress
    public function storeProgress(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'progress_note' => 'required',
            'special_note' => 'nullable',
            'recommendation' => 'nullable',
        ]);

        TrainingProgress::create([
            'trainer_id' => Auth::id(),
            'member_id' => $request->member_id,
            'date' => $request->date,
            'progress_note' => $request->progress_note,
            'special_note' => $request->special_note,
            'recommendation' => $request->recommendation,
        ]);

        return redirect()->route('trainer.progress.index')->with('success', 'Progress berhasil disimpan');
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
