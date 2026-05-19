@extends('admin.layout')
@section('title', __('admin.edit_category'))
@section('page-title', __('admin.edit_category'))

@section('content')
<div style="max-width:640px;">
    <div class="flex items-center gap-10 mb-20" style="font-size:13px;color:var(--text-secondary);">
        <a href="{{ route('admin.category.index') }}" style="color:#a78bfa;text-decoration:none;">👥 {{ __('admin.category_management') }}</a>
        <span>›</span>
        <span>{{ __('admin.edit_category') }}</span>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">✏️ {{ __('admin.edit_category') }}</div>
                <div class="card-subtitle">{{ __('admin.fill_all_fields') }}</div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.category.update', $category->id) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">{{ __('admin.category_name') }}</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required autofocus placeholder="{{ __('admin.category_name_placeholder') }}">
                @error('name') <div class="form-error">⚠️ {{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('admin.category_type') }}</label>
                <select name="type" class="form-control" required>
                    <option value="in" {{ old('type', $category->type) === 'in' ? 'selected' : '' }}>💰 {{ __('admin.category_type_income') }}</option>
                    <option value="out" {{ old('type', $category->type) === 'out' ? 'selected' : '' }}>💸 {{ __('admin.category_type_expense') }}</option>
                </select>
                @error('type') <div class="form-error">⚠️ {{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('admin.category_icon') }}</label>
                <input type="text" name="icon" class="form-control" value="{{ old('icon', $category->icon) }}" placeholder="{{ __('admin.category_icon_placeholder') }}">
                @error('icon') <div class="form-error">⚠️ {{ $message }}</div> @enderror
                <div class="form-hint">{{ __('admin.category_icon_hint') }}</div>
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('admin.category_color') }}</label>
                <input type="text" name="color" class="form-control" value="{{ old('color', $category->color) }}" placeholder="{{ __('admin.category_color_placeholder') }}">
                @error('color') <div class="form-error">⚠️ {{ $message }}</div> @enderror
                <div class="form-hint">{{ __('admin.category_color_hint') }}</div>
            </div>
            <div class="flex gap-10 items-center" style="margin-top:8px;">
                <button type="submit" class="btn btn-primary">✅ {{ __('admin.btn_edit_category') }}</button>
                <a href="{{ route('admin.category.index') }}" class="btn btn-secondary">{{ __('admin.cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
