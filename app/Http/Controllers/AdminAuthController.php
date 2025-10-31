<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    // Menampilkan form minta tanggal lahir
    public function showResetForm()
    {
        return view('auth.password_request');
    }

    // Handle validasi tanggal lahir
    public function handleResetRequest(Request $request)
    {
        $request->validate([
            'username' => 'required|exists:admin,username',
            'tanggal_lahir' => 'required|date',
        ]);

        $admin = Admin::where('username', $request->username)
            ->where('tanggal_lahir', $request->tanggal_lahir)
            ->first();

        if (!$admin) {
            return back()->with('error', 'Username atau tanggal lahir tidak cocok.');
        }

        // Ganti route bawaan dengan route custom
        return redirect()->route('admin.password.reset', $admin->username);
    }


    // Form untuk ganti password baru
    public function showNewPasswordForm($username)
    {
        return view('auth.password_reset', compact('username'));
    }

    // Update password
    public function updatePassword(Request $request, $username)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $admin = Admin::where('username', $username)->firstOrFail();
        $admin->password = Hash::make($request->password);
        $admin->save();

        // Ubah redirect ke halaman login
        return redirect()->route('login.admin')->with('success', 'Password berhasil diperbarui. Silakan login.');
    }
}
