<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login dan role-nya adalah 'owner'
        if (auth()->check() && auth()->user()->isOwner()) {
            return $next($request);
        }

        // Jika bukan owner (misal: admin biasa), tampilkan error 403 Forbidden
        abort(403, 'Akses Ditolak. Halaman ini hanya diperuntukkan bagi Owner.');
    }
}