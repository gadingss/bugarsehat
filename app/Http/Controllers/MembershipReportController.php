<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\User;
use Illuminate\Http\Request;
use App\Exports\MembershipsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Repository\MenuRepository;
use Carbon\Carbon;
use PDF;
use Maatwebsite\Excel\Excel as ExcelTypes; // <-- Tambahkan ini

class MembershipReportController extends Controller
{
    /**
     * Method pribadi untuk menampung semua logika filter agar tidak duplikat.
     */
    private function getFilteredQuery(Request $request)
    {
        $query = Membership::with(['user', 'package']);

        if ($request->filled('user')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user . '%')
                  ->orWhere('email', 'like', '%' . $request->user . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->start_date));
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->end_date));
        }

        return $query;
    }

    /**
     * Menampilkan halaman utama laporan membership.
     */
    public function index(Request $request)
    {
        $config = [
            'title' => 'Laporan Membership',
            'title-alias' => 'Membership',
            'menu' => MenuRepository::generate($request),
        ];

        $query = $this->getFilteredQuery($request);
        $data = $query->latest()->paginate(10);

        return view('membership_report.index', compact('config', 'data'));
    }

    /**
     * Menangani permintaan ekspor data ke format PDF atau Excel.
     */
    public function export(Request $request)
    {
        $memberships = $this->getFilteredQuery($request)->latest()->get();
        $format = $request->query('format');

        // --- BLOK YANG DIPERBAIKI ---
        if ($format == 'excel') {
            // 1. Gunakan ekstensi .xlsx yang benar
            $fileName = 'Laporan-Membership-' . now()->format('d-m-Y_H-i-s') . '.xlsx';
            
            // 2. Berikan tipe file secara eksplisit (lebih aman)
            return Excel::download(new MembershipsExport($memberships), $fileName, ExcelTypes::XLSX);
        }

        if ($format == 'pdf') {
            $fileName = 'Laporan-Membership-' . now()->format('d-m-Y_H-i-s') . '.pdf';
            $pdf = PDF::loadView('exports.memberships_pdf', ['memberships' => $memberships]);
            $pdf->setPaper('a4', 'landscape');
            return $pdf->download($fileName);
        }
        // --- AKHIR BLOK PERBAIKAN ---

        return redirect()->back()->with('error', 'Format ekspor tidak didukung.');
    }

    // --- Sisa method lainnya tidak perlu diubah ---

    public function dashboard(Request $request)
    {
        $config = [
            'title' => 'Dashboard Membership',
            'title-alias' => 'Membership',
            'menu' => MenuRepository::generate($request),
        ];

        $today = Carbon::today();
        $monthStart = $today->copy()->startOfMonth();

        $membershipsToday = Membership::whereDate('created_at', $today)->count();
        $membershipsThisMonth = Membership::whereBetween('created_at', [$monthStart, $today])->count();
        $totalMemberships = Membership::count();

        $monthlyData = Membership::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        $latest = Membership::with('user')->latest()->take(10)->get();

        return view('membership_report.dashboard', compact(
            'config', 'membershipsToday', 'membershipsThisMonth',
            'totalMemberships', 'monthlyData', 'latest'
        ));
    }

    public function create(Request $request)
    {
        $config = [
            'title' => 'Tambah Membership',
            'title-alias' => 'Membership',
            'menu' => MenuRepository::generate($request),
        ];

        return view('membership_report.create', [
            'config' => $config,
            'users' => User::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,expired'
        ]);

        Membership::create($request->all());

        return redirect()->route('membership_report.index')->with('success', 'Membership created.');
    }

    public function show(Request $request, Membership $membership)
    {
        $config = [
            'title' => 'Detail Membership',
            'title-alias' => 'Membership',
            'menu' => MenuRepository::generate($request),
        ];

        return view('membership_report.show', compact('config', 'membership'));
    }

    public function edit(Request $request, Membership $membership)
    {
        $config = [
            'title' => 'Edit Membership',
            'title-alias' => 'Membership',
            'menu' => MenuRepository::generate($request),
        ];

        return view('membership_report.edit', [
            'config' => $config,
            'membership' => $membership,
            'users' => User::all()
        ]);
    }

    public function update(Request $request, Membership $membership)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,expired'
        ]);

        $membership->update($request->all());

        return redirect()->route('membership_report.index')->with('success', 'Membership updated.');
    }

    public function destroy(Request $request, Membership $membership)
    {
        $membership->delete();

        return redirect()->route('membership_report.index')->with('success', 'Membership deleted.');
    }
}
