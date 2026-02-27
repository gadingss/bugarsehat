<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CheckinLog; // Pastikan nama model sudah benar
use App\Repository\MenuRepository; // Tambahkan ini
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan Anda menggunakan facade yang benar untuk PDF
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CheckinExport;

class CheckinReportController extends Controller
{
    /**
     * Menampilkan halaman laporan check-in dengan data dan menu sidebar.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request) // Tambahkan Request $request
    {
        // Konfigurasi untuk judul halaman dan menu sidebar
        $config = [
            'title' => 'Laporan Check-in',
            'title-alias' => 'Laporan Kunjungan Member',
            'menu' => MenuRepository::generate($request),
        ];

        // Mengambil data check-in dari database
        $checkins = CheckinLog::with(['user', 'membership.package', 'staff'])
                              ->latest('checkin_time')
                              ->get();
                              
        // Mengirim data check-in dan konfigurasi ke view
        return view('checkin_report.index', compact('checkins', 'config'));
    }

    /**
     * Mengekspor data laporan ke format PDF.
     *
     * @return \Illuminate\Http\Response
     */
    public function exportPdf()
    {
        $checkins = CheckinLog::with(['user', 'membership.package', 'staff'])
                              ->orderBy('checkin_time', 'desc')
                              ->get();
        
        // Pastikan view 'checkin_report.export_pdf' sudah ada
        $pdf = Pdf::loadView('checkin_report.export_pdf', compact('checkins'));
        return $pdf->download('laporan-check-in.pdf');
    }

    /**
     * Mengekspor data laporan ke format Excel.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel()
    {
        // Pastikan class 'App\Exports\CheckinExport' sudah ada dan benar
        return Excel::download(new CheckinExport, 'laporan-check-in.xlsx');
    }
}