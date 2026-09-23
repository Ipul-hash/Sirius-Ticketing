<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Menangani permintaan masuk dan memverifikasi peran pengguna (RBAC).
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi tidak terotentikasi. Silakan login terlebih dahulu.',
                ], Response::HTTP_UNAUTHORIZED);
            }

            abort(Response::HTTP_UNAUTHORIZED, 'Silakan login terlebih dahulu.');
        }

        $userRole = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

        // Superadmin memiliki wewenang akses universal kecuali dibatasi secara spesifik
        $hasAccess = in_array($userRole, $roles, true) || $userRole === UserRole::Superadmin->value;

        if (! $hasAccess) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki hak akses (role tidak memadai) untuk mengakses data ini.',
                ], Response::HTTP_FORBIDDEN);
            }

            abort(Response::HTTP_FORBIDDEN, 'Akses Ditolak: Anda tidak memiliki izin untuk membuka halaman ini.');
        }

        return $next($request);
    }
}
