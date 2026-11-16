<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('bukus')) {
            return;
        }

        Schema::create('bukus', function (Blueprint $table) {
            $table->id();
            $table->string('kode_buku', 20)->unique();
            $table->string('judul', 150);
            $table->string('penulis', 100);
            $table->string('kategori', 50);
            $table->unsignedInteger('stok');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
