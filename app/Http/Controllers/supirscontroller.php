<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Supir;
use App\Models\Kendaraan;
use App\Models\Rute;
use Illuminate\Http\Request;

class SupirsController extends Controller
{

    public function index()
    {
        $supirs = Supir::all();
        $kendaraans = Kendaraan::with('supir')->get();

        $admin = auth('admin')->user();
        $ruteDiizinkan = match ($admin->role) {
            'padang' => [1, 2],
            'solok' => [4],
            'sawah_lunto' => [3],
            'umum' => null, // null = semua rute boleh
            default => [],
        };

        $rutes = is_null($ruteDiizinkan) ? Rute::all() : Rute::whereIn('id_rute', $ruteDiizinkan)->get();
        $jams = []; // default kosong
        $jadwals = collect(); // default kosong

        return view('admin.supir', compact('supirs', 'kendaraans', 'rutes', 'jams', 'jadwals'));
    }


    public function create(Request $request)
    {
        $tipe = $request->query('tipe'); // apakah tambah supir atau kendaraan
        if ($tipe === 'supir') {
            return view('admin.create_supir');
        } elseif ($tipe === 'kendaraan') {
            $supirs = Supir::all();
            return view('admin.create_kendaraan', compact('supirs'));
        }

        abort(404, 'Halaman tidak ditemukan.');
    }

    public function store(Request $request)
    {
        $tipe = $request->query('tipe');

        if ($tipe === 'supir') {
            $request->validate([
                'nama_supir' => 'required|max:100',
                'no_hp' => 'required|max:20',
                'status' => 'nullable|string|max:20',
            ]);

            Supir::create([
                'nama_supir' => $request->nama_supir,
                'no_hp' => $request->no_hp,
                'status' => $request->status,
            ]);

            return redirect()->route('supir.index')->with('success', 'Supir berhasil ditambahkan.');
        }

        if ($tipe === 'kendaraan') {
            $request->validate([
                'plat_nomor' => 'required|max:20|unique:kendaraan,plat_nomor',
                'id_supir' => 'nullable|exists:supir,id_supir',
                'status' => 'nullable|string|max:20',
            ]);

            Kendaraan::create([
                'plat_nomor' => $request->plat_nomor,
                'id_supir' => $request->id_supir,
                'status' => $request->status,
            ]);

            return redirect()->route('supir.index')->with('success', 'Kendaraan berhasil ditambahkan.');
        }

        abort(404, 'Data tidak valid.');
    }

    public function edit($id, Request $request)
    {
        $tipe = $request->query('tipe');

        if ($tipe === 'supir') {
            $data = Supir::findOrFail($id);
            return view('admin.edit_supir-kendaraan', compact('data', 'tipe'));
        }

        // tipe = kendaraan
        $data = Kendaraan::with('supir')->findOrFail($id);
        $supirs = Supir::all();
        return view('admin.edit_supir-kendaraan', compact('data', 'tipe', 'supirs'));
    }

    public function update(Request $request, $id)
    {
        $tipe = $request->query('tipe', 'supir');

        if ($tipe === 'supir') {
            $request->validate([
                'nama_supir' => 'required|string|max:255',
                'no_hp'      => 'required|string|max:20',
                'status'     => 'nullable|string',
            ]);

            $supir = Supir::findOrFail($id);
            $supir->update($request->only(['nama_supir', 'no_hp', 'status']));
        } else { // kendaraan
            $request->validate([
                'plat_nomor' => 'required|string|max:20|unique:kendaraan,plat_nomor,' . $id . ',id_kendaraan',
                'id_supir'   => 'nullable|exists:supir,id_supir',
                'status'     => 'nullable|string',
            ]);

            $kendaraan = Kendaraan::findOrFail($id);
            $kendaraan->update($request->only(['plat_nomor', 'id_supir', 'status']));
        }

        return redirect()->route('supir.index')->with('success', 'Data berhasil diperbarui.');
    }


    public function destroy($id)
    {
        $tipe = request('tipe');

        if ($tipe === 'supir') {
            $supir = Supir::find($id);
            if ($supir) {
                $supir->delete();
                return redirect()->route('supir.index')->with('success', 'Supir berhasil dihapus.');
            }
            return redirect()->route('supir.index')->with('error', 'Supir tidak ditemukan.');
        }

        if ($tipe === 'kendaraan') {
            $kendaraan = Kendaraan::find($id);
            if ($kendaraan) {
                $kendaraan->delete();
                return redirect()->route('supir.index')->with('success', 'Kendaraan berhasil dihapus.');
            }
            return redirect()->route('supir.index')->with('error', 'Kendaraan tidak ditemukan.');
        }

        abort(404, 'Data tidak valid.');
    }

    public function jadwalIndex(Request $request)
    {
        $admin = auth('admin')->user();

        // mapping role -> rute yang diizinkan
        $ruteDiizinkan = match ($admin->role) {
            'padang' => [1, 2],
            'solok' => [4],
            'sawah_lunto' => [3],
            'umum' => null, // null = semua rute boleh
            default => [],
        };

        $supirs = Supir::all();
        $kendaraans = Kendaraan::all();
        $rutes = is_null($ruteDiizinkan) ? Rute::all() : Rute::whereIn('id_rute', $ruteDiizinkan)->get();

        $jadwals = collect(); // <-- default kosong

        // Ambil jam jika rute dipilih
        $jams = [];
        if ($request->filled('rute_id')) {
            $query = Jadwal::with(['rute', 'supir', 'kendaraan']);

            // filter sesuai role
            if (!is_null($ruteDiizinkan)) {
                $query->whereIn('id_rute', $ruteDiizinkan);
            }

            $query->where('id_rute', $request->rute_id);

            if ($request->filled('jam_keberangkatan')) {
                $query->where('jam_keberangkatan', $request->jam_keberangkatan);
            }

            $jadwals = $query->get();

            // Ambil jam unik untuk dropdown
            $jams = Jadwal::where('id_rute', $request->rute_id)
                ->distinct()
                ->orderBy('jam_keberangkatan')
                ->pluck('jam_keberangkatan');
        }

        return view('admin.supir', compact('jadwals', 'supirs', 'kendaraans', 'rutes', 'jams'));
    }




    public function jadwalStore(Request $request)
    {
        $request->validate([
            'id_rute' => 'required|exists:rute,id_rute',
            'id_supir' => 'required|exists:supir,id_supir',
            'id_kendaraan' => 'required|exists:kendaraan,id_kendaraan',
            'jam_keberangkatan' => 'required',
        ]);

        Jadwal::create($request->only(['id_rute', 'id_supir', 'id_kendaraan', 'jam_keberangkatan']));

        return redirect()->route('supir.jadwal')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function jadwalEdit($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $supirs = Supir::all();
        $kendaraans = Kendaraan::all();
        $rutes = Rute::all();
        return view('admin.edit_jadwal', compact('jadwal', 'supirs', 'kendaraans', 'rutes'));
    }

    public function jadwalUpdate(Request $request, $id)
    {
        $request->validate([
            'id_rute' => 'required|exists:rute,id_rute',
            'id_supir' => 'required|exists:supir,id_supir',
            'id_kendaraan' => 'required|exists:kendaraan,id_kendaraan',
            'jam_keberangkatan' => 'required',
        ]);

        $jadwal = Jadwal::findOrFail($id);
        $jadwal->update($request->only(['id_rute', 'id_supir', 'id_kendaraan', 'jam_keberangkatan']));

        return redirect()->route('supir.jadwal')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function jadwalDestroy($id)
    {
        $jadwal = Jadwal::find($id);
        if ($jadwal) {
            $jadwal->delete();
            return redirect()->route('supir.jadwal')->with('success', 'Jadwal berhasil dihapus.');
        }
        return redirect()->route('supir.jadwal')->with('error', 'Jadwal tidak ditemukan.');
    }
}
