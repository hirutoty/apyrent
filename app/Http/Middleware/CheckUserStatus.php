<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next)
    {
        // Hanya user dengan status 'aktif' yang boleh mengakses sistem
        if (Auth::check() && Auth::user()->status !== 'aktif') {
            
            $user = Auth::user();
            
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = $user->status === 'regis' 
                ? 'Akun Anda masih menunggu persetujuan admin.'
                : 'Akun Anda telah diblokir. Hubungi administrator.';

            return redirect('/login')->withErrors([
                'email' => $message,
            ]);
        }

        return $next($request);
    }
}
