<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthAny
{
    public function handle(Request $request, Closure $next)
    {
        if (
            Auth::guard('admin')->check() ||
            Auth::guard('petugas')->check() ||
            Auth::guard('penumpang')->check()
        ) {
            return $next($request);
        }

        // Cek jika request ke API (bisa pakai prefix atau cek header)
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return redirect()->route('login');
    }
}
