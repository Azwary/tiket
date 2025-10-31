<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\{DetailPemesanan, Jadwal, Kursi, Pembayaran, Pemesanan, Penumpang, Rute};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ApiPenumpangController extends Controller
{
    // Ambil data penumpang
    public function show($id)
    {
        $penumpang = Penumpang::find($id);
        if (!$penumpang) {
            return response()->json(['status' => false, 'message' => 'Penumpang tidak ditemukan'], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Data penumpang berhasil diambil',
            'data' => $penumpang
        ]);
    }

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

    // Ambil jadwal berdasarkan rute dan tanggal
    public function getJam(Request $request)
    {
        $id_rute = $request->query('rute');
        $tanggal = $request->query('tanggal'); // format YYYY-MM-DD

        if (!$id_rute || !$tanggal) {
            return response()->json([
                'status' => true,
                'message' => 'Data jadwal tidak ditemukan',
                'data' => []
            ]);
        }

        // Ambil jadwal dengan relasi supir & kendaraan
        $jadwals = Jadwal::with(['supir', 'kendaraan'])
            ->where('id_rute', $id_rute)
            ->get(['id_jadwal', 'jam_keberangkatan', 'id_supir', 'id_kendaraan']);

        if ($jadwals->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'Jadwal tidak tersedia',
                'data' => []
            ]);
        }

        $kursiTerisiPerJadwal = DB::table('detail_pemesanan')
            ->join('pemesanan', 'detail_pemesanan.id_pemesanan', '=', 'pemesanan.id_pemesanan')
            ->leftJoin('pembayaran', 'pemesanan.id_pemesanan', '=', 'pembayaran.id_pemesanan')
            ->where('pemesanan.tanggal_keberangkatan', $tanggal)
            ->whereIn('pemesanan.id_jadwal', $jadwals->pluck('id_jadwal'))
            ->select(
                'pemesanan.id_jadwal',
                'detail_pemesanan.id_kursi',
                'pembayaran.status_konfirmasi'
            )
            ->get()
            ->groupBy('id_jadwal');


        $totalKursi = Kursi::count() > 0 ? Kursi::count() : 15;
        $semuaKursi = Kursi::all();

        $jadwals = $jadwals->map(function ($j) use ($kursiTerisiPerJadwal, $totalKursi, $semuaKursi) {
            $idJadwal = (int)$j->id_jadwal;

            // Ambil kursi yang terisi
            $terisi = $kursiTerisiPerJadwal->has($idJadwal)
                ? $kursiTerisiPerJadwal[$idJadwal]->toArray()
                : [];

            // Hitung bangku tersedia (yang status berhasil)
            $bangkuTersedia = $totalKursi - count(array_filter($terisi, function ($t) {
                return in_array($t->status_konfirmasi, ['menunggu', 'ditempat', 'berhasil']);
            }));

            $kursiStatus = $semuaKursi->map(function ($k) use ($terisi) {
                $status = 'kosong'; // default
                foreach ($terisi as $booking) {
                    if ($booking->id_kursi == $k->id_kursi) {
                        if (in_array($booking->status_konfirmasi, ['menunggu', 'ditempat', 'berhasil'])) {
                            $status = 'disable'; // abu-abu
                        } elseif ($booking->status_konfirmasi == 'ditolak') {
                            $status = 'kosong';
                        }
                        break;
                    }
                }
                return [
                    'id_kursi' => $k->id_kursi,
                    'no_kursi' => $k->no_kursi,
                    'status' => $status
                ];
            });


            return [
                'id_jadwal' => $j->id_jadwal,
                'jamKeberangkatan' => date('H:i', strtotime($j->jam_keberangkatan)),
                'supir' => $j->supir->nama_supir ?? null,
                'platBus' => $j->kendaraan->plat_nomor ?? null,
                'bangkuTersedia' => $bangkuTersedia,
                'kursi' => $kursiStatus
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Data jadwal berhasil diambil',
            'data' => $jadwals
        ]);
    }



    public function store(Request $request)
    {
        // Validasi request
        $validated = $request->validate([
            'id_penumpang' => 'required|exists:penumpang,id',
            'id_jadwal' => 'required|exists:jadwal,id_jadwal',
            'tanggal' => 'required|date',
            'nama' => 'required|array|min:1',
            'nama.*' => 'string|max:255',
            'kursi' => 'required|array|min:1',
            'kursi.*' => 'exists:kursi,id_kursi',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $jadwal = Jadwal::with('rute')->findOrFail($validated['id_jadwal']);
            $harga = $jadwal->rute->harga ?? 0;

            // Buat pemesanan
            $pemesanan = Pemesanan::create([
                'id_penumpang' => $validated['id_penumpang'],
                'id_jadwal' => $jadwal->id_jadwal,
                'tanggal_pemesanan' => now()->format('Y-m-d'),
                'tanggal_keberangkatan' => $validated['tanggal'],
            ]);

            $totalBayar = 0;

            // Detail pemesanan (nama per penumpang)
            foreach ($validated['nama'] as $index => $nama) {
                $idKursi = $validated['kursi'][$index] ?? null;
                if (!$idKursi) continue;

                // 1️⃣ Buat data penumpang baru
                $penumpang = Penumpang::create([
                    'nama_penumpang' => $nama,
                    // jika perlu, bisa tambahkan field lain seperti 'no_telepon', 'email' dsb
                ]);

                // 2️⃣ Simpan detail pemesanan
                DetailPemesanan::create([
                    'id_pemesanan' => $pemesanan->id_pemesanan,
                    'id_penumpang' => $penumpang->id, // <-- gunakan id penumpang baru
                    'id_kursi' => $idKursi,
                    'nama_penumpang' => $nama, // optional, supaya tetap ada di detail
                ]);

                $totalBayar += $harga;
            }


            // Buat pembayaran
            $pembayaran = Pembayaran::create([
                'id_pemesanan' => $pemesanan->id_pemesanan,
                'jumlah_pembayaran' => $totalBayar,
                'batas_waktu_pembayaran' => now()->addHours(2),
                'status_konfirmasi' => 'menunggu',
            ]);

            // Upload bukti pembayaran
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = 'bukti_' . $pemesanan->id_pemesanan . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/bukti', $filename);

                $pembayaran->update([
                    'upload_bukti' => str_replace('public/', 'storage/', $path),
                    'status_konfirmasi' => 'menunggu',
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Pemesanan berhasil',
                'data' => [
                    'pemesanan' => $pemesanan,
                    'pembayaran' => $pembayaran,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Pemesanan error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Gagal pemesanan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function getTiketPenumpang($id_penumpang)
    {
        // Ambil pemesanan milik penumpang tertentu
        $pemesanan = Pemesanan::with(['detail_pemesanan.kursi', 'detail_pemesanan.penumpang', 'pembayaran', 'jadwal.rute'])
            ->where('id_penumpang', $id_penumpang)
            ->get();

        $tiket = $pemesanan->map(function ($p) {
            $penumpangList = $p->detail_pemesanan->map(function ($d) {
                return [
                    'nama' => $d->penumpang->nama_penumpang ?? '-',
                    'kursi' => $d->kursi->no_kursi ?? '-'
                ];
            });

            return [
                'nomor_tiket' => 'FT-' . str_pad($p->id_pemesanan, 6, '0', STR_PAD_LEFT),
                'asal' => $p->jadwal->rute->asal ?? '-',
                'tujuan' => $p->jadwal->rute->tujuan ?? '-',
                'tanggal_keberangkatan' => $p->tanggal_keberangkatan,
                'jam' => date('H:i', strtotime($p->jadwal->jam_keberangkatan)),
                'status' => $p->pembayaran->status_konfirmasi ?? 'Menunggu',
                'total_bayar' => $p->pembayaran->jumlah_pembayaran ?? 0,
                'tanggal_pemesanan' => $p->tanggal_pemesanan,
                'penumpang' => $penumpangList,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Data tiket berhasil diambil',
            'data' => $tiket,
        ]);
    }
}
