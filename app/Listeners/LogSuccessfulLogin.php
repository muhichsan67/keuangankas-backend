<?php

namespace App\Listeners;

use App\Services\ActivityLogService;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function __construct(protected ActivityLogService $activityLogService) {}

    public function handle(Login $event): void
    {
        $this->activityLogService->log(
            action: 'LOGIN',
            description: "User '{$event->user->email}' berhasil login.",
            userId: $event->user->id,
        );
    }
}
