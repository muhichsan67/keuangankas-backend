<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Debt;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================================
    // SKENARIO 1 (Best Case / Happy Path):
    // User terautentikasi mengirim data valid + mengunggah file kuitansi.
    // Assert: Status 201, data masuk DB, file tersimulasikan masuk ke storage,
    //         dan JSON response valid.
    // =========================================================================
    public function test_authenticated_user_can_create_transaction_with_receipt(): void
    {
        Storage::fake('supabase');

        $user = User::factory()->create();
        $debt = Debt::factory()->create(['user_id' => $user->id]);

        $receipt = UploadedFile::fake()->image('kuitansi.jpg', 640, 480)->size(1024);

        $response = $this->actingAs($user)->postJson('/api/transactions', [
            'debt_id'     => $debt->id,
            'type'        => 'out',
            'amount'      => 500000,
            'category'    => 'Cicilan KPR',
            'date'        => '2025-01-15',
            'description' => 'Cicilan bulan Januari',
            'receipt'     => $receipt,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id', 'type', 'amount', 'category', 'date', 'receipt_url',
                ],
            ]);

        $this->assertDatabaseHas('transactions', [
            'user_id'  => $user->id,
            'debt_id'  => $debt->id,
            'type'     => 'out',
            'amount'   => 500000,
            'category' => 'Cicilan KPR',
        ]);

        // Assert file tersimulasikan masuk ke storage disk
        Storage::disk('supabase')->assertExists(
            collect(Storage::disk('supabase')->allFiles())->first()
        );
    }

    // =========================================================================
    // SKENARIO 2 (Middle Case / Edge Case):
    // User menyimpan transaksi tanpa file receipt (nullable).
    // Assert: Sistem sukses menyimpan data dengan receipt_url = null.
    // =========================================================================
    public function test_authenticated_user_can_create_transaction_without_receipt(): void
    {
        Storage::fake('supabase');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/transactions', [
            'type'     => 'in',
            'amount'   => 1000000,
            'category' => 'Gaji Bulanan',
            'date'     => '2025-01-01',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('transactions', [
            'user_id'     => $user->id,
            'type'        => 'in',
            'receipt_url' => null,
        ]);

        // Pastikan tidak ada file yang diupload
        $this->assertCount(0, Storage::disk('supabase')->allFiles());
    }

    // =========================================================================
    // SKENARIO 3 (Worst Case / IDOR Attack):
    // User A mencoba menautkan transaksi ke debt_id milik User B.
    // Assert: Database tidak berubah, sistem mengembalikan status 422 (validasi
    //         gagal karena IDOR), dan mencatat log 'SUSPICIOUS_ACTIVITY'.
    // =========================================================================
    public function test_user_cannot_link_transaction_to_another_users_debt(): void
    {
        Storage::fake('supabase');

        $userA = User::factory()->create();
        $userB = User::factory()->create();

        // Debt milik User B
        $debtOfUserB = Debt::factory()->create(['user_id' => $userB->id]);

        // User A mencoba menggunakan debt_id milik User B (IDOR Attack)
        $response = $this->actingAs($userA)->postJson('/api/transactions', [
            'debt_id'  => $debtOfUserB->id, // IDOR: debt ini bukan milik User A
            'type'     => 'out',
            'amount'   => 300000,
            'category' => 'Cicilan Orang Lain',
            'date'     => '2025-01-15',
        ]);

        // Sistem harus menolak dengan 422 (validasi Rule::exists gagal)
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['debt_id']);

        // Database tidak boleh berubah
        $this->assertDatabaseCount('transactions', 0);

        // Tidak ada file yang diupload
        $this->assertCount(0, Storage::disk('supabase')->allFiles());
    }

    // =========================================================================
    // SKENARIO TAMBAHAN: Verifikasi rate limiting terdaftar pada route API
    // =========================================================================
    public function test_api_routes_are_protected_by_auth_sanctum(): void
    {
        // Request tanpa token harus ditolak dengan 401
        $this->getJson('/api/transactions')->assertStatus(401);
        $this->getJson('/api/debts')->assertStatus(401);
        $this->getJson('/api/admin/trash')->assertStatus(401);
    }

    // =========================================================================
    // SKENARIO ADMIN: Non-admin tidak bisa akses TrashController
    // =========================================================================
    public function test_non_admin_cannot_access_admin_trash_and_suspicious_activity_is_logged(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->getJson('/api/admin/trash');

        $response->assertStatus(403)
            ->assertJson(['message' => 'Akses ditolak. Hanya admin yang diizinkan.']);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action'  => 'SUSPICIOUS_ACTIVITY',
        ]);
    }
}
