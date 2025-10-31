<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penumpang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthPenumpangController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_penumpang' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:20|unique:penumpang,no_telepon',
            'email' => 'required|string|email|max:255|unique:penumpang,email',
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
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Registrasi berhasil',
            'data' => $penumpang
        ], 201);
    }

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
    public function updateProfile(Request $request, $id)
    {
        $penumpang = Penumpang::find($id);

        if (!$penumpang) {
            return response()->json([
                'status' => false,
                'message' => 'Penumpang tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_penumpang' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:20|unique:penumpang,no_telepon,' . $id,
            'email' => 'required|string|email|max:255|unique:penumpang,email,' . $id,
            'username' => 'required|string|max:255|unique:penumpang,username,' . $id,
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $penumpang->nama_penumpang = $request->nama_penumpang;
        $penumpang->no_telepon = $request->no_telepon;
        $penumpang->email = $request->email;
        $penumpang->username = $request->username;

        if ($request->password) {
            $penumpang->password = Hash::make($request->password);
        }

        $penumpang->save();

        return response()->json([
            'status' => true,
            'message' => 'Profil berhasil diperbarui',
            'data' => $penumpang
        ]);
    }
}
