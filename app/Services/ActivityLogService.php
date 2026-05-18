<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    public function __construct(protected Request $request) {}

    /**
     * Catat aktivitas ke tabel activity_logs.
     */
    public function log(string $action, string $description, ?int $userId = null): void
    {
        ActivityLog::create([
            'user_id'    => $userId ?? Auth::id(),
            'action'     => $action,
            'description'=> $description,
            'ip_address' => $this->request->ip() ?? 'unknown',
            'user_agent' => $this->request->userAgent() ?? 'unknown',
        ]);
    }
}
