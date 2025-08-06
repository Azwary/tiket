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

    $query = Pemesanan::with(['jadwal.rute']);

    // Filter rute sesuai role
    if ($ruteDiizinkan !== null) {
        $query->whereHas('jadwal.rute', fn($q) => $q->whereIn('id_rute', $ruteDiizinkan));
    }

    $pemesanans = collect(); // Default: kosong

    // Cek jika rute dipilih
    if ($request->filled('rute')) {
        $rute = $request->rute;

        // Jika rute == 'semua' → tampilkan semua rute (sudah difilter berdasarkan role di atas)
        if ($rute === 'semua') {
            // Tetap gunakan query yang sudah difilter berdasarkan role
            if ($request->filled('bulan')) {
                $query->whereMonth('tanggal_keberangkatan', date('m', strtotime($request->bulan)))
                      ->whereYear('tanggal_keberangkatan', date('Y', strtotime($request->bulan)));
            }

            $pemesanans = $query->get();

        // Jika rute berupa ID numerik
        } elseif (is_numeric($rute)) {
            $query->whereHas('jadwal.rute', fn($q) => $q->where('id_rute', $rute));

            if ($request->filled('bulan')) {
                $query->whereMonth('tanggal_keberangkatan', date('m', strtotime($request->bulan)))
                      ->whereYear('tanggal_keberangkatan', date('Y', strtotime($request->bulan)));
            }

            $pemesanans = $query->get();
        }

        // Jika rute tidak valid → biarkan kosong
    }

    // Jika rute tidak dipilih sama sekali → tidak tampilkan data (tetap $pemesanans = collect())

    // Rute yang ditampilkan tergantung role
    $rutes = $ruteDiizinkan === null
        ? Rute::all()
        : Rute::whereIn('id_rute', $ruteDiizinkan)->get();

    return view('admin.laporan', compact('pemesanans', 'rutes'));
}



    public function unduh(Request $request)
{
    // Validasi: jika rute tidak diisi atau bukan "semua" atau ID rute valid
    if (!$request->filled('rute') || ($request->rute !== 'semua' && !is_numeric($request->rute))) {
        return back()->with('error', 'Silakan pilih rute terlebih dahulu.');
    }

    $admin = auth('admin')->user();
    $ruteDiizinkan = $this->getRuteYangDiizinkan($admin->role);

    $query = Pemesanan::with(['jadwal.rute', 'penumpang', 'pembayaran']);

    // Filter rute sesuai role
    if (!is_null($ruteDiizinkan)) {
        $query->whereHas('jadwal.rute', function ($q) use ($ruteDiizinkan) {
            $q->whereIn('id_rute', $ruteDiizinkan);
        });
    }

    $ruteTerpilih = null;

    // Jika rute numerik (id)
    if (is_numeric($request->rute)) {
        $query->whereHas('jadwal.rute', function ($q) use ($request) {
            $q->where('id_rute', $request->rute);
        });

        $ruteTerpilih = Rute::find($request->rute);
    }

    // Jika rute == 'semua' maka tidak perlu filter id_rute
    // Tapi tetap lanjut ke filter bulan

    // Filter berdasarkan bulan
    $bulan = $request->bulan;
    if (!empty($bulan)) {
        try {
            $carbonBulan = \Carbon\Carbon::parse($bulan);
            $query->whereMonth('tanggal_pemesanan', $carbonBulan->month)
                ->whereYear('tanggal_pemesanan', $carbonBulan->year);
        } catch (\Exception $e) {
            // log error atau abaikan filter jika gagal parsing
        }
    }

    $data = $query->get();

    return Pdf::loadView('admin.pdf', [
        'data' => $data,
        'bulan' => $bulan,
        'rute_terpilih' => $ruteTerpilih,
    ])->download('laporan.pdf');
}

}
