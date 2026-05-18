@extends('admin.layout')
@section('title', __('admin.trash_title'))
@section('page-title', __('admin.trash_title'))

@section('content')
<div class="tabs">
    <button class="tab-btn active" onclick="switchTab('transactions', this)">
        💸 {{ __('admin.trashed_transactions') }}
        <span class="badge badge-red" style="margin-left:6px;">{{ $trashedTransactions->count() }}</span>
    </button>
    <button class="tab-btn" onclick="switchTab('debts', this)">
        📋 {{ __('admin.trashed_debts') }}
        <span class="badge badge-yellow" style="margin-left:6px;">{{ $trashedDebts->count() }}</span>
    </button>
</div>

<!-- Tab: Transactions -->
<div id="tab-transactions" class="tab-content active">
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">{{ __('admin.soft_deleted_trx') }}</div>
                <div class="card-subtitle">{{ __('admin.soft_deleted_trx_sub') }}</div>
            </div>
            @if($trashedTransactions->count() > 0)
                <span class="badge badge-red">{{ $trashedTransactions->count() }} item</span>
            @endif
        </div>

        @if($trashedTransactions->isEmpty())
            <div style="text-align:center;padding:50px 20px;color:var(--text-muted);">
                <div style="font-size:40px;margin-bottom:12px;">✅</div>
                <div style="font-size:14px;font-weight:500;">{{ __('admin.no_trashed_trx') }}</div>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>{{ __('admin.user') }}</th>
                            <th>{{ __('admin.type') }}</th>
                            <th>{{ __('admin.amount') }}</th>
                            <th>{{ __('admin.category') }}</th>
                            <th>{{ __('admin.date') }}</th>
                            <th>{{ __('admin.receipt') }}</th>
                            <th>{{ __('admin.deleted_at') }}</th>
                            <th style="text-align:right">{{ __('admin.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trashedTransactions as $trx)
                            <tr>
                                <td class="text-muted">#{{ $trx->id }}</td>
                                <td>
                                    <div style="font-weight:500;color:var(--text-primary);">{{ $trx->user?->name ?? '—' }}</div>
                                    <div class="text-muted">{{ $trx->user?->email }}</div>
                                </td>
                                <td>
                                    @if($trx->type === 'in')
                                        <span class="badge badge-green">📈 {{ __('admin.income_type') }}</span>
                                    @else
                                        <span class="badge badge-red">📉 {{ __('admin.expense_type') }}</span>
                                    @endif
                                </td>
                                <td style="font-weight:600;color:{{ $trx->type === 'in' ? '#10b981' : '#ef4444' }};">
                                    Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                </td>
                                <td>{{ $trx->category }}</td>
                                <td class="text-muted">{{ $trx->date?->format('d M Y') }}</td>
                                <td>
                                    @if($trx->receipt_url)
                                        <a href="{{ $trx->receipt_url }}" target="_blank" class="badge badge-blue">🧾 {{ __('admin.view_receipt') }}</a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $trx->deleted_at?->diffForHumans() }}</td>
                                <td>
                                    <div class="flex gap-10 items-center" style="justify-content:flex-end;">
                                        <form method="POST" action="{{ route('admin.trash.transactions.restore', $trx->id) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">♻️ {{ __('admin.restore') }}</button>
                                        </form>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="openModal(
                                            '{{ __("admin.force_delete_trx_title") }}',
                                            'Transaction #{{ $trx->id }} (Rp {{ number_format($trx->amount, 0, ",", ".") }})',
                                            '{{ route("admin.trash.transactions.force-delete", $trx->id) }}',
                                            'DELETE'
                                        )">💥 {{ __('admin.force_delete') }}</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Tab: Debts -->
<div id="tab-debts" class="tab-content">
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">{{ __('admin.soft_deleted_debts') }}</div>
                <div class="card-subtitle">{{ __('admin.soft_deleted_debts_sub') }}</div>
            </div>
            @if($trashedDebts->count() > 0)
                <span class="badge badge-yellow">{{ $trashedDebts->count() }} item</span>
            @endif
        </div>

        @if($trashedDebts->isEmpty())
            <div style="text-align:center;padding:50px 20px;color:var(--text-muted);">
                <div style="font-size:40px;margin-bottom:12px;">✅</div>
                <div style="font-size:14px;font-weight:500;">{{ __('admin.no_trashed_debts') }}</div>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>{{ __('admin.user') }}</th>
                            <th>{{ __('admin.source') }}</th>
                            <th>{{ __('admin.monthly_cost') }}</th>
                            <th>{{ __('admin.tenor') }}</th>
                            <th>{{ __('admin.due_date') }}</th>
                            <th>{{ __('admin.deleted_at') }}</th>
                            <th style="text-align:right">{{ __('admin.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trashedDebts as $debt)
                            <tr>
                                <td class="text-muted">#{{ $debt->id }}</td>
                                <td>
                                    <div style="font-weight:500;color:var(--text-primary);">{{ $debt->user?->name ?? '—' }}</div>
                                    <div class="text-muted">{{ $debt->user?->email }}</div>
                                </td>
                                <td style="font-weight:500;color:var(--text-primary);">{{ $debt->source }}</td>
                                <td style="color:#f59e0b;font-weight:600;">Rp {{ number_format($debt->monthly_cost, 0, ',', '.') }}</td>
                                <td><span class="badge badge-purple">{{ $debt->total_tenor }} {{ __('admin.month_suffix') }}</span></td>
                                <td class="text-muted">Tgl {{ $debt->monthly_deadline }} {{ __('admin.per_month') }}</td>
                                <td class="text-muted">{{ $debt->deleted_at?->diffForHumans() }}</td>
                                <td>
                                    <div class="flex gap-10 items-center" style="justify-content:flex-end;">
                                        <form method="POST" action="{{ route('admin.trash.debts.restore', $debt->id) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">♻️ {{ __('admin.restore') }}</button>
                                        </form>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="openModal(
                                            '{{ __("admin.force_delete_debt_title") }}',
                                            '{{ $debt->source }} (ID #{{ $debt->id }})',
                                            '{{ route("admin.trash.debts.force-delete", $debt->id) }}',
                                            'DELETE'
                                        )">💥 {{ __('admin.force_delete') }}</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function switchTab(tab, el) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    el.classList.add('active');
}
</script>
@endpush
@endsection
