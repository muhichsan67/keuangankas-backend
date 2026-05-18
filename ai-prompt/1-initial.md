Bertindaklah sebagai Lead Backend Architect & Senior Security Engineer Laravel 13. Berdasarkan aturan ketat yang tertera pada file `.cursorrules` / `ai-backend-context.md`, buatkan SELURUH struktur database (migrations), Model Eloquent, Form Requests, Services, API/Web Controllers, API Resources, dokumentasi Swagger, hingga unit testing untuk backend aplikasi "KeluargaKas" secara utuh, terintegrasi, dan siap pakai.

Jangan gunakan placeholder atau komentar kosong seperti "// Tulis logika di sini". Hasilkan seluruh kode secara penuh untuk 6 lapisan sistem berikut:

---

### LAYER 1: DATA DEFINITION & LIFECYCLE (Migrations & Models)
Buat file migration dan Model Eloquent lengkap dengan properti, $casts, serta relasi PostgreSQL/Supabase:
a. Tabel 'users': Standard Laravel Auth + kolom 'role' (enum: 'admin', 'user', default: 'user').
b. Tabel 'debts': id (bigint/uuid), user_id, source (string), monthly_cost (decimal 15,2), monthly_deadline (integer 1-31), total_tenor (integer), timestamps, dan softDeletes.
c. Tabel 'transactions': id, user_id, debt_id (nullable), type (enum: 'in', 'out'), amount (decimal 15,2), category (string), date (date), description (text, nullable), receipt_url (string, nullable), timestamps, dan softDeletes.
d. Tabel 'activity_logs': id, user_id (nullable), action (string), description (text), ip_address (string), user_agent (text), created_at (timestamp saja).

*Aturan Relasi:* Model 'Debt' harus memiliki relasi hasMany bernama 'expenses()' yang otomatis memfilter 'Transaction' dengan 'type = "out"'. Semua model transaksional wajib memakai trait 'SoftDeletes'.

---

### LAYER 2: SECURITY, AUDIT TRAIL, & ACTIVITY LOGGING
Aplikasi harus mencatat jejak digital pengguna secara otomatis dan mengamankan server:
a. App/Services/ActivityLogService: Buat service dengan method `log($action, $description)` untuk mencatat log ke tabel 'activity_logs'.
b. Auth Event Listeners: Buat listener untuk menangani event bawaan Laravel 'Illuminate\Auth\Events\Login' dan 'Logout'. Panggil `ActivityLogService` untuk otomatis mencatat aksi 'LOGIN' dan 'LOGOUT'.
c. App/Http/Middleware/LogUserActivity: Buat global middleware untuk mendeteksi semua interaksi menu/API. Catat ke 'activity_logs' dengan aksi 'INTERACTION' beserta detail URL dan HTTP Method (GET/POST/DELETE) yang sedang diakses.
d. API Rate Limiting: Bungkus semua route API dengan middleware 'throttle:api' untuk proteksi brute-force.

---

### LAYER 3: VALIDATION & DATA INTEGRATION (Form Requests)
Terapkan pencegahan IDOR (Insecure Direct Object Reference) secara ketat pada input data:
a. App/Http/Requests/StoreTransactionRequest: Aturan validasi untuk menyimpan transaksi baru. 'debt_id' bersifat nullable, tetapi JIKA diisi, wajib divalidasi menggunakan Rule::exists yang memastikan bahwa debt_id tersebut terdaftar DAN merupakan hak milik dari `auth()->id()`. Input file 'receipt' wajib divalidasi sebagai `image|mimes:jpeg,png,jpg,webp|max:5120`.
b. App/Http/Requests/StoreDebtRequest: Aturan validasi input hutang baru (source wajib, monthly_cost numerik positif, total_tenor integer positif).

---

### LAYER 4: BUSINESS LOGIC ENGINE (Services)
Isolasi semua business logic dari controller ke dalam layer Service:
a. App/Services/TransactionService: Buat method `createExpense($data)`. Proses wajib dibungkus dalam `DB::transaction()`. Jika ada file 'receipt', upload ke Supabase Storage Bucket 'transaction-receipts' via `Storage::disk("supabase")` ke dalam folder 'receipts', generate nama file unik memakai UUID, lalu simpan public URL-nya ke kolom 'receipt_url'.
b. App/Services/DebtService: Buat method `getDebtsForUser()`. Wajib menggunakan query agregat database `withSum('expenses as total_paid', 'amount')` agar total cicilan yang telah dibayarkan dihitung secara instan dan efisien di level database PostgreSQL Supabase sebelum dikirim ke frontend.

---

### LAYER 5: PRESENTATION & DATA TRANSPORT (Controllers & Resources)
a. App/Http/Resources/TransactionResource & DebtResource: Format output JSON agar seragam dan aman, menyembunyikan raw data internal (seperti password atau kolom database sensitif).
b. App/Http/Controllers/Api/TransactionController & DebtController: Gunakan Dependency Injection untuk memanggil Service terkait. Amankan route menggunakan middleware 'auth:sanctum'.
c. App/Http/Controllers/Admin/TrashController: Controller khusus untuk user dengan 'role = "admin"'. Buat method 'index' untuk melihat data yang tersoft-delete (`onlyTrashed()`), 'restore($id)' untuk pemulihan, dan 'forceDelete($id)' untuk hapus permanen. JIKA transaksi yang di-hard delete memiliki 'receipt_url', hapus file fisiknya dari Supabase Bucket via `Storage::disk("supabase")->delete()` terlebih dahulu.

---

### LAYER 6: SWAGGER DOCUMENTATION & 3-TIER TESTING
a. Dokumentasi OpenAPI: Pada setiap method di API Controller (index, store, destroy), tuliskan PHP 8 Attributes lengkap menggunakan package `l5-swagger` yang mendokumentasikan deskripsi API, RequestBody, serta Response kode 200/201 (Success), 422 (Validation Error), dan 403 (Forbidden / Deteksi IDOR).
b. Automated Testing: Buat satu file Feature Test komprehensif menggunakan PHPUnit/Pest untuk menguji siklus data transaksi, mencakup 3 skenario:
   - Skenario 1 (Best Case): User terautentikasi mengirim data valid + mengunggah file kuitansi. Assert: Status 201, data masuk DB, file tersimulasikan masuk ke storage disk, dan JSON response valid.
   - Skenario 2 (Middle Case): User menyimpan transaksi namun mengosongkan file 'receipt' (karena nullable). Assert: Sistem sukses menyimpan data dengan receipt_url = null tanpa crash.
   - Skenario 3 (Worst Case / IDOR Attack): User A mencoba menembak API untuk menghapus atau menautkan transaksi ke 'debt_id' milik User B. Assert: Database tidak berubah, sistem melakukan rollback, mengembalikan status 403 Forbidden, dan secara otomatis mencatat log 'SUSPICIOUS_ACTIVITY' ke tabel 'activity_logs'.

Gabungkan seluruh komponen di atas ke dalam kode Laravel 13 yang elegan, aman, terstruktur, dan siap pakai. Tulis kodenya sekarang!