<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Membership;
use App\Models\ServiceTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Repository\MenuRepository;
use Midtrans\Snap;
use Midtrans\Config;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $config = [
            'title' => 'Daftar Layanan',
            'menu' => MenuRepository::generate($request),
        ];

        $query = Service::active();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $services = $query->orderBy('name')->paginate(12);
        $categories = Service::active()->select('category')->distinct()->pluck('category');
        $trainers = \App\Models\User::role('User:Trainer')->get();

        return view('services.index', compact('services', 'categories', 'trainers', 'config'));
    }

    public function show($id)
    {
        $service = Service::active()->findOrFail($id);
        $relatedServices = Service::active()
            ->where('category', $service->category)
            ->where('id', '!=', $service->id)
            ->get();

        $trainers = \App\Models\User::role('User:Trainer')->get();

        if (request()->ajax()) {
            $html = view('services.partials.detail', compact('service', 'relatedServices', 'trainers'))->render();
            return response()->json([
                'success' => true,
                'service' => $service,
                'html' => $html
            ]);
        }

        return view('services.show', compact('service', 'relatedServices', 'trainers'));
    }

    public function book(Request $request, $id)
    {
        $request->validate([
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_time' => 'required',
            'notes' => 'nullable|string|max:500',
            'trainer_id' => 'nullable|exists:users,id'
        ]);

        $service = Service::active()->findOrFail($id);
        $user = Auth::user();

        $activeMembership = Membership::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$activeMembership) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus memiliki membership aktif untuk memesan layanan.'
            ], 403);
        }
        if (!$service->requires_booking) {
            return response()->json(['success' => false, 'message' => 'Layanan ini tidak memerlukan booking.']);
        }

        DB::beginTransaction();
        try {
            $transaction = ServiceTransaction::create([
                'user_id' => $user->id,
                'service_id' => $service->id,
                'trainer_id' => $request->trainer_id ?: null,
                'transaction_date' => now(),
                'scheduled_date' => $request->scheduled_date . ' ' . $request->scheduled_time,
                'amount' => $service->price,
                'status' => $service->price > 0 ? 'pending' : 'scheduled',
                'notes' => $request->notes
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $service->price > 0
                    ? 'Booking berhasil! Silakan bayar.'
                    : 'Booking berhasil! Layanan terjadwal.',
                'redirect' => $service->price > 0
                    ? route('services.payment', $transaction->id)
                    : route('services.booking-success', $transaction->id)
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Booking Error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat booking: ' . $e->getMessage()
            ], 500);
        }
    }

    public function payment(Request $request, $transactionId)
    {
        $transaction = ServiceTransaction::with(['service', 'user'])->findOrFail($transactionId);

        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        if ($transaction->status !== 'pending') {
            return redirect()->route('services.index')
                ->with('error', 'Transaksi tidak valid untuk pembayaran.');
        }

        $config = [
            'title' => 'Pembayaran - ' . ($transaction->service->name ?? 'Layanan'),
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
                    'order_id' => 'SRV-' . $transaction->id . '-' . time(),
                    'gross_amount' => (int) $transaction->amount,
                ],
                'customer_details' => [
                    'first_name' => $transaction->user->name,
                    'email' => $transaction->user->email,
                ],
                'item_details' => [
                    [
                        'id' => $transaction->service->id,
                        'price' => (int) $transaction->amount,
                        'quantity' => 1,
                        'name' => $transaction->service->name,
                    ]
                ]
            ];

            try {
                $snapToken = Snap::getSnapToken($params);
            } catch (\Exception $e) {
                \Log::error('Midtrans Snap Error: ' . $e->getMessage());
            }
        }

        $clientKey = config('midtrans.client_key');
        $snapUrl = config('midtrans.is_production')
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';

        return view('services.payment', compact('config', 'transaction', 'snapToken', 'clientKey', 'snapUrl'));
    }

    public function confirmPayment(Request $request, $transactionId)
    {
        $transaction = ServiceTransaction::with('service')->findOrFail($transactionId);

        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'payment_proof' => 'required|file|mimes:jpg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('payment_proof')) {
                $path = $request->file('payment_proof')->store('payment_proofs', 'public');
                $transaction->payment_proof = $path;
            }

            $transaction->status = 'waiting_validation';
            $transaction->save();

            DB::commit();

            return redirect()
                ->route('services.booking-success', ['transactionId' => $transaction->id])
                ->with('success', 'Bukti pembayaran berhasil dikirim! Staff akan segera memvalidasi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengkonfirmasi pembayaran: ' . $e->getMessage());
        }
    }

    public function bookingSuccess(Request $request, $transactionId)
    {
        $transaction = ServiceTransaction::with(['service', 'user'])->findOrFail($transactionId);

        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $config = [
            'title' => 'Booking Berhasil',
            'menu' => MenuRepository::generate($request),
        ];

        return view('services.booking-success', compact('config', 'transaction'));
    }

    public function myBookings(Request $request)
    {
        $user = Auth::user();
        $config = [
            'title' => 'Booking Saya',
            'menu' => MenuRepository::generate($request),
        ];

        $bookings = ServiceTransaction::where('user_id', $user->id)
            ->with('service')
            ->orderBy('transaction_date', 'desc')
            ->paginate(15);

        $upcomingBookings = ServiceTransaction::where('user_id', $user->id)
            ->where('status', 'scheduled')
            ->where('scheduled_date', '>', now())
            ->with(['service', 'trainer'])
            ->orderBy('scheduled_date', 'asc')
            ->get();

        return view('services.my-bookings', compact('config', 'user', 'bookings', 'upcomingBookings'));
    }

    public function cancelBooking($transactionId)
    {
        $transaction = ServiceTransaction::findOrFail($transactionId);

        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        if (!$transaction->canCancel()) {
            return redirect()->back()
                ->with('error', 'Pemesanan tidak dapat dibatalkan. Waktu pembatalan telah terlewat atau status tidak memungkinkan.');
        }

        if ($transaction->cancel('Dibatalkan oleh pengguna')) {
            return redirect()->back()
                ->with('success', 'Pemesanan berhasil dibatalkan.');
        } else {
            return redirect()->back()
                ->with('error', 'Gagal membatalkan pemesanan.');
        }
    }

    public function qrScan()
    {
        $user = Auth::user();
        $activeMembership = Membership::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('package')
            ->first();

        $availableServices = Service::active()
            ->where('requires_booking', false)
            ->get();

        return view('services.qr-scan', compact('user', 'activeMembership', 'availableServices'));
    }

    // ServiceController.php

    // Di dalam file: app/Http/Controllers/ServiceController.php

    public function processQrService(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
            'service_id' => 'required|exists:additional_services,id'
        ]);

        // Ambil data service berdasarkan ID yang dipilih dari form
        $service = Service::find($request->service_id); // Pastikan Model Service di-use di atas
        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Layanan tidak ditemukan atau tidak aktif.'
            ], 404);
        }

        // Validasi sederhana, bisa disesuaikan dengan format QR Anda
        // (Kode validasi ini bisa Anda sesuaikan jika perlu)
        $qrData = $request->qr_code;
        if (strpos($qrData, 'SERVICE_') !== 0) {
            return response()->json(['success' => false, 'message' => 'QR Code layanan tidak valid.'], 400);
        }
        $serviceIdFromQr = str_replace('SERVICE_', '', $qrData);
        if ($serviceIdFromQr != $service->id) {
            return response()->json(['success' => false, 'message' => 'QR Code tidak sesuai.'], 400);
        }

        // ==========================================================
        // INILAH BAGIAN OTOMATISNYA
        // ==========================================================
        $user = Auth::user(); // 1. Ambil data member yang sedang login

        DB::beginTransaction();
        try {
            // 2. Buat transaksi baru dan langsung isi 'user_id'
            ServiceTransaction::create([ // Pastikan Model ServiceTransaction di-use
                'user_id' => $user->id, // <-- ID member diambil otomatis
                'service_id' => $service->id,
                'transaction_date' => now(),
                'scheduled_date' => now(), // Langsung digunakan saat itu juga
                'amount' => $service->price,
                'status' => 'completed', // Langsung dianggap selesai
                'notes' => 'Digunakan via QR Scan'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Layanan ' . $service->name . ' berhasil digunakan!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
