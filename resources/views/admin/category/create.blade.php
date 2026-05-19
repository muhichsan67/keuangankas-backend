@extends('admin.layout')
@section('title', __('admin.add_category'))
@section('page-title', __('admin.add_category'))

@section('content')
<div style="max-width:640px;">
    <div class="flex items-center gap-10 mb-20" style="font-size:13px;color:var(--text-secondary);">
        <a href="{{ route('admin.category.index') }}" style="color:#a78bfa;text-decoration:none;">👥 {{ __('admin.category_management') }}</a>
        <span>›</span>
        <span>{{ __('admin.create_new') }}</span>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">➕ {{ __('admin.add_category') }}</div>
                <div class="card-subtitle">{{ __('admin.fill_all_fields') }}</div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.category.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">{{ __('admin.category_name') }}</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus placeholder="{{ __('admin.category_name_placeholder') }}">
                @error('name') <div class="form-error">⚠️ {{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('admin.category_type') }}</label>
                <select name="type" class="form-control" required>
                    <option value="in" {{ old('type','in') === 'in' ? 'selected' : '' }}>💰 {{ __('admin.category_type_income') }}</option>
                    <option value="out" {{ old('type') === 'out' ? 'selected' : '' }}>💸 {{ __('admin.category_type_expense') }}</option>
                </select>
                @error('type') <div class="form-error">⚠️ {{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('admin.category_icon') }}</label>
                <input type="text" name="icon" class="form-control" value="{{ old('icon') }}" placeholder="{{ __('admin.category_icon_placeholder') }}">
                @error('icon') <div class="form-error">⚠️ {{ $message }}</div> @enderror
                <div class="form-hint">Contoh: 💰, 💸, 🏠, 🚗 (Opsional)</div>
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('admin.category_color') }}</label>
                <input type="text" name="color" class="form-control" value="{{ old('color') }}" placeholder="{{ __('admin.category_color_placeholder') }}">
                @error('color') <div class="form-error">⚠️ {{ $message }}</div> @enderror
                <div class="form-hint">Contoh: #FFFFFF (Opsional)</div>
            </div>
            <div class="flex gap-10 items-center" style="margin-top:8px;">
                <button type="submit" class="btn btn-primary">✅ {{ __('admin.btn_add_category') }}</button>
                <a href="{{ route('admin.category.index') }}" class="btn btn-secondary">{{ __('admin.cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
