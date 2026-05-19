@extends('admin.layout')
@section('title', __('admin.category_management'))
@section('page-title', __('admin.category_management'))

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">{{ __('admin.category_list') }}</div>
            <div class="card-subtitle">{{ __('admin.total_categories', ['count' => $categories->total()]) }}</div>
        </div>
        <div class="flex gap-10 items-center">
            <span class="badge badge-purple">👥 {{ $categories->total() }}</span>
            <a href="{{ route('admin.category.create') }}" class="btn btn-primary btn-sm">➕ {{ __('admin.add_category') }}</a>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>{{ __('admin.category_name') }}</th>
                    <th>{{ __('admin.category_type') }}</th>
                    <th>{{ __('admin.category_color') }}</th>
                    <th>{{ __('admin.category_icon') }}</th>
                    <th style="text-align:right">{{ __('admin.action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>
                            <div class="flex items-center gap-10">
                                <div style="width:36px;height:36px;border-radius:50%;
                                    background:linear-gradient(135deg,#7c3aed,#4f46e5);
                                    display:flex;align-items:center;justify-content:center;
                                    font-size:14px;font-weight:700;color:#fff;flex-shrink:0;">
                                    {{ strtoupper(substr($category->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-size:14px;font-weight:600;color:var(--text-primary);">{{ $category->name }}</div>
                                    <div class="text-muted">ID #{{ $category->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $category->type == 'income' ? __('admin.category_type_income') : __('admin.category_type_expense') }}</td>
                        <td>
                            <div style="width:80px;height:30px;border-radius:10px;background:{{ $category->color }};display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#fff;flex-shrink:0;">
                                {{ strtoupper(substr($category->color, 0, 1)) }}
                            </div>
                        </td>
                        <td><span class="badge badge-green">{{ $category->icon }}</span></td>
                        <td class="text-right">
                            <a href="{{ route('admin.category.edit', $category->id) }}" class="btn btn-secondary btn-sm">✏️ {{ __('admin.edit') }}</a>
                            <form action="{{ route('admin.category.destroy', $category->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('admin.btn_delete_category', ['category' => $category->name]) }}')">🗑️ {{ __('admin.btn_delete_category') }}</button>
                            </form  >
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">{{ __('admin.no_categories') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
        <div class="pagination">
            @if($categories->onFirstPage())
                <span>‹</span>
            @else
                <a href="{{ $categories->previousPageUrl() }}">‹</a>
            @endif
            @foreach($categories->getUrlRange(max(1,$categories->currentPage()-2), min($categories->lastPage(),$categories->currentPage()+2)) as $page => $url)
                @if($page == $categories->currentPage())
                    <span class="active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
            @if($categories->hasMorePages())
                <a href="{{ $categories->nextPageUrl() }}">›</a>
            @else
                <span>›</span>
            @endif
        </div>
    @endif
</div>
@endsection
