<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Membership;
use App\Models\ServiceTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        try {
            $service = Service::with('sessionTemplates')->findOrFail($id);

            $relatedServices = Service::active()
                ->where('category', $service->category)
                ->where('id', '!=', $service->id)
                ->get();

            $trainers = \App\Models\User::role('User:Trainer')->get();

            if (request()->expectsJson() || request()->ajax()) {
                $html = view('services.partials.detail', compact('service', 'relatedServices', 'trainers'))->render();
                return response()->json([
                    'success' => true,
                    'service' => $service,
                    'html' => $html
                ]);
            }

            return view('services.show', compact('service', 'relatedServices', 'trainers'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Service Detail Error: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal memuat detail layanan.');
        }
    }

    public function getSchedules($serviceId)
    {
        $schedules = \App\Models\Schedule::with('trainer', 'service')
            ->where('service_id', $serviceId)
            ->where('start_time', '>', now())
            ->get()
            ->groupBy(function($item) {
                return $item->service_id . '_' . $item->created_at->format('Y-m-d_H:i');
            })
            ->map(function ($group) {
                $first = $group->sortBy('start_time')->first();
                $count = $group->count();
                $title = $first->service->name ?? preg_replace('/ Sesi \d+$/', '', $first->title);
                return [
                    'id' => $first->id,
                    'text' => $first->start_time->format('d M Y, H:i') . ' - ' . $title . 
                              ($count > 1 ? ' (' . $count . ' Pertemuan)' : '') . 
                              ' (Trainer: ' . ($first->trainer->name ?? '-') .') - Terisi: ' . $first->bookings()->count() . '/' . $first->capacity
                ];
            })
            ->values();
            
        return response()->json(['success' => true, 'schedules' => $schedules]);
    }

    public function manage(Request $request)
    {
        $config = [
            'title' => 'Kelola Layanan',
            'title-alias' => 'Kelola Layanan',
            'menu' => MenuRepository::generate($request),
        ];

        $services = Service::orderBy('name')->get();
        return view('services.manage', compact('services', 'config'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:0',
            'category' => 'required|string',
            'max_participants' => 'nullable|integer|min:1',
            'sessions_count' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'requires_booking' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:3072',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('layanans', 'public');
        }

        Service::create([
            'name' => $request->name,
            'price' => $request->price,
            'duration_minutes' => $request->duration_minutes,
            'category' => $request->category,
            'max_participants' => $request->max_participants,
            'sessions_count' => $request->sessions_count,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'requires_booking' => $request->has('requires_booking'),
            'image' => $path
        ]);

        return redirect()->back()->with('success', 'Layanan berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:0',
            'category' => 'required|string',
            'max_participants' => 'nullable|integer|min:1',
            'sessions_count' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'requires_booking' => 'boolean',
        ]);

        $service->update([
            'name' => $request->name,
            'price' => $request->price,
            'duration_minutes' => $request->duration_minutes,
            'category' => $request->category,
            'max_participants' => $request->max_participants,
            'sessions_count' => $request->sessions_count,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'requires_booking' => $request->has('requires_booking'),
        ]);

        return redirect()->back()->with('success', 'Layanan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        
        if ($service->image && Storage::disk('public')->exists($service->image)) {
            Storage::disk('public')->delete($service->image);
        }
        
        $service->delete();
        return redirect()->back()->with('success', 'Layanan berhasil dihapus');
    }

    public function storeTemplate(Request $request, $serviceId)
    {
        $request->validate([
            'session_number' => 'required|integer',
            'topic' => 'nullable|string|max:255',
        ]);

        \App\Models\ServiceSessionTemplate::create([
            'service_id' => $serviceId,
            'session_number' => $request->session_number,
            'topic' => $request->topic,
        ]);

        return response()->json(['success' => true]);
    }

    public function updateTemplate(Request $request, $templateId)
    {
        $request->validate([
            'topic' => 'nullable|string|max:255',
        ]);

        $template = \App\Models\ServiceSessionTemplate::findOrFail($templateId);
        $template->update(['topic' => $request->topic]);

        return response()->json(['success' => true]);
    }

    public function deleteTemplate($templateId)
    {
        $template = \App\Models\ServiceSessionTemplate::findOrFail($templateId);
        $template->delete();

        return response()->json(['success' => true]);
    }

    public function updateImage(Request $request, $id)
    {
        // Only Owner and Staff can update images
        if (!Auth::user()->hasRole('User:Owner') && !Auth::user()->hasRole('User:Staff')) {
            abort(403);
        }

        $service = Service::findOrFail($id);

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ]);

        // Delete old image if exists
        if ($service->image && Storage::disk('public')->exists($service->image)) {
            Storage::disk('public')->delete($service->image);
        }

        $path = $request->file('image')->store('layanan', 'public');
        $service->update(['image' => $path]);

        return redirect()->back()->with('success', 'Gambar layanan berhasil diperbarui.');
    }

    public function book(Request $request, $id)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $service = Service::active()->findOrFail($id);
        $user = Auth::user();
        $schedule = \App\Models\Schedule::findOrFail($request->schedule_id);

        if (!$service->requires_booking) {
            return response()->json(['success' => false, 'message' => 'Layanan ini tidak memerlukan booking.']);
        }

        // Get full batch to book all sessions simultaneously
        $createdAtStart = $schedule->created_at->copy()->startOfMinute();
        $createdAtEnd = $schedule->created_at->copy()->endOfMinute();
        $batchSchedules = \App\Models\Schedule::where('service_id', $schedule->service_id)
            ->whereBetween('created_at', [$createdAtStart, $createdAtEnd])
            ->orderBy('start_time')
            ->get();

        // Check if any schedule is full
        foreach ($batchSchedules as $batchItem) {
            if ($batchItem->bookings()->count() >= $batchItem->capacity) {
                return response()->json(['success' => false, 'message' => 'Maaf, kuota jadwal kelas ini sudah penuh pada salah satu daftar sesinya.']);
            }
        }

        // Check if member already booked any schedule in batch
        $alreadyBooked = \App\Models\Booking::where('user_id', $user->id)
            ->whereIn('schedule_id', $batchSchedules->pluck('id'))
            ->exists();
        
        if ($alreadyBooked) {
            return response()->json(['success' => false, 'message' => 'Anda sudah terdaftar di kelas ini.']);
        }

        DB::beginTransaction();
        try {
            // Check if user has available pending quota transaction
            // We find any transaction that has ENOUGH pending sessions
            $availableSessions = null;
            $transaction = null;
            
            $availableTransactions = \App\Models\ServiceTransaction::where('user_id', $user->id)
                  ->where('service_id', $service->id)
                  ->whereIn('status', ['scheduled', 'completed'])
                  ->with(['serviceSessions' => function($q) {
                      $q->where('status', 'pending')
                        ->whereNull('scheduled_date')
                        ->orderBy('session_number', 'asc');
                  }])
                  ->get();

            foreach ($availableTransactions as $tx) {
                if ($tx->serviceSessions->count() >= $batchSchedules->count()) {
                    $transaction = $tx;
                    $availableSessions = $tx->serviceSessions->take($batchSchedules->count());
                    break;
                }
            }

            $statusMsg = 'Layanan terjadwal.';
            $redirectUrl = null;

            if ($transaction && $availableSessions) {
                // Use Quota
                foreach ($batchSchedules as $i => $batchItem) {
                    $availableSessions[$i]->update([
                        'scheduled_date' => $batchItem->start_time,
                        'topic' => $batchItem->title,
                        'trainer_id' => $batchItem->trainer_id,
                    ]);
                    \App\Models\Booking::create([
                        'user_id' => $user->id,
                        'schedule_id' => $batchItem->id,
                        'status' => 'confirmed'
                    ]);
                }
                
                $redirectUrl = route('services.my-bookings');
                $statusMsg = 'Berhasil booking kelas menggunakan ' . count($batchSchedules) . ' Kuota Sesi Membership Anda!';
            } else {
                // No quota, need to buy new transaction
                $transaction = ServiceTransaction::create([
                    'user_id' => $user->id,
                    'service_id' => $service->id,
                    'trainer_id' => $schedule->trainer_id, // Base trainer
                    'transaction_date' => now(),
                    'scheduled_date' => $schedule->start_time,
                    'amount' => $service->price,
                    'status' => $service->price > 0 ? 'pending' : 'scheduled',
                    'notes' => $request->notes
                ]);

                // Create sessions
                foreach ($batchSchedules as $num => $batchItem) {
                    \App\Models\ServiceSession::create([
                        'service_transaction_id' => $transaction->id,
                        'session_number' => $num + 1,
                        'status' => 'pending',
                        'scheduled_date' => $service->price == 0 ? $batchItem->start_time : null,
                        'topic' => $batchItem->title,
                        'trainer_id' => $batchItem->trainer_id,
                    ]);

                    \App\Models\Booking::create([
                        'user_id' => $user->id,
                        'schedule_id' => $batchItem->id,
                        'status' => 'confirmed'
                    ]);
                }
                
                $redirectUrl = $service->price > 0
                    ? route('services.payment', $transaction->id)
                    : route('services.booking-success', $transaction->id);
                $statusMsg = $service->price > 0 ? 'Booking ' . count($batchSchedules) . ' Sesi Kelas berhasil! Silakan selesaikan pembayaran.' : 'Booking ' . count($batchSchedules) . ' Sesi Kelas berhasil (Gratis)!';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $statusMsg,
                'redirect' => $redirectUrl
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            \Illuminate\Support\Facades\Log::error('Booking Error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat booking: ' . $e->getMessage()
            ], 500);
        }
    }

    public function claimQuota(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:additional_service_transactions,id',
            'schedule_id' => 'required|exists:schedules,id',
        ]);

        $user = Auth::user();
        $transaction = \App\Models\ServiceTransaction::findOrFail($request->transaction_id);
        $schedule = \App\Models\Schedule::findOrFail($request->schedule_id);

        if ($transaction->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Tidak punya hak akses kuota ini.'], 403);
        }

        $createdAtStart = $schedule->created_at->copy()->startOfMinute();
        $createdAtEnd = $schedule->created_at->copy()->endOfMinute();
        $batchSchedules = \App\Models\Schedule::where('service_id', $schedule->service_id)
            ->whereBetween('created_at', [$createdAtStart, $createdAtEnd])
            ->orderBy('start_time')
            ->get();

        foreach ($batchSchedules as $batchItem) {
            if ($batchItem->bookings()->count() >= $batchItem->capacity) {
                return response()->json(['success' => false, 'message' => 'Maaf, kuota maksimal jadwal kelas ini sudah penuh.']);
            }
        }

        $alreadyBooked = \App\Models\Booking::where('user_id', $user->id)
            ->whereIn('schedule_id', $batchSchedules->pluck('id'))
            ->exists();
        
        if ($alreadyBooked) {
            return response()->json(['success' => false, 'message' => 'Anda sudah terdaftar di kelas ini.']);
        }

        $availableSessions = \App\Models\ServiceSession::where('service_transaction_id', $transaction->id)
            ->where('status', 'pending')
            ->whereNull('scheduled_date')
            ->orderBy('session_number', 'asc')
            ->limit($batchSchedules->count())
            ->get();

        if ($availableSessions->count() < $batchSchedules->count()) {
            return response()->json(['success' => false, 'message' => 'Kuota sesi paket Anda (' . $availableSessions->count() . ' Sesi Tersisa) tidak mencukupi untuk mengikuti rangkaian kelas ini yang membutuhkan ' . $batchSchedules->count() . ' pertemuan.']);
        }

        DB::beginTransaction();
        try {
            foreach ($batchSchedules as $i => $batchItem) {
                $availableSessions[$i]->update([
                    'scheduled_date' => $batchItem->start_time,
                    'topic' => $batchItem->title,
                    'trainer_id' => $batchItem->trainer_id,
                ]);

                \App\Models\Booking::create([
                    'user_id' => $user->id,
                    'schedule_id' => $batchItem->id,
                    'status' => 'confirmed'
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil klaim ' . count($batchSchedules) . ' sesi kuota untuk rangkaian kelas ini!',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Gagal klaim: ' . $e->getMessage()], 500);
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

        // Localhost fallback: if Midtrans redirects with settlement/capture, update the status here
        // to waiting_validation so the staff can review it.
        if ($request->has('transaction_status') && in_array($request->transaction_status, ['settlement', 'capture'])) {
            if ($transaction->status === 'pending') {
                $transaction->status = 'waiting_validation';
                $transaction->save();
            }
        }

        $config = [
            'title' => 'Booking Berhasil',
            'menu' => MenuRepository::generate($request),
        ];

        return view('services.booking-success', compact('config', 'transaction', 'request'));
    }

    public function myBookings(Request $request)
    {
        $user = Auth::user();
        $config = [
            'title' => 'Booking Saya',
            'menu' => MenuRepository::generate($request),
        ];

        $bookings = ServiceTransaction::where('user_id', $user->id)
            ->with(['service', 'serviceSessions'])
            ->orderBy('transaction_date', 'desc')
            ->paginate(15);

        $upcomingBookings = ServiceTransaction::where('user_id', $user->id)
            ->whereIn('status', ['scheduled', 'pending'])
            ->where(function($q) {
                $q->where('scheduled_date', '>', now())
                  ->orWhereNull('scheduled_date');
            })
            ->with(['service', 'trainer', 'serviceSessions'])
            ->orderByRaw('-scheduled_date DESC') // nulls go first or last depending on DB, works fine
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
