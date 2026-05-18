<?php

use App\Http\Controllers\Admin\TrashController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DebtController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - KeluargaKas
|--------------------------------------------------------------------------
*/

// =============================================
// Auth Routes (public — tidak perlu token)
// =============================================
Route::prefix('auth')->name('auth.')->middleware('throttle:api')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login',    [AuthController::class, 'login'])->name('login');

    // Logout & me butuh token
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/me',      [AuthController::class, 'me'])->name('me');
    });
});

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {

    // === Debt Routes ===
    Route::prefix('debts')->group(function () {
        Route::get('/', [DebtController::class, 'index']);
        Route::post('/', [DebtController::class, 'store']);
        Route::delete('/{id}', [DebtController::class, 'destroy']);
    });

    // === Transaction Routes ===
    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index']);
        Route::post('/', [TransactionController::class, 'store']);
        Route::delete('/{id}', [TransactionController::class, 'destroy']);
    });

    // === Admin Routes (role = admin only) ===
    Route::prefix('admin')->middleware(EnsureUserIsAdmin::class)->group(function () {
        Route::get('/trash', [TrashController::class, 'index']);

        Route::post('/trash/transactions/{id}/restore', [TrashController::class, 'restoreTransaction']);
        Route::post('/trash/debts/{id}/restore', [TrashController::class, 'restoreDebt']);

        Route::delete('/trash/transactions/{id}', [TrashController::class, 'forceDeleteTransaction']);
        Route::delete('/trash/debts/{id}', [TrashController::class, 'forceDeleteDebt']);
    });
});
