<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\MembershipProduct;
use App\Models\Membership;
use App\Repository\MenuRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Midtrans\Snap;
use Midtrans\Config;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Konfigurasi sidebar/menu
        $config = [
            'title' => 'Produk',
            'title-alias' => 'produk',
            'menu' => MenuRepository::generate($request),
        ];

        // Query produk aktif
        $query = Product::active();

        // Filter berdasarkan kategori
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Pencarian berdasarkan nama
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter promo
        if ($request->filled('promo') && $request->promo == '1') {
            $query->promo();
        }

        // Ambil produk
        $products = $query->orderBy('name', 'asc')->paginate(12);

        // Ambil kategori unik
        $categories = Product::active()
            ->select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');

        // Ambil produk promo
        $promoProducts = Product::active()->promo()->take(6)->get();

        // Kirim semua data ke view
        return view('products.index', compact('config', 'products', 'categories', 'promoProducts'));
    }


    public function store(Request $request)
    {
        // Validasi file
        $request->validate([
            'nama' => 'required|string|max:255',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Simpan file ke storage/app/public/produk
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('produk', 'public');
        }

        // Simpan ke database
        Product::create([
            'nama' => $request->nama,
            'gambar' => $path
        ]);

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan');
    }

    public function show($id)
    {
        $product = Product::active()->findOrFail($id);
        $relatedProducts = Product::active()
            ->where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    public function catalog()
    {
        $user = Auth::user();
        $activeMembership = Membership::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('package')
            ->first();

        $membershipProducts = [];
        if ($activeMembership) {
            $membershipProducts = MembershipProduct::where('membership_id', $activeMembership->id)
                ->with('product')
                ->get();
        }

        $allProducts = Product::active()->orderBy('category', 'asc')->get();
        $categories = $allProducts->groupBy('category');

        return view('products.catalog', compact(
            'user',
            'activeMembership',
            'membershipProducts',
            'categories'
        ));
    }

    public function purchase(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::active()->find($id); // Ganti dari findOrFail ke find

        // Kalau produk tidak ada atau tidak aktif
        if (!$product) {
            // Kalau request lewat AJAX
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan atau sudah tidak aktif.'
                ], 404);
            }

            return redirect()->back()
                ->with('error', 'Produk tidak ditemukan atau sudah tidak aktif.');
        }

        $user = Auth::user();
        $quantity = $request->quantity;

        // Cek stok
        if ($product->stock < $quantity) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $product->stock
                ], 400);
            }

            return redirect()->back()
                ->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $product->stock);
        }

        $totalAmount = $product->getCurrentPrice() * $quantity;

        DB::beginTransaction();
        try {
            // Buat transaksi
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'transaction_date' => now(),
                'amount' => $totalAmount,
                'status' => 'pending'
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produk berhasil dibeli: ' . $product->name,
                    'redirect_url' => route('products.payment', $transaction->id)
                ]);
            }

            return redirect()->route('products.payment', $transaction->id)
                ->with('success', 'Pembelian berhasil! Silakan lakukan pembayaran dan tunggu validasi staff.');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal melakukan pembelian: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal melakukan pembelian: ' . $e->getMessage());
        }
    }


    public function payment(Request $request, $transactionId)
    {
        $transaction = Transaction::with(['product', 'user'])->findOrFail($transactionId);

        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        if ($transaction->status !== 'pending') {
            return redirect()->route('products.index')
                ->with('error', 'Transaksi tidak valid untuk pembayaran.');
        }

        $config = [
            'title' => 'Pembayaran - ' . ($transaction->product->name ?? 'Produk'),
            'menu' => MenuRepository::generate($request),
        ];

        // Midtrans Logic
        $snapToken = null;
        if (config('midtrans.server_key')) {
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = config('midtrans.is_sanitized');
            Config::$is3ds = config('midtrans.is_3ds');

            $params = [
                'transaction_details' => [
                    'order_id' => 'PRD-' . $transaction->id . '-' . time(),
                    'gross_amount' => (int) $transaction->amount,
                ],
                'customer_details' => [
                    'first_name' => $transaction->user->name,
                    'email' => $transaction->user->email,
                ],
                'item_details' => [
                    [
                        'id' => $transaction->product->id,
                        'price' => (int) ($transaction->amount / $transaction->quantity),
                        'quantity' => $transaction->quantity,
                        'name' => $transaction->product->name,
                    ]
                ]
            ];

            try {
                $snapToken = Snap::getSnapToken($params);
            } catch (\Exception $e) {
                \Log::error('Midtrans Snap Error (Product): ' . $e->getMessage());
            }
        }

        $clientKey = config('midtrans.client_key');
        $snapUrl = config('midtrans.is_production')
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';

        return view('products.payment', compact('config', 'transaction', 'snapToken', 'clientKey', 'snapUrl'));
    }

    public function confirmPayment(Request $request, $transactionId)
    {
        // Ambil transaksi beserta relasi product
        $transaction = Transaction::with('product')->findOrFail($transactionId);

        // Pastikan user yang login adalah pemilik transaksi
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        // Validasi file bukti pembayaran
        $request->validate([
            'payment_proof' => 'required|file|mimes:jpg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('payment_proof')) {
                // Simpan file di storage/app/public/payment_proofs
                $path = $request->file('payment_proof')->store('payment_proofs', 'public');
                $transaction->payment_proof = $path;
            }

            // Update status transaksi
            $transaction->status = 'waiting_validation';
            $transaction->save();

            DB::commit();

            return redirect()
                ->route('products.success', ['transactionId' => $transaction->id])
                ->with('success', 'Bukti pembayaran berhasil dikirim! Staff akan segera memvalidasi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengkonfirmasi pembayaran: ' . $e->getMessage());
        }
    }




    public function success(Request $request, $id)
    {
        $transaction = Transaction::with('product')->findOrFail($id);

        $config = [
            'title' => 'Pembelian Berhasil',
            'menu' => MenuRepository::generate($request),
        ];

        return view('products.success', compact('config', 'transaction'));
    }


    public function myProducts()
    {
        $user = Auth::user();
        $activeMembership = Membership::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        $membershipProducts = [];
        if ($activeMembership) {
            $membershipProducts = MembershipProduct::where('membership_id', $activeMembership->id)
                ->with('product')
                ->get();
        }

        $purchasedProducts = Transaction::where('user_id', $user->id)
            ->where('status', 'validated')
            ->with('product')
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);

        return view('products.my-products', compact(
            'user',
            'activeMembership',
            'membershipProducts',
            'purchasedProducts'
        ));
    }

    public function useProduct($membershipProductId)
    {
        $membershipProduct = MembershipProduct::with(['product', 'membership'])
            ->findOrFail($membershipProductId);

        if ($membershipProduct->membership->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        if (!$membershipProduct->canUse()) {
            return redirect()->back()
                ->with('error', 'Produk tidak dapat digunakan. Periksa kuota atau tanggal kedaluwarsa.');
        }

        if ($membershipProduct->useProduct()) {
            return redirect()->back()
                ->with('success', 'Produk berhasil digunakan! Sisa kuota: ' . $membershipProduct->getRemainingQuantity());
        } else {
            return redirect()->back()
                ->with('error', 'Gagal menggunakan produk.');
        }
    }
}
