@extends('admin.layout')
@section('title', __('admin.dashboard'))
@section('page-title', __('admin.dashboard'))

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
        <div class="stat-label">{{ __('admin.total_users') }}</div>
    </div>
    <div class="stat-card success">
        <div class="stat-icon">💸</div>
        <div class="stat-value">{{ number_format($stats['total_transactions']) }}</div>
        <div class="stat-label">{{ __('admin.total_transactions') }}</div>
    </div>
    <div class="stat-card info">
        <div class="stat-icon">📋</div>
        <div class="stat-value">{{ number_format($stats['total_debts']) }}</div>
        <div class="stat-label">{{ __('admin.total_debts') }}</div>
    </div>
    <div class="stat-card warning">
        <div class="stat-icon">🗑️</div>
        <div class="stat-value">{{ number_format($stats['trashed_items']) }}</div>
        <div class="stat-label">{{ __('admin.trashed_items') }}</div>
    </div>
    <div class="stat-card danger">
        <div class="stat-icon">⚠️</div>
        <div class="stat-value">{{ number_format($stats['suspicious']) }}</div>
        <div class="stat-label">{{ __('admin.suspicious') }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">{{ __('admin.cash_flow') }}</div>
                <div class="card-subtitle">{{ __('admin.cash_flow_sub') }}</div>
            </div>
        </div>
        <div style="display:flex;flex-direction:column;gap:12px;">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 16px;background:rgba(16,185,129,0.07);border:1px solid rgba(16,185,129,0.15);border-radius:10px;">
                <div>
                    <div style="font-size:12px;color:var(--text-secondary);margin-bottom:3px;">💰 {{ __('admin.income') }}</div>
                    <div style="font-size:20px;font-weight:700;color:#10b981;">Rp {{ number_format($stats['total_amount_in'], 0, ',', '.') }}</div>
                </div>
                <div style="font-size:28px;">📈</div>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 16px;background:rgba(239,68,68,0.07);border:1px solid rgba(239,68,68,0.15);border-radius:10px;">
                <div>
                    <div style="font-size:12px;color:var(--text-secondary);margin-bottom:3px;">💸 {{ __('admin.expense') }}</div>
                    <div style="font-size:20px;font-weight:700;color:#ef4444;">Rp {{ number_format($stats['total_amount_out'], 0, ',', '.') }}</div>
                </div>
                <div style="font-size:28px;">📉</div>
            </div>
            @php $balance = $stats['total_amount_in'] - $stats['total_amount_out']; @endphp
            <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 16px;background:rgba(124,58,237,0.07);border:1px solid rgba(124,58,237,0.15);border-radius:10px;">
                <div>
                    <div style="font-size:12px;color:var(--text-secondary);margin-bottom:3px;">⚖️ {{ __('admin.balance') }}</div>
                    <div style="font-size:20px;font-weight:700;color:{{ $balance >= 0 ? '#10b981' : '#ef4444' }};">
                        {{ $balance >= 0 ? '+' : '' }}Rp {{ number_format($balance, 0, ',', '.') }}
                    </div>
                </div>
                <div style="font-size:28px;">🏦</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">{{ __('admin.recent_activity') }}</div>
                <div class="card-subtitle">{{ __('admin.recent_activity_sub') }}</div>
            </div>
        </div>
        <div style="display:flex;flex-direction:column;gap:8px;max-height:280px;overflow-y:auto;">
            @forelse($recentLogs as $log)
                <div style="display:flex;align-items:flex-start;gap:10px;padding:10px 12px;background:var(--bg-card);border-radius:8px;">
                    <span style="font-size:16px;flex-shrink:0;margin-top:1px;">
                        @if($log->action === 'LOGIN') 🔓
                        @elseif($log->action === 'LOGOUT') 🔒
                        @elseif($log->action === 'SUSPICIOUS_ACTIVITY') ⚠️
                        @elseif($log->action === 'HARD_DELETE') 💥
                        @elseif($log->action === 'RESTORE') ♻️
                        @else 📋
                        @endif
                    </span>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:12px;font-weight:600;color:
                            @if($log->action === 'SUSPICIOUS_ACTIVITY') #ef4444
                            @elseif($log->action === 'HARD_DELETE') #f59e0b
                            @elseif($log->action === 'RESTORE') #10b981
                            @else #a78bfa
                            @endif;">{{ $log->action }}</div>
                        <div style="font-size:12px;color:var(--text-secondary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $log->description }}</div>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">
                            {{ $log->user?->name ?? 'Guest' }} · {{ $log->created_at?->diffForHumans() }}
                        </div>
                    </div>
                </div>
            @empty
                <div style="text-align:center;padding:30px;color:var(--text-muted);font-size:13px;">{{ __('admin.no_activity') }}</div>
            @endforelse
        </div>
    </div>
</div>

<div class="flex gap-16 mt-20">
    <a href="{{ route('admin.users.index') }}" class="btn btn-primary">👥 {{ __('admin.manage_users') }}</a>
    <a href="{{ route('admin.trash.index') }}" class="btn btn-secondary">🗑️ {{ __('admin.view_trash') }}</a>
</div>
@endsection
