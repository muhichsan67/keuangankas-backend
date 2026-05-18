<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Debt;
use App\Models\Transaction;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'        => User::count(),
            'total_transactions' => Transaction::count(),
            'total_debts'        => Debt::count(),
            'trashed_items'      => Transaction::onlyTrashed()->count() + Debt::onlyTrashed()->count(),
            'suspicious'         => ActivityLog::where('action', 'SUSPICIOUS_ACTIVITY')->count(),
            'total_amount_in'    => Transaction::where('type', 'in')->sum('amount'),
            'total_amount_out'   => Transaction::where('type', 'out')->sum('amount'),
        ];

        $recentLogs = ActivityLog::with('user')
            ->latest('created_at')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentLogs'));
    }
}
