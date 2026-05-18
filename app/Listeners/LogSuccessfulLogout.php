<?php

namespace App\Listeners;

use App\Services\ActivityLogService;
use Illuminate\Auth\Events\Logout;

class LogSuccessfulLogout
{
    public function __construct(protected ActivityLogService $activityLogService) {}

    public function handle(Logout $event): void
    {
        if ($event->user) {
            $this->activityLogService->log(
                action: 'LOGOUT',
                description: "User '{$event->user->email}' logout.",
                userId: $event->user->id,
            );
        }
    }
}
