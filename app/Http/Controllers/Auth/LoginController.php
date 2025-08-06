<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Penumpang;
use App\Models\Petugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    // public function showLoginForm()
    // {
    //     return view('auth.login');
    // }

    // public function authenticate(Request $request)
    // {
    //     $credentials = $request->only('username', 'password');

    //     if (Auth::guard('web')->attempt($credentials)) {
    //         $request->session()->regenerate();
    //         return redirect()->intended('/dashboard');
    //     }

    //     if (Auth::guard('penumpang')->attempt($credentials)) {
    //         $request->session()->regenerate();
    //         return redirect()->intended('/dashboard');
    //     }

    //     return back()->withErrors([
    //         'username' => 'Username atau password salah.',
    //     ]);
    // }

    // public function logout(Request $request)
    // {
    //     Auth::guard('web')->logout();
    //     Auth::guard('penumpang')->logout();

    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();

    //     return redirect('/login');
    // }
}
