<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Jika user tidak login atau tidak punya role yang diizinkan, tolak akses.
        if (!Auth::check() || !Auth::user()->hasAnyRole($roles)) {
            abort(403, 'AKSES DITOLAK. ANDA TIDAK MEMILIKI HAK AKSES.');
        }

        return $next($request);
    }
}