<?php

namespace Modules\Buku\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Buku\Models\Buku;

class BukuDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            [
                'kode_buku' => 'BK00000001',
                'judul' => 'Pemrograman Web dengan Laravel dan React',
                'penulis' => 'Ahmad Santoso',
                'kategori' => 'Teknologi',
                'stok' => 15,
            ],
            [
                'kode_buku' => 'BK00000002',
                'judul' => 'Struktur Data dan Algoritma Modern',
                'penulis' => 'Budi Raharjo',
                'kategori' => 'Teknologi',
                'stok' => 10,
            ],
            [
                'kode_buku' => 'BK00000003',
                'judul' => 'Basis Data Relasional dan MySQL',
                'penulis' => 'Citra Dewi',
                'kategori' => 'Teknologi',
                'stok' => 8,
            ],
            [
                'kode_buku' => 'BK00000004',
                'judul' => 'Arsitektur Komputer dan Jaringan',
                'penulis' => 'Dedi Prasetyo',
                'kategori' => 'Teknologi',
                'stok' => 12,
            ],
            [
                'kode_buku' => 'BK00000005',
                'judul' => 'Rekayasa Perangkat Lunak Berorientasi Objek',
                'penulis' => 'Eka Pratama',
                'kategori' => 'Teknologi',
                'stok' => 20,
            ],
            [
                'kode_buku' => 'BK00000006',
                'judul' => 'Pengantar Sistem Informasi',
                'penulis' => 'Fajar Nugraha',
                'kategori' => 'Sistem Informasi',
                'stok' => 9,
            ],
            [
                'kode_buku' => 'BK00000007',
                'judul' => 'Manajemen Proyek Perangkat Lunak',
                'penulis' => 'Gita Permata',
                'kategori' => 'Manajemen',
                'stok' => 7,
            ],
            [
                'kode_buku' => 'BK00000008',
                'judul' => 'Jaringan Komputer Dasar',
                'penulis' => 'Hendra Saputra',
                'kategori' => 'Jaringan',
                'stok' => 11,
            ],
            [
                'kode_buku' => 'BK00000009',
                'judul' => 'UI dan UX untuk Pemula',
                'penulis' => 'Intan Maharani',
                'kategori' => 'Desain',
                'stok' => 6,
            ],
            [
                'kode_buku' => 'BK00000010',
                'judul' => 'Keamanan Aplikasi Web',
                'penulis' => 'Joko Firmansyah',
                'kategori' => 'Keamanan',
                'stok' => 13,
            ],
        ];

        foreach ($books as $book) {
            Buku::firstOrCreate(['kode_buku' => $book['kode_buku']], $book);
        }
    }
}
