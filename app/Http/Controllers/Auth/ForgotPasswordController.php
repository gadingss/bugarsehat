<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    /**
     * Show the form to request an OTP.
     */
    public function showLinkRequestForm()
    {
        $config = ['title' => 'Forgot Password'];
        return view('authentication.forgot-password', compact('config'));
    }

    /**
     * Send the OTP to the user's WhatsApp.
     */
    public function sendOtp(Request $request)
    {
        $request->validate(['phone' => 'required|string|exists:users,phone']);

        $phone = $request->phone;

        // Rate limiting: 3 requests per 5 minutes per phone
        if (RateLimiter::tooManyAttempts('send-otp:' . $phone, 3)) {
            $seconds = RateLimiter::availableIn('send-otp:' . $phone);
            return back()->withErrors(['phone' => "Terlalu banyak permintaan. Silakan coba lagi dalam $seconds detik."]);
        }

        RateLimiter::hit('send-otp:' . $phone, 300); // 300 seconds = 5 minutes

        // Generate 6-digit OTP
        $otp = sprintf("%06d", mt_rand(1, 999999));
        $hashedOtp = Hash::make($otp);
        $expiresAt = Carbon::now()->addMinutes(5); // Ubah ke 5 menit

        // Save to database
        DB::table('password_otps')->updateOrInsert(
            ['phone' => $phone],
            [
                'otp' => $hashedOtp,
                'expires_at' => $expiresAt,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Send WhatsApp via Fonnte
        $message = "Kode OTP Reset Password Anda adalah: *$otp*. Berlaku selama 5 menit. Jangan berikan kode ini kepada siapapun.";
        $sent = FonnteService::sendMessage($phone, $message);

        if (!$sent) {
            return back()->withErrors(['phone' => 'Gagal mengirim WhatsApp. Silakan periksa koneksi atau nomor Anda.']);
        }

        return redirect()->route('password.otp.verify', ['phone' => $phone])
            ->with('status', 'Kode OTP telah dikirim ke WhatsApp Anda.');
    }

    /**
     * Show the form to verify the OTP.
     */
    public function showOtpVerifyForm(Request $request)
    {
        $phone = $request->phone;
        if (!$phone) {
            return redirect()->route('password.request');
        }
        $config = ['title' => 'Verify OTP'];
        return view('authentication.verify-otp', compact('phone', 'config'));
    }

    /**
     * Verify the OTP.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|exists:users,phone',
            'otp' => 'required|string|size:6',
        ]);

        $otpRecord = DB::table('password_otps')->where('phone', $request->phone)->first();

        if (!$otpRecord || !Hash::check($request->otp, $otpRecord->otp)) {
            return back()->withErrors(['otp' => 'Kode OTP salah atau tidak ditemukan.']);
        }

        if (Carbon::parse($otpRecord->expires_at)->isPast()) {
            return back()->withErrors(['otp' => 'Kode OTP telah kadaluarsa. Silakan minta kode baru.']);
        }

        // Store verification in session
        session(['otp_verified_phone' => $request->phone]);

        return redirect()->route('password.reset.form', ['phone' => $request->phone])
            ->with('status', 'OTP berhasil diverifikasi. Silakan masukkan password baru Anda.');
    }

    /**
     * Show the form to reset the password.
     */
    public function showResetPasswordForm(Request $request)
    {
        $phone = $request->phone;
        if (!$phone || session('otp_verified_phone') !== $phone) {
            return redirect()->route('password.request')->withErrors(['phone' => 'Sesi verifikasi tidak ditemukan.']);
        }

        $config = ['title' => 'Reset Password'];
        return view('authentication.reset-password', compact('phone', 'config'));
    }

    /**
     * Reset the user's password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|exists:users,phone',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (session('otp_verified_phone') !== $request->phone) {
            return redirect()->route('password.request')->withErrors(['phone' => 'Sesi tidak valid.']);
        }

        // Update User Password
        $user = User::where('phone', $request->phone)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Clear OTP and Session
        DB::table('password_otps')->where('phone', $request->phone)->delete();
        session()->forget('otp_verified_phone');

        return redirect()->route('login')->with('success', 'Password Anda telah berhasil direset. Silakan login.');
    }
}
