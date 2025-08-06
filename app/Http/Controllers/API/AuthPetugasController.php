<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Petugas;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthPetugasController extends Controller
{
    // Login Petugas
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $petugas = Petugas::where('username', $request->username)->first();

        if (!$petugas || !Hash::check($request->password, $petugas->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Username atau password salah'
            ], 401);
        }

        return response()->json([
            'status' => true,
            'message' => 'Login berhasil',
            'data' => $petugas
        ], 200);
    }

    public function logout(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'Logout berhasil'
        ]);
    }
}
