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
            'title' => 'Product Transactions',
            'title-alias' => 'Product',
            'menu' => MenuRepository::generate($request),
        ];

        $transactions = Transaction::with(['user', 'product'])
            ->where('type', 'product')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('product_transaction.index', compact('transactions', 'config'));
    }

    // Menampilkan detail transaksi tertentu
    public function show($id)
    {
        $transaction = Transaction::with(['user', 'product'])->findOrFail($id);

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
}
