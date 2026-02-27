<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfigurationPayment;
use App\Repository\MenuRepository;

class ConfigurationPaymentController extends Controller
{
    public function index(Request $request)
    {
        $config = [
            'title' => 'Konfigurasi Pembayaran',
            'description' => 'Atur metode dan informasi pembayaran yang digunakan pada sistem.',
            'menu' => MenuRepository::generate($request),
            'title-alias' => 'konfigurasi',
        ];

        // Ambil data konfigurasi pertama (jika ada)
        $payment = ConfigurationPayment::first();

        return view('configuration.payment.index', compact('config', 'payment'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'metode_pembayaran' => 'required|string',
            'rekening' => 'required|string',
            'atas_nama' => 'required|string',
        ]);

        // Simpan atau update satu baris data saja
        ConfigurationPayment::updateOrCreate(
            ['id' => 1], // ID tetap agar hanya satu konfigurasi yang tersimpan
            $validated
        );

        return back()->with('success', 'Konfigurasi pembayaran berhasil diperbarui!');
    }
}
