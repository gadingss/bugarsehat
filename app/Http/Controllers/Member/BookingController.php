<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repository\MenuRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Schedule;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $config = [
            'title' => 'Booking Saya',
            'title-alias' => 'Kelas Saya',
            'menu' => MenuRepository::generate($request),
        ];

        $bookings = Booking::with('schedule.trainer')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('member.booking.index', compact('config', 'bookings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|integer',
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);
        
        // Validasi kapasitas dan apakah sudah booking
        if ($schedule->bookings()->count() >= $schedule->capacity) {
            return back()->with('error', 'Kelas penuh.');
        }

        Booking::create([
            'user_id' => Auth::id(),
            'schedule_id' => $schedule->id,
            'status' => 'confirmed'
        ]);

        return redirect()->route('member.booking.index')->with('success', 'Berhasil booking kelas!');
    }

    public function destroy($id)
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($id);
        $booking->delete();

        return redirect()->back()->with('success', 'Booking berhasil dibatalkan.');
    }
}
