<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\{DetailPemesanan, Jadwal, Kursi, Pembayaran, Pemesanan, Penumpang, Rute};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiPetugasController extends Controller
{
    // Ambil semua rute
    public function getRute()
    {
        $rutes = Rute::select('id_rute', 'asal', 'tujuan', 'harga')->get();
        return response()->json([
            'status' => true,
            'message' => 'Data rute berhasil diambil',
            'data' => $rutes
        ]);
    }

    // Ambil jadwal berdasarkan rute
    public function getJam($id_rute)
    {
        $rute = Rute::find($id_rute);
        if (!$rute) {
            return response()->json([
                'status' => false,
                'message' => 'Rute tidak ditemukan',
                'data' => [],
            ], 404);
        }

        $jadwals = Jadwal::with(['supir', 'kendaraan'])
            ->where('id_rute', $id_rute)
            ->get(['id_jadwal', 'jam_keberangkatan', 'id_supir', 'id_kendaraan']);

        return response()->json([
            'status' => true,
            'message' => 'Jadwal berhasil diambil',
            'data' => $jadwals,
        ]);
    }

    // Ambil kursi tersedia per rute, tanggal, dan jadwal
    public function getKursiTersedia(Request $request)
    {
        $id_rute = $request->query('rute');
        $id_jadwal = $request->query('jam');
        $tanggal = $request->query('tanggal');

        // ✅ Validasi parameter wajib
        if (!$id_rute || !$tanggal || !$id_jadwal) {
            return response()->json([
                'status' => false,
                'message' => 'Parameter kurang lengkap',
                'data' => []
            ]);
        }

        // ✅ Pastikan jadwal valid
        $jadwal = Jadwal::find($id_jadwal);
        if (!$jadwal) {
            return response()->json([
                'status' => false,
                'message' => 'Jadwal tidak ditemukan',
                'data' => []
            ]);
        }

        // ✅ Ambil data kursi yang sudah ada pemesanan pada tanggal dan jadwal tersebut
        $kursiTerisi = DB::table('detail_pemesanan')
            ->join('pemesanan', 'detail_pemesanan.id_pemesanan', '=', 'pemesanan.id_pemesanan')
            ->join('pembayaran', 'pemesanan.id_pemesanan', '=', 'pembayaran.id_pemesanan')
            ->where('pemesanan.id_jadwal', $id_jadwal)
            ->where('pemesanan.tanggal_keberangkatan', $tanggal)
            ->select('detail_pemesanan.id_kursi', 'pembayaran.status_konfirmasi')
            ->get();

        // ✅ Ambil semua kursi, tandai statusnya
        $kursiSemua = Kursi::all()->map(function ($k) use ($kursiTerisi) {
            $status = 'kosong'; // default bisa dipesan

            foreach ($kursiTerisi as $terisi) {
                if ($terisi->id_kursi == $k->id_kursi) {
                    // ❌ Hanya status berikut yang membuat kursi tidak bisa dipilih
                    if (in_array($terisi->status_konfirmasi, ['menunggu', 'berhasil', 'ditempat'])) {
                        $status = 'terisi';
                        break; // keluar loop karena kursi ini sudah terisi
                    }
                    // ✅ Jika 'tolak', tetap kosong (bisa dipesan lagi)
                }
            }

            return [
                'id_kursi' => $k->id_kursi,
                'no_kursi' => $k->no_kursi,
                'status' => $status,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Data kursi berhasil diambil',
            'data' => $kursiSemua
        ]);
    }



    // Simpan pemesanan oleh petugas
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_jadwal' => 'required|exists:jadwal,id_jadwal',
            'tanggal' => 'required|date',
            'penumpang' => 'required|array|min:1',
            'penumpang.*.nama' => 'required|string|max:255',
            'penumpang.*.kursi' => 'required|exists:kursi,id_kursi',
        ]);

        DB::beginTransaction();
        try {
            $jadwal = Jadwal::with('rute')->findOrFail($validated['id_jadwal']);
            $harga = $jadwal->rute->harga ?? 0;

            $totalBayar = 0;
            $firstPenumpang = null;
            $penumpangData = []; // 🔹 array baru untuk simpan penumpang + kursi

            foreach ($validated['penumpang'] as $index => $p) {
                $penumpang = Penumpang::create([
                    'nama_penumpang' => $p['nama'],
                ]);

                if ($index === 0) {
                    $firstPenumpang = $penumpang;
                }

                $penumpangData[] = [
                    'id_penumpang' => $penumpang->id,
                    'nama' => $p['nama'],
                    'kursi' => $p['kursi'],
                ];

                $totalBayar += $harga;
            }

            // Buat pemesanan pakai penumpang pertama
            $pemesanan = Pemesanan::create([
                'id_penumpang' => $firstPenumpang->id,
                'id_jadwal' => $jadwal->id_jadwal,
                'tanggal_pemesanan' => now()->format('Y-m-d'),
                'tanggal_keberangkatan' => $validated['tanggal'],
            ]);

            // Simpan detail pemesanan
            foreach ($penumpangData as $p) {
                DetailPemesanan::create([
                    'id_pemesanan' => $pemesanan->id_pemesanan,
                    'id_penumpang' => $p['id_penumpang'],
                    'id_kursi' => $p['kursi'],
                    'nama_penumpang' => $p['nama'],
                ]);
            }

            Pembayaran::create([
                'id_pemesanan' => $pemesanan->id_pemesanan,
                'jumlah_pembayaran' => $totalBayar,
                'batas_waktu_pembayaran' => now(),
                'status_konfirmasi' => 'ditempat',
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Pemesanan berhasil disimpan',
                'data' => [
                    'pemesanan' => $pemesanan,
                    'total_bayar' => $totalBayar,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Pemesanan petugas error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal melakukan pemesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
