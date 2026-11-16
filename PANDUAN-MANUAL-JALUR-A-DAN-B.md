# Panduan membuat ulang project latihan SIPustaka secara manual

Panduan ini menjelaskan urutan kerja, perintah terminal, dan file yang harus diisi. Source lengkap yang bisa dijadikan acuan ada di folder `Jalur-A-Blade-jQuery` dan `Jalur-B-React-Inertia`. Buat project latihan baru di lokasi lain; jangan menimpa folder contoh.

> Kerjakan untuk belajar sebelum asesmen. Saat asesmen, ikuti ketentuan dosen tentang penggunaan AI dan gunakan catatan yang memang diizinkan.

## Persiapan umum (Windows / XAMPP)

1. Nyalakan **MySQL** dari XAMPP.
2. Buat database kosong bernama `sipustaka` lewat `http://localhost/phpmyadmin`.
3. Pastikan PHP 8.2+, Composer, Node.js/NPM, dan ekstensi PHP `pdo_mysql`, `fileinfo`, `mbstring`, `openssl` tersedia. Cek:

```powershell
php -v
composer -V
node -v
npm -v
```

4. Semua perintah artisan dijalankan dari direktori root Laravel (direktori yang berisi file `artisan`).

---

# Jalur A: Laravel HMVC + Blade + jQuery

## 1. Buat kerangka Laravel dan pasang HMVC

Di PowerShell, pilih folder kerja (contoh `C:\xampp\htdocs\latihan-pabw-a`):

```powershell
cd C:\xampp\htdocs
composer create-project laravel/laravel latihan-pabw-a
cd latihan-pabw-a
composer require nwidart/laravel-modules
php artisan module:install
php artisan module:make Pustaka
```

Jika perintah `module:make` belum dikenal, cek bahwa `composer require` selesai, lalu jalankan `composer dump-autoload` dan ulangi. Baca juga `php artisan list module` untuk nama perintah modul yang tersedia pada versi paket yang terpasang.

## 2. Konfigurasikan `.env` dan database

Salin file contoh jika `.env` belum dibuat:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Buka `.env` di editor, isi/sesuaikan bagian ini sebelum migrasi:

```dotenv
APP_NAME=SIPustaka
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipustaka
DB_USERNAME=root
DB_PASSWORD=
```

`DB_USERNAME` dan `DB_PASSWORD` harus mengikuti konfigurasi MySQL lokal. Jangan tulis kredensial database di file lain.

## 3. Rancang tabel lalu isi migration

Buat migration dan model modul. Cara paling mudah meniru contoh adalah gunakan perintah generator, lalu pastikan file yang dihasilkan berada di `Modules/Pustaka`:

```powershell
php artisan module:make-migration CreateCategoriesTable Pustaka
php artisan module:make-migration CreateBooksTable Pustaka
php artisan module:make-migration CreateLoansTable Pustaka
```

Buka migration di `Modules/Pustaka/database/migrations/` dan isi urutan skema:

1. `create_categories_table`: `id`, `name`, timestamps.
2. `create_books_table`: `id`, `category_id` sebagai foreign key ke `categories`, kode/judul/penulis/stok, `cover_path` nullable, timestamps.
3. `create_loans_table`: `id`, `book_id` foreign key ke `books`, `user_id` foreign key ke `users`, tanggal pinjam/kembali, status, timestamps.

Pastikan tabel induk dibuat sebelum tabel yang memiliki foreign key. `down()` menghapus tabel dengan urutan kebalikan.

## 4. Isi Eloquent model dan relasi

Buat file model di `Modules/Pustaka/app/Models/` (bisa gunakan `php artisan module:make-model Book Pustaka`, dan seterusnya). Isi:

- `Category.php`: `$fillable` untuk nama; `books(): hasMany(Book::class)`.
- `Book.php`: `$fillable` untuk kolom buku; `category(): belongsTo(Category::class)`; `loans(): hasMany(Loan::class)`.
- `Loan.php`: `$fillable` untuk foreign key/tanggal/status; `book(): belongsTo(Book::class)` dan `user(): belongsTo(User::class)`.
- `app/Models/User.php`: tambahkan role ke `$fillable` sesuai rancangan dan `loans(): hasMany(Loan::class)`.

Gunakan namespace `Modules\\Pustaka\\Models` sesuai file model contoh. Relasi model harus sesuai nama foreign key di migration.

## 5. Role, autentikasi, middleware, dan seeder

Isi file-file berikut sebelum route yang membutuhkan login:

- `database/migrations/...add_role_to_users_table.php`: tambahkan kolom `role` dengan nilai default `mahasiswa`.
- `app/Http/Middleware/RoleMiddleware.php`: cek apakah user login dan rolenya termasuk role yang diizinkan; jika tidak, abort dengan status 403.
- `bootstrap/app.php`: daftarkan alias middleware `role` pada konfigurasi middleware Laravel 12.
- `app/Http/Controllers/AuthController.php`: implementasikan form register/login, validasi, `Hash::make`, `Auth::attempt`, session regenerate, dan logout dengan invalidate/regenerate token.
- `database/seeders/DatabaseSeeder.php`: buat user admin dan mahasiswa menggunakan `Hash::make('password')`; jangan lupa role masing-masing.
- `app/Http/Controllers/DashboardController.php`: tampilkan dashboard berdasar user/role.
- `routes/web.php`: route halaman login/register, proses login/register/logout, serta dashboard dengan middleware `auth`.
- `resources/views/auth/login.blade.php`, `register.blade.php`, `dashboard.blade.php`: isi form dan tampilan yang memakai route bernama tadi.
- `resources/views/errors/403.blade.php`: isi halaman akses ditolak.

Jangan hanya menyembunyikan tombol berdasarkan role. Batasi route di server memakai middleware.

## 6. Isi controller, Form Request, dan route modul

Buat request/controller di dalam modul. Perintah generator yang tersedia bisa dicek lewat `php artisan list module`:

```powershell
php artisan module:make-controller BookController Pustaka
php artisan module:make-controller LoanController Pustaka
php artisan module:make-request SaveBookRequest Pustaka
php artisan module:make-request SaveLoanRequest Pustaka
```

Isi file berikut:

- `Modules/Pustaka/app/Http/Requests/SaveBookRequest.php`: aturan required/string/unique/numeric, validasi gambar, serta `authorize()` sesuai role.
- `SaveLoanRequest.php`: validasi `book_id` exists, tanggal dan aturan peminjaman.
- `BookController.php`: `index` dengan search + `paginate()`; `store`, `update`, `destroy`; upload/delete sampul menggunakan `Storage::disk('public')`; untuk request AJAX kembalikan JSON.
- `LoanController.php`: muat relasi buku/pengguna; buat peminjaman dengan user login; admin dapat mengubah status; validasi kepemilikan sebelum mahasiswa menghapus permintaan.
- `Modules/Pustaka/routes/web.php`: daftar route bernama untuk buku dan pinjaman; kelompokkan dengan `auth` lalu batasi operasi admin dengan `role:admin`.
- `Modules/Pustaka/module.json`, `Modules/Pustaka/app/Providers/PustakaServiceProvider.php`: pastikan provider memuat route dan migration modul.
- `module_statuses.json`: pastikan modul `Pustaka` aktif. Periksa ejaan/kapitalisasi sesuai file yang dibaca konfigurasi paket.
- `composer.json`: pastikan PSR-4 `Modules\\Pustaka\\` menunjuk ke `Modules/Pustaka/app/`; sesudah mengubahnya jalankan `composer dump-autoload`.

## 7. Buat layout dan halaman Blade

Isi file tampilan dalam urutan ini:

1. `resources/views/layouts/app.blade.php`: HTML dasar, navbar, slot konten, Bootstrap 5, jQuery, CSRF meta, tema gelap.
2. `resources/views/books/index.blade.php`: form buku, pencarian, tabel, pagination, tombol edit/hapus, tempat notifikasi dan indikator loading.
3. `resources/views/loans/index.blade.php`: form peminjaman dan tabel transaksi.
4. `resources/views/errors/403.blade.php`: tampilan error 403.

Untuk CRUD biasa, form harus punya `@csrf`; form update juga memakai `@method('PUT')`, delete memakai `@method('DELETE')`. Untuk AJAX multipart, form harus punya input file dan JavaScript membaca `new FormData(form)`.

## 8. Hubungkan jQuery AJAX dan upload

Letakkan JavaScript di bawah layout/halaman Blade atau file JS yang dimuat Vite. Urutannya:

1. Tambahkan token CSRF pada header AJAX dari meta tag `csrf-token`.
2. Tangani submit form, panggil `event.preventDefault()`.
3. Buat objek `FormData(form)`; jangan set `contentType`/`processData` ke string untuk upload.
4. Kirim ke route bernama yang sesuai, proses JSON sukses/error, tampilkan validasi, notifikasi dan loading.
5. Untuk delete, tampilkan `confirm()` sebelum request.

## 9. Migrasi, seeder, asset, dan jalankan

```powershell
composer dump-autoload
php artisan optimize:clear
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000`. Untuk mengulang database latihan dari awal (menghapus seluruh data database ini): `php artisan migrate:fresh --seed`.

---

# Jalur B: Laravel HMVC + React + Inertia + Fetch

Jalur B memiliki backend Laravel, migration, model, middleware, controller, module dan route yang sama konsepnya dengan Jalur A. Yang berbeda adalah adaptor Inertia, halaman React, serta request Fetch. Kerjakan bagian umum/database/role lebih dulu mengikuti urutan Jalur A, lalu tambahkan langkah khusus berikut.

## 1. Buat project dan pasang paket

```powershell
cd C:\xampp\htdocs
composer create-project laravel/laravel latihan-pabw-b
cd latihan-pabw-b
composer require nwidart/laravel-modules inertiajs/inertia-laravel
npm install
npm install react react-dom @inertiajs/react
npm install -D @vitejs/plugin-react
php artisan module:install
php artisan module:make Pustaka
```

Atur `.env` dengan nilai MySQL yang sama seperti Jalur A. Jangan menjalankan dua project dengan folder root yang sama.

## 2. Konfigurasikan Inertia dan Vite

Isi/ubah file berikut sebelum membuat halaman React:

- `bootstrap/app.php`: daftarkan middleware `HandleInertiaRequests` pada grup web dan alias `role`.
- `app/Http/Middleware/HandleInertiaRequests.php`: set root view ke `app` dan bagikan data global seperlunya (misalnya user login/flash).
- `resources/views/app.blade.php`: root HTML Inertia dengan `@vite(...)` dan `@inertiaHead`, `@inertia`.
- `resources/js/app.jsx`: buat aplikasi Inertia React dan resolver halaman React.
- `vite.config.js`: aktifkan plugin React bersama Laravel Vite plugin dan entry `resources/js/app.jsx`.
- `package.json`: pastikan script `dev` dan `build` menjalankan Vite.

## 3. Bangun backend seperti Jalur A

Buat migration untuk kategori, buku, peminjaman dan role; isi model/relasi, seeder, AuthController, RoleMiddleware, Form Request, controller, serta `Modules/Pustaka/routes/web.php` dengan pola pada bagian Jalur A. Pada controller halaman, gunakan `Inertia::render('Books/Index', props)` atau `Inertia::render('Loans/Index', props)`. Untuk endpoint Fetch, kembalikan `response()->json(...)` dan HTTP status yang sesuai.

Pastikan data props berisi struktur yang dipakai React (contoh `books`, `categories`, `filters`, `auth`). Field/prop frontend dan backend harus memiliki nama yang sama.

## 4. Buat halaman React dan isi komponennya

Isi halaman ini:

- `resources/js/Pages/Auth/Login.jsx` dan `Register.jsx`: form autentikasi yang submit ke route Laravel/Inertia.
- `resources/js/Pages/Dashboard.jsx`: dashboard yang menampilkan informasi sesuai role.
- `resources/js/Pages/Layouts/AppLayout.jsx`: navbar/link antarmodul, nama user, logout, mode gelap, area konten.
- `resources/js/Pages/Books/Index.jsx`: pencarian, tabel, pagination, form create/update, upload sampul, delete confirmation.
- `resources/js/Pages/Loans/Index.jsx`: form transaksi, status, daftar peminjaman, loading/notifikasi.

Gunakan Inertia untuk navigasi halaman dan props awal; gunakan `fetch()` untuk operasi tanpa refresh. Untuk upload file, buat `FormData`, tambahkan field yang diperlukan, sertakan header `X-CSRF-TOKEN`, dan jangan set `Content-Type` sendiri karena browser mengatur boundary multipart. Tampilkan pesan validasi dari respons JSON.

Pastikan string nama halaman dalam `Inertia::render()` cocok dengan lokasi halaman. Contoh `Inertia::render('Books/Index')` harus dapat di-resolve ke `resources/js/Pages/Books/Index.jsx` oleh resolver di `app.jsx`.

## 5. Route dan otorisasi

Isi `Modules/Pustaka/routes/web.php` dengan route halaman di balik `auth`; batasi route mutasi yang hanya untuk admin dengan `role:admin`. Route pinjaman harus memeriksa kepemilikan di backend. Jangan mengandalkan tombol yang disembunyikan di React sebagai pengaman. Isi halaman `resources/views/errors/403.blade.php` untuk respons terlarang.

## 6. Jalankan Jalur B

```powershell
composer dump-autoload
php artisan optimize:clear
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000`. Saat mengubah React, gunakan dua terminal dari root project:

Terminal 1:

```powershell
npm run dev
```

Terminal 2:

```powershell
php artisan serve
```

## Urutan file yang paling aman untuk diisi

Pada kedua jalur, kerjakan dengan urutan: **`.env` → migration → model/relasi → seeder → middleware/auth → Form Request → controller → route → layout → halaman UI → AJAX/Fetch → build/migrate/run**. Kalau ada error, cocokkan dulu nama route, namespace, nama view/komponen, field form, dan kolom database.

## Pakai source contoh sebagai referensi isi

Source Jalur A ada di `outputs/Jalur-A-Blade-jQuery`; source Jalur B ada di `outputs/Jalur-B-React-Inertia`. Saat membuat manual, buka file contoh dengan nama yang sama lalu ketik/pahami isinya ke file di project latihan baru. Jangan salin folder `vendor` atau `node_modules`; Composer dan NPM membuatnya dari lockfile/package manifest.
