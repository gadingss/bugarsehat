<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Product;
use App\Models\Membership;
use App\Models\ServiceTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class TransactionManagementController extends Controller
{
    public function index()
    {
        // Get transaction statistics
        $stats = [
            'pending_transactions' => Transaction::where('status', 'pending')->count(),
            'today_revenue' => Transaction::where('status', 'validated')
                ->whereDate('transaction_date', today())
                ->sum('amount'),
            'this_month_revenue' => Transaction::where('status', 'validated')
                ->whereMonth('transaction_date', now()->month)
                ->sum('amount'),
            'total_transactions' => Transaction::count(),
        ];

        // Get pending transactions for quick action
        $pendingTransactions = Transaction::with(['user', 'product'])
            ->where('status', 'pending')
            ->orderBy('transaction_date', 'desc')
            ->take(10)
            ->get();

        // Get daily revenue chart data
        $dailyRevenue = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dailyRevenue[$date->format('M d')] = Transaction::where('status', 'validated')
                ->whereDate('transaction_date', $date)
                ->sum('amount');
        }

        return view('staff.transaction-management.index', compact('stats', 'pendingTransactions', 'dailyRevenue'));
    }

    public function transactions(Request $request)
    {
        if ($request->ajax()) {
            $transactions = Transaction::with(['user', 'product'])
                ->select('transactions.*');

            // Apply filters
            if ($request->status) {
                $transactions->where('status', $request->status);
            }

            if ($request->date_from) {
                $transactions->whereDate('transaction_date', '>=', $request->date_from);
            }

            if ($request->date_to) {
                $transactions->whereDate('transaction_date', '<=', $request->date_to);
            }

            return DataTables::of($transactions)
                ->addColumn('user_name', function ($transaction) {
                    return $transaction->user ? $transaction->user->name : 'N/A';
                })
                ->addColumn('product_name', function ($transaction) {
                    if ($transaction->product) {
                        return $transaction->product->name;
                    } elseif ($transaction->membership_id) {
                        $membership = Membership::with('package')->find($transaction->membership_id);
                        return $membership ? 'Membership ' . $membership->package->name : 'Membership';
                    }
                    return $transaction->description ?? 'N/A';
                })
                ->addColumn('amount_formatted', function ($transaction) {
                    return 'Rp ' . number_format($transaction->amount, 0, ',', '.');
                })
                ->addColumn('status_badge', function ($transaction) {
                    $badges = [
                        'pending' => 'badge-warning',
                        'validated' => 'badge-success',
                        'cancelled' => 'badge-danger',
                        'refunded' => 'badge-info',
                    ];
                    
                    $badgeClass = $badges[$transaction->status] ?? 'badge-secondary';
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($transaction->status) . '</span>';
                })
                ->addColumn('date_formatted', function ($transaction) {
                    return Carbon::parse($transaction->transaction_date)->format('d M Y H:i');
                })
                ->addColumn('actions', function ($transaction) {
                    $actions = '<div class="btn-group" role="group">';
                    
                    $actions .= '<button type="button" class="btn btn-sm btn-light-primary" onclick="viewTransaction(' . $transaction->id . ')">
                        <i class="fas fa-eye"></i>
                    </button>';

                    if ($transaction->status === 'pending') {
                        $actions .= '<button type="button" class="btn btn-sm btn-light-success" onclick="validateTransaction(' . $transaction->id . ')">
                            <i class="fas fa-check"></i>
                        </button>';
                        
                        $actions .= '<button type="button" class="btn btn-sm btn-light-danger" onclick="cancelTransaction(' . $transaction->id . ')">
                            <i class="fas fa-times"></i>
                        </button>';
                    }

                    if ($transaction->status === 'validated') {
                        $actions .= '<button type="button" class="btn btn-sm btn-light-warning" onclick="refundTransaction(' . $transaction->id . ')">
                            <i class="fas fa-undo"></i>
                        </button>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        }

        return view('staff.transaction-management.transactions');
    }

    public function show($id)
    {
        $transaction = Transaction::with(['user', 'product'])->findOrFail($id);
        
        // Get related membership if exists
        $membership = null;
        if ($transaction->membership_id) {
            $membership = Membership::with('package')->find($transaction->membership_id);
        }

        return response()->json([
            'transaction' => $transaction,
            'membership' => $membership,
        ]);
    }

    public function validateTransaction(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $transaction = Transaction::findOrFail($id);

        if ($transaction->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi ini tidak dapat divalidasi'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Update transaction status
            $transaction->update([
                'status' => 'validated',
                'validated_by' => auth()->id(),
                'validated_at' => now(),
                'notes' => $request->notes,
            ]);

            // If this is a membership transaction, activate the membership
            if ($transaction->membership_id) {
                $membership = Membership::find($transaction->membership_id);
                if ($membership && $membership->status === 'inactive') {
                    // Deactivate other active memberships for this user
                    Membership::where('user_id', $membership->user_id)
                        ->where('status', 'active')
                        ->update(['status' => 'expired']);
                    
                    // Activate the new membership
                    $membership->update(['status' => 'active']);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil divalidasi'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $transaction = Transaction::findOrFail($id);

        if ($transaction->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi ini tidak dapat dibatalkan'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Update transaction status
            $transaction->update([
                'status' => 'cancelled',
                'cancelled_by' => auth()->id(),
                'cancelled_at' => now(),
                'cancellation_reason' => $request->reason,
            ]);

            // If this is a membership transaction, delete the membership
            if ($transaction->membership_id) {
                $membership = Membership::find($transaction->membership_id);
                if ($membership && $membership->status === 'inactive') {
                    $membership->delete();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibatalkan'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function refund(Request $request, $id)
    {
        $request->validate([
            'refund_amount' => 'required|numeric|min:0',
            'reason' => 'required|string|max:500',
        ]);

        $transaction = Transaction::findOrFail($id);

        if ($transaction->status !== 'validated') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya transaksi yang sudah divalidasi yang dapat di-refund'
            ], 400);
        }

        if ($request->refund_amount > $transaction->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah refund tidak boleh melebihi jumlah transaksi'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Update transaction status
            $transaction->update([
                'status' => 'refunded',
                'refunded_by' => auth()->id(),
                'refunded_at' => now(),
                'refund_amount' => $request->refund_amount,
                'refund_reason' => $request->reason,
            ]);

            // If this is a membership transaction and full refund, deactivate membership
            if ($transaction->membership_id && $request->refund_amount == $transaction->amount) {
                $membership = Membership::find($transaction->membership_id);
                if ($membership) {
                    $membership->update(['status' => 'cancelled']);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Refund berhasil diproses'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reports()
    {
        // Get comprehensive transaction reports
        $reports = [
            'daily_summary' => $this->getDailySummary(),
            'monthly_summary' => $this->getMonthlySummary(),
            'payment_methods' => $this->getPaymentMethodStats(),
            'top_products' => $this->getTopProducts(),
            'member_spending' => $this->getTopSpendingMembers(),
        ];

        return view('staff.transaction-management.reports', compact('reports'));
    }

    private function getDailySummary()
    {
        $summary = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $summary[$date->format('M d')] = [
                'revenue' => Transaction::where('status', 'validated')
                    ->whereDate('transaction_date', $date)
                    ->sum('amount'),
                'count' => Transaction::where('status', 'validated')
                    ->whereDate('transaction_date', $date)
                    ->count(),
            ];
        }
        return $summary;
    }

    private function getMonthlySummary()
    {
        $summary = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $summary[$month->format('M Y')] = [
                'revenue' => Transaction::where('status', 'validated')
                    ->whereMonth('transaction_date', $month->month)
                    ->whereYear('transaction_date', $month->year)
                    ->sum('amount'),
                'count' => Transaction::where('status', 'validated')
                    ->whereMonth('transaction_date', $month->month)
                    ->whereYear('transaction_date', $month->year)
                    ->count(),
            ];
        }
        return $summary;
    }

    private function getPaymentMethodStats()
    {
        return Transaction::where('status', 'validated')
            ->whereMonth('transaction_date', now()->month)
            ->groupBy('payment_method')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
            ->get();
    }

    private function getTopProducts()
    {
        return Transaction::join('products', 'transactions.product_id', '=', 'products.id')
            ->where('transactions.status', 'validated')
            ->whereMonth('transactions.transaction_date', now()->month)
            ->groupBy('products.id', 'products.name')
            ->selectRaw('products.name, COUNT(*) as count, SUM(transactions.amount) as total')
            ->orderBy('total', 'desc')
            ->take(10)
            ->get();
    }

    private function getTopSpendingMembers()
    {
        return Transaction::join('users', 'transactions.user_id', '=', 'users.id')
            ->where('transactions.status', 'validated')
            ->whereMonth('transactions.transaction_date', now()->month)
            ->groupBy('users.id', 'users.name')
            ->selectRaw('users.name, COUNT(*) as transaction_count, SUM(transactions.amount) as total_spent')
            ->orderBy('total_spent', 'desc')
            ->take(10)
            ->get();
    }

    public function exportReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:excel,pdf',
        ]);

        $transactions = Transaction::with(['user', 'product'])
            ->whereBetween('transaction_date', [$request->start_date, $request->end_date])
            ->where('status', 'validated')
            ->orderBy('transaction_date', 'desc')
            ->get();

        if ($request->format === 'excel') {
            return $this->exportToExcel($transactions, $request->start_date, $request->end_date);
        } else {
            return $this->exportToPdf($transactions, $request->start_date, $request->end_date);
        }
    }

    protected function exportToExcel($transactions, $startDate, $endDate)
    {
        // Implementation for Excel export
        // This would typically use Laravel Excel package
        return response()->json([
            'success' => true,
            'message' => 'Export Excel akan segera dimulai',
            'download_url' => '#' // URL to download file
        ]);
    }

    protected function exportToPdf($transactions, $startDate, $endDate)
    {
        // Implementation for PDF export
        // This would typically use DomPDF or similar package
        return response()->json([
            'success' => true,
            'message' => 'Export PDF akan segera dimulai',
            'download_url' => '#' // URL to download file
        ]);
    }
}