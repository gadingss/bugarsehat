<?php

namespace App\Http\Controllers;

use App\Models\{Membership, MembershipPacket, Transaction};
use App\Repository\MenuRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Validator};
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

// midtrans SDK (install with composer require midtrans/midtrans-php)
use Midtrans\Snap;
use Midtrans\Config;  

class PacketMembershipController extends Controller
{
    /**
     * Menerapkan middleware untuk proteksi route.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->hasRole('member')) {
                abort(403, 'AKSI INI TIDAK DIIZINKAN.');
            }
            return $next($request);
        })->only(['store', 'edit', 'update', 'destroy', 'activateMembership']);
    }

    /**
     * Menampilkan halaman daftar semua paket membership.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isMember = $user && ($user->hasRole('member') || $user->role === 'member');
        
        $config = [
            'title' => 'Katalog Membership',
            'title-alias' => 'Katalog Paket',
            'menu' => MenuRepository::generate($request),
        ];
        $packets = MembershipPacket::with('services')->orderBy('price', 'asc')->get();
        return view('packet_membership.index', compact('config', 'packets'));
    }

    /**
     * Menampilkan halaman Master Data Paket Membership (Table)
     */
    public function master(Request $request)
    {
        $config = [
            'title' => 'Kelola Membership',
            'title-alias' => 'Master Data Paket',
            'menu' => MenuRepository::generate($request),
        ];
        $packets = MembershipPacket::with('services')->orderBy('price', 'asc')->get();
        $services = \App\Models\Service::active()->get();
        return view('packet_membership.master', compact('config', 'packets', 'services'));
    }

    /**
     * Mengambil detail paket untuk ditampilkan di modal (via AJAX).
     */
    public function show($id)
    {
        $packet = MembershipPacket::with('services')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $packet]);
    }

    /**
     * Menyimpan paket membership baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:membership_packages,name',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'max_visits' => 'required|integer|min:1',
            'description' => 'nullable|string|max:500',
            'product_details' => 'nullable|string',
            'services' => 'nullable|array',
            'services.*' => 'exists:additional_services,id',
        ]);
        
        $packet = MembershipPacket::create($request->except('services'));
        
        if ($request->has('services')) {
            $packet->services()->sync($request->services);
        }
        
        return redirect()->back()->with('success', 'Paket membership berhasil ditambahkan.');
    }

    /**
     * Menampilkan halaman daftar paket dengan modal edit yang aktif.
     */
    public function edit(Request $request, $id)
    {
        $config = [
            'title' => 'Edit Katalog Membership',
            'title-alias' => 'Edit Paket',
            'menu' => MenuRepository::generate($request),
        ];
        $packet = MembershipPacket::findOrFail($id);
        $packets = MembershipPacket::orderBy('price', 'asc')->get();
        return view('packet_membership.index', compact('config', 'packets', 'packet'));
    }

    /**
     * Menampilkan halaman master dengan modal edit yang aktif.
     */
    public function editMaster(Request $request, $id)
    {
        $config = [
            'title' => 'Edit Kelola Membership',
            'title-alias' => 'Edit Paket Master',
            'menu' => MenuRepository::generate($request),
        ];
        $packet = MembershipPacket::with('services')->findOrFail($id);
        $packets = MembershipPacket::with('services')->orderBy('price', 'asc')->get();
        $services = \App\Models\Service::active()->get();
        return view('packet_membership.master', compact('config', 'packets', 'packet', 'services'));
    }

    /**
     * Memperbarui data paket membership di database.
     */
    public function update(Request $request, $id)
    {
        $packet = MembershipPacket::findOrFail($id);
        $request->validate([
            'name' => ['required', 'string', 'max:50', Rule::unique('membership_packages')->ignore($packet->id)],
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'max_visits' => 'required|integer|min:1',
            'description' => 'nullable|string|max:500',
            'product_details' => 'nullable|string',
            'services' => 'nullable|array',
            'services.*' => 'exists:additional_services,id',
        ]);
        
        $packet->update($request->except('services'));
        
        if ($request->has('services')) {
            $packet->services()->sync($request->services);
        } else {
            $packet->services()->detach();
        }
        
        // For update, we want to go back to the base route rather than back to raw edit url
        // So we will just redirect back, but if back is the edit url, redirect to the index or master based on referring url
        if (str_contains(url()->previous(), 'master_membership')) {
            return redirect()->route('master_membership')->with('success', 'Paket membership berhasil diperbarui.');
        }
        return redirect()->route('packet_membership')->with('success', 'Paket membership berhasil diperbarui.');
    }

    /**
     * Menghapus paket membership dari database.
     */
    public function destroy($id)
    {
        $packet = MembershipPacket::findOrFail($id);
        if ($packet->memberships()->where('status', 'active')->exists()) {
            return redirect()->back()->with('error', 'Gagal! Paket masih digunakan oleh member aktif.');
        }
        $packet->delete();
        return redirect()->back()->with('success', 'Paket membership berhasil dihapus.');
    }

    /**
     * Menampilkan halaman checkout untuk paket yang dipilih member.
     */
    public function selectPacket(Request $request, $id)
    {
        $packet = MembershipPacket::findOrFail($id);
        $user = Auth::user();
        $config = ['title' => 'Checkout Paket', 'menu' => MenuRepository::generate($request)];

        if ($packet->price == 0) {
            $hasTakenTrial = Membership::where('user_id', $user->id)
                ->whereHas('package', fn($q) => $q->where('price', 0))
                ->exists();
            if ($hasTakenTrial) {
                return redirect()->route('packet-membership.index')->with('error', 'Anda sudah pernah mengambil paket trial.');
            }
        }

        $activeMembership = $user->memberships()->where('status', 'active')->where('end_date', '>=', now())->first();
        
        // Midtrans Config untuk View
        $isProduction = config('midtrans.is_production');
        $snapUrl = $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js';
        $clientKey = config('midtrans.client_key');
        
        return view('packet_membership.checkout', compact('config', 'packet', 'user', 'activeMembership', 'snapUrl', 'clientKey'));
    }

    /**
     * [FINAL] Memproses pembelian paket dan mengembalikan JSON response.
     */
    public function purchasePacket(Request $request, $id)
    {
        $packet = MembershipPacket::findOrFail($id);
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            // 'midtrans' option represents Midtrans gateway
            'payment_method' => ['required', 'string', Rule::in(['midtrans', 'free'])],
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $user->memberships()->where('status', 'active')->update(['status' => 'inactive']);

            $transaction = null;
            if ($packet->price > 0) {
                $transaction = Transaction::create([
                    'invoice_id' => 'INV-' . time() . Str::upper(Str::random(5)),
                    'user_id' => $user->id,
                    'product_id' => $packet->id,
                    'product_type' => MembershipPacket::class,
                    'quantity' => 1,
                    'transaction_date' => now(),
                    'amount' => $packet->price,
                    'status' => 'pending',
                ]);
            }

            $membership = Membership::create([
                'user_id' => $user->id,
                'package_id' => $packet->id,
                'transaction_id' => $transaction?->id,
                'start_date' => now(),
                'end_date' => now()->addDays($packet->duration_days),
                'remaining_visits' => $packet->max_visits,
                'status' => $packet->price == 0 ? 'active' : 'inactive',
            ]);

            $responseData = [
                'success' => true,
                'payment_method' => $request->payment_method,
                'membership_id' => $membership->id, // allow frontend to know which membership was created
            ];

            if ($packet->price > 0) {
                $responseData['invoice_id'] = $transaction->invoice_id;
                $responseData['total'] = $transaction->amount;

                switch ($request->payment_method) {
                    case 'midtrans':
                        // initialize midtrans configuration
                        Config::$serverKey      = config('midtrans.server_key');
                        Config::$clientKey      = config('midtrans.client_key');
                        Config::$isProduction   = config('midtrans.is_production');
                        Config::$isSanitized    = config('midtrans.is_sanitized');
                        Config::$is3ds          = config('midtrans.is_3ds');

                        // debug logging to ensure keys are set
                        \Log::info('midtrans config in purchasePacket', config('midtrans'));
                        \Log::info('midtrans Config static', [
                            'serverKey' => Config::$serverKey,
                            'clientKey' => Config::$clientKey
                        ]);

                        $midtransParams = [
                            'transaction_details' => [
                                'order_id'     => $transaction->invoice_id,  // Gunakan invoice_id agar sesuai dengan pencarian di MidtransController
                                'gross_amount' => (int) $transaction->amount,  // convert to int (Midtrans requirement)
                            ],
                            'customer_details' => [
                                'first_name' => $user->name,
                                'email'      => $user->email,
                                'phone'      => $user->phone,
                            ],
                        ];

                        try {
                            $snapToken = Snap::getSnapToken($midtransParams);
                            $responseData['snap_token'] = $snapToken;
                            $responseData['message'] = 'Silakan lanjutkan pembayaran menggunakan Midtrans.';
                        } catch (\Exception $e) {
                            DB::rollBack();
                            return response()->json(['success' => false, 'message' => 'Gagal membuat transaksi Midtrans: ' . $e->getMessage()], 500);
                        }
                        break;
                }
            } else {
                $responseData['redirect_url'] = route('packet_membership.success', $membership->id);
            }

            DB::commit();
            return response()->json($responseData);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Menampilkan halaman konfirmasi setelah pembelian/aktivasi berhasil.
     */
    public function successPage(Request $request, $membershipId)
    {
        $config = ['title' => 'Pembelian Berhasil', 'menu' => MenuRepository::generate($request)];
        $membership = Membership::with(['package', 'user'])->findOrFail($membershipId);
        return view('packet_membership.success', compact('config', 'membership'));
    }

    /**
     * Mengaktifkan membership (misalnya, setelah pembayaran cash dikonfirmasi).
     */
    public function activateMembership($membershipId)
    {
        $membership = Membership::with('transaction')->findOrFail($membershipId);
        DB::beginTransaction();
        try {
            $membership->update(['status' => 'active']);
            if ($membership->transaction && $membership->transaction->status !== 'validated') {
                $membership->transaction->update(['status' => 'validated', 'validated_by' => Auth::id()]);
            }
            DB::commit();
            return redirect()->route('transaction.history')->with('success', 'Membership berhasil diaktifkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal aktivasi: ' . $e->getMessage());
        }
    }
}