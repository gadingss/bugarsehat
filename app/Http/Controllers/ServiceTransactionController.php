<?php

namespace App\Http\Controllers;

use App\Models\ServiceTransaction;
use App\Models\Service;
use App\Models\TrainerMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Repository\MenuRepository;

class ServiceTransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Sidebar config
        $config = [
            'title' => 'Service Transactions',
            'title-alias' => 'Transaksi Layanan',
            'menu' => MenuRepository::generate($request),
        ];

        // Base query
        $query = ServiceTransaction::with(['user', 'service'])
            ->orderBy('transaction_date', 'desc');

        // Filter by user role
        if (!$user->hasRole('User:Staff') && !$user->hasRole('User:Owner') && !$user->hasRole('Super:Admin')) {
            if ($user->hasRole('User:Trainer')) {
                $query->where('trainer_id', $user->id);
            } else {
                $query->where('user_id', $user->id);
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('service', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        // Get statistics
        $statistics = $this->getStatistics($user);

        // Paginate results
        $transactions = $query->paginate(10)->withQueryString();

        return view('service_transaction.index', compact('transactions', 'statistics', 'config'));
    }

    private function getStatistics($user)
    {
        $baseQuery = ServiceTransaction::query();

        if (!$user->hasRole('User:Staff') && !$user->hasRole('User:Owner') && !$user->hasRole('Super:Admin')) {
            if ($user->hasRole('User:Trainer')) {
                $baseQuery->where('trainer_id', $user->id);
            } else {
                $baseQuery->where('user_id', $user->id);
            }
        }

        return [
            'total_transactions' => $baseQuery->count(),
            'pending_count' => (clone $baseQuery)->where('status', 'pending')->count(),
            'completed_count' => (clone $baseQuery)->where('status', 'completed')->count(),
            'cancelled_count' => (clone $baseQuery)->where('status', 'cancelled')->count(),
            'scheduled_count' => (clone $baseQuery)->where('status', 'scheduled')->count(),
            'total_amount' => (clone $baseQuery)->where('status', 'completed')->sum('amount'),
        ];
    }

    public function show(ServiceTransaction $serviceTransaction, Request $request)
    {
        $this->authorize('view', $serviceTransaction);

        // Sidebar config
        $config = [
            'title' => 'Detail Transaction',
            'title-alias' => 'Detail',
            'menu' => MenuRepository::generate($request),
        ];

        return view('service_transaction.show', compact('serviceTransaction', 'config'));
    }

    public function cancel(Request $request, ServiceTransaction $serviceTransaction)
    {
        $this->authorize('update', $serviceTransaction);

        if ($serviceTransaction->canCancel()) {
            $serviceTransaction->cancel($request->reason);
            return redirect()->back()->with('success', 'Transaksi berhasil dibatalkan');
        }

        return redirect()->back()->with('error', 'Transaksi tidak dapat dibatalkan');
    }

    public function approve(Request $request, ServiceTransaction $serviceTransaction)
    {
        // Only Staff/Owner can approve
        if (!Auth::user()->hasRole('User:Staff') && !Auth::user()->hasRole('User:Owner')) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            // Update transaction status to scheduled (since it's a booking)
            $serviceTransaction->update([
                'status' => 'scheduled',
                'validated_by' => Auth::id(),
                'validated_at' => now(),
            ]);

            // Link member to trainer if trainer_id is present
            if ($serviceTransaction->trainer_id) {
                TrainerMember::firstOrCreate([
                    'trainer_id' => $serviceTransaction->trainer_id,
                    'member_id' => $serviceTransaction->user_id,
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Transaksi berhasil disetujui dan layanan telah dijadwalkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyetujui transaksi: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, ServiceTransaction $serviceTransaction)
    {
        // Only Staff/Owner can reject
        if (!Auth::user()->hasRole('User:Staff') && !Auth::user()->hasRole('User:Owner')) {
            abort(403);
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $serviceTransaction->update([
            'status' => 'cancelled',
            'notes' => $request->reason,
            'validated_by' => Auth::id(),
            'validated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Transaksi berhasil ditolak');
    }
}
