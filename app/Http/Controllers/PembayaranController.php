<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PembayaranController extends Controller
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

        $query = Pembayaran::with(['pemesanan.penumpang', 'pemesanan.jadwal.rute'])
            ->where('status_konfirmasi', '!=', 'ditempat');

        if ($request->filled('status')) {
            $query->where('status_konfirmasi', $request->status);
        }

        if ($request->filled('tanggal')) {
            $query->whereHas('pemesanan', function ($q) use ($request) {
                $q->whereDate('tanggal_pemesanan', $request->tanggal);
            });
        }

        // Filter berdasarkan rute yang diizinkan
        if ($ruteDiizinkan !== null) {
            $query->whereHas('pemesanan.jadwal.rute', function ($q) use ($ruteDiizinkan) {
                $q->whereIn('id_rute', $ruteDiizinkan);
            });
        }

        $pembayaran = $query->get();

        return view('admin.pembayaran', compact('pembayaran'));
    }

    public function show($id)
    {
        $data = Pembayaran::with(['pemesanan.penumpang', 'pemesanan.jadwal.rute'])->findOrFail($id);

        // Debug sementara
        if (!$data->pemesanan) {
            dd('Pemesanan tidak ditemukan untuk id_pembayaran: ' . $id);
        }

        // Opsional: Cek apakah user boleh lihat data ini berdasarkan rute
        $admin = auth('admin')->user();
        $ruteDiizinkan = $this->getRuteYangDiizinkan($admin->role);
        if ($ruteDiizinkan !== null && !in_array($data->pemesanan->jadwal->rute->id_rute, $ruteDiizinkan)) {
            abort(403, 'Akses ditolak.');
        }

        return view('admin.konfirmasi_pembayaran', compact('data'));
    }

    public function konfirmasi(Request $request, $id)
    {
        $pembayaran = Pembayaran::with('pemesanan.jadwal.rute')->findOrFail($id);

        // Validasi akses konfirmasi
        $admin = auth('admin')->user();
        $ruteDiizinkan = $this->getRuteYangDiizinkan($admin->role);
        if ($ruteDiizinkan !== null && !in_array($pembayaran->pemesanan->jadwal->rute->id_rute, $ruteDiizinkan)) {
            abort(403, 'Akses ditolak.');
        }

        if ($request->action == 'berhasil') {
            $pembayaran->status_konfirmasi = 'berhasil';
        } elseif ($request->action == 'ditolak') {
            $pembayaran->status_konfirmasi = 'ditolak';
        }

        $pembayaran->save();

        return redirect()->route('pembayaran.index')->with('success', 'Status pembayaran diperbarui.');
    }
}
