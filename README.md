# KeluargaKas Backend

> Backend API & Admin Panel untuk aplikasi manajemen keuangan keluarga **KeluargaKas**, dibangun dengan **Laravel 13** + **MySQL / PostgreSQL Supabase**.

---

## 🚀 Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Framework | Laravel 13 |
| Database | MySQL (dev) · PostgreSQL/Supabase (prod) |
| Auth API | Laravel Sanctum |
| File Storage | Local `public` disk · Supabase Storage |
| API Docs | L5-Swagger (OpenAPI 3.0) |
| Testing | PHPUnit · 7 tests · 26 assertions |

---

## ⚡ Quick Start

### 1. Clone & Install
```bash
git clone <repo-url> keluargakas/backend
cd keluargakas/backend
composer install
```

### 2. Konfigurasi Environment
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` sesuai kebutuhan (lihat [Konfigurasi](#konfigurasi) di bawah).

### 3. Migrasi & Seeder
```bash
php artisan migrate
php artisan db:seed --class=UserSeeder
```

### 4. Storage Link (untuk local file upload)
```bash
php artisan storage:link
```

### 5. Jalankan Server
```bash
php artisan serve
```

---

## 🔧 Konfigurasi

Semua konfigurasi cukup diubah di file `.env` — **tanpa perlu mengubah kode**.

### Database

```env
# MySQL (development)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=keluargakas
DB_USERNAME=root
DB_PASSWORD=

# PostgreSQL / Supabase (production) → uncomment & ganti DB_CONNECTION
# DB_CONNECTION=pgsql
# DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com
# DB_PORT=6543
# DB_DATABASE=postgres
# DB_USERNAME=postgres.your-project-ref
# DB_PASSWORD=your-password
# DB_SSLMODE=require
```

### File Storage Kuitansi

```env
# Lokal → disimpan di storage/app/public/transactions/
# URL   → http://localhost:8000/storage/transactions/{file}
RECEIPT_DISK=public

# Supabase Storage → ganti ke 'supabase' dan isi kredensial di bawah
RECEIPT_DISK=supabase
SUPABASE_URL=https://your-project-ref.supabase.co
SUPABASE_KEY=your-anon-key
SUPABASE_SECRET=your-service-role-key
SUPABASE_BUCKET=transaction-receipts
```

---

## 🗂️ Struktur Arsitektur

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/               # Web admin panel controllers
│   │   │   ├── AdminAuthController.php
│   │   │   ├── AdminDashboardController.php
│   │   │   ├── AdminUserController.php
│   │   │   ├── AdminTrashWebController.php
│   │   │   └── TrashController.php  (API)
│   │   └── Api/
│   │       ├── DebtController.php
│   │       └── TransactionController.php
│   ├── Middleware/
│   │   ├── EnsureUserIsAdmin.php    # API: return 403 JSON
│   │   ├── EnsureAdminForWeb.php    # Web: redirect ke login
│   │   └── LogUserActivity.php      # Global: catat setiap request
│   ├── Requests/
│   │   ├── StoreDebtRequest.php
│   │   ├── StoreTransactionRequest.php  # Anti-IDOR: debt_id scope
│   │   └── UpdateUserRequest.php        # Admin: kecuali name/id
│   └── Resources/
│       ├── DebtResource.php
│       ├── TransactionResource.php  # URL receipt otomatis dari disk aktif
│       └── UserResource.php
├── Listeners/
│   ├── LogSuccessfulLogin.php
│   └── LogSuccessfulLogout.php
├── Models/
│   ├── User.php          # role: admin | user
│   ├── Debt.php          # SoftDeletes · expenses() relation
│   ├── Transaction.php   # SoftDeletes
│   └── ActivityLog.php
└── Services/
    ├── ActivityLogService.php
    ├── DebtService.php          # withSum() di level DB
    └── TransactionService.php   # DB::transaction() + file upload
```

---

## 🌐 API Endpoints

Base URL: `http://localhost:8000/api`

Semua endpoint dilindungi `auth:sanctum` + `throttle:api` (60 req/menit).

### Debts
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/debts` | Daftar hutang + total cicilan terbayar |
| `POST` | `/debts` | Tambah hutang baru |
| `DELETE` | `/debts/{id}` | Soft delete hutang |

### Transactions
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/transactions` | Daftar transaksi |
| `POST` | `/transactions` | Tambah transaksi (+ upload kuitansi) |
| `DELETE` | `/transactions/{id}` | Soft delete transaksi |

### Admin (role = admin)
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/admin/trash` | Lihat semua data ter-soft delete |
| `POST` | `/admin/trash/transactions/{id}/restore` | Pulihkan transaksi |
| `POST` | `/admin/trash/debts/{id}/restore` | Pulihkan hutang |
| `DELETE` | `/admin/trash/transactions/{id}` | Hard delete + hapus file |
| `DELETE` | `/admin/trash/debts/{id}` | Hard delete hutang |

📖 **Swagger UI:** `http://localhost:8000/api/documentation`

---

## 🛡️ Admin Panel (Web UI)

Akses: `http://localhost:8000/admin/login`

| Halaman | URL | Fitur |
|---------|-----|-------|
| Login | `/admin/login` | Autentikasi session admin |
| Dashboard | `/admin/dashboard` | Stats, cash flow, activity log |
| Users | `/admin/users` | Daftar semua pengguna |
| Edit User | `/admin/users/{id}/edit` | Update email/role/password (name & id terkunci) |
| Trash | `/admin/trash` | Restore atau hard delete transaksi & hutang |

### Akun Default (setelah seeder)

| Role | Email | Password |
|------|-------|----------|
| 🛡️ Admin | `admin@keluargakas.app` | `password` |
| 👤 User | `ichsan@keluargakas.app` | `password` |

---

## 🔒 Fitur Keamanan

| Fitur | Implementasi |
|-------|-------------|
| **Anti-IDOR** | `debt_id` divalidasi milik user login via `Rule::exists` |
| **Audit Trail** | Setiap Login/Logout/Interaksi dicatat ke `activity_logs` |
| **Rate Limiting** | 60 req/menit per user/IP via `throttle:api` |
| **Soft Delete** | Transaksi & hutang tidak langsung hilang |
| **Hard Delete** | Hanya admin · file kuitansi ikut terhapus dari storage |
| **Admin Guard** | API: JSON 403 · Web: redirect ke login |
| **Suspicious Log** | Percobaan IDOR/akses admin dicatat sebagai `SUSPICIOUS_ACTIVITY` |

---

## 🧪 Testing

```bash
php artisan test
```

| Skenario | Hasil |
|----------|-------|
| ✅ Happy Path: transaksi + upload kuitansi | PASS |
| ✅ Edge Case: transaksi tanpa kuitansi (nullable) | PASS |
| ✅ IDOR Attack: user A pakai debt milik user B | PASS (422) |
| ✅ Unauthenticated access ke API | PASS (401) |
| ✅ Non-admin akses `/admin/trash` | PASS (403 + log) |

---

## 📁 Struktur File Upload

```
storage/
└── app/
    └── public/
        └── transactions/       ← kuitansi tersimpan di sini (RECEIPT_DISK=public)
            └── {uuid}.jpg
public/
└── storage → symlink ke storage/app/public/
```

File diakses via: `http://localhost:8000/storage/transactions/{uuid}.jpg`

---

## 📦 Dependencies

```json
{
  "laravel/sanctum": "^4.3",
  "darkaonline/l5-swagger": "^11.0",
  "league/flysystem-aws-s3-v3": "^3.34"
}
```

---

## 📝 Lisensi

Proyek ini untuk kegunaan pribadi / internal keluarga.
