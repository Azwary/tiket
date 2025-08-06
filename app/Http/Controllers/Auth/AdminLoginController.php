<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        // Cek apakah username terdaftar
        $admin = Admin::where('username', $credentials['username'])->first();

        if (!$admin) {
            return redirect()->back()->with('error', 'Username tidak ditemukan.');
        }

        // Coba login
        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->intended('/dashboard')->with('success', 'Login berhasil.');
        } else {
            return redirect()->back()->with('error', 'Password salah.');
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda telah logout.');
    }
}
