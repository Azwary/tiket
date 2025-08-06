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

class ApiPenumpangController extends Controller
{
    // public function index()
    // {
    //     $penumpang = Penumpang::all(); 

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Data penumpang berhasil diambil',
    //         'data' => $penumpang
    //     ]);
    // }
    public function show($id)
    {
        $penumpang = Penumpang::where('id', $id)->first();

        if (!$penumpang) {
            return response()->json([
                'status' => false,
                'message' => 'Penumpang tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Data penumpang berhasil diambil',
            'data' => $penumpang
        ]);
    }
    
    public function rute()
    {
        $rutes = Rute::select('id_rute', 'asal', 'tujuan','harga')->get();

        return response()->json([
            'status' => true,
            'data' => $rutes
        ]);
    }
    public function getJam($id_rute, Request $request)
    {
        $tanggal = $request->query('tanggal'); // ?tanggal=2025-08-01

        $jadwal = Jadwal::where('id_rute', $id_rute)
            ->when($tanggal, function ($query) use ($tanggal) {
                $query->whereHas('pemesanans', function ($q) use ($tanggal) {
                    $q->where('tanggal_keberangkatan', $tanggal);
                });
            })
            ->select('id_jadwal', 'jam_keberangkatan')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $jadwal
        ]);
    }
    public function showKursi(Request $request)
    {
        $id_rute = $request->input('rute');
        $tanggal = $request->input('tanggal');
        $id_jadwal = $request->input('jam'); // asumsi frontend mengirimkan key 'jam'

        // Validasi input
        if (!$id_rute || !$tanggal || !$id_jadwal) {
            return response()->json([
                'status' => false,
                'message' => 'Parameter rute, tanggal, dan jam wajib diisi.'
            ], 400);
        }

        // Ambil data kursi yang sudah dipesan
        $kursi_terisi = DB::table('detail_pemesanan')
            ->join('pemesanan', 'detail_pemesanan.id_pemesanan', '=', 'pemesanan.id_pemesanan')
            ->join('jadwal', 'pemesanan.id_jadwal', '=', 'jadwal.id_jadwal')
            ->where('pemesanan.tanggal_keberangkatan', $tanggal)
            ->where('pemesanan.id_jadwal', $id_jadwal)
            ->where('jadwal.id_rute', $id_rute)
            ->pluck('id_kursi')
            ->toArray();

        // Ambil semua kursi lalu beri status
        $kursis = Kursi::all()->map(function ($kursi) use ($kursi_terisi) {
            return [
                'id_kursi' => $kursi->id_kursi,
                'no_kursi' => $kursi->no_kursi,
                'nama_kursi' => $kursi->nama_kursi,
                'status' => in_array($kursi->id_kursi, $kursi_terisi) ? 'terisi' : 'kosong'
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $kursis
        ]);
    }
    public function pesananterkhir()
    {
        $terakhir = Pemesanan::latest('id_pemesanan')->first();

        if ($terakhir) {
            return response()->json([
                'status' => true,
                'message' => 'Data pemesanan terakhir ditemukan',
                'id_pemesanan' => $terakhir->id_pemesanan,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Belum ada data pemesanan',
            ]);
        }
    }
    public function detailpesananterkhir()
    {
        $terakhir = DetailPemesanan::latest('id')->first();

        if ($terakhir) {
            return response()->json([
                'status' => true,
                'message' => 'Data pemesanan terakhir ditemukan',
                'id' => $terakhir->id,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Belum ada data pemesanan',
            ]);
        }
    }
    public function getJadwal(Request $request)
    {
        $request->validate([
            'rute' => 'required|integer',
            'jam' => 'required|string',
        ]);

        $jadwal = Jadwal::where('id_rute', $request->rute)
            ->where('id_jadwal', $request->jam)
            ->first();

        if ($jadwal) {
            return response()->json([
                'status' => true,
                'message' => 'Data jadwal ditemukan',
                'data' => $jadwal,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data jadwal tidak ditemukan',
            ]);
        }
    }

    //belum
    public function uploadBukti(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_pemesanan' => 'required|exists:pembayaran,id_pemesanan',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Ambil data pembayaran berdasarkan id_pemesanan
        $pembayaran = Pembayaran::where('id_pemesanan', $request->id_pemesanan)->first();

        if (!$pembayaran) {
            return response()->json([
                'status' => false,
                'message' => 'Data pembayaran tidak ditemukan'
            ], 404);
        }

        // Cek relasi dengan pemesanan dan id_penumpang
        if (!$pembayaran->pemesanan || !$pembayaran->pemesanan->id_penumpang) {
            return response()->json([
                'status' => false,
                'message' => 'Relasi pemesanan tidak valid atau id_penumpang tidak ditemukan'
            ], 422);
        }

        $penumpangId = $pembayaran->pemesanan->id_penumpang;
        $file = $request->file('file');
        $ext = $file->getClientOriginalExtension();

        // Buat nama file berdasarkan id_penumpang
        $filename = $penumpangId . '.' . $ext;
        $storagePath = 'public/bukti/' . $filename;
        $dbPath = 'storage/bukti/' . $filename;

        // Hapus file lama jika ada
        if (Storage::exists($storagePath)) {
            Storage::delete($storagePath);
        }

        // Simpan file baru
        $file->storeAs('public/bukti', $filename);

        // Update data pembayaran
        $pembayaran->update([
            'upload_bukti' => $dbPath,
            'status_konfirmasi' => 'menunggu',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Bukti pembayaran berhasil diupload',
            'data' => $pembayaran
        ]);
    }

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

            // 1. Simpan penumpang baru
            $penumpang = Penumpang::create([
                'nama_penumpang' => $validated['nama'],
            ]);

            // 2. Ambil jadwal & harga
            $jadwal = Jadwal::with('rute')->findOrFail($validated['id_jadwal']);
            $harga = $jadwal->rute->harga ?? 0;

            // 3. Simpan pemesanan
            $pemesanan = Pemesanan::create([
                'id_penumpang' => $penumpang->id_penumpang,
                'id_jadwal' => $jadwal->id_jadwal,
                'tanggal_pemesanan' => now()->format('Y-m-d'),
                'tanggal_keberangkatan' => $validated['tanggal'],
            ]);

            // 4. Simpan semua detail pemesanan kursi
            foreach ($validated['kursi'] as $id_kursi) {
                DetailPemesanan::create([
                    'id_pemesanan' => $pemesanan->id_pemesanan,
                    'id_penumpang' => $penumpang->id_penumpang,
                    'id_kursi' => $id_kursi,
                ]);
            }

            // 5. Hitung total harga
            $total_pembayaran = $harga * count($validated['kursi']);

            // 6. Simpan data pembayaran
            Pembayaran::create([
                'id_pemesanan' => $pemesanan->id_pemesanan,
                'jumlah_pembayaran' => $total_pembayaran,
                'batas_waktu_pembayaran' => now()->addHours(2),
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
