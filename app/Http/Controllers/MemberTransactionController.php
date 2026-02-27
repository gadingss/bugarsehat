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

        // Build query
        $query = Transaction::with(['product', 'validator'])
            ->where('user_id', Auth::id())
            ->orderBy('transaction_date', 'desc');

        if ($search) {
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
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

        $statistics = [
            'total_transactions' => Transaction::where('user_id', Auth::id())->count(),
            'total_amount' => Transaction::where('user_id', Auth::id())
                ->where('status', 'validated')
                ->sum('amount'),
            'pending_count' => Transaction::where('user_id', Auth::id())
                ->where('status', 'pending')
                ->count(),
            'validated_count' => Transaction::where('user_id', Auth::id())
                ->where('status', 'validated')
                ->count(),
        ];

        // 🧩 Tambahkan konfigurasi menu dan judul
        $config = [
            'title' => 'Riwayat Transaksi',
            'title-alias' => 'Membership Transaction History',
            'menu' => MenuRepository::generate($request),
        ];

        return view('member_transaction.index', compact('transactions', 'statistics', 'config'));
    }
}
