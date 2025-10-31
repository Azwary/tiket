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
            'message' => 'username atau password salah'
        ], 401);
    }
}
