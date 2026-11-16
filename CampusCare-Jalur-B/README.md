# SiPustaka

Aplikasi manajemen koleksi buku perpustakaan kampus menggunakan Laravel 12, MySQL, Inertia, React, dan `nwidart/laravel-modules`.

## Menjalankan aplikasi di Laragon

1. Buka Laragon lalu pilih **Start All**. Pastikan PHP 8.2 atau lebih baru, MySQL, Composer, dan Node.js tersedia.
2. Ekstrak ZIP proyek ke `C:\laragon\www\sipustaka_hmvc_react_pekan8`, lalu buka Terminal Laragon di folder tersebut.
3. Salin `.env.example` menjadi `.env`. Konfigurasi awal memakai MySQL Laragon dengan database `db_sipustaka`, username `root`, dan password kosong. Buat database tersebut melalui phpMyAdmin Laragon.
4. Pasang dependensi proyek. ZIP tidak menyertakan folder `vendor` dan `node_modules`, jadi langkah ini perlu dilakukan sekali setelah ekstraksi:

   ```bash
   composer install
   npm install
   php artisan key:generate
   php artisan migrate --seed
   ```

5. Jalankan aplikasi:

   ```bash
   composer run dev
   ```

6. Buka `http://127.0.0.1:8000`.

Jika `composer run dev` dijalankan sebelum dua perintah pemasangan dependensi, Laravel tidak menemukan `vendor/autoload.php` dan Vite tidak ditemukan. Jalankan `composer install` dan `npm install` dari folder proyek hasil ekstrak, bukan dari folder ZIP.

Seeder menambahkan 10 buku contoh dan dua akun untuk demonstrasi. Kata sandi awal keduanya adalah `password`.

| Peran | Email | Hak akses buku |
| --- | --- | --- |
| Admin | `admin@example.com` | Melihat, menambah, mengubah, dan menghapus |
| Petugas | `petugas@example.com` | Melihat dan menambah |

Pendaftaran akun baru membuat akun dengan peran Petugas. Pengaturan peran tetap dilakukan di sisi server.

## Alur aplikasi

- Login dan pendaftaran ditangani autentikasi Fortify yang sudah tersedia pada starter kit proyek.
- Dashboard menampilkan nama pengguna, peran, jumlah buku, dan navigasi modul.
- Modul `Modules/Buku` memuat route, controller, model Eloquent, migration, seeder, dan halaman daftar buku.
- Browser mengirim permintaan `fetch()` ke `/buku/data`, `/buku`, dan `/buku/{id}`. Server memvalidasi data dan memeriksa peran sebelum mengubah database. Setelah berhasil, daftar buku dimuat kembali tanpa berpindah halaman.
- FormRequest memeriksa kode buku unik, panjang teks, kolom wajib, dan stok yang tidak negatif.

Untuk demonstrasi, login sebagai Petugas dan coba tambah buku. Keluar lalu login sebagai Admin untuk mencoba tambah, edit, hapus, dan pencarian. Buka Developer Tools pada tab Network untuk menunjukkan request Fetch/XHR dan response JSON.

## Urutan demonstrasi

1. Login sebagai Petugas, tunjukkan nama, peran, dan jumlah koleksi pada Dashboard.
2. Buka Data Buku, lakukan pencarian, lalu tambahkan buku. Pada tab Network, tunjukkan request `POST /buku` dan response JSON.
3. Logout dan login sebagai Admin. Ubah stok sebuah buku, lalu hapus satu buku untuk menunjukkan tabel berubah tanpa memuat ulang halaman.
4. Tunjukkan bahwa tombol edit dan hapus hanya muncul untuk Admin. Route `PUT` dan `DELETE` juga dibatasi middleware Laravel.
5. Tunjukkan halaman Register dan Login, lalu akhiri dengan Logout.

Ambil tangkapan layar halaman Register, Login, Dashboard, Data Buku, dan Data Buku dalam ukuran ponsel. Untuk video berdurasi 3–5 menit, gunakan urutan demonstrasi di atas sambil menjelaskan alur singkatnya: React mengirim `fetch()`, route modul meneruskan permintaan ke controller, FormRequest memvalidasi input dan role, lalu model Eloquent menyimpan perubahan ke MySQL.
