<?php

namespace App\Http\Middleware;

use App\Services\ActivityLogService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    public function __construct(protected ActivityLogService $activityLogService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya catat jika user sudah terautentikasi
        if ($request->user()) {
            $method = $request->method();
            $url    = $request->fullUrl();

            $this->activityLogService->log(
                action: 'INTERACTION',
                description: "[{$method}] {$url}",
            );
        }

        return $response;
    }
}
