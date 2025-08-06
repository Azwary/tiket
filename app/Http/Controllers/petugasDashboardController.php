<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class petugasDashboardController extends Controller
{
        public function index()
    {
        // Contoh: ambil total tiket terjual dari tabel `tiket`
        $totalTiketTerjual = 12;
        $totalTransaksi = 2;
        $menungguKonfirmasi = 121;
        // $totalTiketTerjual = DB::table('tiket')->where('status', 'terjual')->count();
        // $totalTransaksi = DB::table('transaksi')->count();
        // $menungguKonfirmasi = DB::table('pembayaran')->where('status', 'menunggu')->count();

        return view('admin.dashboard', compact('totalTiketTerjual', 'totalTransaksi', 'menungguKonfirmasi'));
    }
}
