<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void { Schema::create('loans', function (Blueprint $table) {
        $table->id(); $table->foreignId('book_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
        $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
        $table->date('borrowed_at'); $table->date('due_at'); $table->date('returned_at')->nullable();
        $table->string('status')->default('menunggu'); $table->text('notes')->nullable(); $table->timestamps();
    }); }
    public function down(): void { Schema::dropIfExists('loans'); }
};
