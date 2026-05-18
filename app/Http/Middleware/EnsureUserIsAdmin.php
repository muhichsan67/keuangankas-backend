<?php

namespace App\Http\Middleware;

use App\Services\ActivityLogService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function __construct(protected ActivityLogService $activityLogService) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isAdmin()) {
            // Catat percobaan akses tidak sah
            $this->activityLogService->log(
                action: 'SUSPICIOUS_ACTIVITY',
                description: 'Percobaan akses admin oleh non-admin ke: ' . $request->fullUrl(),
            );

            return response()->json(['message' => 'Akses ditolak. Hanya admin yang diizinkan.'], 403);
        }

        return $next($request);
    }
}
