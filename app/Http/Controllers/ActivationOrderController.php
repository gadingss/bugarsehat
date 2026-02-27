<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\MembershipPacket;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Product; // Pastikan model ini ada di app/Models/Product.php
use App\Models\Service; // Pastikan model ini ada di app/Models/Service.php
use App\Repository\MenuRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ActivationOrderController extends Controller
{
    /**
     * Menampilkan daftar data berdasarkan tab yang aktif.
     */
    public function index(Request $request)
    {
        $config = [
            'title' => 'Activation Management',
            'title-alias' => 'Manajemen Aktivasi',
            'menu' => MenuRepository::generate($request),
        ];

        $activeTab = $request->get('tab', 'activation');

        // Siapkan variabel data yang akan dikirim ke view
        $viewData = [
            'config' => $config,
            'activeTab' => $activeTab,
            'memberships' => new LengthAwarePaginator([], 0, 15), // Default Paginator kosong
            'products' => new LengthAwarePaginator([], 0, 15),
            'services' => new LengthAwarePaginator([], 0, 15),
        ];

        if (in_array($activeTab, ['activation', 'extension', 'application'])) {
            // ===================================
            // Logika untuk Tab Membership
            // ===================================
            $query = Membership::query()
                ->with(['user', 'package', 'transaction'])
                ->latest('memberships.created_at');

            if (Auth::user()->hasRole('User:Member')) {
                $query->where('user_id', Auth::id());
            }

            switch ($activeTab) {
                case 'activation':
                    $query->where('type', '!=', 'extension');
                    break;
                case 'extension':
                    $query->where('type', 'extension');
                    break;
                case 'application':
                    $query->where('type', 'application');
                    break;
            }

            $this->applyMembershipFilters($query, $request);
            $viewData['memberships'] = $query->paginate(15)->withQueryString();

        } elseif ($activeTab == 'product') {
            // ===================================
            // Logika untuk Tab Produk
            // ===================================
            $query = Product::query()->with('user')->latest(); // Asumsi ada relasi 'user' di model Product

            // Filter khusus untuk user dengan role 'User:Member'
            if (Auth::user()->hasRole('User:Member')) {
                $query->where('user_id', Auth::id());
            }

            $this->applyProductServiceFilters($query, $request);
            $viewData['products'] = $query->paginate(15)->withQueryString();

        } elseif ($activeTab == 'service') {
            // ===================================
            // Logika untuk Tab Layanan
            // ===================================
            $query = Service::query()->with('user')->latest(); // Asumsi ada relasi 'user' di model Service
            
            // Filter khusus untuk user dengan role 'User:Member'
            if (Auth::user()->hasRole('User:Member')) {
                $query->where('user_id', Auth::id());
            }

            $this->applyProductServiceFilters($query, $request);
            $viewData['services'] = $query->paginate(15)->withQueryString();
        }

        return view('activation_order.index', $viewData);
    }

    /**
     * Menerapkan filter pencarian ke query Membership.
     */
    private function applyMembershipFilters($query, Request $request)
    {
        if ($request->filled('member_name')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->member_name . '%'));
        }
        if ($request->filled('package_name')) {
            $query->whereHas('package', fn($q) => $q->where('name', 'like', '%' . $request->package_name . '%'));
        }
        if ($request->filled('payment_status')) {
            $query->whereHas('transaction', fn($q) => $q->where('status', $request->payment_status));
        }
        if ($request->filled('activation_status')) {
            $query->where('status', $request->activation_status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
    }

    /**
     * Menerapkan filter pencarian ke query Produk dan Layanan.
     */
    private function applyProductServiceFilters($query, Request $request)
    {
        if ($request->filled('member_name')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->member_name . '%'));
        }
        // Menggunakan field 'package_name' dari form untuk mencari nama produk/layanan
        if ($request->filled('package_name')) {
            $query->where('name', 'like', '%' . $request->package_name . '%');
        }
        // Menggunakan field 'payment_status' dari form untuk mencari status produk/layanan (misal: 'pending', 'approved')
        if ($request->filled('payment_status')) {
            $query->where('status', $request->payment_status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
    }

    /**
     * Menampilkan form untuk membuat pengajuan membership baru.
     */
    public function createApplication(Request $request)
    {
        $config = [
            'title' => 'Membership Application',
            'title-alias' => 'Pengajuan Membership',
            'menu' => MenuRepository::generate($request),
        ];

        $packages = MembershipPacket::where('type', 'paket')->where('is_active', true)->get();
        $members = User::whereDoesntHave('memberships', fn($q) => $q->where('status', 'active')->where('end_date', '>=', now()))->get();

        return view('activation_order.application', compact('config', 'packages', 'members'));
    }

    /**
     * Menyimpan data pengajuan membership baru.
     */
    public function storeApplication(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'package_id' => 'required|exists:membership_packages,id',
            'start_date' => 'required|date|after_or_equal:today',
        ]);

        $package = MembershipPacket::findOrFail($request->package_id);

        if (Membership::where('user_id', $request->user_id)->where('type', 'application')->whereIn('status', ['inactive', 'pending'])->exists()) {
            return redirect()->back()->with('error', 'Member sudah memiliki pengajuan yang menunggu pembayaran.');
        }

        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'user_id' => $request->user_id,
                'product_id' => $package->id,
                'product_type' => MembershipPacket::class,
                'transaction_date' => now(),
                'amount' => $package->price,
                'status' => 'pending',
                'quantity' => 1,
            ]);

            Membership::create([
                'user_id' => $request->user_id,
                'package_id' => $package->id,
                'transaction_id' => $transaction->id,
                'type' => 'application',
                'start_date' => $request->start_date,
                'end_date' => Carbon::parse($request->start_date)->addDays($package->duration_days),
                'remaining_visits' => $package->max_visits,
                'status' => 'pending',
            ]);

            DB::commit();
            return redirect()->route('activation_order', ['tab' => 'application'])->with('success', 'Pengajuan membership berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal membuat pengajuan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan form untuk perpanjangan membership.
     */
    public function createExtension(Request $request)
    {
        $config = [
            'title' => 'Membership Extension',
            'title-alias' => 'Perpanjangan Membership',
            'menu' => MenuRepository::generate($request),
        ];

        $members = User::whereHas('memberships', fn($q) => $q->where('status', 'active')->where('end_date', '>=', now()))->get();
        $packages = MembershipPacket::where('type', 'paket')->where('is_active', true)->get();

        return view('activation_order.extension', compact('config', 'members', 'packages'));
    }

    /**
     * Menyimpan data perpanjangan membership.
     */
    public function storeExtension(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'package_id' => 'required|exists:membership_packages,id',
        ]);

        $package = MembershipPacket::findOrFail($request->package_id);
        $activeMembership = Membership::where('user_id', $request->user_id)->where('status', 'active')->latest('end_date')->first();

        if (!$activeMembership) {
            return redirect()->back()->with('error', 'Member tidak memiliki membership aktif untuk diperpanjang.');
        }

        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'user_id' => $request->user_id,
                'product_id' => $package->id,
                'product_type' => MembershipPacket::class,
                'transaction_date' => now(),
                'amount' => $package->price,
                'status' => 'pending',
                'quantity' => 1,
            ]);

            $startDate = Carbon::parse($activeMembership->end_date)->addDay();
            Membership::create([
                'user_id' => $request->user_id,
                'package_id' => $package->id,
                'transaction_id' => $transaction->id,
                'type' => 'extension',
                'start_date' => $startDate,
                'end_date' => $startDate->copy()->addDays($package->duration_days),
                'remaining_visits' => $package->max_visits,
                'status' => 'inactive',
            ]);

            DB::commit();
            return redirect()->route('activation_order', ['tab' => 'extension'])->with('success', 'Perpanjangan membership berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal membuat perpanjangan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan form edit membership.
     */
    public function edit($id)
    {
        $membership = Membership::findOrFail($id);
        $packages = MembershipPacket::where('type', 'paket')->where('is_active', true)->get();
        $config = [
            'title' => 'Edit Membership',
            'title-alias' => 'Edit Membership',
            'menu' => MenuRepository::generate(request()),
        ];
        return view('activation_order.edit', compact('config', 'membership', 'packages'));
    }

    /**
     * Menyimpan perubahan data membership.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'package_id' => 'required|exists:membership_packages,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:inactive,active,cancelled,expired,pending',
        ]);

        $membership = Membership::findOrFail($id);
        $package = MembershipPacket::findOrFail($request->package_id);

        DB::beginTransaction();
        try {
            $membership->update([
                'package_id' => $request->package_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
            ]);

            if ($membership->transaction) {
                $membership->transaction->update([
                    'amount' => $package->price,
                    'product_id' => $package->id,
                ]);
            }
            DB::commit();
            return redirect()->route('activation_order', ['tab' => $membership->type])->with('success', 'Membership berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal memperbarui membership: ' . $e->getMessage());
        }
    }

    /**
     * Menyetujui pengajuan membership.
     */
    public function approve($id)
    {
        $membership = Membership::with('transaction')->findOrFail($id);

        if (!in_array($membership->status, ['inactive', 'pending'])) {
            return redirect()->back()->with('error', 'Hanya membership inactive/pending yang bisa disetujui.');
        }
        if ($membership->transaction && $membership->transaction->status !== 'validated') {
            return redirect()->back()->with('error', 'Pembayaran untuk membership ini belum divalidasi.');
        }

        DB::beginTransaction();
        try {
            $membership->update(['status' => 'active']);
            DB::commit();
            return redirect()->back()->with('success', 'Membership berhasil disetujui dan diaktifkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menyetujui membership: ' . $e->getMessage());
        }
    }

    /**
     * Menolak pengajuan membership.
     */
    public function reject($id)
    {
        $membership = Membership::with('transaction')->findOrFail($id);

        if (!in_array($membership->status, ['inactive', 'pending'])) {
            return redirect()->back()->with('error', 'Hanya membership inactive/pending yang bisa ditolak.');
        }

        DB::beginTransaction();
        try {
            $membership->update(['status' => 'cancelled']);
            if ($membership->transaction) {
                $membership->transaction->update(['status' => 'cancelled']);
            }
            DB::commit();
            return redirect()->back()->with('success', 'Membership berhasil ditolak.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menolak membership: ' . $e->getMessage());
        }
    }

    /**
     * Memvalidasi pembayaran secara manual.
     */
    public function validatePayment($membershipId)
    {
        $membership = Membership::findOrFail($membershipId);
        if (!$membership->transaction) {
            return redirect()->back()->with('error', 'Transaksi tidak ditemukan untuk membership ini.');
        }
        if ($membership->transaction->status !== 'pending') {
            return redirect()->back()->with('error', 'Transaksi tidak valid atau sudah divalidasi.');
        }

        $membership->transaction->update([
            'status' => 'validated',
            'validated_by' => Auth::id(),
        ]);
        return redirect()->back()->with('success', 'Pembayaran berhasil divalidasi.');
    }

    /**
     * Mengaktifkan membership yang pembayarannya sudah valid.
     */
    public function activateMembership($membershipId)
    {
        $membership = Membership::with('transaction')->findOrFail($membershipId);

        if (!in_array($membership->status, ['inactive', 'pending'])) {
            return redirect()->back()->with('error', 'Hanya membership inactive/pending yang bisa diaktifkan.');
        }
        if ($membership->transaction && $membership->transaction->status !== 'validated') {
            return redirect()->back()->with('error', 'Pembayaran belum divalidasi.');
        }

        $membership->update(['status' => 'active']);
        return redirect()->back()->with('success', 'Membership berhasil diaktifkan.');
    }

    /**
     * Menghapus data membership.
     */
    public function destroy($id)
    {
        $membership = Membership::findOrFail($id);
        if (!in_array($membership->status, ['inactive', 'pending', 'cancelled'])) {
            return redirect()->back()->with('error', 'Hanya membership inactive, pending, atau cancelled yang boleh dihapus.');
        }

        DB::beginTransaction();
        try {
            if ($membership->transaction) {
                $membership->transaction->delete();
            }
            $membership->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Membership berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menghapus membership: ' . $e->getMessage());
        }
    }
}