<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // 1. Cek apakah user sudah login (punya token)
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Silakan login terlebih dahulu.',
            ], 401);
        }

        // 2. Cek apakah role user sesuai dengan yang diizinkan
        $userRole = auth()->user()->role;

        if (!in_array($userRole, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Anda tidak memiliki akses ke halaman ini.',
            ], 403);
        }

        return $next($request);
    }
}
