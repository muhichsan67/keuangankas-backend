<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminTrashWebController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Middleware\EnsureAdminForWeb;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('admin.dashboard'));

// =============================================
// Locale Switch (accessible without admin auth)
// =============================================
Route::post('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['id', 'en'])) {
        session()->put('locale', $locale);
    }
    return back();
})->name('locale.switch');

// =============================================
// Admin Routes (with locale middleware)
// =============================================
Route::prefix('admin')->name('admin.')->middleware([SetLocale::class])->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');

    // Protected admin routes — EnsureAdminForWeb handles auth + role check
    Route::middleware([EnsureAdminForWeb::class])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/',           [AdminUserController::class, 'index'])->name('index');
            Route::get('/create',     [AdminUserController::class, 'create'])->name('create');
            Route::post('/',          [AdminUserController::class, 'store'])->name('store');
            Route::get('/{id}/edit',  [AdminUserController::class, 'edit'])->name('edit');
            Route::put('/{id}',       [AdminUserController::class, 'update'])->name('update');
        });

        // Trash Management
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::get('/', [AdminTrashWebController::class, 'index'])->name('index');
            Route::post('/transactions/{id}/restore', [AdminTrashWebController::class, 'restoreTransaction'])->name('transactions.restore');
            Route::post('/debts/{id}/restore', [AdminTrashWebController::class, 'restoreDebt'])->name('debts.restore');
            Route::delete('/transactions/{id}', [AdminTrashWebController::class, 'forceDeleteTransaction'])->name('transactions.force-delete');
            Route::delete('/debts/{id}', [AdminTrashWebController::class, 'forceDeleteDebt'])->name('debts.force-delete');
        });
    });
});
