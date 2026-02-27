<?php

namespace App\Http\Controllers;

use App\Models\CheckinLog;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // <-- Saran: Tambahkan ini untuk logging
use Carbon\Carbon;
use App\Repository\MenuRepository;

class CheckinController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $activeMembership = Membership::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('package')
            ->first();

        $todayCheckin = CheckinLog::where('user_id', $user->id)
            ->today()
            ->first();

        $recentCheckins = CheckinLog::where('user_id', $user->id)
            ->orderBy('checkin_time', 'desc')
            ->take(10)
            ->get();

        $stats = [
            'total_visits' => CheckinLog::where('user_id', $user->id)->count(),
            'this_month' => CheckinLog::where('user_id', $user->id)->thisMonth()->count(),
            'this_week' => CheckinLog::where('user_id', $user->id)->thisWeek()->count(),
            'today' => CheckinLog::where('user_id', $user->id)->today()->count(),
        ];

        // Format QR Code yang lebih aman dan terstruktur
        $qrData = "BUGAR_SEHAT_" . $user->id;

        $config = [
            'title' => 'Checkin',
            'title-alias' => ' Checkin',
            'menu' => MenuRepository::generate($request),
        ];

        return view('checkin.index', compact(
            'config',
            'user',
            'activeMembership',
            'todayCheckin',
            'recentCheckins',
            'stats',
            'qrData'
        ));
    }

    /**
     * Handles self check-in by a member.
     */
    public function checkin(Request $request)
    {
        return $this->performCheckin(Auth::id(), $request, null);
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();

        $activeCheckin = CheckinLog::where('user_id', $user->id)
            ->whereNull('checkout_time')
            ->orderBy('checkin_time', 'desc')
            ->first();

        if (!$activeCheckin) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada check-in aktif yang ditemukan.'
            ], 400);
        }

        try {
            $activeCheckin->update([
                'checkout_time' => now(),
                'notes' => $activeCheckin->notes . ($request->notes ? ' | Checkout: ' . $request->notes : '')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Checkout berhasil! Terima kasih telah berlatih.',
                'data' => [
                    'checkout_time' => $activeCheckin->checkout_time->format('H:i'),
                    'duration' => $activeCheckin->getFormattedDuration()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Checkout failed: ' . $e->getMessage()); // <-- Saran: Log error
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan checkout: Terjadi kesalahan sistem.'
            ], 500);
        }
    }

    public function qrScan(Request $request)
    {
        $user = Auth::user();
        $activeMembership = Membership::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('package')
            ->first();

        $config = [
            'title' => 'QR Scan',
            'title-alias' => ' QR Scan',
            'menu' => MenuRepository::generate($request),
        ];

        return view('checkin.staff-qr-scanner', compact('user', 'activeMembership', 'config'));
    }

    public function processQrCheckin(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);

        $qrData = $request->qr_code;

        if (strpos($qrData, 'BUGAR_SEHAT_') !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid.'
            ], 400);
        }

        $parts = explode('_', $qrData);
        if (count($parts) < 3) {
            return response()->json([
                'success' => false,
                'message' => 'Format QR Code tidak valid.'
            ], 400);
        }

        $qrUserId = $parts[2];

        $isStaffScanning = Auth::user()->hasRole('User:Staff') || Auth::user()->hasRole('User:Owner');

        if (!$isStaffScanning && $qrUserId != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak sesuai dengan akun Anda.'
            ], 400);
        }

        $targetUserId = $isStaffScanning ? $qrUserId : Auth::id();

        return $this->performCheckin($targetUserId, $request, $isStaffScanning ? Auth::id() : null);
    }

    public function staffScanQr(Request $request)
    {
        if (!Auth::user()->hasRole('User:Staff') && !Auth::user()->hasRole('User:Owner')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk melakukan scan QR member.'
            ], 403);
        }

        $request->validate([
            'qr_code' => 'required|string'
        ]);

        $qrData = $request->qr_code;

        if (strpos($qrData, 'BUGAR_SEHAT_') !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid.'
            ], 400);
        }

        $parts = explode('_', $qrData);
        if (count($parts) < 3) {
            return response()->json([
                'success' => false,
                'message' => 'Format QR Code tidak valid.'
            ], 400);
        }

        $memberId = $parts[2];

        return $this->performCheckin($memberId, $request, Auth::id());
    }

    /**
     * Centralized method to perform a check-in.
     */
    private function performCheckin($userId, $request, $staffId = null)
{
    $user = User::find($userId);

    if (!$user) {
        return response()->json(['success' => false, 'message' => 'Member tidak ditemukan.'], 404);
    }

    $activeMembership = Membership::where('user_id', $user->id)
        ->where('status', 'active')
        ->first();

    if (!$activeMembership) {
        return response()->json(['success' => false, 'message' => 'Member tidak memiliki membership aktif.'], 400);
    }

    if ($activeMembership->isExpired()) {
        return response()->json(['success' => false, 'message' => 'Membership member telah kedaluwarsa.'], 400);
    }

    if ($activeMembership->remaining_visits !== 999 && $activeMembership->remaining_visits <= 0) {
        return response()->json(['success' => false, 'message' => 'Kuota kunjungan member telah habis.'], 400);
    }

    $todayCheckin = CheckinLog::where('user_id', $user->id)
        ->today()
        ->first();

    if ($todayCheckin && is_null($todayCheckin->checkout_time)) {
        return response()->json(['success' => false, 'message' => 'Member sudah melakukan check-in hari ini dan belum checkout.'], 400);
    }
    
    if ($todayCheckin && !is_null($todayCheckin->checkout_time)) {
        return response()->json(['success' => false, 'message' => 'Member sudah menyelesaikan sesi latihan hari ini.'], 400);
    }

    DB::beginTransaction();
    try {
        $checkinLog = CheckinLog::create([
            'user_id' => $user->id,
            'staff_id' => $staffId,
            'membership_id' => $activeMembership->id,
            'checkin_time' => now(),
            'notes' => $request->notes
        ]);

        if ($activeMembership->remaining_visits != 999) {
            $activeMembership->decrement('remaining_visits');
        }

        DB::commit();

        // --- PERBAIKAN DI SINI ---
        // Logika dibuat lebih aman untuk mencegah error jika staff tidak ditemukan
        $checkedInBy = 'Self'; // Nilai default
        if ($staffId) {
            $staffUser = User::find($staffId);
            // Cek apakah staffUser ditemukan sebelum mengambil namanya
            $checkedInBy = $staffUser ? $staffUser->name : 'Staff (Not Found)';
        }
        // --- AKHIR PERBAIKAN ---

        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil! Selamat berlatih.',
            'data' => [
                'member_name' => $user->name,
                'checkin_time' => $checkinLog->checkin_time->format('H:i'),
                'remaining_visits' => $activeMembership->fresh()->remaining_visits,
                'checked_in_by' => $checkedInBy // Menggunakan variabel yang sudah aman
            ]
        ]);
    } catch (\Exception $e) {
        DB::rollback();
        Log::error('Checkin failed for user ' . $userId . ': ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Gagal melakukan check-in: Terjadi kesalahan pada server.'], 500);
    }
}

    public function staffQrScanner(Request $request)
    {
        if (!Auth::user()->hasRole('User:Staff') && !Auth::user()->hasRole('User:Owner')) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk mengakses fitur ini.'], 403);
        }

        if (Auth::user()->hasRole('User:Owner')) {
            return redirect()->route('checkin.owner-scanner');
        }

        $config = [
            'title' => 'Staff QR Scanner',
            'title-alias' => ' QR Scanner',
            'menu' => MenuRepository::generate($request),
        ];

        return view('checkin.staff-qr-scanner', compact('config'));
    }

    public function ownerQrScanner(Request $request)
    {
        if (!Auth::user()->hasRole('User:Owner')) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk mengakses fitur ini.'], 403);
        }

        $config = [
            'title' => 'Owner QR Scanner',
            'title-alias' => ' QR Scanner',
            'menu' => MenuRepository::generate($request),
        ];

        return view('checkin.owner-qr-scanner', compact('config'));
    }

    public function forceCheckout(Request $request, $checkinId)
    {
        if (!Auth::user()->hasRole('User:Owner')) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk melakukan force checkout.'], 403);
        }

        $checkin = CheckinLog::find($checkinId);

        if (!$checkin) {
            return response()->json(['success' => false, 'message' => 'Check-in tidak ditemukan.'], 404);
        }

        if ($checkin->checkout_time) {
            return response()->json(['success' => false, 'message' => 'Member sudah melakukan checkout.'], 400);
        }

        try {
            $checkin->update([
                'checkout_time' => now(),
                'notes' => $checkin->notes . ' | Force checkout oleh owner: ' . ($request->reason ?? 'Tidak ada alasan')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Force checkout berhasil dilakukan.',
                'data' => [
                    'checkout_time' => $checkin->checkout_time->format('H:i'),
                    'duration' => $checkin->getFormattedDuration()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Force checkout failed: ' . $e->getMessage()); // <-- Saran: Log error
            return response()->json(['success' => false, 'message' => 'Gagal melakukan force checkout: Terjadi kesalahan sistem.'], 500);
        }
    }

    public function getRecentCheckins(Request $request)
    {
        $checkins = CheckinLog::today()
            ->with(['user', 'membership.package', 'staff'])
            ->orderBy('checkin_time', 'desc')
            ->get();

        $html = '';
        foreach ($checkins as $log) {
            $html .= '<tr data-type="' . ($log->staff ? 'staff' : 'self') . '" data-status="' . ($log->checkout_time ? 'completed' : 'active') . '">';
            $html .= '<td>' . $log->checkin_time->format('H:i') . '</td>';
            $html .= '<td>';
            $html .= '<div class="d-flex align-items-center">';
            $html .= '<div class="symbol symbol-40px symbol-circle me-3">';
            $html .= '<span class="symbol-label bg-light-primary text-primary">' . substr($log->user->name, 0, 1) . '</span>';
            $html .= '</div>';
            $html .= '<div>';
            $html .= '<div class="fw-bold">' . $log->user->name . '</div>';
            $html .= '<div class="text-muted fs-7">' . $log->user->email . '</div>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</td>';
            $html .= '<td>';
            $html .= '<span class="badge badge-light-primary">' . ($log->membership->package->name ?? '-') . '</span>';
            $html .= '</td>';
            $html .= '<td>';
            if ($log->staff) {
                $html .= '<span class="badge badge-light-success"><i class="fas fa-user-tie me-1"></i>' . $log->staff->name . '</span>';
            } else {
                $html .= '<span class="badge badge-light-info"><i class="fas fa-user me-1"></i>Mandiri</span>';
            }
            $html .= '</td>';
            $html .= '<td>';
            if ($log->checkout_time) {
                $html .= '<span class="badge badge-light-success"><i class="fas fa-check-circle me-1"></i>Selesai</span>';
            } else {
                $html .= '<span class="badge badge-light-warning"><i class="fas fa-clock me-1"></i>Aktif</span>';
            }
            $html .= '</td>';

            if (Auth::user()->hasRole('User:Owner') && !$log->checkout_time) {
                $html .= '<td>';
                $html .= '<button class="btn btn-sm btn-warning" onclick="forceCheckout(' . $log->id . ')">';
                $html .= '<i class="fas fa-sign-out-alt"></i> Force Checkout';
                $html .= '</button>';
                $html .= '</td>';
            } else {
                $html .= '<td><span class="text-muted">-</span></td>';
            }

            $html .= '</tr>';
        }

        return response()->json([
            'success' => true,
            'html' => $html,
            'checkins' => $checkins->map(function ($log) {
                return [
                    'id' => $log->id,
                    'checkout_time' => $log->checkout_time,
                    'staff_id' => $log->staff_id,
                    'user_name' => $log->user->name,
                    'package_name' => $log->membership->package->name ?? '-',
                ];
            })
        ]);
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        $checkins = CheckinLog::where('user_id', $user->id)
            ->with('membership.package')
            ->orderBy('checkin_time', 'desc')
            ->paginate(20);

        $stats = [
            'total_visits' => CheckinLog::where('user_id', $user->id)->count(),
            'this_month' => CheckinLog::where('user_id', $user->id)->thisMonth()->count(),
            'this_week' => CheckinLog::where('user_id', $user->id)->thisWeek()->count(),
            'average_duration' => CheckinLog::where('user_id', $user->id)
                ->whereNotNull('checkout_time')
                ->get()
                ->avg(function ($log) {
                    return $log->getDuration();
                }),
        ];

        $config = [
            'title' => 'Riwayat Checkin',
            'title-alias' => ' Riwayat',
            'menu' => MenuRepository::generate($request),
        ];

        // BENAR
        return view('membership.history', compact('user', 'checkins', 'stats', 'config'));
    }

    public function generateQr(Request $request)
    {
        $user = Auth::user();

        $activeMembership = Membership::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('package')
            ->first();

        if (!$activeMembership) {
            return redirect()->back()->with('error', 'Anda tidak memiliki membership aktif.');
        }

        $manualCode = strtoupper(\Illuminate\Support\Str::random(8));

        DB::table('checkin_codes')->insert([
            'user_id'    => $user->id,
            'code'       => $manualCode,
            'created_at' => now(),
            'expires_at' => now()->addMinutes(1)
        ]);

        $qrData = $manualCode;

        $config = [
            'title' => 'Generate QR',
            'title-alias' => ' QR Code',
            'menu' => \App\Repository\MenuRepository::generate($request),
        ];

        return view('checkin.generate-qr', compact(
            'user',
            'activeMembership',
            'qrData',
            'manualCode',
            'config'
        ));
    }
    
    // Perhatikan, kurung kurawal yang salah sudah dihapus dari sini

    /**
     * Menangani check-in manual oleh staf menggunakan kode teks.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function manualCheckinByStaff(Request $request)
    {
        // 1. Otorisasi: Pastikan pengguna adalah Staf atau Owner
        $staffUser = Auth::user();
        if (!$staffUser->hasRole('User:Staff') && !$staffUser->hasRole('User:Owner')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk melakukan check-in manual.'
            ], 403);
        }

        // 2. Validasi: Pastikan 'manual_code' ada dalam request
        $request->validate([
            'manual_code' => 'required|string|size:8',
        ]);

        $manualCode = strtoupper($request->manual_code);

        // 3. Cari kode di database
        $checkinCode = DB::table('checkin_codes')
            ->where('code', $manualCode)
            ->first();

        // 4. Validasi kode: keberadaan, kedaluwarsa, dan penggunaan
        if (!$checkinCode) {
            return response()->json([
                'success' => false,
                'message' => 'Kode check-in tidak valid atau tidak ditemukan.'
            ], 404);
        }

        // Cek apakah kode sudah kedaluwarsa
        if (Carbon::now()->isAfter($checkinCode->expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode check-in telah kedaluwarsa. Silakan minta member untuk membuat kode baru.'
            ], 400);
        }
        
        // Cek apakah kode sudah pernah digunakan (membutuhkan kolom 'used_at')
        if (!empty($checkinCode->used_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode check-in ini sudah pernah digunakan.'
            ], 400);
        }

        // 5. Proses check-in
        DB::beginTransaction();
        try {
            // Tandai kode sebagai sudah digunakan
            DB::table('checkin_codes')
                ->where('id', $checkinCode->id)
                ->update(['used_at' => now()]);

            // Dapatkan ID member dari kode
            $memberId = $checkinCode->user_id;
            
            // Lakukan commit untuk update 'used_at' sebelum memanggil method lain
            DB::commit();

            // 6. Panggil method check-in terpusat yang sudah ada
            return $this->performCheckin($memberId, $request, $staffUser->id);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Manual checkin failed: ' . $e->getMessage()); // <-- Saran: Log error
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat memproses check-in.'
            ], 500);
        }
    }

} // <-- Ini adalah penutup class CheckinController yang SEBENARNYA