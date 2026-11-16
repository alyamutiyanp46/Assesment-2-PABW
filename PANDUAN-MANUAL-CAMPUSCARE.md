# CampusCare — panduan persiapan Asesmen PABW

Aplikasi latihan pengelolaan fasilitas kampus dan pengaduan. Dua folder merupakan dua implementasi alternatif, jalankan satu jalur saja:

- **Jalur A:** Laravel HMVC + Blade + jQuery/AJAX.
- **Jalur B:** Laravel HMVC + React Starter Kit + Inertia + Fetch API.

> Gunakan untuk belajar dan latihan sebelum asesmen. Saat asesmen, patuhi instruksi dosen tentang penggunaan AI.

## Cakupan kisi-kisi (24 poin)

| No. | Materi | Implementasi di project |
|---:|---|---|
| 1 | Proyek Laravel | Laravel 12 |
| 2 | MySQL dan `.env` | konfigurasi masing-masing database dijelaskan di atas |
| 3 | HMVC | `nwidart/laravel-modules`; modul Facility dan Complaint |
| 4 | Model, View, Controller, Route | tersedia di masing-masing modul |
| 5 | Blade atau React/Inertia | A: Blade; B: React + Inertia |
| 6 | Register, Login, Logout | auth Laravel; registrasi selalu role user |
| 7 | Role dan seeder | admin/user, akun demo dan data contoh |
| 8 | Dashboard sesuai role | ringkasan berbeda berbasis role/data |
| 9 | Middleware dan authorization | route dibatasi `auth`/`role:admin`, plus pemeriksaan request/controller |
| 10 | Route terlindungi dan 403 | route admin dibatasi di backend; view 403 tersedia |
| 11 | Migration dan relasi Eloquent | fasilitas-pengaduan-user dengan foreign key dan relasi |
| 12 | CRUD modul utama | fasilitas: tambah, baca, ubah, hapus |
| 13 | Search dan pagination | daftar fasilitas dan pengaduan |
| 14 | CRUD modul kedua berelasi | pengaduan terkait fasilitas dan user |
| 15 | AJAX / Fetch | A: jQuery; B: Fetch API |
| 16 | FormData, JSON, tanpa reload | mutation AJAX memakai FormData dan respons JSON |
| 17 | Upload + Storage | foto pengaduan disimpan melalui Laravel Storage |
| 18 | Layout, navbar, navigasi | layout dan tautan antarmodul |
| 19 | Form Request | validasi terpusat di request kelas |
| 20 | Validasi, notifikasi, konfirmasi | pesan validasi, toast/hasil aksi, konfirmasi hapus |
| 21 | Bootstrap/Tailwind | A: Bootstrap 5; B: Tailwind |
| 22 | Responsif | grid dan layout adaptif |
| 23 | Dark mode + loading | tema tersimpan dan indikator proses |
| 24 | Uji fitur/akses/AJAX | checklist uji manual dan Network DevTools di atas |
## Siapkan XAMPP

1. Jalankan Apache dan MySQL dari XAMPP Control Panel.
2. Buat database kosong bernama `campuscare_a` untuk jalur A atau `campuscare_b` untuk jalur B di phpMyAdmin.
3. Pastikan PHP CLI yang dipakai terminal adalah PHP XAMPP (`php -v`). Pada Windows, bila perintah `php` tidak dikenali, tambahkan `C:\xampp\php` ke PATH atau jalankan `C:\xampp\php\php.exe`.
4. Buka terminal di folder jalur yang dipilih. Contoh:

```powershell
cd C:\xampp\htdocs\PABW\CampusCare-Jalur-A
```

Jika folder disalin ke lokasi lain, gunakan lokasi itu.

## Jalankan Jalur A (Blade + jQuery)

```powershell
Copy-Item .env.example .env
composer install
php artisan key:generate
php artisan storage:link
php artisan migrate:fresh --seed
php artisan serve --port=8000
```

Buka terminal kedua pada folder yang sama, lalu:

```powershell
npm install
npm run dev
```

Buka `http://127.0.0.1:8000`. jQuery dimuat dari CDN; fitur AJAX membutuhkan koneksi internet untuk CDN tersebut.

## Jalankan Jalur B (React + Inertia)

```powershell
Copy-Item .env.example .env
composer install
php artisan key:generate
php artisan storage:link
php artisan migrate:fresh --seed
```

Di terminal kedua, jalankan Vite:

```powershell
npm install
npm run dev
```

Di terminal pertama jalankan:

```powershell
php artisan serve --port=8001
```

Buka `http://127.0.0.1:8001`. `npm run build` membuat aset produksi bila tidak ingin mempertahankan server Vite.

### Pengaturan database `.env`

Buka `.env` dan cocokkan nilai berikut dengan MySQL lokal. Jalur A gunakan `campuscare_a`; jalur B gunakan `campuscare_b`.

```dotenv
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=campuscare_a
DB_USERNAME=root
DB_PASSWORD=
```

Untuk jalur B ubah `APP_URL` ke port `8001` dan `DB_DATABASE=campuscare_b`. Jika MySQL memiliki password, isikan `DB_PASSWORD`. Sesudah mengubah konfigurasi, jalankan `php artisan config:clear`.

## Akun demo

Seeder membuat akun berikut (kata sandi keduanya `password`):

- Admin: `admin@campuscare.test`
- User: `user@campuscare.test`

Registrasi baru selalu memperoleh role `user`; role admin disediakan oleh seeder.

## Urutan cek fungsi

1. Login admin, buka Dashboard, Fasilitas, dan Pengaduan.
2. Tambah/edit/hapus fasilitas. Coba cari dan pindah halaman bila data cukup.
3. Tambah pengaduan dengan memilih fasilitas; unggah gambar opsional. Lihat indikator proses dan pesan hasil.
4. Ubah status pengaduan sebagai admin; pastikan gambar dapat dibuka.
5. Logout, login sebagai user. User hanya melihat/mengelola pengaduannya sendiri dan tidak boleh mengubah fasilitas atau status admin.
6. Coba URL tindakan admin saat login sebagai user. Backend semestinya menolak akses (403), bukan hanya menyembunyikan tombol.
7. Uji validasi dengan mengirim form kosong dan perhatikan pesan kesalahan.
8. Uji respons layar sempit, tombol tema terang/gelap, pencarian, pagination, konfirmasi hapus, dan AJAX/Fetch di Developer Tools → Network.

## Peta file dan cara membangun manual

Urutan pentingnya: **kerangka Laravel → database/env → HMVC → model+migration → Form Request → controller → routes/middleware → view React/Blade → seeder → uji**.

### 1. Buat kerangka dan pasang modul

```powershell
composer create-project laravel/laravel CampusCare
cd CampusCare
composer require nwidart/laravel-modules
php artisan module:make Facility
php artisan module:make Complaint
```

Untuk jalur A tambahkan starter auth Blade yang digunakan proyek latihan, atau siapkan login/register/logout Laravel. Untuk jalur B buat proyek dari React Starter Kit Laravel 12 (Inertia React) terlebih dahulu, lalu pasang `nwidart/laravel-modules` dan buat kedua modul.

### 2. Set database

Buat database di phpMyAdmin, salin `.env.example` menjadi `.env`, isi `DB_*`, lalu:

```powershell
php artisan key:generate
php artisan config:clear
```

Jalankan migrasi setelah seluruh migration siap, bukan sebelum relasi tabel dibuat.

### 3. Isi file modul secara berurutan

Di `Modules/Facility` dan `Modules/Complaint`, lengkapi file berikut:

1. `database/migrations/*` — kolom, foreign key, timestamps, dan indeks. Complaint menyimpan `user_id` serta `facility_id`.
2. `app/Models/*` — `$fillable`, casts, dan relasi Eloquent (`Facility hasMany Complaint`; `Complaint belongsTo User/Facility`; `User hasMany Complaint`).
3. `app/Http/Requests/*` — rules validasi; `authorize()` membatasi aksi admin bila diperlukan.
4. `app/Http/Controllers/*` — query, validasi request, CRUD, pencarian, pagination, simpan/hapus berkas melalui `Storage`, dan respons JSON untuk permintaan AJAX.
5. `routes/web.php` — halaman di balik `auth`; operasi fasilitas dan perubahan status hanya `role:admin`.
6. Tampilan: A di `resources/views`; B di `resources/js/pages`. A menggunakan Blade + jQuery FormData; B menggunakan React page Inertia + `fetch` FormData, token CSRF, dan respons JSON.
7. Provider/module config — aktifkan kedua modul jika konfigurasi `modules_statuses.json` menggunakannya. Pastikan Composer memetakan `Modules\\Facility\\` ke `Modules/Facility/app/` dan `Modules\\Complaint\\` ke `Modules/Complaint/app/`.

### 4. Siapkan akses dan demo data

Pastikan `User` memiliki role dan helper pemeriksaan admin. Daftarkan alias middleware role di `bootstrap/app.php`, lalu gunakan di route dan lakukan pemeriksaan ulang di controller/Form Request. Seeder mengisi role admin/user, fasilitas demo, dan contoh pengaduan.

```powershell
php artisan migrate:fresh --seed
```

### 5. Hubungkan antarmuka dan uji

Layout menampung navbar, navigasi antarmodul, dark mode, toast, dan loading. Form harus menampilkan validasi; penghapusan meminta konfirmasi. Untuk AJAX, kirim `Accept: application/json`, header CSRF, dan `FormData` (jangan set `Content-Type` sendiri untuk multipart). Perbarui DOM/state dari JSON agar halaman tidak reload.

```powershell
php artisan route:list
php artisan migrate:status
```

Periksa route dan migrasi, kemudian lakukan urutan cek fungsi di atas dengan kedua role.

## Troubleshooting umum

- **MySQL connection refused:** pastikan MySQL XAMPP aktif, database ada, dan DB port/user benar.
- **Table not found:** jalankan `php artisan migrate:fresh --seed` (ini menghapus isi database proyek tersebut).
- **Module class not found:** cek namespace vs path di `composer.json`, lalu `composer dump-autoload`.
- **Gambar 404:** jalankan `php artisan storage:link`; pastikan unggahan masuk ke disk public dan URL menggunakan route/storage yang disediakan.
- **Vite manifest missing:** jalankan `npm install` lalu `npm run dev`, atau `npm run build`.
- **Port sedang dipakai:** pilih port lain, misalnya `php artisan serve --port=8010`, lalu buka URL dengan port yang sama. Port bukan bagian permanen aplikasi.
- **Perubahan `.env` tidak terbaca:** `php artisan config:clear`.

