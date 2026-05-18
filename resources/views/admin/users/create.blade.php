@extends('admin.layout')
@section('title', __('admin.create_user'))
@section('page-title', __('admin.create_user'))

@section('content')
<div style="max-width:640px;">
    <div class="flex items-center gap-10 mb-20" style="font-size:13px;color:var(--text-secondary);">
        <a href="{{ route('admin.users.index') }}" style="color:#a78bfa;text-decoration:none;">👥 {{ __('admin.user_management') }}</a>
        <span>›</span>
        <span>{{ __('admin.create_new') }}</span>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">➕ {{ __('admin.create_user') }}</div>
                <div class="card-subtitle">{{ __('admin.fill_all_fields') }}</div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">{{ __('admin.full_name') }}</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus placeholder="{{ __('admin.name_placeholder') }}">
                @error('name') <div class="form-error">⚠️ {{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('admin.email') }}</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="{{ __('admin.email_placeholder') }}">
                @error('email') <div class="form-error">⚠️ {{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('admin.role_access') }}</label>
                <select name="role" class="form-control" required>
                    <option value="user" {{ old('role','user') === 'user' ? 'selected' : '' }}>👤 {{ __('admin.role_user_label') }}</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>🛡️ {{ __('admin.role_admin_label') }}</option>
                </select>
                @error('role') <div class="form-error">⚠️ {{ $message }}</div> @enderror
                <div class="form-hint">⚠️ {{ __('admin.role_admin_warning') }}</div>
            </div>
            <div style="border-top:1px solid var(--border);margin:24px 0;"></div>
            <div style="font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:16px;">🔑 {{ __('admin.password') }}</div>
            <div class="form-group">
                <label class="form-label">{{ __('admin.password') }}</label>
                <input type="password" name="password" class="form-control" placeholder="{{ __('admin.password_placeholder') }}" autocomplete="new-password" required>
                @error('password') <div class="form-error">⚠️ {{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('admin.confirm_password') }}</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="{{ __('admin.confirm_placeholder') }}" autocomplete="new-password" required>
            </div>
            <div class="flex gap-10 items-center" style="margin-top:8px;">
                <button type="submit" class="btn btn-primary">✅ {{ __('admin.btn_create_user') }}</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">{{ __('admin.cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
