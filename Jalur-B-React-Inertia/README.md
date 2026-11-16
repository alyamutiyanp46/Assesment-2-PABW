# SIPustaka - Jalur B (Laravel HMVC + React Starter + Inertia + Fetch)

Project latihan asesmen Praktikum PABW. Domain contoh adalah perpustakaan: kategori memiliki banyak buku, dan peminjaman terhubung ke buku serta pengguna. Komentar berbahasa Indonesia menjelaskan bagian penting pada source.

## Menjalankan

1. Pastikan PHP 8.2+, Composer, Node.js/NPM, dan MySQL tersedia. Buat database MySQL bernama `sipustaka`.
2. Salin `.env.example` ke `.env` dan sesuaikan username/password MySQL.
3. Jalankan dari folder project:

```bash
composer install
php artisan key:generate
npm install
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000`. Login demo: `admin@sipustaka.test` / `password` atau `mahasiswa@sipustaka.test` / `password`.

Untuk pengembangan dengan hot reload, jalankan `npm run dev` di terminal lain, lalu `php artisan serve`.

## Isi aplikasi

- React pages dirender oleh Inertia; route, autentikasi, validasi, dan Eloquent tetap memakai Laravel.
- Modul `Modules/Pustaka` berisi model, relasi, Form Request, controller, route, migration dan UI.
- Admin/mahasiswa memiliki dashboard dan hak akses berbeda. Middleware memblokir route terlarang dengan HTTP 403.
- Katalog buku menyediakan pencarian, pagination, CRUD Fetch API, unggah sampul Laravel Storage, validasi dan JSON.
- Transaksi peminjaman berelasi ke buku/pengguna; Fetch API menangani create, perubahan status, dan delete tanpa refresh.
- Bootstrap 5, layout responsif, dark mode tersimpan, status loading, notifikasi, dan konfirmasi hapus.

## Cara memahami pola

`Route -> middleware auth/role -> Controller -> Form Request -> Eloquent -> MySQL -> Inertia props / JSON -> React`

Di sini React adalah UI yang dibawa dalam satu aplikasi Laravel lewat Inertia; route dan sesi login tetap dikelola Laravel. `FormData` membawa file sampul dan field form; untuk request Fetch, backend mengembalikan JSON. Jika menambah modul baru, buat melalui `php artisan module:make NamaModul`, cek PSR-4 module pada `composer.json`, lalu jalankan `composer dump-autoload`.

Ini project latihan sebelum asesmen. Saat ujian, ikuti aturan dosen mengenai penggunaan AI.
