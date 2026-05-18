@extends('admin.layout')
@section('title', __('admin.edit_user') . ' — ' . $user->name)
@section('page-title', __('admin.edit_user'))

@section('content')
<div style="max-width:640px;">
    <div class="flex items-center gap-10 mb-20" style="font-size:13px;color:var(--text-secondary);">
        <a href="{{ route('admin.users.index') }}" style="color:#a78bfa;text-decoration:none;">👥 {{ __('admin.user_management') }}</a>
        <span>›</span>
        <span>{{ __('admin.edit') }}: {{ $user->name }}</span>
    </div>

    <!-- User Info Card (readonly) -->
    <div class="card mb-20">
        <div class="card-header">
            <div class="card-title">{{ __('admin.user_info') }}</div>
            <span class="badge badge-yellow">⚠️ {{ __('admin.cannot_change') }}</span>
        </div>
        <div style="display:flex;align-items:center;gap:16px;padding:12px 0;">
            <div style="width:56px;height:56px;border-radius:50%;
                background:linear-gradient(135deg,#7c3aed,#4f46e5);
                display:flex;align-items:center;justify-content:center;
                font-size:22px;font-weight:700;color:#fff;flex-shrink:0;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <div style="font-size:18px;font-weight:700;color:var(--text-primary);">{{ $user->name }}</div>
                <div style="font-size:13px;color:var(--text-secondary);margin-top:2px;">ID #{{ $user->id }}</div>
                <div class="flex gap-10 items-center" style="margin-top:8px;">
                    <span class="badge badge-green">{{ $user->transactions_count }} {{ __('admin.transactions') }}</span>
                    <span class="badge badge-yellow">{{ $user->debts_count }} {{ __('admin.debts') }}</span>
                    <span class="text-muted">{{ __('admin.joined') }} {{ $user->created_at?->format('d M Y') }}</span>
                </div>
            </div>
        </div>
        <div style="margin-top:12px;padding:10px 14px;background:rgba(245,158,11,0.07);border:1px solid rgba(245,158,11,0.15);border-radius:8px;font-size:12.5px;color:#f59e0b;">
            🔒 {{ __('admin.name_locked_info') }}
        </div>
    </div>

    <!-- Edit Form -->
    <div class="card">
        <div class="card-title" style="margin-bottom:20px;">✏️ {{ __('admin.change_data') }}</div>

        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">{{ __('admin.user_email') }}</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required placeholder="email@example.com">
                @error('email') <div class="form-error">⚠️ {{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('admin.role_access') }}</label>
                <select name="role" class="form-control" required>
                    <option value="user"  {{ old('role', $user->role) === 'user'  ? 'selected' : '' }}>👤 {{ __('admin.role_user_label') }}</option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>🛡️ {{ __('admin.role_admin_label') }}</option>
                </select>
                @error('role') <div class="form-error">⚠️ {{ $message }}</div> @enderror
                <div class="form-hint">⚠️ {{ __('admin.role_admin_warning') }}</div>
            </div>

            <div style="border-top:1px solid var(--border);margin:24px 0;"></div>
            <div style="font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:16px;">🔑 {{ __('admin.change_password') }}</div>

            <div class="form-group">
                <label class="form-label">{{ __('admin.new_password') }}</label>
                <input type="password" name="password" class="form-control" placeholder="{{ __('admin.new_password_hint') }}" autocomplete="new-password">
                @error('password') <div class="form-error">⚠️ {{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('admin.confirm_new') }}</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="{{ __('admin.confirm_new_hint') }}" autocomplete="new-password">
            </div>

            <div class="flex gap-10 items-center" style="margin-top:8px;">
                <button type="submit" class="btn btn-primary">💾 {{ __('admin.save_changes') }}</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">{{ __('admin.cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
