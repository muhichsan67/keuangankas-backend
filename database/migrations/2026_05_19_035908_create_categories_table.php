<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('categories', function (Blueprint $blueprint) {
            $blueprint->id();
            // Menghubungkan ke user agar tiap user bisa punya kategori custom
            $blueprint->foreignId('user_id')->constrained()->cascadeOnDelete();
            $blueprint->string('name');
            // Untuk membedakan kategori pemasukan atau pengeluaran
            $blueprint->enum('type', ['in', 'out'])->default('out');
            $blueprint->string('icon')->nullable(); // Opsional untuk UI Vue
            $blueprint->string('color')->nullable(); // Opsional untuk UI Vue
            $blueprint->timestamps();
            $blueprint->softDeletes(); // Konsisten dengan fitur Soft Delete Anda [3]
        });
    }

    public function down(): void {
        Schema::dropIfExists('categories');
    }
};
