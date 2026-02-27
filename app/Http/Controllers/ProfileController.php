<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Membership;
use App\Models\CheckinLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Repository\MenuRepository;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $config = [
            'title' => 'Profil Pengguna',
            'title-alias' => 'Profile',
            'menu' => MenuRepository::generate($request),
        ];

        $activeMembership = Membership::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('package')
            ->first();

        $membershipHistory = Membership::where('user_id', $user->id)
            ->with('package')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentCheckins = CheckinLog::where('user_id', $user->id)
            ->orderBy('checkin_time', 'desc')
            ->take(10)
            ->get();

        $totalVisits = CheckinLog::where('user_id', $user->id)->count();
        $thisMonthVisits = CheckinLog::where('user_id', $user->id)->thisMonth()->count();

        return view('profile.index', compact(
            'config',
            'user',
            'activeMembership',
            'membershipHistory',
            'recentCheckins',
            'totalVisits',
            'thisMonthVisits'
        ));
    }

    public function edit(Request $request)
    {
        $user = Auth::user();
        $config = [
            'title' => 'Edit Profil',
            'title-alias' => 'Ubah Data Diri',
            'menu' => MenuRepository::generate($request),
        ];
        return view('profile.edit', compact('user', 'config'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $userData = $request->only([
            'name', 'email', 'phone', 'address', 'date_of_birth',
            'gender', 'emergency_contact', 'emergency_phone'
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::exists('public/avatars/' . $user->avatar)) {
                Storage::delete('public/avatars/' . $user->avatar);
            }

            $avatarName = time() . '_' . $user->id . '.' . $request->avatar->extension();
            $request->avatar->storeAs('public/avatars', $avatarName);
            $userData['avatar'] = $avatarName;
        }

        $user->update($userData);

        return redirect()->route('profile')
            ->with('success', 'Profile berhasil diperbarui!');
    }

    public function changePassword(Request $request)
    {
        $config = [
            'title' => 'Ubah Password',
            'title-alias' => 'Ganti Kata Sandi',
            'menu' => MenuRepository::generate($request),
        ];

        return view('profile.change-password', compact('config'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('profile.index')
            ->with('success', 'Password berhasil diubah!');
    }

    public function membershipStatus(Request $request)
    {
        $user = Auth::user();
        $config = [
            'title' => 'Status Membership',
            'title-alias' => 'Membership Aktif & Riwayat',
            'menu' => MenuRepository::generate($request),
        ];

        $activeMembership = Membership::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('package')
            ->first();

        $allMemberships = Membership::where('user_id', $user->id)
            ->with('package')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('profile.membership-status', compact('user', 'activeMembership', 'allMemberships', 'config'));
    }

    public function visitHistory(Request $request)
    {
        $user = Auth::user();
        $config = [
            'title' => 'Riwayat Kunjungan',
            'title-alias' => 'Histori Check-in',
            'menu' => MenuRepository::generate($request),
        ];

        $checkins = CheckinLog::where('user_id', $user->id)
            ->with('membership.package')
            ->orderBy('checkin_time', 'desc')
            ->paginate(20);

        $stats = [
            'total_visits' => CheckinLog::where('user_id', $user->id)->count(),
            'this_month' => CheckinLog::where('user_id', $user->id)->thisMonth()->count(),
            'this_week' => CheckinLog::where('user_id', $user->id)->thisWeek()->count(),
            'today' => CheckinLog::where('user_id', $user->id)->today()->count(),
        ];

        return view('profile.visit-history', compact('user', 'checkins', 'stats', 'config'));
    }
}
