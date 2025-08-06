<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DetailPemesanan;
use App\Models\Jadwal;
use App\Models\Kursi;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use App\Models\Penumpang;
use App\Models\Rute;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class ApiPetugasController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'id_jadwal' => 'required|exists:jadwal,id_jadwal',
            'tanggal' => 'required|date',
            'kursi' => 'required|array|min:1',
            'kursi.*' => 'exists:kursi,id_kursi',
        ]);

        try {
            DB::beginTransaction();

            // 1. Simpan penumpang
            $penumpang = Penumpang::create([
                'nama_penumpang' => $validated['nama'],
            ]);

            // 2. Ambil jadwal & harga
            $jadwal = Jadwal::with('rute')->findOrFail($validated['id_jadwal']);
            $harga = $jadwal->rute->harga ?? 0;

            // 3. Simpan pemesanan
            $pemesanan = Pemesanan::create([
                'id_penumpang' => $penumpang->id,
                'id_jadwal' => $jadwal->id_jadwal,
                'tanggal_pemesanan' => now()->format('Y-m-d'),
                'tanggal_keberangkatan' => $validated['tanggal'],
            ]);

            // 4. Simpan semua detail pemesanan kursi
            foreach ($validated['kursi'] as $id_kursi) {
                DetailPemesanan::create([
                    'id_pemesanan' => $pemesanan->id_pemesanan,
                    'id_penumpang' => $penumpang->id,
                    'id_kursi' => $id_kursi,
                ]);
            }

            // 5. Hitung total harga
            $total_pembayaran = $harga * count($validated['kursi']);

            // 6. Simpan data pembayaran
            Pembayaran::create([
                'id_pemesanan' => $pemesanan->id_pemesanan,
                'jumlah_pembayaran' => $total_pembayaran,
                'batas_waktu_pembayaran' => now(),
                'status_konfirmasi' => 'menunggu',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pemesanan berhasil disimpan',
                'data' => [
                    'penumpang' => $penumpang,
                    'pemesanan' => $pemesanan,
                    'jumlah_kursi' => count($validated['kursi']),
                    'total_bayar' => $total_pembayaran,
                ],
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan pemesanan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
