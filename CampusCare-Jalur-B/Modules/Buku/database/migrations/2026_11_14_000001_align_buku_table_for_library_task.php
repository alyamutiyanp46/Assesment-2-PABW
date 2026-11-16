<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bukus')) {
            Schema::create('bukus', function (Blueprint $table) {
                $table->id();
                $table->string('kode_buku', 20)->unique();
                $table->string('judul', 150);
                $table->string('penulis', 100);
                $table->string('kategori', 50);
                $table->unsignedInteger('stok');
                $table->timestamps();
            });

            return;
        }

        $hasKode = Schema::hasColumn('bukus', 'kode_buku');
        $hasKategori = Schema::hasColumn('bukus', 'kategori');

        Schema::table('bukus', function (Blueprint $table) use ($hasKode, $hasKategori) {
            if (! $hasKode) {
                $table->string('kode_buku', 20)->nullable();
            }

            if (! $hasKategori) {
                $table->string('kategori', 50)->nullable();
            }

            if (Schema::hasColumn('bukus', 'penerbit')) {
                $table->string('penerbit')->nullable()->change();
            }

            if (Schema::hasColumn('bukus', 'tahun_terbit')) {
                $table->integer('tahun_terbit')->nullable()->change();
            }
        });

        DB::table('bukus')->orderBy('id')->get()->each(function ($book) use ($hasKode, $hasKategori) {
            $updates = [];

            if (! $hasKode || $book->kode_buku === null) {
                $updates['kode_buku'] = 'BK'.str_pad((string) $book->id, 8, '0', STR_PAD_LEFT);
            }

            if (! $hasKategori || $book->kategori === null) {
                $updates['kategori'] = 'Umum';
            }

            if ($updates !== []) {
                DB::table('bukus')->where('id', $book->id)->update($updates);
            }
        });

        Schema::table('bukus', function (Blueprint $table) use ($hasKode, $hasKategori) {
            if (! $hasKode) {
                $table->string('kode_buku', 20)->nullable(false)->change();
                $table->unique('kode_buku');
            }

            if (! $hasKategori) {
                $table->string('kategori', 50)->nullable(false)->change();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('bukus')) {
            return;
        }

        Schema::table('bukus', function (Blueprint $table) {
            if (Schema::hasColumn('bukus', 'kode_buku')) {
                $table->dropUnique(['kode_buku']);
                $table->dropColumn('kode_buku');
            }

            if (Schema::hasColumn('bukus', 'kategori')) {
                $table->dropColumn('kategori');
            }
        });
    }
};
