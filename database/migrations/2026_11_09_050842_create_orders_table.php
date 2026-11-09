<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 12)->unique();
            $table->foreignId('cafe_table_id')->constrained()->restrictOnDelete();
            $table->string('nama', 100);
            $table->string('telepon', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('status', 20)->default('dikonfirmasi');
            $table->string('metode_bayar', 20)->default('belum_dipilih');
            $table->string('status_bayar', 20)->default('belum_bayar');
            $table->unsignedInteger('subtotal')->default(0);
            $table->unsignedInteger('total')->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('status_bayar');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
