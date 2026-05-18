@extends('admin.layout')
@section('title', __('admin.user_management'))
@section('page-title', __('admin.user_management'))

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">{{ __('admin.user_list') }}</div>
            <div class="card-subtitle">{{ __('admin.total_registered', ['count' => $users->total()]) }}</div>
        </div>
        <div class="flex gap-10 items-center">
            <span class="badge badge-purple">👥 {{ $users->total() }}</span>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">➕ {{ __('admin.add_user') }}</a>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>{{ __('admin.user') }}</th>
                    <th>{{ __('admin.email') }}</th>
                    <th>{{ __('admin.role') }}</th>
                    <th>{{ __('admin.transactions') }}</th>
                    <th>{{ __('admin.debts') }}</th>
                    <th>{{ __('admin.joined') }}</th>
                    <th style="text-align:right">{{ __('admin.action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="flex items-center gap-10">
                                <div style="width:36px;height:36px;border-radius:50%;
                                    background:linear-gradient(135deg,#7c3aed,#4f46e5);
                                    display:flex;align-items:center;justify-content:center;
                                    font-size:14px;font-weight:700;color:#fff;flex-shrink:0;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-size:14px;font-weight:600;color:var(--text-primary);">{{ $user->name }}</div>
                                    <div class="text-muted">ID #{{ $user->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->role === 'admin')
                                <span class="badge badge-purple">🛡️ {{ __('admin.role_admin') }}</span>
                            @else
                                <span class="badge badge-blue">👤 {{ __('admin.role_user') }}</span>
                            @endif
                        </td>
                        <td><span class="badge badge-green">{{ number_format($user->transactions_count) }}</span></td>
                        <td><span class="badge badge-yellow">{{ number_format($user->debts_count) }}</span></td>
                        <td class="text-muted">{{ $user->created_at?->format('d M Y') }}</td>
                        <td class="text-right">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-secondary btn-sm">✏️ {{ __('admin.edit') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">{{ __('admin.no_users') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="pagination">
            @if($users->onFirstPage())
                <span>‹</span>
            @else
                <a href="{{ $users->previousPageUrl() }}">‹</a>
            @endif
            @foreach($users->getUrlRange(max(1,$users->currentPage()-2), min($users->lastPage(),$users->currentPage()+2)) as $page => $url)
                @if($page == $users->currentPage())
                    <span class="active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}">›</a>
            @else
                <span>›</span>
            @endif
        </div>
    @endif
</div>
@endsection
