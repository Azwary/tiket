<?php

namespace App\Http\Controllers;

use App\Models\{DetailPemesanan, Jadwal, Kursi, Pembayaran, Pemesanan, Penumpang, Rute};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemesananController extends Controller
{
    private function getRuteYangDiizinkan($role)
    {
        return match ($role) {
            'padang' => [1, 2],
            'solok' => [4],
            'sawah_lunto' => [3],
            'umum' => null, // null artinya bebas semua
            default => [],
        };
    }

    public function index(Request $request)
    {
        $admin = auth('admin')->user();
        $ruteDiizinkan = $this->getRuteYangDiizinkan($admin->role);

        $now = Carbon::now(); // waktu saat ini


        $query = Pemesanan::with([
            'penumpang',
            'jadwal.rute',
            'jadwal.supir',
            'jadwal.kendaraan',
            'detail_pemesanan.kursi',
            'pembayaran'
        ])
            ->whereHas('pembayaran', function ($q) {
                $q->whereIn('status_konfirmasi', ['berhasil', 'ditempat']);
            });

        // 🔒 Filter waktu hanya jika user **tidak melakukan filter sendiri**
        if (!$request->filled('tanggal') && !$request->filled('jam')) {
            $query->where(function ($q) use ($now) {
                $q->whereDate('tanggal_keberangkatan', '>', $now->toDateString())
                    ->orWhere(function ($q) use ($now) {
                        $q->whereDate('tanggal_keberangkatan', $now->toDateString())
                            ->whereHas('jadwal', function ($q) use ($now) {
                                $q->whereTime('jam_keberangkatan', '>=', $now->format('H:i:s'));
                            });
                    });
            });
        }

        // Filter rute berdasarkan role
        if ($ruteDiizinkan !== null) {
            $query->whereHas('jadwal', function ($q) use ($ruteDiizinkan) {
                $q->whereIn('id_rute', $ruteDiizinkan);
            });
        }

        // Filter berdasarkan input user
        if ($request->filled('rute')) {
            $query->whereHas('jadwal', function ($q) use ($request) {
                $q->where('id_rute', $request->rute);
            });
        }
        if ($request->filled('supir')) {
            $query->whereHas('jadwal', function ($q) use ($request) {
                $q->where('id_supir', $request->supir);
            });
        }
        if ($request->filled('kendaraan')) {
            $query->whereHas('jadwal', function ($q) use ($request) {
                $q->where('id_kendaraan', $request->kendaraan);
            });
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_keberangkatan', $request->tanggal);
        }

        if ($request->filled('jam')) {
            $query->whereHas('jadwal', function ($q) use ($request) {
                $q->where('jam_keberangkatan', $request->jam);
            });
        }

        if ($request->filled('status')) {
            $query->whereHas('pembayaran', function ($q) use ($request) {
                $q->where('status_konfirmasi', $request->status);
            });
        }

        $pemesanans = $query->orderByDesc('id_pemesanan')->get()->unique('id_pemesanan')->values();

        $pemesanans = $pemesanans->map(function ($item) {
            $item->nama_kursi = $item->detail_pemesanan->pluck('kursi.nama_kursi')->implode(', ');
            return $item;
        });

        $rutes = $ruteDiizinkan !== null
            ? Rute::whereIn('id_rute', $ruteDiizinkan)->get()
            : Rute::all();

        $jams = [];
        if ($request->filled('rute')) {
            $jams = Jadwal::where('id_rute', $request->rute)
                ->distinct()
                ->orderBy('jam_keberangkatan')
                ->pluck('jam_keberangkatan');
        }

        $statuses = ['menunggu', 'berhasil', 'ditolak', 'ditempat'];

        return view('admin.pemesanan', compact('pemesanans', 'rutes', 'jams', 'statuses'));
    }

    public function create()
    {
        $admin = auth('admin')->user();
        $ruteDiizinkan = $this->getRuteYangDiizinkan($admin->role);

        $rutes = $ruteDiizinkan !== null
            ? Rute::whereIn('id_rute', $ruteDiizinkan)->get()
            : Rute::all();

        $jadwals = Jadwal::with('rute')->get();
        $kursis = Kursi::all();

        return view('admin.add', compact('rutes', 'jadwals', 'kursis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required|exists:jadwal,id_jadwal',
            'tanggal' => 'required|date',
            'kursi' => 'required|array|min:1',
            'kursi.*' => 'exists:kursi,id_kursi',
            'nama' => 'required|array',
            'nama.*' => 'required|string|max:255',
        ]);

        // Ambil input sebagai array lokal
        $kursiArray = $request->input('kursi');
        $namaArray = $request->input('nama');

        $jadwal = Jadwal::with('rute')->findOrFail($request->id_jadwal);
        $harga = $jadwal->rute->harga ?? 0;

        // Buat pemesanan utama berdasarkan penumpang pertama
        $firstSeat = reset($kursiArray);
        $firstName = $namaArray[$firstSeat];
        $penumpang = Penumpang::create(['nama_penumpang' => $firstName]);

        $pemesanan = Pemesanan::create([
            'id_penumpang' => $penumpang->id,
            'id_jadwal' => $jadwal->id_jadwal,
            'tanggal_pemesanan' => now()->format('Y-m-d'),
            'tanggal_keberangkatan' => $request->tanggal,
        ]);

        // Simpan semua detail kursi & nama penumpang
        foreach ($kursiArray as $seat) {
            $namaSeat = $namaArray[$seat];
            $penumpangSeat = Penumpang::create(['nama_penumpang' => $namaSeat]);

            DetailPemesanan::create([
                'id_pemesanan' => $pemesanan->id_pemesanan,
                'id_penumpang' => $penumpangSeat->id,
                'id_kursi' => Kursi::where('no_kursi', $seat)->first()->id_kursi,
            ]);
        }

        // Hitung total bayar
        $total_pembayaran = $harga * count($kursiArray);
        Pembayaran::create([
            'id_pemesanan' => $pemesanan->id_pemesanan,
            'jumlah_pembayaran' => $total_pembayaran,
            'batas_waktu_pembayaran' => now(),
            'status_konfirmasi' => 'ditempat',
        ]);

        return redirect()->route('pemesanan.index')->with('success', 'Pemesanan berhasil disimpan dan dianggap lunas!');
    }

    public function destroy($id)
    {
        // Ambil detail pemesanan berdasarkan id_pemesanan
        $detail = DetailPemesanan::where('id_pemesanan', $id)->first();

        if ($detail) {
            // Ambil penumpang berdasarkan id_penumpang dari detail
            $penumpang = Penumpang::find($detail->id_penumpang);

            if ($penumpang) {
                $penumpang->delete();
            }

            // Hapus juga data pemesanan
            $pemesanan = Pemesanan::find($id);
            if ($pemesanan) {
                $pemesanan->delete();
            }

            return redirect()->back()->with('success', 'Penumpang dan pemesanan berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Data tidak ditemukan.');
    }

    public function edit($id)
    {
        $pemesanan = Pemesanan::with([
            'penumpang',
            'jadwal.rute',
            'detail_pemesanan.kursi'
        ])->findOrFail($id);

        $rutes = Rute::all();

        // Ambil rute saat inia
        $id_rute = old('id_rute', $pemesanan->jadwal->rute->id_rute ?? null);

        // Filter jadwal berdasarkan rute yang aktif
        $jadwals = Jadwal::where('id_rute', $id_rute)->get();

        return view('admin.edit', compact(
            'pemesanan',
            'rutes',
            'jadwals'
        ));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_rute' => 'required|exists:rute,id_rute',
            'id_jadwal' => 'required|exists:jadwal,id_jadwal',
            'tanggal' => 'required|date',
            'kursi' => 'required|array|min:1',
            'kursi.*' => 'exists:kursi,no_kursi',
            'nama' => 'required|array',
            'nama.*' => 'required|string|max:255',
        ]);

        // Ambil input sebagai array lokal
        $kursiArray = $request->input('kursi');
        $namaArray = $request->input('nama');

        $valid = Jadwal::where('id_jadwal', $request->id_jadwal)
            ->where('id_rute', $request->id_rute)
            ->exists();

        if (!$valid) return back()->withErrors(['id_jadwal' => 'Jadwal tidak sesuai dengan rute yang dipilih.']);

        DB::transaction(function () use ($request, $id, $kursiArray, $namaArray) {
            $pemesanan = Pemesanan::with('detail_pemesanan.penumpang')->findOrFail($id);

            // Hapus detail lama & penumpang lama
            foreach ($pemesanan->detail_pemesanan as $detail) {
                $detail->penumpang->delete();
                $detail->delete();
            }

            // Update pemesanan utama
            $pemesanan->update([
                'id_jadwal' => $request->id_jadwal,
                'tanggal_keberangkatan' => $request->tanggal,
            ]);

            // Ambil ID kursi
            $kursis = Kursi::whereIn('no_kursi', $kursiArray)->pluck('id_kursi', 'no_kursi');

            // Simpan detail pemesanan baru
            foreach ($kursiArray as $seatNo) {
                $penumpang = Penumpang::create(['nama_penumpang' => $namaArray[$seatNo]]);
                DetailPemesanan::create([
                    'id_pemesanan' => $pemesanan->id_pemesanan,
                    'id_penumpang' => $penumpang->id,
                    'id_kursi' => $kursis[$seatNo],
                ]);
            }

            // Update total pembayaran
            $harga = Jadwal::find($request->id_jadwal)->rute->harga ?? 0;
            Pembayaran::updateOrCreate(
                ['id_pemesanan' => $pemesanan->id_pemesanan],
                ['jumlah_pembayaran' => $harga * count($kursiArray)]
            );
        });

        return redirect()->route('pemesanan.index')->with('success', 'Pemesanan berhasil diperbarui.');
    }
    public function getByRute($id_rute)
    {
        $jadwals = Jadwal::where('id_rute', $id_rute)->get();
        return response()->json($jadwals);
    }



    public function getKursi(Request $request)
    {
        $id_rute = $request->query('id_rute');
        $tanggal = $request->query('tanggal');
        $id_jadwal = $request->query('id_jadwal');

        if (!$id_rute || !$tanggal || !$id_jadwal) {
            return response()->json(['data' => []]);
        }

        $terisi = DB::table('detail_pemesanan')
            ->join('pemesanan', 'detail_pemesanan.id_pemesanan', '=', 'pemesanan.id_pemesanan')
            ->join('kursi', 'detail_pemesanan.id_kursi', '=', 'kursi.id_kursi')
            ->join('jadwal', 'pemesanan.id_jadwal', '=', 'jadwal.id_jadwal')
            ->where('pemesanan.tanggal_keberangkatan', $tanggal)
            ->where('pemesanan.id_jadwal', $id_jadwal)
            ->where('jadwal.id_rute', $id_rute)
            ->pluck('kursi.no_kursi');

        return response()->json(['data' => $terisi]);
    }

    public function getJadwal(Request $request)
    {
        $id_rute = $request->query('id_rute');
        $tanggal = $request->query('tanggal');

        if (!$id_rute || !$tanggal) {
            return response()->json(['data' => []]);
        }

        $jadwals = Jadwal::where('id_rute', $id_rute)->get(['id_jadwal', 'jam']);

        return response()->json(['data' => $jadwals]);
    }

    public function getJamKeberangkatan($id_rute)
    {
        $jadwals = Jadwal::where('id_rute', $id_rute)->get();
        return response()->json($jadwals);
    }

    public function showKursi(Request $request)
    {
        $admin = auth('admin')->user();
        $ruteDiizinkan = $this->getRuteYangDiizinkan($admin->role);

        $id_rute = $request->input('rute');
        $tanggal = $request->input('tanggal');
        $id_jadwal = $request->input('jam');

        // Validasi akses rute tanpa abort
        if ($ruteDiizinkan !== null && $id_rute && !in_array($id_rute, $ruteDiizinkan)) {
            // Kosongkan input agar tidak diproses
            $id_rute = null;
            $id_jadwal = null;
            $tanggal = null;
        }

        $rutes = $ruteDiizinkan !== null
            ? Rute::whereIn('id_rute', $ruteDiizinkan)->get()
            : Rute::all();

        $jadwalsQuery = Jadwal::query();

        if ($id_rute) {
            $jadwalsQuery->where('id_rute', $id_rute);
        }

        if (!$tanggal) {
            $jadwalsQuery->whereRaw('1=0'); // agar tidak tampil jadwal jika tanggal belum dipilih
        }

        $jadwals = $jadwalsQuery->get();

        $kursi_terisi = [];
        if ($id_rute && $tanggal && $id_jadwal) {
            $kursi_terisi = DB::table('detail_pemesanan')
                ->join('pemesanan', 'detail_pemesanan.id_pemesanan', '=', 'pemesanan.id_pemesanan')
                ->join('kursi', 'detail_pemesanan.id_kursi', '=', 'kursi.id_kursi')
                ->join('jadwal', 'pemesanan.id_jadwal', '=', 'jadwal.id_jadwal')
                ->where('pemesanan.tanggal_keberangkatan', $tanggal)
                ->where('pemesanan.id_jadwal', $id_jadwal)
                ->where('jadwal.id_rute', $id_rute)
                ->pluck('kursi.no_kursi')
                ->toArray();
        }

        $kursis = Kursi::all();
        $seats = [1, 2, null, 'driver', null, 3, 4, 5, 6, null, 7, 8, 9, null, 10, 11, 12, 13, 14, 15];
        $sudahPilih = $id_rute && $tanggal && $id_jadwal;

        return view('admin.add', compact(
            'kursi_terisi',
            'seats',
            'rutes',
            'jadwals',
            'kursis',
            'id_rute',
            'tanggal',
            'id_jadwal',
            'sudahPilih'
        ));
    }
}
