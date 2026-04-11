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

        // Get transactions directly assigned to this trainer
        $directTransactionIds = \App\Models\ServiceTransaction::where('trainer_id', $trainer->id)
            ->whereIn('status', ['scheduled', 'completed'])
            ->pluck('id');

        // Also get transaction IDs where any of their sessions are assigned to this trainer (from quota claims)
        $sessionTransactionIds = \App\Models\ServiceSession::where('trainer_id', $trainer->id)
            ->whereNotNull('scheduled_date')
            ->pluck('service_transaction_id');

        $allTransactionIds = $directTransactionIds->merge($sessionTransactionIds)->unique();

        $filters = [
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'search' => $request->get('search')
        ];

        $query = \App\Models\ServiceTransaction::whereIn('id', $allTransactionIds)
            ->with([
                'user',
                'service',
                'serviceSessions' => function ($q) {
                    $q->orderBy('session_number', 'asc');
                }
            ]);

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $query->whereHas('user', function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%');
            });
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

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

    // 🔹 Bulk Action untuk Seses (Hadir, Missed, Hapus)
    public function bulkSessionAction(Request $request)
    {
        $request->validate([
            'session_ids' => 'required|array',
            'session_ids.*' => 'exists:service_sessions,id',
            'action' => 'required|in:attended,missed,delete'
        ]);

        $trainerId = Auth::id();

        // Verifikasi kepemilikan sesi
        $sessions = \App\Models\ServiceSession::whereIn('id', $request->session_ids)->get();
        foreach ($sessions as $session) {
            $transaksiAuthTrainer = $session->serviceTransaction->trainer_id === $trainerId;
            $sesiAuthTrainer = $session->trainer_id === $trainerId;
            if (!$transaksiAuthTrainer && !$sesiAuthTrainer) {
                // If the trainer does not own the parent transaction, nor the session itself
                abort(403, 'Akses ditolak.');
            }
        }

        if ($request->action === 'delete') {
            \App\Models\ServiceSession::whereIn('id', $request->session_ids)->delete();
            $msg = count($sessions) . ' Sesi berhasil dihapus secara massal.';
        } else {
            \App\Models\ServiceSession::whereIn('id', $request->session_ids)->update(['status' => $request->action]);
            $msg = count($sessions) . ' Sesi berhasil ditandai sebagai ' . ($request->action == 'attended' ? 'Hadir/Selesai' : 'Missed/Kelewat') . '.';
        }

        return redirect()->route('trainer.progress.index')->with('success', $msg);
    }

    // 🔹 Hapus Transaksi Kosong / Penuh
    public function destroyTransaction($id)
    {
        $trainer = Auth::user();
        $transaction = \App\Models\ServiceTransaction::findOrFail($id);

        if ($transaction->trainer_id !== $trainer->id) {
            abort(403, 'Akses ditolak.');
        }

        $transaction->serviceSessions()->delete();
        $transaction->delete();

        return back()->with('success', 'Riwayat booking/layanan terpilih berhasil dihapus.');
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
