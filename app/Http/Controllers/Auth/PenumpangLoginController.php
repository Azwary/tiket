<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenumpangLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.penumpang-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::guard('penumpang')->attempt($credentials)) {
            return redirect()->intended('/penumpang/dashboard');
        }

        return back()->withErrors(['username' => 'Login gagal!']);
    }

    public function logout()
    {
        Auth::guard('penumpang')->logout();
        return redirect('/penumpang/login');
    }
}
