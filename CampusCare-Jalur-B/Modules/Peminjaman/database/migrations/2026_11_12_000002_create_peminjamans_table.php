<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('peminjamans')) {
            Schema::create('peminjamans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('nama_peminjam');
                $table->string('judul_buku');
                $table->date('tanggal_pinjam');
                $table->date('tanggal_kembali')->nullable();
                $table->string('status')->default('Dipinjam');
                $table->string('file_path')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('peminjamans', function (Blueprint $table) {
                if (! Schema::hasColumn('peminjamans', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
                }
                if (! Schema::hasColumn('peminjamans', 'file_path')) {
                    $table->string('file_path')->nullable()->after('status');
                }
            });

            // Preserve files uploaded by the earlier unfinished implementation.
            if (Schema::hasColumn('peminjamans', 'bukti_path')) {
                DB::table('peminjamans')->whereNull('file_path')->whereNotNull('bukti_path')
                    ->update(['file_path' => DB::raw('bukti_path')]);
            }
        }
    }

    public function down(): void
    {
        // Keep existing rows and uploaded-file paths on rollback.
    }
};
