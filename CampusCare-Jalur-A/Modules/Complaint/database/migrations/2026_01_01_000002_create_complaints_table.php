<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('facility_id')->constrained('facilities')->restrictOnDelete();
            $table->string('title', 160);
            $table->text('description');
            $table->string('location', 180);
            $table->string('photo_path')->nullable();
            $table->enum('status', ['menunggu', 'diproses', 'selesai', 'ditolak'])->default('menunggu');
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('complaints'); }
};
