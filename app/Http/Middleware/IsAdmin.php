<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
        {
            // Izinkan jika user adalah admin ATAU owner
            if (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'owner')) {
                return $next($request);
            }
            // Jika pelanggan biasa, tendang ke halaman home
            return redirect('/')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }
}
