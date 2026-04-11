<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Repository\MenuRepository;

class ProductTransactionController extends Controller
{
    // Menampilkan daftar semua transaksi produk
    public function index(Request $request)
    {
        $config = [
            'title' => 'Riwayat Produk',
            'title-alias' => 'Transaksi Produk',
            'menu' => MenuRepository::generate($request),
        ];

        $query = Transaction::with(['user', 'product'])
            ->where('type', 'product')
            ->orderBy('id', 'desc');

        // Filter for Member role
        if (!\Illuminate\Support\Facades\Auth::user()->hasRole('User:Staff') && !\Illuminate\Support\Facades\Auth::user()->hasRole('User:Owner') && !\Illuminate\Support\Facades\Auth::user()->hasRole('Super:Admin')) {
            $query->where('user_id', \Illuminate\Support\Facades\Auth::id());
        }

        $transactions = $query->paginate(20);

        return view('product_transaction.index', compact('transactions', 'config'));
    }

    // Menampilkan detail transaksi tertentu
    public function show($id)
    {
        $transaction = Transaction::with(['user', 'product'])->findOrFail($id);

        if (!\Illuminate\Support\Facades\Auth::user()->hasRole('User:Staff') && !\Illuminate\Support\Facades\Auth::user()->hasRole('User:Owner') && !\Illuminate\Support\Facades\Auth::user()->hasRole('Super:Admin')) {
            if ($transaction->user_id !== \Illuminate\Support\Facades\Auth::id()) {
                abort(403);
            }
        }

        return view('product_transaction.show', compact('transaction'));
    }

    // Mengubah status transaksi
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,validated,rejected',
        ]);

        $transaction = Transaction::findOrFail($id);
        $transaction->status = $request->status;
        $transaction->save();

        return redirect()->route('product-transactions.index')->with('success', 'Status transaksi berhasil diperbarui.');
    }

    // Menghapus transaksi
    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return redirect()->route('product-transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:transactions,id',
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user->hasRole('User:Owner') && !$user->hasRole('owner') && !$user->hasRole('User:Staff') && !$user->hasRole('staff') && !$user->hasRole('Super:Admin')) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses.'], 403);
        }

        Transaction::whereIn('id', $request->ids)->delete();

        return response()->json(['success' => true, 'message' => count($request->ids) . ' transaksi berhasil dihapus.']);
    }
}
