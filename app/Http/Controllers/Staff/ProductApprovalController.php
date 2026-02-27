<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductApprovalController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['user', 'product'])
            ->whereIn('status', ['waiting_validation', 'pending'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('staff.product-approval.index', compact('transactions'));
    }

    public function validateTransaction(Request $request, $transactionId)
    {
        $transaction = Transaction::with(['user', 'product'])->findOrFail($transactionId);

        DB::beginTransaction();
        try {
            $transaction->update([
                'status' => 'validated',
                'validated_by' => Auth::id(),
                'validated_at' => now()
            ]);

            // Reduce product stock
            $transaction->product->decrement('stock', $transaction->quantity);

            DB::commit();

            return redirect()->back()->with('success', 'Transaksi berhasil divalidasi');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal validasi transaksi: ' . $e->getMessage());
        }
    }

    public function rejectTransaction(Request $request, $transactionId)
    {
        $request->validate([
            'note' => 'required|string|max:500'
        ]);

        $transaction = Transaction::with(['user', 'product'])->findOrFail($transactionId);

        DB::beginTransaction();
        try {
            $transaction->update([
                'status' => 'rejected',
                'validated_by' => Auth::id(),
                'validated_at' => now()
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Transaksi berhasil ditolak');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menolak transaksi: ' . $e->getMessage());
        }
    }
}
