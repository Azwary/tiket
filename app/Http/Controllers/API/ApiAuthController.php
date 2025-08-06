<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Petugas;
use App\Models\Penumpang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $penumpang = Penumpang::where('username', $request->username)->first();
        if ($penumpang && Hash::check($request->password, $penumpang->password)) {
            return response()->json([
                'status' => true,
                'message' => 'Login berhasil sebagai penumpang',
                'role' => 'penumpang',
                'data' => $penumpang
            ]);
        }

        $petugas = Petugas::where('username', $request->username)->first();
        if ($petugas && Hash::check($request->password, $petugas->password)) {
            return response()->json([
                'status' => true,
                'message' => 'Login berhasil sebagai petugas',
                'role' => 'petugas',
                'data' => $petugas
            ]);
        }

        // Jika tidak ditemukan
        return response()->json([
            'status' => false,
            'message' => 'Username atau password salah'
        ], 401);
    }
    //  public function login(Request $request)
    // {
    //     $request->validate([
    //         'username' => 'required|string',
    //         'password' => 'required|string'
    //     ]);

    //     // Ambil user dari kedua tabel
    //     $petugas = Petugas::where('username', $request->username)->first();
    //     $penumpang = Penumpang::where('username', $request->username)->first();

    //     // Cek jika username dan password cocok di kedua tabel
    //     $validPetugas = $petugas && Hash::check($request->password, $petugas->password);
    //     $validPenumpang = $penumpang && Hash::check($request->password, $penumpang->password);

    //     // Jika dua-duanya cocok, tentukan role berdasarkan field 'no_hp'
    //     if ($validPetugas && $validPenumpang) {
    //         // Kalau punya field no_hp -> penumpang
    //         if (!empty($penumpang->no_telepon)) {
    //             return response()->json([
    //                 'status' => true,
    //                 'message' => 'Login berhasil sebagai penumpang',
    //                 'role' => 'penumpang',
    //                 'data' => $penumpang
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'status' => true,
    //                 'message' => 'Login berhasil sebagai petugas',
    //                 'role' => 'petugas',
    //                 'data' => $petugas
    //             ]);
    //         }
    //     }

    //     // Jika hanya petugas yang cocok
    //     if ($validPetugas) {
    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Login berhasil sebagai petugas',
    //             'role' => 'petugas',
    //             'data' => $petugas
    //         ]);
    //     }

    //     // Jika hanya penumpang yang cocok
    //     if ($validPenumpang) {
    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Login berhasil sebagai penumpang',
    //             'role' => 'penumpang',
    //             'data' => $penumpang
    //         ]);
    //     }

    //     // Jika tidak cocok semua
    //     return response()->json([
    //         'status' => false,
    //         'message' => 'Username atau password salah'
    //     ], 401);
    // }
}
