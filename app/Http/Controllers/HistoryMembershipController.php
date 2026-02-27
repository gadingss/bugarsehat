<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\MenuRepository;
use App\Models\CheckinLog;
use App\Models\Transaction;
use App\Models\ServiceTransaction;
use App\Models\User; // <-- TAMBAHKAN MODEL USER
use Carbon\Carbon;
use Illuminate\Support\Collection;

class HistoryMembershipController extends Controller
{
    /**
     * Menampilkan riwayat aktivitas pengguna.
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        $targetUser = null;
        $members = collect([]);

        $validated = $this->validateFilters($request);
        $filters = $this->buildFilters($validated);
        $selectedUserId = $validated['user_id'] ?? null;

        // Tentukan pengguna target berdasarkan peran
        if ($authUser->hasRole(['User:Owner', 'User:Staff'])) {
            // Owner dan Staff bisa melihat riwayat member lain
            $members = User::whereHas('roles', fn($q) => $q->where('name', 'User:Member'))->orderBy('name')->get();
            if ($selectedUserId) {
                $targetUser = User::find($selectedUserId);
            }
        } else {
            // Member hanya bisa melihat riwayatnya sendiri
            $targetUser = $authUser;
        }

        // Jika ada target user, ambil datanya. Jika tidak (misal: admin belum memilih), tampilkan data kosong.
        if ($targetUser) {
            $type = $filters['type'];
            $data = $this->getFilteredData($targetUser, $type, $filters);

            if ($type === 'all') {
                $timeline = $this->buildTimeline($data);
                $viewData = [
                    'timeline' => $timeline,
                    'memberships' => collect([]),
                    'checkinLogs' => collect([]),
                    'transactions' => collect([]),
                    'serviceTransactions' => collect([]),
                ];
            } else {
                if (is_null($data)) {
                    return back()->withErrors(['error' => 'Tipe riwayat tidak valid atau tidak ditemukan.']);
                }
                $viewData = $this->getPaginatedData($data, $type);
            }
        } else {
            // Data default kosong jika tidak ada member yang dipilih oleh admin
            $type = $filters['type'];
            $viewData = [
                'timeline' => collect([]),
                'memberships' => collect([]),
                'checkinLogs' => collect([]),
                'transactions' => collect([]),
                'serviceTransactions' => collect([]),
            ];
        }

        $config = [
            'title' => 'Riwayat Aktivitas',
            'title-alias' => 'history',
            'menu' => MenuRepository::generate($request),
        ];

        return view('membership.history', array_merge($viewData, [
            'config' => $config,
            'members' => $members, // Kirim daftar member ke view
            'selectedUserId' => $selectedUserId, // Kirim user_id yang dipilih
            'type' => $type,
            'dateFrom' => $filters['date_from'],
            'dateTo' => $filters['date_to'],
            'search' => $filters['search'],
        ]));
    }

    /**
     * Mengekspor data riwayat.
     */
    public function export(Request $request)
    {
        $validated = $this->validateExportFilters($request);
        $authUser = auth()->user();
        $targetUser = null;
        $userIdToExport = $validated['user_id'] ?? null;

        if ($authUser->hasRole(['User:Owner', 'User:Staff'])) {
            if (!$userIdToExport) {
                return response()->json(['success' => false, 'message' => 'Silakan pilih member untuk diekspor.'], 422);
            }
            $targetUser = User::find($userIdToExport);
        } else {
            $targetUser = $authUser;
        }

        if (!$targetUser) {
            return response()->json(['success' => false, 'message' => 'Member tidak ditemukan.'], 404);
        }

        $filters = [
            'date_from' => $validated['date_from'] ?? null,
            'date_to' => $validated['date_to'] ?? null,
            'search' => $request->get('search'),
        ];

        $type = $validated['log_type'];
        $data = $this->getFilteredData($targetUser, $type, $filters, false);

        if ($type === 'all') {
            $data = $this->mergeAndSortExportData($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Export data retrieved successfully',
            'data' => $data,
            'type' => $type,
            'export_type' => $validated['export_type'],
            'count' => $data instanceof Collection ? $data->count() : count($data),
        ]);
    }

    private function validateFilters(Request $request): array
    {
        return $request->validate([
            'user_id' => 'nullable|integer|exists:users,id', // <-- TAMBAHKAN VALIDASI
            'type' => 'sometimes|in:all,memberships,checkins,transactions,services',
            'date_from' => 'nullable|date|before_or_equal:date_to',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'search' => 'nullable|string|max:255',
        ]);
    }

    private function validateExportFilters(Request $request): array
    {
        return $request->validate([
            'user_id' => 'nullable|integer|exists:users,id', // <-- TAMBAHKAN VALIDASI
            'export_type' => 'required|in:pdf,excel',
            'log_type' => 'required|in:all,memberships,checkins,transactions,services',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);
    }

    private function buildFilters(array $validated): array
    {
        return [
            'user_id' => $validated['user_id'] ?? null, // <-- TAMBAHKAN
            'type' => $validated['type'] ?? 'all',
            'date_from' => isset($validated['date_from']) ? Carbon::parse($validated['date_from']) : null,
            'date_to' => isset($validated['date_to']) ? Carbon::parse($validated['date_to'])->endOfDay() : null,
            'search' => $validated['search'] ?? null,
        ];
    }

    private function getFilteredData($targetUser, string $type, array $filters, bool $forIndex = true)
    {
        if (!$targetUser) {
            return $type === 'all' ? [] : null;
        }

        $queries = [];

        if ($type === 'all' || $type === 'memberships') {
            $queries['memberships'] = $this->buildMembershipQuery($targetUser, $filters, $forIndex);
        }
        if ($type === 'all' || $type === 'checkins') {
            $queries['checkinLogs'] = $this->buildCheckinQuery($targetUser, $filters, $forIndex);
        }
        if ($type === 'all' || $type === 'transactions') {
            $queries['transactions'] = $this->buildTransactionQuery($targetUser, $filters, $forIndex);
        }
        if ($type === 'all' || $type === 'services') {
            $queries['serviceTransactions'] = $this->buildServiceQuery($targetUser, $filters, $forIndex);
        }

        if ($type === 'all')
            return $queries;

        $keyMap = [
            'memberships' => 'memberships',
            'checkins' => 'checkinLogs',
            'transactions' => 'transactions',
            'services' => 'serviceTransactions',
        ];
        $correctKey = $keyMap[$type] ?? null;

        return $correctKey ? ($queries[$correctKey] ?? null) : null;
    }

    private function buildMembershipQuery($targetUser, array $filters, bool $forIndex = true)
    {
        $query = $targetUser->memberships()->with('package')->orderBy('created_at', 'desc');
        $this->applyFilters($query, 'created_at', $filters);
        return $forIndex ? $query : $query->get();
    }

    private function buildCheckinQuery($targetUser, array $filters, bool $forIndex = true)
    {
        $query = CheckinLog::with(['membership.package'])
            ->where('user_id', $targetUser->id)
            ->orderBy('checkin_time', 'desc');
        // ... sisa kode tidak berubah ...
        $this->applyFilters($query, 'checkin_time', $filters);
        if ($filters['search'] ?? null) {
            $query->where(function ($q) use ($filters) {
                $q->where('notes', 'like', "%{$filters['search']}%")
                    ->orWhereHas('membership.package', fn($q) => $q->where('name', 'like', "%{$filters['search']}%"));
            });
        }
        return $forIndex ? $query : $query->get();
    }

    private function buildTransactionQuery($targetUser, array $filters, bool $forIndex = true)
    {
        $query = Transaction::with(['product'])
            ->where('user_id', $targetUser->id)
            ->orderBy('transaction_date', 'desc');
        // ... sisa kode tidak berubah ...
        $this->applyFilters($query, 'transaction_date', $filters);
        if ($filters['search'] ?? null) {
            $query->whereHas('product', function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }
        return $forIndex ? $query : $query->get();
    }

    private function buildServiceQuery($targetUser, array $filters, bool $forIndex = true)
    {
        $query = ServiceTransaction::with(['service'])
            ->where('user_id', $targetUser->id)
            ->orderBy('transaction_time', 'desc');
        // ... sisa kode tidak berubah ...
        $this->applyFilters($query, 'transaction_time', $filters);
        if ($filters['search'] ?? null) {
            $query->where(function ($q) use ($filters) {
                $q->whereHas('service', function ($q) use ($filters) {
                    $q->where('name', 'like', "%{$filters['search']}%")
                        ->orWhere('description', 'like', "%{$filters['search']}%");
                })->orWhere('notes', 'like', "%{$filters['search']}%");
            });
        }
        return $forIndex ? $query : $query->get();
    }

    // ... Sisa metode lainnya (applyFilters, getPaginatedData, buildTimeline, mergeAndSortExportData) tidak perlu diubah ...
    private function applyFilters($query, string $dateColumn, array $filters): void
    {
        if ($filters['date_from'] ?? null) {
            $query->whereDate($dateColumn, '>=', $filters['date_from']);
        }
        if ($filters['date_to'] ?? null) {
            $query->whereDate($dateColumn, '<=', $filters['date_to']);
        }
        if (($filters['search'] ?? null) && $dateColumn === 'created_at') {
            $query->whereHas('package', function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }
    }

    private function getPaginatedData($query, string $type): array
    {
        $perPage = 10;
        $paginated = $query->paginate($perPage);

        return [
            'memberships' => $type === 'memberships' ? $paginated : collect([]),
            'checkinLogs' => $type === 'checkins' ? $paginated : collect([]),
            'transactions' => $type === 'transactions' ? $paginated : collect([]),
            'serviceTransactions' => $type === 'services' ? $paginated : collect([]),
        ];
    }

    private function buildTimeline(array $data): Collection
    {
        $timeline = collect();

        foreach (($data['memberships'] ?? collect())->limit(5)->get() as $membership) {
            $timeline->push([
                'type' => 'membership',
                'date' => $membership->created_at,
                'data' => $membership,
                'title' => 'Pembelian Membership: ' . ($membership->package?->name ?? 'Paket Tidak Ditemukan'),
                'description' => 'Status: ' . ($membership->status), // Disederhanakan
                'amount' => null,
                'status' => $membership->status
            ]);
        }

        foreach (($data['checkinLogs'] ?? collect())->limit(5)->get() as $log) {
            $timeline->push([
                'type' => 'checkin',
                'date' => $log->checkin_time,
                'data' => $log,
                'title' => 'Check-in Gym',
                'description' => $log->checkout_time ? 'Durasi: ' . $log->checkin_time->diffForHumans($log->checkout_time, true) : 'Sedang berlangsung',
                'amount' => null,
                'status' => $log->checkout_time ? 'completed' : 'active'
            ]);
        }

        foreach (($data['transactions'] ?? collect())->limit(5)->get() as $transaction) {
            $timeline->push([
                'type' => 'transaction',
                'date' => $transaction->transaction_date,
                'data' => $transaction,
                'title' => 'Pembelian: ' . ($transaction->product?->name ?? 'Produk Tidak Ditemukan'),
                'description' => 'Status: ' . ucfirst($transaction->status),
                'amount' => 'Rp ' . number_format($transaction->amount, 0, ',', '.'),
                'status' => $transaction->status
            ]);
        }

        foreach (($data['serviceTransactions'] ?? collect())->limit(5)->get() as $service) {
            $timeline->push([
                'type' => 'service',
                'date' => $service->transaction_date,
                'data' => $service,
                'title' => 'Layanan: ' . ($service->service?->name ?? 'Layanan Tidak Ditemukan'),
                'description' => 'Status: ' . ucfirst($service->status),
                'amount' => 'Rp ' . number_format($service->amount, 0, ',', '.'),
                'status' => $service->status
            ]);
        }

        return $timeline->sortByDesc('date')->values();
    }

    private function mergeAndSortExportData(array $data): Collection
    {
        return collect()
            ->merge($data['memberships'] ?? collect())
            ->merge($data['checkinLogs'] ?? collect())
            ->merge($data['transactions'] ?? collect())
            ->merge($data['serviceTransactions'] ?? collect())
            ->sortByDesc(function ($item) {
                return $item->created_at ?? $item->checkin_time ?? $item->transaction_date;
            });
    }
}