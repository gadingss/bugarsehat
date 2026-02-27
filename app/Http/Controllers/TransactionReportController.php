<?php

namespace App\Http\Controllers;

use App\Exports\TransactionReportExport;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Repository\MenuRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class TransactionReportController extends Controller
{
    /**
     * Menampilkan daftar laporan transaksi dengan filter dan paginasi.
     */
    public function index(Request $request)
    {
        $config = [
            'title' => 'Laporan Transaksi',
            'title-alias' => 'transaksi',
            'menu' => MenuRepository::generate($request),
        ];

        // Query dasar dengan relasi untuk performa
        $query = Transaction::with(['user', 'product', 'validator'])
            ->orderBy('transaction_date', 'desc');

        // Terapkan semua filter dari request
        $this->applyFilters($query, $request);

        // Kloning query untuk data ringkasan sebelum paginasi
        $summaryQuery = clone $query;

        // Paginasi hasil
        $transactions = $query->paginate(20)->withQueryString();

        // Data ringkasan (summary)
        $summary = [
            'total_transactions' => $summaryQuery->count(),
            'total_amount' => $summaryQuery->sum('amount'),
            'pending_count' => (clone $summaryQuery)->where('status', 'pending')->count(),
            'validated_count' => (clone $summaryQuery)->where('status', 'validated')->count(),
            'cancelled_count' => (clone $summaryQuery)->where('status', 'cancelled')->count(),
        ];

        $products = Product::orderBy('name')->get();

        return view('transaction_report.index', compact('config', 'transactions', 'summary', 'products'));
    }

    /**
     * Menangani ekspor data ke Excel atau PDF.
     */
    public function export(Request $request)
    {
        try {
            $query = Transaction::with(['user', 'product', 'validator'])->orderBy('transaction_date', 'desc');

            // Terapkan filter yang sama dengan halaman index
            $this->applyFilters($query, $request);

            $transactions = $query->get();

            if ($transactions->isEmpty()) {
                return redirect()->back()->with('warning', 'Tidak ada data yang cocok dengan filter untuk diekspor.');
            }

            $exportType = $request->input('export_type', 'excel'); // Default ke Excel
            $filename = 'laporan-transaksi-' . now()->format('Y-m-d');

            if ($exportType === 'pdf') {
                // Logika Ekspor PDF
                $pdf = PDF::loadView('transaction_report.export_pdf', compact('transactions'));
                $pdf->setPaper('a4', 'landscape'); // Atur ke landscape untuk tabel lebar

                return $pdf->download($filename . '.pdf');
            } else {
                // Logika Ekspor Excel (default)
                return Excel::download(new TransactionReportExport($transactions), $filename . '.xlsx');
            }
        } catch (\Exception $e) {
            Log::error('Gagal mengekspor laporan transaksi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mencoba mengekspor data.');
        }
    }

    /**
     * Helper untuk menerapkan filter pada query.
     */
    private function applyFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('product', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
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
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter berbasis role bisa ditambahkan di sini jika perlu
        // if (auth()->user()->hasRole('...')) { ... }
    }

    // --- FUNGSI CRUD DAN LAINNYA (TIDAK ADA PERUBAHAN) ---

    public function show(Transaction $transaction)
    {
        $config = [
            'title' => 'Detail Transaksi',
            'title-alias' => 'transaksi',
            'menu' => MenuRepository::generate(request()),
        ];
        $transaction->load(['user', 'product', 'validator']);
        return view('transaction_report.show', compact('config', 'transaction'));
    }

    public function create()
    {
        $config = [
            'title' => 'Tambah Transaksi',
            'title-alias' => 'transaksi',
            'menu' => MenuRepository::generate(request()),
        ];
        $products = Product::where('status', 'active')->orderBy('name')->get();
        $users = User::role('User:Member')->orderBy('name')->get();
        return view('transaction_report.create', compact('config', 'products', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'status' => 'required|in:pending,validated,cancelled',
        ]);

        $data = $request->all();
        if ($request->status === 'validated') {
            $data['validated_by'] = auth()->id();
        }

        $transaction = Transaction::create($data);
        return redirect()->route('transaction_report.show', $transaction)->with('success', 'Transaksi berhasil ditambahkan');
    }

    public function edit(Transaction $transaction)
    {
        $config = [
            'title' => 'Edit Transaksi',
            'title-alias' => 'transaksi',
            'menu' => MenuRepository::generate(request()),
        ];
        $products = Product::where('status', 'active')->orderBy('name')->get();
        $users = User::role('User:Member')->orderBy('name')->get();
        return view('transaction_report.edit', compact('config', 'transaction', 'products', 'users'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'status' => 'required|in:pending,validated,cancelled',
        ]);

        $data = $request->all();
        $data['validated_by'] = $request->status === 'validated' ? ($transaction->validated_by ?? auth()->id()) : null;

        $transaction->update($data);
        return redirect()->route('transaction_report.show', $transaction)->with('success', 'Transaksi berhasil diperbarui');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->route('transaction_report.index')->with('success', 'Transaksi berhasil dihapus');
    }

    public function validateTransaction(Transaction $transaction)
    {
        $transaction->update([
            'status' => 'validated',
            'validated_by' => auth()->id(),
        ]);
        return redirect()->back()->with('success', 'Transaksi berhasil divalidasi');
    }

    public function cancelTransaction(Transaction $transaction)
    {
        $transaction->update(['status' => 'cancelled']);
        return redirect()->back()->with('success', 'Transaksi berhasil dibatalkan');
    }

    public function dashboard()
    {
        $config = [
            'title' => 'Dashboard Transaksi',
            'title-alias' => 'transaksi',
            'menu' => MenuRepository::generate(request()),
        ];

        $summary = [
            'today_transactions' => Transaction::whereDate('transaction_date', today())->count(),
            'today_amount' => Transaction::whereDate('transaction_date', today())->sum('amount'),
            'month_transactions' => Transaction::whereYear('transaction_date', now()->year)->whereMonth('transaction_date', now()->month)->count(),
            'month_amount' => Transaction::whereYear('transaction_date', now()->year)->whereMonth('transaction_date', now()->month)->sum('amount'),
            'total_transactions' => Transaction::count(),
            'total_amount' => Transaction::sum('amount'),
        ];

        $recent_transactions = Transaction::with(['user', 'product'])->latest('transaction_date')->limit(10)->get();

        $monthly_data = Transaction::select(
            DB::raw('MONTH(transaction_date) as month'),
            DB::raw('SUM(amount) as total_amount')
        )
        ->whereYear('transaction_date', now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('total_amount', 'month')->all();

        // Format data untuk chart
        $labels = [];
        $values = [];
        for ($m = 1; $m <= 12; $m++) {
            $labels[] = date('F', mktime(0, 0, 0, $m, 1));
            $values[] = $monthly_data[$m] ?? 0;
        }

        $chartData = [
            'labels' => $labels,
            'values' => $values,
        ];

        return view('transaction_report.dashboard', compact('config', 'summary', 'recent_transactions', 'chartData'));
    }
}