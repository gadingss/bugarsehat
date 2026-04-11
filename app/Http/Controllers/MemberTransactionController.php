<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Repository\MenuRepository;

class MemberTransactionController extends Controller
{
    public function index(Request $request)
    {
        // Get search query
        $search = $request->get('search');
        $status = $request->get('status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $user = Auth::user();
        
        // Build query
        $query = Transaction::with(['product', 'validator', 'user'])
            ->where('product_type', \App\Models\MembershipPacket::class)
            ->orderBy('transaction_date', 'desc');

        $isMember = $user->hasRole('User:Member') || $user->role === 'member';

        if ($isMember) {
            $query->where('user_id', $user->id);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('product', function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        if ($status && in_array($status, ['pending', 'validated', 'cancelled'])) {
            $query->where('status', $status);
        }

        if ($dateFrom) {
            $query->whereDate('transaction_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('transaction_date', '<=', $dateTo);
        }

        $transactions = $query->paginate(10);

        $statisticsQuery = Transaction::query()->where('product_type', \App\Models\MembershipPacket::class);
        if ($isMember) {
            $statisticsQuery->where('user_id', $user->id);
        }

        $statistics = [
            'total_transactions' => (clone $statisticsQuery)->count(),
            'total_amount' => (clone $statisticsQuery)->where('status', 'validated')->sum('amount'),
            'pending_count' => (clone $statisticsQuery)->where('status', 'pending')->count(),
            'validated_count' => (clone $statisticsQuery)->where('status', 'validated')->count(),
        ];

        // 🧩 Tambahkan konfigurasi menu dan judul
        $config = [
            'title' => 'Riwayat Transaksi',
            'title-alias' => 'Membership Transaction History',
            'menu' => MenuRepository::generate($request),
        ];

        return view('member_transaction.index', compact('transactions', 'statistics', 'config'));
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:transactions,id',
        ]);

        $user = Auth::user();
        if (!$user->hasRole('User:Owner') && !$user->hasRole('owner') && !$user->hasRole('User:Staff') && !$user->hasRole('staff') && !$user->hasRole('Super:Admin')) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses.'], 403);
        }

        Transaction::whereIn('id', $request->ids)->delete();

        // flash session data to be captured on reload if needed or just return json
        return response()->json(['success' => true, 'message' => count($request->ids) . ' transaksi berhasil dihapus.']);
    }
}
