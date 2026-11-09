<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('nama', 150);
            $table->text('deskripsi')->nullable();
            $table->unsignedInteger('harga');
            $table->string('foto')->nullable();
            $table->boolean('tersedia')->default(true);
            $table->boolean('is_best_seller')->default(false);
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();

            $table->index(['category_id', 'urutan']);
            $table->index('tersedia');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
