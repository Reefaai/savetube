<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Blokir akses download jika maintenance mode aktif.
     * Admin tetap bisa mengakses.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Setting::isMaintenanceMode()) {
            // Admin boleh bypass maintenance mode
            if (auth()->check() && auth()->user()->role === 'admin') {
                return $next($request);
            }

            // Untuk API request (JSON), return JSON error
            if ($request->expectsJson() || $request->is('video/*')) {
                return response()->json([
                    'success' => false,
                    'maintenance' => true,
                    'message' => 'SaveTube sedang dalam mode pemeliharaan. Fitur download sementara tidak tersedia. Silakan coba lagi nanti.',
                ], 503);
            }

            // Untuk non-API request, abort
            abort(503, 'SaveTube sedang dalam mode pemeliharaan.');
        }

        return $next($request);
    }
}
