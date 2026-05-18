# 📖 Change Tutorial — KeluargaKas Backend

Panduan ini berisi **lokasi file yang wajib diupdate** untuk setiap skenario pengembangan fitur. Gunakan sebagai referensi cepat sebelum mulai coding.

---

## 📌 Cara Baca Dokumen Ini

Setiap skenario memiliki:
- 🔴 **WAJIB** — file yang harus diubah, fitur tidak akan berjalan tanpa ini
- 🟡 **DISARANKAN** — best practice, sebaiknya diupdate
- 🟢 **OPSIONAL** — hanya jika relevan

---

## 1️⃣ Menambah / Mengubah Teks (Translation)

### Lokasi File

| Priority | File | Keterangan |
|----------|------|-----------|
| 🔴 WAJIB | `lang/id/admin.php` | Teks Bahasa Indonesia |
| 🔴 WAJIB | `lang/en/admin.php` | Teks Bahasa Inggris |

### Cara Menambah Key Baru

**Step 1** — Tambahkan key di kedua file:
```php
// lang/id/admin.php
'contoh_key' => 'Teks dalam Bahasa Indonesia',

// lang/en/admin.php
'contoh_key' => 'Text in English',
```

**Step 2** — Gunakan di Blade view:
```blade
{{ __('admin.contoh_key') }}
```

> **Catatan:** Key yang ada di `id` tapi tidak ada di `en` (atau sebaliknya) akan menampilkan key mentah. Selalu update **kedua file sekaligus**.

---

## 2️⃣ Menambah Halaman Admin Baru (Web UI)

### Lokasi File

| Priority | File | Keterangan |
|----------|------|-----------|
| 🔴 WAJIB | `routes/web.php` | Daftarkan route GET + POST baru |
| 🔴 WAJIB | `app/Http/Controllers/Admin/` | Buat controller baru di sini |
| 🔴 WAJIB | `resources/views/admin/` | Buat folder + Blade view baru |
| 🟡 DISARANKAN | `lang/id/admin.php` + `lang/en/admin.php` | Tambah teks untuk halaman baru |
| 🟡 DISARANKAN | `resources/views/admin/layout.blade.php` | Tambah nav-item di sidebar jika perlu |

### Struktur Route (Contoh)
```php
// routes/web.php — di dalam group middleware EnsureAdminForWeb
Route::prefix('fitur-baru')->name('fitur-baru.')->group(function () {
    Route::get('/',         [AdminFiturBaruController::class, 'index'])->name('index');
    Route::get('/create',   [AdminFiturBaruController::class, 'create'])->name('create');
    Route::post('/',        [AdminFiturBaruController::class, 'store'])->name('store');
    Route::get('/{id}/edit',[AdminFiturBaruController::class, 'edit'])->name('edit');
    Route::put('/{id}',     [AdminFiturBaruController::class, 'update'])->name('update');
    Route::delete('/{id}',  [AdminFiturBaruController::class, 'destroy'])->name('destroy');
});
```

### Struktur View (Contoh)
```
resources/views/admin/
└── fitur-baru/
    ├── index.blade.php    ← @extends('admin.layout')
    ├── create.blade.php
    └── edit.blade.php
```

---

## 3️⃣ Menambah API Endpoint Baru

### Lokasi File

| Priority | File | Keterangan |
|----------|------|-----------|
| 🔴 WAJIB | `routes/api.php` | Daftarkan route API |
| 🔴 WAJIB | `app/Http/Controllers/Api/` | Buat API controller baru |
| 🔴 WAJIB | `app/Http/Requests/` | Buat Form Request untuk validasi |
| 🟡 DISARANKAN | `app/Http/Resources/` | Buat API Resource untuk format JSON |
| 🟡 DISARANKAN | `app/Services/` | Pisahkan business logic ke Service class |
| 🟡 DISARANKAN | `app/Http/Controllers/SwaggerController.php` | Update OpenAPI global schema jika ada model baru |

### Anti-IDOR Checklist
Setiap query yang mengambil data **harus di-scope ke `user_id` milik user yang login**:
```php
// ✅ BENAR — anti-IDOR
$data = Model::where('user_id', Auth::id())->findOrFail($id);

// ❌ SALAH — rentan IDOR
$data = Model::findOrFail($id);
```

---

## 4️⃣ Menambah Migration (Tabel / Kolom Baru)

### Lokasi File

| Priority | File | Keterangan |
|----------|------|-----------|
| 🔴 WAJIB | `database/migrations/` | File migration baru |
| 🔴 WAJIB | `app/Models/` | Update atau buat Model baru |
| 🟡 DISARANKAN | `database/factories/` | Update factory jika dipakai testing |
| 🟡 DISARANKAN | `tests/Feature/` | Update test jika schema berubah |
| 🟢 OPSIONAL | `database/seeders/UserSeeder.php` | Jika data default perlu diperbarui |

### Perintah
```bash
# Buat migration baru
php artisan make:migration add_kolom_baru_to_tabel_table

# Jalankan migration
php artisan migrate

# Rollback jika ada masalah
php artisan migrate:rollback
```

---

## 5️⃣ Menambah / Mengubah Validasi

### Lokasi File

| Priority | File | Keterangan |
|----------|------|-----------|
| 🔴 WAJIB | `app/Http/Requests/` | File Form Request yang relevan |

### File Request yang Ada

| File | Digunakan untuk |
|------|----------------|
| `StoreDebtRequest.php` | Tambah hutang via API |
| `StoreTransactionRequest.php` | Tambah transaksi via API (ada anti-IDOR!) |
| `UpdateUserRequest.php` | Admin update user — kecuali `name` & `id` |
| `CreateUserRequest.php` | Admin buat user baru |

---

## 6️⃣ Mengubah Storage / File Upload

### Lokasi File

| Priority | File | Keterangan |
|----------|------|-----------|
| 🔴 WAJIB | `.env` → `RECEIPT_DISK` | Switch antara `public` (lokal) dan `supabase` |
| 🟡 DISARANKAN | `config/filesystems.php` | Tambah disk baru jika butuh storage lain |
| 🟡 DISARANKAN | `app/Services/TransactionService.php` | Logic upload file ada di sini |
| 🟡 DISARANKAN | `app/Http/Resources/TransactionResource.php` | URL file di-generate di sini |
| 🟡 DISARANKAN | `app/Http/Controllers/Admin/TrashController.php` | Hapus file saat hard delete (API) |
| 🟡 DISARANKAN | `app/Http/Controllers/Admin/AdminTrashWebController.php` | Hapus file saat hard delete (Web) |

### Cara Switch Storage (`.env` saja)
```env
RECEIPT_DISK=public    # File lokal → storage/app/public/transactions/
RECEIPT_DISK=supabase  # Supabase Storage bucket
```

---

## 7️⃣ Mengubah Database

### Lokasi File

| Priority | File | Keterangan |
|----------|------|-----------|
| 🔴 WAJIB | `.env` → `DB_CONNECTION` | Switch antara `mysql` dan `pgsql` |

### Cara Switch Database (`.env` saja)
```env
# MySQL (development)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=keluargakas
DB_USERNAME=root
DB_PASSWORD=

# PostgreSQL / Supabase (production)
DB_CONNECTION=pgsql
DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.your-project-ref
DB_PASSWORD=your-password
```

---

## 8️⃣ Menambah Audit Log

### Lokasi File

| Priority | File | Keterangan |
|----------|------|-----------|
| 🔴 WAJIB | `app/Services/ActivityLogService.php` | Panggil `log($action, $description)` |

### Cara Pakai
```php
// Di mana saja — inject via constructor
public function __construct(protected ActivityLogService $activityLogService) {}

// Catat aktivitas
$this->activityLogService->log('NAMA_ACTION', 'Deskripsi detail aktivitas');
```

### Action yang Sudah Ada
| Action | Deskripsi |
|--------|-----------|
| `LOGIN` | User login |
| `LOGOUT` | User logout |
| `INTERACTION` | Setiap request (auto via middleware) |
| `SUSPICIOUS_ACTIVITY` | Percobaan IDOR atau akses ilegal |
| `CREATE_USER` | Admin buat user baru |
| `UPDATE_USER` | Admin update user |
| `RESTORE` | Admin restore data dari trash |
| `HARD_DELETE` | Admin hard delete data |

> Tambahkan action baru bebas — tidak ada enum, cukup string konsisten.

---

## 9️⃣ Menambah / Mengubah Theme (Light/Dark)

### Lokasi File

| Priority | File | Keterangan |
|----------|------|-----------|
| 🔴 WAJIB | `resources/views/admin/layout.blade.php` | CSS vars untuk `[data-theme="dark"]` dan `[data-theme="light"]` |
| 🟡 DISARANKAN | `resources/views/admin/auth/login.blade.php` | Theme terpisah di halaman login |

### Cara Tambah CSS Variable Baru
```css
/* Di dalam layout.blade.php */
:root, [data-theme="dark"] {
    --warna-baru: #nilai-dark;
}
[data-theme="light"] {
    --warna-baru: #nilai-light;
}
```
Lalu gunakan: `color: var(--warna-baru);`

---

## 🗂️ Struktur Folder Ringkas

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          ← Controller web admin panel
│   │   │   └── Api/            ← Controller API (Sanctum)
│   │   ├── Middleware/         ← Auth, locale, logging, admin guard
│   │   ├── Requests/           ← Validasi & anti-IDOR
│   │   └── Resources/          ← Format output JSON API
│   ├── Models/                 ← Eloquent models
│   ├── Providers/              ← Event listeners, rate limiter
│   └── Services/               ← Business logic (DB transaction, file upload)
├── config/
│   └── filesystems.php         ← Konfigurasi disk storage + receipt_disk
├── database/
│   ├── factories/              ← Untuk testing
│   ├── migrations/             ← Schema database
│   └── seeders/                ← Data default
├── lang/
│   ├── id/admin.php            ← 🇮🇩 Teks Bahasa Indonesia
│   └── en/admin.php            ← 🇬🇧 Teks Bahasa Inggris
├── resources/views/admin/
│   ├── layout.blade.php        ← Layout utama (sidebar, theme, lang)
│   ├── auth/login.blade.php    ← Halaman login
│   ├── dashboard.blade.php
│   ├── users/                  ← index, create, edit
│   └── trash/index.blade.php
├── routes/
│   ├── api.php                 ← API routes (Sanctum)
│   └── web.php                 ← Web admin routes
├── tests/Feature/
│   └── TransactionTest.php     ← Feature tests
└── .env                        ← ⚙️ Switch DB dan Storage di sini
```

---

## ⚡ Cheat Sheet: Perintah Penting

```bash
# Jalankan server
php artisan serve

# Jalankan semua test
php artisan test

# Buat storage symlink (wajib sekali setelah clone)
php artisan storage:link

# Migrasi database
php artisan migrate

# Isi data awal
php artisan db:seed --class=UserSeeder

# Clear semua cache
php artisan optimize:clear

# Generate Swagger docs
php artisan l5-swagger:generate
```
