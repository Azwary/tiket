<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    // public function index()
    // {
    //     // 1. Statistik umum
    //     $totalTiketTerjual = Pemesanan::whereHas('pembayaran', function ($q) {
    //         $q->whereIn('status_konfirmasi', ['berhasil', 'ditempat']);
    //     })->count();


    //     // $totalTransaksi = Pembayaran::count();
    //     $totalTransaksi = Pembayaran::whereIn('status_konfirmasi', ['ditempat', 'berhasil'])
    //         ->sum('jumlah_pembayaran');

    //     $menungguKonfirmasi = Pembayaran::where('status_konfirmasi', 'menunggu')->count();

    //     $jumlahBerhasil = Pembayaran::whereIn('status_konfirmasi', ['berhasil', 'ditempat'])->count();
    //     $jumlahDitolak = Pembayaran::where('status_konfirmasi', 'ditolak')->count();
    //     $jumlahMenunggu = $menungguKonfirmasi;

    //     // 2. Statistik tiket terjual per rute (chart)
    //     $labelsRute = [];
    //     $jumlahTiketRute = [];

    //     $dataRute = Jadwal::with(['rute', 'pemesanan.pembayaran'])
    //         ->get()
    //         ->groupBy(function ($jadwal) {
    //             return $jadwal->rute ? $jadwal->rute->asal . ' - ' . $jadwal->rute->tujuan : 'Rute Tidak Diketahui';
    //         })
    //         ->map(function ($jadwals) {
    //             return $jadwals->flatMap(function ($jadwal) {
    //                 return $jadwal->pemesanan;
    //             })->filter(function ($pemesanan) {
    //                 return $pemesanan->pembayaran
    //                     && in_array($pemesanan->pembayaran->status_konfirmasi, ['berhasil', 'ditempat']);
    //             })->count();
    //         });

    //     foreach ($dataRute as $label => $jumlah) {
    //         $labelsRute[] = $label;
    //         $jumlahTiketRute[] = $jumlah;
    //     }

    //     // 3. Kirim data ke view

    //     return view('admin.dashboard', compact(
    //         'totalTiketTerjual',
    //         'totalTransaksi',
    //         'menungguKonfirmasi',
    //         'labelsRute',
    //         'jumlahTiketRute',
    //         'jumlahBerhasil',
    //         'jumlahMenunggu',
    //         'jumlahDitolak'
    //     ));
    // }
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $ruteDiizinkan = $this->getRuteYangDiizinkan($admin->role);

        // 1. Statistik umum
        $totalTiketTerjual = Pemesanan::whereHas('pembayaran', function ($q) {
            $q->whereIn('status_konfirmasi', ['berhasil', 'ditempat']);
        })->whereHas('jadwal', function ($q) use ($ruteDiizinkan) {
            if ($ruteDiizinkan !== null) {
                $q->whereIn('id_rute', $ruteDiizinkan);
            }
        })->count();
        $totalTransaksi = Pembayaran::whereIn('status_konfirmasi', ['ditempat', 'berhasil'])
            ->whereHas('pemesanan.jadwal', function ($q) use ($ruteDiizinkan) {
                if ($ruteDiizinkan !== null) {
                    $q->whereIn('id_rute', $ruteDiizinkan);
                }
            })->sum('jumlah_pembayaran');

        $menungguKonfirmasi = Pembayaran::where('status_konfirmasi', 'menunggu')
            ->whereHas('pemesanan.jadwal', function ($q) use ($ruteDiizinkan) {
                if ($ruteDiizinkan !== null) {
                    $q->whereIn('id_rute', $ruteDiizinkan);
                }
            })->count();

        $jumlahBerhasil = Pembayaran::whereIn('status_konfirmasi', ['berhasil', 'ditempat'])
            ->whereHas('pemesanan.jadwal', function ($q) use ($ruteDiizinkan) {
                if ($ruteDiizinkan !== null) {
                    $q->whereIn('id_rute', $ruteDiizinkan);
                }
            })->count();

        $jumlahDitolak = Pembayaran::where('status_konfirmasi', 'ditolak')
            ->whereHas('pemesanan.jadwal', function ($q) use ($ruteDiizinkan) {
                if ($ruteDiizinkan !== null) {
                    $q->whereIn('id_rute', $ruteDiizinkan);
                }
            })->count();

        $jumlahMenunggu = $menungguKonfirmasi;

        // 2. Statistik tiket terjual per rute
        $labelsRute = [];
        $jumlahTiketRute = [];

        $dataRute = Jadwal::with(['rute', 'pemesanan.pembayaran'])
            ->when($ruteDiizinkan !== null, function ($q) use ($ruteDiizinkan) {
                $q->whereIn('id_rute', $ruteDiizinkan);
            })
            ->get()
            ->groupBy(function ($jadwal) {
                return $jadwal->rute ? $jadwal->rute->asal . ' - ' . $jadwal->rute->tujuan : 'Rute Tidak Diketahui';
            })
            ->map(function ($jadwals) {
                return $jadwals->flatMap(function ($jadwal) {
                    return $jadwal->pemesanan;
                })->filter(function ($pemesanan) {
                    return $pemesanan->pembayaran
                        && in_array($pemesanan->pembayaran->status_konfirmasi, ['berhasil', 'ditempat']);
                })->count();
            });

        foreach ($dataRute as $label => $jumlah) {
            $labelsRute[] = $label;
            $jumlahTiketRute[] = $jumlah;
        }

        return view('admin.dashboard', compact(
            'totalTiketTerjual',
            'totalTransaksi',
            'menungguKonfirmasi',
            'labelsRute',
            'jumlahTiketRute',
            'jumlahBerhasil',
            'jumlahMenunggu',
            'jumlahDitolak'
        ));
    }

    private function getRuteYangDiizinkan($role)
    {
        return match ($role) {
            'padang' => [1, 2],
            'solok' => [4],
            'sawah_lunto' => [3],
            'umum' => null, // null artinya semua rute
            default => [],
        };
    }
}
