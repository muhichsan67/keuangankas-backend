# AI Context: KeluargaKas Backend (Laravel 13 + Supabase Master Rules)

## 1. Lingkungan & Arsitektur Utama (Clean Architecture)
AI WAJIB mematuhi prinsip *Separation of Concerns* (Pemisahan Tanggung Jawab) untuk membangun sistem yang aman dan *scalable*. Struktur kode harus dipecah menjadi layer berikut:
- **Presentation Layer (Controllers & Requests):** Controller HANYA bertindak sebagai pengatur arus lalu lintas data. Tidak boleh ada query database mentah atau logika bisnis di sini. Validasi input wajib dipisah ke dalam **Form Request**.
- **Domain/Business Logic Layer (Services):** Semua aturan bisnis diisolasi di dalam folder `App\Services\` (misal: `TransactionService`, `DebtService`).
- **Data Access Layer (Eloquent Models & Migrations):** Repositori atau Model Eloquent murni menangani pengambilan data.
- **Data Transport Layer (API Resources):** Semua output JSON ke Vue.js wajib ditransformasikan melalui **Laravel API Resources**.

## 2. Integrasi Database & Media Storage (Supabase)
- **Database:** Menggunakan Supabase (PostgreSQL) via Transaction Pooler (Port 6543).
- **Tipe Data Keuangan:** Kolom nominal uang (`amount`, `monthly_cost`) wajib menggunakan tipe `$table->decimal('column', 15, 2)` di migration untuk akurasi nilai Rupiah. Dilarang menggunakan float/double.
- **Media Storage:** File bukti kuitansi disimpan di Supabase Storage Bucket bernama `transaction-receipts`. Operasi file wajib menggunakan facade `Storage::disk('supabase')`.

## 3. Fitur Siklus Data (Timestamps, Soft Deletes & Hard Delete)
- **Timestamps & Soft Deletes:** Tabel `transactions` dan `debts` wajib mendukung `$table->timestamps()` dan `$table->softDeletes()`. Penghapusan oleh user biasa hanya berupa *Soft Delete* (`$model->delete()`).
- **Admin Control (Hard Delete & Recovery):** Melihat data terhapus (`onlyTrashed()`), memulihkan data (`$model->restore()`), dan menghapus permanen (`$model->forceDelete()`) hanya boleh diakses oleh user dengan role `admin`. Jika transaksi yang memiliki file kuitansi di-hard delete, file tersebut wajib dihapus dari bucket via `Storage::disk('supabase')->delete()`.

## 4. Keamanan Ketat & Validasi (Anti-IDOR)
- **Pencegahan IDOR:** Semua query manipulasi data (Select, Update, Delete) wajib di-scope berdasarkan user terautentikasi (`auth()->user()->...`). Saat input data, pastikan `debt_id` yang dikirim benar-benar milik user yang sedang login.
- **Rate Limiting:** Semua API routes wajib dilindungi oleh middleware `throttle:api` untuk mencegah serangan brute-force.
- **Validasi File:** Upload gambar kuitansi bersifat opsional (`nullable`), wajib divalidasi dengan aturan: `image|mimes:jpeg,png,jpg,webp|max:5120` (Maksimal 5MB) dan nama file di-generate unik memakai UUID.

## 5. Sistem Audit Trail & Aktivitas (Logging)
Setiap aktivitas user wajib dicatat ke dalam tabel `activity_logs` melalui `App\Services\ActivityLogService`:
- **Auth Events:** Catat saat event `Login` dan `Logout` sukses terjadi melalui Laravel Event Listeners.
- **Interaksi Menu/API:** Buat *Global Middleware* `LogUserActivity` untuk mencatat aktivitas user (URL, HTTP Method, IP Address, dan Browser User Agent) pada setiap request.

## 6. Standar Dokumentasi API (Swagger / OpenAPI)
Setiap API endpoint baru wajib menyertakan anotasi PHP 8+ Attributes (`#[OA\Post]`, `#[OA\Property]`, dll) dari package `darkaonline/l5-swagger` tepat di atas method Controller yang mencakup:
- Deskripsi fungsi, RequestBody dengan tipe properti, dan penanda `required`.
- Representasi response kode `200/201` (Success), `421/422` (Validation Error), dan `401/403` (Unauthorized/Forbidden).

## 7. Strategi Pengujian (3-Tier Testing)
Setiap fitur baru wajib dibuatkan Feature/Unit Test menggunakan PHPUnit/Pest yang mencakup tiga skenario berikut:
1. **Best Case Scenario (Happy Path):** Pengujian dengan input data sempurna dan user berhak. Menghasilkan status `200/201`, data masuk database, file terupload, dan response JSON valid.
2. **Middle Case Scenario (Edge Cases):** Pengujian dengan kondisi opsional (misal: upload gambar dikosongkan karena `nullable`, nominal menyentuh batas maksimum, atau memicu alarm rasio hutang). Sistem harus berjalan sukses tanpa *crash*.
3. **Worst Case Scenario (Sad Path / Security Breach):** Pengujian gagal validasi, kegagalan database (harus memicu `DB::rollBack`), atau skenario serangan IDOR (User A mencoba mengakses/menghapus data User B). Harus mengembalikan status `422` atau `403` dan mencatat aktivitas mencurigakan ke `activity_logs`.