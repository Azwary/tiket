<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use App\Models\Rute;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    private function getRuteYangDiizinkan($role)
    {
        return match ($role) {
            'padang' => [1, 2],
            'solok' => [4],
            'sawah_lunto' => [3],
            'umum' => null, // null artinya semua rute boleh
            default => [],
        };
    }

    public function index(Request $request)
    {
        $admin = auth('admin')->user();
        $ruteDiizinkan = $this->getRuteYangDiizinkan($admin->role);

        $query = Pemesanan::with(['jadwal.rute', 'penumpang', 'pembayaran'])
            ->whereHas('pembayaran', fn($q) => $q->whereIn('status_konfirmasi', ['berhasil', 'ditempat']));

        // Filter rute sesuai role
        if ($ruteDiizinkan !== null) {
            $query->whereHas('jadwal.rute', fn($q) => $q->whereIn('id_rute', $ruteDiizinkan));
        }

        // Filter rute yang dipilih
        if ($request->filled('rute')) {
            $rute = $request->rute;
            if (is_numeric($rute)) {
                $query->whereHas('jadwal.rute', fn($q) => $q->where('id_rute', $rute));
            }
        }

        // Filter rentang tanggal
        if ($request->filled('dari_tanggal') && $request->filled('sampai_tanggal')) {
            $query->whereBetween('tanggal_keberangkatan', [
                $request->dari_tanggal,
                $request->sampai_tanggal
            ]);
        }

        $pemesanans = $query->get();

        // Data rute untuk dropdown
        $rutes = $ruteDiizinkan === null
            ? Rute::all()
            : Rute::whereIn('id_rute', $ruteDiizinkan)->get();

        return view('admin.laporan', compact('pemesanans', 'rutes'));
    }

    public function unduh(Request $request)
    {
        if (!$request->filled('rute') || ($request->rute !== 'semua' && !is_numeric($request->rute))) {
            return back()->with('error', 'Silakan pilih rute terlebih dahulu.');
        }

        $admin = auth('admin')->user();
        $ruteDiizinkan = $this->getRuteYangDiizinkan($admin->role);

        $query = Pemesanan::with(['jadwal.rute', 'penumpang', 'pembayaran'])
            ->whereHas('pembayaran', fn($q) => $q->whereIn('status_konfirmasi', ['berhasil', 'ditempat']));

        if (!is_null($ruteDiizinkan)) {
            $query->whereHas('jadwal.rute', fn($q) => $q->whereIn('id_rute', $ruteDiizinkan));
        }

        $ruteTerpilih = null;
        if (is_numeric($request->rute)) {
            $query->whereHas('jadwal.rute', fn($q) => $q->where('id_rute', $request->rute));
            $ruteTerpilih = Rute::find($request->rute);
        }

        // Filter rentang tanggal
        if ($request->filled('dari_tanggal') && $request->filled('sampai_tanggal')) {
            $query->whereBetween('tanggal_keberangkatan', [
                $request->dari_tanggal,
                $request->sampai_tanggal
            ]);
        }

        $pemesanans = $query->get(); // ganti dari $data ke $pemesanans supaya konsisten

        return Pdf::loadView('admin.pdf', [
            'pemesanans' => $pemesanans,
            'dari_tanggal' => $request->dari_tanggal,
            'sampai_tanggal' => $request->sampai_tanggal,
            'rute_terpilih' => $ruteTerpilih,
        ])->download('laporan.pdf');
    }
}
