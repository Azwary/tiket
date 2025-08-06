<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penumpang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthPenumpangController extends Controller
{
    // Registrasi Penumpang
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_penumpang' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:20|unique:penumpang,no_telepon',
            'username' => 'required|string|max:255|unique:penumpang,username',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $penumpang = Penumpang::create([
            'nama_penumpang' => $request->nama_penumpang,
            'no_telepon' => $request->no_telepon,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Registrasi berhasil',
            'data' => $penumpang
        ], 201);
    }

    // Login Penumpang
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $penumpang = Penumpang::where('username', $request->username)->first();

        if (!$penumpang || !Hash::check($request->password, $penumpang->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Username atau password salah'
            ], 401);
        }

        // Token manual atau pasang Laravel Sanctum/Passport jika dibutuhkan
        return response()->json([
            'status' => true,
            'message' => 'Login berhasil',
            'data' => $penumpang
        ]);
    }
    public function logout(Request $request)
    {

        return response()->json([
            'status' => true,
            'message' => 'Logout berhasil'
        ]);
    }
}
