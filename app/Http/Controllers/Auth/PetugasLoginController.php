<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetugasLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.petugas-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::guard('petugas')->attempt($credentials)) {
            return redirect()->intended('/petugas/dashboard');
        }

        return back()->withErrors(['username' => 'Login gagal!']);
    }

    public function logout()
    {
        Auth::guard('petugas')->logout();
        return redirect('/petugas/login');
    }
}
